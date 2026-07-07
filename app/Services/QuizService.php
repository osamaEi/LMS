<?php

namespace App\Services;

use App\Models\Quiz;
use App\Models\Subject;
use App\Repositories\QuizRepository;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Business logic for quizzes: what a teacher may target, and how a quiz is
 * created. Controllers stay thin and delegate here.
 */
class QuizService
{
    public function __construct(
        protected QuizRepository $quizzes,
        protected NotificationService $notifications,
    ) {}

    /**
     * Build the subject → classes options the teacher may choose from when
     * creating a quiz. Each subject carries the list of classes it reaches
     * (its own class plus any class linked through its terms).
     *
     * @return Collection<int, array{id:int, name:string, classes:Collection}>
     */
    public function selectableSubjects(int $teacherId): Collection
    {
        return $this->quizzes->subjectsForTeacher($teacherId)->map(function (Subject $subject) {
            return [
                'id'      => $subject->id,
                'name'    => $subject->name_ar ?? $subject->name,
                'classes' => $this->classesForSubject($subject),
            ];
        })->values();
    }

    /**
     * The distinct classes a subject reaches, as {id, name} pairs.
     */
    public function classesForSubject(Subject $subject): Collection
    {
        $classes = collect();

        // The subject's own class.
        if ($subject->programClass) {
            $classes->push($subject->programClass);
        }

        // Any class reached through the subject's terms.
        foreach ($subject->terms as $term) {
            if ($term->programClass) {
                $classes->push($term->programClass);
            }
        }

        return $classes->filter()
            ->unique('id')
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->name])
            ->values();
    }

    /**
     * Create a quiz for a teacher-owned subject, optionally targeting one class.
     *
     * @param  array  $data  Validated quiz attributes (without subject/class/creator).
     * @throws ValidationException when the class does not belong to the subject.
     */
    public function createForSubject(Subject $subject, ?int $classId, int $teacherId, array $data): Quiz
    {
        if ($classId !== null) {
            $allowed = $this->classesForSubject($subject)->pluck('id');
            if (!$allowed->contains($classId)) {
                throw ValidationException::withMessages([
                    'class_id' => 'الفصل المختار لا يخص هذا المقرر.',
                ]);
            }
        }

        $quiz = $this->quizzes->create(array_merge($data, [
            'subject_id' => $subject->id,
            'class_id'   => $classId,
            'created_by' => $teacherId,
        ]));

        $this->notifications->notifyQuizCreated($quiz);

        return $quiz;
    }
}
