<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // A student may be enrolled in several programs, so collect them all:
        // the student_programs pivot rows, plus the legacy users.program_id if it
        // isn't already represented there.
        $enrolled = $this->programs->all();

        if ($this->program_id && !$this->programs->contains('id', $this->program_id) && $this->program) {
            array_unshift($enrolled, $this->program);
        }

        $programs = collect($enrolled)->map(function ($program) {
            // Each enrollment carries its own class. Prefer the pivot's class_id;
            // fall back to the legacy users.class_id only when it actually belongs
            // to this program (classIdForProgram enforces that).
            $classId      = $this->classIdForProgram((int) $program->id);
            $studentClass = $classId ? \App\Models\ProgramClass::find($classId) : null;

            $status = $program->pivot?->status
                ?? ($program->id === $this->program_id ? $this->program_status : null)
                ?? 'approved';

            $termNumber = $program->pivot?->current_term_number
                ?? ($program->id === $this->program_id ? $this->current_term_number : null)
                ?? 1;

            // Current term, scoped to the student's class when the program defines
            // class-specific terms (shared terms have a null class_id).
            $hasClassTerms = $classId
                ? \App\Models\Term::where('program_id', $program->id)
                    ->where('class_id', $classId)->exists()
                : false;

            $currentTerm = \App\Models\Term::where('program_id', $program->id)
                ->where('term_number', $termNumber)
                ->where(fn($q) => $hasClassTerms
                    ? $q->where('class_id', $classId)
                    : $q->whereNull('class_id'))
                ->first();

            return [
                'id'                  => $program->id,
                'name_ar'             => $program->name_ar,
                'name_en'             => $program->name_en,
                'type'                => $program->type,
                'enrollment_status'   => $status,
                'current_term_number' => $termNumber,
                'class'               => $studentClass ? [
                    'id'   => $studentClass->id,
                    'name' => $studentClass->name,
                ] : null,
                'current_term'        => $currentTerm ? [
                    'id'          => $currentTerm->id,
                    'term_number' => $currentTerm->term_number,
                    'name'        => $currentTerm->name ?? ('الفصل ' . $currentTerm->term_number),
                ] : null,
            ];
        })->values();

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

            // Every program the student is enrolled in, each with the class they sit
            // in for that program and their current term within it. Empty array when
            // the student has no enrollments.
            'programs'              => $programs,

            'created_at'            => $this->created_at->toIso8601String(),
        ];
    }
}
