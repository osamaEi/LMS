<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\Program;
use App\Models\Subject;
use App\Models\Term;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Student34UpcomingSessionsSeeder extends Seeder
{
    public function run(): void
    {
        $student   = User::findOrFail(34);
        $teacherId = User::where('role', 'teacher')->value('id');

        // ── Upcoming sessions for Diploma program 1 (class 7) ──────────────
        $diplomaClass = $student->class_id; // 7
        $diplomaTerm  = Term::where('program_id', 1)->where('term_number', 1)->first();

        $enrolledSubjectIds = \App\Models\Enrollment::where('student_id', $student->id)->pluck('subject_id');
        $subjects = Subject::where('term_id', $diplomaTerm?->id)
            ->whereIn('id', $enrolledSubjectIds)
            ->take(3)
            ->get();

        foreach ($subjects as $idx => $subject) {
            // Next session number (after the 5 already seeded)
            $nextNum = Session::where('subject_id', $subject->id)
                ->where('class_id', $diplomaClass)
                ->max('session_number') + 1;

            $scheduledAt = Carbon::now()->addDays($idx + 1)->setHour(10)->setMinute(0)->setSecond(0);

            $session = Session::firstOrCreate(
                ['subject_id' => $subject->id, 'class_id' => $diplomaClass, 'session_number' => $nextNum],
                [
                    'program_id'       => 1,
                    'teacher_id'       => $teacherId,
                    'title_ar'         => 'الجلسة ' . $nextNum . ' — ' . $subject->name_ar,
                    'type'             => 'live_zoom',
                    'scheduled_at'     => $scheduledAt,
                    'duration_minutes' => 90,
                    'zoom_join_url'    => 'https://zoom.us/j/upcoming' . $subject->id . $nextNum,
                ]
            );

            // Pre-register attendance (not attended yet)
            Attendance::updateOrCreate(
                ['student_id' => $student->id, 'session_id' => $session->id],
                ['attended' => false]
            );
        }

        // ── Upcoming sessions for Course program 8 (class 12) ──────────────
        $courseProgram = Program::find(8);
        if ($courseProgram) {
            $courseClass = DB::table('student_programs')
                ->where('student_id', $student->id)
                ->where('program_id', 8)
                ->value('class_id');

            $nextNum = Session::where('program_id', 8)
                ->where('class_id', $courseClass)
                ->max('session_number') + 1;

            for ($i = 0; $i < 2; $i++) {
                $scheduledAt = Carbon::now()->addDays($i + 2)->setHour(14)->setMinute(0)->setSecond(0);

                $session = Session::firstOrCreate(
                    ['program_id' => 8, 'class_id' => $courseClass, 'session_number' => $nextNum + $i],
                    [
                        'teacher_id'       => $teacherId,
                        'title_ar'         => 'الجلسة ' . ($nextNum + $i) . ' — ' . $courseProgram->name_ar,
                        'type'             => 'live_zoom',
                        'scheduled_at'     => $scheduledAt,
                        'duration_minutes' => 60,
                        'zoom_join_url'    => 'https://zoom.us/j/hr-upcoming' . ($nextNum + $i),
                    ]
                );

                Attendance::updateOrCreate(
                    ['student_id' => $student->id, 'session_id' => $session->id],
                    ['attended' => false]
                );
            }
        }

        $this->command->info('✓ Upcoming sessions seeded for student 34');
    }
}
