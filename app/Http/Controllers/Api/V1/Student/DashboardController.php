<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Setting;
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
     * List enrolled subjects
     */
    public function mySubjects()
    {
        $student = auth()->user();

        $subjects = Subject::whereHas('enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->with(['term.program', 'teacher:id,name,email,profile_photo'])
            ->withCount('sessions')
            ->get();

        // Add progress per subject
        $subjects->each(function ($subject) use ($student) {
            $totalSessions = $subject->sessions()->count();
            $attendedSessions = Attendance::where('student_id', $student->id)
                ->whereHas('session', fn($q) => $q->where('subject_id', $subject->id))
                ->where('attended', true)
                ->count();

            $subject->progress = [
                'total_sessions' => $totalSessions,
                'attended_sessions' => $attendedSessions,
                'percentage' => $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 1) : 0,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $subjects,
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
     * Upcoming Zoom sessions
     */
    public function upcomingSessions()
    {
        $student = auth()->user();

        $upcomingSessions = Session::whereHas('subject.enrollments', function ($q) use ($student) {
            $q->where('student_id', $student->id)->where('status', 'active');
        })
            ->where('type', 'live_zoom')
            ->where('scheduled_at', '>=', now())
            ->with(['subject:id,name_ar,name_en', 'unit:id,title'])
            ->orderBy('scheduled_at', 'asc')
            ->paginate(10);

        $liveSessions = Session::whereHas('subject.enrollments', function ($q) use ($student) {
            $q->where('student_id', $student->id)->where('status', 'active');
        })
            ->where('type', 'live_zoom')
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->with(['subject:id,name_ar,name_en', 'unit:id,title'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'upcoming_sessions' => $upcomingSessions,
                'live_sessions' => $liveSessions,
            ],
        ]);
    }

    /**
     * GET /api/v1/student/attendance
     * Attendance records
     */
    public function myAttendance(Request $request)
    {
        $student = auth()->user();
        $subjectId = $request->query('subject_id');

        $query = Attendance::where('student_id', $student->id)
            ->with(['session.subject:id,name_ar,name_en', 'session.unit:id,title']);

        if ($subjectId) {
            $query->whereHas('session', fn($q) => $q->where('subject_id', $subjectId));
        }

        $attendances = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $totalSessions = Attendance::where('student_id', $student->id)->count();
        $attendedSessions = Attendance::where('student_id', $student->id)->where('attended', true)->count();
        $attendanceRate = $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 1) : 0;
        $totalMinutes = Attendance::where('student_id', $student->id)->sum('duration_minutes') ?? 0;

        // Enrolled subjects for filter
        $enrolledSubjects = Subject::whereHas('enrollments', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->select('id', 'name_ar', 'name_en')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'attendances' => $attendances,
                'statistics' => [
                    'total_sessions' => $totalSessions,
                    'attended_sessions' => $attendedSessions,
                    'attendance_rate' => $attendanceRate,
                    'total_minutes' => $totalMinutes,
                ],
                'enrolled_subjects' => $enrolledSubjects,
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
