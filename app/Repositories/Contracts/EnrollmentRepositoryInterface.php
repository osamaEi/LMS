<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface EnrollmentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get student enrollments
     */
    public function getByStudent(int $studentId): Collection;

    /**
     * Get subject enrollments
     */
    public function getBySubject(int $subjectId): Collection;

    /**
     * Check if student is enrolled in subject
     */
    public function isEnrolled(int $studentId, int $subjectId): bool;

    /**
     * Enroll student in subject
     */
    public function enroll(int $studentId, int $subjectId): mixed;

    /**
     * Withdraw student from subject
     */
    public function withdraw(int $enrollmentId): bool;

    /**
     * Get active enrollments for student
     */
    public function getActiveEnrollments(int $studentId): Collection;

    /**
     * Update final grade
     */
    public function updateFinalGrade(int $enrollmentId, float $grade, string $letterGrade): bool;
}
