<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Session;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        // Get student's enrolled subjects with relationships
        $subjects = Subject::whereHas('enrollments', function($query) use ($student) {
                $query->where('user_id', $student->id);
            })
            ->with(['term.program', 'teacher'])
            ->withCount('sessions')
            ->get();

        // Get upcoming sessions for enrolled subjects
        $upcomingSessions = Session::whereHas('subject.enrollments', function($query) use ($student) {
                $query->where('user_id', $student->id);
            })
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>', now())
            ->with(['subject'])
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        // Get recent sessions
        $recentSessions = Session::whereHas('subject.enrollments', function($query) use ($student) {
                $query->where('user_id', $student->id);
            })
            ->with(['subject'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get live sessions
        $liveSessions = Session::whereHas('subject.enrollments', function($query) use ($student) {
                $query->where('user_id', $student->id);
            })
            ->where('status', 'live')
            ->with(['subject'])
            ->get();

        // Statistics
        $stats = [
            'subjects_count' => $subjects->count(),
            'total_sessions' => Session::whereHas('subject.enrollments', function($query) use ($student) {
                $query->where('user_id', $student->id);
            })->count(),
            'completed_sessions' => Session::whereHas('subject.enrollments', function($query) use ($student) {
                $query->where('user_id', $student->id);
            })->where('status', 'completed')->count(),
            'live_sessions' => $liveSessions->count(),
        ];

        return view('student.dashboard', compact(
            'subjects',
            'upcomingSessions',
            'recentSessions',
            'liveSessions',
            'stats'
        ));
    }

    public function showSubject($id)
    {
        $student = auth()->user();

        // Get subject only if student is enrolled
        $subject = Subject::whereHas('enrollments', function($query) use ($student) {
                $query->where('user_id', $student->id);
            })
            ->with(['term.program', 'teacher'])
            ->findOrFail($id);

        // Get all sessions for this subject
        $sessions = Session::where('subject_id', $id)
            ->orderBy('session_number', 'asc')
            ->get();

        return view('student.subject-detail', compact('subject', 'sessions'));
    }
}
