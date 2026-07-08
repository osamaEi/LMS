<?php

namespace App\Repositories;

use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Data access for quizzes. No business rules live here — only queries and
 * persistence. Business logic belongs in QuizService.
 */
class QuizRepository
{
   
    public function subjectsForTeacher(int $teacherId): Collection
    {
        return Subject::assignedToTeacher($teacherId)
            ->with(['programClass:id,name', 'terms.programClass:id,name'])
            ->orderBy('name_ar')
            ->get();
    }

    /**
     * Course/English programs the teacher reaches — either as a class supervisor
     * (program_classes.teacher_id) or via the program_teacher pivot — each with
     * the classes that teacher is responsible for.
     *
     * @return Collection<int, Program>
     */
    public function programsForTeacher(int $teacherId): Collection
    {
        $types = ['course', 'english', 'training'];

        // Classes this teacher supervises, grouped per program.
        $supervisedClasses = ProgramClass::where('teacher_id', $teacherId)
            ->whereHas('program', fn($q) => $q->whereIn('type', $types))
            ->get(['id', 'program_id', 'name']);

        $classProgramIds = $supervisedClasses->pluck('program_id');
        $directProgramIds = DB::table('program_teacher')
            ->where('teacher_id', $teacherId)->pluck('program_id');

        $programIds = $classProgramIds->merge($directProgramIds)->unique()->values();

        $programs = Program::whereIn('id', $programIds)
            ->whereIn('type', $types)
            ->orderBy('name_ar')
            ->get();

        // Attach the classes the teacher may target for each program. A direct
        // (pivot) assignment can target any of the program's classes; a
        // supervisor targets the classes they own.
        $programs->each(function (Program $program) use ($supervisedClasses, $directProgramIds) {
            if ($directProgramIds->contains($program->id)) {
                $classes = ProgramClass::where('program_id', $program->id)->get(['id', 'name']);
            } else {
                $classes = $supervisedClasses->where('program_id', $program->id)
                    ->map(fn($c) => (object) ['id' => $c->id, 'name' => $c->name])->values();
            }
            $program->setRelation('targetClasses', $classes);
        });

        return $programs;
    }

    /**
     * Persist a new quiz from an attributes array.
     */
    public function create(array $attributes): Quiz
    {
        return Quiz::create($attributes);
    }

