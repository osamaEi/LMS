<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyResponse extends Model
{
    protected $fillable = [
        'survey_id',
        'user_id',
        'question_id',
        'rating',
        'answer',
        'selected_option',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    // Relationships
    public function survey(): BelongsTo
    {
        return $this->belongsTo(SatisfactionSurvey::class, 'survey_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }

    // Helper Methods
    public function isLowRating(): bool
    {
        return $this->rating !== null && $this->rating <= 2;
    }

    public function hasComment(): bool
    {
        return !empty($this->answer);
    }

    public function requiresComment(): bool
    {
        return $this->isLowRating() &&
               $this->question &&
               $this->question->requires_comment_on_low_rating;
    }

    public function getRatingLabel(): string
    {
        return match($this->rating) {
            5 => 'ممتاز',
            4 => 'جيد جداً',
            3 => 'جيد',
            2 => 'مقبول',
            1 => 'ضعيف',
            default => 'غير محدد',
        };
    }
}
