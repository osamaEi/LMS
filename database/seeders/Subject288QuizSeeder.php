<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Database\Seeder;

/**
 * Demo quiz + questions for Subject 288 ("إدارة الموارد البشرية (٢)") so the
 * teacher quizzes screens can be exercised with realistic data.
 *
 * Run: php artisan db:seed --class=Subject288QuizSeeder
 * Idempotent: re-running reuses the same quiz (matched by title) and skips
 * re-seeding its questions.
 */
class Subject288QuizSeeder extends Seeder
{
    public function run(): void
    {
        $subject = Subject::find(288);

        if (! $subject) {
            $this->command?->warn('Subject 288 not found — skipping quiz seed.');
            return;
        }

        $quiz = Quiz::firstOrCreate(
            [
                'subject_id' => $subject->id,
                'title_ar'   => 'اختبار تجريبي — إدارة الموارد البشرية',
            ],
            [
                'created_by'           => $subject->teacher_id,
                'title_en'             => 'Demo Quiz — Human Resource Management',
                'description_ar'       => 'اختبار تجريبي يغطي أساسيات إدارة الموارد البشرية بأنواع أسئلة مختلفة.',
                'description_en'       => 'A demo quiz covering HRM basics with mixed question types.',
                'type'                 => 'quiz',
                'duration_minutes'     => 30,
                'total_marks'          => 10,
                'pass_marks'           => 5,
                'max_attempts'         => 2,
                'shuffle_questions'    => false,
                'shuffle_answers'      => true,
                'show_results'         => true,
                'show_correct_answers' => true,
                'starts_at'            => now()->startOfDay(),
                'ends_at'              => now()->addWeeks(2)->endOfDay(),
                'is_active'            => true,
            ]
        );

        if ($quiz->questions()->exists()) {
            $this->command?->info("Quiz #{$quiz->id} already has questions — skipping.");
            return;
        }

        $this->seedQuestions($quiz);

        $this->command?->info("Seeded quiz #{$quiz->id} with 4 questions for subject 288.");
    }

    private function seedQuestions(Quiz $quiz): void
    {
        // 1) Multiple choice
        $q1 = Question::create([
            'quiz_id'        => $quiz->id,
            'type'           => 'multiple_choice',
            'question_ar'    => 'أي مما يلي يُعد من الوظائف الأساسية لإدارة الموارد البشرية؟',
            'question_en'    => 'Which of the following is a core function of HR management?',
            'explanation_ar' => 'الاستقطاب والتعيين من الوظائف الجوهرية لإدارة الموارد البشرية.',
            'marks'          => 3,
            'order'          => 1,
        ]);
        $this->options($q1, [
            ['الاستقطاب والتعيين', 'Recruitment & hiring', true],
            ['تصميم المنتجات', 'Product design', false],
            ['إدارة المخزون', 'Inventory management', false],
            ['تطوير البرمجيات', 'Software development', false],
        ]);

        // 2) True / false
        $q2 = Question::create([
            'quiz_id'        => $quiz->id,
            'type'           => 'true_false',
            'question_ar'    => 'تحليل الوظائف هو عملية جمع المعلومات عن محتوى الوظيفة ومتطلباتها.',
            'question_en'    => 'Job analysis is the process of gathering information about a job\'s content and requirements.',
            'explanation_ar' => 'العبارة صحيحة؛ تحليل الوظائف أساس لوصف الوظيفة ومواصفاتها.',
            'marks'          => 2,
            'order'          => 2,
        ]);
        QuestionOption::create(['question_id' => $q2->id, 'option_ar' => 'صح',  'option_en' => 'True',  'is_correct' => true,  'order' => 1]);
        QuestionOption::create(['question_id' => $q2->id, 'option_ar' => 'خطأ', 'option_en' => 'False', 'is_correct' => false, 'order' => 2]);

        // 3) Short answer (no options — graded manually)
        Question::create([
            'quiz_id'        => $quiz->id,
            'type'           => 'short_answer',
            'question_ar'    => 'اذكر اثنين من مصادر الاستقطاب الداخلي للموظفين.',
            'question_en'    => 'Name two internal sources of employee recruitment.',
            'explanation_ar' => 'مثل: الترقية من الداخل، والنقل بين الأقسام.',
            'marks'          => 2,
            'order'          => 3,
        ]);

        // 4) Essay (no options — graded manually)
        Question::create([
            'quiz_id'        => $quiz->id,
            'type'           => 'essay',
            'question_ar'    => 'اشرح أهمية تقييم الأداء في تطوير الموارد البشرية داخل المنظمة.',
            'question_en'    => 'Explain the importance of performance appraisal in HR development.',
            'explanation_ar' => 'يُقيّم الإجابة المُصحّح يدوياً وفق وضوح الفكرة والأمثلة.',
            'marks'          => 3,
            'order'          => 4,
        ]);
    }

    /**
     * @param array<int, array{0:string,1:string,2:bool}> $rows  [ar, en, isCorrect]
     */
    private function options(Question $question, array $rows): void
    {
        foreach ($rows as $i => [$ar, $en, $correct]) {
            QuestionOption::create([
                'question_id' => $question->id,
                'option_ar'   => $ar,
                'option_en'   => $en,
                'is_correct'  => $correct,
                'order'       => $i + 1,
            ]);
        }
    }
}
