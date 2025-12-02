<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface AttendanceRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get attendance by session
     */
    public function getBySession(int $sessionId): Collection;

    /**
     * Get attendance by student
     */
    public function getByStudent(int $studentId): Collection;

    /**
     * Mark student as attended
     */
    public function markAttended(int $studentId, int $sessionId, array $data = []): mixed;

    /**
     * Update watch progress for video session
     */
    public function updateWatchProgress(int $studentId, int $sessionId, float $percentage): bool;

    /**
     * Check if student attended session
     */
    public function hasAttended(int $studentId, int $sessionId): bool;

    /**
     * Calculate attendance rate for student
     */
    public function calculateStudentAttendanceRate(int $studentId, int $subjectId = null): float;

    /**
     * Get attendance record or create
     */
    public function getOrCreate(int $studentId, int $sessionId): mixed;
}
