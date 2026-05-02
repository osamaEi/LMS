<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $fillable = [
        'question_ar',
        'question_en',
        'answer_ar',
        'answer_en',
        'category',
        'sort_order',
        'status',
    ];

    public function getQuestionAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? ($this->question_en ?: $this->question_ar) : $this->question_ar;
    }

    public function getAnswerAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? ($this->answer_en ?: $this->answer_ar) : $this->answer_ar;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public static function categories(): array
    {
        return [
            'registration' => ['label' => 'التسجيل والدفع',        'icon' => '💳'],
            'courses'      => ['label' => 'الدورات والدبلومات',     'icon' => '📚'],
            'certificates' => ['label' => 'الشهادات والاعتماد',     'icon' => '🎓'],
            'platform'     => ['label' => 'منصة الطالب',            'icon' => '💻'],
            'support'      => ['label' => 'الدعم التقني',           'icon' => '🛠️'],
        ];
    }
}
