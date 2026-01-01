<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Session;
use App\Models\Setting;
use App\Models\User;
use App\Services\ZoomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * عرض لوحة تحكم الطالب
     * GET /api/v1/student/dashboard
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $student = $request->user();

            if (!$student->track_id || !$student->current_term_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student is not assigned to any track or term',
                ], 404);
            }

            // الحصول على الربع الحالي للطالب
            $currentTerm = $student->track->terms()
                ->where('term_number', $student->current_term_number)
                ->with(['subjects' => function ($query) use ($student) {
                    $query->with([
                        'teacher:id,name,profile_photo,specialization',
                        'units' => function ($q) {
                            $q->where('status', 'published')
                              ->withCount('sessions');
                        },
                        'enrollments' => function ($q) use ($student) {
                            $q->where('student_id', $student->id);
                        }
                    ]);
                }])
                ->first();

            if (!$currentTerm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current term not found',
                ], 404);
            }

            // حساب إحصائيات لكل مادة
            $subjects = $currentTerm->subjects->map(function ($subject) use ($student) {
                $enrollment = $subject->enrollments->first();
                $totalSessions = $subject->sessions()->count();
                $attendedSessions = $subject->sessions()
                    ->whereHas('attendances', function ($q) use ($student) {
                        $q->where('student_id', $student->id)
                          ->where('attended', true);
                    })
                    ->count();

                $completionPercentage = $totalSessions > 0
                    ? round(($attendedSessions / $totalSessions) * 100, 2)
                    : 0;

                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                    'description' => $subject->description,
                    'banner_photo' => $subject->banner_photo,
                    'credits' => $subject->credits,
                    'total_hours' => $subject->total_hours,
                    'teacher' => $subject->teacher ? [
                        'id' => $subject->teacher->id,
                        'name' => $subject->teacher->name,
                        'photo' => $subject->teacher->profile_photo,
                        'specialization' => $subject->teacher->specialization,
                    ] : null,
                    'units_count' => $subject->units->count(),
                    'total_sessions' => $totalSessions,
                    'enrollment' => $enrollment ? [
                        'status' => $enrollment->status,
                        'enrolled_at' => $enrollment->enrolled_at,
                        'final_grade' => $enrollment->final_grade,
                        'grade_letter' => $enrollment->grade_letter,
                    ] : null,
                    'progress' => [
                        'attended_sessions' => $attendedSessions,
                        'total_sessions' => $totalSessions,
                        'completion_percentage' => $completionPercentage,
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                        'phone' => $student->phone,
                        'national_id' => $student->national_id,
                        'profile_photo' => $student->profile_photo,
                        'status' => $student->status,
                    ],
                    'program' => $student->program ? [
                        'id' => $student->program->id,
                        'name' => $student->program->name,
                        'code' => $student->program->code,
                        'description' => $student->program->description,
                        'duration_months' => $student->program->duration_months,
                    ] : null,
                    'track' => [
                        'id' => $student->track->id,
                        'name' => $student->track->name,
                        'code' => $student->track->code,
                    ],
                    'current_term' => [
                        'id' => $currentTerm->id,
                        'term_number' => $currentTerm->term_number,
                        'name' => $currentTerm->name,
                        'start_date' => $currentTerm->start_date,
                        'end_date' => $currentTerm->end_date,
                    ],
                    'subjects' => $subjects,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * عرض جميع المواد المسجل فيها الطالب
     * GET /api/v1/student/subjects
     */
    public function mySubjects(Request $request): JsonResponse
    {
        try {
            $student = $request->user();

            $enrollments = $student->enrollments()
                ->with([
                    'subject' => function ($query) {
                        $query->with([
                            'teacher:id,name,profile_photo,specialization',
                            'term:id,term_number,name',
                            'units' => function ($q) {
                                $q->where('status', 'published')
                                  ->withCount('sessions');
                            }
                        ]);
                    }
                ])
                ->get();

            $subjects = $enrollments->map(function ($enrollment) use ($student) {
                $subject = $enrollment->subject;
                $totalSessions = $subject->sessions()->count();
                $attendedSessions = $subject->sessions()
                    ->whereHas('attendances', function ($q) use ($student) {
                        $q->where('student_id', $student->id)
                          ->where('attended', true);
                    })
                    ->count();

                return [
                    'subject' => [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'code' => $subject->code,
                        'banner_photo' => $subject->banner_photo,
                        'total_hours' => $subject->total_hours,
                        'units_count' => $subject->units->count(),
                    ],
                    'term' => [
                        'term_number' => $subject->term->term_number,
                        'name' => $subject->term->name,
                    ],
                    'teacher' => $subject->teacher ? [
                        'name' => $subject->teacher->name,
                        'photo' => $subject->teacher->profile_photo,
                    ] : null,
                    'enrollment' => [
                        'status' => $enrollment->status,
                        'enrolled_at' => $enrollment->enrolled_at,
                        'final_grade' => $enrollment->final_grade,
                        'grade_letter' => $enrollment->grade_letter,
                    ],
                    'progress' => [
                        'completion_percentage' => $totalSessions > 0
                            ? round(($attendedSessions / $totalSessions) * 100, 2)
                            : 0,
                    ],
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $subjects,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على الجلسات القادمة للطالب
     * GET /api/v1/student/upcoming-sessions
     */
    public function upcomingSessions(Request $request): JsonResponse
    {
        try {
            $student = $request->user();

            // Get all enrolled subject IDs
            $enrolledSubjectIds = $student->enrollments()
                ->where('status', 'active')
                ->pluck('subject_id');

            // Get upcoming live sessions
            $upcomingSessions = Session::whereIn('subject_id', $enrolledSubjectIds)
                ->where('type', 'live_zoom')
                ->where('scheduled_at', '>=', now())
                ->with(['subject:id,name_ar,name_en,code', 'unit:id,title_ar,title_en'])
                ->orderBy('scheduled_at', 'asc')
                ->limit(10)
                ->get()
                ->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'title' => $session->title,
                        'description' => $session->description,
                        'subject' => [
                            'id' => $session->subject->id,
                            'name' => $session->subject->name,
                            'code' => $session->subject->code,
                        ],
                        'unit' => $session->unit ? [
                            'id' => $session->unit->id,
                            'title' => $session->unit->title,
                        ] : null,
                        'scheduled_at' => $session->scheduled_at,
                        'duration_minutes' => $session->duration_minutes,
                        'has_zoom' => !empty($session->zoom_meeting_id),
                        'zoom_join_url' => $session->zoom_join_url,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $upcomingSessions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الانضمام إلى جلسة Zoom وتسجيل الحضور
     * POST /api/v1/student/sessions/{sessionId}/join-zoom
     */
    public function joinZoom(Request $request, int $sessionId): JsonResponse
    {
        try {
            $student = $request->user();

            // Find the session
            $session = Session::with('subject')->findOrFail($sessionId);

            // Check if student is enrolled in the subject
            $isEnrolled = $student->enrollments()
                ->where('subject_id', $session->subject_id)
                ->where('status', 'active')
                ->exists();

            if (!$isEnrolled) {
                return response()->json([
                    'success' => false,
                    'message' => 'أنت غير مسجل في هذه المادة',
                ], 403);
            }

            // Check if session has Zoom meeting
            if (empty($session->zoom_meeting_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذه الجلسة لا تحتوي على اجتماع Zoom',
                ], 400);
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

            // If attendance already exists but not joined yet, record join
            if (!$attendance->joined_at) {
                $attendance->recordJoin($request->ip(), $request->userAgent());
                $attendance->markAsAttended();
            }

            // Generate Zoom SDK signature for joining
            $zoomService = new ZoomService();
            $signature = $zoomService->generateSignature($session->zoom_meeting_id, 0);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل حضورك بنجاح',
                'data' => [
                    'session' => [
                        'id' => $session->id,
                        'title' => $session->title,
                        'subject_name' => $session->subject->name,
                    ],
                    'zoom' => [
                        'meeting_id' => $session->zoom_meeting_id,
                        'join_url' => $session->zoom_join_url,
                        'password' => $session->zoom_password,
                        'signature' => $signature,
                        'sdk_key' => config('services.zoom.sdk_key'),
                    ],
                    'attendance' => [
                        'id' => $attendance->id,
                        'joined_at' => $attendance->joined_at,
                        'attended' => $attendance->attended,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * تسجيل مغادرة جلسة Zoom
     * POST /api/v1/student/sessions/{sessionId}/leave-zoom
     */
    public function leaveZoom(Request $request, int $sessionId): JsonResponse
    {
        try {
            $student = $request->user();

            // Find attendance record
            $attendance = Attendance::where('student_id', $student->id)
                ->where('session_id', $sessionId)
                ->first();

            if (!$attendance) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على سجل حضور',
                ], 404);
            }

            // Record leave time and calculate duration
            $attendance->recordLeave();

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل مغادرتك',
                'data' => [
                    'attendance' => [
                        'id' => $attendance->id,
                        'joined_at' => $attendance->joined_at,
                        'left_at' => $attendance->left_at,
                        'duration_minutes' => $attendance->duration_minutes,
                        'participation_percentage' => $attendance->getParticipationPercentage(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على سجل حضور الطالب
     * GET /api/v1/student/attendance
     */
    public function myAttendance(Request $request): JsonResponse
    {
        try {
            $student = $request->user();
            $subjectId = $request->query('subject_id');

            $query = Attendance::where('student_id', $student->id)
                ->with(['session.subject:id,name_ar,name_en,code', 'session.unit:id,title_ar,title_en']);

            if ($subjectId) {
                $query->whereHas('session', function ($q) use ($subjectId) {
                    $q->where('subject_id', $subjectId);
                });
            }

            $attendances = $query->orderBy('created_at', 'desc')
                ->paginate(20);

            $attendanceData = $attendances->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'session' => [
                        'id' => $attendance->session->id,
                        'title' => $attendance->session->title,
                        'type' => $attendance->session->type,
                        'scheduled_at' => $attendance->session->scheduled_at,
                        'subject' => [
                            'id' => $attendance->session->subject->id,
                            'name' => $attendance->session->subject->name,
                            'code' => $attendance->session->subject->code,
                        ],
                    ],
                    'attended' => $attendance->attended,
                    'joined_at' => $attendance->joined_at,
                    'left_at' => $attendance->left_at,
                    'duration_minutes' => $attendance->duration_minutes,
                    'watch_percentage' => $attendance->watch_percentage,
                    'participation_percentage' => $attendance->getParticipationPercentage(),
                    'is_full_attendance' => $attendance->isFullAttendance(),
                ];
            });

            // Calculate overall statistics
            $totalSessions = $attendances->total();
            $attendedCount = $attendances->getCollection()->where('attended', true)->count();
            $attendanceRate = $totalSessions > 0 ? round(($attendedCount / $totalSessions) * 100, 2) : 0;

            return response()->json([
                'success' => true,
                'data' => [
                    'attendances' => $attendanceData,
                    'statistics' => [
                        'total_sessions' => $totalSessions,
                        'attended_count' => $attendedCount,
                        'attendance_rate' => $attendanceRate,
                    ],
                    'pagination' => [
                        'current_page' => $attendances->currentPage(),
                        'last_page' => $attendances->lastPage(),
                        'per_page' => $attendances->perPage(),
                        'total' => $attendances->total(),
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على الروابط المفيدة للطالب
     * GET /api/v1/student/links
     */
    public function usefulLinks(Request $request): JsonResponse
    {
        try {
            $student = $request->user();

            // Get links from settings or return default links
            $links = [
                [
                    'id' => 1,
                    'title_ar' => 'البوابة الإلكترونية',
                    'title_en' => 'Student Portal',
                    'url' => Setting::where('key', 'student_portal_url')->value('value') ?? '#',
                    'icon' => 'portal',
                    'type' => 'portal',
                ],
                [
                    'id' => 2,
                    'title_ar' => 'المكتبة الرقمية',
                    'title_en' => 'Digital Library',
                    'url' => Setting::where('key', 'library_url')->value('value') ?? '#',
                    'icon' => 'library',
                    'type' => 'library',
                ],
                [
                    'id' => 3,
                    'title_ar' => 'الدعم الفني',
                    'title_en' => 'Technical Support',
                    'url' => Setting::where('key', 'support_url')->value('value') ?? '#',
                    'icon' => 'support',
                    'type' => 'support',
                ],
                [
                    'id' => 4,
                    'title_ar' => 'الجدول الدراسي',
                    'title_en' => 'Academic Schedule',
                    'url' => Setting::where('key', 'schedule_url')->value('value') ?? '#',
                    'icon' => 'schedule',
                    'type' => 'schedule',
                ],
                [
                    'id' => 5,
                    'title_ar' => 'نظام البلاك بورد',
                    'title_en' => 'Blackboard System',
                    'url' => Setting::where('key', 'blackboard_url')->value('value') ?? '#',
                    'icon' => 'blackboard',
                    'type' => 'lms',
                ],
                [
                    'id' => 6,
                    'title_ar' => 'التقويم الأكاديمي',
                    'title_en' => 'Academic Calendar',
                    'url' => Setting::where('key', 'calendar_url')->value('value') ?? '#',
                    'icon' => 'calendar',
                    'type' => 'calendar',
                ],
            ];

            // Add program-specific links if available
            if ($student->program) {
                $programLinks = Setting::where('key', 'program_' . $student->program->id . '_links')
                    ->value('value');

                if ($programLinks) {
                    $decodedLinks = json_decode($programLinks, true);
                    if (is_array($decodedLinks)) {
                        $links = array_merge($links, $decodedLinks);
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => $links,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * الحصول على إحصائيات الطالب الشاملة
     * GET /api/v1/student/statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        try {
            $student = $request->user();

            // Get enrollments statistics
            $enrollments = $student->enrollments()->with('subject')->get();
            $totalSubjects = $enrollments->count();
            $completedSubjects = $enrollments->where('status', 'completed')->count();
            $activeSubjects = $enrollments->where('status', 'active')->count();

            // Get attendance statistics
            $totalAttendances = Attendance::where('student_id', $student->id)->count();
            $attendedSessions = Attendance::where('student_id', $student->id)
                ->where('attended', true)
                ->count();
            $overallAttendanceRate = $totalAttendances > 0
                ? round(($attendedSessions / $totalAttendances) * 100, 2)
                : 0;

            // Get average grade
            $gradesSum = $enrollments->whereNotNull('final_grade')->sum('final_grade');
            $gradesCount = $enrollments->whereNotNull('final_grade')->count();
            $averageGrade = $gradesCount > 0 ? round($gradesSum / $gradesCount, 2) : null;

            // Get upcoming sessions count
            $enrolledSubjectIds = $enrollments->where('status', 'active')->pluck('subject_id');
            $upcomingSessionsCount = Session::whereIn('subject_id', $enrolledSubjectIds)
                ->where('type', 'live_zoom')
                ->where('scheduled_at', '>=', now())
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'subjects' => [
                        'total' => $totalSubjects,
                        'active' => $activeSubjects,
                        'completed' => $completedSubjects,
                    ],
                    'attendance' => [
                        'total_sessions' => $totalAttendances,
                        'attended' => $attendedSessions,
                        'rate' => $overallAttendanceRate,
                    ],
                    'grades' => [
                        'average' => $averageGrade,
                        'graded_subjects' => $gradesCount,
                    ],
                    'upcoming_sessions' => $upcomingSessionsCount,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
