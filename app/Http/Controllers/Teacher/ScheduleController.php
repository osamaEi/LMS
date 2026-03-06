<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Subject;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();
        $now     = now();

        $sessions = Session::whereHas('subject', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
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
        $subjects = Subject::where('teacher_id', $teacher->id)
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
        $teacher = auth()->user();

        $request->validate([
            'sessions'                    => 'required|array|min:1',
            'sessions.*.subject_id'       => 'nullable|integer',
            'sessions.*.title_ar'         => 'nullable|string|max:255',
            'sessions.*.title_en'         => 'nullable|string|max:255',
            'sessions.*.type'             => 'nullable|in:live_zoom,recorded_video',
            'sessions.*.scheduled_at'     => 'nullable|date',
            'sessions.*.duration_minutes' => 'nullable|integer|min:1|max:480',
        ]);

        // Verify all subjects belong to this teacher
        $subjectIds = collect($request->sessions)->pluck('subject_id')->unique();
        $allowed    = Subject::where('teacher_id', $teacher->id)
            ->whereIn('id', $subjectIds)
            ->pluck('id');

        foreach ($subjectIds as $sid) {
            if (!$allowed->contains($sid)) {
                return back()->withInput()
                    ->withErrors(['sessions' => 'إحدى المواد غير مصرح لك بها.']);
            }
        }

        // Determine next session number per subject
        $nextNumbers = [];
        foreach ($allowed as $sid) {
            $nextNumbers[$sid] = Session::where('subject_id', $sid)->max('session_number') ?? 0;
        }

        $created = 0;
        foreach ($request->sessions as $row) {
            $sid = (int) ($row['subject_id'] ?? 0);
            if (!$allowed->contains($sid)) continue;
            $nextNumbers[$sid]++;

            Session::create([
                'subject_id'       => $sid,
                'title_ar'         => $row['title_ar'] ?? null,
                'title_en'         => $row['title_en'] ?? null,
                'type'             => $row['type'] ?? 'live_zoom',
                'scheduled_at'     => $row['scheduled_at'] ?? null,
                'duration_minutes' => $row['duration_minutes'] ?? 60,
                'session_number'   => $nextNumbers[$sid],
                'status'           => 'scheduled',
            ]);
            $created++;
        }

        return redirect()->route('teacher.schedule')
            ->with('success', "تم إنشاء {$created} جلسة بنجاح ✓");
    }
}
