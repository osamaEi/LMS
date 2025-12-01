<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NafathTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'transaction_id',
        'national_id',
        'status',
        'request_payload',
        'response_payload',
        'polled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'request_payload' => 'array',
            'response_payload' => 'array',
            'polled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if transaction is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if transaction is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if transaction is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    /**
     * Mark as approved
     */
    public function markAsApproved(array $response = []): void
    {
        $this->update([
            'status' => 'approved',
            'response_payload' => $response,
            'completed_at' => now(),
        ]);
    }

    /**
     * Mark as rejected
     */
    public function markAsRejected(array $response = []): void
    {
        $this->update([
            'status' => 'rejected',
            'response_payload' => $response,
            'completed_at' => now(),
        ]);
    }

    /**
     * Update poll timestamp
     */
    public function updatePollTimestamp(): void
    {
        $this->update(['polled_at' => now()]);
    }
}
