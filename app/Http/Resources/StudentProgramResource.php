<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class StudentProgramResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $program = $this->resource['program'] ?? null;

        if (!$program) {
            return ['status' => $this->resource['status']];
        }

        $locale      = app()->getLocale(); // set by SetLocale middleware
        $isEn        = $locale === 'en';

        $currentTerm = $this->resource['current_term_obj'] ?? null;

        // Supervisor
        $supervisor = $program->relationLoaded('supervisor') ? $program->supervisor : null;

        // Teachers from current term subjects
        $teachers = [];
        if ($currentTerm && $currentTerm->relationLoaded('subjects')) {
            $teachers = $currentTerm->subjects
                ->filter(fn($s) => $s->teacher)
                ->map(fn($s) => [
                    'id'             => $s->teacher->id,
                    'name'           => $s->teacher->name,
                    'specialization' => $s->teacher->specialization,
                    'photo'          => $s->teacher->profile_photo
                        ? Storage::url($s->teacher->profile_photo)
                        : null,
                ])
                ->unique('id')
                ->values();
        }

        // Type labels
        $typeLabels = [
            'diploma'     => $isEn ? 'Diploma'     : 'دبلوم',
            'training'    => $isEn ? 'Training'    : 'تدريب',
            'certificate' => $isEn ? 'Certificate' : 'شهادة',
        ];

        return [
            'status'       => $this->resource['status'],
            'name'         => $isEn ? ($program->name_en ?: $program->name_ar) : $program->name_ar,
            'description'  => ($isEn ? ($program->description_en ?: $program->description_ar) : $program->description_ar) ?? 'description',
            'period'       => $program->duration_months,
            'type'         => $program->type,
            'type_label'   => $typeLabels[$program->type] ?? null,
            'photo'        => $program->image ? Storage::url($program->image) : null,
           // 'current_term' => $this->resource['current_term'] ?? null,
            'current_term_name' => $currentTerm
                ? ($isEn ? ($currentTerm->name_en ?: $currentTerm->name_ar) : $currentTerm->name_ar)
                : ($this->resource['current_term_name'] ?? null),
            'supervisor'   => $supervisor ? [
                'name'           => $supervisor->name,
              
            ] : null,
           // 'teachers'     => $teachers,
        ];
    }
}
