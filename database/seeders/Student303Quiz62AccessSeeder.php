<?php

namespace Database\Seeders;

use App\Models\ProgramClass;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\User;
use App\Services\StudentProgramClassService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class Student303Quiz62AccessSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $quiz = Quiz::where('subject_id', 212)->findOrFail(62);
            Subject::findOrFail(212);

            if ((int) $quiz->class_id !== 13) {
                throw new RuntimeException('Quiz 62 no longer targets class 13. Run the diagnostic seeder again.');
            }

            $class = ProgramClass::lockForUpdate()->findOrFail(13);
            $student = User::where('role', 'student')->lockForUpdate()->findOrFail(303);
            $existingClassId = $student->classIdForProgram((int) $class->program_id);

            if ($existingClassId && $existingClassId !== (int) $class->id) {
                throw new RuntimeException(
                    "Student 303 already belongs to class {$existingClassId} in program {$class->program_id}. "
                    .'No changes made: moving him to class 13 would replace that assignment.'
                );
            }

            // Use the normal assignment flow, including program eligibility and class checks.
            app(StudentProgramClassService::class)->assignStudents($class, [$student->id]);

            $student->refresh();
            $classIds = $student->allProgramIds()
                ->map(fn ($programId) => $student->classIdForProgram((int) $programId))
                ->filter()->unique()->values();

            if (! Quiz::where('subject_id', 212)->visibleToClasses($classIds)->whereKey(62)->exists()) {
                throw new RuntimeException('Quiz visibility verification failed. Assignment rolled back.');
            }
        });

        $this->command?->info('Student 303 is assigned to class 13. Quiz 62 now passes the subject and class visibility checks.');
    }
}
