<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user();

        $subjects = collect();

        if ($student->program_id) {
            $subjects = Subject::whereHas('term', function ($q) use ($student) {
                $q->where('program_id', $student->program_id);
            })
            ->with(['sessions' => function ($q) {
                $q->with(['files' => function ($fq) {
                    $fq->whereIn('type', ['pdf', 'video'])->orderBy('order');
                }])->orderBy('session_number');
            }])
            ->get();
        }

        $activeSubjectId = $request->integer('subject');

        return view('student.files.index', compact('subjects', 'activeSubjectId'));
    }
}
