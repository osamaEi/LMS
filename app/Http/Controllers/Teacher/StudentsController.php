<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\ProgramClass;
use App\Models\Session;
use App\Models\User;

class StudentsController extends Controller
{
    /**
     * GET /teacher/students
     * The teacher's students, grouped by the class (المجموعة) they belong to.
     */
    public function index()
    {
        /** @var \App\Models\User $teacher */
        $teacher = auth()->user();

        // Sessions this teacher owns, plus the subjects they are assigned to.
        $sessions   = Session::where('teacher_id', $teacher->id)->get(['id', 'subject_id', 'class_id']);
        $sessionIds = $sessions->pluck('id');
        $subjectIds = $teacher->assignedSubjects()->pluck('subjects.id')
            ->merge($sessions->pluck('subject_id')->filter())
            ->unique()->values();

        // A student counts as "mine" if they attended one of my sessions or are
        // enrolled in one of my subjects.
        $studentIds = Attendance::whereIn('session_id', $sessionIds)->distinct()->pluck('student_id')
            ->merge(Enrollment::whereIn('subject_id', $subjectIds)->distinct()->pluck('student_id'))
            ->unique()->values();

        $students = User::whereIn('id', $studentIds)
            ->where('role', 'student')
            ->with(['program', 'programClass', 'programs'])
            ->orderBy('name')
            ->get();

        // Resolve each student's class: prefer the student_programs pivot, fall
        // back to the legacy users.class_id.
        $classNames = ProgramClass::pluck('name', 'id');

        $grouped = $students->groupBy(function (User $student) {
            return $student->allClassIds()->first() ?? 0;
        })->map(function ($group, $classId) use ($classNames) {
            return [
                'class_id' => $classId ?: null,
                'name'     => $classId ? ($classNames[$classId] ?? ('مجموعة #' . $classId)) : 'بدون مجموعة',
                'students' => $group->values(),
            ];
        })->sortBy('name')->values();

        $totalStudents = $students->count();

        return view('teacher.students.index', compact('grouped', 'totalStudents'));
    }
}
