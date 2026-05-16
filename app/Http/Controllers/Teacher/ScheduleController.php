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

        $sessions = Session::whereHas('subject', function ($query) use ($teacher) {
                $query->assignedToTeacher($teacher->id);
            })
            ->with(['subject'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        $upcoming = $sessions->filter(fn($s) => $s->scheduled_at && \Carbon\Carbon::parse($s->scheduled_at)->gte($now))->values();
        $past     = $sessions->filter(fn($s) => !$s->scheduled_at || \Carbon\Carbon::parse($s->scheduled_at)->lt($now))->sortByDesc('scheduled_at')->values();

        // Group upcoming by day label
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

        // Teacher's active subjects for the create form
        $subjects = Subject::assignedToTeacher($teacher->id)
            ->where('status', 'active')
            ->orderBy('name_ar')
            ->get(['id', 'name_ar', 'name_en']);

        return view('teacher.schedule', compact('groupedUpcoming', 'past', 'stats', 'subjects'));
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
}
