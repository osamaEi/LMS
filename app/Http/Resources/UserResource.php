<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'national_id' => $this->national_id,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'type' => $this->type,
            'program_id' => $this->program_id,
            'program' => $this->whenLoaded('program'),
            'date_of_register' => $this->date_of_register?->format('Y-m-d'),
            'is_terms' => $this->is_terms,
            'is_confirm_user' => $this->is_confirm_user,
            'role' => $this->role,
            'status' => $this->status,
            'profile_photo' => $this->profile_photo,
            'bio' => $this->bio,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'phone_verified_at' => $this->phone_verified_at?->toIso8601String(),
            'nafath_verified_at' => $this->nafath_verified_at?->toIso8601String(),
            'profile_completed_at' => $this->profile_completed_at?->toIso8601String(),
            'documents' => $this->whenLoaded('documents'),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
