<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'session_id',
        'attended',
        'joined_at',
        'left_at',
        'duration_minutes',
        'watch_percentage',
        'video_completed',
        'participation_score',
        'notes',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'attended' => 'boolean',
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
            'duration_minutes' => 'integer',
            'watch_percentage' => 'decimal:2',
            'video_completed' => 'boolean',
            'participation_score' => 'decimal:2',
        ];
    }

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    // Helper Methods
    public function hasAttended(): bool
    {
        return $this->attended;
    }

    public function isVideoCompleted(): bool
    {
        return $this->video_completed;
    }

    public function markAsAttended(): void
    {
        $this->attended = true;
        $this->save();
    }

    public function updateWatchProgress(float $percentage): void
    {
        $this->watch_percentage = min(100, $percentage);

        if ($this->watch_percentage >= 100) {
            $this->video_completed = true;
            $this->attended = true;
        }

        $this->save();
    }

    public function recordJoin(?string $ipAddress = null, ?string $userAgent = null): void
    {
        $this->joined_at = now();
        $this->ip_address = $ipAddress ?? request()->ip();
        $this->user_agent = $userAgent ?? request()->userAgent();
        $this->save();
    }

    public function recordLeave(): void
    {
        $this->left_at = now();

        if ($this->joined_at) {
            $this->duration_minutes = $this->joined_at->diffInMinutes($this->left_at);
        }

        $this->save();
    }

    public function getAttendanceDuration(): ?int
    {
        if (!$this->joined_at || !$this->left_at) {
            return null;
        }

        return $this->joined_at->diffInMinutes($this->left_at);
    }

    public function getParticipationPercentage(): float
    {
        if (!$this->session) {
            return 0;
        }

        $sessionDuration = $this->session->duration_minutes ?? $this->session->getActualDuration();

        if (!$sessionDuration || !$this->duration_minutes) {
            return 0;
        }

        return min(100, round(($this->duration_minutes / $sessionDuration) * 100, 2));
    }

    public function isFullAttendance(): bool
    {
        if ($this->session->isRecordedVideo()) {
            return $this->video_completed;
        }

        return $this->attended && $this->getParticipationPercentage() >= 80;
    }
}
