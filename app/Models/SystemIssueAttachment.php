<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemIssueAttachment extends Model
{
    /** Files live on the private disk; they are only reachable through the admin route. */
    public const DISK = 'local';

    /** Shown inline in the browser. SVG is excluded on purpose (it can carry scripts). */
    private const INLINE_IMAGE = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
    private const INLINE_VIDEO = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'];

    protected $fillable = [
        'system_issue_id',
        'comment_id',
        'user_id',
        'path',
        'original_name',
        'mime',
        'size',
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(SystemIssue::class, 'system_issue_id');
    }

    public function comment(): BelongsTo
    {
        return $this->belongsTo(SystemIssueComment::class, 'comment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isImage(): bool
    {
        return in_array($this->mime, self::INLINE_IMAGE, true);
    }

    public function isVideo(): bool
    {
        return in_array($this->mime, self::INLINE_VIDEO, true);
    }

    public function isInline(): bool
    {
        return $this->isImage() || $this->isVideo();
    }

    public function url(bool $download = false): string
    {
        return route('admin.system-issues.attachments.show', [$this->system_issue_id, $this->id]) . ($download ? '?download=1' : '');
    }

    public function humanSize(): string
    {
        $b = (int) $this->size;
        return match (true) {
            $b >= 1073741824 => round($b / 1073741824, 1) . ' GB',
            $b >= 1048576    => round($b / 1048576, 1) . ' MB',
            $b >= 1024       => round($b / 1024) . ' KB',
            default          => $b . ' B',
        };
    }

    public function icon(): string
    {
        $ext = strtolower(pathinfo($this->original_name, PATHINFO_EXTENSION));
        return match (true) {
            $this->isImage() => '🖼',
            $this->isVideo() => '🎬',
            $ext === 'pdf' => '📕',
            in_array($ext, ['doc', 'docx', 'txt', 'rtf', 'odt']) => '📄',
            in_array($ext, ['xls', 'xlsx', 'csv', 'ods']) => '📊',
            in_array($ext, ['ppt', 'pptx', 'odp']) => '📽',
            in_array($ext, ['zip', 'rar', '7z']) => '🗜',
            str_starts_with((string) $this->mime, 'audio/') => '🎵',
            default => '📎',
        };
    }
}
