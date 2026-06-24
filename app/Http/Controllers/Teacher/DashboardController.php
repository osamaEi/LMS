<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Session;
use App\Models\User;
use App\Models\Attendance;
use App\Models\TeacherRating;
use App\Models\Ticket;
use App\Models\SatisfactionSurvey;
use App\Models\SurveyResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Build the weekly-schedule calendar payload for a teacher — same data and
     * shape used by /teacher/schedule, so the dashboard renders the same calendar.
     */
    private function buildTeacherCalendarSessions($teacher): \Illuminate\Support\Collection
    {
        // All sessions assigned to THIS teacher only (session.teacher_id) — both
        // subject-based (diploma) and program-based (course/training/english).
        return Session::where('teacher_id', $teacher->id)
            ->with(['teacher', 'subject.program', 'subject.term.program', 'program', 'programClass', 'subject.programClass', 'subject.term.programClass'])
            ->orderBy('scheduled_at')
            ->get()
            ->map(fn($s) => [
                'id'               => $s->id,
                'title'            => $s->title_ar ?: ($s->subject->name_ar ?? $s->program->name_ar ?? 'جلسة'),
                'subject_name'     => $s->subject->name_ar ?? '',
                'program_name'     => $s->program->name_ar ?? $s->subject?->program?->name_ar ?? '',
                'scheduled_at'     => $s->scheduled_at ? \Carbon\Carbon::parse($s->scheduled_at)->toIso8601String() : null,
                'duration_minutes' => $s->duration_minutes ?? 60,
                'type'             => $s->type ?? '',
                'status'           => (string) ($s->status ?? ''),
                'session_number'   => $s->session_number,
                'class_name'       => $s->programClass->name ?? $s->subject?->programClass?->name ?? $s->subject?->term?->programClass?->name ?? '',
                'zoom_join_url'    => $s->zoom_join_url,
                'zoom_start_url'   => $s->zoom_start_url,
                'subject_id'       => $s->subject_id,
                'program_id'       => $s->program_id,
            ])
            ->filter(fn($s) => $s['scheduled_at'])
            ->values();
    }

    /**
     * Quizzes the teacher created (created_by), shaped for the weekly calendar so
     * they appear alongside sessions — same idea as the student calendar.
     */
    private function buildTeacherQuizzesCalendar($teacher): \Illuminate\Support\Collection
    {
        return \App\Models\Quiz::where('created_by', $teacher->id)
            ->whereNotNull('starts_at')
            ->with(['subject:id,name_ar'])
            ->withCount(['questions', 'attempts'])
            ->get()
            ->map(fn($q) => [
                'id'           => $q->id,
                'subject_id'   => $q->subject_id,
                'title'        => $q->title_ar,
                'subject_name' => $q->subject->name_ar ?? '',
                'type'         => $q->type,
                'type_label'   => $q->type_label,
                'starts_at'    => \Carbon\Carbon::parse($q->starts_at)->toIso8601String(),
                'ends_at'      => $q->ends_at ? \Carbon\Carbon::parse($q->ends_at)->toIso8601String() : null,
                'total_marks'  => $q->total_marks,
                'duration'     => $q->duration_minutes,
                'questions'    => $q->questions_count,
                'attempts'     => $q->attempts_count,
                'is_active'    => (bool) $q->is_active,
                'url'          => $q->subject_id ? route('teacher.quizzes.show', [$q->subject_id, $q->id]) : null,
            ])
            ->filter(fn($q) => $q['starts_at'])
            ->values();
    }

    public function index()
    {
        $teacher = auth()->user();

        // Get teacher's subjects with student count
        $subjects = Subject::assignedToTeacher($teacher->id)
            ->with(['term.program'])
            ->withCount('enrollments')
            ->get();

        // Program IDs for course/training/english sessions (no subject_id)
        // Single source of truth: a session belongs to this teacher when its own
        // teacher_id is his (the teacher chosen when the session was scheduled).
        $teacherSessionFilter = fn ($q) => $q->where('teacher_id', $teacher->id);

        // Get upcoming sessions
        $upcomingSessions = Session::where($teacherSessionFilter)
            ->where('scheduled_at', '>', now())
            ->with(['subject.program', 'subject.term', 'program'])
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        // Get live/current sessions (started but not ended)
        $liveSessions = Session::where($teacherSessionFilter)
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->with(['subject.program', 'subject.term', 'program'])
            ->orderBy('started_at', 'desc')
            ->get();

        // Get past sessions (ended or scheduled_at in past)
        $pastSessions = Session::where($teacherSessionFilter)
            ->where(function($q) {
                $q->whereNotNull('ended_at')
                  ->orWhere('scheduled_at', '<', now()->subHour());
            })
            ->with(['subject.program', 'subject.term', 'program'])
            ->orderBy('scheduled_at', 'desc')
            ->take(20)
            ->get();

        // Get recent sessions
        $recentSessions = Session::whereHas('subject', function($query) use ($teacher) {
                $query->assignedToTeacher($teacher->id);
            })
            ->with('subject')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get recent past sessions with attendance counts for dashboard attendance section
        $recentSessionsWithAttendance = Session::whereHas('subject', function($query) use ($teacher) {
                $query->assignedToTeacher($teacher->id);
            })
            ->where('scheduled_at', '<', now())
            ->with(['subject', 'attendances' => function($q) {
                $q->where('attended', true)->with('student:id,name');
            }])
            ->orderBy('scheduled_at', 'desc')
            ->take(8)
            ->get()
            ->map(function($session) {
                $session->enrolled_count = $session->subject
                    ? $session->subject->enrollments()->count()
                    : 0;
                return $session;
            });

        // Statistics
        $stats = [
            'subjects_count' => $subjects->count(),
            'total_students' => $subjects->sum('enrollments_count'),
            'total_sessions' => Session::whereHas('subject', function($query) use ($teacher) {
                $query->assignedToTeacher($teacher->id);
            })->count(),
            'live_sessions' => Session::whereHas('subject', function($query) use ($teacher) {
                $query->assignedToTeacher($teacher->id);
            })->where('type', 'live_zoom')->whereNotNull('started_at')->whereNull('ended_at')->count(),
        ];
        
        // Get the preferred dashboard view from settings
        // Default to 'teacher.dashboard' if not set
        $dashboardView = \App\Models\Setting::get('teacher_dashboard_view', 'teacher.dashboard');

        // NELC: Teacher ratings
        $teacherRating = [
            'overall' => $teacher->getAverageRating(),
            'breakdown' => $teacher->getRatingsBreakdown(),
            'total_ratings' => $teacher->ratingsReceived()->where('is_approved', true)->count(),
        ];

        // Recent feedback from students
        $recentFeedback = TeacherRating::where('teacher_id', $teacher->id)
            ->where('is_approved', true)
            ->whereNotNull('comment')
            ->with(['student:id,name', 'subject:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        // Rating distribution
        $ratingDistribution = TeacherRating::where('teacher_id', $teacher->id)
            ->where('is_approved', true)
            ->select(DB::raw('FLOOR(overall_rating) as rating'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('FLOOR(overall_rating)'))
            ->pluck('count', 'rating');

        // Weekly attendance for teacher's subjects
        $subjectIds = $subjects->pluck('id');
        $weeklyAttendance = Attendance::whereHas('session', function($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })
            ->where('created_at', '>=', now()->subWeeks(4))
            ->select(
                DB::raw('YEARWEEK(created_at) as week'),
                DB::raw('SUM(CASE WHEN attended = 1 THEN 1 ELSE 0 END) as present'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('week')
            ->orderBy('week')
            ->get()
            ->map(function($item) {
                $item->rate = $item->total > 0 ? round(($item->present / $item->total) * 100, 1) : 0;
                return $item;
            });

        // Teacher's tickets
        $myTickets = Ticket::where('user_id', $teacher->id)
            ->latest()
            ->limit(5)
            ->get();

        $openTicketsCount = Ticket::where('user_id', $teacher->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        // Pending surveys
        $pendingSurveys = SatisfactionSurvey::where('status', 'active')
            ->where('type', 'teacher')
            ->where(function($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->whereDoesntHave('responses', function($q) use ($teacher) {
                $q->where('user_id', $teacher->id);
            })
            ->count();

        // Use the dynamic view setting, verify it exists
        if (!view()->exists($dashboardView)) {
            $dashboardView = 'teacher.dashboard';
        }

        // Weekly-schedule calendar (same as /teacher/schedule) for the dashboard
        $calSessions = $this->buildTeacherCalendarSessions($teacher);
        $calQuizzes  = $this->buildTeacherQuizzesCalendar($teacher);

        // Teacher's single personal Zoom link (applies to all their sessions)
        $myZoomLink = $teacher->zoom_join_url;

        return view($dashboardView, compact(
            'subjects',
            'calSessions',
            'calQuizzes',
            'myZoomLink',
            'upcomingSessions',
            'liveSessions',
            'pastSessions',
            'recentSessions',
            'recentSessionsWithAttendance',
            'stats',
            'teacherRating',
            'recentFeedback',
            'ratingDistribution',
            'weeklyAttendance',
            'myTickets',
            'openTicketsCount',
            'pendingSurveys'
        ));
    }

    public function showSubject($id)
    {
        $teacher = auth()->user();

        $subject = Subject::assignedToTeacher($teacher->id)
            ->with(['term.program', 'enrollments.student'])
            ->findOrFail($id);

        $sessions = Session::where('subject_id', $id)
            ->orderBy('session_number', 'asc')
            ->get();

        $students = User::whereHas('enrollments', function($query) use ($id) {
            $query->where('subject_id', $id);
        })->get();

        return view('teacher.subject-detail', compact('subject', 'sessions', 'students'));
    }
}
