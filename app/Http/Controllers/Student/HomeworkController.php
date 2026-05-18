<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\Subject;

class HomeworkController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        // All subject IDs accessible to this student (program or enrollment)
        $subjectIds = Subject::where(function ($q) use ($student) {
                $q->where('program_id', $student->program_id)
                  ->orWhereHas('term', fn($tq) => $tq->where('program_id', $student->program_id))
                  ->orWhereHas('terms', fn($tq) => $tq->where('program_id', $student->program_id))
                  ->orWhereHas('enrollments', fn($eq) => $eq->where('student_id', $student->id));
            })->pluck('id');

        // All homeworks for those subjects, including future sessions
        $homeworks = Homework::whereHas('session', function ($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })
            ->with(['session.subject'])
            ->orderByDesc('created_at')
            ->get();

        return view('student.homework.index', compact('homeworks'));
    }
}
