<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'question'    => $this->question,
            'question_ar' => $this->question_ar,
            'question_en' => $this->question_en,
            'answer'      => $this->answer,
            'answer_ar'   => $this->answer_ar,
            'answer_en'   => $this->answer_en,
            'category'    => $this->category,
            'sort_order'  => $this->sort_order,
        ];
    }
}
