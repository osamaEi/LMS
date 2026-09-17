<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

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

    /**
     * Human-readable duration in Arabic. Durations of a year or more read
     * better as years ("سنتان ونصف") than as a raw month count ("30 شهر").
     */
    public function getDurationLabelAttribute(): ?string
    {
        $months = (int) $this->duration_months;

        if ($months <= 0) {
            return null;
        }

        if ($months < 12) {
            return match (true) {
                $months === 1 => 'شهر',
                $months === 2 => 'شهران',
                $months <= 10 => $months . ' أشهر',
                default       => $months . ' شهراً',
            };
        }

        $years  = intdiv($months, 12);
        $rest   = $months % 12;

        $yearsLabel = match (true) {
            $years === 1 => 'سنة',
            $years === 2 => 'سنتان',
            $years <= 10 => $years . ' سنوات',
            default      => $years . ' سنة',
        };

        // Half a year reads as "ونصف"; other remainders keep the months.
        if ($rest === 0) {
            return $yearsLabel;
        }

        if ($rest === 6) {
            return match (true) {
                $years === 1 => 'سنة ونصف',
                $years === 2 => 'سنتان ونصف',
                default      => $yearsLabel . ' ونصف',
            };
        }

        $restLabel = match (true) {
            $rest === 1  => 'شهر',
            $rest === 2  => 'شهران',
            $rest <= 10  => $rest . ' أشهر',
            default      => $rest . ' شهراً',
        };

        return $yearsLabel . ' و' . $restLabel;
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

    public function classes()
    {
        return $this->hasMany(\App\Models\ProgramClass::class);
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
