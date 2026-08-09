<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\HomeworkSubmission;
use App\Models\QuizAttempt;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Per-student academic report for the TEACHER side.
 *
 * Mirrors the admin report (grades / exams / homework / attendance, all
 * editable) but every query is scoped to what this teacher actually teaches:
 * their own sessions and their own assigned subjects. A teacher never sees or
 * edits a record belonging to another teacher's subject.
 */
class StudentReportController extends Controller
{
    /**
     * GET /teacher/students/{student}/report
     */
    public function show(User $student)
    {
        $teacher = auth()->user();
        $scope   = $this->scope($teacher);

        $this->authorizeStudent($student, $scope);

        $report = $this->buildReport($student, $scope);

        return view('teacher.students.report', compact('student', 'report'));
    }

    /**
     * PATCH /teacher/students/{student}/report
     * Persists edits back to the source records, after re-checking that every
     * submitted id belongs to this student AND falls inside the teacher's scope.
     */
    public function update(Request $request, User $student)
    {
        $teacher = auth()->user();
        $scope   = $this->scope($teacher);

        $this->authorizeStudent($student, $scope);

        $data = $request->validate([
            'quizzes'                => ['array'],
            'quizzes.*.score'        => ['nullable', 'numeric', 'min:0'],
            'quizzes.*.percentage'   => ['nullable', 'numeric', 'min:0', 'max:100'],

            'homework'               => ['array'],
            'homework.*.grade'       => ['nullable', 'integer', 'min:0'],
            'homework.*.feedback'    => ['nullable', 'string', 'max:1000'],

            'subjects'               => ['array'],
            'subjects.*.final_grade' => ['nullable', 'numeric', 'min:0', 'max:100'],

            'attendance'             => ['array'],
            'attendance.*.attended'  => ['nullable', 'boolean'],
        ]);

        // Allow-lists: owned by the student *and* inside the teacher's scope.
        $allowedAttempts = $this->scopedAttempts($student, $scope)->pluck('id')->flip();
        $allowedSubs     = $this->scopedSubmissions($student, $scope)->pluck('id')->flip();
        $allowedEnrolls  = $this->scopedEnrollments($student, $scope)->pluck('id')->flip();
        $allowedAtts     = $this->scopedAttendances($student, $scope)->pluck('id')->flip();

        DB::transaction(function () use ($data, $allowedAttempts, $allowedSubs, $allowedEnrolls, $allowedAtts) {
            foreach ($data['quizzes'] ?? [] as $id => $row) {
                if (!isset($allowedAttempts[$id])) continue;
                $attempt = QuizAttempt::find($id);
                if (!$attempt) continue;
                if (array_key_exists('score', $row) && $row['score'] !== null) {
                    $attempt->score = $row['score'];
                }
                if (array_key_exists('percentage', $row) && $row['percentage'] !== null) {
                    $attempt->percentage = $row['percentage'];
                    $attempt->passed     = $row['percentage'] >= 50;
                }
                $attempt->save();
            }

            foreach ($data['homework'] ?? [] as $id => $row) {
                if (!isset($allowedSubs[$id])) continue;
                $sub = HomeworkSubmission::find($id);
                if (!$sub) continue;
                if (array_key_exists('grade', $row))    $sub->grade    = $row['grade'];
                if (array_key_exists('feedback', $row)) $sub->feedback = $row['feedback'];
                $sub->save();
            }

            foreach ($data['subjects'] ?? [] as $id => $row) {
                if (!isset($allowedEnrolls[$id])) continue;
                if (!array_key_exists('final_grade', $row) || $row['final_grade'] === null) continue;
                $enr = Enrollment::find($id);
                if ($enr) $enr->updateFinalGrade((float) $row['final_grade']);
            }

            foreach ($data['attendance'] ?? [] as $id => $row) {
                if (!isset($allowedAtts[$id])) continue;
                $att = Attendance::find($id);
                if (!$att) continue;
                $att->attended = (bool) ($row['attended'] ?? false);
                $att->save();
            }
        });

        return redirect()->route('teacher.students.report.show', $student)
            ->with('success', 'تم حفظ تعديلات التقرير.');
    }

    // ── Scope helpers ───────────────────────────────────────────────────────

