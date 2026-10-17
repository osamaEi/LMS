<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Services\StudentGradesService;
use Illuminate\Http\Request;

class GradesController extends Controller
{
    /** GET /api/v1/student/grades/summary */
    public function summary(Request $request, StudentGradesService $grades)
    {
        abort_unless($request->user()->role === 'student', 403);

        $overview = $grades->overview($request->user());
        $result = collect($overview['subjectGrades'])->map(fn ($row) => [
            'subject_name' => $row['subject']->name_ar ?? $row['subject']->name,
            'grade' => (float) $row['percentage'],
            'evaluation' => $row['grade_label'],
            'certificate_url' => $row['subject']->teacher?->certificate_url,
        ])->values();

        return response()->json(['result' => $result]);
    }

    /** GET /api/v1/student/grades */
    public function index(Request $request, StudentGradesService $grades)
    {
        abort_unless($request->user()->role === 'student', 403);

        $subjectKey = fn ($row) => isset($row['subject_id'])
            ? 'id:' . $row['subject_id']
            : 'name:' . $row['name'];

        $rows = $grades->sentGradeReports($request->user())
            ->flatMap(fn ($report) => $report->rows)
            ->unique($subjectKey)
            ->keyBy($subjectKey);

        foreach ($grades->savedFinalGrades($request->user()) as $saved) {
            $key = $subjectKey($saved);
            $rows->put($key, array_replace($rows->get($key, []), $saved));
        }

        $result = $rows
            ->map(function ($row) {
                $total = isset($row['total']) ? (float) $row['total'] : null;
                [$letter, $label] = Enrollment::gradeBand($total);

                return [
                    'subject_id' => isset($row['subject_id']) ? (string) $row['subject_id'] : null,
                    'subject_name' => $row['name'],
                    'attendance' => ['score' => $row['attendance'] ?? null, 'max' => 10],
                    'participation' => ['score' => $row['participation'] ?? null, 'max' => 30],
                    'midterm' => ['score' => $row['midterm'] ?? null, 'max' => 20],
                    'final' => ['score' => $row['final'] ?? null, 'max' => 40],
                    'total' => ['score' => $total, 'max' => 100],
                    'grade_letter' => $letter,
                    'grade_label' => $label,
                ];
            })->values();

        return response()->json(['result' => $result]);
    }
}
