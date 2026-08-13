<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

/**
 * The absence limit: once a student misses more than the allowed percentage of a
 * subject's sessions, they are barred from that subject's remaining sessions.
 *
 * Only *unexcused* absences count — a session with an approved apology is
 * ignored — and an admin can exempt a student per subject to lift the ban.
 *
 * This is the single source of truth; both the web and API join gates call it.
 */
class AttendanceLimitService
{
    public const SETTING_ENABLED = 'attendance_limit_enabled';
    public const SETTING_PERCENT = 'attendance_limit_percent';

    /** Fallback when the setting has never been saved. */
    public const DEFAULT_PERCENT = 25;

    public static function isEnabled(): bool
    {
        return (bool) Setting::get(self::SETTING_ENABLED, false);
    }

    /** The global allowed unexcused-absence percentage. */
    public static function allowedPercent(): float
    {
        $value = Setting::get(self::SETTING_PERCENT, self::DEFAULT_PERCENT);

        return max(0, min(100, (float) $value));
    }

    /**
     * The limit that actually applies to one subject: its own override when set,
     * otherwise the global limit.
     */
    public static function limitForSubject(int $subjectId): float
    {
        $own = DB::table('subjects')->where('id', $subjectId)->value('absence_limit_percent');

        return $own !== null
            ? max(0, min(100, (float) $own))
            : self::allowedPercent();
    }

    /**
     * A student's absence standing in one subject.
     *
     * @return array{total:int, absent:int, excused:int, percent:float,
     *               limit:float, exceeded:bool, exempt:bool, blocked:bool}
     */
    public static function statusFor(int $studentId, int $subjectId): array
    {
        $rows = Attendance::where('attendances.student_id', $studentId)
            ->join('class_sessions', 'class_sessions.id', '=', 'attendances.session_id')
            ->where('class_sessions.subject_id', $subjectId)
            ->get(['attendances.attended', 'attendances.session_id']);

        $total = $rows->count();

        // Absences the student apologised for, and the apology was approved.
        $excusedIds = DB::table('attendance_apologies')
            ->where('student_id', $studentId)
            ->where('status', 'approved')
            ->whereIn('session_id', $rows->pluck('session_id'))
            ->pluck('session_id');

        $absentRows = $rows->where('attended', false);
        $excused    = $absentRows->whereIn('session_id', $excusedIds)->count();
        $absent     = $absentRows->count() - $excused;

        $limit    = self::limitForSubject($subjectId);
        $percent  = $total > 0 ? round($absent / $total * 100, 1) : 0.0;
        $exceeded = self::isEnabled() && $total > 0 && $percent > $limit;
        $exempt   = self::isExempt($studentId, $subjectId);

        return [
            'total'    => $total,
            'absent'   => $absent,
            'excused'  => $excused,
            'percent'  => $percent,
            'limit'    => $limit,
            'exceeded' => $exceeded,
            'exempt'   => $exempt,
            'blocked'  => $exceeded && !$exempt,
        ];
    }

    public static function isExempt(int $studentId, int $subjectId): bool
    {
        return DB::table('attendance_exemptions')
            ->where('student_id', $studentId)
            ->where('subject_id', $subjectId)
            ->exists();
    }

    /**
     * Whether this student is barred from a session, and why. Returns null when
     * they may join.
     */
    public static function blockReason(int $studentId, ?int $subjectId): ?string
    {
        if (!$subjectId || !self::isEnabled()) {
            return null;
        }

        $status = self::statusFor($studentId, $subjectId);

        if (!$status['blocked']) {
            return null;
        }

        return sprintf(
            'تم منعك من حضور محاضرات هذه المادة لتجاوز الحد المسموح للغياب (%s%% من %s%% المسموح بها). يرجى مراجعة الإدارة.',
            $status['percent'],
            $status['limit'],
        );
    }
}
