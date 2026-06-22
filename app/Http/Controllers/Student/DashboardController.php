<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Session;
use App\Models\SessionFile;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\TeacherRating;
use App\Models\Term;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // ── Shared helper ──────────────────────────────────────────────────────────
    private function studentProgramIds($student): \Illuminate\Support\Collection
    {
        return $student->allProgramIds();
    }

    /**
     * Build the weekly-calendar session payload for a student, shared by the
     * dashboard and the my-sessions page so both render the same schedule.
     */
    private function buildClassSessionsCalendar($student): \Illuminate\Support\Collection
    {
        $programIds = $this->studentProgramIds($student);

        $studentClassIds = $programIds
            ->map(fn($pid) => $student->classIdForProgram((int) $pid))
            ->filter()->unique()->values();

        // Sessions assigned to this student (via attendance), scoped to their classes + class-agnostic
        $assignedSessionIds = Attendance::where('student_id', $student->id)->pluck('session_id');
        if ($studentClassIds->isNotEmpty()) {
            $assignedSessionIds = Session::whereIn('id', $assignedSessionIds)
                ->where(fn($q) => $q->whereIn('class_id', $studentClassIds)->orWhereNull('class_id'))
                ->pluck('id');
        }

        $allAttendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $assignedSessionIds)
            ->get()->keyBy('session_id');

        $apologies = \App\Models\AttendanceApology::where('student_id', $student->id)
            ->get()->keyBy('session_id');

        $assignedClassIds = Session::whereIn('id', $assignedSessionIds)
            ->whereNotNull('class_id')->pluck('class_id');

        $calendarClassIds = $studentClassIds->merge($assignedClassIds)->filter()->unique()->values();

        return Session::whereNotNull('scheduled_at')
            ->where(function ($q) use ($calendarClassIds, $assignedSessionIds) {
                $q->whereIn('class_id', $calendarClassIds)
                  ->orWhereIn('id', $assignedSessionIds);
            })
            ->with(['subject:id,name_ar,name_en', 'teacher:id,name'])
            ->orderBy('scheduled_at')
            ->get()
            ->map(function ($s) use ($allAttendances, $apologies) {
                $att = $allAttendances[$s->id] ?? null;
                $apo = $apologies[$s->id] ?? null;
                return [
                    'id'               => $s->id,
                    'title'            => $s->title_ar ?: ($s->subject->name_ar ?? 'جلسة'),
                    'subject_name'     => $s->subject->name_ar ?? '',
                    'teacher_name'     => $s->teacher->name ?? '',
                    'scheduled_at'     => \Carbon\Carbon::parse($s->scheduled_at)->toIso8601String(),
                    'duration_minutes' => $s->duration_minutes ?? 60,
                    'type'             => $s->type ?? '',
                    'status'           => (string) ($s->status ?? ''),
                    'session_number'   => $s->session_number,
                    'zoom_join_url'    => $s->zoom_link ?? $s->zoom_join_url ?? null,
                    'zoom_start_url'   => $s->zoom_start_url ?? null,
                    'started_at'       => $s->started_at ? \Carbon\Carbon::parse($s->started_at)->toIso8601String() : null,
                    'ended_at'         => $s->ended_at ? \Carbon\Carbon::parse($s->ended_at)->toIso8601String() : null,
                    'attended'         => $att ? (bool) $att->attended : null,
                    'apology_status'   => $apo?->status,
                ];
            })
            ->values();
    }

    /**
     * JSON feed for the student's weekly calendar (used for realtime polling so the
     * "join" button appears as soon as the teacher starts a session).
     */
    public function calendarSessions()
    {
        return response()->json([
            'sessions' => $this->buildClassSessionsCalendar(auth()->user()),
        ]);
    }

    private function studentSubjectIds($student): \Illuminate\Support\Collection
    {
        $programIds = $this->studentProgramIds($student);

        // The student's class per program (to scope subjects to their class + shared)
        $classIds = $programIds->map(fn($pid) => $student->classIdForProgram((int) $pid))
            ->filter()->unique()->values()->all();

        return Subject::where(function ($q) use ($student, $programIds) {
                $q->whereIn('program_id', $programIds)
                  ->orWhereHas('term', fn($tq) => $tq->whereIn('program_id', $programIds))
                  ->orWhereHas('enrollments', fn($eq) => $eq->where('student_id', $student->id));
            })
            // Class scoping: show class-specific subjects of the student's class + shared (NULL)
            ->where(function ($q) use ($classIds) {
                $q->whereNull('class_id');
                if (!empty($classIds)) {
                    $q->orWhereIn('class_id', $classIds);
                }
            })
            ->pluck('id');
    }
    // ──────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $student = auth()->user();

        // Check if student is assigned to a program
        if (!$student->program_id) {
            return redirect()->route('student.my-program');
        }

        $studentSubjectIds = $this->studentSubjectIds($student);

        $studentProgramIds = $this->studentProgramIds($student);

        // Student's class IDs (one per program)
        $studentClassIds = $studentProgramIds
            ->map(fn($pid) => $student->classIdForProgram((int) $pid))
            ->filter()->unique()->values()->all();

        // Get student's subjects scoped to their class (or program if not in a class)
        $subjects = Subject::whereIn('id', $studentSubjectIds)
            ->when(
                !empty($studentClassIds),
                fn($q) => $q->whereIn('class_id', $studentClassIds),
                fn($q) => $q->whereIn('program_id', $studentProgramIds)
            )
            ->with(['term.program', 'teacher'])
            ->withCount('sessions')
            ->get();

        // Subject IDs already filtered (class or program)
        $classSubjectIds = $subjects->pluck('id');

        // Session scope: scoped to the student's class if assigned, otherwise by program
        $sessionScope = fn($q) => $q->where(function ($inner) use ($classSubjectIds, $studentProgramIds, $studentClassIds) {
            if (!empty($studentClassIds)) {
                $inner->whereIn('class_id', $studentClassIds);
            } else {
                $inner->whereIn('subject_id', $classSubjectIds)
                      ->orWhereIn('program_id', $studentProgramIds);
            }
        });

        // Get upcoming sessions
        $upcomingSessions = Session::where($sessionScope)
            ->where('scheduled_at', '>', now())
            ->with(['subject', 'program'])
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        // Get recent sessions
        $recentSessions = Session::where($sessionScope)
            ->with(['subject', 'program'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get live sessions
        $liveSessions = Session::where($sessionScope)
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->with(['subject', 'program'])
            ->get();

        // Attendance rate scoped to the student's sessions
        $classSessionIds = Session::when(
                !empty($studentClassIds),
                fn($q) => $q->whereIn('class_id', $studentClassIds),
                fn($q) => $q->whereIn('subject_id', $classSubjectIds)->orWhereIn('program_id', $studentProgramIds)
            )
            ->pluck('id');

        $totalAtt   = Attendance::where('student_id', $student->id)->whereIn('session_id', $classSessionIds)->count();
        $presentAtt = Attendance::where('student_id', $student->id)->whereIn('session_id', $classSessionIds)->where('attended', true)->count();
        $overallAttendance = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100, 1) : 0;

        // Statistics
        $stats = [
            'subjects_count'     => $classSubjectIds->count(),
            'total_sessions'     => Session::whereIn('id', $classSessionIds)->count(),
            'completed_sessions' => Session::whereIn('id', $classSessionIds)->whereNotNull('ended_at')->count(),
            'live_sessions'      => $liveSessions->count(),
        ];

        // NELC: My tickets
        $myTickets = Ticket::where('user_id', $student->id)
            ->latest()
            ->limit(5)
            ->get();

        $openTicketsCount = Ticket::where('user_id', $student->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        // NELC: Teachers I can rate
        $ratableTeachers = Subject::whereIn('id', $classSubjectIds)
            ->whereNotNull('teacher_id')
            ->whereDoesntHave('teacherRatings', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->with('teacher')
            ->get();

        // Ratings I've submitted
        $myRatings = TeacherRating::where('student_id', $student->id)
            ->with(['teacher:id,name', 'subject:id,name_ar'])
            ->latest()
            ->limit(5)
            ->get();

        // Weekly-calendar sessions — same schedule shown on /student/my-sessions
        $classSessions = $this->buildClassSessionsCalendar($student);

        return view('student.dashboard', compact(
            'subjects',
            'upcomingSessions',
            'recentSessions',
            'liveSessions',
            'classSessions',
            'stats',
            'overallAttendance',
            'myTickets',
            'openTicketsCount',
            'ratableTeachers',
            'myRatings'
        ));
    }

    public function showSubject($id)
    {
        $student = auth()->user();

        $programIds = $this->studentProgramIds($student);
        $classIds   = $programIds->map(fn($pid) => $student->classIdForProgram((int) $pid))->filter()->unique()->values();

        // Sessions the student is assigned to (via attendance), scoped to their classes
        $assignedSessionIds = Attendance::where('student_id', $student->id)->pluck('session_id');
        if ($classIds->isNotEmpty()) {
            $assignedSessionIds = Session::whereIn('id', $assignedSessionIds)
                ->where(fn($q) => $q->whereIn('class_id', $classIds)->orWhereNull('class_id'))
                ->pluck('id');
        }

        // Check if any of the student's programs is a diploma
        $allPrograms = \App\Models\Program::whereIn('id', $programIds)->get();
        $isDiploma   = $allPrograms->contains(fn($p) => $p->type === 'diploma');

        // Diploma: allow access to any subject in student's programs/classes
        // Non-diploma: require at least one assigned session in this subject
        if ($isDiploma) {
            $subject = Subject::where(function ($q) use ($programIds, $classIds) {
                    $q->whereHas('term', fn($tq) => $tq->whereIn('program_id', $programIds));
                    if ($classIds->isNotEmpty()) {
                        $q->orWhereIn('class_id', $classIds)
                          ->orWhereHas('term', fn($tq) => $tq->whereIn('class_id', $classIds));
                    }
                })
                ->with(['term.program', 'teacher'])
                ->findOrFail($id);
        } else {
            $subject = Subject::whereHas('sessions', fn($q) => $q->whereIn('id', $assignedSessionIds))
                ->with(['term.program', 'teacher'])
                ->findOrFail($id);
        }

        // Sessions for this subject — class-scoped
        $sessionsQuery = Session::where('subject_id', $id)->with(['files', 'homework']);
        if ($classIds->isNotEmpty()) {
            $sessionsQuery->where(fn($q) => $q->whereIn('class_id', $classIds)->orWhereNull('class_id'));
        }
        $sessions = $sessionsQuery->orderBy('session_number', 'asc')->get();

        // Get attendance for this subject
        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        // Collect all homework across all sessions
        $homeworks = $sessions->flatMap(fn($s) => $s->homework ? [$s->homework] : []);

        // Subject-level files
        $subject->load('files');

        return view('student.subject-detail', compact('subject', 'sessions', 'attendances', 'homeworks'));
    }

    /**
     * عرض جميع جلسات المتدرب مجمّعة حسب البرنامج
     */
    public function mySessions(Request $request)
    {
        $student    = auth()->user();
        $programIds = $this->studentProgramIds($student);
        $allPrograms = $student->allPrograms();

        // All classes the student belongs to (one per program — student can be in
        // multiple classes across different programs, never two in the same program)
        $studentClassIds = $programIds
            ->map(fn($pid) => $student->classIdForProgram((int) $pid))
            ->filter()->unique()->values();

        // Sessions assigned to this student (via attendance records), scoped to their
        // classes + any class-agnostic sessions (class_id NULL)
        $assignedSessionIds = Attendance::where('student_id', $student->id)->pluck('session_id');
        if ($studentClassIds->isNotEmpty()) {
            $assignedSessionIds = Session::whereIn('id', $assignedSessionIds)
                ->where(fn($q) => $q->whereIn('class_id', $studentClassIds)->orWhereNull('class_id'))
                ->pluck('id');
        }

        // Build per-program session data
        $programsSessionData = [];
        foreach ($allPrograms as $prog) {
            $isDiploma = $prog->type === 'diploma';

            if ($isDiploma) {
                // Diploma: sessions belong to subjects which belong to terms
                $progSessions = Session::whereIn('id', $assignedSessionIds)
                    ->whereHas('subject.term', fn($q) => $q->where('program_id', $prog->id))
                    ->with(['subject.term', 'subject.teacher', 'files', 'homework'])
                    ->orderBy('subject_id')->orderBy('session_number')->get();

                $sessionsBySubject = $progSessions->groupBy('subject_id');
            } else {
                // Non-diploma: sessions belong directly to the program
                $progSessions = Session::whereIn('id', $assignedSessionIds)
                    ->where('program_id', $prog->id)
                    ->with(['files', 'homework'])
                    ->orderBy('session_number')->orderBy('scheduled_at')->get();

                $sessionsBySubject = collect();
            }

            $progAttendances = Attendance::where('student_id', $student->id)
                ->whereIn('session_id', $progSessions->pluck('id'))
                ->get()->keyBy('session_id');

            $programsSessionData[$prog->id] = [
                'program'          => $prog,
                'isDiploma'        => $isDiploma,
                'sessions'         => $progSessions,
                'sessionsBySubject'=> $sessionsBySubject,
                'attendances'      => $progAttendances,
                'totalSessions'    => $progSessions->count(),
                'completedSessions'=> $progSessions->whereNotNull('ended_at')->count(),
                'liveSessions'     => $progSessions->filter(fn($s) => $s->started_at && !$s->ended_at)->count(),
            ];
        }

        // Aggregate totals
        $allSessions       = collect($programsSessionData)->flatMap(fn($d) => $d['sessions']);
        $totalSessions     = $allSessions->count();
        $completedSessions = $allSessions->whereNotNull('ended_at')->count();
        $zoomSessions      = $allSessions->where('type', 'live_zoom')->count();
        $liveSessions      = $allSessions->filter(fn($s) => $s->started_at && !$s->ended_at)->count();

        $allAttendances = collect($programsSessionData)
            ->flatMap(fn($d) => $d['attendances']->all())
            ->keyBy('session_id');

        // Student's apologies keyed by session_id (for showing status / hiding submit button)
        $apologies = \App\Models\AttendanceApology::where('student_id', $student->id)
            ->get()->keyBy('session_id');

        // Calendar sessions (weekly schedule) — shared with the dashboard view.
        $classSessions = $this->buildClassSessionsCalendar($student);

        $firstProg = $allPrograms->first();

        return view('student.sessions.index', [
            'classSessions'       => $classSessions,
            'apologies'           => $apologies,
            'programsSessionData' => $programsSessionData,
            'totalSessions'       => $totalSessions,
            'completedSessions'   => $completedSessions,
            'zoomSessions'        => $zoomSessions,
            'liveSessions'        => $liveSessions,
            'attendances'         => $allAttendances,
            // legacy compat vars (used by header subtitle etc.)
            'isDiploma'           => $firstProg && $firstProg->type === 'diploma',
            'sessions'            => $allSessions,
            'sessionsBySubject'   => collect($programsSessionData)->first()['sessionsBySubject'] ?? collect(),
            'programSubjects'     => collect(),
            'filterSubjects'      => collect(),
            'currentTerm'         => null,
            'termFilter'          => 'all',
            'subjectId'           => null,
            'type'                => null,
        ]);
    }

    /**
     * عرض سجل الحضور — مقسم حسب البرنامج
     */
    public function attendance(Request $request)
    {
        $student = auth()->user();

        // Simplified: attendance is only counted when the student actually joins a
        // session — so we show ONLY the sessions the student joined (attended = true).
        $attendances = Attendance::where('student_id', $student->id)
            ->where('attended', true)
            ->whereHas('session')
            ->with(['session.subject', 'session.program', 'session.unit'])
            ->orderByDesc('joined_at')
            ->paginate(20);

        // Stats based on joined sessions only
        $base = Attendance::where('student_id', $student->id)->where('attended', true)->whereHas('session');
        $attendedSessions = (clone $base)->count();
        $totalMinutes     = (clone $base)->sum('duration_minutes') ?? 0;

        return view('student.attendance.index', [
            'attendances'      => $attendances,
            'attendedSessions' => $attendedSessions,
            'totalMinutes'     => $totalMinutes,
        ]);
    }

    /**
     * عرض الجلسات القادمة
     */
    public function upcomingSessions()
    {
        $student = auth()->user();

        // Get upcoming Zoom sessions
        $upcomingSessions = Session::whereHas('subject.enrollments', function($q) use ($student) {
                $q->where('student_id', $student->id)->where('status', 'active');
            })
            ->where('type', 'live_zoom')
            ->where('scheduled_at', '>=', now())
            ->with(['subject', 'unit'])
            ->orderBy('scheduled_at', 'asc')
            ->paginate(10);

        // Get live sessions (currently running)
        $liveSessions = Session::whereHas('subject.enrollments', function($q) use ($student) {
                $q->where('student_id', $student->id)->where('status', 'active');
            })
            ->where('type', 'live_zoom')
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->with(['subject', 'unit'])
            ->get();

        return view('student.sessions.upcoming', compact('upcomingSessions', 'liveSessions'));
    }

    /**
     * الانضمام إلى جلسة Zoom
     */
    public function joinZoom(Request $request, $sessionId)
    {
        $student = auth()->user();

        $session = Session::with('subject.term')->findOrFail($sessionId);

        // Students can't join until the teacher has started the session.
        if (!$session->started_at) {
            return back()->with('error', 'لم يبدأ المعلّم المحاضرة بعد. يرجى الانتظار حتى يبدأ المعلّم.');
        }

        // Record attendance immediately
        $attendance = Attendance::firstOrCreate(
            ['student_id' => $student->id, 'session_id' => $session->id],
            [
                'attended'   => true,
                'joined_at'  => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        if (!$attendance->wasRecentlyCreated) {
            $attendance->update([
                'attended'  => true,
                'joined_at' => $attendance->joined_at ?? now(),
            ]);
        }

        // Which link to open: ?link=start uses the host/start link, otherwise the
        // student join link. Attendance is recorded either way (above).
        $which = $request->query('link');
        if ($which === 'start' && !empty($session->zoom_start_url)) {
            return redirect($session->zoom_start_url);
        }

        // Redirect directly to zoom join url
        if (!empty($session->zoom_join_url)) {
            return redirect($session->zoom_join_url);
        }

        // If only the start link exists, use it
        if (!empty($session->zoom_start_url)) {
            return redirect($session->zoom_start_url);
        }

        // Fallback: embedded zoom view
        if (!empty($session->zoom_meeting_id)) {
            $zoomService = new ZoomService();
            $signature = $zoomService->generateSignature($session->zoom_meeting_id, 0);
            return view('student.sessions.zoom', compact('session', 'attendance', 'signature'));
        }

        return back()->with('error', 'هذه الجلسة لا تحتوي على رابط Zoom');
    }

    /**
     * تسجيل مغادرة جلسة Zoom
     */
    public function leaveZoom(Request $request, $sessionId)
    {
        $student = auth()->user();

        $attendance = Attendance::where('student_id', $student->id)
            ->where('session_id', $sessionId)
            ->first();

        if ($attendance) {
            $attendance->recordLeave();
        }

        return redirect()->route('student.upcoming-sessions')
            ->with('success', 'تم تسجيل مغادرتك بنجاح');
    }

    /**
     * عرض معلومات البرنامج التدريبي 
     */
    public function myProgram()
    {
        $student    = auth()->user();
        $programIds = $this->studentProgramIds($student);

        if ($student->program_status === 'pending') {
            return view('student.program.pending');
        }

        if ($programIds->isEmpty()) {
            $allPrograms = \App\Models\Program::where('status', 'active')->withCount('terms')->get();
            return view('student.program.index', [
                'programsData'        => [],
                'allAssignedPrograms' => collect(),
                'program'             => null,
                'track'               => null,
                'terms'               => collect(),
                'subjects'            => collect(),
                'stats'               => [],
                'enrollments'         => collect(),
                'availablePrograms'   => $allPrograms,
                'diplomaPrograms'     => $allPrograms->where('type', 'diploma')->values(),
                'coursePrograms'      => $allPrograms->whereIn('type', ['course', 'training'])->values(),
                'englishPrograms'     => $allPrograms->where('type', 'english')->values(),
            ]);
        }

        $allAssignedPrograms = $student->allPrograms();
        $currentTermNumber   = $student->current_term_number ?? 1;

        // Build per-program data for tabs
        $programsData = [];
        foreach ($allAssignedPrograms as $prog) {
            $isDiploma = $prog->type === 'diploma';

            if ($isDiploma) {
                $classId = $student->classIdForProgram((int) $prog->id);

                // Show the class's OWN terms only. If the class has none yet, fall back to
                // the program-wide (shared) terms so the page isn't empty.
                $hasClassTerms = $classId
                    ? Term::where('program_id', $prog->id)->where('class_id', $classId)->exists()
                    : false;

                $subjectScope = fn($sq) => $hasClassTerms
                    ? $sq->where('class_id', $classId)
                    : $sq->whereNull('class_id');

                $progTerms = Term::where('program_id', $prog->id)
                    ->when($hasClassTerms,
                        fn($q) => $q->where('class_id', $classId),
                        fn($q) => $q->whereNull('class_id'))
                    ->orderBy('term_number', 'asc')
                    ->with(['subjects' => fn($q) => $q->with(['teacher', 'sessions'])])
                    ->get();

                $currentTerm = $progTerms->first(fn($t) =>
                    $t->start_date && $t->end_date && $t->start_date <= now() && $t->end_date >= now()
                ) ?? $progTerms->firstWhere('term_number', $currentTermNumber) ?? $progTerms->first();

                $currentTermIndex = $currentTerm
                    ? $progTerms->search(fn($t) => $t->id === $currentTerm->id) + 1
                    : 1;

                $progSubjects = Subject::whereIn('term_id', $progTerms->pluck('id'))->get();

                $currentTermSubjects = $currentTerm
                    ? $currentTerm->subjects->load(['teacher', 'sessions'])
                    : $progSubjects;

                $subjectsProgress = [];
                foreach ($progSubjects as $subject) {
                    $tot = $subject->sessions()->count();
                    $att = Attendance::where('student_id', $student->id)
                        ->whereHas('session', fn($q) => $q->where('subject_id', $subject->id))
                        ->where('attended', true)->count();
                    $subjectsProgress[$subject->id] = [
                        'total' => $tot, 'attended' => $att,
                        'percentage' => $tot > 0 ? round(($att/$tot)*100,1) : 0,
                    ];
                }

                $totalSess = Session::whereHas('subject.enrollments', fn($q) => $q->where('student_id', $student->id))->count();
                $doneSess  = Session::whereHas('subject.enrollments', fn($q) => $q->where('student_id', $student->id))->whereNotNull('ended_at')->count();
                $totAtt    = Attendance::where('student_id', $student->id)->count();
                $presAtt   = Attendance::where('student_id', $student->id)->where('attended', true)->count();

                $programsData[$prog->id] = [
                    'program'             => $prog,
                    'isDiploma'           => true,
                    'terms'               => $progTerms,
                    'currentTerm'         => $currentTerm,
                    'currentTermIndex'    => $currentTermIndex,
                    'currentTermSubjects' => $currentTermSubjects,
                    'subjects'            => $progSubjects,
                    'subjectsProgress'    => $subjectsProgress,
                    'enrollments'         => collect(),
                    'programSessions'     => collect(),
                    'stats' => [
                        'total_subjects'      => $progSubjects->count(),
                        'total_sessions'      => $totalSess,
                        'completed_sessions'  => $doneSess,
                        'attendance_rate'     => $totAtt > 0 ? round(($presAtt/$totAtt)*100,1) : 0,
                        'current_term'        => $currentTermIndex,
                        'total_terms'         => $progTerms->count(),
                        'progress_percentage' => $progTerms->count() > 0
                            ? round(($currentTermIndex / $progTerms->count()) * 100, 1) : 0,
                    ],
                ];
            } else {
                // Scope course/English sessions to the student's class
                $classId = $student->classIdForProgram((int) $prog->id);
                $progSessions = Session::where('program_id', $prog->id)
                    ->when($classId, fn($q) => $q->where('class_id', $classId))
                    ->with(['files', 'homework'])
                    ->orderBy('session_number')->orderBy('scheduled_at')->get();

                $totAtt  = Attendance::where('student_id', $student->id)->whereIn('session_id', $progSessions->pluck('id'))->count();
                $presAtt = Attendance::where('student_id', $student->id)->whereIn('session_id', $progSessions->pluck('id'))->where('attended', true)->count();

                $programsData[$prog->id] = [
                    'program'             => $prog,
                    'isDiploma'           => false,
                    'terms'               => collect(),
                    'currentTerm'         => null,
                    'currentTermIndex'    => 1,
                    'currentTermSubjects' => collect(),
                    'subjects'            => collect(),
                    'subjectsProgress'    => [],
                    'enrollments'         => collect(),
                    'programSessions'     => $progSessions,
                    'stats' => [
                        'total_sessions'     => $progSessions->count(),
                        'completed_sessions' => $progSessions->whereNotNull('ended_at')->count(),
                        'attendance_rate'    => $totAtt > 0 ? round(($presAtt/$totAtt)*100,1) : 0,
                    ],
                ];
            }
        }

        // Legacy single-program vars for the view (used by non-tabbed sections)
        $firstData = collect($programsData)->first() ?? [];

        return view('student.program.index', [
            'programsData'        => $programsData,
            'allAssignedPrograms' => $allAssignedPrograms,
            // legacy compat
            'program'             => $firstData['program']             ?? null,
            'track'               => $student->track,
            'terms'               => $firstData['terms']               ?? collect(),
            'subjects'            => $firstData['subjects']            ?? collect(),
            'currentTermSubjects' => $firstData['currentTermSubjects'] ?? collect(),
            'currentTerm'         => $firstData['currentTerm']         ?? null,
            'currentTermIndex'    => $firstData['currentTermIndex']    ?? 1,
            'currentTermNumber'   => $currentTermNumber,
            'subjectsProgress'    => $firstData['subjectsProgress']    ?? [],
            'enrollments'         => $firstData['enrollments']         ?? collect(),
            'programSessions'     => $firstData['programSessions']     ?? collect(),
            'stats'               => $firstData['stats']               ?? [],
        ]);
    }

    /**
     * عرض تفاصيل جلسة من برنامج (تدريب / إنجليزي / دورة)
     */
    public function showSession(Session $session)
    {
        $student    = auth()->user();
        $programIds = $this->studentProgramIds($student);

        // Authorise: session must belong to one of student's programs
        if ($session->program_id && !$programIds->contains($session->program_id)) {
            abort(403);
        }

        $session->load(['files', 'homework.submissions' => fn($q) => $q->where('student_id', $student->id)]);

        $mySubmission = $session->homework
            ? $session->homework->submissions->first()
            : null;

        $attendance = Attendance::where('student_id', $student->id)
            ->where('session_id', $session->id)
            ->first();

        return view('student.sessions.show', compact('session', 'mySubmission', 'attendance'));
    }

    /**
     * التسجيل في برنامج دراسي
     */
    public function enrollInProgram(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
        ]);

        $student = auth()->user();

        // Check if student already has a program
        if ($student->program_id) {
            return back()->with('error', 'أنت مسجل بالفعل في برنامج دراسي');
        }

        // Get the program
        $program = \App\Models\Program::where('id', $request->program_id)
            ->where('status', 'active')
            ->firstOrFail();

        $isPaid = $program->price && $program->price > 0;

        $student->update([
            'program_id' => $program->id,
            'program_status' => 'pending',
            'current_term_number' => 1,
        ]);

        if ($isPaid) {
            return redirect()->route('student.my-program')
                ->with('info', 'يرجى التواصل مع الإدارة لإتمام التسجيل في هذا البرنامج.');
        }

        return redirect()->route('student.my-program')
            ->with('success', 'تم إرسال طلب التسجيل في البرنامج: ' . $program->name . '. في انتظار موافقة الإدارة.');
    }

    /**
     * عرض صفحة خدمة بعينها
     */
    public function showLink(string $service)
    {
        $services = [
            'portal' => [
                'key'         => 'student_portal_url',
                'title'       => 'البوابة الإلكترونية',
                'subtitle'    => 'بوابة ال متدرب الرسمية',
                'description' => 'منصة متكاملة تتيح لل متدرب الوصول إلى جميع خدماته الأكاديمية والإدارية في مكان واحد.',
                'icon_bg'     => 'linear-gradient(135deg,#3b82f6,#1d4ed8)',
                'badge_bg'    => '#dbeafe',
                'badge_color' => '#1d4ed8',
                'btn_bg'      => 'linear-gradient(135deg,#3b82f6,#1d4ed8)',
                'features'    => [
                    ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'السجل الأكاديمي', 'desc' => 'عرض درجاتك وسجلك الكامل'],
                    ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'title' => 'الإشعارات الرسمية', 'desc' => 'استقبال إشعارات الجامعة والكليات'],
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'طلبات الخدمات', 'desc' => 'تقديم الطلبات الإدارية إلكترونياً'],
                    ['icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', 'title' => 'التواصل الأكاديمي', 'desc' => 'التواصل مع الأقسام والمدربون '],
                ],
                'steps'       => ['افتح البوابة الإلكترونية', 'سجّل الدخول ببيانات الجامعة', 'اختر الخدمة التي تحتاجها'],
            ],
            'library' => [
                'key'         => 'library_url',
                'title'       => 'المكتبة الرقمية',
                'subtitle'    => 'مستودع المعرفة الأكاديمية',
                'description' => 'مكتبة إلكترونية شاملة تضم آلاف الكتب والمراجع والدوريات العلمية المحكّمة.',
                'icon_bg'     => 'linear-gradient(135deg,#8b5cf6,#6d28d9)',
                'badge_bg'    => '#ede9fe',
                'badge_color' => '#6d28d9',
                'btn_bg'      => 'linear-gradient(135deg,#8b5cf6,#6d28d9)',
                'features'    => [
                    ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'title' => 'كتب إلكترونية', 'desc' => 'آلاف الكتب والمراجع العلمية'],
                    ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'أبحاث ودوريات', 'desc' => 'مقالات علمية محكّمة من مجلات عالمية'],
                    ['icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4', 'title' => 'تنزيل مباشر', 'desc' => 'تحميل الملفات لقراءتها دون اتصال'],
                    ['icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'title' => 'بحث متقدم', 'desc' => 'فلترة حسب نوع المؤهل والسنة والمؤلف'],
                ],
                'steps'       => ['ادخل المكتبة الرقمية', 'ابحث بالكلمة المفتاحية أو التصنيف', 'اقرأ أو نزّل المرجع'],
            ],
            'blackboard' => [
                'key'         => 'blackboard_url',
                'title'       => 'نظام البلاك بورد',
                'subtitle'    => 'منصة إدارة التعلم',
                'description' => 'نظام متكامل لإدارة العملية التعليمية يربط  المتدربون  بالمحاضرين والمحتوى التدريبي .',
                'icon_bg'     => 'linear-gradient(135deg,#374151,#111827)',
                'badge_bg'    => '#f3f4f6',
                'badge_color' => '#374151',
                'btn_bg'      => 'linear-gradient(135deg,#4b5563,#1f2937)',
                'features'    => [
                    ['icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'title' => 'محاضرات مسجّلة', 'desc' => 'مشاهدة المحاضرات في أي وقت'],
                    ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'title' => 'الواجبات والتكاليف', 'desc' => 'تسليم واستلام الأعمال إلكترونياً'],
                    ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'متابعة التقدم', 'desc' => 'عرض درجاتك ومعدلك التراكمي'],
                    ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'title' => 'التواصل مع المحاضر', 'desc' => 'مراسلة المدربون  مباشرة'],
                ],
                'steps'       => ['ادخل إلى البلاك بورد', 'اختر المقرر  من قائمة مقرارتك', 'تصفح المحتوى أو سلّم الواجب'],
            ],
            'calendar' => [
                'key'         => 'calendar_url',
                'title'       => 'التقويم الأكاديمي',
                'subtitle'    => 'جدول العام التدريبي ',
                'description' => 'التقويم الرسمي للعام التدريبي يشمل مواعيد بداية الفصول والامتحانات والإجازات.',
                'icon_bg'     => 'linear-gradient(135deg,#0891b2,#0e7490)',
                'badge_bg'    => '#cffafe',
                'badge_color' => '#0e7490',
                'btn_bg'      => 'linear-gradient(135deg,#06b6d4,#0e7490)',
                'features'    => [
                    ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'title' => 'بداية الفصول', 'desc' => 'مواعيد بداية ونهاية كل فصل دراسي'],
                    ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'title' => 'فترات الامتحانات', 'desc' => 'جداول الاختبارات النصفية والنهائية'],
                    ['icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'title' => 'الأعياد والإجازات', 'desc' => 'مواعيد الإجازات الرسمية والمناسبات'],
                    ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'title' => 'تنبيهات مهمة', 'desc' => 'آخر مواعيد الإضافة والحذف'],
                ],
                'steps'       => ['افتح التقويم الأكاديمي', 'راجع المواعيد المهمة لفصلك الحالي', 'خطّط جدولك بناءً على التواريخ'],
            ],
            'support' => [
                'key'         => 'support_url',
                'title'       => 'الدعم الفني',
                'subtitle'    => 'نحن هنا لمساعدتك',
                'description' => 'فريق الدعم الفني جاهز للإجابة على استفساراتك وحل المشكلات التقنية التي تواجهها.',
                'icon_bg'     => 'linear-gradient(135deg,#16a34a,#15803d)',
                'badge_bg'    => '#dcfce7',
                'badge_color' => '#15803d',
                'btn_bg'      => 'linear-gradient(135deg,#22c55e,#15803d)',
                'features'    => [
                    ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'title' => 'دردشة فورية', 'desc' => 'تحدث مع فريق الدعم مباشرة'],
                    ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => 'البريد الإلكتروني', 'desc' => 'أرسل استفسارك وسيُرد عليك'],
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'حل سريع', 'desc' => 'وقت استجابة لا يتجاوز 24 ساعة'],
                    ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'title' => 'قاعدة المعرفة', 'desc' => 'إجابات للأسئلة الشائعة'],
                ],
                'steps'       => ['افتح بوابة الدعم الفني', 'اختر نوع المشكلة', 'تحدث مع المختص أو أرسل تذكرة'],
            ],
            'schedule' => [
                'key'         => 'schedule_url',
                'title'       => 'الجدول التدريبي ',
                'subtitle'    => 'جدولك الأسبوعي',
                'description' => 'اطّلع على جدولك التدريبي الأسبوعي مع أوقات المحاضرات وأماكن القاعات.',
                'icon_bg'     => 'linear-gradient(135deg,#f59e0b,#d97706)',
                'badge_bg'    => '#fef3c7',
                'badge_color' => '#b45309',
                'btn_bg'      => 'linear-gradient(135deg,#f59e0b,#d97706)',
                'features'    => [
                    ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'أوقات المحاضرات', 'desc' => 'مواعيد كل محاضرة أسبوعياً'],
                    ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z', 'title' => 'أماكن القاعات', 'desc' => 'رقم وموقع قاعة كل مادة'],
                    ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'title' => 'اسم المحاضر', 'desc' => 'معلومات المدربلكل مادة'],
                    ['icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4', 'title' => 'تحميل PDF', 'desc' => 'تنزيل الجدول للمشاهدة دون اتصال'],
                ],
                'steps'       => ['ادخل لنظام الجدول التدريبي ', 'تأكد من الفصل التدريبي الحالي', 'نزّل الجدول أو احفظه كصورة'],
            ],
        ];

        if (!isset($services[$service])) {
            abort(404);
        }

        $data = $services[$service];
        $data['url'] = \App\Models\Setting::where('key', $data['key'])->value('value') ?? null;
        $data['service'] = $service;

        return view('student.links.service', $data);
    }

    /**
     * عرض الروابط المفيدة
     */
    public function usefulLinks()
    {
        $student = auth()->user();

        $links = [
            [
                'title' => 'البوابة الإلكترونية',
                'url' => Setting::where('key', 'student_portal_url')->value('value') ?? '#',
                'icon' => 'portal',
                'description' => 'الوصول إلى البوابة الإلكترونية للطالب',
            ],
            [
                'title' => 'المكتبة الرقمية',
                'url' => Setting::where('key', 'library_url')->value('value') ?? '#',
                'icon' => 'library',
                'description' => 'تصفح الكتب والمراجع الإلكترونية',
            ],
            [
                'title' => 'الدعم الفني',
                'url' => Setting::where('key', 'support_url')->value('value') ?? '#',
                'icon' => 'support',
                'description' => 'التواصل مع فريق الدعم الفني',
            ],
            [
                'title' => 'الجدول التدريبي ',
                'url' => Setting::where('key', 'schedule_url')->value('value') ?? '#',
                'icon' => 'schedule',
                'description' => 'عرض الجدول التدريبي الخاص بك',
            ],
            [
                'title' => 'نظام البلاك بورد',
                'url' => Setting::where('key', 'blackboard_url')->value('value') ?? '#',
                'icon' => 'blackboard',
                'description' => 'الوصول إلى نظام إدارة التعلم',
            ],
            [
                'title' => 'التقويم الأكاديمي',
                'url' => Setting::where('key', 'calendar_url')->value('value') ?? '#',
                'icon' => 'calendar',
                'description' => 'عرض المواعيد والأحداث الأكاديمية',
            ],
        ];

        return view('student.links.index', compact('links'));
    }
}
