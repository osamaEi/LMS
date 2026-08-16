<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Subject;
use App\Models\User;
use App\Services\AttendanceLimitService;
use Illuminate\Support\Facades\DB;

/**
 * Absence standing for a teacher's own subjects: who is already barred
 * (محروم) and how many more sessions each student can still miss.
 *
 * Read-only — the limit itself and any exemptions are managed by the admin.
 */
class AttendanceStatusController extends Controller
{
    /** GET /teacher/attendance-status — the teacher's subjects as cards. */
    public function index()
    {
        $teacher    = auth()->user();
        $subjectIds = $this->subjectIds($teacher);

        $subjects = Subject::whereIn('id', $subjectIds)
            ->orderBy('name_ar')
            ->get(['id', 'name_ar', 'name_en', 'code', 'absence_limit_percent'])
            ->map(function ($s) {
                $stats = $this->subjectStats($s->id);

                return [
                    'id'       => $s->id,
                    'name'     => $s->name_ar ?: $s->name_en,
                    'code'     => $s->code,
                    'limit'    => AttendanceLimitService::limitForSubject($s->id),
                    'custom'   => $s->absence_limit_percent !== null,
                    'sessions' => $stats['sessions'],
                    'students' => $stats['students'],
                    'banned'   => $stats['banned'],
                    'at_risk'  => $stats['at_risk'],
                ];
            });

        $enabled = AttendanceLimitService::isEnabled();

        return view('teacher.attendance-status.index', compact('subjects', 'enabled'));
    }

    /** GET /teacher/attendance-status/{subject} — the students of one subject. */
    public function show(Subject $subject)
    {
        $teacher = auth()->user();

        abort_unless($this->subjectIds($teacher)->contains($subject->id), 403,
            'هذه المادة ليست ضمن موادك.');

        $limit    = AttendanceLimitService::limitForSubject($subject->id);
        $enabled  = AttendanceLimitService::isEnabled();
        $sessions = $this->sessionIds($subject->id);

        $students = $this->studentRows($subject->id, $sessions, $limit);

        return view('teacher.attendance-status.show', compact(
            'subject', 'students', 'limit', 'enabled'
        ) + ['totalSessions' => $sessions->count()]);
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    /** Subjects this teacher is assigned to, or actually teaches sessions on. */
    private function subjectIds(User $teacher)
    {
        return $teacher->assignedSubjects()->pluck('subjects.id')
            ->merge(Session::where('teacher_id', $teacher->id)->pluck('subject_id')->filter())
            ->unique()->values();
    }

    private function sessionIds(int $subjectId)
    {
        return Session::where('subject_id', $subjectId)->pluck('id');
    }

    /** Headline counts for one subject's card. */
    private function subjectStats(int $subjectId): array
    {
        $sessions = $this->sessionIds($subjectId);
        $limit    = AttendanceLimitService::limitForSubject($subjectId);
        $rows     = $this->studentRows($subjectId, $sessions, $limit);

        return [
            'sessions' => $sessions->count(),
            'students' => $rows->count(),
            'banned'   => $rows->where('banned', true)->count(),
            // Not barred yet, but one or two absences away from it.
            'at_risk'  => $rows->where('banned', false)->where('remaining', '<=', 2)->count(),
        ];
    }

    /**
     * One row per student in the subject: their unexcused absences, the
     * percentage that represents, and how many more sessions they may miss
     * before crossing the limit.
     */
    private function studentRows(int $subjectId, $sessionIds, float $limit)
    {
        if ($sessionIds->isEmpty()) {
            return collect();
        }

        // Sessions covered by an approved apology don't count as absences.
        $excused = DB::table('attendance_apologies')
            ->select('student_id', 'session_id')
            ->where('status', 'approved');

        $rows = DB::table('attendances as a')
            ->join('users as u', 'u.id', '=', 'a.student_id')
            ->leftJoinSub($excused, 'ex', function ($join) {
                $join->on('ex.student_id', '=', 'a.student_id')
                     ->on('ex.session_id', '=', 'a.session_id');
            })
            ->whereIn('a.session_id', $sessionIds)
            ->groupBy('a.student_id', 'u.name', 'u.email')
            ->select([
                'a.student_id',
                'u.name as name',
                'u.email as email',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN a.attended = 1 THEN 1 ELSE 0 END) as attended'),
                DB::raw('SUM(CASE WHEN a.attended = 0 AND ex.session_id IS NULL THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN a.attended = 0 AND ex.session_id IS NOT NULL THEN 1 ELSE 0 END) as excused'),
            ])
            ->get();

        // Admin exemptions lift the ban for a specific student.
        $exempt = DB::table('attendance_exemptions')
            ->where('subject_id', $subjectId)
            ->pluck('student_id')
            ->flip();

        // The limit is a percentage of *all* the subject's sessions, so the
        // allowance is fixed for everyone regardless of how many they attended.
        $totalSessions = $sessionIds->count();
        $allowed       = (int) floor($totalSessions * $limit / 100);

        return $rows->map(function ($r) use ($exempt, $totalSessions, $allowed, $limit) {
                $r->percent   = $totalSessions > 0 ? round($r->absent / $totalSessions * 100, 1) : 0;
                $r->exempt    = $exempt->has($r->student_id);
                $r->exceeded  = $r->percent > $limit;
                $r->banned    = $r->exceeded && !$r->exempt;
                $r->allowed   = $allowed;
                // How many more sessions they may still miss.
                $r->remaining = max(0, $allowed - $r->absent);

                return $r;
            })
            ->sortByDesc('percent')
            ->values();
    }
}
