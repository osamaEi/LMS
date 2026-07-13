<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Resolve the student's class, then the program that class belongs to.
        // The class is the anchor — a student may sit in a class without a matching
        // student_programs row — so prefer the pivot's class, then the legacy
        // users.class_id, and only fall back to the student's own program_id.
        $pivotProgram = $this->programs
            ->first(fn($p) => $p->pivot?->class_id) ?? $this->programs->first();

        $classId      = $pivotProgram?->pivot?->class_id ?? $this->class_id;
        $studentClass = $classId ? \App\Models\ProgramClass::find($classId) : null;

        $program = $studentClass?->program ?? $pivotProgram ?? $this->program;

        $enrollmentStatus  = $pivotProgram?->pivot?->status ?? $this->program_status ?? null;
        $currentTermNumber = $pivotProgram?->pivot?->current_term_number
            ?? $this->current_term_number
            ?? 1;

        // Current term within that program, scoped to the student's class when the
        // program defines class-specific terms (shared terms have a null class_id).
        $currentTerm = null;
        if ($program) {
            $hasClassTerms = $classId
                ? \App\Models\Term::where('program_id', $program->id)
                    ->where('class_id', $classId)->exists()
                : false;

            $currentTerm = \App\Models\Term::where('program_id', $program->id)
                ->where('term_number', $currentTermNumber)
                ->where(fn($q) => $hasClassTerms
                    ? $q->where('class_id', $classId)
                    : $q->whereNull('class_id'))
                ->first();
        }

        // Auto-generate registration number if none stored: ST-YYYY-{id}
        $registrationNumber = 'ST-' . ($this->created_at?->format('Y') ?? now()->year) . '-' . $this->id;

        return [
            // Identity
            'id'                    => $this->id,
            'name'                  => $this->name,
            'email'                 => $this->email,
            'phone'                 => $this->phone,
            'national_id'           => $this->national_id,
            'student_code'          => $this->student_code ?? null,
            'gender'                => $this->gender,
            'date_of_birth'         => $this->date_of_birth?->format('Y-m-d'),
            'nationality'           => $this->nationality ?? null,
            'role'                  => $this->role,
            'status'                => $this->status,
            'registration_number'   => $registrationNumber,

            // Photo — filename only
            'profile_photo' => $this->profile_photo ? basename($this->profile_photo) : null,

            // Academic background
            'specialization'        => $this->specialization ?? null,
            'specialization_type'   => $this->specialization_type ?? null,
            'date_of_graduation'    => $this->date_of_graduation?->format('Y-m-d') ?? null,

            // The class the student belongs to, and the program that class belongs to.
            // The class is the anchor: a student may sit in a class without a matching
            // row in student_programs, so resolve the program from the class first and
            // only fall back to the student's own program_id.
            'class'                 => $studentClass ? [
                'id'   => $studentClass->id,
                'name' => $studentClass->name,
            ] : null,

            'program'               => $program ? [
                'id'                  => $program->id,
                'name_ar'             => $program->name_ar,
                'name_en'             => $program->name_en,
                'type'                => $program->type,
                'enrollment_status'   => $enrollmentStatus,
                'current_term_number' => $currentTermNumber,
                'current_term'        => $currentTerm ? [
                    'id'          => $currentTerm->id,
                    'term_number' => $currentTerm->term_number,
                    'name'        => $currentTerm->name ?? ('الفصل ' . $currentTerm->term_number),
                ] : null,
            ] : null,

            'created_at'            => $this->created_at->toIso8601String(),
        ];
    }
}
