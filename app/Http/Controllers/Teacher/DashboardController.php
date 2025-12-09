<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();

        $stats = [
            'my_courses_count' => Course::where('teacher_id', $teacher->id)->count(),
            'total_students' => Course::where('teacher_id', $teacher->id)
                ->withCount('students')
                ->get()
                ->sum('students_count'),
            'today_classes' => 0, // Will be implemented with class schedule system
        ];

        $myCourses = Course::where('teacher_id', $teacher->id)
            ->withCount('students')
            ->latest()
            ->take(4)
            ->get();

        $todayClasses = []; // Will be implemented with class schedule system

        return view('teacher.dashboard', compact('stats', 'myCourses', 'todayClasses'));
    }
}
