<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentGradesService;

class GradesController extends Controller
{
    public function index(StudentGradesService $grades)
    {
        $overview = $grades->overview(auth()->user());
        $subjectGrades = collect($overview['subjectGrades'])
            ->filter(fn ($data) => $data['final_grade'] !== null || isset($data['breakdown']['total']))
            ->all();

        return view('student.grades.index', compact('subjectGrades'));
    }
}
