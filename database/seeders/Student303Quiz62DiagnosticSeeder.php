<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/** Read-only diagnostics for /student/subjects/212/quizzes/62. */
class Student303Quiz62DiagnosticSeeder extends Seeder
{
    public function run(): void
    {
        $quiz = DB::table('quizzes')->where('id', 62)
            ->first(['id', 'subject_id', 'program_id', 'class_id']);
        $assignments = DB::table('student_programs')->where('student_id', 303)
            ->get(['program_id', 'class_id']);
        $primary = DB::table('users')->where('id', 303)
            ->first(['program_id', 'class_id']);

        $this->command?->line(json_encode([
            'quiz' => $quiz,
            'student_programs' => $assignments->toArray(),
            'student_primary_program' => $primary,
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));

        $student = User::find(303);
        if (! $student) {
            $this->command?->error('Student 303 is missing or deleted.');
            return;
        }

        if (! Subject::whereKey(212)->exists()) {
            $this->command?->error('404: Subject 212 is missing or deleted.');
            return;
        }

        if (! Quiz::whereKey(62)->exists()) {
            $this->command?->error('404: Quiz 62 is missing or deleted.');
            return;
        }

        if ((int) $quiz->subject_id !== 212) {
            $this->command?->error('404: Quiz 62 does not belong to subject 212.');
            return;
        }

        // Use the same class resolution and visibility scope as the student controller.
        $classIds = $student->allProgramIds()
            ->map(fn ($programId) => $student->classIdForProgram((int) $programId))
            ->filter()->unique()->values();

        $this->command?->line('Resolved student class IDs: '.$classIds->toJson());

        if (! Quiz::where('subject_id', 212)->visibleToClasses($classIds)->whereKey(62)->exists()) {
            $this->command?->error("404: Student 303 is not assigned to quiz 62's class ({$quiz->class_id}).");
            return;
        }

        $this->command?->info('Quiz 62 passes the subject and class visibility checks for student 303. Check the logged-in account and deployed routes if the URL still returns 404.');
    }
}
