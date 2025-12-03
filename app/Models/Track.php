<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Track extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'program_id',
        'name',
        'code',
        'description',
        'total_terms',
        'duration_months',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_terms' => 'integer',
            'duration_months' => 'integer',
        ];
    }

    // Relationships
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function terms(): HasMany
    {
        return $this->hasMany(Term::class)->orderBy('term_number', 'asc');
    }

    public function students(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function enrollments()
    {
        return $this->hasManyThrough(
            Enrollment::class,
            Term::class,
            'track_id', // Foreign key on terms table
            'subject_id', // Foreign key on enrollments table
            'id', // Local key on tracks table
            'id' // Local key on terms table
        )->join('subjects', 'enrollments.subject_id', '=', 'subjects.id')
         ->where('subjects.term_id', '=', 'terms.id');
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

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function getTotalStudents(): int
    {
        return $this->students()->count();
    }

    public function getTermByNumber(int $termNumber): ?Term
    {
        return $this->terms()->where('term_number', $termNumber)->first();
    }

    public function hasAllTerms(): bool
    {
        return $this->terms()->count() === $this->total_terms;
    }

    public function getCompletionPercentage(): float
    {
        if ($this->total_terms === 0) {
            return 0;
        }

        $completedTerms = $this->terms()->where('status', 'completed')->count();
        return round(($completedTerms / $this->total_terms) * 100, 2);
    }

    public function getCurrentActiveTerm(): ?Term
    {
        return $this->terms()
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    public function getNextTerm(int $currentTermNumber): ?Term
    {
        return $this->terms()
            ->where('term_number', '>', $currentTermNumber)
            ->orderBy('term_number', 'asc')
            ->first();
    }

    public function getActiveStudentsCount(): int
    {
        return $this->students()
            ->where('is_active', true)
            ->count();
    }
}
