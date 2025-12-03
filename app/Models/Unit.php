<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'unit_number',
        'duration_hours',
        'learning_objectives',
        'status',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'unit_number' => 'integer',
            'duration_hours' => 'integer',
            'order' => 'integer',
        ];
    }

    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class)->orderBy('session_number', 'asc');
    }

    public function files()
    {
        return $this->morphMany(\App\Models\File::class, 'fileable');
    }

    // Helper Methods
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function publish(): void
    {
        $this->status = 'published';
        $this->save();
    }

    public function archive(): void
    {
        $this->status = 'archived';
        $this->save();
    }

    public function getTotalSessions(): int
    {
        return $this->sessions()->count();
    }

    public function getCompletedSessionsCount(int $studentId): int
    {
        return $this->sessions()
            ->whereHas('attendances', function ($query) use ($studentId) {
                $query->where('student_id', $studentId)
                    ->where('attended', true);
            })
            ->count();
    }

    public function getCompletionPercentage(int $studentId): float
    {
        $total = $this->getTotalSessions();

        if ($total === 0) {
            return 0;
        }

        $completed = $this->getCompletedSessionsCount($studentId);

        return round(($completed / $total) * 100, 2);
    }

    public function hasVideoSessions(): bool
    {
        return $this->sessions()->where('type', 'recorded_video')->exists();
    }

    public function hasLiveSessions(): bool
    {
        return $this->sessions()->where('type', 'live_zoom')->exists();
    }
}
