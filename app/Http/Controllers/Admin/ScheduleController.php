<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Session;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $sessions = Session::with(['subject', 'teacher'])
            ->orderBy('scheduled_at')
            ->get();

        $calSessions = $sessions->map(fn($s) => [
            'id'               => $s->id,
            'title'            => $s->title_ar ?: (($s->subject->name_ar ?? 'جلسة') . ' #' . $s->session_number),
            'subject_name'     => $s->subject->name_ar ?? '',
            'teacher_name'     => $s->teacher?->name ?? '',
            'scheduled_at'     => $s->scheduled_at ? \Carbon\Carbon::parse($s->scheduled_at)->toIso8601String() : null,
            'duration_minutes' => $s->duration_minutes,
            'type'             => $s->type ?? '',
            'status'           => $s->status ?? '',
            'session_number'   => $s->session_number,
            'subject_id'       => $s->subject_id,
            'attendance_count' => $s->attendances()->count(),
        ])->filter(fn($s) => $s['scheduled_at'])->values();

        $stats = [
            'total'     => $sessions->count(),
            'upcoming'  => $sessions->filter(fn($s) => $s->scheduled_at && \Carbon\Carbon::parse($s->scheduled_at)->isFuture())->count(),
            'live'      => $sessions->where('status', 'live')->count(),
            'completed' => $sessions->where('status', 'completed')->count(),
        ];

        return view('admin.schedule.index', compact('calSessions', 'stats'));
    }

    public function sessionStudents(Session $session)
    {
        $enrolled = Enrollment::where('subject_id', $session->subject_id)
            ->where('status', 'active')
            ->with('student:id,name,email')
            ->get();

        $assigned = Attendance::where('session_id', $session->id)
            ->pluck('student_id')
            ->toArray();

        return response()->json([
            'session' => [
                'id'           => $session->id,
                'title'        => $session->title_ar ?: (($session->subject->name_ar ?? 'جلسة') . ' #' . $session->session_number),
                'scheduled_at' => $session->scheduled_at,
            ],
            'students' => $enrolled->map(fn($e) => [
                'id'       => $e->student->id,
                'name'     => $e->student->name,
                'email'    => $e->student->email,
                'assigned' => in_array($e->student->id, $assigned),
            ])->values(),
        ]);
    }

    public function assignStudents(Request $request, Session $session)
    {
        $request->validate([
            'student_ids'   => 'required|array',
            'student_ids.*' => 'integer|exists:users,id',
        ]);

        $inserted = 0;
        foreach ($request->student_ids as $studentId) {
            $exists = Attendance::where('session_id', $session->id)
                ->where('student_id', $studentId)
                ->exists();

            if (!$exists) {
                Attendance::create([
                    'session_id' => $session->id,
                    'student_id' => $studentId,
                    'attended'   => false,
                ]);
                $inserted++;
            }
        }

        return response()->json(['success' => true, 'inserted' => $inserted]);
    }
}
