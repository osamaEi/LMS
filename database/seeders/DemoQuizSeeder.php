<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\Quiz;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoQuizSeeder extends Seeder
{
    /**
     * Creates a demo program/class/subject, a student, and a bilingual quiz
     * (Arabic instruction + English sentence) so the exam flow can be tested.
     *
     * Run: php artisan db:seed --class=DemoQuizSeeder
     */
    public function run(): void
    {
        // 1) Program
        $program = Program::firstOrCreate(
            ['code' => 'DEMO-ENG'],
            [
                'name_ar' => 'برنامج تجريبي - إنجليزي',
                'name_en' => 'Demo English Program',
                'type' => 'english',
                'status' => 'active',
                'duration_months' => 3,
            ]
        );

        // 2) Class
        $class = ProgramClass::firstOrCreate(
            ['program_id' => $program->id, 'name' => 'المجموعة التجريبية'],
            ['status' => 'active', 'max_students' => 50]
        );

        // 3) Subject
        $subject = Subject::firstOrCreate(
            ['program_id' => $program->id, 'class_id' => $class->id, 'code' => 'DEMO-GRAMMAR'],
            [
                'name_ar' => 'قواعد اللغة الإنجليزية',
                'name_en' => 'English Grammar',
                'status' => 'active',
            ]
        );

        // 4) Student
        $student = User::updateOrCreate(
            ['email' => 'demo.student@alertiqa.test'],
            [
                'name' => 'طالب تجريبي',
                'password' => Hash::make('password'),
                'role' => 'student',
                'status' => 'active',
                'program_id' => $program->id,
                'class_id' => $class->id,
                'program_status' => 'approved',
                'current_term_number' => 1,
                'email_verified_at' => now(),
                'is_confirm_user' => true,
            ]
        );

        // Enroll the student in the subject (so the quiz is accessible).
        Enrollment::firstOrCreate(
            ['student_id' => $student->id, 'subject_id' => $subject->id],
            ['status' => 'active', 'enrolled_at' => now()]
        );

        // 5) Quiz
        $quiz = Quiz::updateOrCreate(
            ['subject_id' => $subject->id, 'title_ar' => 'اختبار القواعد التجريبي'],
            [
                'title_en' => 'Demo Grammar Quiz',
                'created_by' => $student->id, // any user id; just for the FK
                'type' => 'quiz',
                'duration_minutes' => 15,
                'total_marks' => 5,
                'pass_marks' => 3,
                'max_attempts' => 3,
                'shuffle_questions' => false,
                'shuffle_answers' => false,
                'show_results' => true,
                'show_correct_answers' => true,
                'is_active' => true,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonth(),
            ]
        );

        // Reset questions for idempotency
        $quiz->questions()->each(function ($q) {
            $q->options()->delete();
            $q->delete();
        });

        // 6) Questions (Arabic instruction + English sentence) + options
        $data = [
            [
                'en' => 'She ......... eaten.',
                'opts' => [['has', true], ['have', false]],
            ],
            [
                'en' => 'They ......... played.',
                'opts' => [['has', false], ['have', true]],
            ],
            [
                'en' => 'I have worked ......... 2020.',
                'opts' => [['for', false], ['since', true]],
            ],
            [
                'en' => 'Reem has studied ......... two years.',
                'opts' => [['for', true], ['since', false]],
            ],
            [
                'en' => 'They have cooked. (negative)',
                'opts' => [['They have not cooked.', true], ['They not cooked.', false]],
            ],
        ];

        foreach ($data as $i => $row) {
            $question = Question::create([
                'quiz_id' => $quiz->id,
                'type' => 'multiple_choice',
                'question_ar' => 'اختاري الإجابة الصحيحة:',
                'question_en' => $row['en'],
                'marks' => 1,
                'order' => $i + 1,
            ]);

            foreach ($row['opts'] as $j => [$text, $correct]) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_ar' => $text,
                    'option_en' => $text,
                    'is_correct' => $correct,
                    'order' => $j + 1,
                ]);
            }
        }

        $this->command->info('✓ Demo quiz seeded.');
        $this->command->info('  Student login : demo.student@alertiqa.test / password');
        $this->command->info('  Quiz ID       : ' . $quiz->id . ' (subject ' . $subject->id . ')');
        $this->command->info('  Admin view    : /admin/quizzes/' . $quiz->id);
        $this->command->info('  Student view  : /student/subjects/' . $subject->id . '/quizzes');
    }
}
