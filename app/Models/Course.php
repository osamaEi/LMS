<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'teacher_id',
        'image',
        'status',
        'max_students',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Relationships
     */
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
            ->withPivot('status', 'enrolled_at', 'completion_date', 'final_grade', 'grade_letter', 'progress')
            ->withTimestamps();
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get status display name in Arabic
     */
    public function getStatusDisplayAttribute(): string
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'active' => 'نشطة',
            'inactive' => 'غير نشطة',
            'archived' => 'مؤرشفة',
            default => 'غير محدد',
        };
    }
}
