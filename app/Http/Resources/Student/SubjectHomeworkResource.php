<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectHomeworkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $homework = $this->homework;

        return [
            'session_id'     => $this->id,
            'session_number' => $this->session_number,
            'session_title'  => $this->title_ar ?? $this->title ?? null,
            'id'             => $homework->id,
            'title'          => $homework->title,
            'description'    => $homework->description ?? null,
            'due_date'       => $homework->due_date?->format('Y-m-d'),
            'file'           => $homework->file_path ? [
                'name' => $homework->file_name,
                'url'  => asset('storage/' . $homework->file_path),
            ] : null,
        ];
    }
}
