<?php

namespace App\Services;

use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\Subject;
use App\Repositories\QuizRepository;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
     * Build the class → subjects options for a class-first create form. Each
     * class carries the subjects that reach it (own class + classes via terms),
     * so the teacher picks a class and then only its subjects appear.
     *
     * @return Collection<int, array{id:int, name:string, subjects:Collection}>
     */
    public function selectableClasses(int $teacherId): Collection
    {
        $classes = collect();

        $ensureBucket = function ($id, $name) use ($classes) {
            return $classes->get($id) ?? [
                'id'       => $id,
                'name'     => $name,
                'subjects' => collect(),
                'program'  => null,
            ];
        };

        // Diploma path: classes reached through the teacher's subjects.
        $this->quizzes->subjectsForTeacher($teacherId)->each(function (Subject $subject) use ($classes, $ensureBucket) {
            $subjectEntry = ['id' => $subject->id, 'name' => $subject->name_ar ?? $subject->name];

            foreach ($this->classesForSubject($subject) as $class) {
                $bucket = $ensureBucket($class['id'], $class['name']);
                $bucket['subjects']->push($subjectEntry);
                $classes->put($class['id'], $bucket);
            }
        });

        // Course/English path: each program class carries its program directly.
        $this->quizzes->programsForTeacher($teacherId)->each(function ($program) use ($classes, $ensureBucket) {
            foreach ($program->targetClasses as $class) {
                $bucket = $ensureBucket($class->id, $class->name);
                $bucket['program'] = ['id' => $program->id, 'name' => $program->name_ar ?? $program->name];
                $classes->put($class->id, $bucket);
            }
        });

        return $classes->values()->map(fn($c) => [
            'id'       => $c['id'],
            'name'     => $c['name'],
            'subjects' => $c['subjects']->unique('id')->values(),
            'program'  => $c['program'],
        ]);
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

    /**
     * Create a quiz for a course/English program, targeting one of its classes.
     *
     * @throws ValidationException when the class does not belong to the program,
     *         or the teacher does not reach that program.
     */
    public function createForProgram(int $programId, int $classId, int $teacherId, array $data): Quiz
    {
        $program = $this->quizzes->programsForTeacher($teacherId)->firstWhere('id', $programId);

        if (!$program) {
            throw ValidationException::withMessages([
                'program_id' => 'البرنامج المختار غير متاح لك.',
            ]);
        }

        if (!$program->targetClasses->contains('id', $classId)) {
            throw ValidationException::withMessages([
                'class_id' => 'الفصل المختار لا يخص هذا البرنامج.',
            ]);
        }

        $quiz = $this->quizzes->create(array_merge($data, [
            'program_id' => $programId,
            'class_id'   => $classId,
            'created_by' => $teacherId,
        ]));

        $this->notifications->notifyQuizCreated($quiz);

        return $quiz;
    }

    /**
     * Create a quiz directly from validated attributes (subject-scoped form),
     * notifying enrolled students.
     */
    public function create(int $subjectId, int $teacherId, array $data): Quiz
    {
        $quiz = $this->quizzes->create(array_merge($data, [
            'subject_id' => $subjectId,
            'created_by' => $teacherId,
        ]));

        $this->notifications->notifyQuizCreated($quiz);

        return $quiz;
    }

    /**
     * Update a quiz's attributes.
     */
    public function update(Quiz $quiz, array $data): Quiz
    {
        return $this->quizzes->update($quiz, $data);
    }

    /**
     * Delete a quiz along with any images attached to its questions.
     */
    public function delete(Quiz $quiz): void
    {
        foreach ($quiz->questions as $question) {
            $this->deleteImage($question->image);
        }

        $this->quizzes->delete($quiz);
    }

    /**
     * Create a question (plus its options) for a quiz. Rolls back and cleans up
     * an uploaded image if anything fails.
     */
    public function createQuestion(Quiz $quiz, array $data, ?UploadedFile $image = null): Question
    {
        $imagePath = $image ? $image->store('uploads/images', 'public') : null;

        try {
            return DB::transaction(function () use ($quiz, $data, $imagePath) {
                $question = Question::create([
                    'quiz_id'        => $quiz->id,
                    'type'           => $data['type'],
                    'question_ar'    => $data['question_ar'],
                    'question_en'    => $data['question_en'] ?? null,
                    'explanation_ar' => $data['explanation_ar'] ?? null,
                    'explanation_en' => $data['explanation_en'] ?? null,
                    'marks'          => $data['marks'],
                    'order'          => $data['order'],
                    'image'          => $imagePath,
                ]);

                $this->syncOptions($question, $data);

                return $question;
            });
        } catch (\Throwable $e) {
            $this->deleteImage($imagePath);
            throw $e;
        }
    }

    /**
     * Update a question (plus its options), handling image replace/removal.
     */
    public function updateQuestion(Question $question, array $data, ?UploadedFile $image = null, bool $removeImage = false): Question
    {
        $imagePath = $question->image;

        if ($removeImage && $question->image) {
            $this->deleteImage($question->image);
            $imagePath = null;
        }

        if ($image) {
            $this->deleteImage($question->image);
            $imagePath = $image->store('uploads/images', 'public');
        }

        return DB::transaction(function () use ($question, $data, $imagePath) {
            $question->update([
                'type'           => $data['type'],
                'question_ar'    => $data['question_ar'],
                'question_en'    => $data['question_en'] ?? null,
                'explanation_ar' => $data['explanation_ar'] ?? null,
                'explanation_en' => $data['explanation_en'] ?? null,
                'marks'          => $data['marks'],
                'order'          => $data['order'],
                'image'          => $imagePath,
            ]);

            $question->options()->delete();
            $this->syncOptions($question, $data);

            return $question;
        });
    }

    /**
     * Delete a question and its image.
     */
    public function deleteQuestion(Question $question): void
    {
        $this->deleteImage($question->image);
        $question->delete();
    }

    /**
     * Create the option rows for a question based on its type. Multiple-choice
     * uses the submitted options; true/false generates the fixed pair.
     */
    private function syncOptions(Question $question, array $data): void
    {
        if ($data['type'] === 'multiple_choice' && !empty($data['options'])) {
            foreach ($data['options'] as $index => $option) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_ar'   => $option['text_ar'],
                    'option_en'   => $option['text_en'] ?? null,
                    'is_correct'  => isset($option['is_correct']) && $option['is_correct'],
                    'order'       => $index + 1,
                ]);
            }
        } elseif ($data['type'] === 'true_false') {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_ar'   => 'صح',
                'option_en'   => 'True',
                'is_correct'  => $data['correct_answer'] === 'true',
                'order'       => 1,
            ]);
            QuestionOption::create([
                'question_id' => $question->id,
                'option_ar'   => 'خطأ',
                'option_en'   => 'False',
                'is_correct'  => $data['correct_answer'] === 'false',
                'order'       => 2,
            ]);
        }
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
