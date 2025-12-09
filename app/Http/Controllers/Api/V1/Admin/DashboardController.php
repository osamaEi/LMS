<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subject;
use App\Models\Session;
use App\Models\Enrollment;
use App\Models\Attendance;
use App\Models\Program;
use App\Models\Track;
use App\Models\Evaluation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Get comprehensive admin dashboard statistics
     * GET /api/v1/admin/dashboard
     */
    public function index(Request $request): JsonResponse
    {
        $stats = [
            'users' => $this->getUserStats(),
            'programs' => $this->getProgramStats(),
            'subjects' => $this->getSubjectStats(),
            'sessions' => $this->getSessionStats(),
            'enrollments' => $this->getEnrollmentStats(),
            'attendance' => $this->getAttendanceStats(),
            'evaluations' => $this->getEvaluationStats(),
            'recent_activities' => $this->getRecentActivities(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get user statistics
     */
    private function getUserStats(): array
    {
        return [
            'total' => User::count(),
            'students' => User::where('role', 'student')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'admins' => User::whereIn('role', ['admin', 'super_admin'])->count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'suspended' => User::where('status', 'suspended')->count(),
            'new_this_month' => User::whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count(),
            'by_role' => User::select('role', DB::raw('count(*) as count'))
                            ->groupBy('role')
                            ->get()
                            ->pluck('count', 'role'),
            'by_status' => User::select('status', DB::raw('count(*) as count'))
                              ->groupBy('status')
                              ->get()
                              ->pluck('count', 'status'),
        ];
    }

    /**
     * Get program statistics
     */
    private function getProgramStats(): array
    {
        return [
            'total' => Program::count(),
            'active' => Program::where('status', 'active')->count(),
            'inactive' => Program::where('status', 'inactive')->count(),
            'with_students' => Program::has('students')->count(),
            'total_tracks' => Track::count(),
            'students_per_program' => Program::withCount('students')->get()->map(function($program) {
                return [
                    'name' => $program->name,
                    'students_count' => $program->students_count,
                ];
            }),
        ];
    }

    /**
     * Get subject statistics
     */
    private function getSubjectStats(): array
    {
        return [
            'total' => Subject::count(),
            'active' => Subject::where('status', 'active')->count(),
            'draft' => Subject::where('status', 'draft')->count(),
            'archived' => Subject::where('status', 'archived')->count(),
            'with_enrollments' => Subject::has('enrollments')->count(),
            'total_units' => DB::table('units')->count(),
            'subjects_per_term' => Subject::select('term_id', DB::raw('count(*) as count'))
                                         ->groupBy('term_id')
                                         ->with('term:id,name')
                                         ->get()
                                         ->map(function($item) {
                                             return [
                                                 'term' => $item->term->name ?? 'N/A',
                                                 'count' => $item->count,
                                             ];
                                         }),
        ];
    }

    /**
     * Get session statistics
     */
    private function getSessionStats(): array
    {
        return [
            'total' => Session::count(),
            'live_sessions' => Session::where('type', 'live_zoom')->count(),
            'recorded_videos' => Session::where('type', 'recorded_video')->count(),
            'scheduled' => Session::where('status', 'scheduled')->count(),
            'completed' => Session::where('status', 'completed')->count(),
            'cancelled' => Session::where('status', 'cancelled')->count(),
            'upcoming' => Session::where('status', 'scheduled')
                                ->where('scheduled_at', '>', now())
                                ->count(),
            'today' => Session::whereDate('scheduled_at', today())->count(),
            'this_week' => Session::whereBetween('scheduled_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
        ];
    }

    /**
     * Get enrollment statistics
     */
    private function getEnrollmentStats(): array
    {
        return [
            'total' => Enrollment::count(),
            'active' => Enrollment::where('status', 'active')->count(),
            'completed' => Enrollment::where('status', 'completed')->count(),
            'dropped' => Enrollment::where('status', 'dropped')->count(),
            'pending' => Enrollment::where('status', 'pending')->count(),
            'this_month' => Enrollment::whereMonth('enrolled_at', now()->month)
                                      ->whereYear('enrolled_at', now()->year)
                                      ->count(),
            'completion_rate' => $this->calculateCompletionRate(),
            'average_grade' => Enrollment::whereNotNull('final_grade')->avg('final_grade'),
        ];
    }

    /**
     * Get attendance statistics
     */
    private function getAttendanceStats(): array
    {
        $totalAttendance = Attendance::count();
        $attended = Attendance::where('attended', true)->count();

        return [
            'total_records' => $totalAttendance,
            'attended' => $attended,
            'absent' => $totalAttendance - $attended,
            'attendance_rate' => $totalAttendance > 0
                ? round(($attended / $totalAttendance) * 100, 2)
                : 0,
            'video_completed' => Attendance::where('video_completed', true)->count(),
            'today' => Attendance::whereDate('created_at', today())->count(),
            'this_week' => Attendance::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'average_watch_percentage' => Attendance::avg('watch_percentage'),
        ];
    }

    /**
     * Get evaluation statistics
     */
    private function getEvaluationStats(): array
    {
        return [
            'total' => Evaluation::count(),
            'assignments' => Evaluation::where('type', 'assignment')->count(),
            'quizzes' => Evaluation::where('type', 'quiz')->count(),
            'exams' => Evaluation::where('type', 'exam')->count(),
            'projects' => Evaluation::where('type', 'project')->count(),
            'published' => Evaluation::where('status', 'published')->count(),
            'draft' => Evaluation::where('status', 'draft')->count(),
            'pending_submissions' => DB::table('evaluation_submissions')
                                       ->where('status', 'pending')
                                       ->count(),
            'graded_submissions' => DB::table('evaluation_submissions')
                                      ->where('status', 'graded')
                                      ->count(),
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities(): array
    {
        $activities = [];

        // Recent enrollments
        $recentEnrollments = Enrollment::with(['student:id,name', 'subject:id,name'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($enrollment) {
                return [
                    'type' => 'enrollment',
                    'message' => "{$enrollment->student->name} enrolled in {$enrollment->subject->name}",
                    'timestamp' => $enrollment->enrolled_at,
                ];
            });

        // Recent users
        $recentUsers = User::latest()
            ->take(5)
            ->get()
            ->map(function($user) {
                return [
                    'type' => 'user',
                    'message' => "New {$user->role} registered: {$user->name}",
                    'timestamp' => $user->created_at,
                ];
            });

        // Recent sessions
        $recentSessions = Session::with(['subject:id,name'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function($session) {
                return [
                    'type' => 'session',
                    'message' => "New session created: {$session->title} for {$session->subject->name}",
                    'timestamp' => $session->created_at,
                ];
            });

        $activities = collect()
            ->merge($recentEnrollments)
            ->merge($recentUsers)
            ->merge($recentSessions)
            ->sortByDesc('timestamp')
            ->take(10)
            ->values();

        return $activities->toArray();
    }

    /**
     * Calculate completion rate
     */
    private function calculateCompletionRate(): float
    {
        $total = Enrollment::count();
        if ($total === 0) {
            return 0;
        }

        $completed = Enrollment::where('status', 'completed')->count();
        return round(($completed / $total) * 100, 2);
    }

    /**
     * Get chart data for analytics
     * GET /api/v1/admin/dashboard/charts
     */
    public function getChartData(Request $request): JsonResponse
    {
        $type = $request->get('type', 'enrollments');
        $period = $request->get('period', '7days'); // 7days, 30days, 12months

        $data = [];

        switch ($type) {
            case 'enrollments':
                $data = $this->getEnrollmentChartData($period);
                break;
            case 'attendance':
                $data = $this->getAttendanceChartData($period);
                break;
            case 'users':
                $data = $this->getUserChartData($period);
                break;
            case 'sessions':
                $data = $this->getSessionChartData($period);
                break;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get enrollment chart data
     */
    private function getEnrollmentChartData(string $period): array
    {
        $query = Enrollment::select(
            DB::raw('DATE(enrolled_at) as date'),
            DB::raw('count(*) as count')
        );

        if ($period === '7days') {
            $query->where('enrolled_at', '>=', now()->subDays(7));
        } elseif ($period === '30days') {
            $query->where('enrolled_at', '>=', now()->subDays(30));
        } elseif ($period === '12months') {
            $query->where('enrolled_at', '>=', now()->subMonths(12));
        }

        return $query->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(function($item) {
                        return [
                            'date' => $item->date,
                            'count' => $item->count,
                        ];
                    })
                    ->toArray();
    }

    /**
     * Get attendance chart data
     */
    private function getAttendanceChartData(string $period): array
    {
        $query = Attendance::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(CASE WHEN attended = 1 THEN 1 ELSE 0 END) as attended'),
            DB::raw('SUM(CASE WHEN attended = 0 THEN 1 ELSE 0 END) as absent')
        );

        if ($period === '7days') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($period === '30days') {
            $query->where('created_at', '>=', now()->subDays(30));
        } elseif ($period === '12months') {
            $query->where('created_at', '>=', now()->subMonths(12));
        }

        return $query->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->toArray();
    }

    /**
     * Get user chart data
     */
    private function getUserChartData(string $period): array
    {
        $query = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        );

        if ($period === '7days') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($period === '30days') {
            $query->where('created_at', '>=', now()->subDays(30));
        } elseif ($period === '12months') {
            $query->where('created_at', '>=', now()->subMonths(12));
        }

        return $query->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->map(function($item) {
                        return [
                            'date' => $item->date,
                            'count' => $item->count,
                        ];
                    })
                    ->toArray();
    }

    /**
     * Get session chart data
     */
    private function getSessionChartData(string $period): array
    {
        $query = Session::select(
            DB::raw('DATE(scheduled_at) as date'),
            DB::raw('count(*) as count'),
            DB::raw('SUM(CASE WHEN type = "live_zoom" THEN 1 ELSE 0 END) as live'),
            DB::raw('SUM(CASE WHEN type = "recorded_video" THEN 1 ELSE 0 END) as recorded')
        );

        if ($period === '7days') {
            $query->where('scheduled_at', '>=', now()->subDays(7));
        } elseif ($period === '30days') {
            $query->where('scheduled_at', '>=', now()->subDays(30));
        } elseif ($period === '12months') {
            $query->where('scheduled_at', '>=', now()->subMonths(12));
        }

        return $query->groupBy('date')
                    ->orderBy('date')
                    ->get()
                    ->toArray();
    }

    /**
     * Get system overview
     * GET /api/v1/admin/dashboard/overview
     */
    public function getOverview(): JsonResponse
    {
        $overview = [
            'system_health' => [
                'database_size' => $this->getDatabaseSize(),
                'total_files' => DB::table('files')->count(),
                'storage_used' => $this->getStorageUsed(),
            ],
            'quick_stats' => [
                'active_sessions_today' => Session::whereDate('scheduled_at', today())
                                                  ->where('status', 'scheduled')
                                                  ->count(),
                'pending_evaluations' => DB::table('evaluation_submissions')
                                           ->where('status', 'pending')
                                           ->count(),
                'new_enrollments_week' => Enrollment::whereBetween('enrolled_at', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'active_students' => User::where('role', 'student')
                                        ->where('status', 'active')
                                        ->count(),
            ],
            'top_subjects' => $this->getTopSubjects(),
            'top_teachers' => $this->getTopTeachers(),
        ];

        return response()->json([
            'success' => true,
            'data' => $overview,
        ]);
    }

    /**
     * Get database size
     */
    private function getDatabaseSize(): string
    {
        try {
            $size = DB::select("
                SELECT
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb
                FROM information_schema.tables
                WHERE table_schema = DATABASE()
            ");

            return ($size[0]->size_mb ?? 0) . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get storage used
     */
    private function getStorageUsed(): string
    {
        try {
            $totalSize = DB::table('files')->sum('file_size');
            $sizeMB = round($totalSize / 1024 / 1024, 2);
            return $sizeMB . ' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    /**
     * Get top subjects by enrollment
     */
    private function getTopSubjects(): array
    {
        return Subject::withCount('enrollments')
            ->orderBy('enrollments_count', 'desc')
            ->take(5)
            ->get()
            ->map(function($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                    'enrollments_count' => $subject->enrollments_count,
                ];
            })
            ->toArray();
    }

    /**
     * Get top teachers by subjects
     */
    private function getTopTeachers(): array
    {
        return User::where('role', 'teacher')
            ->withCount('subjects')
            ->orderBy('subjects_count', 'desc')
            ->take(5)
            ->get()
            ->map(function($teacher) {
                return [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'subjects_count' => $teacher->subjects_count,
                ];
            })
            ->toArray();
    }
}
