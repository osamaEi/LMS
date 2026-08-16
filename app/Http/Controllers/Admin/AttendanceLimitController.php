<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Setting;
use App\Models\Subject;
use App\Services\AttendanceLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin screen for the absence limit, organised as three tabs over one URL:
 * the global setting, the per-subject overrides, and who has exceeded them.
 *
 * Program scoping (دبلومات / دورات) is shared by the subjects and offenders
 * tabs so both always talk about the same set of courses.
 */
class AttendanceLimitController extends Controller
{
    /** Course-like program types; everything else counts as a diploma. */
    private const COURSE_TYPES = ['course', 'english', 'training'];

    private const TABS = ['settings', 'subjects', 'offenders'];

    public function index(Request $request)
    {
        $ctx = $this->context($request);

        // Offenders are only computed for the tab that shows them.
        $offenders  = collect();
        $exemptions = collect();

        if ($ctx['tab'] === 'offenders') {
            $offenders  = $this->offenders($ctx);
            $exemptions = $this->exemptions($ctx['subjectIds']);
        }

        return view('admin.attendance-limit.index', $ctx + compact('offenders', 'exemptions'));
    }

    /** Persist the global limit settings. */
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

    /**
     * Per-subject overrides. A blank field clears the override so the subject
     * falls back to the global limit.
     */
    public function updateSubjects(Request $request)
    {
        $data = $request->validate([
            'limits'   => ['array'],
            'limits.*' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        foreach ($data['limits'] ?? [] as $subjectId => $value) {
            $percent = ($value === null || $value === '') ? null : (float) $value;

            Subject::where('id', $subjectId)->update(['absence_limit_percent' => $percent]);
        }

        return back()->with('success', 'تم حفظ حدود الغياب لكل مقرر.');
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

    /** Download the current offenders view as CSV. */
    public function export(Request $request): StreamedResponse
    {
        $ctx  = $this->context($request);
        $rows = $this->offenders($ctx);

        $name = 'attendance-limit-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');

            // BOM so Excel opens the Arabic columns as UTF-8.
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['المتدرب', 'البريد', 'المقرر', 'الغياب', 'إجمالي المحاضرات',
                           'غياب بعذر', 'النسبة %', 'الحد %', 'الحالة']);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->student_name,
                    $r->student_email,
                    $r->subject,
                    $r->absent,
                    $r->total,
                    $r->excused,
                    $r->percent,
                    $r->limit,
                    $r->exempt ? 'مستثنى' : 'ممنوع',
                ]);
            }

            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    // ── Shared request context ──────────────────────────────────────────────

    /**
     * Everything both the view and the queries need: the active tab, the
     * program scope, and the offender filters.
     */
    private function context(Request $request): array
    {
        $tab = in_array($request->get('tab'), self::TABS, true)
            ? $request->get('tab')
            : 'settings';

        $kind = $request->get('kind') === 'course' ? 'course' : 'diploma';

        $programs  = $this->programsOfKind($kind);
        $programId = $request->integer('program_id') ?: null;

        // "All programs of this kind" is a valid choice on the offenders tab;
        // the subjects tab always edits one program at a time.
        $allPrograms = $request->has('program_id') && $request->get('program_id') === '';

        if (!$allPrograms && (!$programId || !$programs->contains('id', $programId))) {
            $programId = $programs->first()->id ?? null;
        }

        $subjects = $programId
            ? Subject::where('program_id', $programId)
                ->orderBy('name_ar')
                ->get(['id', 'name_ar', 'name_en', 'code', 'absence_limit_percent'])
            : collect();

        // Which subjects the offenders tab looks at: one program, or all of them.
        $subjectIds = $allPrograms
            ? Subject::whereIn('program_id', $programs->pluck('id'))->pluck('id')
            : $subjects->pluck('id');

        return [
            'tab'         => $tab,
            'kind'        => $kind,
            'programs'    => $programs,
            'programId'   => $allPrograms ? null : $programId,
            'allPrograms' => $allPrograms,
            'subjects'    => $subjects,
            'subjectIds'  => $subjectIds,
            'enabled'     => AttendanceLimitService::isEnabled(),
            'percent'     => AttendanceLimitService::allowedPercent(),
            'search'      => trim((string) $request->get('q')),
            'status'      => in_array($request->get('status'), ['banned', 'exempt'], true)
                ? $request->get('status')
                : null,
        ];
    }

    /**
     * Programs of one kind. "course" covers the course-like types the admin
     * lists under الدورات; "diploma" is everything else (including untyped).
     */
    private function programsOfKind(string $kind)
    {
        return Program::when($kind === 'course',
                fn($q) => $q->whereIn('type', self::COURSE_TYPES),
                fn($q) => $q->where(fn($w) => $w->whereNull('type')
                    ->orWhereNotIn('type', self::COURSE_TYPES)),
            )
            ->orderBy('name_ar')
            ->get(['id', 'name_ar', 'name_en', 'code']);
    }

    // ── Offenders ───────────────────────────────────────────────────────────

    /**
     * Every student/subject pair whose unexcused absence rate is over the limit
     * that applies to *that* subject — its own override, or the global default.
     *
     * Counting is done in SQL so this stays cheap: one row per student+subject
     * with totals, minus the sessions covered by an approved apology.
     */
    private function offenders(array $ctx)
    {
        if ($ctx['subjectIds']->isEmpty()) {
            return collect();
        }

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
            ->whereIn('s.subject_id', $ctx['subjectIds'])
            ->when($ctx['search'], fn($q, $term) => $q->where(fn($w) => $w
                ->where('u.name', 'like', "%{$term}%")
                ->orWhere('u.email', 'like', "%{$term}%")))
            ->groupBy('a.student_id', 's.subject_id', 'u.name', 'u.email',
                      'sub.name_ar', 'sub.name_en', 'sub.absence_limit_percent')
            ->select([
                'a.student_id',
                's.subject_id',
                'u.name as student_name',
                'u.email as student_email',
                'sub.name_ar as subject_ar',
                'sub.name_en as subject_en',
                'sub.absence_limit_percent as subject_limit',
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
            ->map(function ($r) use ($exempt, $ctx) {
                $r->percent = $r->total > 0 ? round($r->absent / $r->total * 100, 1) : 0;
                $key        = $r->student_id . '-' . $r->subject_id;
                $r->exempt  = $exempt->has($key);
                $r->reason  = $exempt->get($key)->reason ?? null;
                $r->subject = $r->subject_ar ?: $r->subject_en;

                // The subject's own limit wins; NULL falls back to the global one.
                $r->limit  = $r->subject_limit !== null
                    ? (float) $r->subject_limit
                    : $ctx['percent'];
                $r->custom = $r->subject_limit !== null;

                return $r;
            })
            ->filter(fn($r) => $r->percent > $r->limit)
            ->when($ctx['status'] === 'banned', fn($c) => $c->where('exempt', false))
            ->when($ctx['status'] === 'exempt', fn($c) => $c->where('exempt', true))
            ->sortByDesc('percent')
            ->values();
    }

    /** Every exemption granted inside the current program scope. */
    private function exemptions($subjectIds)
    {
        if ($subjectIds->isEmpty()) {
            return collect();
        }

        return DB::table('attendance_exemptions as e')
            ->join('users as u', 'u.id', '=', 'e.student_id')
            ->join('subjects as sub', 'sub.id', '=', 'e.subject_id')
            ->leftJoin('users as g', 'g.id', '=', 'e.granted_by')
            ->whereIn('e.subject_id', $subjectIds)
            ->orderByDesc('e.updated_at')
            ->get([
                'e.student_id',
                'e.subject_id',
                'e.reason',
                'e.updated_at',
                'u.name as student_name',
                'u.email as student_email',
                'sub.name_ar as subject_ar',
                'sub.name_en as subject_en',
                'g.name as granted_by_name',
            ])
            ->map(function ($e) {
                $e->subject = $e->subject_ar ?: $e->subject_en;
                return $e;
            });
    }
}
