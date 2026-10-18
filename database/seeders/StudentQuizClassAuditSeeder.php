<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\User;
use Illuminate\Database\Seeder;

/** Read-only audit: never assign students to classes automatically. */
class StudentQuizClassAuditSeeder extends Seeder
{
    public function run(): void
    {
        $studentsChecked = 0;
        $studentsFlagged = 0;
        $missingClass = 0;
        $otherClass = 0;

        $this->command?->info('Checking all non-deleted students against class-targeted quizzes. No database changes.');
        $this->command?->warn('A quiz targeting another class may be intentionally unavailable. Review these rows before changing assignments.');

        User::where('role', 'student')->chunkById(100, function ($students) use (
            &$studentsChecked, &$studentsFlagged, &$missingClass, &$otherClass
        ) {
            foreach ($students as $student) {
                $studentsChecked++;
                $flagged = false;

                foreach ($this->findBlockedQuizzes($student) as $row) {
                    $flagged = true;
                    if ($row['reason'] === 'missing_class_assignment') {
                        $missingClass++;
                    } else {
                        $otherClass++;
                    }

                    $this->command?->line(json_encode($row, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
                }

                $studentsFlagged += (int) $flagged;
            }
        });

        $this->command?->info("Students checked: {$studentsChecked}; students with blocked quizzes: {$studentsFlagged}.");
        $this->command?->info("Student/quiz pairs: {$missingClass} missing a class assignment; {$otherClass} other class or data mismatch (review required).");
        $this->command?->line('This checks class visibility only, including inactive and past quizzes; it does not diagnose every possible 404 or quiz availability rule.');
    }

    public function findBlockedQuizzes(User $student): iterable
    {
        $programIds = $student->allProgramIds();
        $classByProgram = $programIds->mapWithKeys(fn ($programId) => [
            (int) $programId => $student->classIdForProgram((int) $programId),
        ]);
        $classIds = $classByProgram->filter()->unique()->values();
        $subjectIds = Enrollment::where('student_id', $student->id)
            ->whereIn('status', ['active', 'completed'])->pluck('subject_id');

        // Only consider quizzes related to this student's programs or enrolled subjects.
        $quizzes = Quiz::with(['subject.term', 'programClass'])
            ->whereNotNull('class_id')
            ->when($classIds->isNotEmpty(), fn ($q) => $q->whereNotIn('class_id', $classIds))
            ->where(function ($q) use ($programIds, $subjectIds) {
                $q->whereIn('subject_id', $subjectIds)
                    ->orWhereIn('program_id', $programIds)
                    ->orWhereHas('programClass', fn ($c) => $c->whereIn('program_id', $programIds))
                    ->orWhereHas('subject', function ($s) use ($programIds) {
                        $s->whereIn('program_id', $programIds)
                            ->orWhereHas('term', fn ($t) => $t->whereIn('program_id', $programIds));
                    });
            })->orderBy('id')->lazyById(100);

        foreach ($quizzes as $quiz) {
            $programId = $quiz->programClass?->program_id
                ?? $quiz->program_id
                ?? $quiz->subject?->program_id
                ?? $quiz->subject?->term?->program_id;
            $currentClassId = $programId ? $classByProgram->get((int) $programId) : null;

            $reason = match (true) {
                ! $quiz->programClass => 'missing_or_deleted_quiz_class',
                ! $classByProgram->has((int) $programId) => 'missing_program_assignment',
                ! $currentClassId => 'missing_class_assignment',
                default => 'different_class_review_required',
            };

            yield [
                'student_id' => $student->id,
                'student_status' => $student->status,
                'program_id' => $programId,
                'current_class_id' => $currentClassId,
                'quiz_id' => $quiz->id,
                'subject_id' => $quiz->subject_id,
                'required_class_id' => $quiz->class_id,
                'quiz_active' => $quiz->is_active,
                'quiz_ends_at' => $quiz->ends_at?->toIso8601String(),
                'reason' => $reason,
            ];
        }
    }
}
