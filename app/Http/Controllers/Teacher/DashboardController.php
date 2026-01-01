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
    public function index()
    {
        $teacher = auth()->user();

        // Get teacher's subjects with student count
        $subjects = Subject::where('teacher_id', $teacher->id)
            ->with(['term.program'])
            ->withCount('enrollments')
            ->get();

        // Get upcoming sessions
        $upcomingSessions = Session::whereHas('subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('scheduled_at', '>', now())
            ->with('subject')
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        // Get recent sessions
        $recentSessions = Session::whereHas('subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with('subject')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Statistics
        $stats = [
            'subjects_count' => $subjects->count(),
            'total_students' => $subjects->sum('enrollments_count'),
            'total_sessions' => Session::whereHas('subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->count(),
            'live_sessions' => Session::whereHas('subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->where('type', 'live_zoom')->whereNotNull('started_at')->whereNull('ended_at')->count(),
        ];

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

        return view('teacher.dashboard', compact(
            'subjects',
            'upcomingSessions',
            'recentSessions',
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

        $subject = Subject::where('teacher_id', $teacher->id)
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
