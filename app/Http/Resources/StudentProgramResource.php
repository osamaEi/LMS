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

    private static array $emptyStatistics = [
        'total_subjects'      => 0,
        'total_sessions'      => 0,
        'completed_sessions'  => 0,
        'attendance_rate'     => 0,
        'current_term'        => 0,
        'total_terms'         => 0,
        'progress_percentage' => 0,
    ];

    public function toArray(Request $request): array
    {
        $program        = $this->resource['program'] ?? null;
        $enrollments    = $this->resource['enrollments'] ?? collect();
        $enrollmentsMap = $this->resource['enrollments_map'] ?? collect();

        return [
            'status'      => $this->resource['status'],
            'program'     => $program ? [
                'id'          => $program->id,
                'name'        => $program->name,
                'description' => $program->description,
                'price'       => (float) $program->price,
            ] : null,
            'track'       => null,
            'terms'       => $program && $program->relationLoaded('terms')
                ? $program->terms
                    ->sortBy('term_number')
                    ->map(fn($term) =>
                        (new TermWithSubjectsResource($term))
                            ->withEnrollmentsMap($enrollmentsMap)
                            ->toArray($request)
                    )->values()
                : [],
            'enrollments' => EnrollmentResource::collection($enrollments),
            'statistics'  => $this->resource['statistics'] ?? self::$emptyStatistics,
        ];
    }
}
