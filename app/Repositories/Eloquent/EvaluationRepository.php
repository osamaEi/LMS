<?php

namespace App\Repositories\Eloquent;

use App\Models\Evaluation;
use App\Repositories\Contracts\EvaluationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EvaluationRepository extends BaseRepository implements EvaluationRepositoryInterface
{
    public function __construct(Evaluation $model)
    {
        parent::__construct($model);
    }

    public function getBySubject(int $subjectId): Collection
    {
        return $this->model->where('subject_id', $subjectId)
            ->with(['student', 'gradedByUser'])
            ->get();
    }

    public function getByStudent(int $studentId): Collection
    {
        return $this->model->where('student_id', $studentId)
            ->with(['subject', 'gradedByUser'])
            ->get();
    }

    public function getByStudentAndSubject(int $studentId, int $subjectId): Collection
    {
        return $this->model->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->with('gradedByUser')
            ->get();
    }

    public function getByType(string $type, int $subjectId = null): Collection
    {
        $query = $this->model->where('type', $type)
            ->with(['student', 'subject']);

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        return $query->get();
    }

    public function grade(int $evaluationId, float $earnedScore, string $feedback = null, int $gradedBy = null): bool
    {
        $evaluation = $this->find($evaluationId);

        if (!$evaluation) {
            return false;
        }

        $evaluation->grade($earnedScore, $feedback, $gradedBy);

        return true;
    }

    public function calculateTotalScore(int $studentId, int $subjectId): array
    {
        $evaluations = $this->getByStudentAndSubject($studentId, $subjectId);

        $gradedEvaluations = $evaluations->where('status', 'graded');

        if ($gradedEvaluations->isEmpty()) {
            return [
                'total_weighted_score' => 0,
                'total_weight' => 0,
                'final_grade' => 0,
                'grade_letter' => 'N/A',
            ];
        }

        $totalWeightedScore = 0;
        $totalWeight = 0;

        foreach ($gradedEvaluations as $evaluation) {
            $weightedScore = $evaluation->getWeightedScore();
            $totalWeightedScore += $weightedScore;
            $totalWeight += $evaluation->weight;
        }

        $finalGrade = $totalWeight > 0 ? round($totalWeightedScore, 2) : 0;

        $gradeLetter = $this->calculateGradeLetter($finalGrade);

        return [
            'total_weighted_score' => $totalWeightedScore,
            'total_weight' => $totalWeight,
            'final_grade' => $finalGrade,
            'grade_letter' => $gradeLetter,
        ];
    }

    public function getPendingEvaluations(int $subjectId = null): Collection
    {
        $query = $this->model->where('status', 'pending')
            ->with(['student', 'subject']);

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        return $query->get();
    }

    public function getGradedEvaluations(int $subjectId = null): Collection
    {
        $query = $this->model->where('status', 'graded')
            ->with(['student', 'subject', 'gradedByUser']);

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        return $query->get();
    }

    private function calculateGradeLetter(float $grade): string
    {
        if ($grade >= 95) return 'A+';
        if ($grade >= 90) return 'A';
        if ($grade >= 85) return 'B+';
        if ($grade >= 80) return 'B';
        if ($grade >= 75) return 'C+';
        if ($grade >= 70) return 'C';
        if ($grade >= 65) return 'D+';
        if ($grade >= 60) return 'D';

        return 'F';
    }
}
