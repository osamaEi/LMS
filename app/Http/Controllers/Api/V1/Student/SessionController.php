<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\Subject;
use App\Models\Attendance;
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
}
