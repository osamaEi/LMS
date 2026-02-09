<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class XapiStatement extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'activity_log_id',
        'user_id',
        'verb_id',
        'verb_display',
        'object_type',
        'object_id',
        'object_name',
        'statement_json',
        'status',
        'retry_count',
        'error_message',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'statement_json' => 'array',
            'retry_count' => 'integer',
            'sent_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // ===========================
    // Relationships
    // ===========================

    public function activityLog(): BelongsTo
    {
        return $this->belongsTo(ActivityLog::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ===========================
    // Scopes
    // ===========================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeRetry($query)
    {
        return $query->where('status', 'retry')
            ->where('retry_count', '<', config('xapi.retry_max', 3));
    }

    // ===========================
    // Helper Methods
    // ===========================

    /**
     * Mark as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'error_message' => null,
        ]);

        // Mark activity log as xAPI sent
        if ($this->activityLog) {
            $this->activityLog->markAsXapiSent();
        }
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->increment('retry_count');

        $status = $this->retry_count >= config('xapi.retry_max', 3) ? 'failed' : 'retry';

        $this->update([
            'status' => $status,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Check if can retry
     */
    public function canRetry(): bool
    {
        return $this->retry_count < config('xapi.retry_max', 3);
    }
}
