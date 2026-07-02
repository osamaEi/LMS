<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Homework extends Model
{
    use HasFactory;

    protected $table = 'homeworks';

    protected $fillable = [
        'session_id',
        'subject_id',
        'program_id',
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

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function submissions()
    {
        return $this->hasMany(HomeworkSubmission::class);
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
