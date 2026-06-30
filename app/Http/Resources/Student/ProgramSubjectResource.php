<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramSubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name_ar'          => $this->name_ar,
            'name_en'          => $this->name_en,
            'code'             => $this->code,
            'description_ar'   => $this->description_ar ?? null,
            'description_en'   => $this->description_en ?? null,
            'credits'          => $this->credits,
            'status'           => $this->status,
            'banner_photo'     => $this->banner_photo ? asset('storage/' . $this->banner_photo) : null,
            'teacher'          => $this->teacher ? [
                'id'            => $this->teacher->id,
                'name'          => $this->teacher->name,
                'profile_photo' => $this->teacher->profile_photo
                    ? asset('storage/' . $this->teacher->profile_photo)
                    : null,
            ] : null,
            'sessions_count'   => $this->sessions_count,
            'recordings_count' => $this->recordings_count,
            'is_enrolled'      => (bool) $this->is_enrolled,
        ];
    }
}
