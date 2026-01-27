<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentInstallment extends Model
{
    protected $fillable = [
        'payment_id',
        'installment_number',
        'amount',
        'due_date',
        'status',
        'payment_method',
        'transaction_reference',
        'paid_at',
        'paid_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // Helper Methods
    public function markAsPaid(string $method, ?string $reference, ?int $adminId): void
    {
        $this->status = 'paid';
        $this->payment_method = $method;
        $this->transaction_reference = $reference;
        $this->paid_at = now();
        $this->paid_by = $adminId;
        $this->save();

        // Update parent payment
        $this->payment->updatePaidAmount();
    }

    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->due_date < now();
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    // Scopes
    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                     ->where('due_date', '<', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeDueSoon($query, int $days = 3)
    {
        return $query->where('status', 'pending')
                     ->whereBetween('due_date', [now(), now()->addDays($days)]);
    }

    // Accessors
    public function getStatusDisplayNameAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'paid' => 'مدفوع',
            'overdue' => 'متأخر',
            'cancelled' => 'ملغي',
            default => $this->status,
        };
    }

    public function getPaymentMethodDisplayNameAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'tamara' => 'تمارا',
            'waived' => 'معفى',
            default => $this->payment_method ?? 'غير محدد',
        };
    }
}
