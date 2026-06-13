<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyProgramSubjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'code'    => $this->code,
            'teacher' => $this->teacher ? [
                'id'             => $this->teacher->id,
                'name'           => $this->teacher->name,
                'specialization' => $this->teacher->specialization ?? null,
                'profile_photo'  => $this->teacher->profile_photo
                    ? asset('storage/' . $this->teacher->profile_photo)
                    : null,
            ] : null,
        ];
    }
}
