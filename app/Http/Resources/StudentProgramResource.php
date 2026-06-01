<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


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

        $isDiploma = $program->type === 'diploma';

        // Supervisor (diploma only)
        $supervisor = ($isDiploma && $program->relationLoaded('supervisor'))
            ? $program->supervisor
            : null;

        // Teachers from current term subjects (diploma)
        $termTeachers = collect();
        if ($isDiploma && $currentTerm && $currentTerm->relationLoaded('subjects')) {
            $termTeachers = $currentTerm->subjects
                ->filter(fn($s) => $s->teacher)
                ->map(fn($s) => $this->formatTeacher($s->teacher))
                ->unique('id')
                ->values();
        }

        // Teachers assigned to the program directly (course/training/english)
        $programTeachersRaw = $this->resource['program_teachers'] ?? collect();
        $programTeachers = collect($programTeachersRaw)
            ->map(fn($t) => $this->formatTeacher($t))
            ->values();

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
            'photo'        => $program->image ? basename($program->image) : null,
           // 'current_term' => $this->resource['current_term'] ?? null,
            'current_term_name' => $currentTerm
                ? ($isEn ? ($currentTerm->name_en ?: $currentTerm->name_ar) : $currentTerm->name_ar)
                : ($this->resource['current_term_name'] ?? null),
            'supervisor' => $isDiploma
                ? ($supervisor ? $this->formatTeacher($supervisor) : null)
                : $programTeachers->first(),
        ];
    }

    private function formatTeacher($teacher): array
    {
        return [
            'id'             => $teacher->id,
            'name'           => $teacher->name,
            'specialization' => $teacher->specialization ?? null,
            'photo'          => $teacher->profile_photo
                ? (filter_var($teacher->profile_photo, FILTER_VALIDATE_URL)
                    ? $teacher->profile_photo
                    : asset('storage/' . $teacher->profile_photo))
                : null,
        ];
    }
}
