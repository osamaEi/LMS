<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\TermRepositoryInterface;

class TermService
{
    public function __construct(
        protected TermRepositoryInterface $termRepository
    ) {
    }

    public function getAllTerms(array $filters = [])
    {
        if (isset($filters['program_id'])) {
            return $this->termRepository->getByProgram($filters['program_id']);
        }

        if (isset($filters['status']) && $filters['status'] === 'active') {
            return $this->termRepository->getActiveTerms();
        }

        return $this->termRepository->all();
    }

    public function getTerm(int $id)
    {
        return $this->termRepository->findOrFail($id, ['*'], ['program', 'subjects']);
    }

    public function createTerm(array $data)
    {
        return $this->termRepository->create($data);
    }

    public function updateTerm(int $id, array $data)
    {
        return $this->termRepository->update($id, $data);
    }

    public function deleteTerm(int $id)
    {
        return $this->termRepository->delete($id);
    }

    public function autoAssignStudent(User $student)
    {
        return $this->termRepository->autoAssignStudentToTerm($student);
    }

    public function getCurrentActiveTerm(int $programId)
    {
        return $this->termRepository->getCurrentActiveTerm($programId);
    }

    public function isRegistrationOpen(int $termId): bool
    {
        return $this->termRepository->isRegistrationOpen($termId);
    }
}
