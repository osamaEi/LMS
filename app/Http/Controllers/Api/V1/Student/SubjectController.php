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
            ], 403);
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
