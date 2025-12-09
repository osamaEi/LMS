<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Session;
use App\Models\Evaluation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get teacher dashboard with statistics and overview
     * GET /api/v1/teacher/dashboard
     */
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        // Get teacher's subjects with student counts
        $subjects = Subject::where('teacher_id', $teacher->id)
            ->withCount(['enrollments as students_count' => function ($query) {
                $query->where('status', 'active');
            }])
            ->with(['term:id,term_number,name,track_id'])
            ->get();

        // Get upcoming sessions
        $upcomingSessions = Session::whereHas('subject', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>', now())
            ->with(['subject:id,name', 'unit:id,title'])
            ->orderBy('scheduled_at', 'asc')
            ->limit(5)
            ->get();

        // Get pending evaluations (not graded)
        $pendingEvaluations = Evaluation::whereHas('subject', function ($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->where('status', '!=', 'graded')
            ->with(['student:id,name', 'subject:id,name'])
            ->orderBy('due_date', 'asc')
            ->limit(10)
            ->get();

        // Statistics
        $stats = [
            'total_subjects' => Subject::where('teacher_id', $teacher->id)->count(),
            'total_students' => Subject::where('teacher_id', $teacher->id)
                ->withCount('enrollments')
                ->get()
                ->sum('enrollments_count'),
            'upcoming_sessions' => Session::whereHas('subject', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })
                ->where('status', 'scheduled')
                ->where('scheduled_at', '>', now())
                ->count(),
            'pending_evaluations' => Evaluation::whereHas('subject', function ($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })
                ->where('status', '!=', 'graded')
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'teacher' => [
                    'id' => $teacher->id,
                    'name' => $teacher->name,
                    'email' => $teacher->email,
                    'specialization' => $teacher->specialization,
                ],
                'stats' => $stats,
                'subjects' => $subjects,
                'upcoming_sessions' => $upcomingSessions,
                'pending_evaluations' => $pendingEvaluations,
            ],
        ]);
    }

    /**
     * Get all teacher's subjects
     * GET /api/v1/teacher/my-subjects
     */
    public function mySubjects(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $subjects = Subject::where('teacher_id', $teacher->id)
            ->with([
                'term:id,term_number,name,track_id',
                'term.track:id,name',
            ])
            ->withCount([
                'enrollments as students_count' => function ($q) {
                    $q->where('status', 'active');
                },
                'units',
                'sessions',
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }
}
