<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TermResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'term_number'         => $this->term_number,
            'name'                => $this->name,
            'name_ar'             => $this->name_ar,
            'name_en'             => $this->name_en,
            'status'              => $this->status,
            'start_date'          => $this->start_date?->format('Y-m-d'),
            'end_date'            => $this->end_date?->format('Y-m-d'),
            'subjects_count'      => $this->when(
                isset($this->subjects_count),
                $this->subjects_count,
                fn() => $this->whenLoaded('subjects', fn() => $this->subjects->count())
            ),
            // Injected via additional() in controller
            'completion_percentage'   => $this->when(isset($this->completion_percentage), $this->completion_percentage),
            'min_attendance_required' => $this->when(isset($this->min_attendance_required), $this->min_attendance_required),
        ];
    }
}
