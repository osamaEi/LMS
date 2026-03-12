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
            'description'    => $this->description,
            'attachment_url' => $this->attachment
                ? asset('storage/' . $this->attachment)
                : null,

            // Category
            'category'       => $this->category,
            'category_label' => $this->getCategoryLabel(),

            // Priority
            'priority'       => $this->priority,
            'priority_label' => $this->getPriorityLabel(),

            // Status
            'status'         => $this->status,
            'status_label'   => $this->getStatusLabel(),

            // Flags
            'is_open'        => $this->isOpen(),
            'is_resolved'    => $this->isResolved(),
            'is_closed'      => $this->isClosed(),
            'can_reply'      => !$this->isClosed(),

            // Satisfaction
            'satisfaction_rating' => $this->satisfaction_rating,

            // Dates
            'first_response_at' => $this->first_response_at?->toIso8601String(),
            'resolved_at'       => $this->resolved_at?->toIso8601String(),
            'closed_at'         => $this->closed_at?->toIso8601String(),
            'created_at'        => $this->created_at->toIso8601String(),
            'updated_at'        => $this->updated_at->toIso8601String(),

            // Counts
            'replies_count'  => $this->when(
                isset($this->replies_count),
                $this->replies_count
            ),

            // Replies (only loaded in show)
            'replies'        => TicketReplyResource::collection(
                $this->whenLoaded('replies')
            ),
        ];
    }
}
