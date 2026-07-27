<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyProgramResource extends JsonResource
{
  
    public function toArray(Request $request): array
    {
        $additional = $this->additional;
        $isDiploma  = $this->type === 'diploma';

        $result = [
            'id'              => (string) $this->id,
            'name_ar'         => $this->name_ar,
            'name_en'         => $this->name_en,
            'type'            => $this->type,
            // Drives the client's branch: diploma → terms/supervisor,
            // everything else → a flat teachers list.
            'is_diploma'      => $isDiploma,
            'description_ar'  => $this->description_ar ?? null,
            'description_en'  => $this->description_en ?? null,
            'image'           => $this->image ? asset('storage/' . $this->image) : null,
  
            'duration_hours'  => $isDiploma
                ? ($this->subjects_sum_credits !== null ? (int) $this->subjects_sum_credits : null)
                : ($this->duration_hours !== null ? (int) $this->duration_hours : null),
           
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
