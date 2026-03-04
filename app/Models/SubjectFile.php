<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SubjectFile extends Model
{
    protected $fillable = [
        'subject_id',
        'title',
        'file_path',
        'file_original_name',
        'file_size',
        'file_type',
        'description',
        'order',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function getUrl(): string
    {
        return Storage::url($this->file_path);
    }

    public function getFormattedSize(): string
    {
        if (!$this->file_size) return '';
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }
}
