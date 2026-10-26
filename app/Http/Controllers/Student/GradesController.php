<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentGradesService;

class GradesController extends Controller
{
    public function index(StudentGradesService $grades)
    {
        $overview = $grades->overview(auth()->user());
        // overview() already limits subjects to teacher-sent grade reports.
        $subjectGrades = collect($overview['subjectGrades'])
            ->map(fn ($data) => [
                'subject' => $data['subject'],
                'percentage' => $data['percentage'],
                'grade_label' => $data['grade_label'],
            ])
            ->all();

        return view('student.grades.index', compact('subjectGrades'));
    }
}
