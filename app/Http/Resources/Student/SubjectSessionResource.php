<?php

namespace App\Http\Resources\Student;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubjectSessionResource extends JsonResource
{
    // Pass attendances keyed by session_id via ->additional(['attendances' => $attendances])
    public function toArray(Request $request): array
    {
        $attendance = $this->additional['attendances'][$this->id] ?? null;

        $status = match (true) {
            !is_null($this->ended_at)   => 'ended',
            !is_null($this->started_at) => 'live',
            default                     => 'upcoming',
        };

        return [
            'id'               => $this->id,
            'title'            => $this->title_ar ?? $this->title ?? '',
            'title_en'         => $this->title_en ?? '',
            'type'             => $this->type,
            'session_number'   => $this->session_number,
            'scheduled_at'     => $this->scheduled_at,
            'status'           => $status,
            'join_url'         =>  $this->zoom_join_url ?? '',
    
        
        
        ];
    }
}
