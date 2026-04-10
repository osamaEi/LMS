<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class TermWithSubjectsResource extends JsonResource
{
    private Collection|array $enrollmentsMap = [];

    public function withEnrollmentsMap(Collection|array $map): static
    {
        $this->enrollmentsMap = $map;
        return $this;
    }

    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'term_number' => $this->term_number,
            'start_date'  => $this->start_date?->format('Y-m-d'),
            'end_date'    => $this->end_date?->format('Y-m-d'),
            'subjects'    => $this->whenLoaded('subjects', fn() =>
                $this->subjects->map(fn($subject) =>
                    (new SubjectWithProgressResource($subject))
                        ->additional(['enrollments' => $this->enrollmentsMap])
                        ->toArray($request)
                )->values()
            ),
        ];
    }
}
