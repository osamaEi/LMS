<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MyProgramTermResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'term_number' => $this->term_number,
            'name'        => $this->name ?? ('الفصل ' . $this->term_number),
            'status'      => $this->status,
            'subjects'    => MyProgramSubjectResource::collection($this->whenLoaded('subjects')),
        ];
    }
}
