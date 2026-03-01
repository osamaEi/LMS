<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Setting;
use App\Models\Term;
use App\Models\Ticket;
use App\Models\TeacherRating;
use App\Models\SatisfactionSurvey;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * GET /api/v1/student/dashboard
     * Main dashboard with all student data
     */
    public function index()
    {
        $student = auth()->user();

        if (!$student->program_id) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم تعيين برنامج دراسي',
                'redirect' => 'my-program',
            ], 200);
        }

        $subjects = Subject::whereHas('enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->with(['term.program', 'teacher:id,name,email,profile_photo'])
            ->withCount('sessions')
            ->get();

        $upcomingSessions = Session::whereHas('subject.enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->where('scheduled_at', '>', now())
            ->with(['subject:id,name_ar,name_en'])
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        $recentSessions = Session::whereHas('subject.enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->with(['subject:id,name_ar,name_en'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $liveSessions = Session::whereHas('subject.enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->where('type', 'live_zoom')
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->with(['subject:id,name_ar,name_en'])
            ->get();

        $stats = [
            'subjects_count' => $subjects->count(),
            'total_sessions' => Session::whereHas('subject.enrollments', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })->count(),
            'completed_sessions' => Session::whereHas('subject.enrollments', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })->whereNotNull('ended_at')->count(),
            'live_sessions' => $liveSessions->count(),
        ];

        $subjectIds = $subjects->pluck('id');
        $totalAttendances = Attendance::where('student_id', $student->id)
            ->whereHas('session', fn($q) => $q->whereIn('subject_id', $subjectIds))
            ->count();
        $presentAttendances = Attendance::where('student_id', $student->id)
            ->whereHas('session', fn($q) => $q->whereIn('subject_id', $subjectIds))
            ->where('attended', true)
            ->count();
        $overallAttendance = $totalAttendances > 0
            ? round(($presentAttendances / $totalAttendances) * 100, 1)
            : 0;

        $openTicketsCount = Ticket::where('user_id', $student->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'subjects' => $subjects,
                'upcoming_sessions' => $upcomingSessions,
                'recent_sessions' => $recentSessions,
                'live_sessions' => $liveSessions,
                'statistics' => $stats,
                'overall_attendance' => $overallAttendance,
                'open_tickets_count' => $openTicketsCount,
            ],
        ]);
    }

    /**
     * GET /api/v1/student/my-subjects
     * All subjects in the student's program, grouped by term, with enrollment status
     */
    public function mySubjects()
    {
        $student = auth()->user();

        if (!$student->program_id) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم تعيين برنامج دراسي',
            ], 200);
        }

        // Load all terms of the student's program with subjects
        $terms = Term::where('program_id', $student->program_id)
            ->orderBy('term_number', 'asc')
            ->with([
                'subjects' => function ($q) {
                    $q->with(['teacher:id,name,email,profile_photo'])
                      ->withCount('sessions')
                      ->orderBy('name_ar');
                },
            ])
            ->get();

        // Student's enrollments keyed by subject_id
        $enrollments = Enrollment::where('student_id', $student->id)
            ->get()
            ->keyBy('subject_id');

        // Attendance counts keyed by subject_id
        $attendanceCounts = Attendance::where('student_id', $student->id)
            ->where('attended', true)
            ->select('session_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('session_id')
            ->get();

        // Build subject_id → attended sessions count map
        $subjectAttended = [];
        if ($attendanceCounts->isNotEmpty()) {
            $sessionIds = $attendanceCounts->pluck('session_id');
            $sessionSubjects = Session::whereIn('id', $sessionIds)
                ->select('id', 'subject_id')
                ->get()
                ->keyBy('id');

            foreach ($attendanceCounts as $row) {
                $subjectId = $sessionSubjects[$row->session_id]->subject_id ?? null;
                if ($subjectId) {
                    $subjectAttended[$subjectId] = ($subjectAttended[$subjectId] ?? 0) + 1;
                }
            }
        }

        $termsData = $terms->map(function ($term) use ($enrollments, $subjectAttended, $student) {
            $subjects = $term->subjects->map(function ($subject) use ($enrollments, $subjectAttended) {
                $enrollment = $enrollments->get($subject->id);
                $totalSessions = $subject->sessions_count;
                $attended      = $subjectAttended[$subject->id] ?? 0;

                return [
                    'id'           => $subject->id,
                    'name_ar'      => $subject->name_ar,
                    'name_en'      => $subject->name_en,
                    'code'         => $subject->code,
                    'credits'      => $subject->credits,
                    'color'        => $subject->color,
                    'banner_photo' => $subject->banner_photo,
                    'status'       => $subject->status,
                    'teacher'      => $subject->teacher ? [
                        'id'            => $subject->teacher->id,
                        'name'          => $subject->teacher->name,
                        'email'         => $subject->teacher->email,
                        'profile_photo' => $subject->teacher->profile_photo,
                    ] : null,
                    'sessions_count' => $totalSessions,
                    'enrollment'   => $enrollment ? [
                        'status'      => $enrollment->status,
                        'enrolled_at' => $enrollment->enrolled_at,
                        'final_grade' => $enrollment->final_grade,
                        'grade_letter'=> $enrollment->grade_letter,
                    ] : null,
                    'is_enrolled'  => (bool) $enrollment,
                    'progress'     => [
                        'total_sessions'    => $totalSessions,
                        'attended_sessions' => $attended,
                        'percentage'        => $totalSessions > 0
                            ? round(($attended / $totalSessions) * 100, 1)
                            : 0,
                    ],
                ];
            });

            $enrolledCount = $subjects->where('is_enrolled', true)->count();

            return [
                'id'             => $term->id,
                'term_number'    => $term->term_number,
                'name'           => $term->name ?? ('الفصل ' . $term->term_number),
                'subjects_count' => $subjects->count(),
                'enrolled_count' => $enrolledCount,
                'is_current'     => $term->term_number === ($student->current_term_number ?? 1),
                'subjects'       => $subjects->values(),
            ];
        });

        // Summary stats
        $totalSubjects    = $termsData->sum('subjects_count');
        $enrolledSubjects = $termsData->sum('enrolled_count');

        // Current term full object
        $currentTermNumber = $student->current_term_number ?? 1;
        $currentTermData   = $termsData->firstWhere('term_number', $currentTermNumber)
                          ?? $termsData->first();

        return response()->json([
            'success' => true,
            'data'    => [
                'program_id'        => $student->program_id,
                'program_status'    => $student->program_status,
                'current_term_number' => $currentTermNumber,
                'current_term'      => $currentTermData,   // full term object with subjects
                'total_terms'       => $terms->count(),
                'total_subjects'    => $totalSubjects,
                'enrolled_subjects' => $enrolledSubjects,
                'terms'             => $termsData,
            ],
        ]);
    }

    /**
     * GET /api/v1/student/statistics
     * Student statistics overview
     */
    public function statistics()
    {
        $student = auth()->user();

        $subjectIds = Subject::whereHas('enrollments', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->pluck('id');

        $totalSessions = Session::whereIn('subject_id', $subjectIds)->count();
        $completedSessions = Session::whereIn('subject_id', $subjectIds)->whereNotNull('ended_at')->count();

        $totalAttendances = Attendance::where('student_id', $student->id)->count();
        $presentAttendances = Attendance::where('student_id', $student->id)->where('attended', true)->count();
        $attendanceRate = $totalAttendances > 0
            ? round(($presentAttendances / $totalAttendances) * 100, 1)
            : 0;

        $totalMinutes = Attendance::where('student_id', $student->id)->sum('duration_minutes') ?? 0;

        // Monthly attendance chart (last 6 months)
        $monthlyAttendance = Attendance::where('student_id', $student->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN attended = 1 THEN 1 ELSE 0 END) as present'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $item->rate = $item->total > 0 ? round(($item->present / $item->total) * 100, 1) : 0;
                return $item;
            });

        return response()->json([
            'success' => true,
            'data' => [
                'subjects_count' => $subjectIds->count(),
                'total_sessions' => $totalSessions,
                'completed_sessions' => $completedSessions,
                'attendance_rate' => $attendanceRate,
                'total_attended' => $presentAttendances,
                'total_absent' => $totalAttendances - $presentAttendances,
                'total_minutes' => $totalMinutes,
                'monthly_attendance' => $monthlyAttendance,
            ],
        ]);
    }

    /**
     * GET /api/v1/student/upcoming-sessions
     * Upcoming & live sessions for the student's program (enrollment not required)
     */
    public function upcomingSessions()
    {
        $student = auth()->user();

        if (!$student->program_id) {
            return response()->json([
                'success' => false,
                'message' => 'لم يتم تعيين برنامج دراسي',
            ], 200);
        }

        // Subject IDs in the student's program
        $programSubjectIds = Subject::whereHas('term', fn($q) => $q->where('program_id', $student->program_id))
            ->pluck('id');

        // Also include subjects the student is explicitly enrolled in (other programs / standalone)
        $enrolledSubjectIds = Enrollment::where('student_id', $student->id)
            ->where('status', 'active')
            ->pluck('subject_id');

        $subjectIds = $programSubjectIds->merge($enrolledSubjectIds)->unique();

        $sessionWith = [
            'subject:id,name_ar,name_en,code,color',
            'subject.term:id,term_number,name',
            'unit:id,title',
        ];

        // Live sessions (currently running)
        $liveSessions = Session::whereIn('subject_id', $subjectIds)
            ->where('type', 'live_zoom')
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->with($sessionWith)
            ->get()
            ->map(fn($s) => $this->formatSession($s, $student->id, true));

        // Upcoming sessions
        $upcomingSessions = Session::whereIn('subject_id', $subjectIds)
            ->where('scheduled_at', '>=', now())
            ->whereNull('started_at')
            ->with($sessionWith)
            ->orderBy('scheduled_at', 'asc')
            ->paginate(15)
            ->through(fn($s) => $this->formatSession($s, $student->id, false));

        // Past sessions (last 10)
        $pastSessions = Session::whereIn('subject_id', $subjectIds)
            ->whereNotNull('ended_at')
            ->with($sessionWith)
            ->orderBy('ended_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn($s) => $this->formatSession($s, $student->id, false));

        return response()->json([
            'success' => true,
            'data' => [
                'live_sessions'     => $liveSessions,
                'upcoming_sessions' => $upcomingSessions,
                'past_sessions'     => $pastSessions,
            ],
        ]);
    }

    /**
     * Format a session with join links and attendance info
     */
    private function formatSession(Session $session, int $studentId, bool $isLive): array
    {
        $attendance = Attendance::where('student_id', $studentId)
            ->where('session_id', $session->id)
            ->first();

        $joinUrl = $session->zoom_join_url;

        return [
            'id'             => $session->id,
            'title'          => $session->title,
            'type'           => $session->type,
            'scheduled_at'   => $session->scheduled_at,
            'started_at'     => $session->started_at,
            'ended_at'       => $session->ended_at,
            'duration_minutes' => $session->duration_minutes,
            'is_live'        => $isLive,
            'subject'        => $session->subject ? [
                'id'      => $session->subject->id,
                'name_ar' => $session->subject->name_ar,
                'name_en' => $session->subject->name_en,
                'code'    => $session->subject->code,
                'color'   => $session->subject->color,
                'term'    => $session->subject->term ? [
                    'id'          => $session->subject->term->id,
                    'term_number' => $session->subject->term->term_number,
                    'name'        => $session->subject->term->name,
                ] : null,
            ] : null,
            'unit'           => $session->unit ? [
                'id'    => $session->unit->id,
                'title' => $session->unit->title,
            ] : null,
            'links'          => [
                'join_zoom'   => $joinUrl,
                'zoom_meeting_id' => $session->zoom_meeting_id,
                'recording'   => $session->recording_url ?? null,
                'join_api'    => url("/api/v1/student/sessions/{$session->id}/join-zoom"),
                'leave_api'   => url("/api/v1/student/sessions/{$session->id}/leave-zoom"),
            ],
            'attendance'     => $attendance ? [
                'attended'         => $attendance->attended,
                'joined_at'        => $attendance->joined_at,
                'left_at'          => $attendance->left_at,
                'duration_minutes' => $attendance->duration_minutes,
            ] : null,
        ];
    }

    /**
     * GET /api/v1/student/attendance
     * Attendance records (filter by ?subject_id=)
     */
    public function myAttendance(Request $request)
    {
        $student   = auth()->user();
        $subjectId = $request->query('subject_id');

        $query = Attendance::where('student_id', $student->id)
            ->with([
                'session:id,subject_id,unit_id,title,type,scheduled_at,started_at,ended_at,zoom_join_url,recording_url',
                'session.subject:id,name_ar,name_en,code,color',
                'session.unit:id,title',
            ]);

        if ($subjectId) {
            $query->whereHas('session', fn($q) => $q->where('subject_id', $subjectId));
        }

        $attendances = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $total    = Attendance::where('student_id', $student->id)->count();
        $attended = Attendance::where('student_id', $student->id)->where('attended', true)->count();
        $rate     = $total > 0 ? round(($attended / $total) * 100, 1) : 0;
        $minutes  = (int) (Attendance::where('student_id', $student->id)->sum('duration_minutes') ?? 0);

        // Subjects available for filtering: program subjects + enrolled subjects
        $programSubjects = $student->program_id
            ? Subject::whereHas('term', fn($q) => $q->where('program_id', $student->program_id))
                ->select('id', 'name_ar', 'name_en', 'code', 'color')
                ->orderBy('name_ar')
                ->get()
            : collect();

        $enrolledSubjects = Subject::whereHas('enrollments', fn($q) => $q->where('student_id', $student->id))
            ->select('id', 'name_ar', 'name_en', 'code', 'color')
            ->get();

        $availableSubjects = $programSubjects->merge($enrolledSubjects)->unique('id')->values();

        return response()->json([
            'success' => true,
            'data'    => [
                'attendances'       => $attendances,
                'statistics'        => [
                    'total_sessions'    => $total,
                    'attended_sessions' => $attended,
                    'absent_sessions'   => $total - $attended,
                    'attendance_rate'   => $rate,
                    'total_minutes'     => $minutes,
                ],
                'available_subjects' => $availableSubjects,
            ],
        ]);
    }

    /**
     * GET /api/v1/student/links
     * Useful links
     */
    public function usefulLinks()
    {
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
                'title' => 'الجدول الدراسي',
                'url' => Setting::where('key', 'schedule_url')->value('value') ?? '#',
                'icon' => 'schedule',
                'description' => 'عرض الجدول الدراسي الخاص بك',
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

        return response()->json([
            'success' => true,
            'data' => $links,
        ]);
    }

    /**
     * POST /api/v1/student/sessions/{sessionId}/join-zoom
     * Join a Zoom session
     */
    public function joinZoom(Request $request, $sessionId)
    {
        $student = auth()->user();

        $session = Session::with('subject.term')->findOrFail($sessionId);

        // Check access
        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('subject_id', $session->subject_id)
            ->where('status', 'active')
            ->exists();

        $isInProgram = $student->program_id && $session->subject
            && $session->subject->term
            && $session->subject->term->program_id === $student->program_id;

        if (!$isEnrolled && !$isInProgram) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية الانضمام لهذه الجلسة',
            ], 403);
        }

        if (empty($session->zoom_meeting_id)) {
            return response()->json([
                'success' => false,
                'message' => 'هذه الجلسة لا تحتوي على اجتماع Zoom',
            ], 400);
        }

        // Create or update attendance
        $attendance = Attendance::firstOrCreate(
            [
                'student_id' => $student->id,
                'session_id' => $session->id,
            ],
            [
                'attended' => true,
                'joined_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        if (!$attendance->joined_at) {
            $attendance->recordJoin($request->ip(), $request->userAgent());
            $attendance->markAsAttended();
        }

        // Generate Zoom SDK signature
        $zoomService = new ZoomService();
        $signature = $zoomService->generateSignature($session->zoom_meeting_id, 0);

        return response()->json([
            'success' => true,
            'data' => [
                'session' => $session,
                'attendance_id' => $attendance->id,
                'zoom_meeting_id' => $session->zoom_meeting_id,
                'zoom_signature' => $signature,
                'zoom_join_url' => $session->zoom_join_url,
            ],
        ]);
    }

    /**
     * POST /api/v1/student/sessions/{sessionId}/leave-zoom
     * Leave a Zoom session
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

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل مغادرتك بنجاح',
        ]);
    }
}
