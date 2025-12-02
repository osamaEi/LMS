<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject_id',
        'student_id',
        'type',
        'title',
        'description',
        'total_score',
        'earned_score',
        'percentage',
        'weight',
        'due_date',
        'submitted_at',
        'graded_at',
        'graded_by',
        'feedback',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_score' => 'decimal:2',
            'earned_score' => 'decimal:2',
            'percentage' => 'decimal:2',
            'weight' => 'decimal:2',
            'due_date' => 'datetime',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
        ];
    }

    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function gradedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isGraded(): bool
    {
        return $this->status === 'graded';
    }

    public function isLate(): bool
    {
        return $this->status === 'late';
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date < now() && !$this->isGraded();
    }

    public function submit(): void
    {
        $this->submitted_at = now();
        $this->status = $this->isOverdue() ? 'late' : 'submitted';
        $this->save();
    }

    public function grade(float $earnedScore, ?string $feedback = null, ?int $gradedBy = null): void
    {
        $this->earned_score = min($earnedScore, $this->total_score);
        $this->percentage = ($this->earned_score / $this->total_score) * 100;
        $this->feedback = $feedback;
        $this->graded_by = $gradedBy ?? auth()->id();
        $this->graded_at = now();
        $this->status = 'graded';
        $this->save();
    }

    public function calculatePercentage(): float
    {
        if (!$this->total_score || $this->total_score == 0) {
            return 0;
        }

        return round(($this->earned_score / $this->total_score) * 100, 2);
    }

    public function getWeightedScore(): float
    {
        if (!$this->percentage || !$this->weight) {
            return 0;
        }

        return round(($this->percentage * $this->weight) / 100, 2);
    }

    public function hasPassed(): bool
    {
        return $this->isGraded() && $this->percentage >= 60;
    }

    public function getGradeLetter(): string
    {
        if (!$this->percentage) {
            return 'N/A';
        }

        $percentage = $this->percentage;

        if ($percentage >= 95) return 'A+';
        if ($percentage >= 90) return 'A';
        if ($percentage >= 85) return 'B+';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 75) return 'C+';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 65) return 'D+';
        if ($percentage >= 60) return 'D';

        return 'F';
    }

    public function isAssignment(): bool
    {
        return $this->type === 'assignment';
    }

    public function isQuiz(): bool
    {
        return $this->type === 'quiz';
    }

    public function isMidtermExam(): bool
    {
        return $this->type === 'midterm_exam';
    }

    public function isFinalExam(): bool
    {
        return $this->type === 'final_exam';
    }

    public function isProject(): bool
    {
        return $this->type === 'project';
    }

    public function isParticipation(): bool
    {
        return $this->type === 'participation';
    }
}
