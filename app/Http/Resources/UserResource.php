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
            'id'                  => $this->id,
            'name'                => $this->name,
            'email'               => $this->email,
            'phone'               => $this->phone,
            'national_id'         => $this->national_id,
            'gender'              => $this->gender,
            'date_of_birth'       => $this->date_of_birth?->format('Y-m-d'),
          //  'bio'                 => $this->bio,
            'role'                => $this->role,
            'status'              => $this->status,
            'registration_number' => $registrationNumber,

            // Profile photo — full URL
            'profile_photo'       => $this->profile_photo
                ? asset('storage/' . $this->profile_photo)
                : asset('images/placeholder-avatar.png'),

            // // Program & term
            // 'program_id'     => $this->program_id,
            // 'program_status' => $this->program_status,
            // 'program'        => $this->whenLoaded('program', fn() => $this->program ? [
            //     'id'   => $this->program->id,
            //     'name' => $this->program->name,
            //     'code' => $this->program->code,
            // ] : null),

            // Track / Group
            // 'track' => $this->whenLoaded('track', fn() => $this->track ? [
            //     'id'   => $this->track->id,
            //     'name' => $this->track->name,
            //     'code' => $this->track->code ?? null,
            // ] : null),

            // Current term
            // 'current_term_number' => $this->current_term_number,
            // 'current_term'        => $currentTerm ? [
            //     'id'       => $currentTerm->id,
            //     'name'     => $currentTerm->name,
            //     'end_date' => $currentTerm->end_date?->format('Y-m-d'),
            //     'status'   => $currentTerm->status,
            // ] : null,

            // Verification flags
            'is_confirm_user'    => $this->is_confirm_user,
            // 'email_verified_at'  => $this->email_verified_at?->toIso8601String(),
            // 'nafath_verified_at' => $this->nafath_verified_at?->toIso8601String(),

            'date_of_register'   => $this->date_of_register?->format('Y-m-d'),
            'created_at'         => $this->created_at->toIso8601String(),
        ];
    }
}
