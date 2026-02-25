<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'body_ar',
        'body_en',
        'image',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getTitleAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? ($this->title_en ?: $this->title_ar) : $this->title_ar;
    }

    public function getBodyAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? ($this->body_en ?: $this->body_ar) : $this->body_ar;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
