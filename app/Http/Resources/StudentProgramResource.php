<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentProgramResource extends JsonResource
{
    /**
     * Expected keys in $this->resource (array):
     *   - program:         Program model (terms relation loaded with subjects)
     *   - enrollments:     Collection of Enrollment models (flat)
     *   - enrollments_map: Collection keyed by subject_id
     *   - statistics:      array
     */
    public function toArray(Request $request): array
    {
        $program        = $this->resource['program'];
        $enrollmentsMap = $this->resource['enrollments_map'];

        return [
            'status'      => 'enrolled',
            'program'     => [
                'id'          => $program->id,
                'name'        => $program->name,
                'description' => $program->description,
                'price'       => (float) $program->price,
            ],
            'track'       => null,
            'terms'       => $program->terms
                ->sortBy('term_number')
                ->map(fn($term) =>
                    (new TermWithSubjectsResource($term))
                        ->withEnrollmentsMap($enrollmentsMap)
                        ->toArray($request)
                )->values(),
            'enrollments' => EnrollmentResource::collection($this->resource['enrollments']),
            'statistics'  => $this->resource['statistics'],
        ];
    }
}
