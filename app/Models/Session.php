<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Session extends Model
{
    use SoftDeletes;

    protected $table = 'class_sessions';

    protected $fillable = [
        'subject_id',
        'title',
        'description',
        'session_number',
        'type',
        'scheduled_at',
        'duration_minutes',
        'zoom_meeting_id',
        'zoom_start_url',
        'zoom_join_url',
        'zoom_password',
        'started_at',
        'ended_at',
        'video_path',
        'video_url',
        'video_platform',
        'video_duration',
        'video_size',
        'status',
        'is_mandatory',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'duration_minutes' => 'integer',
            'video_duration' => 'integer',
            'video_size' => 'integer',
            'is_mandatory' => 'boolean',
        ];
    }

    // Relationships
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // Helper Methods
    public function isLiveZoom(): bool
    {
        return $this->type === 'live_zoom';
    }

    public function isRecordedVideo(): bool
    {
        return $this->type === 'recorded_video';
    }

    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    public function isLive(): bool
    {
        return $this->status === 'live';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function isMandatory(): bool
    {
        return $this->is_mandatory;
    }

    public function start(): void
    {
        $this->started_at = now();
        $this->status = 'live';
        $this->save();
    }

    public function end(): void
    {
        $this->ended_at = now();
        $this->status = 'completed';
        $this->save();
    }

    public function cancel(): void
    {
        $this->status = 'cancelled';
        $this->save();
    }

    public function getActualDuration(): ?int
    {
        if (!$this->started_at || !$this->ended_at) {
            return null;
        }

        return $this->started_at->diffInMinutes($this->ended_at);
    }

    public function hasVideo(): bool
    {
        return !empty($this->video_path) || !empty($this->video_url);
    }

    public function getVideoUrl(): ?string
    {
        if ($this->video_url) {
            return $this->video_url;
        }

        if ($this->video_path && $this->video_platform === 'local') {
            return asset('storage/' . $this->video_path);
        }

        return null;
    }

    public function getAttendanceRate(): float
    {
        $totalEnrolled = $this->subject->enrollments()->where('status', 'active')->count();

        if ($totalEnrolled === 0) {
            return 0;
        }

        $attended = $this->attendances()->where('attended', true)->count();

        return round(($attended / $totalEnrolled) * 100, 2);
    }
}
