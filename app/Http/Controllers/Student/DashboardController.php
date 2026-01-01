<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Ticket;
use App\Models\TeacherRating;
use App\Models\SatisfactionSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $student = auth()->user();

        // Get student's enrolled subjects with relationships
        $subjects = Subject::whereHas('enrollments', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with(['term.program', 'teacher'])
            ->withCount('sessions')
            ->get();

        // Get upcoming sessions for enrolled subjects
        $upcomingSessions = Session::whereHas('subject.enrollments', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->where('scheduled_at', '>', now())
            ->with(['subject'])
            ->orderBy('scheduled_at', 'asc')
            ->take(5)
            ->get();

        // Get recent sessions
        $recentSessions = Session::whereHas('subject.enrollments', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with(['subject'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get live sessions
        $liveSessions = Session::whereHas('subject.enrollments', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->where('type', 'live_zoom')
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->with(['subject'])
            ->get();

        // Statistics
        $stats = [
            'subjects_count' => $subjects->count(),
            'total_sessions' => Session::whereHas('subject.enrollments', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })->count(),
            'completed_sessions' => Session::whereHas('subject.enrollments', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })->whereNotNull('ended_at')->count(),
            'live_sessions' => $liveSessions->count(),
        ];

        // NELC: Progress per subject
        $subjectsProgress = [];
        foreach ($subjects as $subject) {
            $totalSessions = $subject->sessions()->count();
            $attendedSessions = Attendance::where('student_id', $student->id)
                ->whereHas('session', function($q) use ($subject) {
                    $q->where('subject_id', $subject->id);
                })
                ->where('attended', true)
                ->count();

            $subjectsProgress[$subject->id] = [
                'name' => $subject->name,
                'total' => $totalSessions,
                'attended' => $attendedSessions,
                'percentage' => $totalSessions > 0 ? round(($attendedSessions / $totalSessions) * 100, 1) : 0,
            ];
        }

        // Overall attendance
        $subjectIds = $subjects->pluck('id');
        $totalAttendances = Attendance::where('student_id', $student->id)
            ->whereHas('session', function($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })->count();

        $presentAttendances = Attendance::where('student_id', $student->id)
            ->whereHas('session', function($q) use ($subjectIds) {
                $q->whereIn('subject_id', $subjectIds);
            })
            ->where('attended', true)
            ->count();

        $overallAttendance = $totalAttendances > 0
            ? round(($presentAttendances / $totalAttendances) * 100, 1)
            : 0;

        // Monthly attendance chart
        $monthlyAttendance = Attendance::where('student_id', $student->id)
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN attended = 1 THEN 1 ELSE 0 END) as present'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function($item) {
                $item->rate = $item->total > 0 ? round(($item->present / $item->total) * 100, 1) : 0;
                return $item;
            });

        // NELC: Pending surveys
        $pendingSurveys = SatisfactionSurvey::where('status', 'active')
            ->where('type', 'student')
            ->where(function($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->whereDoesntHave('responses', function($q) use ($student) {
                $q->where('user_id', $student->id);
            })
            ->with('subject')
            ->get();

        // NELC: My tickets
        $myTickets = Ticket::where('user_id', $student->id)
            ->latest()
            ->limit(5)
            ->get();

        $openTicketsCount = Ticket::where('user_id', $student->id)
            ->whereIn('status', ['open', 'in_progress'])
            ->count();

        // NELC: Teachers I can rate
        $ratableTeachers = Subject::whereHas('enrollments', function($q) use ($student) {
                $q->where('student_id', $student->id)->where('status', 'active');
            })
            ->whereDoesntHave('teacherRatings', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->with('teacher')
            ->get();

        // Ratings I've submitted
        $myRatings = TeacherRating::where('student_id', $student->id)
            ->with(['teacher:id,name', 'subject:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        return view('student.dashboard', compact(
            'subjects',
            'upcomingSessions',
            'recentSessions',
            'liveSessions',
            'stats',
            'subjectsProgress',
            'overallAttendance',
            'monthlyAttendance',
            'pendingSurveys',
            'myTickets',
            'openTicketsCount',
            'ratableTeachers',
            'myRatings'
        ));
    }

    public function showSubject($id)
    {
        $student = auth()->user();

        // Get subject only if student is enrolled
        $subject = Subject::whereHas('enrollments', function($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with(['term.program', 'teacher'])
            ->findOrFail($id);

        // Get all sessions for this subject
        $sessions = Session::where('subject_id', $id)
            ->orderBy('session_number', 'asc')
            ->get();

        // Get attendance for this subject
        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        return view('student.subject-detail', compact('subject', 'sessions', 'attendances'));
    }
}
