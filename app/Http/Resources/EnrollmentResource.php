<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'subject_id'      => $this->subject_id,
            'status'          => $this->status,
            'progress'        => (int) ($this->progress ?? 0),
            'final_grade'     => $this->final_grade,
            'grade_letter'    => $this->grade_letter,
            'enrolled_at'     => $this->enrolled_at?->toIso8601String(),
            'completion_date' => $this->completion_date?->format('Y-m-d'),
        ];
    }
}
