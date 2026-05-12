<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Program;

class Offer extends Model
{
    protected $fillable = [
        'title_ar', 'title_en',
        'description_ar', 'description_en',
        'code', 'discount_type', 'discount_value', 'offer_price',
        'program_id', 'start_date', 'end_date',
        'max_uses', 'uses_count', 'image',
        'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'start_date'     => 'date',
            'end_date'       => 'date',
            'discount_value' => 'decimal:2',
            'offer_price'    => 'decimal:2',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────────

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('start_date', '<=', now())
                     ->where('end_date', '>=', now());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', 'active')
                     ->where('start_date', '>', now());
    }

    // ── Accessors ──────────────────────────────────────────────────────────────

    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'en' ? ($this->title_en ?: $this->title_ar) : $this->title_ar;
    }

    public function getDescriptionAttribute(): ?string
    {
        return app()->getLocale() === 'en' ? ($this->description_en ?: $this->description_ar) : $this->description_ar;
    }

    public function getDiscountLabelAttribute(): string
    {
        if ($this->discount_type === 'override') {
            return number_format($this->offer_price, 0) . ' ر.س (سعر ثابت)';
        }
        return $this->discount_type === 'percentage'
            ? number_format($this->discount_value, 0) . '%'
            : number_format($this->discount_value, 0) . ' ر.س';
    }

    public function getEffectivePriceForProgram(Program $program): float
    {
        $original = (float) $program->price;

        if ($this->discount_type === 'override') {
            return max(0, (float) $this->offer_price);
        }

        if ($this->discount_type === 'percentage') {
            return max(0, $original - ($original * $this->discount_value / 100));
        }

        return max(0, $original - (float) $this->discount_value);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active'
            && $this->start_date->lte(now())
            && $this->end_date->gte(now());
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date->lt(now());
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date->gt(now());
    }

    public function getDaysLeftAttribute(): int
    {
        return max(0, (int) now()->diffInDays($this->end_date, false));
    }

    public function getUsesLeftAttribute(): ?int
    {
        if (is_null($this->max_uses)) return null;
        return max(0, $this->max_uses - $this->uses_count);
    }
}
