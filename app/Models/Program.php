<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'description_ar',
        'description_en',
        'duration_months',
        'price',
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

    /**
     * Get the localized description
     */
    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? ($this->description_en ?: $this->description_ar) : $this->description_ar;
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
