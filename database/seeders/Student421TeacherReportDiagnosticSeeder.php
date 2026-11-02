<?php

namespace Database\Seeders;

use App\Models\Session;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Read-only diagnostics for /teacher/students/421/report (403).
 * Walks the same steps as Teacher\StudentReportController::scope() for every
 * teacher who has a session in one of the student's classes — the teachers
 * whose students list shows this student.
 */
class Student421TeacherReportDiagnosticSeeder extends Seeder
{
    private const STUDENT_ID = 421;

    public function run(): void
    {
        $student = User::find(self::STUDENT_ID);
        if (! $student) {
            $this->command?->error('Student '.self::STUDENT_ID.' is missing or deleted.');
            return;
        }

        $classIds = $student->allClassIds()
            ->merge(DB::table('student_class_memberships')->where('student_id', $student->id)->pluck('class_id'))
            ->filter()->unique()->values();

        $this->print('student', [
            'id' => $student->id,
            'name' => $student->name,
            'role' => $student->role,
            'users.class_id' => $student->class_id,
            'student_programs' => DB::table('student_programs')->where('student_id', $student->id)
                ->get(['program_id', 'class_id'])->toArray(),
            'student_class_memberships' => DB::table('student_class_memberships')
                ->where('student_id', $student->id)->pluck('class_id')->toArray(),
            'resolved_class_ids' => $classIds->toArray(),
        ]);

        if ($student->role !== 'student') {
            $this->command?->error('404: user is not a student.');
            return;
        }

        $teacherIds = Session::whereIn('class_id', $classIds)
            ->whereNotNull('teacher_id')->distinct()->pluck('teacher_id');

        if ($teacherIds->isEmpty()) {
            $this->command?->error('No teacher has a session in the student\'s classes.');
            return;
        }

        foreach (User::whereIn('id', $teacherIds)->get() as $teacher) {
            $sessionsInClass = Session::where('teacher_id', $teacher->id)
                ->whereIn('class_id', $classIds)
                ->get(['id', 'class_id', 'subject_id']);
            $taughtInClass = $sessionsInClass->pluck('subject_id')->filter()->unique()->values();

            $teacherSubjectIds = $teacher->assignedSubjects()->pluck('subjects.id')
                ->merge(Subject::where('teacher_id', $teacher->id)->pluck('id'))
                ->unique()->values();

            $inClass = fn ($q) => $q->whereIn('class_id', $classIds);
            $linkedToClass = Subject::whereIn('id', $teacherSubjectIds)
                ->where(fn ($q) => $q->whereIn('class_id', $classIds)
                    ->orWhereHas('term', $inClass)
                    ->orWhereHas('terms', $inClass))
                ->pluck('id');

            $subjectIds = $linkedToClass->merge($taughtInClass)->unique()->values();

            $this->print("teacher {$teacher->id} ({$teacher->name})", [
                'sessions_in_student_classes' => $sessionsInClass->count(),
                'of_which_without_subject' => $sessionsInClass->whereNull('subject_id')->count(),
                'subjects_taught_in_class' => $taughtInClass->toArray(),
                'teacher_subjects (assigned/owned)' => $teacherSubjectIds->toArray(),
                'teacher_subjects_linked_to_class' => $linkedToClass->toArray(),
                'teacher_subjects_detail' => Subject::whereIn('id', $teacherSubjectIds)
                    ->with(['term:id,class_id', 'terms:id,class_id'])
                    ->get(['id', 'name_ar', 'program_id', 'class_id', 'term_id'])
                    ->map(fn ($s) => [
                        'id' => $s->id,
                        'name' => $s->name_ar,
                        'program_id' => $s->program_id,
                        'class_id' => $s->class_id,
                        'term_class_id' => $s->term?->class_id,
                        'terms_class_ids' => $s->terms->pluck('class_id')->toArray(),
                    ])->toArray(),
                'report_subject_ids' => $subjectIds->toArray(),
            ]);

            $subjectIds->isEmpty()
                ? $this->command?->error("403 for teacher {$teacher->id}: no subject of theirs is in the student's classes.")
                : $this->command?->info("OK for teacher {$teacher->id}: report shows subjects ".$subjectIds->toJson());
        }
    }

    private function print(string $title, array $data): void
    {
        $this->command?->line("── {$title}");
        $this->command?->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }
}
