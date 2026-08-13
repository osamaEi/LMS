<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enrollment extends Model
{
    use HasFactory, SoftDeletes;

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

    /**
     * The official grading scale, shared by every program. Keyed by the minimum
     * score out of 100 that earns the band, highest first.
     */
    public const GRADE_SCALE = [
        95 => ['A+', 'ممتاز مرتفع'],
        90 => ['A',  'ممتاز'],
        85 => ['B+', 'جيد جدًا مرتفع'],
        80 => ['B',  'جيد جدًا'],
        75 => ['C+', 'جيد مرتفع'],
        70 => ['C',  'جيد'],
        65 => ['D+', 'مقبول مرتفع'],
        60 => ['D',  'مقبول'],
    ];

    /**
     * Letter symbol and Arabic تقدير for any score out of 100 — usable for a
     * computed total, not only a saved enrollment.
     *
     * @return array{0: string, 1: string}
     */
    public static function gradeBand(?float $grade): array
    {
        if ($grade === null) {
            return ['—', '—'];
        }

        foreach (self::GRADE_SCALE as $min => $band) {
            if ($grade >= $min) return $band;
        }

        return ['F', 'راسب'];
    }

    public static function letterFor(?float $grade): string
    {
        return self::gradeBand($grade)[0];
    }

    public static function labelFor(?float $grade): string
    {
        return self::gradeBand($grade)[1];
    }

    public function calculateGradeLetter(): string
    {
        if (!$this->final_grade) {
            return 'N/A';
        }

        return self::letterFor((float) $this->final_grade);
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
