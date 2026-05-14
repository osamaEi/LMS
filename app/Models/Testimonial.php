<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'author_ar', 'author_en',
        'role_ar', 'role_en',
        'text_ar', 'text_en',
        'rating', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'rating'    => 'integer',
        'sort_order'=> 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function getAuthorAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->author_ar : ($this->author_en ?: $this->author_ar);
    }

    public function getRoleAttribute(): string
    {
        return app()->getLocale() === 'ar' ? ($this->role_ar ?? '') : ($this->role_en ?: ($this->role_ar ?? ''));
    }

    public function getTextAttribute(): string
    {
        return app()->getLocale() === 'ar' ? $this->text_ar : ($this->text_en ?: $this->text_ar);
    }
}
