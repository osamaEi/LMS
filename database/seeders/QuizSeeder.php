<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get subject 3 or first available subject
        $subject = Subject::find(3) ?? Subject::first();

        if (!$subject) {
            $this->command->error('No subjects found. Please create a subject first.');
            return;
        }

        // Get teacher (creator)
        $teacher = User::where('role', 'teacher')->first() ?? User::first();

        // Create the quiz
        $quiz = Quiz::create([
            'subject_id' => $subject->id,
            'created_by' => $teacher->id,
            'title_ar' => 'اختبار شامل في المادة',
            'title_en' => 'Comprehensive Subject Test',
            'description_ar' => 'اختبار شامل يغطي جميع المواضيع الأساسية في المادة. يرجى قراءة الأسئلة بعناية قبل الإجابة.',
            'description_en' => 'A comprehensive test covering all basic topics in the subject.',
            'type' => 'quiz',
            'duration_minutes' => 30,
            'total_marks' => 100,
            'pass_marks' => 60,
            'max_attempts' => 3,
            'shuffle_questions' => true,
            'shuffle_answers' => true,
            'show_results' => true,
            'show_correct_answers' => true,
            'starts_at' => '2026-01-10 21:58:24',
            'ends_at' => '2026-01-20 23:59:59',
            'is_active' => true,
        ]);

        $this->command->info("Quiz created: {$quiz->title_ar}");

        // Question 1: Multiple Choice
        $q1 = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'multiple_choice',
            'question_ar' => 'ما هي عاصمة المملكة العربية السعودية؟',
            'question_en' => 'What is the capital of Saudi Arabia?',
            'explanation_ar' => 'الرياض هي عاصمة المملكة العربية السعودية وأكبر مدنها.',
            'marks' => 10,
            'order' => 1,
        ]);
        QuestionOption::insert([
            ['question_id' => $q1->id, 'option_ar' => 'الرياض', 'option_en' => 'Riyadh', 'is_correct' => true, 'order' => 1],
            ['question_id' => $q1->id, 'option_ar' => 'جدة', 'option_en' => 'Jeddah', 'is_correct' => false, 'order' => 2],
            ['question_id' => $q1->id, 'option_ar' => 'مكة المكرمة', 'option_en' => 'Mecca', 'is_correct' => false, 'order' => 3],
            ['question_id' => $q1->id, 'option_ar' => 'الدمام', 'option_en' => 'Dammam', 'is_correct' => false, 'order' => 4],
        ]);

        // Question 2: True/False
        $q2 = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'true_false',
            'question_ar' => 'الشمس تدور حول الأرض.',
            'question_en' => 'The sun revolves around the earth.',
            'explanation_ar' => 'الأرض تدور حول الشمس وليس العكس.',
            'marks' => 10,
            'order' => 2,
        ]);
        QuestionOption::insert([
            ['question_id' => $q2->id, 'option_ar' => 'صح', 'option_en' => 'True', 'is_correct' => false, 'order' => 1],
            ['question_id' => $q2->id, 'option_ar' => 'خطأ', 'option_en' => 'False', 'is_correct' => true, 'order' => 2],
        ]);

        // Question 3: Multiple Choice
        $q3 = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'multiple_choice',
            'question_ar' => 'كم عدد أركان الإسلام؟',
            'question_en' => 'How many pillars of Islam are there?',
            'explanation_ar' => 'أركان الإسلام خمسة: الشهادتان، الصلاة، الزكاة، الصوم، والحج.',
            'marks' => 10,
            'order' => 3,
        ]);
        QuestionOption::insert([
            ['question_id' => $q3->id, 'option_ar' => 'ثلاثة', 'option_en' => 'Three', 'is_correct' => false, 'order' => 1],
            ['question_id' => $q3->id, 'option_ar' => 'أربعة', 'option_en' => 'Four', 'is_correct' => false, 'order' => 2],
            ['question_id' => $q3->id, 'option_ar' => 'خمسة', 'option_en' => 'Five', 'is_correct' => true, 'order' => 3],
            ['question_id' => $q3->id, 'option_ar' => 'ستة', 'option_en' => 'Six', 'is_correct' => false, 'order' => 4],
        ]);

        // Question 4: True/False
        $q4 = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'true_false',
            'question_ar' => 'الماء يتكون من ذرتين هيدروجين وذرة أكسجين.',
            'question_en' => 'Water consists of two hydrogen atoms and one oxygen atom.',
            'explanation_ar' => 'الصيغة الكيميائية للماء هي H2O.',
            'marks' => 10,
            'order' => 4,
        ]);
        QuestionOption::insert([
            ['question_id' => $q4->id, 'option_ar' => 'صح', 'option_en' => 'True', 'is_correct' => true, 'order' => 1],
            ['question_id' => $q4->id, 'option_ar' => 'خطأ', 'option_en' => 'False', 'is_correct' => false, 'order' => 2],
        ]);

        // Question 5: Multiple Choice
        $q5 = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'multiple_choice',
            'question_ar' => 'ما هو أكبر كوكب في المجموعة الشمسية؟',
            'question_en' => 'What is the largest planet in the solar system?',
            'explanation_ar' => 'المشتري هو أكبر كوكب في المجموعة الشمسية.',
            'marks' => 10,
            'order' => 5,
        ]);
        QuestionOption::insert([
            ['question_id' => $q5->id, 'option_ar' => 'الأرض', 'option_en' => 'Earth', 'is_correct' => false, 'order' => 1],
            ['question_id' => $q5->id, 'option_ar' => 'المريخ', 'option_en' => 'Mars', 'is_correct' => false, 'order' => 2],
            ['question_id' => $q5->id, 'option_ar' => 'المشتري', 'option_en' => 'Jupiter', 'is_correct' => true, 'order' => 3],
            ['question_id' => $q5->id, 'option_ar' => 'زحل', 'option_en' => 'Saturn', 'is_correct' => false, 'order' => 4],
        ]);

        // Question 6: Short Answer
        $q6 = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'short_answer',
            'question_ar' => 'ما هو ناتج 15 × 8؟',
            'question_en' => 'What is 15 × 8?',
            'explanation_ar' => 'الناتج هو 120.',
            'marks' => 10,
            'order' => 6,
        ]);

        // Question 7: Multiple Choice
        $q7 = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'multiple_choice',
            'question_ar' => 'في أي عام تأسست المملكة العربية السعودية؟',
            'question_en' => 'In what year was Saudi Arabia founded?',
            'explanation_ar' => 'تأسست المملكة العربية السعودية عام 1932م.',
            'marks' => 10,
            'order' => 7,
        ]);
        QuestionOption::insert([
            ['question_id' => $q7->id, 'option_ar' => '1920', 'option_en' => '1920', 'is_correct' => false, 'order' => 1],
            ['question_id' => $q7->id, 'option_ar' => '1932', 'option_en' => '1932', 'is_correct' => true, 'order' => 2],
            ['question_id' => $q7->id, 'option_ar' => '1945', 'option_en' => '1945', 'is_correct' => false, 'order' => 3],
            ['question_id' => $q7->id, 'option_ar' => '1950', 'option_en' => '1950', 'is_correct' => false, 'order' => 4],
        ]);

        // Question 8: True/False
        $q8 = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'true_false',
            'question_ar' => 'اللغة العربية تُكتب من اليمين إلى اليسار.',
            'question_en' => 'Arabic is written from right to left.',
            'explanation_ar' => 'اللغة العربية تُكتب من اليمين إلى اليسار.',
            'marks' => 10,
            'order' => 8,
        ]);
        QuestionOption::insert([
            ['question_id' => $q8->id, 'option_ar' => 'صح', 'option_en' => 'True', 'is_correct' => true, 'order' => 1],
            ['question_id' => $q8->id, 'option_ar' => 'خطأ', 'option_en' => 'False', 'is_correct' => false, 'order' => 2],
        ]);

        // Question 9: Essay
        $q9 = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'essay',
            'question_ar' => 'اشرح أهمية التعليم في بناء المجتمعات الحديثة.',
            'question_en' => 'Explain the importance of education in building modern societies.',
            'explanation_ar' => 'يتم تقييم هذا السؤال يدوياً من قبل المعلم.',
            'marks' => 10,
            'order' => 9,
        ]);

        // Question 10: Multiple Choice
        $q10 = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'multiple_choice',
            'question_ar' => 'ما هي أطول سورة في القرآن الكريم؟',
            'question_en' => 'What is the longest surah in the Quran?',
            'explanation_ar' => 'سورة البقرة هي أطول سورة في القرآن الكريم.',
            'marks' => 10,
            'order' => 10,
        ]);
        QuestionOption::insert([
            ['question_id' => $q10->id, 'option_ar' => 'سورة الفاتحة', 'option_en' => 'Al-Fatiha', 'is_correct' => false, 'order' => 1],
            ['question_id' => $q10->id, 'option_ar' => 'سورة البقرة', 'option_en' => 'Al-Baqarah', 'is_correct' => true, 'order' => 2],
            ['question_id' => $q10->id, 'option_ar' => 'سورة آل عمران', 'option_en' => 'Al-Imran', 'is_correct' => false, 'order' => 3],
            ['question_id' => $q10->id, 'option_ar' => 'سورة النساء', 'option_en' => 'An-Nisa', 'is_correct' => false, 'order' => 4],
        ]);

        $this->command->info("10 questions created successfully!");
        $this->command->info("Quiz ID: {$quiz->id}");
        $this->command->info("Subject ID: {$subject->id}");
    }
}
