<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Program;
use App\Models\ProgramClass;
use App\Models\Term;
use App\Models\Subject;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuizAttempt;
use App\Models\Evaluation;
use App\Models\StudentDocument;
use Carbon\Carbon;

/**
 * Demo seeder for student ID 34 (طارق محمد):
 *  - Diploma (program 1, term 34) — sessions per enrolled subject + quizzes + evaluations
 *  - Course (program 8) — class + sessions + quiz + evaluation
 *  - Certificates (StudentDocument)
 */
class Student34DemoSeeder extends Seeder
{
    public function run(): void
    {
        $student   = User::findOrFail(34);
        $teacherId = User::where('role', 'teacher')->value('id');

        $this->command->info("Seeding for: {$student->name} (ID {$student->id})");

        // ─────────────────────────────────────────────────────────────────
        // 1. DIPLOMA — Program 1, Term 34
        // ─────────────────────────────────────────────────────────────────
        $diplomaProgram = Program::findOrFail(1);
        $diplomaTerm    = Term::where('program_id', 1)->where('term_number', 1)->firstOrFail();

        // Assign class 7 (الفصل الأول - برمجيات أ)
        $diplomaClass = ProgramClass::findOrFail(7);

        $student->update([
            'program_id'          => $diplomaProgram->id,
            'program_status'      => 'approved',
            'class_id'            => $diplomaClass->id,
            'current_term_number' => 1,
        ]);

        DB::table('student_programs')->updateOrInsert(
            ['student_id' => $student->id, 'program_id' => $diplomaProgram->id],
            ['status' => 'approved', 'class_id' => $diplomaClass->id, 'current_term_number' => 1,
             'enrolled_at' => now(), 'created_at' => now(), 'updated_at' => now()]
        );

        // Take first 4 enrolled diploma subjects
        $enrolledSubjectIds = \App\Models\Enrollment::where('student_id', $student->id)->pluck('subject_id');
        $diplomaSubjects    = Subject::where('term_id', $diplomaTerm->id)
            ->whereIn('id', $enrolledSubjectIds)->take(4)->get();

        foreach ($diplomaSubjects as $sIdx => $subject) {
            // 5 sessions per subject
            for ($i = 1; $i <= 5; $i++) {
                $scheduledAt = Carbon::now()->subDays(($sIdx * 12) + (5 - $i));
                $isEnded     = $i <= 4;

                $session = Session::firstOrCreate(
                    ['subject_id' => $subject->id, 'class_id' => $diplomaClass->id, 'session_number' => $i],
                    [
                        'program_id'       => $diplomaProgram->id,
                        'teacher_id'       => $teacherId,
                        'title_ar'         => 'الجلسة ' . $i . ' — ' . $subject->name_ar,
                        'type'             => $i % 2 === 0 ? 'recorded_video' : 'live_zoom',
                        'scheduled_at'     => $scheduledAt,
                        'duration_minutes' => 90,
                        'started_at'       => $isEnded ? $scheduledAt : null,
                        'ended_at'         => $isEnded ? $scheduledAt->copy()->addMinutes(90) : null,
                        'zoom_join_url'    => 'https://zoom.us/j/demo' . $subject->id . $i,
                        'video_url'        => $i % 2 === 0 ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null,
                    ]
                );

                Attendance::updateOrCreate(
                    ['student_id' => $student->id, 'session_id' => $session->id],
                    ['attended' => $isEnded, 'joined_at' => $isEnded ? $scheduledAt->copy()->addMinutes(1) : null, 'duration_minutes' => $isEnded ? 87 : 0]
                );
            }

            // Quiz per subject
            $quiz = Quiz::firstOrCreate(
                ['subject_id' => $subject->id, 'title_ar' => 'اختبار ' . $subject->name_ar],
                [
                    'created_by'           => $teacherId,
                    'title_en'             => 'Quiz - ' . ($subject->name_en ?: $subject->name_ar),
                    'type'                 => 'quiz',
                    'duration_minutes'     => 30,
                    'total_marks'          => 20,
                    'pass_marks'           => 12,
                    'max_attempts'         => 3,
                    'shuffle_questions'    => true,
                    'shuffle_answers'      => true,
                    'show_results'         => true,
                    'show_correct_answers' => true,
                    'is_active'            => true,
                ]
            );

            // 3 questions per quiz
            $this->seedQuizQuestions($quiz, $subject->name_ar);

            // Student attempt (passed)
            QuizAttempt::updateOrCreate(
                ['quiz_id' => $quiz->id, 'student_id' => $student->id],
                [
                    'started_at'          => Carbon::now()->subDays($sIdx * 3 + 1),
                    'submitted_at'        => Carbon::now()->subDays($sIdx * 3 + 1)->addMinutes(22),
                    'score'               => 16 + $sIdx,
                    'percentage'          => round(((16 + $sIdx) / 20) * 100, 1),
                    'passed'              => true,
                    'time_spent_seconds'  => 1320,
                ]
            );

            // Evaluation (grade)
            Evaluation::updateOrCreate(
                ['subject_id' => $subject->id, 'student_id' => $student->id, 'type' => 'midterm_exam'],
                [
                    'title'        => 'تقييم منتصف الفصل — ' . $subject->name_ar,
                    'total_score'  => 100,
                    'earned_score' => 75 + ($sIdx * 5),
                    'percentage'   => 75 + ($sIdx * 5),
                    'weight'       => 30,
                    'status'       => 'graded',
                    'graded_by'    => $teacherId,
                    'graded_at'    => now()->subDays($sIdx * 2),
                    'feedback'     => 'أداء جيد، يُنصح بمراجعة الفصل الثالث.',
                ]
            );
        }

        $this->command->info("✓ Diploma: {$diplomaSubjects->count()} subjects — sessions, quizzes, evaluations created");

        // ─────────────────────────────────────────────────────────────────
        // 2. COURSE — Program 8 (إدارة الموارد البشرية)
        // ─────────────────────────────────────────────────────────────────
        $courseProgram = Program::findOrFail(8);

        $courseClass = ProgramClass::firstOrCreate(
            ['name' => 'مجموعة الموارد البشرية أ', 'program_id' => $courseProgram->id]
        );

        DB::table('student_programs')->updateOrInsert(
            ['student_id' => $student->id, 'program_id' => $courseProgram->id],
            ['status' => 'approved', 'class_id' => $courseClass->id, 'current_term_number' => 1,
             'enrolled_at' => now(), 'created_at' => now(), 'updated_at' => now()]
        );

        // 6 sessions
        for ($i = 1; $i <= 6; $i++) {
            $scheduledAt = Carbon::now()->subDays(6 - $i);
            $isEnded     = $i <= 5;

            $session = Session::firstOrCreate(
                ['program_id' => $courseProgram->id, 'class_id' => $courseClass->id, 'session_number' => $i],
                [
                    'teacher_id'       => $teacherId,
                    'title_ar'         => 'الجلسة ' . $i . ' — ' . $courseProgram->name_ar,
                    'type'             => $i % 2 === 0 ? 'recorded_video' : 'live_zoom',
                    'scheduled_at'     => $scheduledAt,
                    'duration_minutes' => 60,
                    'started_at'       => $isEnded ? $scheduledAt : null,
                    'ended_at'         => $isEnded ? $scheduledAt->copy()->addMinutes(60) : null,
                    'zoom_join_url'    => 'https://zoom.us/j/hr' . $i,
                    'video_url'        => $i % 2 === 0 ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null,
                ]
            );

            Attendance::updateOrCreate(
                ['student_id' => $student->id, 'session_id' => $session->id],
                ['attended' => $isEnded, 'joined_at' => $isEnded ? $scheduledAt->copy()->addMinutes(1) : null, 'duration_minutes' => $isEnded ? 58 : 0]
            );
        }

        // Course quiz — attach to first enrolled diploma subject as fallback
        $courseQuizSubjectId = $enrolledSubjectIds->first();
        $courseQuiz = Quiz::firstOrCreate(
            ['title_ar' => 'اختبار ' . $courseProgram->name_ar, 'subject_id' => $courseQuizSubjectId],
            [
                'created_by'           => $teacherId,
                'title_en'             => 'Quiz - ' . $courseProgram->name_ar,
                'type'                 => 'quiz',
                'duration_minutes'     => 20,
                'total_marks'          => 10,
                'pass_marks'           => 6,
                'max_attempts'         => 2,
                'shuffle_questions'    => true,
                'show_results'         => true,
                'is_active'            => true,
            ]
        );

        $this->seedQuizQuestions($courseQuiz, $courseProgram->name_ar);

        QuizAttempt::updateOrCreate(
            ['quiz_id' => $courseQuiz->id, 'student_id' => $student->id],
            ['started_at' => now()->subDays(2), 'submitted_at' => now()->subDays(2)->addMinutes(15),
             'score' => 8, 'percentage' => 80, 'passed' => true, 'time_spent_seconds' => 900]
        );

        $this->command->info("✓ Course (program 8) — class: {$courseClass->name} — 6 sessions + quiz created");

        // ─────────────────────────────────────────────────────────────────
        // 3. CERTIFICATES (StudentDocument)
        // ─────────────────────────────────────────────────────────────────
        $certificates = [
            ['document_type' => 'certificate', 'original_name' => 'شهادة إتمام دبلوم تقنية المعلومات.pdf',   'status' => 'approved'],
            ['document_type' => 'certificate', 'original_name' => 'شهادة إدارة الموارد البشرية.pdf',          'status' => 'approved'],
            ['document_type' => 'other',        'original_name' => 'كشف الدرجات — الفصل الأول.pdf',           'status' => 'approved'],
            ['document_type' => 'certificate', 'original_name' => 'شهادة مشاركة في برنامج التدريب.pdf',      'status' => 'pending'],
        ];

        foreach ($certificates as $cert) {
            StudentDocument::firstOrCreate(
                ['user_id' => $student->id, 'original_name' => $cert['original_name']],
                [
                    'document_type' => $cert['document_type'],
                    'file_path'     => 'certificates/demo-' . md5($cert['original_name']) . '.pdf',
                    'file_size'     => rand(100000, 500000),
                    'mime_type'     => 'application/pdf',
                    'status'        => $cert['status'],
                    'reviewed_by'   => $cert['status'] === 'approved' ? 1 : null,
                    'reviewed_at'   => $cert['status'] === 'approved' ? now()->subDays(rand(1, 10)) : null,
                ]
            );
        }

        $this->command->info("✓ Certificates: " . count($certificates) . " documents created");
        $this->command->info("✓ Student 34 ({$student->name}) seeded successfully.");
    }

    private function seedQuizQuestions(Quiz $quiz, string $subjectName): void
    {
        if ($quiz->questions()->count() >= 3) return;

        $questions = [
            ['q' => 'ما هو المفهوم الأساسي في ' . $subjectName . '؟', 'correct' => 0],
            ['q' => 'أي من التالي يُعدّ من أهداف ' . $subjectName . '؟', 'correct' => 1],
            ['q' => 'ما الأسلوب الأمثل لتطبيق مبادئ ' . $subjectName . '؟', 'correct' => 2],
        ];

        foreach ($questions as $order => $qData) {
            $question = Question::create([
                'quiz_id'     => $quiz->id,
                'type'        => 'multiple_choice',
                'question_ar' => $qData['q'],
                'marks'       => round($quiz->total_marks / 3),
                'order'       => $order + 1,
            ]);

            $options = ['الخيار الأول', 'الخيار الثاني', 'الخيار الثالث', 'الخيار الرابع'];
            foreach ($options as $oIdx => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_ar'   => $opt,
                    'is_correct'  => $oIdx === $qData['correct'],
                    'order'       => $oIdx + 1,
                ]);
            }
        }
    }
}
