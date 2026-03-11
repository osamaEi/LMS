<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'title_ar'     => $this->title_ar,
            'title_en'     => $this->title_en,
            'body'         => $this->body,
            'body_ar'      => $this->body_ar,
            'body_en'      => $this->body_en,
            'image_url'    => $this->image_url,
            'status'       => $this->status,
            'published_at' => $this->published_at?->toIso8601String(),
            'created_at'   => $this->created_at->toIso8601String(),
        ];
    }
}
