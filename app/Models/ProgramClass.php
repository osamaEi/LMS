<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramClass extends Model
{
    use SoftDeletes;

    protected $table = 'program_classes';

    protected $fillable = [
        'program_id',
        'name',
        'teacher_id',
        'supervisor_name',
        'start_date',
        'end_date',
        'status',
        'max_students',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'student_programs', 'class_id', 'student_id');
    }

    public function terms()
    {
        return $this->hasMany(Term::class, 'class_id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id');
    }

    public function getStudentsCountAttribute(): int
    {
        return $this->students()->count();
    }
}
