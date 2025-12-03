<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\User;
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
                        'profile_photo' => $student->profile_photo,
                    ],
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
}
