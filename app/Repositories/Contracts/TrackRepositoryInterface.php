<?php

namespace App\Repositories\Contracts;

use App\Models\Track;
use Illuminate\Database\Eloquent\Collection;

interface TrackRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active tracks
     */
    public function getActiveTracks(): Collection;

    /**
     * Get tracks by program
     */
    public function getByProgram(int $programId): Collection;

    /**
     * Get track with all terms
     */
    public function getWithTerms(int $trackId): ?Track;

    /**
     * Get track with students
     */
    public function getWithStudents(int $trackId): ?Track;

    /**
     * Check if track has all 10 terms
     */
    public function hasAllTerms(int $trackId): bool;

    /**
     * Get track's current active term
     */
    public function getCurrentActiveTerm(int $trackId): mixed;

    /**
     * Get students count in track
     */
    public function getStudentsCount(int $trackId): int;
}
