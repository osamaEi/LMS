<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class StudentProgramResource extends JsonResource
{
    /**
     * Keys in $this->resource (array):
     *   - status:          string  (no_program | pending | enrolled)
     *   - program:         Program model|null
     *   - enrollments:     Collection<Enrollment>
     *   - enrollments_map: Collection keyed by subject_id
     *   - statistics:      array
     */

    public function toArray(Request $request): array
    {
        $program = $this->resource['program'] ?? null;

        return [
            'status'       => $this->resource['status'],
            'name'         => $program?->name,
            'description'  => $program?->description ?? 'description',
            'period'       => $program?->duration_months,
            'current_term'      => $this->resource['current_term'] ?? null,
            'current_term_name' => $this->resource['current_term_name'] ?? null,
        ];
    }
}
