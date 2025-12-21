<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyQuestion extends Model
{
    protected $fillable = [
        'survey_id',
        'question',
        'type',
        'options',
        'is_required',
        'requires_comment_on_low_rating',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'options' => 'array',
            'is_required' => 'boolean',
            'requires_comment_on_low_rating' => 'boolean',
        ];
    }

    // Relationships
    public function survey(): BelongsTo
    {
        return $this->belongsTo(SatisfactionSurvey::class, 'survey_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class, 'question_id');
    }

    // Helper Methods
    public function isRatingType(): bool
    {
        return $this->type === 'rating';
    }

    public function isTextType(): bool
    {
        return $this->type === 'text';
    }

    public function isMultipleChoice(): bool
    {
        return $this->type === 'multiple_choice';
    }

    public function getAverageRating(): float
    {
        if (!$this->isRatingType()) return 0;
        $avg = $this->responses()->whereNotNull('rating')->avg('rating');
        return round($avg ?? 0, 2);
    }

    public function getLowRatingCount(): int
    {
        return $this->responses()->where('rating', '<=', 2)->count();
    }
}
