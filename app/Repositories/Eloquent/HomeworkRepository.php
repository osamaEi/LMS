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

    public function subjectHomeworks(SupportCollection $subjectIds, array $relations = [], ?SupportCollection $classIds = null): Collection
    {
        return $this->model
            ->whereIn('subject_id', $subjectIds)
            // Class-scoped homework is only visible to students in that class;
            // homework with a null class_id is visible to all classes.
            ->when($classIds !== null, function ($q) use ($classIds) {
                $q->where(function ($cq) use ($classIds) {
                    $cq->whereNull('class_id')
                       ->orWhereIn('class_id', $classIds);
                });
            })
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
        $classIds = $student->allClassIds();

        return $this->model
            ->where('id', $id)
            ->where(function ($q) use ($subjectIds, $student, $classIds) {
                // Subject homework: class-scoped ones must match the student's classes.
                $q->where(function ($sq) use ($subjectIds, $classIds) {
                    $sq->whereIn('subject_id', $subjectIds)
                       ->where(function ($cq) use ($classIds) {
                           $cq->whereNull('class_id')
                              ->orWhereIn('class_id', $classIds);
                       });
                })->orWhere('program_id', $student->program_id);
            })
            ->with(['subject:id,name_ar,name_en,code', 'program:id,name_ar,name_en'])
            ->firstOrFail();
    }

    /**
     * Subjects the given teacher may assign homework to (via class, term class,
     * or direct assignment).
     */
    /**
     * Classes (groups) the teacher is registered on through their sessions,
     * i.e. classes that have at least one session whose teacher_id is this
     * teacher. Falls back to classes the teacher owns directly.
     */
    public function teacherClasses(User $teacher): Collection
    {
        return ProgramClass::where(function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id)
                  ->orWhereHas('sessions', fn($sq) => $sq->where('teacher_id', $teacher->id));
            })
            ->where('status', 'active')
            ->with('program:id,name_ar')
            ->orderBy('name')
            ->get();
    }

    public function teacherSubjects(User $teacher): Collection
    {
        $classIds = ProgramClass::where('teacher_id', $teacher->id)->pluck('id');

        return Subject::where(function ($q) use ($teacher, $classIds) {
                $q->whereIn('class_id', $classIds)
                  ->orWhereHas('term', fn($tq) => $tq->whereIn('class_id', $classIds))
                  ->orWhere(fn($aq) => $aq->assignedToTeacher($teacher->id));
            })
            ->with(['term:id,class_id', 'terms:id,class_id'])
            ->orderBy('name_ar')
            ->get();
    }

    /**
     * Homework the teacher created for any of the given subjects, newest first.
     *
     * Includes legacy homework still tied to a session (session_id, no
     * subject_id) whose session belongs to one of the teacher's subjects, so
     * old homework keeps showing after the subject/program migration.
     */
    public function teacherHomeworks(SupportCollection $subjectIds, array $relations = []): Collection
    {
        return $this->model
            ->where(function ($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds)
                  ->orWhereHas('session', fn($sq) => $sq->whereIn('subject_id', $subjectIds));
            })
            ->with($relations)
            ->orderByDesc('created_at')
            ->get();
    }
}
