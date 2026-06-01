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
        $student  = auth()->user();
        $program  = $student->program;
        $isDiploma = $program && $program->type === 'diploma';

        // Session IDs the admin has assigned this student to (via Attendance records)
        $assignedSessionIds = Attendance::where('student_id', $student->id)->pluck('session_id');

        // Access check: diploma students can access any subject in their program;
        // others need at least one assigned session in this subject
        if ($isDiploma) {
            $subject = Subject::where(function ($q) use ($student) {
                $q->whereHas('term', fn($tq) => $tq->where('program_id', $student->program_id))
                  ->orWhereHas('terms', fn($tq) => $tq->where('program_id', $student->program_id));
            })->with(['term.program', 'teacher:id,name,profile_photo', 'files'])
              ->findOrFail($id);
        } else {
            $subject = Subject::whereHas('sessions', fn($q) => $q->whereIn('id', $assignedSessionIds))
                ->with(['term.program', 'teacher:id,name,profile_photo', 'files'])
                ->findOrFail($id);
        }

        // Only show sessions the admin has assigned this student to
        $sessions = Session::where('subject_id', $id)
            ->whereIn('id', $assignedSessionIds)
            ->with(['files', 'homework'])
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
                'id'               => $session->id,
                'title'            => $session->title_ar ?? $session->title ?? null,
                'title_en'         => $session->title_en ?? null,
                'type'             => $session->type,
                'session_number'   => $session->session_number,
                'scheduled_at'     => $session->scheduled_at,
                'duration_minutes' => $session->duration_minutes,
                'status'           => $status,
                'is_live'          => $status === 'live',
                'join_url'         => $session->type === 'live_zoom' ? $session->zoom_join_url : null,
                'zoom_meeting_id'  => $session->zoom_meeting_id ?? null,
                'video_url'        => $session->type === 'recorded_video' ? $session->getVideoUrl() : null,
                'recording_url'    => $session->recording_url ?? null,
                'files'            => $session->files->map(fn($f) => [
                    'id'    => $f->id,
                    'title' => $f->title,
                    'url'   => asset('storage/' . $f->file_path),
                    'type'  => $f->file_type,
                    'size'  => $f->file_size,
                ])->values(),
                'homework'         => $session->homework ? [
                    'id'       => $session->homework->id,
                    'title'    => $session->homework->title,
                    'due_date' => $session->homework->due_date?->format('Y-m-d'),
                ] : null,
                'attendance' => [
                    'attended'         => $attendance?->attended ?? false,
                    'joined_at'        => $attendance?->joined_at,
                    'left_at'          => $attendance?->left_at,
                    'duration_minutes' => $attendance?->duration_minutes ?? 0,
                    'watch_percentage' => $attendance?->watch_percentage ?? 0,
                ],
                'links' => [
                    'join_api'  => url("/api/v1/student/sessions/{$session->id}/join-zoom"),
                    'leave_api' => url("/api/v1/student/sessions/{$session->id}/leave-zoom"),
                ],
            ];
        });

        $totalSessions   = $data->count();
        $attendedSessions = $data->where('attendance.attended', true)->count();

        return response()->json([
            'success' => true,
            'subject' => [
                'id'           => $subject->id,
                'name_ar'      => $subject->name_ar,
                'name_en'      => $subject->name_en,
                'code'         => $subject->code,
                'banner_photo' => $subject->banner_photo ? asset('storage/' . $subject->banner_photo) : null,
                'teacher'      => $subject->teacher ? [
                    'id'            => $subject->teacher->id,
                    'name'          => $subject->teacher->name,
                    'profile_photo' => $subject->teacher->profile_photo
                        ? asset('storage/' . $subject->teacher->profile_photo)
                        : null,
                ] : null,
                'files' => $subject->files->map(fn($f) => [
                    'id'    => $f->id,
                    'title' => $f->title,
                    'url'   => asset('storage/' . $f->file_path),
                    'type'  => $f->file_type,
                    'size'  => $f->file_size,
                ])->values(),
            ],
            'progress' => [
                'total_sessions'    => $totalSessions,
                'attended_sessions' => $attendedSessions,
                'percentage'        => $totalSessions > 0
                    ? round(($attendedSessions / $totalSessions) * 100, 1)
                    : 0,
            ],
            'data' => $data->values(),
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
                'message' => 'أنت غير مسجل في هذه المقرر ',
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
