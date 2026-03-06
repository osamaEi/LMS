<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    protected $table = 'homeworks';

    protected $fillable = [
        'session_id',
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'due_date',
        'file_path',
        'file_name',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function getTitleAttribute()
    {
        return $this->title_ar ?: $this->title_en;
    }

    public function getDescriptionAttribute()
    {
        return $this->description_ar ?: $this->description_en;
    }

    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }
}
