<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Session;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $sessions = Session::with(['subject.term.program', 'program', 'teacher', 'programClass'])
            ->orderBy('scheduled_at')
            ->get();

        $calSessions = $sessions->map(function ($s) {
            $subjectName  = $s->subject->name_ar ?? null;
            $programName  = $s->subject->term->program->name_ar ?? $s->program->name_ar ?? null;
            $className    = $s->programClass->name ?? null;
            $displayName  = $subjectName ?? $programName ?? 'جلسة';
            $isCourse     = is_null($s->subject_id) && !is_null($s->program_id);

            return [
                'id'               => $s->id,
                'title'            => $s->title_ar ?: ($displayName . ' #' . $s->session_number),
                'subject_name'     => $subjectName ?? $programName ?? '',
                'program_name'     => $programName ?? '',
                'class_name'       => $className ?? '',
                'class_id'         => $s->programClass->id ?? null,
                'teacher_name'     => $s->teacher?->name ?? '',
                'scheduled_at'     => $s->scheduled_at ? \Carbon\Carbon::parse($s->scheduled_at)->toIso8601String() : null,
                'duration_minutes' => $s->duration_minutes,
                'type'             => $s->type ?? '',
                'status'           => $s->status ?? '',
                'session_number'   => $s->session_number,
                'subject_id'       => $s->subject_id,
                'program_id'       => $s->program_id,
                'is_course'        => $isCourse,
                'attendance_count' => $s->attendances()->count(),
            ];
        })->filter(fn($s) => $s['scheduled_at'])->values();

        $stats = [
            'total'     => $sessions->count(),
            'upcoming'  => $sessions->filter(fn($s) => $s->scheduled_at && \Carbon\Carbon::parse($s->scheduled_at)->isFuture())->count(),
            'live'      => $sessions->where('status', 'live')->count(),
            'completed' => $sessions->where('status', 'completed')->count(),
        ];

        return view('admin.schedule.index', compact('calSessions', 'stats'));
    }

    public function sessionStudents(Session $session)
    {
        $session->load('subject.term');

        // Students enrolled directly in the subject (course/training programs)
        $fromEnrollments = Enrollment::where('subject_id', $session->subject_id)
            ->where('status', 'active')
            ->with('student:id,name,email')
            ->get()
            ->pluck('student')
            ->filter();

        // Students enrolled in the diploma program that owns this subject's term,
        // scoped to the session's class when available.
        $programId = $session->subject?->term?->program_id;
        $fromProgram = $programId
            ? User::where('role', 'student')
                ->where('program_id', $programId)
                ->where('program_status', 'approved')
                ->when($session->class_id, fn($q) => $q->where('class_id', $session->class_id))
                ->get(['id', 'name', 'email'])
            : collect();

        $assigned = Attendance::where('session_id', $session->id)
            ->pluck('student_id')
            ->toArray();

        $students = $fromEnrollments->merge($fromProgram)
            ->unique('id')
            ->values()
            ->map(fn($s) => [
                'id'       => $s->id,
                'name'     => $s->name,
                'email'    => $s->email,
                'assigned' => in_array($s->id, $assigned),
            ]);

        return response()->json([
            'session' => [
                'id'           => $session->id,
                'title'        => $session->title_ar ?: (($session->subject->name_ar ?? 'جلسة') . ' #' . $session->session_number),
                'scheduled_at' => $session->scheduled_at,
            ],
            'students' => $students,
        ]);
    }

    public function assignStudents(Request $request, Session $session)
    {
        $request->validate([
            'student_ids'   => 'required|array', 
            'student_ids.*' => 'integer|exists:users,id',
        ]);

        $inserted = 0;
        foreach ($request->student_ids as $studentId) {
            $exists = Attendance::where('session_id', $session->id)
                ->where('student_id', $studentId)
                ->exists();

            if (!$exists) {
                Attendance::create([
                    'session_id' => $session->id,
                    'student_id' => $studentId,
                    'attended'   => false,
                ]);
                $inserted++;
            }
        }

        return response()->json(['success' => true, 'inserted' => $inserted]);
    }

    // Return programs/subjects for the generate modal
    public function getPrograms()
    {
        $programs = Program::where('status', 'active')
            ->orderBy('name_ar')
            ->get(['id', 'name_ar', 'type', 'duration_months']);

        $subjects = Subject::where('status', 'active')
            ->whereNull('class_id') // exclude class-specific clones from the global picker
            ->where(fn($q) => $q->whereNotNull('program_id')->orWhereNotNull('term_id'))
            ->with(['program:id,name_ar', 'term.program:id,name_ar'])
            ->orderBy('name_ar')
            ->get()
            ->map(function($s) {
                $programId   = $s->program_id ?? $s->term?->program_id;
                $programName = $s->program?->name_ar ?? $s->term?->program?->name_ar;
                if (!$programId) return null;
                return [
                    'id'           => $s->id,
                    'name_ar'      => $s->name_ar,
                    'program_id'   => $programId,
                    'program_name' => $programName,
                ];
            })
            ->filter()
            ->values();

        return response()->json(['programs' => $programs, 'subjects' => $subjects]);
    }

    // Return classes for a given program
    public function getClasses(Request $request)
    {
        $programId = $request->query('program_id');
        $classes = ProgramClass::where('program_id', $programId)
            ->where('status', 'active')
            ->withCount('students')
            ->with('teacher:id,name')
            ->get(['id', 'name', 'teacher_id', 'start_date', 'end_date', 'max_students']);

        return response()->json(['classes' => $classes]);
    }

    // Generate weekly recurring sessions for a class
    public function generate(Request $request)
    {
        $data = $request->validate([
            'entity_type'      => 'required|in:program,subject',
            'entity_id'        => 'required|integer',
            'class_id'         => 'required|exists:program_classes,id',
            'teacher_id'       => 'required|exists:users,id',
            'days'             => 'required|array|min:1',
            'days.*'           => 'integer|min:0|max:6',
            'time'             => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after:start_date',
            'session_type'     => 'required|in:live_zoom,recorded_video,in_person',
        ]);

        $class = ProgramClass::with('students')->findOrFail($data['class_id']);

        // Resolve FK column and value
        if ($data['entity_type'] === 'program') {
            $fkCol = 'program_id';
            $fkVal = $data['entity_id'];
            $label = Program::find($fkVal)?->name_ar ?? 'جلسة';
        } else {
            $fkCol = 'subject_id';
            $fkVal = $data['entity_id'];
            $label = Subject::find($fkVal)?->name_ar ?? 'جلسة';
        }

        // The teacher chosen in the admin schedule form — and only that — is used.
        // No fallback to subject teacher or class supervisor.
        $teacherId = $data['teacher_id'];
        [$hh, $mm]  = explode(':', $data['time']);
        $start      = Carbon::parse($data['start_date']);
        $end        = Carbon::parse($data['end_date']);
        $days       = array_map('intval', $data['days']);

        // Build list of matching dates
        $dates = [];
        $cur   = $start->copy();
        while ($cur->lte($end)) {
            if (in_array($cur->dayOfWeek, $days)) {
                $dates[] = $cur->copy()->setHour((int)$hh)->setMinute((int)$mm)->setSecond(0);
            }
            $cur->addDay();
        }

        if (empty($dates)) {
            return response()->json(['success' => false, 'message' => 'لا توجد أيام مطابقة في الفترة المحددة'], 422);
        }

        $nextNumber = Session::where($fkCol, $fkVal)->max('session_number') ?? 0;
        $students   = $class->students;
        $created    = 0;

        foreach ($dates as $scheduledAt) {
            $nextNumber++;
            $sessionTitle = $label . ' (#' . $nextNumber . ')';

            $session = Session::create([
                $fkCol             => $fkVal,
                'class_id'         => $class->id,
                'teacher_id'       => $teacherId,
                'type'             => $data['session_type'],
                'status'           => 'scheduled',
                'scheduled_at'     => $scheduledAt,
                'duration_minutes' => $data['duration_minutes'],
                'session_number'   => $nextNumber,
                'title_ar'         => $sessionTitle,
            ]);

            foreach ($students as $student) {
                Attendance::firstOrCreate([
                    'session_id' => $session->id,
                    'student_id' => $student->id,
                ], ['attended' => false]);
            }

            $created++;
        }

        $msg = "تم إنشاء {$created} جلسة وإسناد {$students->count()} متدرب لكل جلسة";

        return response()->json([
            'success'  => true,
            'created'  => $created,
            'students' => $students->count(),
            'message'  => $msg,
        ]);
    }
}
