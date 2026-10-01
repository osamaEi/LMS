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
    /**
     * @return array{subjectGrades: array, totalEvaluations: int, totalQuizzes: int, avgPercentage: float|null, sentReports: Collection}
     */
    public function overview(User $student): array
    {
        // Evaluations grouped by subject
        $evaluations = Evaluation::where('student_id', $student->id)
            ->where('status', 'graded')
            ->with(['subject.teacher'])
            ->orderBy('graded_at', 'desc')
            ->get();

        // Quiz attempts with scores
        $quizAttempts = QuizAttempt::where('student_id', $student->id)
            ->whereNotNull('submitted_at')
            ->with(['quiz.subject.teacher'])
            ->orderBy('submitted_at', 'desc')
            ->get();

        $finalGrades = Enrollment::where('student_id', $student->id)
            ->whereNotNull('final_grade')
            ->pluck('final_grade', 'subject_id');

        // Include saved final grades even when no assessments exist.
        $subjectIds = $evaluations->pluck('subject_id')
            ->merge($quizAttempts->pluck('quiz.subject_id'))
            ->merge($finalGrades->keys())->unique()->filter();
        $subjects   = Subject::whereIn('id', $subjectIds)->with('teacher')->get()->keyBy('id');

        // Per-subject grade summary
        $subjectGrades = [];
        foreach ($subjects as $subject) {
            $subjectEvals   = $evaluations->where('subject_id', $subject->id);
            $subjectAttempts = $quizAttempts->filter(fn($a) => $a->quiz?->subject_id === $subject->id);

            $totalEarned = $subjectEvals->sum('earned_score') + $subjectAttempts->max('score');
            $totalMax    = $subjectEvals->sum('total_score') + $subjectAttempts->max(fn($a) => $a->quiz?->total_marks ?? 0);

            $percentage = $totalMax > 0 ? round(($totalEarned / $totalMax) * 100, 1) : 0;
            $finalGrade = $finalGrades->get($subject->id);
            if ($finalGrade !== null) {
                $percentage = (float) $finalGrade;
            }

            $subjectGrades[$subject->id] = [
                'subject'     => $subject,
                'evaluations' => $subjectEvals->values(),
                'attempts'    => $subjectAttempts->values(),
                'percentage'  => $percentage,
                'final_grade' => $finalGrade,
                'grade_label' => $this->gradeLabel($percentage),
            ];
        }

        return [
            'subjectGrades'    => $subjectGrades,
            'totalEvaluations' => $evaluations->count(),
            'totalQuizzes'     => $quizAttempts->count(),
            'avgPercentage'    => collect($subjectGrades)->avg('percentage'),
            'sentReports'      => $this->sentGradeReports($student),
        ];
    }

    /**
     * Grade breakdowns teachers have explicitly sent to this student from the
     * student report page. Stored as database notifications, newest first.
     *
     * Each entry: teacher name, when it was sent, and the per-subject rows
     * (attendance / participation / midterm / final / total).
     */
    private function sentGradeReports(User $student): Collection
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
