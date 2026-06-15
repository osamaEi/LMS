<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Session;
use App\Models\SessionFile;
use App\Models\Subject;
use App\Services\NotificationService;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SubjectController extends Controller
{
    protected $zoomService;
    protected $notificationService;

    public function __construct(ZoomService $zoomService, NotificationService $notificationService)
    {
        $this->zoomService = $zoomService;
        $this->notificationService = $notificationService;
    }

    /**
     * Display teacher's subjects
     */
    public function index()
    {
        $teacher = auth()->user();

        // Sessions this teacher is assigned to (used as a fallback assignment signal).
        $sessionSubjectIds = \App\Models\Session::where('teacher_id', $teacher->id)
            ->whereNotNull('subject_id')->distinct()->pluck('subject_id');

        // Show subjects the teacher actually teaches (direct teacher_id,
        // teachers pivot, or assigned sessions) AND that belong to a class.
        $subjects = Subject::where(function ($q) use ($teacher, $sessionSubjectIds) {
                $q->assignedToTeacher($teacher->id)
                  ->orWhereIn('id', $sessionSubjectIds);
            })
            ->where(function ($q) {
                $q->whereNotNull('class_id')
                  ->orWhereHas('term', fn($tq) => $tq->whereNotNull('class_id'));
            })
            ->where(function ($q) {
                $q->whereHas('program', fn($pq) => $pq->where('type', 'diploma'))
                  ->orWhereHas('term.program', fn($pq) => $pq->where('type', 'diploma'));
            })
            ->with(['term.program'])
            ->withCount('sessions')
            ->orderBy(app()->getLocale() === 'en' ? 'name_en' : 'name_ar')
            ->get();

        // Count students per subject scoped to the subject's class
        $allClassIds = $subjects->map(fn($s) => $s->class_id ?? $s->term?->class_id ?? null)->filter()->unique()->values();
        $totalStudents = $allClassIds->isNotEmpty()
            ? \Illuminate\Support\Facades\DB::table('student_programs')
                ->whereIn('class_id', $allClassIds)->distinct()->count('student_id')
            : 0;

        $subjects->each(function ($subject) {
            $classId = $subject->class_id ?? $subject->term?->class_id ?? null;
            $subject->students_count = $classId
                ? \Illuminate\Support\Facades\DB::table('student_programs')
                    ->where('class_id', $classId)->distinct()->count('student_id')
                : 0;
        });

        return view('teacher.subjects.index', compact('subjects', 'totalStudents'));
    }

    /**
     * Display subject details with sessions
     */
    public function show($id)
    {
        $teacher = auth()->user();

        $sessionSubjectIds = \App\Models\Session::where('teacher_id', $teacher->id)
            ->whereNotNull('subject_id')->distinct()->pluck('subject_id');

        $subject = Subject::where(function ($q) use ($teacher, $sessionSubjectIds) {
                $q->assignedToTeacher($teacher->id)
                  ->orWhereIn('id', $sessionSubjectIds);
            })
            ->with(['term.program', 'units', 'files'])
            ->withCount('enrollments')
            ->findOrFail($id);

        // Only the lectures assigned to THIS teacher for this subject.
        // Fall back to all the subject's sessions if none carry a teacher_id yet (legacy).
        $sessionsQuery = Session::where('subject_id', $id)->with(['unit', 'files']);
        $hasTeacherSessions = (clone $sessionsQuery)->where('teacher_id', $teacher->id)->exists();
        if ($hasTeacherSessions) {
            $sessionsQuery->where('teacher_id', $teacher->id);
        }
        $sessions = $sessionsQuery->orderBy('session_number', 'asc')->get();

        $resolvedClassId = $subject->term?->class_id ?? null;

        $studentIds = \Illuminate\Support\Facades\DB::table('student_programs')
            ->where('class_id', $resolvedClassId)
            ->distinct()->pluck('student_id');
        $students = \App\Models\User::whereIn('id', $studentIds)
            ->where('role', 'student')
            ->with('program:id,name_ar,name_en')
            ->orderBy('name')->get();

        return view('teacher.subjects.show', compact('subject', 'sessions', 'students'));
    }

    /**
     * Show session creation form
     */
    public function createSession($subjectId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)
            ->with('units')
            ->findOrFail($subjectId);

        // Get next session number
        $nextSessionNumber = Session::where('subject_id', $subjectId)->max('session_number') + 1;

        return view('teacher.subjects.sessions.create', compact('subject', 'nextSessionNumber'));
    }

    /**
     * Store a new session
     */
    public function storeSession(Request $request, $subjectId)
    {
        $teacher = auth()->user();

        // Verify subject belongs to teacher
        $subject = Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $validated = $request->validate([
            'unit_id'          => 'nullable|exists:units,id',
            'title_ar'         => 'nullable|string|max:255',
            'title_en'         => 'nullable|string|max:255',
            'description_ar'   => 'nullable|string',
            'description_en'   => 'nullable|string',
            'session_number'   => 'nullable|integer|min:1',
            'type'             => 'nullable|in:live_zoom,recorded_video',
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

        $validated['subject_id'] = $subjectId;
        $validated['teacher_id'] = $teacher->id;

        // Auto-fill required fields when not provided
        $nextNumber = $subject->sessions()->max('session_number') + 1 ?? 1;
        $validated['session_number'] = $validated['session_number'] ?? $nextNumber;
        $validated['title_ar'] = $validated['title_ar'] ?? (' محاضرة ' . $validated['session_number']);
        $validated['title_en'] = $validated['title_en'] ?? ('Session ' . $validated['session_number']);
        $validated['type']     = $validated['type'] ?? 'recorded_video';

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            $videoFile = $request->file('video_file');
            $path = $videoFile->store('session-videos', 'public');

            $validated['video_path'] = $path;
            $validated['video_platform'] = 'local';
            $validated['video_size'] = $videoFile->getSize();
        }

        // Filter out null values
        $validated = array_filter($validated, function($value) {
            return $value !== null && $value !== '';
        });

        // Set default video platform
        if (!empty($validated['video_url']) && empty($validated['video_platform'])) {
            $validated['video_platform'] = 'external';
        }

        unset($validated['video_file']);

        // Create Zoom meeting if type is live_zoom
        if ($validated['type'] === 'live_zoom' && empty($validated['zoom_meeting_id'])) {
            try {
                $meetingData = [
                    'topic' => $validated['title_ar'],
                    'type' => 2,
                    'start_time' => isset($validated['scheduled_at'])
                        ? \Carbon\Carbon::parse($validated['scheduled_at'])->toIso8601String()
                        : now()->addHour()->toIso8601String(),
                    'duration' => $validated['duration_minutes'] ?? 60,
                    'timezone' => 'Asia/Riyadh',
                    'agenda' => $validated['description_ar'] ?? '',
                ];

                $meeting = $this->zoomService->createMeeting($meetingData);

                if ($meeting) {
                    $validated['zoom_meeting_id'] = $meeting['id'];
                    $validated['zoom_join_url'] = $meeting['join_url'];
                    $validated['zoom_password'] = $meeting['password'] ?? null;

                    Log::info('Zoom meeting created by teacher', [
                        'meeting_id' => $meeting['id'],
                        'teacher_id' => $teacher->id,
                        'title' => $validated['title_ar']
                    ]);
                } else {
                    return back()->withInput()->withErrors([
                        'zoom' => 'فشل إنشاء اجتماع Zoom تلقائياً. يرجى التحقق من إعدادات Zoom API.'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Zoom creation exception by teacher: ' . $e->getMessage());
                return back()->withInput()->withErrors([
                    'zoom' => 'حدث خطأ أثناء إنشاء اجتماع Zoom: ' . $e->getMessage()
                ]);
            }
        }

        $session = Session::create($validated);

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $index => $file) {
                $path = $file->store('session-files', 'public');

                SessionFile::create([
                    'session_id' => $session->id,
                    'title' => $file->getClientOriginalName(),
                    'type' => 'pdf',
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'order' => $index + 1,
                ]);
            }
        }

        // Notify enrolled students
        $this->notificationService->notifySessionCreated($session);

        return redirect()->route('teacher.my-subjects.show', $subjectId)
            ->with('success', 'تم إضافة ال محاضرة بنجاح');
    }

    /**
     * Show session edit form
     */
    public function editSession($subjectId, $sessionId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)
            ->with('units')
            ->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)
            ->with('files')
            ->findOrFail($sessionId);

        return view('teacher.subjects.sessions.edit', compact('subject', 'session'));
    }

    /**
     * Update session
     */
    public function updateSession(Request $request, $subjectId, $sessionId)
    {
        $teacher = auth()->user();

        // Verify subject belongs to teacher
        Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)->findOrFail($sessionId);

        $validated = $request->validate([
            'unit_id' => 'nullable|exists:units,id',
            'title_ar' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'description_ar' => 'nullable|string',
            'description_en' => 'nullable|string',
            'session_number' => 'required|integer|min:1',
            'type' => 'required|in:live_zoom,recorded_video',
            'scheduled_at' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:1',

            // Zoom fields
            'zoom_meeting_id' => 'nullable|string',
            'zoom_join_url' => 'nullable|url',
            'zoom_password' => 'nullable|string',

            // Video fields
            'video_file' => 'nullable|file|mimes:mp4,avi,mov,mkv,wmv,flv|max:512000',
            'video_url' => 'nullable|url',
            'video_platform' => 'nullable|in:youtube,vimeo,external,local',

            // File uploads
            'files.*' => 'nullable|file|max:10240',
        ]);

        $validated['teacher_id'] = $teacher->id;

        // Handle video file upload
        if ($request->hasFile('video_file')) {
            // Delete old video if exists
            if ($session->video_path) {
                Storage::disk('public')->delete($session->video_path);
            }

            $videoFile = $request->file('video_file');
            $path = $videoFile->store('session-videos', 'public');

            $validated['video_path'] = $path;
            $validated['video_platform'] = 'local';
            $validated['video_size'] = $videoFile->getSize();
        }

        // Filter out null values
        $validated = array_filter($validated, function($value) {
            return $value !== null;
        });

        if (!empty($validated['video_url']) && empty($validated['video_platform'])) {
            $validated['video_platform'] = 'external';
        }

        unset($validated['video_file']);

        // Create Zoom meeting if switching to live_zoom
        if ($validated['type'] === 'live_zoom' && empty($validated['zoom_meeting_id']) && empty($session->zoom_meeting_id)) {
            try {
                $meetingData = [
                    'topic' => $validated['title_ar'],
                    'type' => 2,
                    'start_time' => isset($validated['scheduled_at'])
                        ? \Carbon\Carbon::parse($validated['scheduled_at'])->toIso8601String()
                        : now()->addHour()->toIso8601String(),
                    'duration' => $validated['duration_minutes'] ?? 60,
                    'timezone' => 'Asia/Riyadh',
                    'agenda' => $validated['description_ar'] ?? '',
                ];

                $meeting = $this->zoomService->createMeeting($meetingData);

                if ($meeting) {
                    $validated['zoom_meeting_id'] = $meeting['id'];
                    $validated['zoom_join_url'] = $meeting['join_url'];
                    $validated['zoom_password'] = $meeting['password'] ?? null;
                } else {
                    return back()->withInput()->withErrors([
                        'zoom' => 'فشل إنشاء اجتماع Zoom تلقائياً.'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Zoom creation exception: ' . $e->getMessage());
                return back()->withInput()->withErrors([
                    'zoom' => 'حدث خطأ أثناء إنشاء اجتماع Zoom: ' . $e->getMessage()
                ]);
            }
        }

        $session->update($validated);

        // Handle new file uploads
        if ($request->hasFile('files')) {
            $currentMaxOrder = $session->files()->max('order') ?? 0;

            foreach ($request->file('files') as $index => $file) {
                $path = $file->store('session-files', 'public');

                SessionFile::create([
                    'session_id' => $session->id,
                    'title' => $file->getClientOriginalName(),
                    'type' => 'pdf',
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'order' => $currentMaxOrder + $index + 1,
                ]);
            }
        }

        // Notify enrolled students about the update
        $this->notificationService->notifySessionUpdated($session);

        return redirect()->route('teacher.my-subjects.show', $subjectId)
            ->with('success', 'تم تحديث ال محاضرة بنجاح');
    }

    /**
     * Delete session
     */
    public function destroySession($subjectId, $sessionId)
    {
        $teacher = auth()->user();

        // Verify subject belongs to teacher
        Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)->findOrFail($sessionId);

        // Delete Zoom meeting if exists
        if ($session->zoom_meeting_id) {
            try {
                $this->zoomService->deleteMeeting($session->zoom_meeting_id);
            } catch (\Exception $e) {
                Log::error('Zoom deletion exception: ' . $e->getMessage());
            }
        }

        // Delete video if exists
        if ($session->video_path) {
            Storage::disk('public')->delete($session->video_path);
        }

        // Delete associated files
        foreach ($session->files as $file) {
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }

        $session->delete();

        return redirect()->route('teacher.my-subjects.show', $subjectId)
            ->with('success', 'تم حذف ال محاضرة بنجاح');
    }

    /**
     * Delete session file
     */
    public function deleteSessionFile($subjectId, $sessionId, $fileId)
    {
        $teacher = auth()->user();

        Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);
        Session::where('subject_id', $subjectId)->findOrFail($sessionId);

        $file = SessionFile::where('session_id', $sessionId)->findOrFail($fileId);

        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return back()->with('success', 'تم حذف الملف بنجاح');
    }

    /**
     * Show Zoom meeting page (fullscreen)
     */
    public function showZoom($subjectId, $sessionId)
    {
        $teacher = auth()->user();

        Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)
            ->with('subject')
            ->findOrFail($sessionId);

        return view('teacher.subjects.sessions.zoom', compact('session'));
    }

    /**
     * Show Zoom meeting page (embedded in dashboard)
     */
    public function showZoomEmbedded($subjectId, $sessionId)
    {
        $teacher = auth()->user();

        Subject::assignedToTeacher($teacher->id)->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)
            ->with('subject')
            ->findOrFail($sessionId);

        return view('teacher.subjects.sessions.zoom-embedded', compact('session'));
    }

    /**
     * Show session attendance
     */
    public function sessionAttendance($subjectId, $sessionId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)
            ->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)
            ->findOrFail($sessionId);

        // All students assigned to this session by admin (via Attendance table)
        $attendances = Attendance::where('session_id', $sessionId)
            ->with('student')
            ->orderBy('joined_at', 'desc')
            ->get();

        $totalAssigned = $attendances->count();
        $attendedCount = $attendances->where('attended', true)->count();
        $absentStudents = $attendances->where('attended', false)->pluck('student')->filter()->values();

        $stats = [
            'total_enrolled'  => $totalAssigned,
            'attended'        => $attendedCount,
            'absent'          => $absentStudents->count(),
            'attendance_rate' => $totalAssigned > 0
                ? round(($attendedCount / $totalAssigned) * 100, 1)
                : 0,
        ];

        return view('teacher.subjects.sessions.attendance', compact('subject', 'session', 'attendances', 'absentStudents', 'stats'));
    }

    /**
     * Save/add attendance records manually
     */
    public function saveAttendance(Request $request, $subjectId, $sessionId)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)
            ->findOrFail($subjectId);

        $session = Session::where('subject_id', $subjectId)
            ->findOrFail($sessionId);

        $validated = $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        foreach ($validated['student_ids'] as $studentId) {
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'session_id' => $session->id],
                [
                    'attended'    => true,
                    'joined_at'   => now(),
                ]
            );
        }

        return back()->with('success', 'تم تسجيل الحضور بنجاح');
    }

    /**
     * Attendance overview for all teacher's sessions (subjects + programs)
     */
    public function attendanceOverview()
    {
        $teacher = auth()->user();

        $sessionQuery = function ($q) {
            $q->where('scheduled_at', '<', now())
              ->withCount(['attendances as attended_count' => fn($q) => $q->where('attended', true)])
              ->orderBy('scheduled_at', 'desc');
        };

        // Classes this teacher is assigned to teach.
        $classIds = \App\Models\ProgramClass::where('teacher_id', $teacher->id)->pluck('id');

        $subjects = Subject::where(function ($q) use ($teacher, $classIds) {
                $q->whereIn('class_id', $classIds)
                  ->orWhereHas('term', fn($tq) => $tq->whereIn('class_id', $classIds))
                  ->orWhere(fn($aq) => $aq->assignedToTeacher($teacher->id));
            })
            ->where(fn($q) => $q
                ->whereHas('program', fn($pq) => $pq->where('type', 'diploma'))
                ->orWhereHas('term.program', fn($pq) => $pq->where('type', 'diploma'))
            )
            ->with(['sessions' => $sessionQuery, 'programClass', 'term.programClass'])
            ->withCount('enrollments')
            ->get();

        // Programs reachable via assigned classes or direct assignment.
        $classProgramIds  = \App\Models\ProgramClass::where('teacher_id', $teacher->id)->pluck('program_id');
        $directProgramIds = $teacher->teachingPrograms()->pluck('programs.id');
        $programIds       = $classProgramIds->merge($directProgramIds)->unique()->values();

        $programs = \App\Models\Program::whereIn('id', $programIds)
            ->whereIn('type', ['training', 'english', 'course'])
            ->with(['sessions' => $sessionQuery, 'classes' => fn($q) => $q->where('teacher_id', $teacher->id)])
            ->withCount('enrolledStudents as enrolled_count')
            ->get();

        return view('teacher.attendance.index', compact('subjects', 'programs'));
    }
}
