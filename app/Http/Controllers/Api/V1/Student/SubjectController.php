<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\Unit;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * GET /api/v1/student/subjects/{id}
     * Show subject details with sessions and attendance
     */
    public function show($id)
    {
        $student = auth()->user();

        $subject = Subject::whereHas('enrollments', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
            ->with(['term.program', 'teacher:id,name,email,profile_photo'])
            ->findOrFail($id);

        $sessions = Session::where('subject_id', $id)
            ->with('files')
            ->orderBy('session_number', 'asc')
            ->get();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        // Progress
        $totalSessions = $sessions->count();
        $attendedSessions = $attendances->where('attended', true)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'subject' => $subject,
                'sessions' => $sessions,
                'attendances' => $attendances,
                'progress' => [
                    'total_sessions' => $totalSessions,
                    'attended_sessions' => $attendedSessions,
                    'percentage' => $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 1) : 0,
                ],
            ],
        ]);
    }

    /**
     * GET /api/v1/student/subjects/{id}/sessions
     * List all sessions for a subject with join links and schedule
     */
    public function sessions($id)
    {
        $student = auth()->user();

        $subject = Subject::whereHas('enrollments', fn($q) => $q->where('student_id', $student->id))
            ->findOrFail($id);

        $sessions = Session::where('subject_id', $id)
            ->orderBy('session_number', 'asc')
            ->get();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        $data = $sessions->map(function ($session) use ($attendances) {
            $attendance = $attendances->get($session->id);

            $status = match (true) {
                !is_null($session->ended_at)   => 'ended',
                !is_null($session->started_at) => 'live',
                default                        => 'upcoming',
            };

            return [
                'id'             => $session->id,
                'title'          => $session->title,
                'type'           => $session->type,
                'session_number' => $session->session_number,
                'scheduled_at'   => $session->scheduled_at,
                'duration_minutes' => $session->duration_minutes,
                'status'         => $status,
                'join_url'       => $session->type === 'live_zoom' ? $session->zoom_join_url : null,
                'video_url'      => $session->type === 'recorded_video' ? $session->getVideoUrl() : null,
                'attended'       => $attendance?->attended ?? false,
                'watch_percentage' => $attendance?->watch_percentage ?? 0,
            ];
        });

        return response()->json([
            'success'  => true,
            'subject'  => ['id' => $subject->id, 'name' => $subject->name],
            'data'     => $data,
        ]);
    }

    /**
     * GET /api/v1/student/units/{id}
     * Show unit details with its sessions
     */
    public function showUnit($id)
    {
        $student = auth()->user();

        $unit = Unit::with(['subject.enrollments' => function ($q) use ($student) {
            $q->where('student_id', $student->id);
        }])->findOrFail($id);

        // Verify enrollment
        if ($unit->subject->enrollments->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'أنت غير مسجل في هذه المادة',
            ], 406);
        }

        $sessions = Session::where('unit_id', $id)
            ->with('files')
            ->orderBy('session_number', 'asc')
            ->get();

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        return response()->json([
            'success' => true,
            'data' => [
                'unit' => $unit,
                'sessions' => $sessions,
                'attendances' => $attendances,
            ],
        ]);
    }
}
