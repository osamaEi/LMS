<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'type',
        'question_ar',
        'question_en',
        'explanation_ar',
        'explanation_en',
        'marks',
        'order',
        'image',
    ];

    protected $casts = [
        'marks' => 'decimal:2',
    ];

    /**
     * Get the question text based on current locale
     */
    public function getQuestionAttribute()
    {
        return app()->getLocale() === 'en' && $this->question_en ? $this->question_en : $this->question_ar;
    }

    /**
     * Get the explanation based on current locale
     */
    public function getExplanationAttribute()
    {
        return app()->getLocale() === 'en' && $this->explanation_en ? $this->explanation_en : $this->explanation_ar;
    }

    /**
     * Get the type label in Arabic
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'multiple_choice' => 'اختيار من متعدد',
            'true_false' => 'صح وخطأ',
            'short_answer' => 'إجابة قصيرة',
            'essay' => 'مقالي',
            default => $this->type,
        };
    }

    /**
     * Check if this question requires manual grading
     */
    public function requiresManualGrading()
    {
        return in_array($this->type, ['short_answer', 'essay']);
    }

    // Relationships

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    public function correctOptions()
    {
        return $this->options()->where('is_correct', true);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Get options shuffled if quiz setting is enabled
     */
    public function getShuffledOptions()
    {
        $options = $this->options;

        if ($this->quiz->shuffle_answers) {
            return $options->shuffle();
        }

        return $options;
    }

    /**
     * Check if the given option is correct
     */
    public function isOptionCorrect($optionId)
    {
        return $this->options()->where('id', $optionId)->where('is_correct', true)->exists();
    }
}
