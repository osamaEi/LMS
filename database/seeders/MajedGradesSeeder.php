<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class MajedGradesSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::where('email', 'majed@lms.com')->where('role', 'student')->first();
        if (! $student) {
            throw new RuntimeException('Student majed@lms.com was not found.');
        }

        $enrolledIds = Enrollment::where('student_id', $student->id)->pluck('subject_id');
        $subjects = Subject::whereIn('id', $enrolledIds)->orderBy('id')->take(3)->get();

        if ($subjects->isEmpty()) {
            $classIds = $student->allProgramIds()
                ->map(fn ($id) => $student->classIdForProgram((int) $id))
                ->filter()->unique()->values();

            $subjects = Subject::where(function ($query) use ($classIds) {
                $query->whereIn('class_id', $classIds)
                    ->orWhereHas('term', fn ($term) => $term->whereIn('class_id', $classIds));
            })->orderBy('id')->take(3)->get();
        }

        if ($subjects->isEmpty()) {
            throw new RuntimeException('Enroll majed@lms.com in a subject or assign a class with subjects first.');
        }

        DB::transaction(function () use ($student, $subjects) {
            foreach ($subjects as $index => $subject) {
                $enrollment = Enrollment::firstOrCreate([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                ], ['status' => 'active', 'enrolled_at' => now()]);

                // Preserve existing grades, including zero, when run again.
                if ($enrollment->final_grade === null) {
                    $score = [92, 84, 73][$index];
                    $enrollment->update([
                        'final_grade' => $score,
                        'grade_letter' => Enrollment::letterFor($score),
                    ]);
                }

                $this->command?->info("{$subject->name_ar}: {$enrollment->final_grade}/100");
            }
        });

        $this->command?->info('Grades ready for majed@lms.com at /api/v1/student/grades.');
    }
}
