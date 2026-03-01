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

        // Check if student is assigned to a program
        if (!$student->program_id) {
            return redirect()->route('student.my-program');
        }

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
            ->with(['teacher:id,name', 'subject:id,name_ar'])
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
            ->with('files')
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
     * عرض جميع جلسات الطالب من برنامجه
     */
    public function mySessions(Request $request)
    {
        $student = auth()->user();
        $subjectId = $request->query('subject_id');
        $type      = $request->query('type');
        $termFilter = $request->query('term_filter', 'current'); // 'current' | 'all'

        // Detect current term (same 3-step logic as myProgram)
        $terms = Term::where('program_id', $student->program_id)
            ->orderBy('term_number', 'asc')
            ->get();

        $currentTermNumber = $student->current_term_number ?? 1;
        $currentTerm = $terms->first(function ($term) {
            return $term->start_date && $term->end_date
                && $term->start_date <= now()
                && $term->end_date   >= now();
        }) ?? $terms->firstWhere('term_number', $currentTermNumber)
          ?? $terms->first();

        // Get all subjects in student's program (through terms)
        $programSubjects = Subject::whereHas('term', function($q) use ($student) {
            $q->where('program_id', $student->program_id);
        })->with(['term', 'teacher'])->get();

        // Subjects scoped to current term (for the filter dropdown when on current tab)
        $currentTermSubjects = $currentTerm
            ? $programSubjects->where('term_id', $currentTerm->id)->values()
            : $programSubjects;

        // Subjects shown in the filter dropdown depend on active tab
        $filterSubjects = ($termFilter === 'current' && $currentTerm)
            ? $currentTermSubjects
            : $programSubjects;

        // Build sessions query - all sessions from program subjects
        $query = Session::whereHas('subject.term', function($q) use ($student) {
                $q->where('program_id', $student->program_id);
            })
            ->with(['subject.term', 'subject.teacher', 'unit', 'files']);

        // Scope to current term unless "all" requested
        if ($termFilter === 'current' && $currentTerm) {
            $query->whereHas('subject', function($q) use ($currentTerm) {
                $q->where('term_id', $currentTerm->id);
            });
        }

        // Filter by subject
        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        }

        // Filter by type
        if ($type) {
            $query->where('type', $type);
        }

        $sessions = $query->orderBy('subject_id')
            ->orderBy('session_number', 'asc')
            ->get();

        // Group sessions by subject
        $sessionsBySubject = $sessions->groupBy('subject_id');

        // Get attendance records for these sessions
        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        // Statistics
        $totalSessions    = $sessions->count();
        $completedSessions = $sessions->whereNotNull('ended_at')->count();
        $zoomSessions     = $sessions->where('type', 'live_zoom')->count();
        $liveSessions     = $sessions->whereNotNull('started_at')->whereNull('ended_at')->count();

        return view('student.sessions.index', compact(
            'sessions',
            'sessionsBySubject',
            'programSubjects',
            'filterSubjects',
            'currentTerm',
            'termFilter',
            'subjectId',
            'type',
            'attendances',
            'totalSessions',
            'completedSessions',
            'zoomSessions',
            'liveSessions'
        ));
    }

    /**
     * عرض سجل الحضور
     */
    public function attendance(Request $request)
    {
        $student    = auth()->user();
        $subjectId  = $request->query('subject_id');
        $termFilter = $request->query('term_filter', 'current'); // 'current' | 'all'

        // Detect current term
        $terms = $student->program_id
            ? Term::where('program_id', $student->program_id)->orderBy('term_number', 'asc')->get()
            : collect();

        $currentTermNumber = $student->current_term_number ?? 1;
        $currentTerm = $terms->first(function ($term) {
            return $term->start_date && $term->end_date
                && $term->start_date <= now()
                && $term->end_date   >= now();
        }) ?? $terms->firstWhere('term_number', $currentTermNumber)
          ?? $terms->first();

        // Get enrolled subjects for filter dropdown
        $enrolledSubjects = Subject::whereHas('enrollments', function($q) use ($student) {
            $q->where('student_id', $student->id);
        })->get();

        // Subjects shown in the filter dropdown depend on active tab
        $filterSubjects = ($termFilter === 'current' && $currentTerm)
            ? $enrolledSubjects->where('term_id', $currentTerm->id)->values()
            : $enrolledSubjects;

        // Build attendance query
        $query = Attendance::where('student_id', $student->id)
            ->with(['session.subject', 'session.unit']);

        // Scope to current term unless "all" requested
        if ($termFilter === 'current' && $currentTerm) {
            $query->whereHas('session.subject', function($q) use ($currentTerm) {
                $q->where('term_id', $currentTerm->id);
            });
        }

        if ($subjectId) {
            $query->whereHas('session', function($q) use ($subjectId) {
                $q->where('subject_id', $subjectId);
            });
        }

        $attendances = $query->orderBy('created_at', 'desc')->paginate(15);

        // Calculate statistics scoped to active term filter
        $baseQuery = Attendance::where('student_id', $student->id);
        if ($termFilter === 'current' && $currentTerm) {
            $baseQuery->whereHas('session.subject', function($q) use ($currentTerm) {
                $q->where('term_id', $currentTerm->id);
            });
        }

        $totalSessions    = (clone $baseQuery)->count();
        $attendedSessions = (clone $baseQuery)->where('attended', true)->count();
        $attendanceRate   = $totalSessions > 0
            ? round(($attendedSessions / $totalSessions) * 100, 1)
            : 0;
        $totalMinutes = (clone $baseQuery)->sum('duration_minutes') ?? 0;

        return view('student.attendance.index', compact(
            'attendances',
            'enrolledSubjects',
            'filterSubjects',
            'currentTerm',
            'termFilter',
            'subjectId',
            'totalSessions',
            'attendedSessions',
            'attendanceRate',
            'totalMinutes'
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
        $session = Session::with('subject.term')->findOrFail($sessionId);

        // Check if student has access (enrolled in subject OR session belongs to student's program)
        $isEnrolled = Enrollment::where('student_id', $student->id)
            ->where('subject_id', $session->subject_id)
            ->where('status', 'active')
            ->exists();

        $isInProgram = $student->program_id && $session->subject
            && $session->subject->term
            && $session->subject->term->program_id === $student->program_id;

        if (!$isEnrolled && !$isInProgram) {
            return back()->with('error', 'ليس لديك صلاحية الانضمام لهذه الجلسة');
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

        // Check if program enrollment is pending approval
        if ($student->program_status === 'pending') {
            return view('student.program.pending');
        }

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

        $currentTermNumber = $student->current_term_number ?? 1;

        // Get all terms for this program
        $terms = Term::where('program_id', $program->id)
            ->orderBy('term_number', 'asc')
            ->with(['subjects' => function($q) use ($student) {
                $q->with(['teacher', 'sessions']);
            }])
            ->get();

        // Determine current term intelligently:
        // 1) Term whose dates span today
        // 2) Term matching stored current_term_number
        // 3) First term in program
        $currentTerm = $terms->first(function ($term) {
            return $term->start_date && $term->end_date
                && $term->start_date <= now()
                && $term->end_date   >= now();
        }) ?? $terms->firstWhere('term_number', $currentTermNumber)
          ?? $terms->first();

        // Get enrolled subjects with details
        $enrollments = Enrollment::where('student_id', $student->id)
            ->with(['subject.term', 'subject.teacher', 'subject.sessions'])
            ->get();

        $subjects = Subject::whereIn('term_id', $terms->pluck('id'))->get();

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

        // Current term index (1-based position in sorted list)
        $currentTermIndex = $currentTerm
            ? $terms->search(fn($t) => $t->id === $currentTerm->id) + 1
            : 1;

        $stats = [
            'total_subjects' => $subjects->count(),
            'total_sessions' => $totalSessions,
            'completed_sessions' => $completedSessions,
            'attendance_rate' => $attendanceRate,
            'current_term' => $currentTermIndex,
            'total_terms' => $terms->count(),
            'progress_percentage' => $terms->count() > 0 ? round(($currentTermIndex / $terms->count()) * 100, 1) : 0,
        ];

        return view('student.program.index', compact(
            'program',
            'track',
            'terms',
            'subjects',
            'stats',
            'enrollments',
            'subjectsProgress',
            'currentTermNumber',
            'currentTerm',
            'currentTermIndex'
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

        // If program has a price, redirect to Stripe payment
        if ($program->price && $program->price > 0) {
            $stripeService = app(\App\Services\StripePaymentService::class);

            if (!$stripeService->isConfigured()) {
                return back()->with('error', 'خدمة الدفع غير متاحة حالياً. يرجى التواصل مع الإدارة.');
            }

            // Create payment record
            $payment = \App\Models\Payment::create([
                'user_id' => $student->id,
                'program_id' => $program->id,
                'total_amount' => $program->price,
                'paid_amount' => 0,
                'discount_amount' => 0,
                'remaining_amount' => $program->price,
                'payment_type' => 'full',
                'payment_method' => 'stripe',
                'status' => 'pending',
            ]);

            // Create Stripe Checkout Session
            $result = $stripeService->createCheckoutSession(
                payment: $payment,
                successUrl: route('student.payments.stripe.success'),
                cancelUrl: route('student.payments.stripe.cancel'),
            );

            if ($result['success']) {
                return redirect($result['checkout_url']);
            }

            // Stripe failed - clean up payment record
            $payment->delete();
            return back()->with('error', 'فشل إنشاء جلسة الدفع. يرجى المحاولة مرة أخرى.');
        }

        // Free program - set pending status
        $student->update([
            'program_id' => $program->id,
            'program_status' => 'pending',
            'current_term_number' => 1,
        ]);

        return redirect()->route('student.my-program')
            ->with('success', 'تم إرسال طلب التسجيل في البرنامج: ' . $program->name . '. في انتظار موافقة الإدارة.');
    }

    /**
     * عرض صفحة خدمة بعينها
     */
    public function showLink(string $service)
    {
        $services = [
            'portal' => [
                'key'         => 'student_portal_url',
                'title'       => 'البوابة الإلكترونية',
                'subtitle'    => 'بوابة الطالب الرسمية',
                'description' => 'منصة متكاملة تتيح للطالب الوصول إلى جميع خدماته الأكاديمية والإدارية في مكان واحد.',
                'icon_bg'     => 'linear-gradient(135deg,#3b82f6,#1d4ed8)',
                'badge_bg'    => '#dbeafe',
                'badge_color' => '#1d4ed8',
                'btn_bg'      => 'linear-gradient(135deg,#3b82f6,#1d4ed8)',
                'features'    => [
                    ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'title' => 'السجل الأكاديمي', 'desc' => 'عرض درجاتك وسجلك الكامل'],
                    ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'title' => 'الإشعارات الرسمية', 'desc' => 'استقبال إشعارات الجامعة والكليات'],
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'طلبات الخدمات', 'desc' => 'تقديم الطلبات الإدارية إلكترونياً'],
                    ['icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', 'title' => 'التواصل الأكاديمي', 'desc' => 'التواصل مع الأقسام والأساتذة'],
                ],
                'steps'       => ['افتح البوابة الإلكترونية', 'سجّل الدخول ببيانات الجامعة', 'اختر الخدمة التي تحتاجها'],
            ],
            'library' => [
                'key'         => 'library_url',
                'title'       => 'المكتبة الرقمية',
                'subtitle'    => 'مستودع المعرفة الأكاديمية',
                'description' => 'مكتبة إلكترونية شاملة تضم آلاف الكتب والمراجع والدوريات العلمية المحكّمة.',
                'icon_bg'     => 'linear-gradient(135deg,#8b5cf6,#6d28d9)',
                'badge_bg'    => '#ede9fe',
                'badge_color' => '#6d28d9',
                'btn_bg'      => 'linear-gradient(135deg,#8b5cf6,#6d28d9)',
                'features'    => [
                    ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'title' => 'كتب إلكترونية', 'desc' => 'آلاف الكتب والمراجع العلمية'],
                    ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'title' => 'أبحاث ودوريات', 'desc' => 'مقالات علمية محكّمة من مجلات عالمية'],
                    ['icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4', 'title' => 'تنزيل مباشر', 'desc' => 'تحميل الملفات لقراءتها دون اتصال'],
                    ['icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'title' => 'بحث متقدم', 'desc' => 'فلترة حسب التخصص والسنة والمؤلف'],
                ],
                'steps'       => ['ادخل المكتبة الرقمية', 'ابحث بالكلمة المفتاحية أو التصنيف', 'اقرأ أو نزّل المرجع'],
            ],
            'blackboard' => [
                'key'         => 'blackboard_url',
                'title'       => 'نظام البلاك بورد',
                'subtitle'    => 'منصة إدارة التعلم',
                'description' => 'نظام متكامل لإدارة العملية التعليمية يربط الطلاب بالمحاضرين والمحتوى الدراسي.',
                'icon_bg'     => 'linear-gradient(135deg,#374151,#111827)',
                'badge_bg'    => '#f3f4f6',
                'badge_color' => '#374151',
                'btn_bg'      => 'linear-gradient(135deg,#4b5563,#1f2937)',
                'features'    => [
                    ['icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z', 'title' => 'محاضرات مسجّلة', 'desc' => 'مشاهدة المحاضرات في أي وقت'],
                    ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'title' => 'الواجبات والتكاليف', 'desc' => 'تسليم واستلام الأعمال إلكترونياً'],
                    ['icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'title' => 'متابعة التقدم', 'desc' => 'عرض درجاتك ومعدلك التراكمي'],
                    ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'title' => 'التواصل مع المحاضر', 'desc' => 'مراسلة الأساتذة مباشرة'],
                ],
                'steps'       => ['ادخل إلى البلاك بورد', 'اختر المادة من قائمة موادك', 'تصفح المحتوى أو سلّم الواجب'],
            ],
            'calendar' => [
                'key'         => 'calendar_url',
                'title'       => 'التقويم الأكاديمي',
                'subtitle'    => 'جدول العام الدراسي',
                'description' => 'التقويم الرسمي للعام الدراسي يشمل مواعيد بداية الفصول والامتحانات والإجازات.',
                'icon_bg'     => 'linear-gradient(135deg,#0891b2,#0e7490)',
                'badge_bg'    => '#cffafe',
                'badge_color' => '#0e7490',
                'btn_bg'      => 'linear-gradient(135deg,#06b6d4,#0e7490)',
                'features'    => [
                    ['icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'title' => 'بداية الفصول', 'desc' => 'مواعيد بداية ونهاية كل فصل دراسي'],
                    ['icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'title' => 'فترات الامتحانات', 'desc' => 'جداول الاختبارات النصفية والنهائية'],
                    ['icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z', 'title' => 'الأعياد والإجازات', 'desc' => 'مواعيد الإجازات الرسمية والمناسبات'],
                    ['icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'title' => 'تنبيهات مهمة', 'desc' => 'آخر مواعيد الإضافة والحذف'],
                ],
                'steps'       => ['افتح التقويم الأكاديمي', 'راجع المواعيد المهمة لفصلك الحالي', 'خطّط جدولك بناءً على التواريخ'],
            ],
            'support' => [
                'key'         => 'support_url',
                'title'       => 'الدعم الفني',
                'subtitle'    => 'نحن هنا لمساعدتك',
                'description' => 'فريق الدعم الفني جاهز للإجابة على استفساراتك وحل المشكلات التقنية التي تواجهها.',
                'icon_bg'     => 'linear-gradient(135deg,#16a34a,#15803d)',
                'badge_bg'    => '#dcfce7',
                'badge_color' => '#15803d',
                'btn_bg'      => 'linear-gradient(135deg,#22c55e,#15803d)',
                'features'    => [
                    ['icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z', 'title' => 'دردشة فورية', 'desc' => 'تحدث مع فريق الدعم مباشرة'],
                    ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'title' => 'البريد الإلكتروني', 'desc' => 'أرسل استفسارك وسيُرد عليك'],
                    ['icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'حل سريع', 'desc' => 'وقت استجابة لا يتجاوز 24 ساعة'],
                    ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'title' => 'قاعدة المعرفة', 'desc' => 'إجابات للأسئلة الشائعة'],
                ],
                'steps'       => ['افتح بوابة الدعم الفني', 'اختر نوع المشكلة', 'تحدث مع المختص أو أرسل تذكرة'],
            ],
            'schedule' => [
                'key'         => 'schedule_url',
                'title'       => 'الجدول الدراسي',
                'subtitle'    => 'جدولك الأسبوعي',
                'description' => 'اطّلع على جدولك الدراسي الأسبوعي مع أوقات المحاضرات وأماكن القاعات.',
                'icon_bg'     => 'linear-gradient(135deg,#f59e0b,#d97706)',
                'badge_bg'    => '#fef3c7',
                'badge_color' => '#b45309',
                'btn_bg'      => 'linear-gradient(135deg,#f59e0b,#d97706)',
                'features'    => [
                    ['icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title' => 'أوقات المحاضرات', 'desc' => 'مواعيد كل محاضرة أسبوعياً'],
                    ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z', 'title' => 'أماكن القاعات', 'desc' => 'رقم وموقع قاعة كل مادة'],
                    ['icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'title' => 'اسم المحاضر', 'desc' => 'معلومات الأستاذ لكل مادة'],
                    ['icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4', 'title' => 'تحميل PDF', 'desc' => 'تنزيل الجدول للمشاهدة دون اتصال'],
                ],
                'steps'       => ['ادخل لنظام الجدول الدراسي', 'تأكد من الفصل الدراسي الحالي', 'نزّل الجدول أو احفظه كصورة'],
            ],
        ];

        if (!isset($services[$service])) {
            abort(404);
        }

        $data = $services[$service];
        $data['url'] = \App\Models\Setting::where('key', $data['key'])->value('value') ?? null;
        $data['service'] = $service;

        return view('student.links.service', $data);
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
