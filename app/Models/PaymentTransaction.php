<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'payment_id',
        'installment_id',
        'amount',
        'type',
        'payment_method',
        'transaction_reference',
        'transaction_id',
        'response_code',
        'response_message',
        'status',
        'metadata',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relationships
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function installment(): BelongsTo
    {
        return $this->belongsTo(PaymentInstallment::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper Methods
    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    public function isPayment(): bool
    {
        return $this->type === 'payment';
    }

    public function isRefund(): bool
    {
        return $this->type === 'refund';
    }

    // Scopes
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePayments($query)
    {
        return $query->where('type', 'payment');
    }

    public function scopeRefunds($query)
    {
        return $query->where('type', 'refund');
    }

    // Accessors
    public function getTypeDisplayNameAttribute(): string
    {
        return match($this->type) {
            'payment' => 'دفعة',
            'refund' => 'استرداد',
            'adjustment' => 'تعديل',
            default => $this->type,
        };
    }

    public function getPaymentMethodDisplayNameAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'tamara' => 'تمارا',
            'paytabs' => 'بطاقة ائتمان (PayTabs)',
            'apple_pay' => 'Apple Pay',
            'waived' => 'معفى',
            default => $this->payment_method,
        };
    }

    public function getStatusDisplayNameAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'success' => 'ناجح',
            'failed' => 'فشل',
            'refunded' => 'مسترد',
            default => $this->status,
        };
    }
}
