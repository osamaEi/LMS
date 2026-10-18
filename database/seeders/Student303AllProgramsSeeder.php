<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\Program;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Student303AllProgramsSeeder extends Seeder
{
    public function run(): void
    {
        $counts = DB::transaction(function () {
            $student = User::where('role', 'student')->lockForUpdate()->findOrFail(303);
            $programs = Program::whereIn('type', ['diploma', 'course'])->orderBy('id')->get();

            if ($programs->isEmpty()) {
                return [0, 0];
            }

            if ($student->program_id === null) {
                $student->program_id = $programs->first()->id;
                $student->current_term_number = 1;
            }

            if ($programs->contains('id', $student->program_id)
                && $student->program_status !== 'completed') {
                $student->program_status = 'approved';
            }

            $student->status = 'active';
            $student->save();

            $existing = $student->programs()->get()->keyBy('id');

            foreach ($programs as $program) {
                $pivot = $existing->get($program->id)?->pivot;

                if ($pivot) {
                    if (! in_array($pivot->status, ['approved', 'completed'], true)) {
                        $student->programs()->updateExistingPivot($program->id, ['status' => 'approved']);
                    }

                    continue;
                }

                $isPrimary = (int) $student->program_id === (int) $program->id;
                $student->programs()->attach($program->id, [
                    'status' => $isPrimary ? $student->program_status : 'approved',
                    'class_id' => $isPrimary ? $student->class_id : null,
                    'current_term_number' => $isPrimary ? ($student->current_term_number ?? 1) : 1,
                    'enrolled_at' => now(),
                ]);
            }

            // Subjects can belong directly to a program or through a diploma term.
            $programIds = $programs->modelKeys();
            $subjects = Subject::where(function ($query) use ($programIds) {
                $query->whereIn('program_id', $programIds)
                    ->orWhereHas('term', fn ($term) => $term->whereIn('program_id', $programIds));
            })->get();

            foreach ($subjects as $subject) {
                $enrollment = Enrollment::withTrashed()->firstOrNew([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                ]);

                if (! $enrollment->exists) {
                    $enrollment->enrolled_at = now();
                    $enrollment->status = 'active';
                } elseif ($enrollment->status === 'withdrawn') {
                    $enrollment->status = 'active';
                }

                $enrollment->deleted_at = null;
                $enrollment->save();
            }

            return [$programs->count(), $subjects->count()];
        });

        $this->command?->info("Student 303: {$counts[0]} diploma/course programs and {$counts[1]} subjects assigned.");
    }
}
