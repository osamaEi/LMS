<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface SubjectRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get subjects by term
     */
    public function getByTerm(int $termId): Collection;

    /**
     * Get subjects by teacher
     */
    public function getByTeacher(int $teacherId): Collection;

    /**
     * Get active subjects
     */
    public function getActiveSubjects(): Collection;

    /**
     * Check if subject has capacity for enrollment
     */
    public function hasCapacity(int $subjectId): bool;

    /**
     * Get enrolled students count
     */
    public function getEnrolledCount(int $subjectId): int;

    /**
     * Get subjects with enrolled students
     */
    public function getWithEnrollments(int $subjectId): mixed;
}
