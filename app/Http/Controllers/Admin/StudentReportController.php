<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Session;
use App\Models\User;
use App\Notifications\StudentReportNotification;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentReportController extends Controller
{
    /**
     * GET /admin/students/{student}/report
     * Compiled academic report across ALL the student's enrolled programs:
     * exams (quizzes), homework, attendance, and per-subject final grades.
     */
    public function show(User $student)
    {
        abort_unless($student->role === 'student', 404);

        $report = $this->buildReport($student);

        return view('admin.students.report', compact('student', 'report'));
    }

    /**
     * PATCH /admin/students/{student}/report
     * Persist admin edits back to the source records (quiz attempts, homework
     * submissions, subject final grades, attendance flags).
     */
    public function update(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $data = $request->validate([
            'quizzes'              => ['array'],
            'quizzes.*.score'      => ['nullable', 'numeric', 'min:0'],
            'quizzes.*.percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'homework'             => ['array'],
            'homework.*.grade'     => ['nullable', 'integer', 'min:0'],
            'homework.*.feedback'  => ['nullable', 'string', 'max:1000'],

            'subjects'             => ['array'],
            'subjects.*.final_grade' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'attendance'           => ['array'],
            'attendance.*.attended'=> ['nullable', 'boolean'],
        ]);

        // Ids must belong to THIS student before we touch any row.
        $ownedAttemptIds = QuizAttempt::where('student_id', $student->id)->pluck('id')->flip();
        $ownedSubmissionIds = HomeworkSubmission::where('student_id', $student->id)->pluck('id')->flip();
        $ownedEnrollmentIds = Enrollment::where('student_id', $student->id)->pluck('id')->flip();
        $ownedAttendanceIds = Attendance::where('student_id', $student->id)->pluck('id')->flip();

        DB::transaction(function () use ($data, $ownedAttemptIds, $ownedSubmissionIds, $ownedEnrollmentIds, $ownedAttendanceIds) {
            foreach ($data['quizzes'] ?? [] as $id => $row) {
                if (!isset($ownedAttemptIds[$id])) continue;
                $attempt = QuizAttempt::find($id);
                if (!$attempt) continue;
                if (array_key_exists('score', $row) && $row['score'] !== null) {
                    $attempt->score = $row['score'];
                }
                if (array_key_exists('percentage', $row) && $row['percentage'] !== null) {
                    $attempt->percentage = $row['percentage'];
                    $attempt->passed = $row['percentage'] >= 50;
                }
                $attempt->save();
            }

            foreach ($data['homework'] ?? [] as $id => $row) {
                if (!isset($ownedSubmissionIds[$id])) continue;
                $sub = HomeworkSubmission::find($id);
                if (!$sub) continue;
                if (array_key_exists('grade', $row))    $sub->grade = $row['grade'];
                if (array_key_exists('feedback', $row)) $sub->feedback = $row['feedback'];
                $sub->save();
            }

            foreach ($data['subjects'] ?? [] as $id => $row) {
                if (!isset($ownedEnrollmentIds[$id])) continue;
                if (!array_key_exists('final_grade', $row) || $row['final_grade'] === null) continue;
                $enr = Enrollment::find($id);
                if ($enr) $enr->updateFinalGrade((float) $row['final_grade']);
            }

            foreach ($data['attendance'] ?? [] as $id => $row) {
                if (!isset($ownedAttendanceIds[$id])) continue;
                $att = Attendance::find($id);
                if (!$att) continue;
                $att->attended = (bool) ($row['attended'] ?? false);
                $att->save();
            }
        });

        return redirect()->route('admin.students.report.show', $student)
            ->with('success', 'تم حفظ تعديلات التقرير.');
    }

    /**
     * POST /admin/students/{student}/report/send
     * Deliver the report to the student: in-app notification + email, with a
     * generated PDF attached.
     */
    public function send(Request $request, User $student)
    {
        abort_unless($student->role === 'student', 404);

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:2000'],
        ]);

        $report = $this->buildReport($student);
        $pdf = Pdf::loadView('admin.students.report-pdf', [
            'student' => $student,
            'report'  => $report,
            'note'    => $data['note'] ?? null,
        ]);
        $pdfData = $pdf->output();

        $student->notify(new StudentReportNotification(
            summary: $report['summary'],
            note: $data['note'] ?? null,
            pdf: $pdfData,
            hasEmail: filter_var($student->email, FILTER_VALIDATE_EMAIL) !== false,
        ));

        return redirect()->route('admin.students.report.show', $student)
            ->with('success', 'تم إرسال التقرير للمتدرب.');
    }

    /**
     * GET /admin/students/{student}/report/pdf
     * Download the report as a PDF (no send).
     */
    public function pdf(User $student)
    {
        abort_unless($student->role === 'student', 404);

        $report = $this->buildReport($student);
        $filename = 'report-' . $student->id . '-' . now()->format('Ymd') . '.pdf';

        return Pdf::loadView('admin.students.report-pdf', [
            'student' => $student,
            'report'  => $report,
            'note'    => null,
        ])->download($filename);
    }

    /**
     * Assemble the report data across all of the student's enrolled programs.
     *
     * @return array{summary: array, subjects: \Illuminate\Support\Collection, quizzes: \Illuminate\Support\Collection, homework: \Illuminate\Support\Collection, attendance: array}
     */
    private function buildReport(User $student): array
    {
        $programIds = $student->allProgramIds();

        // ── Per-subject enrollments + final grade (editable) ────────────────
        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['subject:id,name_ar,name_en,code,program_id,term_id'])
            ->get();

        $subjects = $enrollments->map(fn($e) => [
            'enrollment_id' => $e->id,
            'subject_id'    => $e->subject_id,
            'name'          => $e->subject->name_ar ?? $e->subject->name_en ?? '—',
            'code'          => $e->subject->code ?? null,
            'status'        => $e->status,
            'final_grade'   => $e->final_grade,
            'grade_letter'  => $e->grade_letter,
        ])->values();

        // ── Exams (quiz attempts) ───────────────────────────────────────────
        $attempts = QuizAttempt::where('student_id', $student->id)
            ->whereNotNull('submitted_at')
            ->with(['quiz:id,title_ar,subject_id,program_id'])
            ->latest('submitted_at')
            ->get();

        $quizzes = $attempts->map(fn($a) => [
            'attempt_id'  => $a->id,
            'quiz_id'     => $a->quiz_id,
            'title'       => $a->quiz->title_ar ?? '—',
            'score'       => $a->score,
            'percentage'  => $a->percentage,
            'passed'      => $a->passed,
            'released'    => $a->isReleased(),
            'submitted_at'=> $a->submitted_at?->format('Y/m/d'),
        ])->values();

        // ── Homework submissions ────────────────────────────────────────────
        $submissions = HomeworkSubmission::where('student_id', $student->id)
            ->with(['homework:id,title_ar,title_en,subject_id,program_id'])
            ->latest('submitted_at')
            ->get();

        $homework = $submissions->map(fn($s) => [
            'submission_id' => $s->id,
            'homework_id'   => $s->homework_id,
            'title'         => $s->homework->title_ar ?? $s->homework->title_en ?? '—',
            'grade'         => $s->grade,
            'max_grade'     => $s->max_grade,
            'feedback'      => $s->feedback,
            'submitted_at'  => $s->submitted_at?->format('Y/m/d'),
        ])->values();

        // ── Attendance ──────────────────────────────────────────────────────
        $attendances = Attendance::where('student_id', $student->id)
            ->with(['session:id,title_ar,title_en,scheduled_at'])
            ->get();

        $totalSessions    = $attendances->count();
        $attendedSessions = $attendances->where('attended', true)->count();
        $attendanceRate   = $totalSessions > 0
            ? round(($attendedSessions / $totalSessions) * 100, 1)
            : 0;

        $attendanceRows = $attendances->map(fn($a) => [
            'attendance_id' => $a->id,
            'session'       => $a->session->title_ar ?? $a->session->title_en ?? ('محاضرة #' . $a->session_id),
            'scheduled_at'  => $a->session?->scheduled_at
                ? \Carbon\Carbon::parse($a->session->scheduled_at)->format('Y/m/d')
                : null,
            'attended'      => (bool) $a->attended,
        ])->values();

        // ── Summary (used by notification/email/PDF header) ─────────────────
        $gradedQuizzes = $quizzes->whereNotNull('percentage');
        $avgQuiz = $gradedQuizzes->count() > 0
            ? round($gradedQuizzes->avg('percentage'), 1)
            : null;

        $summary = [
            'programs_count'    => $programIds->count(),
            'subjects_count'    => $subjects->count(),
            'quizzes_count'     => $quizzes->count(),
            'avg_quiz'          => $avgQuiz,
            'homework_count'    => $homework->count(),
            'attendance_rate'   => $attendanceRate,
            'attended_sessions' => $attendedSessions,
            'total_sessions'    => $totalSessions,
        ];

        return [
            'summary'    => $summary,
            'subjects'   => $subjects,
            'quizzes'    => $quizzes,
            'homework'   => $homework,
            'attendance' => [
                'rate'     => $attendanceRate,
                'attended' => $attendedSessions,
                'total'    => $totalSessions,
                'rows'     => $attendanceRows,
            ],
        ];
    }
}
