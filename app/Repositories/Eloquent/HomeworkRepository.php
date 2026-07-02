<?php

namespace App\Repositories\Eloquent;

use App\Models\Homework;
use App\Models\ProgramClass;
use App\Models\Subject;
use App\Models\User;
use App\Repositories\Contracts\HomeworkRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class HomeworkRepository extends BaseRepository implements HomeworkRepositoryInterface
{
    public function __construct(Homework $model)
    {
        parent::__construct($model);
    }

    public function accessibleSubjectIdsForProgram(User $student): SupportCollection
    {
        return Subject::where(function ($q) use ($student) {
            $q->where('program_id', $student->program_id)
              ->orWhereHas('term', fn($tq) => $tq->where('program_id', $student->program_id))
              ->orWhereHas('terms', fn($tq) => $tq->where('program_id', $student->program_id))
              ->orWhereHas('enrollments', fn($eq) => $eq->where('student_id', $student->id));
        })->pluck('id');
    }

    public function accessibleSubjectIdsForAllPrograms(User $student): SupportCollection
    {
        $programIds = $student->allProgramIds();

        return Subject::where(function ($q) use ($student, $programIds) {
            $q->whereIn('program_id', $programIds)
              ->orWhereHas('term', fn($tq) => $tq->whereIn('program_id', $programIds))
              ->orWhereHas('enrollments', fn($eq) => $eq->where('student_id', $student->id));
        })->pluck('id');
    }

    public function subjectHomeworks(SupportCollection $subjectIds, array $relations = []): Collection
    {
        return $this->model
            ->whereIn('subject_id', $subjectIds)
            ->with($relations)
            ->orderByDesc('created_at')
            ->get();
    }

    public function programHomeworks($programIds, array $relations = []): Collection
    {
        return $this->model
            ->where(function ($q) use ($programIds) {
                is_array($programIds) || $programIds instanceof SupportCollection
                    ? $q->whereIn('program_id', $programIds)
                    : $q->where('program_id', $programIds);
            })
            ->with($relations)
            ->orderByDesc('created_at')
            ->get();
    }

    public function findAccessibleForStudent(int $id, SupportCollection $subjectIds, User $student): Homework
    {
        return $this->model
            ->where('id', $id)
            ->where(function ($q) use ($subjectIds, $student) {
                $q->whereIn('subject_id', $subjectIds)
                  ->orWhere('program_id', $student->program_id);
            })
            ->with(['subject:id,name_ar,name_en,code', 'program:id,name_ar,name_en'])
            ->firstOrFail();
    }

    /**
     * Subjects the given teacher may assign homework to (via class, term class,
     * or direct assignment).
     */
    public function teacherSubjects(User $teacher): Collection
    {
        $classIds = ProgramClass::where('teacher_id', $teacher->id)->pluck('id');

        return Subject::where(function ($q) use ($teacher, $classIds) {
                $q->whereIn('class_id', $classIds)
                  ->orWhereHas('term', fn($tq) => $tq->whereIn('class_id', $classIds))
                  ->orWhere(fn($aq) => $aq->assignedToTeacher($teacher->id));
            })
            ->orderBy('name_ar')
            ->get();
    }

    /**
     * Homework the teacher created for any of the given subjects, newest first.
     */
    public function teacherHomeworks(SupportCollection $subjectIds, array $relations = []): Collection
    {
        return $this->model
            ->whereIn('subject_id', $subjectIds)
            ->with($relations)
            ->orderByDesc('created_at')
            ->get();
    }
}
