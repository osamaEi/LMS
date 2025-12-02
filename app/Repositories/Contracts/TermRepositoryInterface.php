<?php

namespace App\Repositories\Contracts;

use App\Models\Term;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface TermRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get active terms
     */
    public function getActiveTerms(): Collection;

    /**
     * Get terms by program
     */
    public function getByProgram(int $programId): Collection;

    /**
     * Get current active term for a program
     */
    public function getCurrentActiveTerm(int $programId): ?Term;

    /**
     * Get next upcoming term for a program
     */
    public function getNextUpcomingTerm(int $programId): ?Term;

    /**
     * Check if registration is open for term
     */
    public function isRegistrationOpen(int $termId): bool;

    /**
     * Auto-assign student to appropriate term
     */
    public function autoAssignStudentToTerm(User $student): ?Term;
}
