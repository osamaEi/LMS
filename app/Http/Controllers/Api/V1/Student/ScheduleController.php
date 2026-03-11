<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\WeeklySessionResource;
use App\Models\Attendance;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * GET /api/v1/student/schedule
     * Calendar events for student's sessions (existing, kept for compatibility)
     */
    public function index(Request $request)
    {
        $student = auth()->user();

        $query = Session::whereHas('subject.enrollments', function ($q) use ($student) {
            $q->where('student_id', $student->id);
        })->with('subject:id,name_ar,name_en,color');

        if ($request->has('start')) {
            $query->where('scheduled_at', '>=', $request->query('start'));
        }
        if ($request->has('end')) {
            $query->where('scheduled_at', '<=', $request->query('end'));
        }

        $sessions = $query->get()->map(fn($session) => [
            'id'             => $session->id,
            'title'          => $session->title,
            'start'          => $session->scheduled_at,
            'end'            => $session->scheduled_at
                ? Carbon::parse($session->scheduled_at)->addMinutes($session->duration_minutes ?? 120)
                : null,
            'color'          => $this->getStatusColor($session->status),
            'subject'        => $session->subject?->name,
            'type'           => $session->type,
            'status'         => $session->status,
            'session_number' => $session->session_number,
        ]);

        return response()->json(['success' => true, 'data' => $sessions]);
    }

    /**
     * GET /api/v1/student/schedule/weekly
     * This week's sessions with attendance rate and summary.
     *
     * Optional query param: ?week=2024-11-18  (any date inside the desired week)
     */
    public function weekly(Request $request)
    {
        $student = auth()->user();

        // Resolve week boundaries (Saturday → Friday for Arabic calendar, or Mon → Sun)
        $anchor     = $request->query('week') ? Carbon::parse($request->query('week')) : now();
        $weekStart  = $anchor->copy()->startOfWeek(Carbon::SATURDAY);
        $weekEnd    = $weekStart->copy()->endOfWeek(Carbon::FRIDAY);

        // Previous week boundaries (for change calculation)
        $prevStart  = $weekStart->copy()->subWeek();
        $prevEnd    = $weekEnd->copy()->subWeek();

        // ── Enrolled subject IDs ──────────────────────────────────────────────
        $enrolledSubjectIds = $student->enrollments()->pluck('subject_id');

        // ── Sessions this week ────────────────────────────────────────────────
        $sessions = Session::whereIn('subject_id', $enrolledSubjectIds)
            ->whereBetween('scheduled_at', [$weekStart, $weekEnd])
            ->with('subject:id,name_ar,name_en,color')
            ->orderBy('scheduled_at')
            ->get();

        // ── Attendance records keyed by session_id ────────────────────────────
        $sessionIds  = $sessions->pluck('id');
        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessionIds)
            ->get()
            ->keyBy('session_id');

        // ── Current week attendance rate ──────────────────────────────────────
        [$currentRate, $currentPresent, $currentAbsent, $currentExcused] =
            $this->calcWeekAttendance($student->id, $enrolledSubjectIds, $weekStart, $weekEnd);

        // ── Previous week attendance rate (for change) ────────────────────────
        [$prevRate] = $this->calcWeekAttendance($student->id, $enrolledSubjectIds, $prevStart, $prevEnd);

        $change    = round($currentRate - $prevRate, 1);
        $direction = $change > 0 ? 'up' : ($change < 0 ? 'down' : 'same');

        // ── Build response ────────────────────────────────────────────────────
        return response()->json([
            'success' => true,
            'data' => [
                'week_start' => $weekStart->toDateString(),
                'week_end'   => $weekEnd->toDateString(),
                'today'      => now()->toDateString(),

                'attendance_rate' => [
                    'current'   => $currentRate,
                    'previous'  => $prevRate,
                    'change'    => abs($change),
                    'direction' => $direction,
                ],

                'summary' => [
                    'present'      => $currentPresent,
                    'absent'       => $currentAbsent,
                    'excused'      => $currentExcused,
                    'upcoming'     => $sessions->filter(fn($s) => !$s->ended_at && !$s->started_at && $s->scheduled_at?->isFuture())->count(),
                    'total'        => $sessions->count(),
                ],

                'sessions' => WeeklySessionResource::collection($sessions)
                    ->additional(['attendances' => $attendances]),
            ],
        ]);
    }

    /**
     * GET /api/v1/student/schedule/calendar
     * Monthly calendar — each day shows attendance status.
     *
     * Query params:
     *   ?month=2024-11   (default: current month)
     */
    public function calendar(Request $request)
    {
        $student = auth()->user();

        $month      = $request->query('month') ? Carbon::parse($request->query('month') . '-01') : now()->startOfMonth();
        $monthStart = $month->copy()->startOfMonth();
        $monthEnd   = $month->copy()->endOfMonth();

        $enrolledSubjectIds = $student->enrollments()->pluck('subject_id');

        // All sessions in the month
        $sessions = Session::whereIn('subject_id', $enrolledSubjectIds)
            ->whereBetween('scheduled_at', [$monthStart, $monthEnd])
            ->select('id', 'subject_id', 'scheduled_at', 'started_at', 'ended_at')
            ->get();

        // All attendance records for those sessions
        $sessionIds  = $sessions->pluck('id');
        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessionIds)
            ->get()
            ->groupBy('session_id');

        // Group sessions by date
        $sessionsByDate = $sessions->groupBy(fn($s) => $s->scheduled_at->toDateString());

        // Build day-by-day array
        $days = [];
        $totalDays = $monthEnd->day;

        for ($day = 1; $day <= $totalDays; $day++) {
            $date        = $month->copy()->day($day);
            $dateString  = $date->toDateString();
            $daySessions = $sessionsByDate->get($dateString, collect());

            if ($daySessions->isEmpty()) {
                $days[] = [
                    'date'               => $dateString,
                    'day'                => $day,
                    'has_sessions'       => false,
                    'attendance_status'  => null,
                    'sessions_count'     => 0,
                ];
                continue;
            }

            // Determine day status from all sessions that day
            $statuses = $daySessions->map(function ($session) use ($attendances) {
                $att = $attendances->get($session->id)?->first();

                if (!$session->ended_at && !$session->started_at && $session->scheduled_at->isFuture()) {
                    return 'upcoming';
                }
                if (!$att) {
                    return 'absent';
                }
                if ($att->attended) {
                    return 'present';
                }
                return $att->notes ? 'excused' : 'absent';
            });

            // Priority: live > upcoming > present > excused > absent
            $status = 'absent';
            if ($statuses->contains('upcoming'))  $status = 'upcoming';
            if ($statuses->contains('excused'))   $status = 'excused';
            if ($statuses->contains('present'))   $status = 'present';
            // If today has a live session
            if ($daySessions->contains(fn($s) => $s->started_at && !$s->ended_at)) {
                $status = 'live';
            }

            $days[] = [
                'date'              => $dateString,
                'day'               => $day,
                'has_sessions'      => true,
                'attendance_status' => $status,  // present | absent | excused | upcoming | live
                'sessions_count'    => $daySessions->count(),
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'month'      => $month->format('Y-m'),
                'month_name' => $month->translatedFormat('F Y'),
                'today'      => now()->toDateString(),
                'days'       => $days,
            ],
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Calculate attendance rate + counts for a given week range.
     * Returns [rate, present_count, absent_count, excused_count]
     */
    private function calcWeekAttendance(int $studentId, $subjectIds, Carbon $start, Carbon $end): array
    {
        $sessions = Session::whereIn('subject_id', $subjectIds)
            ->whereBetween('scheduled_at', [$start, $end])
            ->whereNotNull('ended_at')          // only completed sessions count
            ->pluck('id');

        if ($sessions->isEmpty()) {
            return [0.0, 0, 0, 0];
        }

        $allAttendances = Attendance::where('student_id', $studentId)
            ->whereIn('session_id', $sessions)
            ->get();

        $present = $allAttendances->where('attended', true)->count();
        $excused = $allAttendances->where('attended', false)->whereNotNull('notes')->count();
        $absent  = $sessions->count() - $present - $excused;
        $absent  = max(0, $absent);

        $rate = round(($present / $sessions->count()) * 100, 1);

        return [$rate, $present, $absent, $excused];
    }

    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'live'      => '#EF4444',
            'completed' => '#10B981',
            'scheduled' => '#3B82F6',
            default     => '#6B7280',
        ];
    }
}