    /**
     * What this teacher is allowed to touch: their own session ids, and the
     * subject ids they either are assigned to or actually teach sessions on.
     *
     * @return array{session_ids: \Illuminate\Support\Collection, subject_ids: \Illuminate\Support\Collection}
     */
    private function scope(User $teacher): array
    {
        $sessions = Session::where('teacher_id', $teacher->id)
            ->get(['id', 'subject_id']);

        $subjectIds = $teacher->assignedSubjects()->pluck('subjects.id')
            ->merge($sessions->pluck('subject_id')->filter())
            ->unique()->values();

        return [
            'session_ids' => $sessions->pluck('id'),
            'subject_ids' => $subjectIds,
        ];
    }

    /** A teacher may only open the report of a student they actually teach. */
    private function authorizeStudent(User $student, array $scope): void
    {
        abort_unless($student->role === 'student', 404);

        $teaches = Attendance::where('student_id', $student->id)
                ->whereIn('session_id', $scope['session_ids'])->exists()
            || Enrollment::where('student_id', $student->id)
                ->whereIn('subject_id', $scope['subject_ids'])->exists();

        abort_unless($teaches, 403, 'هذا المتدرب ليس ضمن طلابك.');
    }

    private function scopedEnrollments(User $student, array $scope)
    {
        return Enrollment::where('student_id', $student->id)
            ->whereIn('subject_id', $scope['subject_ids'])
            ->with(['subject:id,name_ar,name_en,code,program_id,term_id'])
            ->get();
    }

    private function scopedAttempts(User $student, array $scope)
    {
        return QuizAttempt::where('student_id', $student->id)
            ->whereNotNull('submitted_at')
            ->whereHas('quiz', fn($q) => $q->whereIn('subject_id', $scope['subject_ids']))
            ->with(['quiz:id,title_ar,subject_id,program_id'])
            ->latest('submitted_at')
            ->get();
    }

    private function scopedSubmissions(User $student, array $scope)
    {
        return HomeworkSubmission::where('student_id', $student->id)
            ->whereHas('homework', fn($q) => $q->whereIn('subject_id', $scope['subject_ids']))
            ->with(['homework:id,title_ar,title_en,subject_id,program_id'])
            ->latest('submitted_at')
            ->get();
    }

    private function scopedAttendances(User $student, array $scope)
    {
        return Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $scope['session_ids'])
            ->with(['session:id,title_ar,title_en,scheduled_at'])
            ->get();
    }

    /**
     * Assemble the report, restricted to the teacher's own subjects/sessions.
     */
    private function buildReport(User $student, array $scope): array
    {
        $subjects = $this->scopedEnrollments($student, $scope)->map(fn($e) => [
            'enrollment_id' => $e->id,
            'subject_id'    => $e->subject_id,
            'name'          => $e->subject->name_ar ?? $e->subject->name_en ?? '—',
            'code'          => $e->subject->code ?? null,
            'status'        => $e->status,
            'final_grade'   => $e->final_grade,
            'grade_letter'  => $e->grade_letter,
        ])->values();

        $quizzes = $this->scopedAttempts($student, $scope)->map(fn($a) => [
            'attempt_id'   => $a->id,
            'quiz_id'      => $a->quiz_id,
            'title'        => $a->quiz->title_ar ?? '—',
            'score'        => $a->score,
            'percentage'   => $a->percentage,
            'passed'       => $a->passed,
            'released'     => $a->isReleased(),
            'submitted_at' => $a->submitted_at?->format('Y/m/d'),
        ])->values();

        $homework = $this->scopedSubmissions($student, $scope)->map(fn($s) => [
            'submission_id' => $s->id,
            'homework_id'   => $s->homework_id,
            'title'         => $s->homework->title_ar ?? $s->homework->title_en ?? '—',
            'grade'         => $s->grade,
            'max_grade'     => $s->max_grade,
            'feedback'      => $s->feedback,
            'submitted_at'  => $s->submitted_at?->format('Y/m/d'),
        ])->values();

        $attendances      = $this->scopedAttendances($student, $scope);
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

        $gradedQuizzes = $quizzes->whereNotNull('percentage');
        $avgQuiz = $gradedQuizzes->count() > 0
            ? round($gradedQuizzes->avg('percentage'), 1)
            : null;

        $summary = [
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
