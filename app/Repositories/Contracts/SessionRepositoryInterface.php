<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface SessionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get sessions by subject
     */
    public function getBySubject(int $subjectId): Collection;

    /**
     * Get live sessions
     */
    public function getLiveSessions(): Collection;

    /**
     * Get recorded sessions
     */
    public function getRecordedSessions(): Collection;

    /**
     * Get upcoming sessions
     */
    public function getUpcomingSessions(int $subjectId = null): Collection;

    /**
     * Get session with attendance records
     */
    public function getWithAttendance(int $sessionId): mixed;

    /**
     * Calculate attendance rate for session
     */
    public function calculateAttendanceRate(int $sessionId): float;
}
