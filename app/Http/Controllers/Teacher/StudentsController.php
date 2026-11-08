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

        // Same class list as the admin teacher profile (owned + sessions + subjects).
        $classes = ProgramClass::whereIn('id', $teacher->teachingClassIds())->get(['id', 'name']);
        $classIds = $classes->pluck('id');
        $service = app(\App\Services\ProgramClassService::class);
        $classStudents = $classes->mapWithKeys(fn ($class) => [$class->id => $service->studentIds($class)]);
        $classStudentIds = $classStudents->flatten();
        $subjectIds = $teacher->assignedSubjects()->pluck('subjects.id')
            ->merge($sessions->pluck('subject_id')->filter())
            ->unique()->values();

        // A student counts as "mine" if they attended one of my sessions or are
        // enrolled in one of my subjects.
        $studentIds = Attendance::whereIn('session_id', $sessionIds)->distinct()->pluck('student_id')
            ->merge(Enrollment::whereIn('subject_id', $subjectIds)->distinct()->pluck('student_id'))
            ->merge($classStudentIds)
            ->unique()->values();

        $students = User::whereIn('id', $studentIds)
            ->where('role', 'student')
            ->with(['program', 'programClass', 'programs'])
            ->orderBy('name')
            ->get();

        // One group per class — including empty ones, so the list matches the
        // admin profile — with membership resolved the same way the admin counts it.
        $pastClassIds = ProgramClass::pastIds($classIds);
        $studentsById = $students->keyBy('id');

        $grouped = $classes->map(fn (ProgramClass $class) => [
                'class_id' => $class->id,
                'name'     => $class->name,
                'is_past'  => $pastClassIds->contains($class->id),
                'students' => $classStudents[$class->id]
                    ->map(fn ($id) => $studentsById->get($id))
                    ->filter()
                    ->sortBy('name')
                    ->values(),
            ])
            // Current classes first, then previous ones; alphabetical within each.
            ->sortBy(fn ($g) => ($g['is_past'] ? '1' : '0') . $g['name'])
            ->values();

        // Students reached through attendance/enrollment but in none of these classes.
        $ungrouped = $students->whereNotIn('id', $classStudentIds->unique()->all())->values();
        if ($ungrouped->isNotEmpty()) {
            $grouped->push(['class_id' => null, 'name' => 'بدون مجموعة', 'is_past' => false, 'students' => $ungrouped]);
        }

        $totalStudents = $students->count();

        return view('teacher.students.index', compact('grouped', 'totalStudents'));
    }
}
