<?php

namespace App\Repositories\Eloquent;

use App\Models\Term;
use App\Models\User;
use App\Repositories\Contracts\TermRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TermRepository extends BaseRepository implements TermRepositoryInterface
{
    public function __construct(Term $model)
    {
        parent::__construct($model);
    }

    public function getActiveTerms(): Collection
    {
        return $this->model->where('status', 'active')->get();
    }

    public function getByProgram(int $programId): Collection
    {
        return $this->model->where('program_id', $programId)
            ->orderBy('start_date', 'desc')
            ->get();
    }

    public function getCurrentActiveTerm(int $programId): ?Term
    {
        return $this->model->where('program_id', $programId)
            ->where('status', 'active')
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->first();
    }

    public function getNextUpcomingTerm(int $programId): ?Term
    {
        return $this->model->where('program_id', $programId)
            ->where('status', 'upcoming')
            ->where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->first();
    }

    public function isRegistrationOpen(int $termId): bool
    {
        $term = $this->find($termId);

        if (!$term) {
            return false;
        }

        return $term->isRegistrationOpen();
    }

    public function autoAssignStudentToTerm(User $student): ?Term
    {
        if (!$student->program_id) {
            return null;
        }

        $currentTerm = $this->getCurrentActiveTerm($student->program_id);

        if ($currentTerm && $currentTerm->isRegistrationOpen()) {
            return $currentTerm;
        }

        return $this->getNextUpcomingTerm($student->program_id);
    }
}
