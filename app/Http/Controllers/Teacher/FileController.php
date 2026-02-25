<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user();

        $subjects = Subject::where('teacher_id', $teacher->id)
            ->with(['sessions' => function ($q) {
                $q->with(['files' => function ($fq) {
                    $fq->whereIn('type', ['pdf', 'video'])->orderBy('order');
                }])->orderBy('session_number');
            }])
            ->get();

        $activeSubjectId = $request->integer('subject');

        return view('teacher.files.index', compact('subjects', 'activeSubjectId'));
    }
}
