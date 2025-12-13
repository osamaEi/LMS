<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();

        // Get teacher's subjects with student count
        $subjects = Subject::where('teacher_id', $teacher->id)
            ->with(['term.program'])
            ->withCount('enrollments')
            ->get();

        // Get upcoming sessions
        $upcomingSessions = Session::whereHas('subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>', now())
            ->with('subject')
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        // Get recent sessions
        $recentSessions = Session::whereHas('subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })
            ->with('subject')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Statistics
        $stats = [
            'subjects_count' => $subjects->count(),
            'total_students' => $subjects->sum('enrollments_count'),
            'total_sessions' => Session::whereHas('subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->count(),
            'live_sessions' => Session::whereHas('subject', function($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            })->where('status', 'live')->count(),
        ];

        return view('teacher.dashboard', compact(
            'subjects',
            'upcomingSessions',
            'recentSessions',
            'stats'
        ));
    }

    public function showSubject($id)
    {
        $teacher = auth()->user();

        $subject = Subject::where('teacher_id', $teacher->id)
            ->with(['term.program', 'enrollments.student'])
            ->findOrFail($id);

        $sessions = Session::where('subject_id', $id)
            ->orderBy('session_number', 'asc')
            ->get();

        $students = User::whereHas('enrollments', function($query) use ($id) {
            $query->where('subject_id', $id);
        })->get();

        return view('teacher.subject-detail', compact('subject', 'sessions', 'students'));
    }
}
