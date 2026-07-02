<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectHomeworkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'description'    => $this->description ?? null,
            'due_date'       => $this->due_date?->format('Y-m-d'),
            'file'           => $this->file_path ? [
                'name' => $this->file_name,
                'url'  => asset('storage/' . $this->file_path),
            ] : null,
        ];
    }
}
