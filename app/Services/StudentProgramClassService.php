<?php

namespace App\Services;

use App\Models\ProgramClass;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentProgramClassService
{
    public function __construct(
        private readonly ProgramClassService $programClassService,
    ) {}

    public function assignStudents(ProgramClass $programClass, array $studentIds): int
    {
        return DB::transaction(function () use ($programClass, $studentIds) {
            $programClass = $this->programClassService->lock($programClass);
            $this->programClassService->assertActive($programClass);

            $studentIds = collect($studentIds)->map(fn ($id) => (int) $id)->unique()->values();
            $students = User::query()
                ->whereKey($studentIds)
                ->where('role', 'student')
                ->where(function ($query) use ($programClass) {
                    $query->where('program_id', $programClass->program_id)
                        ->orWhereHas('programs', fn ($programs) => $programs
                            ->where('programs.id', $programClass->program_id));
                })
                ->get();

            if ($students->count() !== $studentIds->count()) {
                throw ValidationException::withMessages([
                    'student_ids' => 'يجب أن يكون كل المستخدمين طلابًا مسجلين في برنامج هذه المجموعة.',
                ]);
            }

            $newStudentIds = $studentIds->diff($this->programClassService->studentIds($programClass));
            $this->programClassService->ensureCapacity($programClass, $newStudentIds->count());

            foreach ($students as $student) {
                $this->assignStudent($programClass, $student);
            }

            return $students->count();
        });
    }

    public function removeStudent(ProgramClass $programClass, User $student): void
    {
        DB::transaction(function () use ($programClass, $student) {
            DB::table('student_programs')
                ->where('student_id', $student->id)
                ->where('program_id', $programClass->program_id)
                ->where('class_id', $programClass->id)
                ->update(['class_id' => null, 'updated_at' => now()]);

            if ((int) $student->class_id === (int) $programClass->id) {
                $student->forceFill(['class_id' => null])->save();
            }
        });
    }

    private function assignStudent(ProgramClass $programClass, User $student): void
    {
        if ($student->programs()->where('programs.id', $programClass->program_id)->exists()) {
            $student->programs()->updateExistingPivot($programClass->program_id, [
                'class_id' => $programClass->id,
            ]);
        } else {
            $student->programs()->attach($programClass->program_id, [
                'class_id' => $programClass->id,
                'status' => $student->program_status ?? 'approved',
                'enrolled_at' => now(),
            ]);
        }

        if ((int) $student->program_id === (int) $programClass->program_id) {
            $student->forceFill(['class_id' => $programClass->id])->save();
        }
    }
}
