<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Subject;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index()
    {
        $teacher = Auth::user();
        $now     = now();

        // Subject-based sessions assigned to THIS teacher only (session.teacher_id).
        $subjectSessions = Session::whereNotNull('subject_id')
            ->where('teacher_id', $teacher->id)
            ->with(['subject.program', 'subject.term.program', 'programClass', 'subject.programClass', 'subject.term.programClass'])
            ->get();

        // Program-based sessions assigned to THIS teacher only (session.teacher_id).
        $programSessions = Session::whereNotNull('program_id')
            ->where('teacher_id', $teacher->id)
            ->with(['program', 'programClass'])
            ->get();

        $sessions = $subjectSessions->merge($programSessions)->sortBy('scheduled_at')->values();

        $upcoming = $sessions->filter(fn($s) => $s->scheduled_at && \Carbon\Carbon::parse($s->scheduled_at)->gte($now))->values();
        $past     = $sessions->filter(fn($s) => !$s->scheduled_at || \Carbon\Carbon::parse($s->scheduled_at)->lt($now))->sortByDesc('scheduled_at')->values();

        $groupedUpcoming = $upcoming->groupBy(function ($s) {
            $dt = \Carbon\Carbon::parse($s->scheduled_at);
            if ($dt->isToday())    return 'اليوم';
            if ($dt->isTomorrow()) return 'غداً';
            return $dt->translatedFormat('l، d F Y');
        });

        $stats = [
            'total'     => $sessions->count(),
            'upcoming'  => $upcoming->count(),
            'live'      => $sessions->where('status', 'live')->count(),
            'completed' => $sessions->where('status', 'completed')->count(),
        ];

        $subjects = Subject::assignedToTeacher($teacher->id)
            ->where(fn($q) => $q
                ->whereHas('program', fn($pq) => $pq->where('type', 'diploma'))
                ->orWhereHas('term.program', fn($pq) => $pq->where('type', 'diploma'))
            )
            ->with(['program:id,name_ar', 'term.program:id,name_ar'])
            ->orderBy('name_ar')
            ->get(['id', 'name_ar', 'name_en', 'program_id', 'term_id']);

        $programs = $teacher->teachingPrograms()
            ->whereIn('type', ['training', 'english', 'course'])
            ->orderBy('name_ar')
            ->get(['id', 'name_ar', 'type']);

        return view('teacher.schedule', compact('groupedUpcoming', 'past', 'stats', 'subjects', 'programs', 'sessions'));
    }

    /**
     * Bulk-create multiple sessions at once (no Zoom auto-create).
     * Teacher can edit individual sessions later to add Zoom links.
     */
    public function bulkStore(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'sessions'                    => 'required|array|min:1',
            'sessions.*.subject_id'       => 'nullable|integer',
            'sessions.*.scheduled_at'     => 'nullable|date',
            'sessions.*.duration_minutes' => 'nullable|integer|min:1|max:480',
        ]);

        // Verify all subjects belong to this teacher
        $subjectIds = collect($request->sessions)->pluck('subject_id')->unique();
        $subjects   = Subject::assignedToTeacher($teacher->id)
            ->whereIn('id', $subjectIds)
            ->get(['id', 'name_ar']);
        $allowed    = $subjects->pluck('id');

        foreach ($subjectIds as $sid) {
            if (!$allowed->contains($sid)) {
                return back()->withInput()
                    ->withErrors(['sessions' => 'إحدى المقررات  غير مصرح لك بها.']);
            }
        }

        // Determine next session number per subject
        $nextNumbers = [];
        foreach ($allowed as $sid) {
            $nextNumbers[$sid] = Session::where('subject_id', $sid)->max('session_number') ?? 0;
        }

        $zoom = app(ZoomService::class);
        $created = 0;
        $zoomFailed = 0;

        foreach ($request->sessions as $row) {
            $sid = (int) ($row['subject_id'] ?? 0);
            if (!$allowed->contains($sid)) continue;
            $nextNumbers[$sid]++;

            $subject       = $subjects->firstWhere('id', $sid);
            $sessionNumber = $nextNumbers[$sid];
            $scheduledAt   = $row['scheduled_at'] ?? null;
            $duration      = (int) ($row['duration_minutes'] ?? 60);

            // Create Zoom meeting
            $zoomData = $zoom->createMeeting([
                'topic'      => ($subject->name_ar ?? 'جلسة') . ' - جلسة ' . $sessionNumber,
                'type'       => 2,
                'start_time' => $scheduledAt ? \Carbon\Carbon::parse($scheduledAt)->toIso8601String() : now()->addHour()->toIso8601String(),
                'duration'   => $duration,
            ]);

            $sessionRow = [
                'subject_id'       => $sid,
                'teacher_id'       => $teacher->id,
                'type'             => 'live_zoom',
                'scheduled_at'     => $scheduledAt,
                'duration_minutes' => $duration,
                'session_number'   => $sessionNumber,
            ];

            if ($zoomData) {
                $sessionRow['zoom_meeting_id'] = (string) ($zoomData['id'] ?? '');
                $sessionRow['zoom_start_url']  = $zoomData['start_url'] ?? null;
                $sessionRow['zoom_join_url']   = $zoomData['join_url'] ?? null;
                $sessionRow['zoom_password']   = $zoomData['password'] ?? null;
            } else {
                $zoomFailed++;
            }

            Session::create($sessionRow);
            $created++;
        }

        $msg = "تم إنشاء {$created} جلسة بنجاح ✓";
        if ($zoomFailed > 0) {
            $msg .= " (تعذّر إنشاء رابط Zoom لـ {$zoomFailed} جلسة، يمكن إضافته لاحقاً)";
        }

        return redirect()->route('teacher.schedule')->with('success', $msg);
    }

    /**
     * Bulk-schedule an entire month: pick subject + recurring days-of-week + time → creates one session per matching day.
     */
    public function monthlyBulkStore(Request $request)
    {
        $teacher = Auth::user();

        $request->validate([
            'schedule_type'    => 'nullable|in:subject,program',
            'subject_id'       => 'nullable|integer',
            'program_id'       => 'nullable|integer',
            'year'             => 'required|integer|min:2024|max:2035',
            'month'            => 'required|integer|min:1|max:12',
            'days'             => 'required|array|min:1',
            'days.*'           => 'integer|min:0|max:6',
            'time'             => 'required|date_format:H:i',
            'duration_minutes' => 'required|integer|min:15|max:480',
        ]);

        $scheduleType = $request->input('schedule_type', $request->filled('program_id') ? 'program' : 'subject');

        // Resolve subject or program
        $subject = null;
        $program = null;
        $entityName = '';
        $sessionFkColumn = '';
        $sessionFkValue  = null;

        if ($scheduleType === 'program') {
            if (!$request->filled('program_id')) {
                return back()->withErrors(['program_id' => 'يرجى اختيار الدورة.']);
            }
            $program = $teacher->teachingPrograms()
                ->whereIn('type', ['training', 'english', 'course'])
                ->findOrFail($request->program_id);
            $entityName      = $program->name_ar;
            $sessionFkColumn = 'program_id';
            $sessionFkValue  = $program->id;
        } else {
            if (!$request->filled('subject_id')) {
                return back()->withErrors(['subject_id' => 'يرجى اختيار المقرر.']);
            }
            $subject = Subject::assignedToTeacher($teacher->id)
                ->findOrFail($request->subject_id);
            $entityName      = $subject->name_ar;
            $sessionFkColumn = 'subject_id';
            $sessionFkValue  = $subject->id;
        }

        $year     = (int) $request->year;
        $month    = (int) $request->month;
        $days     = array_map('intval', $request->days);
        [$hh, $mm] = explode(':', $request->time);
        $duration = (int) $request->duration_minutes;

        $startOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth();

        $dates = [];
        $cur   = $startOfMonth->copy();
        while ($cur->lte($endOfMonth)) {
            if (in_array($cur->dayOfWeek, $days)) {
                $dates[] = $cur->copy()->setHour((int)$hh)->setMinute((int)$mm)->setSecond(0);
            }
            $cur->addDay();
        }

        if (empty($dates)) {
            return back()->withErrors(['days' => 'لا توجد أيام مطابقة في الشهر المحدد.']);
        }

        $nextNumber = Session::where($sessionFkColumn, $sessionFkValue)->max('session_number') ?? 0;
        $zoom       = app(ZoomService::class);
        $created    = 0;
        $zoomFailed = 0;

        foreach ($dates as $scheduledAt) {
            $nextNumber++;

            $zoomData = $zoom->createMeeting([
                'topic'      => $entityName . ' - جلسة ' . $nextNumber,
                'type'       => 2,
                'start_time' => $scheduledAt->toIso8601String(),
                'duration'   => $duration,
            ]);

            $row = [
                $sessionFkColumn   => $sessionFkValue,
                'teacher_id'       => $teacher->id,
                'type'             => 'live_zoom',
                'scheduled_at'     => $scheduledAt,
                'duration_minutes' => $duration,
                'session_number'   => $nextNumber,
            ];

            if ($zoomData) {
                $row['zoom_meeting_id'] = (string) ($zoomData['id'] ?? '');
                $row['zoom_start_url']  = $zoomData['start_url'] ?? null;
                $row['zoom_join_url']   = $zoomData['join_url'] ?? null;
                $row['zoom_password']   = $zoomData['password'] ?? null;
            } else {
                $zoomFailed++;
            }

            Session::create($row);
            $created++;
        }

        $monthName = $startOfMonth->locale('ar')->translatedFormat('F Y');
        $msg = "تم جدولة {$created} جلسة لشهر {$monthName} ✓";
        if ($zoomFailed > 0) {
            $msg .= " (تعذّر إنشاء Zoom لـ {$zoomFailed} جلسة)";
        }

        return redirect()->route('teacher.schedule')->with('success', $msg);
    }
}
