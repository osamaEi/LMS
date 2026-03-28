<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsappConversation extends Model
{
    protected $fillable = [
        'wa_id',
        'channel',
        'user_id',
        'customer_name',
        'phone_number',
        'status',
        'last_message_at',
        'unread_admin_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'conversation_id')->orderBy('id');
    }

    public function latestMessage(): HasMany
    {
        return $this->hasMany(WhatsappMessage::class, 'conversation_id')->latest('id')->limit(1);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeWithUnread($query)
    {
        return $query->where('unread_admin_count', '>', 0);
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isAiResponding(): bool
    {
        return $this->status === 'ai_responding';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'open'          => 'مفتوح',
            'closed'        => 'مغلق',
            'ai_responding' => 'الذكاء الاصطناعي يرد',
            default         => 'غير محدد',
        };
    }

    public function getStatusColorClassAttribute(): string
    {
        return match ($this->status) {
            'open'          => 'bg-blue-100 text-blue-700',
            'closed'        => 'bg-gray-100 text-gray-600',
            'ai_responding' => 'bg-purple-100 text-purple-700',
            default         => 'bg-gray-100 text-gray-600',
        };
    }

    public function isWebChat(): bool
    {
        return $this->channel === 'web';
    }

    public function isWhatsAppChat(): bool
    {
        return $this->channel === 'whatsapp';
    }

    public function getChannelLabelAttribute(): string
    {
        return $this->channel === 'whatsapp' ? '📱 واتساب' : '🌐 موقع';
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->customer_name) {
            return $this->customer_name;
        }
        if ($this->user) {
            return $this->user->name;
        }
        return $this->phone_number ?? 'زائر';
    }
}
