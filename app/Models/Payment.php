<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'program_id',
        'created_by',
        'total_amount',
        'paid_amount',
        'discount_amount',
        'remaining_amount',
        'payment_type',
        'payment_method',
        'status',
        'tamara_checkout_id',
        'tamara_order_id',
        'tamara_metadata',
        'paytabs_tran_ref',
        'paytabs_cart_id',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'tamara_metadata' => 'array',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function installments(): HasMany
    {
        return $this->hasMany(PaymentInstallment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    // Helper Methods
    public function updatePaidAmount(): void
    {
        $this->paid_amount = $this->transactions()
            ->where('status', 'success')
            ->where('type', 'payment')
            ->sum('amount');

        $this->updateRemainingAmount();
        $this->updateStatus();
        $this->save();
    }

    public function updateRemainingAmount(): void
    {
        $this->remaining_amount = $this->total_amount - $this->paid_amount - $this->discount_amount;
    }

    public function updateStatus(): void
    {
        if ($this->remaining_amount <= 0) {
            $this->status = 'completed';
            $this->completed_at = now();
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        }
    }

    public function isFullyPaid(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isPartial(): bool
    {
        return $this->status === 'partial';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isTamaraPayment(): bool
    {
        return $this->payment_method === 'tamara';
    }

    public function isInstallment(): bool
    {
        return $this->payment_type === 'installment';
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePartial($query)
    {
        return $query->where('status', 'partial');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForProgram($query, $programId)
    {
        return $query->where('program_id', $programId);
    }

    // Accessors
    public function getPaymentTypeDisplayNameAttribute(): string
    {
        return match($this->payment_type) {
            'full' => 'دفعة كاملة',
            'installment' => 'تقسيط',
            default => $this->payment_type,
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
            default => $this->payment_method ?? 'غير محدد',
        };
    }

    public function isPayTabsPayment(): bool
    {
        return $this->payment_method === 'paytabs';
    }

    public function getStatusDisplayNameAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد الانتظار',
            'partial' => 'مدفوع جزئياً',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد',
            default => $this->status,
        };
    }
}
