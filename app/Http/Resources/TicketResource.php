<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'ticket_number'  => $this->ticket_number,
            'subject'        => $this->subject,

            'category'       => $this->category,
            'category_label' => $this->getCategoryLabel(),

            'priority'       => $this->priority,
            'priority_label' => $this->getPriorityLabel(),

            'status'         => $this->status,
            'status_label'   => $this->getStatusLabel(),

            'is_open'        => $this->isOpen(),
            'is_resolved'    => $this->isResolved(),
            'is_closed'      => $this->isClosed(),
            'can_reply'      => !$this->isClosed(),

            'satisfaction_rating' => $this->satisfaction_rating,

            'replies_count'  => $this->when(
                isset($this->replies_count),
                $this->replies_count
            ),

            'created_at'     => $this->created_at->toIso8601String(),
        ];
    }
}
