<?php

namespace Database\Seeders;

use App\Models\ProgramClass;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Run after the final quiz type migration. Does not change student enrollment. */
class MajedFinalQuizSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::where('email', 'majed@lms.com')->where('role', 'student')->first();
        if (! $student) {
            throw new RuntimeException('Student majed@lms.com was not found.');
        }

        // Resolve classes exactly as the student quiz controller does.
        $classIds = $student->allProgramIds()
            ->map(fn ($id) => $student->classIdForProgram((int) $id))
            ->filter()->unique()->values();
        if ($classIds->isEmpty()) {
            throw new RuntimeException('The student has no assigned class. Assign a class before seeding.');
        }

        $subject = Subject::with('term')->where(function ($query) use ($classIds) {
            $query->whereIn('class_id', $classIds)
                ->orWhereHas('term', fn ($term) => $term->whereIn('class_id', $classIds));
        })->orderBy('id')->first();

        $program = null;
        if ($subject) {
            $classId = $classIds->contains($subject->class_id)
                ? $subject->class_id : $subject->term->class_id;
            $teacherId = $subject->teacher_id ?: $subject->teachers()->value('users.id');
        } else {
            $program = $student->allPrograms()->first(fn ($item) =>
                in_array($item->type, ['course', 'english', 'training'], true)
                && $student->classIdForProgram((int) $item->id)
            );
            if (! $program) {
                throw new RuntimeException('No accessible subject or course was found for this student.');
            }
            $classId = $student->classIdForProgram((int) $program->id);
            $teacherId = DB::table('program_teacher')->where('program_id', $program->id)->value('teacher_id');
        }
        $teacherId ??= ProgramClass::find($classId)?->teacher_id;
        if (! $teacherId) {
            throw new RuntimeException('Assign a teacher to the selected subject/program or class first.');
        }

        $quiz = DB::transaction(function () use ($student, $subject, $program, $classId, $teacherId) {
            // Stable marker prevents duplicate quizzes on subsequent runs.
            $quiz = Quiz::firstOrCreate([
                'title_en' => 'Majed final quiz demo - student ' . $student->id,
            ], [
                'subject_id' => $subject?->id,
                'program_id' => $program?->id,
                'class_id' => $classId,
                'created_by' => $teacherId,
                'title_ar' => 'اختبار نهائي تجريبي — ' . ($subject?->name_ar ?? $program->name_ar),
                'description_ar' => 'بيانات تجريبية لاختبار ظهور الاختبار النهائي وإرسال الإجابات، وليست أسئلة تقييم معتمدة.',
                'type' => 'final',
                'duration_minutes' => 30,
                'total_marks' => 10,
                'pass_marks' => 5,
                'max_attempts' => 3,
                'shuffle_questions' => false,
                'shuffle_answers' => false,
                'show_results' => true,
                'show_correct_answers' => true,
                'starts_at' => now()->subMinute(),
                'ends_at' => now()->addWeek(),
                'is_active' => true,
            ]);

            // Never overwrite questions, attempts, grades, or schedules on reruns.
            if ($quiz->wasRecentlyCreated) {
                foreach ([
                    ['كم يساوي ٢ + ٣؟', ['٤', '٥', '٦'], 1],
                    ['كم عدد أيام الأسبوع؟', ['٥', '٦', '٧'], 2],
                ] as $index => [$text, $options, $correct]) {
                    $question = $quiz->questions()->create([
                        'type' => 'multiple_choice', 'question_ar' => $text,
                        'marks' => 5, 'order' => $index + 1,
                    ]);
                    foreach ($options as $optionIndex => $option) {
                        $question->options()->create([
                            'option_ar' => $option, 'is_correct' => $optionIndex === $correct,
                            'order' => $optionIndex + 1,
                        ]);
                    }
                }
            }

            return $quiz;
        });

        $this->command?->info(($quiz->wasRecentlyCreated ? 'Created' : 'Already exists:') . " quiz #{$quiz->id} for majed@lms.com.");
        $this->command?->warn('This demo quiz is visible to classmates too; quizzes are assigned to classes, not individual students.');
        $this->command?->info(route(
            $quiz->program_id ? 'student.quizzes.program.show' : 'student.quizzes.show',
            [$quiz->program_id ?: $quiz->subject_id, $quiz->id]
        ));
    }
}
