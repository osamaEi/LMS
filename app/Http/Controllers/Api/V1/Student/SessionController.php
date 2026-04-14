<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Subject;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Services\ZoomService;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * GET /api/v1/student/sessions
     * List all sessions from student's program with filters
     */
    public function index(Request $request)
    {
        $student = auth()->user();
        $subjectId = $request->query('subject_id');
        $type = $request->query('type');

        $query = Session::whereHas('subject.term', function ($q) use ($student) {
            $q->where('program_id', $student->program_id);
        })
            ->with(['subject:id,name_ar,name_en', 'subject.teacher:id,name', 'unit:id,title', 'files']);

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        if ($type) {
            $query->where('type', $type);
        }

        $sessions = $query->orderBy('subject_id')
            ->orderBy('session_number', 'asc')
            ->paginate(20);

        // Get attendance records
        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        // Statistics
        $allSessions = Session::whereHas('subject.term', function ($q) use ($student) {
            $q->where('program_id', $student->program_id);
        });

        $stats = [
            'total_sessions' => (clone $allSessions)->count(),
            'completed_sessions' => (clone $allSessions)->whereNotNull('ended_at')->count(),
            'zoom_sessions' => (clone $allSessions)->where('type', 'live_zoom')->count(),
            'live_sessions' => (clone $allSessions)->whereNotNull('started_at')->whereNull('ended_at')->count(),
        ];

        // Subjects for filter
        $programSubjects = Subject::whereHas('term', function ($q) use ($student) {
            $q->where('program_id', $student->program_id);
        })->select('id', 'name_ar', 'name_en')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'sessions' => $sessions,
                'attendances' => $attendances,
                'statistics' => $stats,
                'subjects' => $programSubjects,
            ],
        ]);
    }

    /**
     * POST /api/v1/student/sessions/{sessionId}/join
     * Student joins a session (live zoom or recorded video)
     */
    public function join(Request $request, $sessionId)
    {
        $student = auth()->user();

        $session = Session::findOrFail($sessionId);

        // Verify enrollment
        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('subject_id', $session->subject_id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json([
                'success' => false,
                'message' => 'ليس لديك صلاحية الانضمام لهذه الجلسة',
            ], 403);
        }

        // Record attendance
        $attendance = Attendance::firstOrCreate(
            ['student_id' => $student->id, 'session_id' => $session->id],
            ['attended' => true, 'joined_at' => now()]
        );

        if (!$attendance->joined_at) {
            $attendance->recordJoin($request->ip(), $request->userAgent());
            $attendance->markAsAttended();
        }

        // ── Live Zoom ──────────────────────────────────────────────────────────
        if ($session->type === 'live_zoom') {
            if (empty($session->zoom_meeting_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم إعداد اجتماع Zoom لهذه الجلسة بعد',
                ], 400);
            }

            $signature = (new ZoomService())->generateSignature($session->zoom_meeting_id, 0);

            return response()->json([
                'success'       => true,
                'type'          => 'live_zoom',
                'attendance_id' => $attendance->id,
                'data'          => [
                    'zoom_meeting_id' => $session->zoom_meeting_id,
                    'zoom_join_url'   => $session->zoom_join_url,
                    'zoom_password'   => $session->zoom_password,
                    'zoom_signature'  => $signature,
                ],
            ]);
        }

        // ── Recorded Video ─────────────────────────────────────────────────────
        if ($session->type === 'recorded_video') {
            if (!$session->hasVideo()) {
                return response()->json([
                    'success' => false,
                    'message' => 'الفيديو غير متاح بعد',
                ], 400);
            }

            return response()->json([
                'success'       => true,
                'type'          => 'recorded_video',
                'attendance_id' => $attendance->id,
                'data'          => [
                    'video_url'      => $session->getVideoUrl(),
                    'video_duration' => $session->video_duration,
                ],
            ]);
        }

        return response()->json(['success' => false, 'message' => 'نوع الجلسة غير معروف'], 400);
    }
}
