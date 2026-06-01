<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Current term from the student's program
        $currentTerm = null;
        if ($this->relationLoaded('program') && $this->program) {
            $currentTerm = $this->program->terms
                ?->firstWhere('term_number', $this->current_term_number);
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
            'level'                 => $this->level ?? null,
            'role'                  => $this->role,
            'status'                => $this->status,
            'registration_number'   => $registrationNumber,

            // Photo — filename only
            'profile_photo' => $this->profile_photo ? basename($this->profile_photo) : null,

            // Academic background
            'specialization'        => $this->specialization ?? null,
            'specialization_type'   => $this->specialization_type ?? null,
            'date_of_graduation'    => $this->date_of_graduation?->format('Y-m-d') ?? null,

            // Program & enrollment
            'program_id'            => $this->program_id,
            'program_status'        => $this->program_status,
            'program'               => $this->whenLoaded('program', fn() => $this->program ? [
                'id'   => $this->program->id,
                'name' => $this->program->name_ar ?? $this->program->name,
                'name_en' => $this->program->name_en ?? null,
                'code' => $this->program->code ?? null,
                'type' => $this->program->type ?? null,
            ] : null),

            // Track / Group
            'track' => $this->whenLoaded('track', fn() => $this->track ? [
                'id'   => $this->track->id,
                'name' => $this->track->name,
                'code' => $this->track->code ?? null,
            ] : null),

            // Current term
            'current_term_number'   => $this->current_term_number,
            'current_term'          => $currentTerm ? [
                'id'         => $currentTerm->id,
                'name'       => $currentTerm->name ?? ('الفصل ' . $currentTerm->term_number),
                'term_number'=> $currentTerm->term_number,
                'start_date' => $currentTerm->start_date?->format('Y-m-d'),
                'end_date'   => $currentTerm->end_date?->format('Y-m-d'),
                'status'     => $currentTerm->status,
            ] : null,

            // Verification
            'is_confirm_user'       => $this->is_confirm_user,

            'date_of_register'      => $this->date_of_register?->format('Y-m-d'),
            'created_at'            => $this->created_at->toIso8601String(),
        ];
    }
}
