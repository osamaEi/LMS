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
use Carbon\Carbon;

/**
 * Demo seeder for student ID 29:
 *  - Enrolls in diploma (program 1) with class + term subjects + sessions
 *  - Enrolls in two courses (program 5 & 7) each with a class + sessions
 *  - Creates Attendance records for every session
 */
class Student29DemoSeeder extends Seeder
{
    public function run(): void
    {
        $student = User::findOrFail(29);
        $teacherId = User::where('role', 'teacher')->value('id'); // first teacher

        // ────────────────────────────────────────────────────────────────────
        // 1.  DIPLOMA — Program 1  (قسم الحاسب وتقنية المعلومات)
        // ────────────────────────────────────────────────────────────────────
        $diplomaProgram = Program::findOrFail(1);

        // Class for diploma
        $diplomaClass = ProgramClass::firstOrCreate(
            ['name' => 'الفصل الأول - برمجيات أ', 'program_id' => $diplomaProgram->id],
        );

        // Active term (term 1)
        $diplomaTerm = Term::where('program_id', $diplomaProgram->id)
            ->where('term_number', 1)
            ->firstOrFail(); // term id=34 from your data

        // Set as primary program on the user
        $student->update([
            'program_id'          => $diplomaProgram->id,
            'program_status'      => 'approved',
            'class_id'            => $diplomaClass->id,
            'current_term_number' => 1,
        ]);

        // Ensure pivot row exists too
        DB::table('student_programs')->updateOrInsert(
            ['student_id' => $student->id, 'program_id' => $diplomaProgram->id],
            [
                'status'               => 'approved',
                'class_id'             => $diplomaClass->id,
                'current_term_number'  => 1,
                'enrolled_at'          => now(),
                'created_at'           => now(),
                'updated_at'           => now(),
            ]
        );

        // Pick first 3 subjects in the active term
        $diplomaSubjects = Subject::where('term_id', $diplomaTerm->id)->take(3)->get();

        foreach ($diplomaSubjects as $index => $subject) {
            // 4 sessions per subject
            for ($i = 1; $i <= 4; $i++) {
                $scheduledAt = Carbon::now()->subDays(($index * 10) + (4 - $i));
                $isEnded     = $i <= 3; // last session is upcoming

                $session = Session::firstOrCreate(
                    [
                        'subject_id'     => $subject->id,
                        'class_id'       => $diplomaClass->id,
                        'session_number' => $i,
                    ],
                    [
                        'program_id'       => $diplomaProgram->id,
                        'teacher_id'       => $teacherId,
                        'title_ar'         => 'الجلسة ' . $i . ' - ' . $subject->name_ar,
                        'title_en'         => 'Session ' . $i,
                        'type'             => 'live_zoom',
                        'scheduled_at'     => $scheduledAt,
                        'duration_minutes' => 90,
                        'status'           => $isEnded ? 'ended' : 'upcoming',
                        'started_at'       => $isEnded ? $scheduledAt : null,
                        'ended_at'         => $isEnded ? $scheduledAt->copy()->addMinutes(90) : null,
                        'zoom_join_url'    => 'https://zoom.us/j/demo' . $subject->id . $i,
                    ]
                );

                Attendance::updateOrCreate(
                    ['student_id' => $student->id, 'session_id' => $session->id],
                    [
                        'attended'         => $isEnded,
                        'joined_at'        => $isEnded ? $scheduledAt->copy()->addMinutes(2) : null,
                        'duration_minutes' => $isEnded ? 85 : 0,
                    ]
                );
            }
        }

        $this->command->info("✓ Diploma (program 1) — class: {$diplomaClass->name} — {$diplomaSubjects->count()} subjects, sessions created");

        // ────────────────────────────────────────────────────────────────────
        // 2.  COURSE — Program 5  (إدخال البيانات)
        // ────────────────────────────────────────────────────────────────────
        $this->seedCourse($student, 5, 'مجموعة إدخال البيانات أ', $teacherId);

        // ────────────────────────────────────────────────────────────────────
        // 3.  COURSE — Program 7  (التسويق الرقمي)
        // ────────────────────────────────────────────────────────────────────
        $this->seedCourse($student, 7, 'مجموعة التسويق أ', $teacherId);

        $this->command->info("✓ Student 29 ({$student->name}) seeded successfully.");
    }

    private function seedCourse(User $student, int $programId, string $className, int $teacherId): void
    {
        $program = Program::findOrFail($programId);

        $class = ProgramClass::firstOrCreate(
            ['name' => $className, 'program_id' => $program->id],
        );

        // Pivot enrollment (additional program)
        DB::table('student_programs')->updateOrInsert(
            ['student_id' => $student->id, 'program_id' => $program->id],
            [
                'status'              => 'approved',
                'class_id'            => $class->id,
                'current_term_number' => 1,
                'enrolled_at'         => now(),
                'created_at'          => now(),
                'updated_at'          => now(),
            ]
        );

        // Create 6 sessions for this course program
        for ($i = 1; $i <= 6; $i++) {
            $scheduledAt = Carbon::now()->subDays(6 - $i);
            $isEnded     = $i <= 4;

            $type = $i % 2 === 0 ? 'recorded_video' : 'live_zoom';

            $session = Session::firstOrCreate(
                [
                    'program_id'     => $program->id,
                    'class_id'       => $class->id,
                    'session_number' => $i,
                ],
                [
                    'teacher_id'       => $teacherId,
                    'title_ar'         => 'الجلسة ' . $i . ' — ' . $program->name_ar,
                    'title_en'         => 'Session ' . $i,
                    'type'             => $type,
                    'scheduled_at'     => $scheduledAt,
                    'duration_minutes' => 60,
                    'status'           => $isEnded ? 'ended' : 'upcoming',
                    'started_at'       => $isEnded ? $scheduledAt : null,
                    'ended_at'         => $isEnded ? $scheduledAt->copy()->addMinutes(60) : null,
                    'zoom_join_url'    => $type === 'live_zoom' ? 'https://zoom.us/j/demo' . $programId . $i : null,
                    'video_url'        => $type === 'recorded_video' ? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' : null,
                ]
            );

            Attendance::updateOrCreate(
                ['student_id' => $student->id, 'session_id' => $session->id],
                [
                    'attended'         => $isEnded,
                    'joined_at'        => $isEnded ? $scheduledAt->copy()->addMinutes(1) : null,
                    'duration_minutes' => $isEnded ? 58 : 0,
                ]
            );
        }

        $this->command->info("✓ Course (program {$programId} — {$program->name_ar}) — class: {$className} — 6 sessions created");
    }
}
