<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentGradesService;

class GradesController extends Controller
{
    public function index(StudentGradesService $grades)
    {
        return view('student.grades.index', $grades->overview(auth()->user()));
    }
}
