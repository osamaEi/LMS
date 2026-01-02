<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'created_by',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'type',
        'duration_minutes',
        'total_marks',
        'pass_marks',
        'max_attempts',
        'shuffle_questions',
        'shuffle_answers',
        'show_results',
        'show_correct_answers',
        'starts_at',
        'ends_at',
        'is_active',
    ];

    protected $casts = [
        'shuffle_questions' => 'boolean',
        'shuffle_answers' => 'boolean',
        'show_results' => 'boolean',
        'show_correct_answers' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'total_marks' => 'decimal:2',
        'pass_marks' => 'decimal:2',
    ];

    /**
     * Get the title based on current locale
     */
    public function getTitleAttribute()
    {
        return app()->getLocale() === 'en' && $this->title_en ? $this->title_en : $this->title_ar;
    }

    /**
     * Get the description based on current locale
     */
    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'en' && $this->description_en ? $this->description_en : $this->description_ar;
    }

    /**
     * Check if the quiz is currently available
     */
    public function isAvailable()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->starts_at && $now < $this->starts_at) {
            return false;
        }

        if ($this->ends_at && $now > $this->ends_at) {
            return false;
        }

        return true;
    }

    /**
     * Check if quiz has started
     */
    public function hasStarted()
    {
        return !$this->starts_at || now() >= $this->starts_at;
    }

    /**
     * Check if quiz has ended
     */
    public function hasEnded()
    {
        return $this->ends_at && now() > $this->ends_at;
    }

    /**
     * Get the type label in Arabic
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'quiz' => 'اختبار قصير',
            'exam' => 'امتحان',
            'homework' => 'واجب',
            default => $this->type,
        };
    }

    // Relationships

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get attempts for a specific student
     */
    public function attemptsForStudent($studentId)
    {
        return $this->attempts()->where('student_id', $studentId);
    }

    /**
     * Check if student can attempt the quiz
     */
    public function canStudentAttempt($studentId)
    {
        if (!$this->isAvailable()) {
            return false;
        }

        $attemptCount = $this->attemptsForStudent($studentId)
            ->whereNotNull('submitted_at')
            ->count();

        return $attemptCount < $this->max_attempts;
    }

    /**
     * Get remaining attempts for student
     */
    public function remainingAttempts($studentId)
    {
        $attemptCount = $this->attemptsForStudent($studentId)
            ->whereNotNull('submitted_at')
            ->count();

        return max(0, $this->max_attempts - $attemptCount);
    }

    /**
     * Get the best score for a student
     */
    public function bestScoreForStudent($studentId)
    {
        return $this->attemptsForStudent($studentId)
            ->whereNotNull('submitted_at')
            ->max('score');
    }
}
