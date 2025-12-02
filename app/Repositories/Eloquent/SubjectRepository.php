<?php

namespace App\Repositories\Eloquent;

use App\Models\Subject;
use App\Repositories\Contracts\SubjectRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SubjectRepository extends BaseRepository implements SubjectRepositoryInterface
{
    public function __construct(Subject $model)
    {
        parent::__construct($model);
    }

    public function getByTerm(int $termId): Collection
    {
        return $this->model->where('term_id', $termId)
            ->with(['teacher', 'enrollments'])
            ->get();
    }

    public function getByTeacher(int $teacherId): Collection
    {
        return $this->model->where('teacher_id', $teacherId)
            ->with(['term', 'enrollments'])
            ->get();
    }

    public function getActiveSubjects(): Collection
    {
        return $this->model->where('status', 'active')
            ->with(['teacher', 'term'])
            ->get();
    }

    public function hasCapacity(int $subjectId): bool
    {
        $subject = $this->find($subjectId);

        if (!$subject) {
            return false;
        }

        return $subject->hasCapacity();
    }

    public function getEnrolledCount(int $subjectId): int
    {
        $subject = $this->find($subjectId);

        if (!$subject) {
            return 0;
        }

        return $subject->getEnrolledCount();
    }

    public function getWithEnrollments(int $subjectId): mixed
    {
        return $this->model->with(['enrollments.student', 'teacher', 'term'])
            ->findOrFail($subjectId);
    }
}
