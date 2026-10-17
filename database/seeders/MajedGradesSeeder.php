<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Subject;
use App\Models\User;
use App\Services\StudentGradesService;
use App\Notifications\TeacherGradesNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        $count = DB::transaction(function () use ($student, $subjects) {
            foreach ($subjects as $index => $subject) {
                $teacher = $subject->teacher ?? $subject->teachers()->first();
                if (! $teacher) {
                    throw new RuntimeException("Assign a teacher to subject #{$subject->id} before seeding its grade report.");
                }

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

                $marker = 'majed-grades-demo-' . $subject->id;
                $exists = $student->notifications()
                    ->where('type', TeacherGradesNotification::class)
                    ->where('data->demo_key', $marker)->exists();

                if (! $exists) {
                    $total = (float) $enrollment->final_grade;
                    $attendance = round($total * 0.1, 1);
                    $participation = round($total * 0.3, 1);
                    $midterm = round($total * 0.2, 1);
                    $notification = new TeacherGradesNotification($teacher->id, $teacher->name, [[
                        'subject_id' => $subject->id,
                        'name' => $subject->name_ar,
                        'attendance' => $attendance,
                        'participation' => $participation,
                        'midterm' => $midterm,
                        'final' => round($total - $attendance - $participation - $midterm, 1),
                        'total' => $total,
                    ]]);
                    $student->notifications()->create([
                        'id' => (string) Str::uuid(),
                        'type' => TeacherGradesNotification::class,
                        'data' => $notification->toArray($student) + ['demo_key' => $marker],
                    ]);
                }
            }

            $count = app(StudentGradesService::class)->savedFinalGrades($student)->count();
            if ($count === 0) {
                throw new RuntimeException('Verification failed: no saved grades are visible to the grades API.');
            }

            return $count;
        });

        $this->command?->info("Verified {$count} saved grade result(s) for majed@lms.com (student ID: {$student->id}).");
        $this->command?->info('Call /api/v1/student/grades using this student\'s token.');
    }
}
