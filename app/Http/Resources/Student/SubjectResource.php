<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name_ar'      => $this->name_ar,
            'name_en'      => $this->name_en,
            'code'         => $this->code,
            'banner_photo' => $this->banner_photo ? asset('storage/' . $this->banner_photo) : null,
            'teacher'      => $this->whenLoaded('teacher', fn() => $this->teacher ? [
                'id'            => $this->teacher->id,
                'name'          => $this->teacher->name,
                'profile_photo' => $this->teacher->profile_photo
                    ? asset('storage/' . $this->teacher->profile_photo)
                    : null,
            ] : null),
        ];
    }
}
