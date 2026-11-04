<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class SystemIssueComment extends Model
{
    protected $fillable = [
        'system_issue_id',
        'user_id',
        'parent_id',
        'type',
        'body',
        'voice_path',
        'edited_at',
    ];

    protected function casts(): array
    {
        return [
            'edited_at' => 'datetime',
        ];
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(SystemIssue::class, 'system_issue_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('created_at');
    }

    public function isStatusChange(): bool
    {
        return $this->type === 'status';
    }

    public function voiceUrl(): ?string
    {
        return $this->voice_path ? Storage::disk('public')->url($this->voice_path) : null;
    }
}
