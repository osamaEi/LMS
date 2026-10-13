<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\ProgramClass;
use App\Models\Subject;
use App\Models\User;
use App\Services\AttendanceLimitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

/**
 * Absence standing for a teacher's own subjects: who is already barred
 * (محروم) and how many more sessions each student can still miss.
 *
 * Read-only — the limit itself and any exemptions are managed by the admin.
 */
class AttendanceStatusController extends Controller
{
    /** Subject attendance cards grouped by the teacher's classes. */
    public function index()
    {
        $teacher    = auth()->user();
        $subjectIds = $this->subjectIds($teacher);

        $subjects = Subject::whereIn('id', $subjectIds)
            ->orderBy('name_ar')
            ->with(['term', 'terms'])
            ->get()
            ->flatMap(function ($s) use ($teacher) {
                return $this->subjectClasses($s, $teacher)->map(function ($class) use ($s) {
                $stats = $this->subjectStats($s->id, $class->id);

                return [
                    'id'       => $s->id,
                    'class_id' => $class->id,
                    'class_name' => $class->name,
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
            });

        $enabled = AttendanceLimitService::isEnabled();

        return view('teacher.attendance-status.index', compact('subjects', 'enabled'));
    }

    /** Attendance for one subject within an authorized class. */
    public function show(Request $request, Subject $subject)
    {
        $teacher = auth()->user();

        abort_unless($this->subjectIds($teacher)->contains($subject->id), 403,
            'هذه المادة ليست ضمن موادك.');

        $validated = $request->validate(['class_id' => 'nullable|integer|min:1']);
        if (empty($validated['class_id'])) {
            return redirect()->route('teacher.attendance-status.index');
        }
        $selectedClass = $this->subjectClasses($subject, $teacher)->firstWhere('id', $validated['class_id']);
        abort_unless($selectedClass, 403);

        $limit    = AttendanceLimitService::limitForSubject($subject->id);
        $enabled  = AttendanceLimitService::isEnabled();
        $sessions = $this->sessionIds($subject->id, $selectedClass->id);

        $students = $this->studentRows($subject->id, $sessions, $limit);

        return view('teacher.attendance-status.show', compact(
            'subject', 'students', 'limit', 'enabled', 'selectedClass'
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

    private function subjectClasses(Subject $subject, User $teacher)
    {
        $taughtClassIds = Session::where('subject_id', $subject->id)
            ->where('teacher_id', $teacher->id)->whereNotNull('class_id')->pluck('class_id');

        return ProgramClass::where(function ($query) use ($subject, $teacher, $taughtClassIds) {
            $query->where(function ($assigned) use ($subject, $teacher) {
                $assigned->whereIn('id', $subject->classIds())->where('teacher_id', $teacher->id);
            })->orWhereIn('id', $taughtClassIds);
        })->orderBy('name')->get();
    }

    private function sessionIds(int $subjectId, int $classId)
    {
        return Session::where('subject_id', $subjectId)->where('class_id', $classId)
            ->where('teacher_id', auth()->id())->pluck('id');
    }

    /** Headline counts for one subject's card. */
    private function subjectStats(int $subjectId, int $classId): array
    {
        $sessions = $this->sessionIds($subjectId, $classId);
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
            ->join('class_sessions as cs', 'cs.id', '=', 'a.session_id')
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
                DB::raw("SUM(CASE WHEN a.attended = 0 AND (cs.ended_at IS NOT NULL OR cs.status = 'completed') AND ex.session_id IS NULL THEN 1 ELSE 0 END) as absent"),
                DB::raw("SUM(CASE WHEN a.attended = 0 AND (cs.ended_at IS NOT NULL OR cs.status = 'completed') AND ex.session_id IS NOT NULL THEN 1 ELSE 0 END) as excused"),
            ])
            ->get();

        // Admin exemptions lift the ban for a specific student.
        $exempt = DB::table('attendance_exemptions')
            ->where('subject_id', $subjectId)
            ->pluck('student_id')
            ->flip();

        // Use the same class-scoped sessions for the denominator and absences.
        $totalSessions = $sessionIds->count();
        $allowed       = (int) floor($totalSessions * $limit / 100);

        return $rows->map(function ($r) use ($exempt, $totalSessions, $allowed, $limit) {
                $r->absent = (int) $r->absent;
                $r->excused = (int) $r->excused;
                $r->percent   = $totalSessions > 0 ? round($r->absent / $totalSessions * 100, 1) : 0;
                $r->exempt    = $exempt->has($r->student_id);
                $r->exceeded  = AttendanceLimitService::exceedsLimit((int) $r->absent, $totalSessions, $limit);
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
