<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\StudentGradesService;
use Illuminate\Http\Request;

class GradesController extends Controller
{
    /**
     * GET /api/v1/student/grades
     * Same data as the web page student/grades.
     */
    public function index(Request $request, StudentGradesService $grades)
    {
        abort_unless($request->user()->role === 'student', 403);

        $overview = $grades->overview($request->user());

        $subjects = collect($overview['subjectGrades'])->values()->map(function ($data) {
            $subject = $data['subject'];
            $isFinal = $data['final_grade'] !== null;

            return [
                'subject_id'        => $subject->id,
                'subject_name'      => $subject->name_ar ?? $subject->name,
                'teacher'           => $subject->teacher ? [
                    'id'   => $subject->teacher->id,
                    'name' => $subject->teacher->name,
                ] : null,
                'percentage'        => (float) $data['percentage'],
                'is_final'          => $isFinal,
                'final_grade'       => $isFinal ? (float) $data['final_grade'] : null,
                'source'            => $isFinal ? 'final_grade' : 'calculated',
                'source_label'      => $isFinal ? 'درجة نهائية مسجّلة' : 'نسبة محسوبة',
                'grade_label'       => $data['grade_label'],
                'grade_level'       => $this->gradeLevel($data['percentage']),
                'evaluations_count' => $data['evaluations']->count(),
                'attempts_count'    => $data['attempts']->count(),
            ];
        });

        $sentReports = $overview['sentReports']->map(fn ($report) => [
            'id'           => $report->id,
            'teacher_name' => $report->teacher_name,
            'sent_at'      => $report->sent_at?->toIso8601String(),
            'is_unread'    => $report->is_unread,
            'rows'         => $report->rows->map(fn ($row) => [
                'name'          => $row['name'],
                'attendance'    => $row['attendance'] ?? null,
                'participation' => $row['participation'] ?? null,
                'midterm'       => $row['midterm'] ?? null,
                'final'         => $row['final'] ?? null,
                'total'         => $row['total'] ?? null,
                'total_max'     => 100,
            ])->values(),
        ])->values();

        // "Final results" card: overall average + approval state. The result
        // counts as approved once every non-withdrawn enrollment has a saved
        // final grade.
        $average = $subjects->isEmpty() ? null : round((float) $overview['avgPercentage'], 1);
        $enrollments = Enrollment::where('student_id', $request->user()->id)
            ->where('status', '!=', 'withdrawn')
            ->get(['final_grade']);
        $approvalStatus = match (true) {
            $enrollments->isEmpty() || $enrollments->every(fn ($e) => $e->final_grade === null) => 'no_grades',
            $enrollments->every(fn ($e) => $e->final_grade !== null) => 'approved',
            default => 'pending',
        };

        return response()->json(['success' => true, 'data' => [
            'final_result' => [
                'average'         => $average,
                'grade_label'     => $average === null ? null : $grades->gradeLabel($average),
                'grade_level'     => $average === null ? null : $this->gradeLevel($average),
                'is_approved'     => $approvalStatus === 'approved',
                'approval_status' => $approvalStatus,
                'approval_label'  => match ($approvalStatus) {
                    'approved'  => 'تم اعتماد النتيجة من شؤون المتدربين',
                    'pending'   => 'النتيجة بانتظار الاعتماد',
                    'no_grades' => 'لم تُرصد النتيجة بعد',
                },
                // No certificate generation exists yet.
                'certificates' => [
                    'available'    => false,
                    'download_url' => null,
                ],
            ],
            'stats' => [
                'average_percentage' => $subjects->isEmpty() ? null : round((float) $overview['avgPercentage'], 1),
                'subjects_count'     => $subjects->count(),
                'quiz_attempts'      => $overview['totalQuizzes'],
                'graded_evaluations' => $overview['totalEvaluations'],
            ],
            'sent_reports' => $sentReports,
            'subjects'     => $subjects,
        ]]);
    }

    private function gradeLevel(float $pct): string
    {
        return match (true) {
            $pct >= 90 => 'excellent',
            $pct >= 75 => 'good',
            $pct >= 60 => 'average',
            default    => 'poor',
        };
    }
}
