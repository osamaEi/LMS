<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Session;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\TeacherRating;
use App\Models\SatisfactionSurvey;
use App\Models\Term;
use App\Services\ZoomService;
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

    /**
     * عرض جميع جلسات الطالب
     */
    public function mySessions(Request $request)
    {
        $student = auth()->user();
        $subjectId = $request->query('subject_id');
        $type = $request->query('type');

        // Get enrolled subjects for filter dropdown
        $enrolledSubjects = Subject::whereHas('enrollments', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->get();

        // Build sessions query
        $query = Session::whereHas('subject.enrollments', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->with(['subject', 'unit']);

        // Filter by subject
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        // Filter by type
        if ($type) {
            $query->where('type', $type);
        }

        $sessions = $query->orderBy('scheduled_at', 'desc')
            ->orderBy('session_number', 'asc')
            ->paginate(15);

        // Get attendance records for these sessions
        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        // Statistics
        $totalSessions = Session::whereHas('subject.enrollments', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->count();

        $completedSessions = Session::whereHas('subject.enrollments', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->whereNotNull('ended_at')->count();

        $zoomSessions = Session::whereHas('subject.enrollments', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->where('type', 'live_zoom')->count();

        return view('student.sessions.index', compact(
            'sessions',
            'enrolledSubjects',
            'subjectId',
            'type',
            'attendances',
            'totalSessions',
            'completedSessions',
            'zoomSessions'
        ));
    }

    /**
     * عرض سجل الحضور
     */
    public function attendance(Request $request)
    {
        $student = auth()->user();
        $subjectId = $request->query('subject_id');

        // Get enrolled subjects for filter dropdown
        $enrolledSubjects = Subject::whereHas('enrollments', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->get();

        // Build attendance query
        $query = Attendance::where('student_id', $student->id)
            ->with(['session.subject', 'session.unit']);

        if ($subjectId) {
            $query->whereHas('session', function($q) use ($subjectId) {
                $q->where('subject_id', $subjectId);
            });
        }

        $attendances = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics
        $totalSessions = Attendance::where('student_id', $student->id)->count();
        $attendedSessions = Attendance::where('student_id', $student->id)
            ->where('attended', true)
            ->count();
        $attendanceRate = $totalSessions > 0
            ? round(($attendedSessions / $totalSessions) * 100, 1)
            : 0;

        return view('student.attendance.index', compact(
            'attendances',
            'enrolledSubjects',
            'subjectId',
            'totalSessions',
            'attendedSessions',
            'attendanceRate'
        ));
    }

    /**
     * عرض الجلسات القادمة
     */
    public function upcomingSessions()
    {
        $student = auth()->user();

        // Get upcoming Zoom sessions
        $upcomingSessions = Session::whereHas('subject.enrollments', function($q) use ($student) {
                $q->where('student_id', $student->id)->where('status', 'active');
            })
            ->where('type', 'live_zoom')
            ->where('scheduled_at', '>=', now())
            ->with(['subject', 'unit'])
            ->orderBy('scheduled_at', 'asc')
            ->paginate(10);

        // Get live sessions (currently running)
        $liveSessions = Session::whereHas('subject.enrollments', function($q) use ($student) {
                $q->where('student_id', $student->id)->where('status', 'active');
            })
            ->where('type', 'live_zoom')
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->with(['subject', 'unit'])
            ->get();

        return view('student.sessions.upcoming', compact('upcomingSessions', 'liveSessions'));
    }

    /**
     * الانضمام إلى جلسة Zoom
     */
    public function joinZoom(Request $request, $sessionId)
    {
        $student = auth()->user();

        // Find the session
        $session = Session::with('subject')->findOrFail($sessionId);

        // Check if student is enrolled
        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('subject_id', $session->subject_id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            return back()->with('error', 'أنت غير مسجل في هذه المادة');
        }

        // Check if session has Zoom meeting
        if (empty($session->zoom_meeting_id)) {
            return back()->with('error', 'هذه الجلسة لا تحتوي على اجتماع Zoom');
        }

        // Create or update attendance record
        $attendance = Attendance::firstOrCreate(
            [
                'student_id' => $student->id,
                'session_id' => $session->id,
            ],
            [
                'attended' => true,
                'joined_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        // If attendance exists but not joined, record join
        if (!$attendance->joined_at) {
            $attendance->recordJoin($request->ip(), $request->userAgent());
            $attendance->markAsAttended();
        }

        // Generate Zoom SDK signature
        $zoomService = new ZoomService();
        $signature = $zoomService->generateSignature($session->zoom_meeting_id, 0);

        return view('student.sessions.zoom', compact('session', 'attendance', 'signature'));
    }

    /**
     * تسجيل مغادرة جلسة Zoom
     */
    public function leaveZoom(Request $request, $sessionId)
    {
        $student = auth()->user();

        $attendance = Attendance::where('student_id', $student->id)
            ->where('session_id', $sessionId)
            ->first();

        if ($attendance) {
            $attendance->recordLeave();
        }

        return redirect()->route('student.upcoming-sessions')
            ->with('success', 'تم تسجيل مغادرتك بنجاح');
    }

    /**
     * عرض معلومات البرنامج الدراسي
     */
    public function myProgram()
    {
        $student = auth()->user();

        // Get student's program with terms
        $program = $student->program;

        if (!$program) {
            // Get available programs for enrollment
            $availablePrograms = \App\Models\Program::where('status', 'active')
                ->withCount('terms')
                ->get();

            return view('student.program.index', [
                'program' => null,
                'track' => null,
                'terms' => collect(),
                'subjects' => collect(),
                'stats' => [],
                'enrollments' => collect(),
                'availablePrograms' => $availablePrograms,
            ]);
        }

        // Get student's track
        $track = $student->track;

        // Get current term
        $currentTermNumber = $student->current_term_number ?? 1;

        // Get all terms for this program
        $terms = Term::where('program_id', $program->id)
            ->orderBy('term_number', 'asc')
            ->with(['subjects' => function($q) use ($student) {
                $q->with(['teacher', 'sessions']);
            }])
            ->get();

        // Get enrolled subjects with details
        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['subject.term', 'subject.teacher', 'subject.sessions'])
            ->get();

        $subjects = Subject::whereHas('enrollments', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->with(['term', 'teacher'])
            ->withCount(['sessions', 'sessions as completed_sessions_count' => function($q) {
                $q->whereNotNull('ended_at');
            }])
            ->get();

        // Calculate statistics
        $totalSessions = Session::whereHas('subject.enrollments', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->count();

        $completedSessions = Session::whereHas('subject.enrollments', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->whereNotNull('ended_at')->count();

        // Attendance stats
        $totalAttendances = Attendance::where('student_id', $student->id)->count();
        $presentAttendances = Attendance::where('student_id', $student->id)->where('attended', true)->count();
        $attendanceRate = $totalAttendances > 0 ? round(($presentAttendances / $totalAttendances) * 100, 1) : 0;

        // Progress per subject
        $subjectsProgress = [];
        foreach ($subjects as $subject) {
            $subjectTotalSessions = $subject->sessions()->count();
            $subjectAttended = Attendance::where('student_id', $student->id)
                ->whereHas('session', function($q) use ($subject) {
                    $q->where('subject_id', $subject->id);
                })
                ->where('attended', true)
                ->count();

            $subjectsProgress[$subject->id] = [
                'total' => $subjectTotalSessions,
                'attended' => $subjectAttended,
                'percentage' => $subjectTotalSessions > 0 ? round(($subjectAttended / $subjectTotalSessions) * 100, 1) : 0,
            ];
        }

        $stats = [
            'total_subjects' => $subjects->count(),
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'attendance_rate' => $attendanceRate,
            'current_term' => $currentTermNumber,
            'total_terms' => $terms->count(),
            'progress_percentage' => $terms->count() > 0 ? round(($currentTermNumber / $terms->count()) * 100, 1) : 0,
        ];

        return view('student.program.index', compact(
            'program',
            'track',
            'terms',
            'subjects',
            'stats',
            'enrollments',
            'subjectsProgress',
            'currentTermNumber'
        ));
    }

    /**
     * التسجيل في برنامج دراسي
     */
    public function enrollInProgram(Request $request)
    {
        $request->validate([
            'program_id' => 'required|exists:programs,id',
        ]);

        $student = auth()->user();

        // Check if student already has a program
        if ($student->program_id) {
            return back()->with('error', 'أنت مسجل بالفعل في برنامج دراسي');
        }

        // Get the program
        $program = \App\Models\Program::where('id', $request->program_id)
            ->where('status', 'active')
            ->firstOrFail();

        // Update student's program
        $student->update([
            'program_id' => $program->id,
            'current_term_number' => 1,
        ]);

        return redirect()->route('student.my-program')
            ->with('success', 'تم تسجيلك في البرنامج بنجاح: ' . $program->name);
    }

    /**
     * عرض الروابط المفيدة
     */
    public function usefulLinks()
    {
        $student = auth()->user();

        $links = [
            [
                'title' => 'البوابة الإلكترونية',
                'url' => Setting::where('key', 'student_portal_url')->value('value') ?? '#',
                'icon' => 'portal',
                'description' => 'الوصول إلى البوابة الإلكترونية للطالب',
            ],
            [
                'title' => 'المكتبة الرقمية',
                'url' => Setting::where('key', 'library_url')->value('value') ?? '#',
                'icon' => 'library',
                'description' => 'تصفح الكتب والمراجع الإلكترونية',
            ],
            [
                'title' => 'الدعم الفني',
                'url' => Setting::where('key', 'support_url')->value('value') ?? '#',
                'icon' => 'support',
                'description' => 'التواصل مع فريق الدعم الفني',
            ],
            [
                'title' => 'الجدول الدراسي',
                'url' => Setting::where('key', 'schedule_url')->value('value') ?? '#',
                'icon' => 'schedule',
                'description' => 'عرض الجدول الدراسي الخاص بك',
            ],
            [
                'title' => 'نظام البلاك بورد',
                'url' => Setting::where('key', 'blackboard_url')->value('value') ?? '#',
                'icon' => 'blackboard',
                'description' => 'الوصول إلى نظام إدارة التعلم',
            ],
            [
                'title' => 'التقويم الأكاديمي',
                'url' => Setting::where('key', 'calendar_url')->value('value') ?? '#',
                'icon' => 'calendar',
                'description' => 'عرض المواعيد والأحداث الأكاديمية',
            ],
        ];

        return view('student.links.index', compact('links'));
    }
}
