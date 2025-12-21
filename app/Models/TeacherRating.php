<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherRating extends Model
{
    protected $fillable = [
        'teacher_id',
        'student_id',
        'subject_id',
        'teaching_quality',
        'communication',
        'punctuality',
        'content_delivery',
        'overall_rating',
        'comment',
        'improvement_suggestions',
        'is_anonymous',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'teaching_quality' => 'integer',
            'communication' => 'integer',
            'punctuality' => 'integer',
            'content_delivery' => 'integer',
            'overall_rating' => 'integer',
            'is_anonymous' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    // Relationships
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    // Helper Methods
    public function getAverageRating(): float
    {
        $ratings = [
            $this->teaching_quality,
            $this->communication,
            $this->punctuality,
            $this->content_delivery,
            $this->overall_rating,
        ];
        return round(array_sum($ratings) / count($ratings), 2);
    }

    public function getRatingLabel(int $rating): string
    {
        return match($rating) {
            5 => 'ممتاز',
            4 => 'جيد جداً',
            3 => 'جيد',
            2 => 'مقبول',
            1 => 'ضعيف',
            default => 'غير محدد',
        };
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
    }

    public function scopeForTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    // Static Methods
    public static function getTeacherAverageRating(int $teacherId): float
    {
        $avg = self::where('teacher_id', $teacherId)
                   ->where('is_approved', true)
                   ->avg('overall_rating');
        return round($avg ?? 0, 2);
    }

    public static function getTeacherRatingsBreakdown(int $teacherId): array
    {
        $ratings = self::where('teacher_id', $teacherId)
                       ->where('is_approved', true)
                       ->get();

        if ($ratings->isEmpty()) {
            return [
                'teaching_quality' => 0,
                'communication' => 0,
                'punctuality' => 0,
                'content_delivery' => 0,
                'overall' => 0,
                'total_ratings' => 0,
            ];
        }

        return [
            'teaching_quality' => round($ratings->avg('teaching_quality'), 2),
            'communication' => round($ratings->avg('communication'), 2),
            'punctuality' => round($ratings->avg('punctuality'), 2),
            'content_delivery' => round($ratings->avg('content_delivery'), 2),
            'overall' => round($ratings->avg('overall_rating'), 2),
            'total_ratings' => $ratings->count(),
        ];
    }
}
