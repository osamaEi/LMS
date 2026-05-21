<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'code',
        'description_ar',
        'description_en',
        'duration_months',
        'duration_hours',
        'price',
        'status',
        'image',
        'type',
        'course_type',
        'category',
        'supervisor_id',
        'supervisor_name',
        'level',
    ];

    /**
     * Get the localized name
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? ($this->name_en ?: $this->name_ar) : $this->name_ar;
    }

    /**
     * Get the localized description
     */
    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        return $locale === 'en' ? ($this->description_en ?: $this->description_ar) : $this->description_ar;
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'program_teacher', 'program_id', 'teacher_id')->withTimestamps();
    }

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function files()
    {
        return $this->hasMany(SubjectFile::class);
    }

    public function enrolledStudents()
    {
        return $this->hasMany(User::class)->where('role', 'student');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