    /**
     * Quizzes for a program, with question/attempt counts, newest first.
     */
    public function forProgram(int $programId): Collection
    {
        return Quiz::where('program_id', $programId)
            ->withCount(['questions', 'attempts'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * A single quiz scoped to a program, or 404.
     */
    public function findForProgram(int $programId, int $quizId, array $with = [], array $withCount = []): Quiz
    {
        $query = Quiz::where('program_id', $programId);

        if ($with) {
            $query->with($with);
        }
        if ($withCount) {
            $query->withCount($withCount);
        }

        return $query->findOrFail($quizId);
    }

    /**
     * Students of a program's class — the recipients/eligible set for a
     * program-targeted quiz. When $classId is null, all students of the program.
     */
    public function studentsForProgram(int $programId, ?int $classId = null): SupportCollection
    {
        return User::where('role', 'student')
            ->whereHas('programs', function ($pq) use ($programId, $classId) {
                $pq->where('programs.id', $programId);
                if ($classId) {
                    $pq->where('student_programs.class_id', $classId);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    /**
     * Quizzes for a subject, with question/attempt counts, newest first.
     */
    public function forSubject(int $subjectId): Collection
    {
        return Quiz::where('subject_id', $subjectId)
            ->withCount(['questions', 'attempts'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * A single quiz scoped to a subject, or 404. Optionally eager-load relations
     * and request the given withCount aggregates.
     */
    public function findForSubject(int $subjectId, int $quizId, array $with = [], array $withCount = []): Quiz
    {
        $query = Quiz::where('subject_id', $subjectId);

        if ($with) {
            $query->with($with);
        }
        if ($withCount) {
            $query->withCount($withCount);
        }

        return $query->findOrFail($quizId);
    }

    /**
     * Update a quiz's attributes.
     */
    public function update(Quiz $quiz, array $attributes): Quiz
    {
        $quiz->update($attributes);

        return $quiz;
    }

    /**
     * Delete a quiz (question image cleanup is handled in the service).
     */
    public function delete(Quiz $quiz): void
    {
        $quiz->delete();
    }

    /**
     * A single question scoped to a quiz, or 404.
     */
    public function findQuestion(int $quizId, int $questionId, array $with = []): Question
    {
        $query = Question::where('quiz_id', $quizId);

        if ($with) {
            $query->with($with);
        }

        return $query->findOrFail($questionId);
    }

    /**
     * The next question order for a quiz (max + 1).
     */
    public function nextQuestionOrder(Quiz $quiz): int
    {
        return $quiz->questions()->max('order') + 1;
    }

    /**
     * Submitted attempts for a quiz with the student loaded, paginated.
     */
    public function submittedAttempts(int $quizId, int $perPage = 20): LengthAwarePaginator
    {
        return QuizAttempt::where('quiz_id', $quizId)
            ->whereNotNull('submitted_at')
            ->with('student')
            ->orderBy('submitted_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Aggregate statistics over a quiz's submitted attempts.
     */
    public function attemptStats(int $quizId): array
    {
        $base = fn() => QuizAttempt::where('quiz_id', $quizId)->whereNotNull('submitted_at');

        return [
            'total_attempts' => $base()->count(),
            'passed'         => $base()->where('passed', true)->count(),
            'failed'         => $base()->where('passed', false)->count(),
            'average_score'  => $base()->avg('percentage') ?? 0,
            'highest_score'  => $base()->max('percentage') ?? 0,
            'lowest_score'   => $base()->min('percentage') ?? 0,
        ];
    }

    /**
     * A single attempt scoped to a quiz, or 404, with relations loaded.
     */
    public function findAttempt(int $quizId, int $attemptId, array $with = []): QuizAttempt
    {
        $query = QuizAttempt::where('quiz_id', $quizId);

        if ($with) {
            $query->with($with);
        }

        return $query->findOrFail($attemptId);
    }

    /**
     * Quizzes this teacher created, filtered by search/type, paginated for the
     * cross-subject overview page.
     */
    public function overviewForTeacher(int $teacherId, string $search = '', string $type = '', int $perPage = 20): LengthAwarePaginator
    {
        return Quiz::where('created_by', $teacherId)
            ->with(['subject:id,name_ar,name_en', 'program:id,name_ar,name_en', 'programClass:id,name', 'creator:id,name'])
            ->withCount(['questions', 'attempts'])
            ->withCount(['attempts as completed_count' => fn($q) => $q->whereNotNull('submitted_at')])
            ->when($search, fn($q) => $q->where(fn($w) =>
                $w->where('title_ar', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
            ))
            ->when($type, fn($q) => $q->where('type', $type))
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Headline counters for the teacher's quiz overview page.
     */
    public function overviewStats(int $teacherId): array
    {
        return [
            'total'    => Quiz::where('created_by', $teacherId)->count(),
            'exams'    => Quiz::where('created_by', $teacherId)->where('type', 'exam')->count(),
            'quizzes'  => Quiz::where('created_by', $teacherId)->where('type', 'quiz')->count(),
            'attempts' => QuizAttempt::whereHas('quiz', fn($q) => $q->where('created_by', $teacherId))
                ->whereNotNull('submitted_at')->count(),
        ];
    }

    /**
     * Students eligible to see a quiz (mirrors student-side class visibility),
     * each decorated with their most-recent attempt from $attemptByStudent.
     */
    public function eligibleStudentsFor(Subject $subject, SupportCollection $attemptByStudent): SupportCollection
    {
        $classId = $subject->class_id;

        return User::where('role', 'student')
            ->where(function ($q) use ($subject, $classId) {
                $q->whereHas('enrollments', fn($eq) => $eq->where('subject_id', $subject->id));

                if ($classId) {
                    $q->orWhereHas('programs', fn($pq) => $pq
                        ->where('student_programs.class_id', $classId));
                    $q->orWhere('class_id', $classId);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->map(function ($student) use ($attemptByStudent) {
                $student->attempt = $attemptByStudent[$student->id] ?? null;
                return $student;
            });
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
