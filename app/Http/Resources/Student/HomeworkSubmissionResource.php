<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeworkSubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'content'      => $this->content,
            'file_url'     => $this->file_path
                ? (filter_var($this->file_path, FILTER_VALIDATE_URL)
                    ? $this->file_path
                    : asset('storage/' . $this->file_path))
                : null,
            'file_name'    => $this->file_name,
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'grade'        => $this->grade,
            'max_grade'    => $this->max_grade ?? 100,
            'feedback'     => $this->feedback,
        ];
    }
}
