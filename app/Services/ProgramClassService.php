<?php

namespace App\Services;

use App\Models\ProgramClass;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProgramClassService
{
    public function lock(ProgramClass $programClass): ProgramClass
    {
        return ProgramClass::query()->lockForUpdate()->findOrFail($programClass->id);
    }

    public function assertActive(ProgramClass $programClass): void
    {
        if ($programClass->status !== 'active') {
            throw ValidationException::withMessages([
                'class_id' => 'لا يمكن إسناد طلاب إلى مجموعة غير نشطة.',
            ]);
        }
    }

    public function ensureCapacity(ProgramClass $programClass, int $newStudents): void
    {
        if ($programClass->max_students === null) {
            return;
        }

        $remaining = max(0, $programClass->max_students - $this->studentIds($programClass)->count());

        if ($newStudents > $remaining) {
            throw ValidationException::withMessages([
                'student_ids' => "السعة المتبقية للمجموعة {$remaining} طالب فقط.",
            ]);
        }
    }

    public function studentIds(ProgramClass $programClass): Collection
    {
        return DB::table('student_programs')
            ->where('class_id', $programClass->id)
            ->pluck('student_id')
            ->merge(User::where('class_id', $programClass->id)->pluck('id'))
            ->unique()
            ->values();
    }

    public function delete(ProgramClass $programClass): void
    {
        DB::transaction(function () use ($programClass) {
            $programClass = $this->lock($programClass);

            DB::table('student_programs')
                ->where('class_id', $programClass->id)
                ->update(['class_id' => null, 'updated_at' => now()]);

            User::where('class_id', $programClass->id)->update(['class_id' => null]);

            $programClass->delete();
        });
    }
}
