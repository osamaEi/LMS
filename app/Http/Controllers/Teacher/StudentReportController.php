<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\HomeworkSubmission;
use App\Models\ParticipationMark;
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

            // Manual participation items — existing rows, then newly added ones.
            'participation'                => ['array'],
            'participation.*.title'        => ['nullable', 'string', 'max:255'],
            'participation.*.grade'        => ['nullable', 'numeric', 'min:0'],
            'participation.*.max_grade'    => ['nullable', 'integer', 'min:1', 'max:1000'],
            'participation.*.delete'       => ['nullable', 'boolean'],

            'new_participation'             => ['array'],
            'new_participation.*.title'     => ['nullable', 'string', 'max:255'],
            'new_participation.*.grade'     => ['nullable', 'numeric', 'min:0'],
            'new_participation.*.max_grade' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'new_participation.*.subject_id'=> ['nullable', 'integer'],
        ]);

        // Allow-lists: owned by the student *and* inside the teacher's scope.
        $allowedAttempts = $this->scopedAttempts($student, $scope)->pluck('id')->flip();
        $allowedSubs     = $this->scopedSubmissions($student, $scope)->pluck('id')->flip();
        $allowedEnrolls  = $this->scopedEnrollments($student, $scope)->pluck('id')->flip();
        $allowedAtts     = $this->scopedAttendances($student, $scope)->pluck('id')->flip();
        $allowedParts    = $this->scopedParticipation($student, $scope)->pluck('id')->flip();
        $allowedSubjects = $scope['subject_ids']->flip();
        $teacherId       = $teacher->id;
        $studentId       = $student->id;

        DB::transaction(function () use (
            $data, $allowedAttempts, $allowedSubs, $allowedEnrolls, $allowedAtts,
            $allowedParts, $allowedSubjects, $teacherId, $studentId
        ) {
            foreach ($data['quizzes'] ?? [] as $id => $row) {
                if (!isset($allowedAttempts[$id])) continue;
                $attempt = QuizAttempt::find($id);
                if (!$attempt) continue;

                if (array_key_exists('score', $row) && $row['score'] !== null) {
                    $attempt->score = $row['score'];

                    // The grade is the only field the teacher types, so derive the
                    // percentage from it against the quiz total.
                    $total = (float) ($attempt->quiz->total_marks ?? 0);
                    if ($total > 0) {
                        $attempt->percentage = round(min(100, $row['score'] / $total * 100), 2);
                        $attempt->passed     = $attempt->percentage >= 50;
                    }
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

            // Existing manual participation items: update or delete.
            foreach ($data['participation'] ?? [] as $id => $row) {
                if (!isset($allowedParts[$id])) continue;
                $mark = ParticipationMark::find($id);
                if (!$mark) continue;

                if (!empty($row['delete'])) {
                    $mark->delete();
                    continue;
                }
                if (array_key_exists('title', $row) && $row['title'] !== null) {
                    $mark->title = $row['title'];
                }
                if (array_key_exists('grade', $row) && $row['grade'] !== null) {
                    $mark->grade = $row['grade'];
                }
                if (!empty($row['max_grade'])) {
                    $mark->max_grade = $row['max_grade'];
                }
                $mark->save();
            }

            // Newly added items — need a title and a subject inside the teacher's scope.
            foreach ($data['new_participation'] ?? [] as $row) {
                $title     = trim((string) ($row['title'] ?? ''));
                $subjectId = $row['subject_id'] ?? null;
                if ($title === '' || !$subjectId || !isset($allowedSubjects[$subjectId])) continue;

                ParticipationMark::create([
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'teacher_id' => $teacherId,
                    'title'      => $title,
                    'grade'      => $row['grade'] ?? 0,
                    'max_grade'  => $row['max_grade'] ?? 10,
                ]);
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

    /** The classes (groups) this student belongs to, via student_programs. */
    private function studentClassIds(User $student)
    {
        return DB::table('student_programs')
            ->where('student_id', $student->id)
            ->whereNotNull('class_id')
            ->pluck('class_id')
            ->unique()
            ->values();
    }

    /**
     * Subjects to report on: the teacher's subjects that belong to one of the
     * student's classes, unioned with any subject the student has an explicit
     * enrollment in. Class membership is the primary source — most students are
     * attached to a class via student_programs and have no enrollment rows.
     *
     * @return \Illuminate\Support\Collection<int, array>
     */
    private function reportSubjects(User $student, array $scope)
    {
        $classIds    = $this->studentClassIds($student);
        $enrollments = $this->scopedEnrollments($student, $scope)->keyBy('subject_id');

        $subjects = \App\Models\Subject::whereIn('id', $scope['subject_ids'])
            ->with(['term:id,class_id', 'terms:id,class_id'])
            ->get(['id', 'name_ar', 'name_en', 'code', 'class_id', 'term_id']);

        return $subjects->filter(function ($subject) use ($classIds, $enrollments) {
                if ($enrollments->has($subject->id)) return true;      // explicit enrollment
                $subjectClasses = $subject->classIds();
                if ($subjectClasses->isEmpty()) return true;           // open to any class
                return $subjectClasses->intersect($classIds)->isNotEmpty();
            })
            ->map(function ($subject) use ($enrollments) {
                $e = $enrollments->get($subject->id);

                return [
                    'enrollment_id' => $e->id ?? null,
                    'subject_id'    => $subject->id,
                    'name'          => $subject->name_ar ?? $subject->name_en ?? '—',
                    'code'          => $subject->code ?? null,
                    'status'        => $e->status ?? 'active',
                    'final_grade'   => $e->final_grade ?? null,
                    'grade_letter'  => $e->grade_letter ?? null,
                ];
            })
            ->values();
    }

    private function scopedAttempts(User $student, array $scope)
    {
        return QuizAttempt::where('student_id', $student->id)
            ->whereNotNull('submitted_at')
            ->whereHas('quiz', fn($q) => $q->whereIn('subject_id', $scope['subject_ids']))
            ->with(['quiz:id,title_ar,subject_id,program_id,type,total_marks'])
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

    private function scopedParticipation(User $student, array $scope)
    {
        return ParticipationMark::where('student_id', $student->id)
            ->whereIn('subject_id', $scope['subject_ids'])
            ->orderBy('id')
            ->get();
    }

    private function scopedAttendances(User $student, array $scope)
    {
        return Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $scope['session_ids'])
            ->with(['session:id,title_ar,title_en,scheduled_at,subject_id'])
            ->get();
    }

    /**
     * Weight of each component of the 100-mark distribution.
     * الحضور 10 — المشاركة 30 (قصيرة 15 + واجبات 15) — نصفي 20 — نهائي 40
     */
    private const WEIGHTS = [
        'attendance' => 10,
        'quizzes'    => 15,
        'homework'   => 15,
        'midterm'    => 20,
        'final'      => 40,
    ];

    /**
     * Mark distribution per subject: attendance, participation (short quizzes +
     * homework), midterm, final, and the resulting total out of 100.
     *
     * Every component is a percentage of what the student earned in that bucket
     * scaled onto its weight; a bucket with nothing recorded scores 0.
     */
    private function buildMarks($subjects, $quizzes, $homework, $attendances, $participation)
    {
        $rows = $subjects->map(function ($sub) use ($quizzes, $homework, $attendances, $participation) {
            $sid = $sub['subject_id'];

            // Attendance: attended / total sessions of this subject.
            $subAtt   = $attendances->filter(fn($a) => ($a->session->subject_id ?? null) === $sid);
            $attTotal = $subAtt->count();
            $attPct   = $attTotal > 0 ? $subAtt->where('attended', true)->count() / $attTotal : 0;

            // Short quizzes / midterm / final: average percentage per type.
            $ofType = fn(array $types) => $quizzes
                ->filter(fn($q) => $q['subject_id'] === $sid
                    && in_array($q['type'], $types, true)
                    && $q['percentage'] !== null);

            $avg = function ($set) {
                return $set->count() > 0 ? $set->avg('percentage') / 100 : 0;
            };

            $quizPct    = $avg($ofType(['quiz', 'paper']));
            $midtermPct = $avg($ofType(['midterm']));
            $finalPct   = $avg($ofType(['exam']));

            // Homework: sum of grades over sum of max grades.
            $subHw   = $homework->filter(fn($h) => $h['subject_id'] === $sid && $h['grade'] !== null);
            $hwMax   = $subHw->sum(fn($h) => $h['max_grade'] ?: 0);
            $hwPct   = $hwMax > 0 ? $subHw->sum('grade') / $hwMax : 0;

            // Manual participation items: sum of grades over sum of max grades.
            $subPart  = $participation->where('subject_id', $sid);
            $partMax  = $subPart->sum('max_grade');
            $partPct  = $partMax > 0 ? $subPart->sum('grade') / $partMax : 0;

            $marks = [
                'attendance' => round($attPct    * self::WEIGHTS['attendance'], 1),
                'quizzes'    => round($quizPct   * self::WEIGHTS['quizzes'], 1),
                'homework'   => round($hwPct     * self::WEIGHTS['homework'], 1),
                'midterm'    => round($midtermPct * self::WEIGHTS['midterm'], 1),
                'final'      => round($finalPct  * self::WEIGHTS['final'], 1),
            ];

            // المشاركة = quizzes + homework + manual items. The manual items take
            // a proportional share of the same 30 marks rather than adding a new
            // weight, so the column can never exceed its cap.
            $partWeight = self::WEIGHTS['quizzes'] + self::WEIGHTS['homework'];
            $buckets    = [];
            if ($quizPct !== 0 || $ofType(['quiz', 'paper'])->isNotEmpty()) $buckets[] = $quizPct;
            if ($hwMax > 0)   $buckets[] = $hwPct;
            if ($partMax > 0) $buckets[] = $partPct;

            $participationMark = count($buckets) > 0
                ? round(array_sum($buckets) / count($buckets) * $partWeight, 1)
                : 0;

            $totals = $marks;
            $totals['quizzes']  = $participationMark;   // المشاركة as one figure
            unset($totals['homework']);

            return $marks + [
                'subject_id'    => $sid,
                'name'          => $sub['name'],
                'code'          => $sub['code'],
                'participation' => $participationMark,
                'total'         => round(array_sum($totals), 1),
            ];
        })->values();

        return [
            'weights' => self::WEIGHTS,
            'rows'    => $rows,
            'total'   => $rows->count() > 0 ? round($rows->avg('total'), 1) : null,
            'details' => $this->buildDetails($quizzes, $homework, $attendances, $participation),
        ];
    }

    /**
     * The individual records behind each column, for the drill-down modal.
     * Keyed by column so the view can hand one bucket to the modal on click.
     */
    private function buildDetails($quizzes, $homework, $attendances, $participation): array
    {
        $quizRows = fn(array $types) => $quizzes
            ->filter(fn($q) => in_array($q['type'], $types, true))
            ->map(fn($q) => [
                'id'    => $q['attempt_id'],
                'kind'  => 'quiz',
                'title' => $q['title'],
                'date'  => $q['submitted_at'],
                'score' => $q['score'],
                'total' => $q['total_marks'],
                'pct'   => $q['percentage'],
            ])->values();

        return [
            'attendance' => $attendances->map(fn($a) => [
                'id'       => $a->id,
                'kind'     => 'attendance',
                'title'    => $a->session->title_ar ?? $a->session->title_en ?? ('محاضرة #' . $a->session_id),
                'date'     => $a->session?->scheduled_at
                    ? \Carbon\Carbon::parse($a->session->scheduled_at)->format('Y/m/d')
                    : null,
                'attended' => (bool) $a->attended,
            ])->values(),

            'quizzes'  => $quizRows(['quiz', 'paper']),
            'midterm'  => $quizRows(['midterm']),
            'final'    => $quizRows(['exam']),

            'homework' => $homework->map(fn($h) => [
                'id'        => $h['submission_id'],
                'kind'      => 'homework',
                'title'     => $h['title'],
                'date'      => $h['submitted_at'],
                'grade'     => $h['grade'],
                'max_grade' => $h['max_grade'],
                'feedback'  => $h['feedback'],
            ])->values(),

            'manual' => $participation->map(fn($p) => [
                'id'         => $p->id,
                'kind'       => 'manual',
                'subject_id' => $p->subject_id,
                'title'      => $p->title,
                'grade'      => $p->grade,
                'max_grade'  => $p->max_grade,
            ])->values(),
        ];
    }

    /**
     * Assemble the report, restricted to the teacher's own subjects/sessions.
     */
    private function buildReport(User $student, array $scope): array
    {
        $subjects = $this->reportSubjects($student, $scope);

        $quizzes = $this->scopedAttempts($student, $scope)->map(fn($a) => [
            'attempt_id'   => $a->id,
            'quiz_id'      => $a->quiz_id,
            'subject_id'   => $a->quiz->subject_id ?? null,
            'type'         => $a->quiz->type ?? 'quiz',
            'total_marks'  => $a->quiz->total_marks ?? null,
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
            'subject_id'    => $s->homework->subject_id ?? null,
            'title'         => $s->homework->title_ar ?? $s->homework->title_en ?? '—',
            'grade'         => $s->grade,
            'max_grade'     => $s->max_grade,
            'feedback'      => $s->feedback,
            'submitted_at'  => $s->submitted_at?->format('Y/m/d'),
        ])->values();

        $participation    = $this->scopedParticipation($student, $scope);
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
            'marks'      => $this->buildMarks($subjects, $quizzes, $homework, $attendances, $participation),
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
