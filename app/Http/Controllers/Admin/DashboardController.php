<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Term;
use App\Models\Enrollment;
use App\Models\SatisfactionSurvey;
use App\Models\SurveyResponse;
use App\Models\Ticket;
use App\Models\TeacherRating;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Basic counts
        $teachersCount = User::where('role', 'teacher')->count();
        $studentsCount = User::where('role', 'student')->count();
        $coursesCount = Course::count();
        $termsCount = Term::count();

        $stats = [
            'teachers_count' => $teachersCount,
            'students_count' => $studentsCount,
            'courses_count' => $coursesCount,
            'terms_count' => $termsCount,
            'active_courses' => Course::where('status', 'active')->count(),
            'active_terms' => Term::where('status', 'active')->count(),
            'total_users' => $teachersCount + $studentsCount + 1,
            'today_enrollments' => Enrollment::whereDate('created_at', today())->count(),
            'avg_students_per_course' => $coursesCount > 0
                ? round($studentsCount / $coursesCount, 1)
                : 0,
        ];

        // NELC Compliance Stats
        $nelcStats = $this->getNelcStats();

        // Chart data: Students per month (last 6 months)
        $studentsPerMonth = User::where('role', 'student')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        // Chart data: Teachers per month (last 6 months)
        $teachersPerMonth = User::where('role', 'teacher')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month');

        // Chart data: Courses status distribution
        $coursesStatus = Course::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Chart data: Terms status distribution
        $termsStatus = Term::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Satisfaction trend (last 6 months)
        $satisfactionTrend = SurveyResponse::whereNotNull('rating')
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('AVG(rating) as avg_rating')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('avg_rating', 'month')
            ->map(fn($val) => round($val, 2));

        $recentTeachers = User::select('id', 'name', 'email', 'created_at')
            ->where('role', 'teacher')
            ->latest()
            ->limit(5)
            ->get();

        $recentCourses = Course::select('courses.id', 'courses.title', 'courses.description', 'courses.teacher_id', 'courses.status', 'courses.created_at')
            ->with(['teacher:id,name'])
            ->withCount('students')
            ->latest()
            ->limit(4)
            ->get();

        // Recent tickets
        $recentTickets = Ticket::with(['user:id,name', 'assignedTo:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        // Top rated teachers
        $topTeachers = User::where('role', 'teacher')
            ->get()
            ->map(function($teacher) {
                $teacher->avg_rating = $teacher->getAverageRating();
                $teacher->ratings_count = $teacher->ratingsReceived()
                    ->where('is_approved', true)->count();
                return $teacher;
            })
            ->filter(fn($t) => $t->ratings_count > 0)
            ->sortByDesc('avg_rating')
            ->take(5);

        // Active surveys
        $activeSurveys = SatisfactionSurvey::where('status', 'active')
            ->withCount('responses')
            ->latest()
            ->limit(5)
            ->get();

        // Pending ratings
        $pendingRatingsCount = TeacherRating::where('is_approved', false)->count();

        return view('admin.dashboard', compact(
            'stats',
            'nelcStats',
            'recentTeachers',
            'recentCourses',
            'studentsPerMonth',
            'teachersPerMonth',
            'coursesStatus',
            'termsStatus',
            'satisfactionTrend',
            'recentTickets',
            'topTeachers',
            'activeSurveys',
            'pendingRatingsCount'
        ));
    }

    private function getNelcStats(): array
    {
        // Satisfaction rate (NELC 1.2.11)
        $avgSatisfaction = SurveyResponse::whereNotNull('rating')->avg('rating');
        $satisfactionRate = $avgSatisfaction ? round(($avgSatisfaction / 5) * 100, 1) : 0;

        // Average response time for tickets (NELC 1.3.3)
        $ticketsWithResponse = Ticket::whereNotNull('first_response_at')->get();
        $avgResponseMinutes = 0;
        if ($ticketsWithResponse->count() > 0) {
            $totalMinutes = 0;
            foreach ($ticketsWithResponse as $ticket) {
                $totalMinutes += $ticket->created_at->diffInMinutes($ticket->first_response_at);
            }
            $avgResponseMinutes = round($totalMinutes / $ticketsWithResponse->count());
        }

        // Teacher rating average (NELC 2.4.9)
        $avgTeacherRating = TeacherRating::where('is_approved', true)->avg('overall_rating');

        // Attendance rate (NELC 1.2.5)
        $totalAttendances = Attendance::count();
        $presentAttendances = Attendance::where('attended', true)->count();
        $attendanceRate = $totalAttendances > 0
            ? round(($presentAttendances / $totalAttendances) * 100, 1)
            : 0;

        // Open tickets
        $openTickets = Ticket::whereIn('status', ['open', 'in_progress'])->count();

        // Active surveys
        $activeSurveysCount = SatisfactionSurvey::where('status', 'active')->count();

        return [
            'satisfaction_rate' => $satisfactionRate,
            'avg_response_time' => $avgResponseMinutes,
            'avg_teacher_rating' => round($avgTeacherRating ?? 0, 2),
            'attendance_rate' => $attendanceRate,
            'open_tickets' => $openTickets,
            'active_surveys' => $activeSurveysCount,
        ];
    }
}
