<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'session_id',
        'type',
        'title',
        'description',
        'order',
        'video_path',
        'video_url',
        'video_platform',
        'video_duration',
        'video_size',
        'file_path',
        'file_url',
        'file_size',
        'zoom_meeting_id',
        'zoom_start_url',
        'zoom_join_url',
        'zoom_password',
        'zoom_scheduled_at',
        'zoom_duration',
        'is_mandatory',
    ];

    protected function casts(): array
    {
        return [
            'video_duration' => 'integer',
            'video_size' => 'integer',
            'file_size' => 'integer',
            'zoom_duration' => 'integer',
            'zoom_scheduled_at' => 'datetime',
            'is_mandatory' => 'boolean',
            'order' => 'integer',
        ];
    }

    // Relationships
    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    // Helper Methods
    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isPDF(): bool
    {
        return $this->type === 'pdf';
    }

    public function isZoom(): bool
    {
        return $this->type === 'zoom';
    }

    public function getFileUrl(): ?string
    {
        if ($this->type === 'pdf') {
            if ($this->file_url) {
                return $this->file_url;
            }
            if ($this->file_path) {
                return asset('storage/' . $this->file_path);
            }
        }

        if ($this->type === 'video') {
            if ($this->video_url) {
                return $this->video_url;
            }
            if ($this->video_path) {
                return asset('storage/' . $this->video_path);
            }
        }

        return null;
    }

    public function getFormattedSize(): string
    {
        $size = $this->type === 'pdf' ? $this->file_size : $this->video_size;

        if (!$size) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    public function getDurationFormatted(): ?string
    {
        $duration = $this->type === 'video' ? $this->video_duration : $this->zoom_duration;

        if (!$duration) {
            return null;
        }

        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d ساعة', $hours, $minutes);
        }

        return $minutes . ' دقيقة';
    }
}
