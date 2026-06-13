<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyProgramResource extends JsonResource
{
    // Pass pivot + computed data via ->additional([...])
    // Required keys: pivot_status, pivot_term_number, pivot_enrolled_at
    // Optional keys: current_term (Term model), supervisor (User model), teachers (Collection)
    public function toArray(Request $request): array
    {
        $additional = $this->additional;

        $result = [
            'id'              => $this->id,
            'name_ar'         => $this->name_ar,
            'name_en'         => $this->name_en,
            'type'            => $this->type,
            'course_type'     => $this->course_type ?? null,
            'description_ar'  => $this->description_ar ?? null,
            'description_en'  => $this->description_en ?? null,
            'image'           => $this->image ? asset('storage/' . $this->image) : null,
            'duration_months' => $this->duration_months ?? null,
            'duration_hours'  => $this->duration_hours ?? null,
            'status'          => $this->status,

            'enrollment_status'   => $additional['pivot_status'],
            'current_term_number' => $additional['pivot_term_number'],
            'enrolled_at'         => $additional['pivot_enrolled_at'],
        ];

        // Diploma: current_term + optional supervisor
        if (isset($additional['current_term']) && $additional['current_term']) {
            $term = $additional['current_term'];
            $result['current_term'] = (new MyProgramTermResource($term))
                ->additional([])
                ->resolve();

            if (isset($additional['supervisor']) && $additional['supervisor']) {
                $result['supervisor'] = [
                    'id'   => $additional['supervisor']->id,
                    'name' => $additional['supervisor']->name,
                ];
            }
        }

        // Course/training/english: teachers list
        if (isset($additional['teachers']) && $additional['teachers']->isNotEmpty()) {
            $result['teachers'] = $additional['teachers']->map(fn($t) => [
                'id'            => $t->id,
                'name'          => $t->name,
                'profile_photo' => $t->profile_photo
                    ? asset('storage/' . $t->profile_photo)
                    : null,
            ])->values();
        }

        return $result;
    }
}
