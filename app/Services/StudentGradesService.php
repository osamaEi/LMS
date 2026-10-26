<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\QuizAttempt;
use App\Models\Subject;
use App\Models\User;
use App\Notifications\TeacherGradesNotification;
use Illuminate\Support\Collection;

/**
 * Builds the student "grades & evaluations" overview, shared by the web page
 * (student/grades) and the API (api/v1/student/grades).
 */
class StudentGradesService
{
    /** Final grades saved by the teacher, including grades not yet sent. */
    public function savedFinalGrades(User $student): Collection
    {
        return Enrollment::where('student_id', $student->id)
            ->whereNotNull('final_grade')
            ->with('subject')
            ->get()
            ->filter(fn ($enrollment) => $enrollment->subject !== null)
            ->map(fn ($enrollment) => [
                'subject_id' => $enrollment->subject_id,
                'name' => $enrollment->subject->name_ar ?? $enrollment->subject->name,
                'total' => (float) $enrollment->final_grade,
            ])->values();
    }

    /**
     * @return array{subjectGrades: array, totalEvaluations: int, totalQuizzes: int, avgPercentage: float|null, sentReports: Collection}
     */
    public function overview(User $student): array
    {
        $sentReports = $this->sentGradeReports($student);
        $releasedRows = $sentReports->flatMap(fn ($report) => $report->rows)
            ->filter(fn ($row) => isset($row['subject_id']))
            ->unique('subject_id')->keyBy('subject_id');
        // Evaluations grouped by subject
        $evaluations = Evaluation::where('student_id', $student->id)
            ->where('status', 'graded')
            ->with(['subject.teacher'])
            ->orderBy('graded_at', 'desc')
            ->get();

        // Quiz attempts with scores
        $quizAttempts = QuizAttempt::where('student_id', $student->id)
            ->whereNotNull('submitted_at')
            ->whereNotNull('results_released_at')
            ->with(['quiz.subject.teacher'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $quizAttempts = $quizAttempts->filter(fn ($attempt) => $attempt->canShowResults());

        // Only subjects the teacher explicitly sent a grade report for. An
        // enrollment's final_grade or a graded quiz alone is NOT enough to
        // surface a subject here — the teacher has to send it to the student.
        // Current training quarter only: subjects whose quarter is over are hidden.
        $subjectIds = $releasedRows->keys();
        $subjects   = Subject::whereIn('id', $subjectIds)
            ->whereNotIn('id', Subject::query()->quarterEnded()->select('id'))
            ->with('teacher')->get()->keyBy('id');

        // Per-subject grade summary
        $subjectGrades = [];
        foreach ($subjects as $subject) {
            $subjectEvals   = $evaluations->where('subject_id', $subject->id);
            $subjectAttempts = $quizAttempts->filter(fn($a) => $a->quiz?->subject_id === $subject->id);

            // The number shown is the one the teacher sent, nothing else.
            $breakdown  = $releasedRows->get($subject->id);
            $percentage = (float) ($breakdown['total'] ?? 0);

            $subjectGrades[$subject->id] = [
                'subject'     => $subject,
                'evaluations' => $subjectEvals->values(),
                'attempts'    => $subjectAttempts->values(),
                'percentage'  => $percentage,
                'final_grade' => $breakdown['total'] ?? null,
                'breakdown'   => $breakdown,
                'grade_label' => $this->gradeLabel($percentage),
            ];
        }

        // Counts cover the displayed subjects only, so they match what is shown.
        return [
            'subjectGrades'    => $subjectGrades,
            'totalEvaluations' => collect($subjectGrades)->sum(fn ($row) => $row['evaluations']->count()),
            'totalQuizzes'     => collect($subjectGrades)->sum(fn ($row) => $row['attempts']->count()),
            'avgPercentage'    => collect($subjectGrades)->avg('percentage'),
            'sentReports'      => $sentReports,
        ];
    }

    /**
     * Grade breakdowns teachers have explicitly sent to this student from the
     * student report page. Stored as database notifications, newest first.
     *
     * Each entry: teacher name, when it was sent, and the per-subject rows
     * (attendance / participation / midterm / final / total).
     */
    public function sentGradeReports(User $student): Collection
    {
        return $student->notifications()
            ->where('type', TeacherGradesNotification::class)
            ->latest()
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;

                $rows = collect($data['grades'] ?? [])
                    // Guard against malformed payloads from older sends.
                    ->filter(fn ($row) => is_array($row) && isset($row['name']))
                    ->values();

                return $rows->isEmpty() ? null : (object) [
                    'id'           => $notification->id,
                    'teacher_name' => $data['sender_name'] ?? 'المعلم',
                    'sent_at'      => $notification->created_at,
                    'is_unread'    => $notification->read_at === null,
                    'rows'         => $rows,
                ];
            })
            ->filter()
            ->values();
    }

    public function gradeLabel(float $pct): string
    {
        return match (true) {
            $pct >= 90 => 'ممتاز',
            $pct >= 75 => 'جيد جداً',
            $pct >= 60 => 'جيد',
            $pct >= 50 => 'مقبول',
            default    => 'ضعيف',
        };
    }
}
