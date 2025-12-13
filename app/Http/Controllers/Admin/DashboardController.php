<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Term;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Optimize with single queries
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

        return view('admin.dashboard', compact(
            'stats',
            'recentTeachers',
            'recentCourses',
            'studentsPerMonth',
            'teachersPerMonth',
            'coursesStatus',
            'termsStatus'
        ));
    }
}
