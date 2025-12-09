<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        $stats = [
            'enrolled_courses' => Enrollment::where('student_id', $student->id)
                ->where('status', 'active')
                ->count(),
            'completed_lessons' => 0, // Will be implemented with lesson progress system
            'pending_assignments' => 0, // Will be implemented with assignment system
            'gpa' => 0, // Will be implemented with grades system
        ];

        $enrolledCourses = Enrollment::where('student_id', $student->id)
            ->where('status', 'active')
            ->with(['course.teacher'])
            ->latest()
            ->take(4)
            ->get();

        $upcomingAssignments = []; // Will be implemented with assignment system

        return view('student.dashboard', compact('stats', 'enrolledCourses', 'upcomingAssignments'));
    }
}
