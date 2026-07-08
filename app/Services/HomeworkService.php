<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Session;
use App\Models\User;
use App\Notifications\HomeworkCreatedNotification;
use App\Repositories\Contracts\HomeworkRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HomeworkService
{
    public function __construct(
        protected HomeworkRepositoryInterface $homeworkRepository
    ) {
    }

    // ─── Teacher: management ────────────────────────────────────────────────

    /**
     * Create a homework for a subject or program (no longer tied to a session),
     * store its optional file, and notify the relevant active students.
     *
     * $data must contain either subject_id or program_id.
     */
    public function create(array $data, ?UploadedFile $file = null): Homework
    {
        if ($file) {
            $data['file_path'] = $file->store('homework-files', 'public');
            $data['file_name'] = $file->getClientOriginalName();
        }

        $homework = Homework::create($data);

        $this->notifyEnrolledStudents($homework);

        return $homework;
    }

    /**
     * Update a homework, replacing its file (and deleting the old one) if a new
     * file is supplied.
     */
    public function update(Homework $homework, array $data, ?UploadedFile $file = null): Homework
    {
        if ($file) {
            if ($homework->file_path) {
                Storage::disk('public')->delete($homework->file_path);
            }
            $data['file_path'] = $file->store('homework-files', 'public');
            $data['file_name'] = $file->getClientOriginalName();
        }

        $homework->update($data);

        return $homework;
    }

    /**
     * Delete a homework and its stored file.
     */
    public function delete(Homework $homework): void
    {
        if ($homework->file_path) {
            Storage::disk('public')->delete($homework->file_path);
        }

        $homework->delete();
    }

    /**
     * Save a grade and feedback on a submission.
     */
    public function gradeSubmission(HomeworkSubmission $submission, array $data): HomeworkSubmission
    {
        $submission->update([
            'grade'     => $data['grade'] ?? null,
            'max_grade' => $data['max_grade'] ?? 100,
            'feedback'  => $data['feedback'] ?? null,
        ]);

        return $submission;
    }

    private function notifyEnrolledStudents(Homework $homework): void
    {
        $students = collect();

        // When scoped to a class (group), notify only that class's students.
        if ($homework->class_id) {
            $students = \App\Models\ProgramClass::find($homework->class_id)
                ?->students()->get() ?? collect();

            foreach ($students as $student) {
                $student->notify(new HomeworkCreatedNotification($homework));
            }

            return;
        }

        if ($homework->subject_id) {
            $students = Enrollment::where('subject_id', $homework->subject_id)
                ->where('status', 'active')
                ->with('student')
                ->get()
                ->pluck('student')
                ->filter();
        } elseif ($homework->program_id) {
            $subjectIds = \App\Models\Subject::where('program_id', $homework->program_id)->pluck('id');

            $students = Enrollment::whereIn('subject_id', $subjectIds)
                ->where('status', 'active')
                ->with('student')
                ->get()
                ->pluck('student')
                ->filter()
                ->unique('id');
        }

        foreach ($students as $student) {
            $student->notify(new HomeworkCreatedNotification($homework));
        }
    }

    // ─── Student: submissions ───────────────────────────────────────────────

    /**
     * Whether the homework's submission window has closed. The whole due day is
     * allowed — only days after it are rejected.
     */
    public function isPastDeadline(Homework $homework): bool
    {
        return $homework->due_date && $homework->due_date->endOfDay()->isPast();
    }

    /**
     * Create or update a student's submission for a homework. Accepts text
     * content, an uploaded file, or an external URL (URL is stored directly in
     * file_path, mirroring the API behavior).
     */
    public function submit(
        Homework $homework,
        User $student,
        array $data,
        ?UploadedFile $file = null,
        ?string $fileUrl = null
    ): HomeworkSubmission {
        $payload = [
            'homework_id'  => $homework->id,
            'student_id'   => $student->id,
            'content'      => $data['content'] ?? null,
            'submitted_at' => now(),
        ];

        if ($file) {
            $payload['file_path'] = $file->store('homework-submissions', 'public');
            $payload['file_name'] = $file->getClientOriginalName();
        } elseif ($fileUrl) {
            $payload['file_path'] = $fileUrl;
            $payload['file_name'] = basename($fileUrl);
        }

        return HomeworkSubmission::updateOrCreate(
            ['homework_id' => $homework->id, 'student_id' => $student->id],
            $payload
        );
    }

    /**
     * Delete a submission and its stored file. External URLs are left untouched
     * in storage.
     */
    public function deleteSubmission(HomeworkSubmission $submission): void
    {
        if ($submission->file_path && !filter_var($submission->file_path, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($submission->file_path);
        }

        $submission->delete();
    }

    // ─── Read helpers ───────────────────────────────────────────────────────

    /**
     * All homework visible to a student across all their programs, newest first.
     * Mirrors the web student index scope.
     */
    public function homeworksForStudentWeb(User $student)
    {
        $subjectIds = $this->homeworkRepository->accessibleSubjectIdsForAllPrograms($student);

        $subjectHomeworks = $this->homeworkRepository->subjectHomeworks($subjectIds, ['subject'], $student->allClassIds());
        $programHomeworks = $this->homeworkRepository->programHomeworks($student->allProgramIds(), ['program']);

        return $subjectHomeworks->merge($programHomeworks)->sortByDesc('created_at')->values();
    }

    /**
     * All homework visible to a student via their primary program, newest first.
     * Mirrors the API student index scope.
     */
    public function homeworksForStudentApi(User $student)
    {
        $subjectIds = $this->homeworkRepository->accessibleSubjectIdsForProgram($student);

        $subjectHomeworks = $this->homeworkRepository->subjectHomeworks(
            $subjectIds,
            ['subject:id,name_ar,name_en,code'],
            $student->allClassIds()
        );
        $programHomeworks = $this->homeworkRepository->programHomeworks(
            $student->program_id,
            ['program:id,name_ar,name_en']
        );

        return $subjectHomeworks->merge($programHomeworks)->sortByDesc('created_at')->values();
    }

    /**
     * A student's submissions for the given homework ids, keyed by homework_id.
     */
    public function submissionsFor(User $student, $homeworkIds)
    {
        return HomeworkSubmission::where('student_id', $student->id)
            ->whereIn('homework_id', $homeworkIds)
            ->get()
            ->keyBy('homework_id');
    }

    /**
     * Find a homework a student may access (via API scope), or fail.
     */
    public function findAccessibleForStudent(int $id, User $student): Homework
    {
        $subjectIds = $this->homeworkRepository->accessibleSubjectIdsForProgram($student);

        return $this->homeworkRepository->findAccessibleForStudent($id, $subjectIds, $student);
    }

    /**
     * Data for the teacher homework dashboard: the subjects the teacher can
     * assign homework to, plus all homework they've created (newest first).
     *
     * @return array{subjects: \Illuminate\Database\Eloquent\Collection, homeworks: \Illuminate\Database\Eloquent\Collection}
     */
    public function teacherHomeworkDashboard(User $teacher): array
    {
        $subjects = $this->homeworkRepository->teacherSubjects($teacher);
        $classes  = $this->homeworkRepository->teacherClasses($teacher);

        $homeworks = $this->homeworkRepository->teacherHomeworks(
            $subjects->pluck('id'),
            ['subject', 'program', 'programClass', 'session.subject', 'session.program']
        );

        return [
            'subjects'  => $subjects,
            'classes'   => $classes,
            'homeworks' => $homeworks,
        ];
    }
}
