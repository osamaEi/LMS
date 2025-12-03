<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    /**
     * عرض تفاصيل المادة مع كل المحتوى
     * GET /api/v1/student/subjects/{id}
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $student = $request->user();

            $subject = Subject::with([
                'term:id,term_number,name,track_id',
                'term.track:id,name',
                'teacher:id,name,profile_photo,specialization,bio',
                'units' => function ($query) {
                    $query->where('status', 'published')
                        ->orderBy('order', 'asc')
                        ->with(['sessions' => function ($q) {
                            $q->orderBy('session_number', 'asc')
                              ->with('files');
                        }]);
                },
                'enrollments' => function ($query) use ($student) {
                    $query->where('student_id', $student->id);
                }
            ])->findOrFail($id);

            // التحقق من أن الطالب مسجل في المادة
            $enrollment = $subject->enrollments->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not enrolled in this subject',
                ], 403);
            }

            // حساب إحصائيات المادة
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

            // تنسيق الوحدات مع الجلسات
            $units = $subject->units->map(function ($unit) use ($student) {
                $sessions = $unit->sessions->map(function ($session) use ($student) {
                    // الحصول على حضور الطالب للجلسة
                    $attendance = $session->attendances()
                        ->where('student_id', $student->id)
                        ->first();

                    // تنسيق الملفات (فيديوهات، PDFs، Zoom)
                    $files = $session->files->map(function ($file) {
                        $fileData = [
                            'id' => $file->id,
                            'type' => $file->type,
                            'title' => $file->title,
                            'description' => $file->description,
                            'order' => $file->order,
                            'is_mandatory' => $file->is_mandatory,
                        ];

                        if ($file->isVideo()) {
                            $fileData['video'] = [
                                'url' => $file->getFileUrl(),
                                'duration' => $file->video_duration,
                                'platform' => $file->video_platform,
                                'size' => $file->getFormattedSize(),
                            ];
                        } elseif ($file->isPDF()) {
                            $fileData['pdf'] = [
                                'url' => $file->getFileUrl(),
                                'size' => $file->getFormattedSize(),
                            ];
                        } elseif ($file->isZoom()) {
                            $fileData['zoom'] = [
                                'join_url' => $file->zoom_join_url,
                                'meeting_id' => $file->zoom_meeting_id,
                                'password' => $file->zoom_password,
                                'scheduled_at' => $file->zoom_scheduled_at,
                                'duration' => $file->getDurationFormatted(),
                            ];
                        }

                        return $fileData;
                    });

                    return [
                        'id' => $session->id,
                        'title' => $session->title,
                        'description' => $session->description,
                        'type' => $session->type,
                        'session_number' => $session->session_number,
                        'scheduled_at' => $session->scheduled_at,
                        'duration_minutes' => $session->duration_minutes,
                        'status' => $session->status,
                        'is_mandatory' => $session->is_mandatory,

                        // الملفات (فيديوهات، PDFs، Zoom)
                        'files' => $files,

                        // حضور الطالب
                        'my_attendance' => $attendance ? [
                            'attended' => $attendance->attended,
                            'watch_percentage' => $attendance->watch_percentage,
                            'video_completed' => $attendance->video_completed,
                            'joined_at' => $attendance->joined_at,
                            'duration_minutes' => $attendance->duration_minutes,
                        ] : null,
                    ];
                });

                // حساب تقدم الطالب في الوحدة
                $unitProgress = $unit->getCompletionPercentage($student->id);

                return [
                    'id' => $unit->id,
                    'title' => $unit->title,
                    'description' => $unit->description,
                    'unit_number' => $unit->unit_number,
                    'duration_hours' => $unit->duration_hours,
                    'learning_objectives' => $unit->learning_objectives,
                    'sessions_count' => $unit->sessions->count(),
                    'completion_percentage' => $unitProgress,
                    'sessions' => $sessions,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'subject' => [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'code' => $subject->code,
                        'description' => $subject->description,
                        'banner_photo' => $subject->banner_photo,
                        'credits' => $subject->credits,
                        'total_hours' => $subject->total_hours,
                        'status' => $subject->status,
                    ],
                    'term' => [
                        'term_number' => $subject->term->term_number,
                        'name' => $subject->term->name,
                        'track' => [
                            'id' => $subject->term->track->id,
                            'name' => $subject->term->track->name,
                        ],
                    ],
                    'teacher' => $subject->teacher ? [
                        'id' => $subject->teacher->id,
                        'name' => $subject->teacher->name,
                        'photo' => $subject->teacher->profile_photo,
                        'specialization' => $subject->teacher->specialization,
                        'bio' => $subject->teacher->bio,
                    ] : null,
                    'enrollment' => [
                        'status' => $enrollment->status,
                        'enrolled_at' => $enrollment->enrolled_at,
                        'final_grade' => $enrollment->final_grade,
                        'grade_letter' => $enrollment->grade_letter,
                    ],
                    'progress' => [
                        'attended_sessions' => $attendedSessions,
                        'total_sessions' => $totalSessions,
                        'completion_percentage' => $completionPercentage,
                    ],
                    'units' => $units,
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
     * عرض تفاصيل وحدة معينة
     * GET /api/v1/student/units/{id}
     */
    public function showUnit(int $id, Request $request): JsonResponse
    {
        try {
            $student = $request->user();

            $unit = \App\Models\Unit::with([
                'subject:id,name,code,teacher_id',
                'subject.teacher:id,name,profile_photo,specialization',
                'sessions' => function ($q) {
                    $q->orderBy('session_number', 'asc')
                      ->with('files');
                }
            ])->findOrFail($id);

            // التحقق من أن الطالب مسجل في المادة
            $enrollment = $unit->subject->enrollments()
                ->where('student_id', $student->id)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not enrolled in this subject',
                ], 403);
            }

            $sessions = $unit->sessions->map(function ($session) use ($student) {
                $attendance = $session->attendances()
                    ->where('student_id', $student->id)
                    ->first();

                // تنسيق الملفات
                $files = $session->files->map(function ($file) {
                    $fileData = [
                        'id' => $file->id,
                        'type' => $file->type,
                        'title' => $file->title,
                        'description' => $file->description,
                        'order' => $file->order,
                        'is_mandatory' => $file->is_mandatory,
                    ];

                    if ($file->isVideo()) {
                        $fileData['video'] = [
                            'url' => $file->getFileUrl(),
                            'duration' => $file->video_duration,
                            'platform' => $file->video_platform,
                            'size' => $file->getFormattedSize(),
                        ];
                    } elseif ($file->isPDF()) {
                        $fileData['pdf'] = [
                            'url' => $file->getFileUrl(),
                            'size' => $file->getFormattedSize(),
                        ];
                    } elseif ($file->isZoom()) {
                        $fileData['zoom'] = [
                            'join_url' => $file->zoom_join_url,
                            'meeting_id' => $file->zoom_meeting_id,
                            'password' => $file->zoom_password,
                            'scheduled_at' => $file->zoom_scheduled_at,
                            'duration' => $file->getDurationFormatted(),
                        ];
                    }

                    return $fileData;
                });

                return [
                    'id' => $session->id,
                    'title' => $session->title,
                    'description' => $session->description,
                    'type' => $session->type,
                    'session_number' => $session->session_number,
                    'scheduled_at' => $session->scheduled_at,
                    'duration_minutes' => $session->duration_minutes,
                    'status' => $session->status,
                    'files' => $files,
                    'my_attendance' => $attendance ? [
                        'attended' => $attendance->attended,
                        'watch_percentage' => $attendance->watch_percentage,
                        'video_completed' => $attendance->video_completed,
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'unit' => [
                        'id' => $unit->id,
                        'title' => $unit->title,
                        'description' => $unit->description,
                        'unit_number' => $unit->unit_number,
                        'duration_hours' => $unit->duration_hours,
                        'learning_objectives' => $unit->learning_objectives,
                    ],
                    'subject' => [
                        'id' => $unit->subject->id,
                        'name' => $unit->subject->name,
                        'code' => $unit->subject->code,
                    ],
                    'completion_percentage' => $unit->getCompletionPercentage($student->id),
                    'sessions' => $sessions,
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
