<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentGradesService;
use Illuminate\Http\Request;

class GradesController extends Controller
{
    /**
     * GET /api/v1/student/grades
     * Mirrors the web page student/grades section by section:
     * header → stats → sent_reports → subjects → note.
     */
    public function index(Request $request, StudentGradesService $grades)
    {
        abort_unless($request->user()->role === 'student', 403);

        $overview      = $grades->overview($request->user());
        $subjectGrades = collect($overview['subjectGrades'])->values();
        $sentReports   = $overview['sentReports'];

        // Stats cards are only shown on the web page when there are subject grades.
        $stats = $subjectGrades->isEmpty() ? [] : [
            [
                'key'     => 'average_percentage',
                'value'   => round((float) ($overview['avgPercentage'] ?? 0), 1),
                'display' => number_format($overview['avgPercentage'] ?? 0, 1) . '%',
                'label'   => 'متوسط الدرجات المعروضة',
            ],
            [
                'key'     => 'subjects_count',
                'value'   => $subjectGrades->count(),
                'display' => (string) $subjectGrades->count(),
                'label'   => 'مقررات لها درجات',
            ],
            [
                'key'     => 'quiz_attempts',
                'value'   => $overview['totalQuizzes'],
                'display' => (string) $overview['totalQuizzes'],
                'label'   => 'محاولات اختبار مسلّمة',
            ],
            [
                'key'     => 'graded_evaluations',
                'value'   => $overview['totalEvaluations'],
                'display' => (string) $overview['totalEvaluations'],
                'label'   => 'تقييمات مصحّحة',
            ],
        ];

        $subjects = $subjectGrades->map(function ($data) {
            $subject = $data['subject'];
            $isFinal = $data['final_grade'] !== null;
            $pct     = (float) $data['percentage'];

            return [
                'subject_id'        => (string) $subject->id,
                'subject_name'      => $subject->name_ar ?? $subject->name,
                'teacher'           => $subject->teacher ? [
                    'id'    => (string) $subject->teacher->id,
                    'name'  => $subject->teacher->name,
                    'label' => 'المدرب: ' . $subject->teacher->name,
                ] : null,
                'percentage'        => $pct,
                'score'             => rtrim(rtrim(number_format($pct, 2, '.', ''), '0'), '.'),
                'score_suffix'      => $isFinal ? '/ 100' : '%',
                'score_label'       => $isFinal ? 'الدرجة النهائية من 100' : 'نسبة التقييمات والاختبارات',
                'progress'          => max(0, min(100, $pct)),
                'is_final'          => $isFinal,
                'final_grade'       => $isFinal ? (float) $data['final_grade'] : null,
                'grade_label'       => $data['grade_label'],
                'grade_level'       => $this->gradeLevel($pct),
                'source'            => $isFinal ? 'final_grade' : 'calculated',
                'source_label'      => $isFinal ? 'درجة نهائية مسجّلة' : 'نسبة محسوبة',
                'evaluations_count' => $data['evaluations']->count(),
                'attempts_count'    => $data['attempts']->count(),
                'counts_label'      => $data['evaluations']->count() . ' تقييم · ' . $data['attempts']->count() . ' محاولة اختبار',
            ];
        });

        $reports = $sentReports->map(fn ($report) => [
            'id'            => (string) $report->id,
            'teacher_name'  => $report->teacher_name,
            'sent_at'       => $report->sent_at?->toIso8601String(),
            'sent_at_label' => $report->sent_at?->translatedFormat('j F Y — g:i A'),
            'is_new'        => $report->is_unread,
            'rows'          => $report->rows->map(fn ($row) => [
                'name'          => $row['name'],
                'attendance'    => $row['attendance'] ?? null,
                'participation' => $row['participation'] ?? null,
                'midterm'       => $row['midterm'] ?? null,
                'final'         => $row['final'] ?? null,
                'total'         => $row['total'] ?? null,
                'total_max'     => 100,
            ])->values(),
        ])->values();

        return response()->json(['success' => true, 'data' => [
            'header' => [
                'eyebrow' => 'سجلك الأكاديمي',
                'title'   => 'الدرجات والتقييمات',
                'intro'   => 'درجاتك في مكان واحد، لمتابعة تقدمك في كل مقرر.',
            ],
            'stats' => $stats,
            'sent_reports' => [
                'title'       => 'درجات أرسلها المعلم',
                'subtitle'    => 'تفاصيل الدرجات كما اعتمدها المعلم وأرسلها إليك.',
                'count'       => $reports->count(),
                'count_label' => $reports->count() . ' إرسال',
                'columns'     => [
                    ['key' => 'name',          'label' => 'المقرر'],
                    ['key' => 'attendance',    'label' => 'الحضور'],
                    ['key' => 'participation', 'label' => 'المشاركة'],
                    ['key' => 'midterm',       'label' => 'النصفي'],
                    ['key' => 'final',         'label' => 'النهائي'],
                    ['key' => 'total',         'label' => 'المجموع'],
                ],
                'items'       => $reports,
            ],
            'subjects' => [
                'title'       => 'درجات المقررات',
                'subtitle'    => 'اطّلع على درجتك وتقديرك في كل مقرر.',
                'count'       => $subjects->count(),
                'count_label' => $subjects->count() . ' مقرر',
                'empty'       => $subjects->isNotEmpty() ? null : [
                    'title'   => 'درجاتك ستظهر هنا',
                    'message' => $reports->isNotEmpty()
                        ? 'الدرجات التي أرسلها المعلم موضّحة بالأعلى. وعندما تُسجَّل درجتك النهائية أو تُصحّح تقييماتك، ستجد ملخّص المقررات هنا.'
                        : 'عندما يسجّل المدرب درجتك النهائية أو تُصحّح تقييماتك، ستجد درجات المقررات في هذه الصفحة.',
                ],
                'items'       => $subjects,
            ],
            'note' => [
                'title'   => 'كيف تُعرض درجاتك؟',
                'message' => 'تظهر الدرجة النهائية عند تسجيلها من المدرب. قبل ذلك، تظهر النسبة المحسوبة من التقييمات والاختبارات المتاحة. للاستفسار عن درجتك، تواصل مع مدرب المقرر.',
            ],
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
