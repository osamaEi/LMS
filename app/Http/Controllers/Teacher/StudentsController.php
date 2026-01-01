<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();

        // Get all students enrolled in teacher's subjects
        $students = User::whereHas('enrollments.subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with(['enrollments' => function($query) use ($teacher) {
                $query->whereHas('subject', function($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })->with('subject');
            }])
            ->get();

        // Get teacher's subjects for filtering
        $subjects = Subject::where('teacher_id', $teacher->id)
            ->orderBy(app()->getLocale() === 'en' ? 'name_en' : 'name_ar')
            ->get();

        return view('teacher.students.index', compact('students', 'subjects'));
    }
}
