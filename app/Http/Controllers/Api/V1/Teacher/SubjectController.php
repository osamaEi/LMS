<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Enrollment;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * Get all subjects taught by the authenticated teacher
     */
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $subjects = Subject::where('teacher_id', $teacher->id)
            ->with(['term.track'])
            ->withCount([
                'enrollments as students_count',
                'units',
                'sessions'
            ])
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }

    /**
     * Get specific subject details with units and sessions
     */
    public function show(Request $request, $id): JsonResponse
    {
        $teacher = $request->user();

        $subject = Subject::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with([
                'term.track',
                'units.sessions.files',
            ])
            ->withCount(['enrollments as students_count'])
            ->first();

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'Subject not found or you do not have access to it',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $subject,
        ]);
    }

    /**
     * Get all students enrolled in a subject
     */
    public function students(Request $request, $id): JsonResponse
    {
        $teacher = $request->user();

        $subject = Subject::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'Subject not found or you do not have access to it',
            ], 404);
        }

        $enrollments = Enrollment::where('subject_id', $id)
            ->with(['student'])
            ->withCount([
                'attendances as attended_sessions',
            ])
            ->get()
            ->map(function ($enrollment) use ($subject) {
                // Calculate attendance rate
                $totalSessions = $subject->sessions()->count();
                $attendanceRate = $totalSessions > 0
                    ? round(($enrollment->attended_sessions / $totalSessions) * 100, 2)
                    : 0;

                return [
                    'enrollment_id' => $enrollment->id,
                    'student' => [
                        'id' => $enrollment->student->id,
                        'name' => $enrollment->student->name,
                        'email' => $enrollment->student->email,
                        'national_id' => $enrollment->student->national_id,
                    ],
                    'enrolled_at' => $enrollment->enrolled_at,
                    'status' => $enrollment->status,
                    'progress_percentage' => $enrollment->progress_percentage,
                    'final_grade' => $enrollment->final_grade,
                    'attended_sessions' => $enrollment->attended_sessions,
                    'total_sessions' => $totalSessions,
                    'attendance_rate' => $attendanceRate,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'subject' => [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                ],
                'total_students' => $enrollments->count(),
                'students' => $enrollments,
            ],
        ]);
    }

    /**
     * Get subject statistics
     */
    public function statistics(Request $request, $id): JsonResponse
    {
        $teacher = $request->user();

        $subject = Subject::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'Subject not found or you do not have access to it',
            ], 404);
        }

        // Get enrollments
        $enrollments = Enrollment::where('subject_id', $id)->get();
        $totalStudents = $enrollments->count();

        // Get sessions
        $totalSessions = $subject->sessions()->count();
        $completedSessions = $subject->sessions()->where('status', 'completed')->count();
        $upcomingSessions = $subject->sessions()
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>', now())
            ->count();

        // Calculate attendance statistics
        $totalAttendances = Attendance::whereHas('session.subject', function ($q) use ($id) {
            $q->where('id', $id);
        })->count();

        $totalPossibleAttendances = $totalStudents * $totalSessions;
        $overallAttendanceRate = $totalPossibleAttendances > 0
            ? round(($totalAttendances / $totalPossibleAttendances) * 100, 2)
            : 0;

        // Enrollment status breakdown
        $enrollmentsByStatus = $enrollments->groupBy('status')->map->count();

        // Progress statistics
        $avgProgress = $enrollments->avg('progress_percentage') ?? 0;
        $studentsCompleted = $enrollments->where('progress_percentage', 100)->count();

        // Grade statistics
        $gradesData = $enrollments->whereNotNull('final_grade');
        $avgGrade = $gradesData->avg('final_grade') ?? 0;
        $passedStudents = $gradesData->where('final_grade', '>=', 50)->count();
        $failedStudents = $gradesData->where('final_grade', '<', 50)->count();

        $stats = [
            'students' => [
                'total' => $totalStudents,
                'by_status' => [
                    'active' => $enrollmentsByStatus['active'] ?? 0,
                    'completed' => $enrollmentsByStatus['completed'] ?? 0,
                    'dropped' => $enrollmentsByStatus['dropped'] ?? 0,
                    'withdrawn' => $enrollmentsByStatus['withdrawn'] ?? 0,
                ],
                'completed_course' => $studentsCompleted,
            ],
            'sessions' => [
                'total' => $totalSessions,
                'completed' => $completedSessions,
                'upcoming' => $upcomingSessions,
                'in_progress' => $totalSessions - $completedSessions - $upcomingSessions,
            ],
            'attendance' => [
                'total_attendances' => $totalAttendances,
                'total_possible' => $totalPossibleAttendances,
                'overall_rate' => $overallAttendanceRate,
            ],
            'progress' => [
                'average_progress' => round($avgProgress, 2),
                'students_completed' => $studentsCompleted,
                'completion_rate' => $totalStudents > 0
                    ? round(($studentsCompleted / $totalStudents) * 100, 2)
                    : 0,
            ],
            'grades' => [
                'average_grade' => round($avgGrade, 2),
                'graded_students' => $gradesData->count(),
                'passed_students' => $passedStudents,
                'failed_students' => $failedStudents,
                'pass_rate' => $gradesData->count() > 0
                    ? round(($passedStudents / $gradesData->count()) * 100, 2)
                    : 0,
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'subject' => [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                ],
                'statistics' => $stats,
            ],
        ]);
    }
}
