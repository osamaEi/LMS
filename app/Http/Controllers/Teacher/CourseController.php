<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Session;
use App\Models\SessionFile;
use App\Models\SubjectFile;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    protected $zoomService;
    protected $notificationService;

    public function __construct(ZoomService $zoomService, NotificationService $notificationService)
    {
        $this->zoomService = $zoomService;
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $teacher = auth()->user(); 

        // Classes where this teacher is supervisor
        $teacherClasses = ProgramClass::where('teacher_id', $teacher->id)->get(['id', 'program_id']);
        $classProgramIds = $teacherClasses->pluck('program_id');

        // Programs assigned directly via program_teacher pivot
        $directProgramIds = $teacher->teachingPrograms()
            ->whereIn('type', ['training', 'english', 'course'])
            ->pluck('programs.id');

        $programIds = $classProgramIds->merge($directProgramIds)->unique()->values();

        $programs = Program::whereIn('id', $programIds)
            ->whereIn('type', ['training', 'english', 'course'])
            ->withCount(['terms', 'files'])
            ->get();

        // Attach class-scoped counts per program
        $programs->each(function ($program) use ($teacherClasses) {
            $progClassIds = $teacherClasses->where('program_id', $program->id)->pluck('id');
            $program->sessions_count = \App\Models\Session::where('program_id', $program->id)
                ->where(function ($q) use ($progClassIds) {
                    $q->whereIn('class_id', $progClassIds)->orWhereNull('class_id');
                })->count();
            $program->class_student_count = $progClassIds->isNotEmpty()
                ? \Illuminate\Support\Facades\DB::table('student_programs')
                    ->where('program_id', $program->id)
                    ->whereIn('class_id', $progClassIds)
                    ->distinct()->count('student_id')
                : \Illuminate\Support\Facades\DB::table('student_programs')
                    ->where('program_id', $program->id)
                    ->distinct()->count('student_id');
        });

        return view('teacher.courses.index', compact('programs'));
    }

    public function show($id)
    {
        $teacher = auth()->user();

        // Programs accessible via class supervisor OR direct program_teacher pivot
        $classProgramIds  = ProgramClass::where('teacher_id', $teacher->id)->pluck('program_id');
        $directProgramIds = $teacher->teachingPrograms()->whereIn('type', ['training', 'english', 'course'])->pluck('programs.id');
        $programIds       = $classProgramIds->merge($directProgramIds)->unique()->values();

        $program = Program::whereIn('id', $programIds)
            ->whereIn('type', ['training', 'english', 'course'])
            ->with(['files'])
            ->withCount(['enrolledStudents'])
            ->findOrFail($id);

        // Show only the sessions (lectures) assigned to THIS teacher in this program.
        // Session teacher_id is set when sessions are generated for a class.
        $sessions = Session::where('program_id', $id)
            ->where('teacher_id', $teacher->id)
            ->with(['files'])
            ->orderBy('session_number', 'asc')
            ->get();

        // Students enrolled in this program OR who attended its sessions
        $classIds    = \App\Models\ProgramClass::where('program_id', $id)->pluck('id');
        $pivotIds    = \Illuminate\Support\Facades\DB::table('student_programs')
                        ->where('program_id', $id)->orWhereIn('class_id', $classIds)
                        ->distinct()->pluck('student_id');
        $legacyIds   = User::where('program_id', $id)->pluck('id');
        $attendedIds = \App\Models\Attendance::whereIn('session_id', $sessions->pluck('id'))->distinct()->pluck('student_id');
        $studentIds  = $pivotIds->merge($legacyIds)->merge($attendedIds)->unique()->values();
        $students    = User::whereIn('id', $studentIds)->where('role', 'student')
                        ->with('program:id,name_ar,name_en')->orderBy('name')->get();

        return view('teacher.courses.show', compact('program', 'sessions', 'students'));
    }

    public function storeSession(Request $request, $programId)
    {
        $teacher = auth()->user();
        $program = $teacher->teachingPrograms()
            ->whereIn('type', ['training', 'english', 'course'])
            ->findOrFail($programId);

        $validated = $request->validate([
            'title_ar'         => 'nullable|string|max:255',
            'scheduled_at'     => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:1',
            'type'             => 'nullable|in:live_zoom,recorded_video',
            'zoom_meeting_id'  => 'nullable|string',
            'zoom_join_url'    => 'nullable|url',
            'zoom_password'    => 'nullable|string',
            'video_url'        => 'nullable|url',
            'video_platform'   => 'nullable|in:youtube,vimeo,external,local',
            'video_file'       => 'nullable|file|mimes:mp4,avi,mov,mkv,wmv,flv|max:512000',
            'files.*'          => 'nullable|file|max:10240',
        ]);

        $nextNumber = Session::where('program_id', $programId)->max('session_number') + 1;

        $data = [
            'program_id'       => $programId,
            'teacher_id'       => $teacher->id,
            'session_number'   => $nextNumber,
            'title_ar'         => $validated['title_ar'] ?? ('محاضرة ' . $nextNumber),
            'title_en'         => 'Session ' . $nextNumber,
            'type'             => $validated['type'] ?? 'recorded_video',
            'scheduled_at'     => $validated['scheduled_at'] ?? null,
            'duration_minutes' => $validated['duration_minutes'] ?? null,
        ];

        if (!empty($validated['video_url'])) {
            $data['video_url']      = $validated['video_url'];
            $data['video_platform'] = $validated['video_platform'] ?? 'external';
        }

        if ($request->hasFile('video_file')) {
            $vf = $request->file('video_file');
            $data['video_path']     = $vf->store('session-videos', 'public');
            $data['video_platform'] = 'local';
            $data['video_size']     = $vf->getSize();
        }

        if (($data['type'] ?? '') === 'live_zoom' && empty($validated['zoom_meeting_id'])) {
            try {
                $meeting = $this->zoomService->createMeeting([
                    'topic'      => $data['title_ar'],
                    'type'       => 2,
                    'start_time' => isset($data['scheduled_at'])
                        ? \Carbon\Carbon::parse($data['scheduled_at'])->toIso8601String()
                        : now()->addHour()->toIso8601String(),
                    'duration'  => $data['duration_minutes'] ?? 60,
                    'timezone'  => 'Asia/Riyadh',
                ]);
                if ($meeting) {
                    $data['zoom_meeting_id'] = $meeting['id'];
                    $data['zoom_join_url']   = $meeting['join_url'];
                    $data['zoom_password']   = $meeting['password'] ?? null;
                } else {
                    return back()->withInput()->withErrors(['zoom' => 'فشل إنشاء اجتماع Zoom.']);
                }
            } catch (\Exception $e) {
                Log::error('Zoom creation error: ' . $e->getMessage());
                return back()->withInput()->withErrors(['zoom' => 'خطأ في Zoom: ' . $e->getMessage()]);
            }
        } else {
            $data['zoom_meeting_id'] = $validated['zoom_meeting_id'] ?? null;
            $data['zoom_join_url']   = $validated['zoom_join_url']   ?? null;
            $data['zoom_password']   = $validated['zoom_password']   ?? null;
        }

        $session = Session::create(array_filter($data, fn($v) => $v !== null && $v !== ''));

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $i => $file) {
                $path = $file->store('session-files', 'public');
                SessionFile::create([
                    'session_id' => $session->id,
                    'title'      => $file->getClientOriginalName(),
                    'type'       => 'pdf',
                    'file_path'  => $path,
                    'file_size'  => $file->getSize(),
                    'order'      => $i + 1,
                ]);
            }
        }

        return redirect()->route('teacher.my-courses.show', $programId)
            ->with('success', 'تم إضافة المحاضرة بنجاح');
    }

    public function editSession($programId, $sessionId)
    {
        $teacher = auth()->user();
        $program = $teacher->teachingPrograms()
            ->whereIn('type', ['training', 'english', 'course'])
            ->findOrFail($programId);

        $session = Session::where('program_id', $programId)->with('files')->findOrFail($sessionId);

        return view('teacher.courses.sessions.edit', compact('program', 'session'));
    }

    public function updateSession(Request $request, $programId, $sessionId)
    {
        $teacher = auth()->user();
        $teacher->teachingPrograms()->whereIn('type', ['training', 'english', 'course'])->findOrFail($programId);
        $session = Session::where('program_id', $programId)->findOrFail($sessionId);

        $validated = $request->validate([
            'title_ar'         => 'required|string|max:255',
            'session_number'   => 'required|integer|min:1',
            'type'             => 'required|in:live_zoom,recorded_video',
            'scheduled_at'     => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:1',
            'zoom_meeting_id'  => 'nullable|string',
            'zoom_join_url'    => 'nullable|url',
            'zoom_password'    => 'nullable|string',
            'video_file'       => 'nullable|file|mimes:mp4,avi,mov,mkv,wmv,flv|max:512000',
            'video_url'        => 'nullable|url',
            'video_platform'   => 'nullable|in:youtube,vimeo,external,local',
            'files.*'          => 'nullable|file|max:10240',
        ]);

        $data = array_filter($validated, fn($v) => $v !== null && $v !== '');
        unset($data['video_file'], $data['files']);

        if ($request->hasFile('video_file')) {
            if ($session->video_path) Storage::disk('public')->delete($session->video_path);
            $vf = $request->file('video_file');
            $data['video_path']     = $vf->store('session-videos', 'public');
            $data['video_platform'] = 'local';
            $data['video_size']     = $vf->getSize();
        }

        $session->update($data);

        if ($request->hasFile('files')) {
            $maxOrder = $session->files()->max('order') ?? 0;
            foreach ($request->file('files') as $i => $file) {
                $path = $file->store('session-files', 'public');
                SessionFile::create([
                    'session_id' => $session->id,
                    'title'      => $file->getClientOriginalName(),
                    'type'       => 'pdf',
                    'file_path'  => $path,
                    'file_size'  => $file->getSize(),
                    'order'      => $maxOrder + $i + 1,
                ]);
            }
        }

        return redirect()->route('teacher.my-courses.show', $programId)
            ->with('success', 'تم تحديث المحاضرة بنجاح');
    }

    public function destroySession($programId, $sessionId)
    {
        $teacher = auth()->user();
        $teacher->teachingPrograms()->whereIn('type', ['training', 'english', 'course'])->findOrFail($programId);
        $session = Session::where('program_id', $programId)->findOrFail($sessionId);

        if ($session->zoom_meeting_id) {
            try { $this->zoomService->deleteMeeting($session->zoom_meeting_id); } catch (\Exception $e) {}
        }
        if ($session->video_path) Storage::disk('public')->delete($session->video_path);
        foreach ($session->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }
        $session->delete();

        return redirect()->route('teacher.my-courses.show', $programId)
            ->with('success', 'تم حذف المحاضرة بنجاح');
    }

    public function deleteSessionFile($programId, $sessionId, $fileId)
    {
        $teacher = auth()->user();
        $teacher->teachingPrograms()->whereIn('type', ['training', 'english', 'course'])->findOrFail($programId);
        Session::where('program_id', $programId)->findOrFail($sessionId);
        $file = SessionFile::where('session_id', $sessionId)->findOrFail($fileId);
        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'تم حذف الملف بنجاح');
    }

    public function storeFile(Request $request, $programId)
    {
        $teacher = auth()->user();
        $program = $teacher->teachingPrograms()
            ->whereIn('type', ['training', 'english', 'course'])
            ->findOrFail($programId);

        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'file'        => 'required|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,png,jpg,jpeg',
        ]);

        $uploaded = $request->file('file');
        $path = $uploaded->store('program-files/' . $program->id, 'public');

        SubjectFile::create([
            'program_id'         => $program->id,
            'title'              => $request->title,
            'description'        => $request->description,
            'file_path'          => $path,
            'file_original_name' => $uploaded->getClientOriginalName(),
            'file_size'          => $uploaded->getSize(),
            'file_type'          => strtolower($uploaded->getClientOriginalExtension()),
            'order'              => SubjectFile::where('program_id', $program->id)->max('order') + 1,
        ]);

        return back()->with('success', 'تم رفع الملف بنجاح');
    }

    public function destroyFile($programId, $fileId)
    {
        $teacher = auth()->user();
        $teacher->teachingPrograms()->whereIn('type', ['training', 'english', 'course'])->findOrFail($programId);
        $file = SubjectFile::where('program_id', $programId)->findOrFail($fileId);
        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'تم حذف الملف بنجاح');
    }

    public function sessionAttendance($programId, $sessionId)
    {
        $teacher = auth()->user();
        $program = $teacher->teachingPrograms()
            ->whereIn('type', ['training', 'english', 'course'])
            ->findOrFail($programId);

        $session = Session::where('program_id', $programId)->findOrFail($sessionId);

        // Resolve class: from session.class_id or teacher's assigned class for this program
        $classId = $session->class_id
            ?? \App\Models\ProgramClass::where('program_id', $programId)
                ->where('teacher_id', $teacher->id)
                ->value('id');

        // Get all students in the class via student_programs pivot
        $classStudentIds = $classId
            ? \Illuminate\Support\Facades\DB::table('student_programs')
                ->where('class_id', $classId)->distinct()->pluck('student_id')
            : \App\Models\User::where('program_id', $programId)->where('role', 'student')->pluck('id');

        $classStudents = User::whereIn('id', $classStudentIds)
            ->where('role', 'student')
            ->orderBy('name')
            ->get(['id', 'name', 'student_code']);

        // Existing attendance records
        $attendanceMap = Attendance::where('session_id', $sessionId)
            ->get()->keyBy('student_id');

        // Build unified list (one row per class student)
        $attendances = $classStudents->map(function ($student) use ($attendanceMap, $sessionId) {
            $att = $attendanceMap->get($student->id);
            return (object)[
                'id'         => $att?->id,
                'session_id' => $sessionId,
                'student_id' => $student->id,
                'student'    => $student,
                'attended'   => $att?->attended ?? false,
                'joined_at'  => $att?->joined_at,
                'notes'      => $att?->notes,
            ];
        });

        $totalStudents  = $attendances->count();
        $attendedCount  = $attendances->where('attended', true)->count();
        $absentStudents = $attendances->where('attended', false)->pluck('student')->filter()->values();

        $stats = [
            'total_enrolled'  => $totalStudents,
            'attended'        => $attendedCount,
            'absent'          => $absentStudents->count(),
            'attendance_rate' => $totalStudents > 0
                ? round(($attendedCount / $totalStudents) * 100, 1)
                : 0,
        ];

        return view('teacher.courses.sessions.attendance', compact('program', 'session', 'attendances', 'absentStudents', 'stats'));
    }

    public function exportAttendance($programId, $sessionId)
    {
        $teacher = auth()->user();
        $program = $teacher->teachingPrograms()->whereIn('type', ['training', 'english', 'course'])->findOrFail($programId);
        $session = Session::where('program_id', $programId)->findOrFail($sessionId);

        $classId = $session->class_id
            ?? \App\Models\ProgramClass::where('program_id', $programId)->where('teacher_id', $teacher->id)->value('id');

        $classStudentIds = $classId
            ? \Illuminate\Support\Facades\DB::table('student_programs')->where('class_id', $classId)->distinct()->pluck('student_id')
            : \App\Models\User::where('program_id', $programId)->where('role', 'student')->pluck('id');

        $students = \App\Models\User::whereIn('id', $classStudentIds)->where('role', 'student')->orderBy('name')->get();
        $attendanceMap = \App\Models\Attendance::where('session_id', $sessionId)->get()->keyBy('student_id');

        $filename = 'حضور_' . $session->session_number . '_' . now()->format('Ymd') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($students, $attendanceMap, $session, $program) {
            $f = fopen('php://output', 'w');
            fprintf($f, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM for Excel Arabic
            fputcsv($f, ['البرنامج', 'المحاضرة', 'التاريخ']);
            fputcsv($f, [
                $program->name_ar,
                'محاضرة ' . $session->session_number,
                $session->scheduled_at ? \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d H:i') : '',
            ]);
            fputcsv($f, []);
            fputcsv($f, ['#', 'اسم المتدرب', 'كود المتدرب', 'الحضور', 'وقت الانضمام']);
            foreach ($students as $i => $student) {
                $att = $attendanceMap->get($student->id);
                fputcsv($f, [
                    $i + 1,
                    $student->name,
                    $student->student_code ?? '',
                    $att && $att->attended ? 'حاضر' : 'غائب',
                    $att && $att->joined_at ? \Carbon\Carbon::parse($att->joined_at)->format('H:i') : '',
                ]);
            }
            fclose($f);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function saveAttendance(Request $request, $programId, $sessionId)
    {
        $teacher = auth()->user();
        $teacher->teachingPrograms()->whereIn('type', ['training', 'english', 'course'])->findOrFail($programId);
        $session = Session::where('program_id', $programId)->findOrFail($sessionId);

        $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->student_ids as $studentId) {
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'session_id' => $session->id],
                ['attended' => true, 'joined_at' => now()]
            );
        }

        return back()->with('success', 'تم تسجيل الحضور بنجاح');
    }
}
