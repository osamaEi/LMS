<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\QuizAttempt;
use App\Models\Subject;

class GradesController extends Controller
{
    public function index()
    {
        $student = auth()->user();

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

        // Subjects with evaluations summary
        $subjectIds = $evaluations->pluck('subject_id')->merge($quizAttempts->pluck('quiz.subject_id'))->unique()->filter();
        $subjects   = Subject::whereIn('id', $subjectIds)->with('teacher')->get()->keyBy('id');

        // Per-subject grade summary
        $subjectGrades = [];
        foreach ($subjects as $subject) {
            $subjectEvals   = $evaluations->where('subject_id', $subject->id);
            $subjectAttempts = $quizAttempts->filter(fn($a) => $a->quiz?->subject_id === $subject->id);

            $totalEarned = $subjectEvals->sum('earned_score') + $subjectAttempts->max('score');
            $totalMax    = $subjectEvals->sum('total_score') + $subjectAttempts->max(fn($a) => $a->quiz?->total_marks ?? 0);

            $percentage = $totalMax > 0 ? round(($totalEarned / $totalMax) * 100, 1) : 0;

            $subjectGrades[$subject->id] = [
                'subject'     => $subject,
                'evaluations' => $subjectEvals->values(),
                'attempts'    => $subjectAttempts->values(),
                'percentage'  => $percentage,
                'grade_label' => $this->gradeLabel($percentage),
            ];
        }

        // Overall stats
        $totalEvaluations = $evaluations->count();
        $totalQuizzes     = $quizAttempts->count();
        $avgPercentage    = collect($subjectGrades)->avg('percentage');

        return view('student.grades.index', compact(
            'subjectGrades',
            'totalEvaluations',
            'totalQuizzes',
            'avgPercentage'
        ));
    }

    private function gradeLabel(float $pct): string
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
