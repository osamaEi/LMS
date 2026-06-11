<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Session;
use App\Models\User;

class StudentsController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $teacher */
        $teacher = auth()->user();

        // Students who have attendance records in sessions taught by this teacher
        $sessionIds = Session::where('teacher_id', $teacher->id)->pluck('id');

        $students = User::whereIn('id',
                Attendance::whereIn('session_id', $sessionIds)->distinct()->pluck('student_id')
            )
            ->where('role', 'student')
            ->with(['program'])
            ->orderBy('name')
            ->get();

        $totalStudents = $students->count();

        return view('teacher.students.index', compact('students', 'totalStudents'));
    }
}
