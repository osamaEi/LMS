<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentGradesService;
use Illuminate\Http\Request;

class GradesController extends Controller
{
    public function index(Request $request, StudentGradesService $grades)
    {
        $selectedTerm = $request->integer('term') ?: null;
        $overview = $grades->overview(auth()->user(), $selectedTerm);
        // overview() already limits subjects to teacher-sent grade reports.
        $subjectGrades = collect($overview['subjectGrades'])
            ->map(fn ($data) => [
                'subject' => $data['subject'],
                'terms' => $data['terms'],
                'percentage' => $data['percentage'],
                'grade_label' => $data['grade_label'],
            ])
            ->all();
        $terms = $overview['terms'];

        return view('student.grades.index', compact('subjectGrades', 'terms', 'selectedTerm'));
    }
}
