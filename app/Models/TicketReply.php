<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReply extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'attachment',
        'is_internal_note_note',
    ];

    protected function casts(): array
    {
        return [
            'is_internal_note_note' => 'boolean',
        ];
    }

    // Relationships
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper Methods
    public function isFromStaff(): bool
    {
        return $this->user && ($this->user->isAdmin() || $this->user->isSuperAdmin());
    }

    public function isFromUser(): bool
    {
        return $this->user && $this->ticket && $this->user_id === $this->ticket->user_id;
    }

    public function hasAttachment(): bool
    {
        return !empty($this->attachment);
    }
}
