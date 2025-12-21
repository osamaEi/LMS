<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'term_id',
        'teacher_id',
        'name',
        'code',
        'description',
        'banner_photo',
        'credits',
        'total_hours',
        'max_students',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'max_students' => 'integer',
        ];
    }

    // Relationships
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(Unit::class)->orderBy('order', 'asc');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(SatisfactionSurvey::class);
    }

    public function teacherRatings(): HasMany
    {
        return $this->hasMany(TeacherRating::class);
    }

    // Prerequisites relationships
    public function prerequisites()
    {
        return $this->belongsToMany(Subject::class, 'subject_prerequisites', 'subject_id', 'prerequisite_id')
                    ->withPivot('is_mandatory')
                    ->withTimestamps();
    }

    public function dependentSubjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_prerequisites', 'prerequisite_id', 'subject_id')
                    ->withPivot('is_mandatory')
                    ->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'subject_id', 'student_id')
            ->withPivot('status', 'enrolled_at', 'final_grade', 'grade_letter', 'completion_date')
            ->withTimestamps();
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function getEnrolledCount(): int
    {
        return $this->enrollments()->where('status', 'active')->count();
    }

    public function hasCapacity(): bool
    {
        return $this->getEnrolledCount() < $this->max_students;
    }

    public function isFull(): bool
    {
        return !$this->hasCapacity();
    }

    public function getAvailableSeats(): int
    {
        return max(0, $this->max_students - $this->getEnrolledCount());
    }

    /**
     * Check if student meets all prerequisites
     */
    public function studentMeetsPrerequisites(int $studentId): bool
    {
        $mandatoryPrereqs = $this->prerequisites()->wherePivot('is_mandatory', true)->get();

        foreach ($mandatoryPrereqs as $prereq) {
            $completed = Enrollment::where('student_id', $studentId)
                                   ->where('subject_id', $prereq->id)
                                   ->where('status', 'completed')
                                   ->exists();
            if (!$completed) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get missing prerequisites for a student
     */
    public function getMissingPrerequisites(int $studentId): array
    {
        $missing = [];
        $mandatoryPrereqs = $this->prerequisites()->wherePivot('is_mandatory', true)->get();

        foreach ($mandatoryPrereqs as $prereq) {
            $completed = Enrollment::where('student_id', $studentId)
                                   ->where('subject_id', $prereq->id)
                                   ->where('status', 'completed')
                                   ->exists();
            if (!$completed) {
                $missing[] = $prereq;
            }
        }

        return $missing;
    }

    /**
     * Get teacher's average rating for this subject
     */
    public function getTeacherRatingForSubject(): float
    {
        $avg = $this->teacherRatings()->where('is_approved', true)->avg('overall_rating');
        return round($avg ?? 0, 2);
    }
}
