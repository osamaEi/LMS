<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Term extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'program_id',
        'term_number',
        'name_ar',
        'name_en',
        'start_date',
        'end_date',
        'registration_start_date',
        'registration_end_date',
        'status',
    ];

    /**
     * Get the localized name
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? ($this->name_en ?: $this->name_ar) : $this->name_ar;
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'registration_start_date' => 'date',
            'registration_end_date' => 'date',
        ];
    }

    // Relationships
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function track(): BelongsTo
    {
        return $this->belongsTo(Track::class);
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasManyThrough(Enrollment::class, Subject::class);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isRegistrationOpen(): bool
    {
        $now = now();
        return $this->registration_start_date <= $now
            && $this->registration_end_date >= $now
            && $this->status === 'active';
    }

    public function hasStarted(): bool
    {
        return $this->start_date <= now();
    }

    public function hasEnded(): bool
    {
        return $this->end_date < now();
    }
}
