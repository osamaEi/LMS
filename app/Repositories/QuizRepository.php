<?php

namespace App\Repositories;

use App\Models\Quiz;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Data access for quizzes. No business rules live here — only queries and
 * persistence. Business logic belongs in QuizService.
 */
class QuizRepository
{
    /**
     * Subjects the given teacher is assigned to, with the relations needed to
     * resolve their target classes (own class + classes reached via terms).
     */
    public function subjectsForTeacher(int $teacherId): Collection
    {
        return Subject::assignedToTeacher($teacherId)
            ->with(['programClass:id,name', 'terms.programClass:id,name'])
            ->orderBy('name_ar')
            ->get();
    }

    /**
     * Persist a new quiz from an attributes array.
     */
    public function create(array $attributes): Quiz
    {
        return Quiz::create($attributes);
    }

    /**
     * Students who should see a quiz for the given subject, optionally narrowed
     * to a single target class. Mirrors the student-side visibility rules.
     */
    public function studentsForSubject(Subject $subject, ?int $classId = null): SupportCollection
    {
        $targetClassId = $classId ?? $subject->class_id;

        return User::where('role', 'student')
            ->where(function ($q) use ($subject, $targetClassId) {
                // Direct enrollment in the subject always counts.
                $q->whereHas('enrollments', fn($eq) => $eq->where('subject_id', $subject->id));

                if ($targetClassId) {
                    // Assigned to the target class via the student_programs pivot.
                    $q->orWhereHas('programs', fn($pq) => $pq
                        ->where('student_programs.class_id', $targetClassId));
                    // Legacy users.class_id fallback.
                    $q->orWhere('class_id', $targetClassId);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
