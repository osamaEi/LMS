<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'student_id',
        'subject_id',
        'enrolled_at',
        'status',
        'final_grade',
        'grade_letter',
        'completion_date',
        'progress',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'completion_date' => 'date',
            'final_grade' => 'decimal:2',
        ];
    }

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isWithdrawn(): bool
    {
        return $this->status === 'withdrawn';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function hasPassed(): bool
    {
        return $this->isCompleted() && $this->final_grade >= 60;
    }

    public function calculateGradeLetter(): string
    {
        if (!$this->final_grade) {
            return 'N/A';
        }

        $grade = $this->final_grade;

        if ($grade >= 95) return 'A+';
        if ($grade >= 90) return 'A';
        if ($grade >= 85) return 'B+';
        if ($grade >= 80) return 'B';
        if ($grade >= 75) return 'C+';
        if ($grade >= 70) return 'C';
        if ($grade >= 65) return 'D+';
        if ($grade >= 60) return 'D';

        return 'F';
    }

    public function updateFinalGrade(float $grade): void
    {
        $this->final_grade = $grade;
        $this->grade_letter = $this->calculateGradeLetter();
        $this->save();
    }

    public function complete(): void
    {
        $this->status = 'completed';
        $this->completion_date = now();
        $this->save();
    }
}
