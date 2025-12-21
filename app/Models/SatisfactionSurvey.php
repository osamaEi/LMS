<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SatisfactionSurvey extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject_id',
        'course_id',
        'title',
        'description',
        'type',
        'status',
        'starts_at',
        'ends_at',
        'is_mandatory',
        'is_anonymous',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_mandatory' => 'boolean',
            'is_anonymous' => 'boolean',
        ];
    }

    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class, 'survey_id')->orderBy('order');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class, 'survey_id');
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->status === 'active' &&
               ($this->starts_at === null || $this->starts_at <= now()) &&
               ($this->ends_at === null || $this->ends_at >= now());
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed' || ($this->ends_at && $this->ends_at < now());
    }

    public function hasUserResponded(int $userId): bool
    {
        return $this->responses()->where('user_id', $userId)->exists();
    }

    public function getResponseCount(): int
    {
        return $this->responses()->distinct('user_id')->count('user_id');
    }

    public function getAverageRating(): float
    {
        $avg = $this->responses()->whereNotNull('rating')->avg('rating');
        return round($avg ?? 0, 2);
    }

    public function getRatingDistribution(): array
    {
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->responses()->where('rating', $i)->count();
        }
        return $distribution;
    }

    public function getCompletionRate(int $totalEligible): float
    {
        if ($totalEligible === 0) return 0;
        return round(($this->getResponseCount() / $totalEligible) * 100, 2);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where(function ($q) {
                         $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                     })
                     ->where(function ($q) {
                         $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
                     });
    }

    public function scopeForStudents($query)
    {
        return $query->where('type', 'student');
    }

    public function scopeForTeachers($query)
    {
        return $query->where('type', 'teacher');
    }
}
