<?php

namespace App\Repositories\Contracts;

use App\Models\Homework;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface HomeworkRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Subject IDs a student can access via their primary program only
     * (program_id / term / terms / direct enrollments). Mirrors the API
     * student scope which keys off a single program_id.
     */
    public function accessibleSubjectIdsForProgram(User $student): SupportCollection;

    /**
     * Subject IDs a student can access across ALL of their programs
     * (allProgramIds() / term / direct enrollments). Mirrors the web
     * student scope which spans every program the student belongs to.
     */
    public function accessibleSubjectIdsForAllPrograms(User $student): SupportCollection;

    /**
     * Homework belonging to sessions in the given subjects, newest first.
     */
    public function subjectHomeworks(SupportCollection $subjectIds, array $relations = []): Collection;

    /**
     * Homework belonging to sessions in the given program(s), newest first.
     *
     * @param  int|array<int>  $programIds
     */
    public function programHomeworks($programIds, array $relations = []): Collection;

    /**
     * Find a homework by id that the student is allowed to access, or fail.
     * Access = homework's subject is in $subjectIds OR homework's program is
     * the student's program.
     */
    public function findAccessibleForStudent(int $id, SupportCollection $subjectIds, User $student): Homework;

    /**
     * Subjects the given teacher may assign homework to.
     */
    public function teacherSubjects(User $teacher): Collection;

    /**
     * Classes (groups) the teacher is registered on via their sessions.
     */
    public function teacherClasses(User $teacher): Collection;

    /**
     * Homework the teacher created for any of the given subjects, newest first.
     */
    public function teacherHomeworks(SupportCollection $subjectIds, array $relations = []): Collection;
}
