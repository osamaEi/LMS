<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\Enrollment;
use App\Models\Session;

class HomeworkController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        // Get all subject IDs the student is enrolled in
        $subjectIds = Enrollment::where('student_id', $student->id)->pluck('subject_id');

        // Get all sessions for those subjects that have homework
        $homeworks = Homework::whereHas('session', function ($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })
            ->with(['session.subject'])
            ->orderByDesc('created_at')
            ->get();

        return view('student.homework.index', compact('homeworks'));
    }
}
