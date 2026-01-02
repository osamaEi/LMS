<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'student_id',
        'started_at',
        'submitted_at',
        'score',
        'percentage',
        'passed',
        'time_spent_seconds',
        'ip_address',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'passed' => 'boolean',
    ];

    /**
     * Check if attempt is completed
     */
    public function isCompleted()
    {
        return $this->submitted_at !== null;
    }

    /**
     * Check if attempt is in progress
     */
    public function isInProgress()
    {
        return $this->started_at && !$this->submitted_at;
    }

    /**
     * Get formatted time spent
     */
    public function getFormattedTimeSpentAttribute()
    {
        if (!$this->time_spent_seconds) {
            return '-';
        }

        $minutes = floor($this->time_spent_seconds / 60);
        $seconds = $this->time_spent_seconds % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Get remaining time in seconds
     */
    public function getRemainingTimeAttribute()
    {
        if (!$this->quiz->duration_minutes || !$this->started_at) {
            return null;
        }

        $endTime = $this->started_at->addMinutes($this->quiz->duration_minutes);
        $remaining = $endTime->diffInSeconds(now(), false);

        return max(0, -$remaining);
    }

    /**
     * Check if time has expired
     */
    public function hasTimeExpired()
    {
        if (!$this->quiz->duration_minutes || !$this->started_at) {
            return false;
        }

        $endTime = $this->started_at->addMinutes($this->quiz->duration_minutes);
        return now() > $endTime;
    }

    /**
     * Calculate and update the score
     */
    public function calculateScore()
    {
        $totalMarks = 0;
        $obtainedMarks = 0;

        foreach ($this->answers as $answer) {
            $totalMarks += $answer->question->marks;
            $obtainedMarks += $answer->marks_obtained ?? 0;
        }

        $this->score = $obtainedMarks;
        $this->percentage = $totalMarks > 0 ? ($obtainedMarks / $totalMarks) * 100 : 0;
        $this->passed = $this->score >= $this->quiz->pass_marks;
        $this->save();

        return $this;
    }

    // Relationships

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class, 'attempt_id');
    }

    /**
     * Get answer for a specific question
     */
    public function answerForQuestion($questionId)
    {
        return $this->answers()->where('question_id', $questionId)->first();
    }

    /**
     * Get unanswered questions count
     */
    public function getUnansweredCountAttribute()
    {
        $answeredCount = $this->answers()->count();
        $totalQuestions = $this->quiz->questions()->count();

        return $totalQuestions - $answeredCount;
    }

    /**
     * Check if all questions requiring manual grading have been graded
     */
    public function isFullyGraded()
    {
        return $this->answers()
            ->whereHas('question', function($q) {
                $q->whereIn('type', ['short_answer', 'essay']);
            })
            ->whereNull('marks_obtained')
            ->count() === 0;
    }
}
