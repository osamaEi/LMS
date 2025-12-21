<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'assigned_to',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'satisfaction_rating',
    ];

    protected function casts(): array
    {
        return [
            'first_response_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-' . strtoupper(uniqid());
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class)->orderBy('created_at', 'asc');
    }

    // Helper Methods
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function markAsInProgress(): void
    {
        $this->update(['status' => 'in_progress']);
    }

    public function markAsResolved(): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    public function markAsClosed(): void
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
        ]);
    }

    public function recordFirstResponse(): void
    {
        if ($this->first_response_at === null) {
            $this->update(['first_response_at' => now()]);
        }
    }

    public function getResponseTime(): ?string
    {
        if ($this->first_response_at === null) return null;
        return $this->created_at->diffForHumans($this->first_response_at, true);
    }

    public function getResolutionTime(): ?string
    {
        if ($this->resolved_at === null) return null;
        return $this->created_at->diffForHumans($this->resolved_at, true);
    }

    public function getCategoryLabel(): string
    {
        return match($this->category) {
            'technical' => 'دعم تقني',
            'academic' => 'أكاديمي',
            'payment' => 'مالي',
            'account' => 'حساب',
            'other' => 'أخرى',
            default => 'غير محدد',
        };
    }

    public function getPriorityLabel(): string
    {
        return match($this->priority) {
            'urgent' => 'عاجل',
            'high' => 'مرتفع',
            'medium' => 'متوسط',
            'low' => 'منخفض',
            default => 'غير محدد',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'open' => 'مفتوح',
            'in_progress' => 'قيد المعالجة',
            'waiting_response' => 'بانتظار الرد',
            'resolved' => 'تم الحل',
            'closed' => 'مغلق',
            default => 'غير محدد',
        };
    }

    public function getPriorityColor(): string
    {
        return match($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'secondary',
            default => 'secondary',
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'open' => 'primary',
            'in_progress' => 'info',
            'waiting_response' => 'warning',
            'resolved' => 'success',
            'closed' => 'secondary',
            default => 'secondary',
        };
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting_response']);
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
