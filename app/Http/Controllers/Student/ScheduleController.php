<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Session;

class ScheduleController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        $sessions = Session::whereHas('subject.enrollments', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with('subject')
            ->orderBy('scheduled_at')
            ->get();

        $now = now();

        $calSessions = $sessions->map(fn($s) => [
            'id'               => $s->id,
            'title'            => $s->title_ar ?: (($s->subject->name_ar ?? 'جلسة') . ' #' . $s->session_number),
            'subject_name'     => $s->subject->name_ar ?? '',
            'scheduled_at'     => $s->scheduled_at ? \Carbon\Carbon::parse($s->scheduled_at)->toIso8601String() : null,
            'duration_minutes' => $s->duration_minutes,
            'type'             => $s->type ?? '',
            'status'           => (string) ($s->status ?? ''),
            'session_number'   => $s->session_number,
            'zoom_join_url'    => $s->zoom_join_url,
            // Students may only join after the teacher has started the session.
            'started_at'       => $s->started_at ? \Carbon\Carbon::parse($s->started_at)->toIso8601String() : null,
            'ended_at'         => $s->ended_at ? \Carbon\Carbon::parse($s->ended_at)->toIso8601String() : null,
        ])->filter(fn($s) => $s['scheduled_at'])->values();

        $stats = [
            'total'     => $sessions->count(),
            'upcoming'  => $sessions->filter(fn($s) => $s->scheduled_at && \Carbon\Carbon::parse($s->scheduled_at)->gte($now))->count(),
            'completed' => $sessions->where('status', 'completed')->count(),
            'live'      => $sessions->where('status', 'live')->count(),
        ];

        return view('student.schedule', compact('calSessions', 'stats'));
    }
}
