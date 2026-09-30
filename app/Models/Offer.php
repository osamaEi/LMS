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
        'video_url', 'video_path',
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

    /** Programs / courses the offer applies to. Empty = all programs. */
    public function programs()
    {
        return $this->belongsToMany(Program::class)->withTimestamps();
    }

    public function getAppliesToAllAttribute(): bool
    {
        return $this->programs->isEmpty();
    }

    /**
     * Cheapest original price among the offer's programs and its price after the offer,
     * or null when there is no priced program to compare against.
     */
    public function getPriceInfoAttribute(): ?array
    {
        $programs = $this->programs->filter(fn($p) => (float) $p->price > 0);
        if ($programs->isEmpty()) return null;

        $rows = $programs->map(fn($p) => ['orig' => (float) $p->price, 'new' => $this->getEffectivePriceForProgram($p)]);
        $best = $rows->sortBy('new')->first();

        return $best + ['from' => $programs->count() > 1];
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

    /** Values for rendering the discount headline: number, whether it is money, label, css type. */
    public function getDisplayAttribute(): array
    {
        return match ($this->discount_type) {
            'percentage' => ['num' => number_format($this->discount_value, 0), 'money' => false, 'label' => 'نسبة خصم', 'type' => 'pct'],
            'fixed'      => ['num' => number_format($this->discount_value, 0), 'money' => true,  'label' => 'خصم ثابت', 'type' => 'fix'],
            default      => ['num' => number_format($this->offer_price, 0),    'money' => true,  'label' => 'سعر العرض', 'type' => 'ovr'],
        };
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }

    public function getHasVideoAttribute(): bool
    {
        return !empty($this->video_path) || !empty($this->video_url);
    }

    /** Embeddable iframe URL for YouTube / Vimeo links, null otherwise. */
    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url || $this->video_path) return null;

        if (preg_match('~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $this->video_url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0';
        }

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $this->video_url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return null;
    }

    /** Direct video file URL (uploaded file or a direct .mp4/.webm link). */
    public function getVideoFileUrlAttribute(): ?string
    {
        if ($this->video_path) return asset('storage/' . $this->video_path);
        if ($this->video_url && !$this->video_embed_url) return $this->video_url;
        return null;
    }

    public function getUsesLeftAttribute(): ?int
    {
        if (is_null($this->max_uses)) return null;
        return max(0, $this->max_uses - $this->uses_count);
    }
}
