<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A manual participation item recorded by a teacher for one student in one
 * subject — a free-text title ("key") plus a grade out of max_grade.
 */
class ParticipationMark extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'teacher_id',
        'title',
        'grade',
        'max_grade',
    ];

    protected $casts = [
        'grade'     => 'float',
        'max_grade' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
