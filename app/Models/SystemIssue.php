<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class SystemIssue extends Model
{
    use SoftDeletes;

    public const STATUSES = [
        'open'        => ['label' => 'مفتوحة',        'color' => '#2563eb', 'bg' => '#dbeafe'],
        'in_progress' => ['label' => 'قيد المعالجة',  'color' => '#d97706', 'bg' => '#fef3c7'],
        'waiting'     => ['label' => 'بانتظار رد',    'color' => '#7c3aed', 'bg' => '#ede9fe'],
        'resolved'    => ['label' => 'تم الحل',       'color' => '#16a34a', 'bg' => '#dcfce7'],
        'closed'      => ['label' => 'مغلقة',         'color' => '#64748b', 'bg' => '#f1f5f9'],
    ];

    public const PRIORITIES = [
        'low'    => ['label' => 'منخفضة', 'color' => '#64748b', 'bg' => '#f1f5f9'],
        'medium' => ['label' => 'متوسطة', 'color' => '#0891b2', 'bg' => '#cffafe'],
        'high'   => ['label' => 'مرتفعة', 'color' => '#ea580c', 'bg' => '#ffedd5'],
        'urgent' => ['label' => 'عاجلة',  'color' => '#dc2626', 'bg' => '#fee2e2'],
    ];

    protected $fillable = [
        'title',
        'description',
        'voice_path',
        'status',
        'priority',
        'created_by',
        'assigned_to',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'resolved_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function tags(): HasMany
    {
        return $this->hasMany(SystemIssueTag::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(SystemIssueComment::class)->orderBy('created_at');
    }

    /** Files attached to the issue itself (not to a comment). */
    public function attachments(): HasMany
    {
        return $this->hasMany(SystemIssueAttachment::class)->whereNull('comment_id')->orderBy('id');
    }

    /** Every file of the issue, including those on comments. */
    public function allAttachments(): HasMany
    {
        return $this->hasMany(SystemIssueAttachment::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status]['label'] ?? $this->status;
    }

    public function statusStyle(): string
    {
        $s = self::STATUSES[$this->status] ?? self::STATUSES['closed'];
        return "background:{$s['bg']};color:{$s['color']}";
    }

    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->priority]['label'] ?? $this->priority;
    }

    public function priorityStyle(): string
    {
        $p = self::PRIORITIES[$this->priority] ?? self::PRIORITIES['low'];
        return "background:{$p['bg']};color:{$p['color']}";
    }

    public function voiceUrl(): ?string
    {
        return $this->voice_path ? Storage::disk('public')->url($this->voice_path) : null;
    }

    /** Safe HTML of the description (rich-text, or legacy plain text). */
    public function descriptionHtml(): string
    {
        return self::renderDescription($this->description);
    }

    // ── Rich-text description (see App\Support\RichText) ─────────────

    /** Clean editor HTML for storage; null when the editor is empty. */
    public static function sanitizeDescription(?string $html): ?string
    {
        return \App\Support\RichText::sanitize($html);
    }

    /** Safe HTML for display, whatever was stored. */
    public static function renderDescription(?string $value): string
    {
        return \App\Support\RichText::render($value);
    }
}
