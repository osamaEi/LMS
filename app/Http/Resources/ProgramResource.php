<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'name_ar'         => $this->name_ar,
            'name_en'         => $this->name_en,
            'description'     => $this->description,
            'description_ar'  => $this->description_ar,
            'description_en'  => $this->description_en,
            'code'            => $this->code,
            'duration_months' => $this->duration_months,
            'price'           => $this->price,
            'status'          => $this->status,
            'total_terms'     => $this->whenLoaded('terms', fn() => $this->terms->count()),
        ];
    }
}
