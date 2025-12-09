<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Optimize with single queries
        $teachersCount = User::where('role', 'teacher')->count();
        $studentsCount = User::where('role', 'student')->count();
        $coursesCount = Course::count();

        $stats = [
            'teachers_count' => $teachersCount,
            'students_count' => $studentsCount,
            'courses_count' => $coursesCount,
            'active_courses' => Course::where('status', 'active')->count(),
            'total_users' => $teachersCount + $studentsCount + 1, // +1 for admin
            'today_enrollments' => 0,
            'avg_students_per_course' => $coursesCount > 0
                ? round($studentsCount / $coursesCount, 1)
                : 0,
        ];

        $recentTeachers = User::select('id', 'name', 'email', 'created_at')
            ->where('role', 'teacher')
            ->latest()
            ->limit(5)
            ->get();

        $recentCourses = Course::select('courses.id', 'courses.title', 'courses.description', 'courses.teacher_id', 'courses.status', 'courses.created_at')
            ->with(['teacher:id,name'])
            ->latest()
            ->limit(4)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentTeachers', 'recentCourses'));
    }
}
