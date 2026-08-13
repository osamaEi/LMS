<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Setting;
use App\Models\Subject;
use App\Services\AttendanceLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin screen for the absence limit: set the allowed percentage, see who has
 * gone over it, and exempt individual students.
 */
class AttendanceLimitController extends Controller
{
    public function index(Request $request)
    {
        $enabled = AttendanceLimitService::isEnabled();
        $percent = AttendanceLimitService::allowedPercent();

        $subjectId = $request->integer('subject_id') ?: null;

        $subjects = Subject::orderBy('name_ar')->get(['id', 'name_ar', 'name_en']);
        $offenders = $this->offenders($percent, $subjectId);

        return view('admin.attendance-limit.index', compact(
            'enabled', 'percent', 'subjects', 'offenders', 'subjectId'
        ));
    }

    /** Persist the limit settings. */
    public function update(Request $request)
    {
        $data = $request->validate([
            'enabled' => ['nullable', 'boolean'],
            'percent' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        Setting::set(AttendanceLimitService::SETTING_ENABLED, $request->boolean('enabled') ? '1' : '0');
        Setting::set(AttendanceLimitService::SETTING_PERCENT, (string) $data['percent']);

        // The Setting model only seeds type/group on first write, so make sure
        // these two cast correctly whichever way they were created.
        Setting::where('key', AttendanceLimitService::SETTING_ENABLED)
            ->update(['type' => 'boolean', 'group' => 'attendance', 'label' => 'تفعيل حد الغياب']);
        Setting::where('key', AttendanceLimitService::SETTING_PERCENT)
            ->update(['type' => 'number', 'group' => 'attendance', 'label' => 'نسبة الغياب المسموحة']);

        Setting::clearCache();

        return back()->with('success', 'تم حفظ إعدادات حد الغياب.');
    }

    /** Let one student back into one subject's sessions. */
    public function exempt(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'reason'     => ['nullable', 'string', 'max:1000'],
        ]);

        DB::table('attendance_exemptions')->updateOrInsert(
            ['student_id' => $data['student_id'], 'subject_id' => $data['subject_id']],
            [
                'reason'     => $data['reason'] ?? null,
                'granted_by' => auth()->id(),
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );

        return back()->with('success', 'تم استثناء المتدرب وإعادة السماح له بحضور المحاضرات.');
    }

    /** Re-apply the ban to a student who was previously exempted. */
    public function revoke(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer'],
            'subject_id' => ['required', 'integer'],
        ]);

        DB::table('attendance_exemptions')
            ->where('student_id', $data['student_id'])
            ->where('subject_id', $data['subject_id'])
            ->delete();

        return back()->with('success', 'تم إلغاء الاستثناء.');
    }

    /**
     * Every student/subject pair whose unexcused absence rate is over the limit.
     *
     * Counting is done in SQL so this stays cheap: one row per student+subject
     * with totals, minus the sessions covered by an approved apology.
     */
    private function offenders(float $percent, ?int $subjectId)
    {
        $excused = DB::table('attendance_apologies')
            ->select('student_id', 'session_id')
            ->where('status', 'approved');

        $rows = DB::table('attendances as a')
            ->join('class_sessions as s', 's.id', '=', 'a.session_id')
            ->join('users as u', 'u.id', '=', 'a.student_id')
            ->join('subjects as sub', 'sub.id', '=', 's.subject_id')
            ->leftJoinSub($excused, 'ex', function ($join) {
                $join->on('ex.student_id', '=', 'a.student_id')
                     ->on('ex.session_id', '=', 'a.session_id');
            })
            ->when($subjectId, fn($q) => $q->where('s.subject_id', $subjectId))
            ->whereNotNull('s.subject_id')
            ->groupBy('a.student_id', 's.subject_id', 'u.name', 'u.email', 'sub.name_ar', 'sub.name_en')
            ->select([
                'a.student_id',
                's.subject_id',
                'u.name as student_name',
                'u.email as student_email',
                'sub.name_ar as subject_ar',
                'sub.name_en as subject_en',
                DB::raw('COUNT(*) as total'),
                // Absent, excluding sessions with an approved apology.
                DB::raw('SUM(CASE WHEN a.attended = 0 AND ex.session_id IS NULL THEN 1 ELSE 0 END) as absent'),
                DB::raw('SUM(CASE WHEN a.attended = 0 AND ex.session_id IS NOT NULL THEN 1 ELSE 0 END) as excused'),
            ])
            ->get();

        $exempt = DB::table('attendance_exemptions')
            ->get(['student_id', 'subject_id', 'reason'])
            ->keyBy(fn($e) => $e->student_id . '-' . $e->subject_id);

        return $rows
            ->map(function ($r) use ($exempt) {
                $r->percent = $r->total > 0 ? round($r->absent / $r->total * 100, 1) : 0;
                $key        = $r->student_id . '-' . $r->subject_id;
                $r->exempt  = $exempt->has($key);
                $r->reason  = $exempt->get($key)->reason ?? null;
                $r->subject = $r->subject_ar ?: $r->subject_en;

                return $r;
            })
            ->filter(fn($r) => $r->percent > $percent)
            ->sortByDesc('percent')
            ->values();
    }
}
