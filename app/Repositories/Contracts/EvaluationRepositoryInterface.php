<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface EvaluationRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get evaluations by subject
     */
    public function getBySubject(int $subjectId): Collection;

    /**
     * Get evaluations by student
     */
    public function getByStudent(int $studentId): Collection;

    /**
     * Get evaluations by student and subject
     */
    public function getByStudentAndSubject(int $studentId, int $subjectId): Collection;

    /**
     * Get evaluations by type
     */
    public function getByType(string $type, int $subjectId = null): Collection;

    /**
     * Grade evaluation
     */
    public function grade(int $evaluationId, float $earnedScore, string $feedback = null, int $gradedBy = null): bool;

    /**
     * Calculate total score for student in subject
     */
    public function calculateTotalScore(int $studentId, int $subjectId): array;

    /**
     * Get pending evaluations
     */
    public function getPendingEvaluations(int $subjectId = null): Collection;

    /**
     * Get graded evaluations
     */
    public function getGradedEvaluations(int $subjectId = null): Collection;
}
