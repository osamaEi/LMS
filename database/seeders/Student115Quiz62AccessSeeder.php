<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\User;
use App\Services\StudentProgramClassService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class Student115Quiz62AccessSeeder extends Seeder
{
    public function run(): void
    {
        $assignment = DB::transaction(function () {
            $quiz = Quiz::where('subject_id', 212)->findOrFail(62);
            Subject::findOrFail(212);

            if ((int) $quiz->class_id !== 13) {
                throw new RuntimeException('Quiz 62 no longer targets class 13. No changes made.');
            }

            $class = ProgramClass::lockForUpdate()->findOrFail(13);
            Program::findOrFail($class->program_id);
            $student = User::where('role', 'student')->lockForUpdate()->findOrFail(115);
            $previousClassId = $student->classIdForProgram((int) $class->program_id);

            // Enroll only in this quiz's program; preserve existing progress and dates.
            $enrollment = $student->programs()->where('programs.id', $class->program_id)->first();
            if (! $enrollment) {
                $isPrimary = (int) $student->program_id === (int) $class->program_id;
                $student->programs()->attach($class->program_id, [
                    'status' => $isPrimary && $student->program_status === 'completed' ? 'completed' : 'approved',
                    'current_term_number' => $isPrimary ? ($student->current_term_number ?? 1) : 1,
                    'enrolled_at' => now(),
                ]);
            } elseif (! in_array($enrollment->pivot->status, ['approved', 'completed'], true)) {
                $student->programs()->updateExistingPivot($class->program_id, ['status' => 'approved']);
            }

            // Applies the normal active-class and capacity checks, and updates the
            // legacy class column when this is the student's primary program.
            app(StudentProgramClassService::class)->assignStudents($class, [$student->id]);

            $student->refresh();
            $classIds = $student->allProgramIds()
                ->map(fn ($programId) => $student->classIdForProgram((int) $programId))
                ->filter()->unique()->values();

            if (! Quiz::where('subject_id', 212)->visibleToClasses($classIds)->whereKey(62)->exists()) {
                throw new RuntimeException('Quiz visibility verification failed. All changes rolled back.');
            }

            return ['program_id' => $class->program_id, 'previous_class_id' => $previousClassId];
        });

        $previous = $assignment['previous_class_id'] ?? 'none';
        $this->command?->info("Student 115: program {$assignment['program_id']}, class {$previous} -> 13. Quiz 62 passes the subject and class visibility checks.");
    }
}
