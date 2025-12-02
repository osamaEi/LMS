<?php

namespace App\Repositories\Eloquent;

use App\Models\Enrollment;
use App\Repositories\Contracts\EnrollmentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EnrollmentRepository extends BaseRepository implements EnrollmentRepositoryInterface
{
    public function __construct(Enrollment $model)
    {
        parent::__construct($model);
    }

    public function getByStudent(int $studentId): Collection
    {
        return $this->model->where('student_id', $studentId)
            ->with(['subject.term', 'subject.teacher'])
            ->get();
    }

    public function getBySubject(int $subjectId): Collection
    {
        return $this->model->where('subject_id', $subjectId)
            ->with('student')
            ->get();
    }

    public function isEnrolled(int $studentId, int $subjectId): bool
    {
        return $this->model->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->exists();
    }

    public function enroll(int $studentId, int $subjectId): mixed
    {
        return $this->model->create([
            'student_id' => $studentId,
            'subject_id' => $subjectId,
            'enrolled_at' => now(),
            'status' => 'active',
        ]);
    }

    public function withdraw(int $enrollmentId): bool
    {
        $enrollment = $this->find($enrollmentId);

        if (!$enrollment) {
            return false;
        }

        return $enrollment->update(['status' => 'withdrawn']);
    }

    public function getActiveEnrollments(int $studentId): Collection
    {
        return $this->model->where('student_id', $studentId)
            ->where('status', 'active')
            ->with(['subject.term', 'subject.teacher'])
            ->get();
    }

    public function updateFinalGrade(int $enrollmentId, float $grade, string $letterGrade): bool
    {
        $enrollment = $this->find($enrollmentId);

        if (!$enrollment) {
            return false;
        }

        return $enrollment->update([
            'final_grade' => $grade,
            'grade_letter' => $letterGrade,
        ]);
    }
}
