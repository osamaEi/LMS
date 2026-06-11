<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ProgramClass;
use App\Models\User;
use App\Models\Subject;
class StudentsController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $teacher */
        $teacher = auth()->user();

        // Classes where this teacher is the assigned teacher
        $classes = ProgramClass::where('teacher_id', $teacher->id)
            ->with(['program', 'students' => function ($q) {
                $q->orderBy('name');
            }])
            ->orderBy('name')
            ->get();

        // Total unique students across all classes
        $totalStudents = $classes->flatMap(fn($c) => $c->students)->unique('id')->count();

        return view('teacher.students.index', compact('classes', 'totalStudents'));
    }
}
