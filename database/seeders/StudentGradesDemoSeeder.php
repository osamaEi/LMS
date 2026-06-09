<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Evaluation;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuizAttempt;
use App\Models\Enrollment;
use App\Models\Subject;
use Carbon\Carbon;

/**
 * Seeds grades (Evaluations + QuizAttempts) for the currently-active student
 * or any student passed via --id=X option.
 *
 * Usage:
 *   php artisan db:seed --class=StudentGradesDemoSeeder
 *   php artisan db:seed --class=StudentGradesDemoSeeder  (seeds student 4 by default)
 */
class StudentGradesDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Target student — change ID here or pass via tinker
        $studentId = 4;
        $student   = User::findOrFail($studentId);
        $teacherId = User::where('role', 'teacher')->value('id') ?? 1;

        $this->command->info("Seeding grades for: {$student->name} (ID {$student->id})");

        // Grab up to 6 enrolled subjects
        $enrolledSubjectIds = Enrollment::where('student_id', $student->id)->pluck('subject_id');
        $subjects = Subject::whereIn('id', $enrolledSubjectIds)->with('teacher')->take(6)->get();

        if ($subjects->isEmpty()) {
            $this->command->warn('No enrolled subjects found.');
            return;
        }

        $evalTypes = ['midterm_exam', 'assignment', 'quiz', 'final_exam', 'participation', 'project'];
        $scores    = [
            ['earned' => 88,  'total' => 100],
            ['earned' => 74,  'total' => 100],
            ['earned' => 92,  'total' => 100],
            ['earned' => 65,  'total' => 100],
            ['earned' => 79,  'total' => 100],
            ['earned' => 95,  'total' => 100],
        ];

        foreach ($subjects as $idx => $subject) {
            $score    = $scores[$idx % count($scores)];
            $evalType = $evalTypes[$idx % count($evalTypes)];

            // ── Evaluation ────────────────────────────────────────────────
            Evaluation::updateOrCreate(
                ['subject_id' => $subject->id, 'student_id' => $student->id, 'type' => $evalType],
                [
                    'title'        => $this->evalTitle($evalType) . ' — ' . $subject->name_ar,
                    'total_score'  => $score['total'],
                    'earned_score' => $score['earned'],
                    'percentage'   => $score['earned'],
                    'weight'       => $this->evalWeight($evalType),
                    'status'       => 'graded',
                    'graded_by'    => $subject->teacher_id ?? $teacherId,
                    'graded_at'    => Carbon::now()->subDays($idx * 5 + 3),
                    'feedback'     => $this->feedback($score['earned']),
                ]
            );

            // ── Quiz + Attempt ────────────────────────────────────────────
            $quiz = Quiz::firstOrCreate(
                ['subject_id' => $subject->id, 'title_ar' => 'اختبار قصير — ' . $subject->name_ar],
                [
                    'created_by'           => $subject->teacher_id ?? $teacherId,
                    'title_en'             => 'Short Quiz — ' . ($subject->name_en ?: $subject->name_ar),
                    'type'                 => 'quiz',
                    'duration_minutes'     => 20,
                    'total_marks'          => 10,
                    'pass_marks'           => 6,
                    'max_attempts'         => 3,
                    'shuffle_questions'    => true,
                    'shuffle_answers'      => true,
                    'show_results'         => true,
                    'show_correct_answers' => true,
                    'is_active'            => true,
                ]
            );

            // Questions (skip if already exist)
            if ($quiz->questions()->count() === 0) {
                $this->seedQuestions($quiz, $subject->name_ar);
            }

            // Attempt
            $quizScore = round($score['earned'] / 10);
            QuizAttempt::updateOrCreate(
                ['quiz_id' => $quiz->id, 'student_id' => $student->id],
                [
                    'started_at'         => Carbon::now()->subDays($idx * 4 + 2),
                    'submitted_at'       => Carbon::now()->subDays($idx * 4 + 2)->addMinutes(16),
                    'score'              => $quizScore,
                    'percentage'         => $quizScore * 10,
                    'passed'             => $quizScore >= 6,
                    'time_spent_seconds' => 960,
                ]
            );

            $this->command->info("  ✓ {$subject->name_ar} — {$score['earned']}% ({$evalType})");
        }

        $this->command->info("✓ Done — grades seeded for {$student->name}");
    }

    private function seedQuestions(Quiz $quiz, string $subjectName): void
    {
        $questions = [
            'ما هو المفهوم الأساسي في ' . $subjectName . '؟',
            'أي من التالي يُعدّ من أهداف ' . $subjectName . '؟',
            'ما الأسلوب الأمثل لتطبيق مبادئ ' . $subjectName . '؟',
        ];

        foreach ($questions as $order => $q) {
            $question = Question::create([
                'quiz_id'     => $quiz->id,
                'type'        => 'multiple_choice',
                'question_ar' => $q,
                'marks'       => 3,
                'order'       => $order + 1,
            ]);

            foreach (['الخيار الأول', 'الخيار الثاني', 'الخيار الثالث', 'الخيار الرابع'] as $i => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_ar'   => $opt,
                    'is_correct'  => $i === $order % 4,
                    'order'       => $i + 1,
                ]);
            }
        }
    }

    private function evalTitle(string $type): string
    {
        return match ($type) {
            'midterm_exam'  => 'اختبار منتصف الفصل',
            'final_exam'    => 'الاختبار النهائي',
            'assignment'    => 'تقييم واجب',
            'quiz'          => 'اختبار قصير',
            'participation' => 'تقييم المشاركة',
            'project'       => 'تقييم المشروع',
            default         => 'تقييم',
        };
    }

    private function evalWeight(string $type): float
    {
        return match ($type) {
            'midterm_exam'  => 30,
            'final_exam'    => 40,
            'assignment'    => 15,
            'quiz'          => 10,
            'participation' => 5,
            'project'       => 20,
            default         => 10,
        };
    }

    private function feedback(int $score): string
    {
        return match (true) {
            $score >= 90 => 'أداء ممتاز، استمر على هذا المستوى.',
            $score >= 75 => 'أداء جيد جداً، يُنصح بمراجعة بعض النقاط.',
            $score >= 60 => 'أداء جيد، يحتاج إلى مزيد من المراجعة.',
            default      => 'يُرجى مراجعة المحتوى وإعادة الاختبار.',
        };
    }
}
