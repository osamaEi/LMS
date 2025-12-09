<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Session;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Get attendance records for a specific session
     */
    public function index(Request $request, $sessionId): JsonResponse
    {
        $teacher = $request->user();

        // Verify session belongs to teacher
        $session = Session::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->with(['subject', 'unit'])->find($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or you do not have access to it',
            ], 404);
        }

        // Get all students enrolled in this subject
        $enrollments = Enrollment::where('subject_id', $session->subject_id)
            ->with(['student'])
            ->get();

        // Get attendance records for this session
        $attendances = Attendance::where('session_id', $sessionId)
            ->with(['student'])
            ->get()
            ->keyBy('student_id');

        // Build attendance list
        $attendanceList = $enrollments->map(function ($enrollment) use ($attendances) {
            $attendance = $attendances->get($enrollment->student_id);

            return [
                'student' => [
                    'id' => $enrollment->student->id,
                    'name' => $enrollment->student->name,
                    'email' => $enrollment->student->email,
                    'national_id' => $enrollment->student->national_id,
                ],
                'attendance' => $attendance ? [
                    'id' => $attendance->id,
                    'status' => $attendance->status,
                    'attended_at' => $attendance->attended_at,
                    'video_watched_duration' => $attendance->video_watched_duration,
                    'video_watched_percentage' => $attendance->video_watched_percentage,
                    'notes' => $attendance->notes,
                ] : null,
            ];
        });

        // Calculate statistics
        $totalStudents = $enrollments->count();
        $presentCount = $attendances->where('status', 'present')->count();
        $absentCount = $attendances->where('status', 'absent')->count();
        $lateCount = $attendances->where('status', 'late')->count();
        $notMarkedCount = $totalStudents - $attendances->count();

        return response()->json([
            'success' => true,
            'data' => [
                'session' => [
                    'id' => $session->id,
                    'title' => $session->title,
                    'scheduled_at' => $session->scheduled_at,
                    'status' => $session->status,
                ],
                'statistics' => [
                    'total_students' => $totalStudents,
                    'present' => $presentCount,
                    'absent' => $absentCount,
                    'late' => $lateCount,
                    'not_marked' => $notMarkedCount,
                    'attendance_rate' => $totalStudents > 0
                        ? round(($presentCount / $totalStudents) * 100, 2)
                        : 0,
                ],
                'attendance_list' => $attendanceList,
            ],
        ]);
    }

    /**
     * Mark attendance for a student
     */
    public function store(Request $request, $sessionId): JsonResponse
    {
        $teacher = $request->user();

        // Verify session belongs to teacher
        $session = Session::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or you do not have access to it',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:users,id',
            'status' => 'required|in:present,absent,late,excused',
            'attended_at' => 'nullable|date',
            'video_watched_duration' => 'nullable|integer|min:0',
            'video_watched_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify student is enrolled in this subject
        $enrollment = Enrollment::where('subject_id', $session->subject_id)
            ->where('student_id', $request->student_id)
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Student is not enrolled in this subject',
            ], 404);
        }

        // Check if attendance already exists
        $existingAttendance = Attendance::where('session_id', $sessionId)
            ->where('student_id', $request->student_id)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance already marked for this student in this session',
            ], 409);
        }

        $attendanceData = $request->all();
        $attendanceData['session_id'] = $sessionId;
        $attendanceData['enrollment_id'] = $enrollment->id;
        $attendanceData['attended_at'] = $attendanceData['attended_at'] ?? now();

        $attendance = Attendance::create($attendanceData);

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully',
            'data' => $attendance->load(['student', 'session']),
        ], 201);
    }

    /**
     * Update attendance record
     */
    public function update(Request $request, $sessionId, $attendanceId): JsonResponse
    {
        $teacher = $request->user();

        // Verify session belongs to teacher
        $session = Session::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or you do not have access to it',
            ], 404);
        }

        $attendance = Attendance::where('session_id', $sessionId)->find($attendanceId);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|required|in:present,absent,late,excused',
            'attended_at' => 'nullable|date',
            'video_watched_duration' => 'nullable|integer|min:0',
            'video_watched_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $attendance->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully',
            'data' => $attendance->load(['student', 'session']),
        ]);
    }

    /**
     * Delete attendance record
     */
    public function destroy(Request $request, $sessionId, $attendanceId): JsonResponse
    {
        $teacher = $request->user();

        // Verify session belongs to teacher
        $session = Session::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or you do not have access to it',
            ], 404);
        }

        $attendance = Attendance::where('session_id', $sessionId)->find($attendanceId);

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record not found',
            ], 404);
        }

        $attendance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attendance record deleted successfully',
        ]);
    }

    /**
     * Bulk mark attendance for multiple students
     */
    public function bulkStore(Request $request, $sessionId): JsonResponse
    {
        $teacher = $request->user();

        // Verify session belongs to teacher
        $session = Session::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or you do not have access to it',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'attendances' => 'required|array|min:1',
            'attendances.*.student_id' => 'required|exists:users,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
            'attendances.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $created = [];
        $errors = [];

        foreach ($request->attendances as $attendanceData) {
            // Verify student is enrolled
            $enrollment = Enrollment::where('subject_id', $session->subject_id)
                ->where('student_id', $attendanceData['student_id'])
                ->first();

            if (!$enrollment) {
                $errors[] = [
                    'student_id' => $attendanceData['student_id'],
                    'message' => 'Student not enrolled in this subject',
                ];
                continue;
            }

            // Check if attendance already exists
            $existingAttendance = Attendance::where('session_id', $sessionId)
                ->where('student_id', $attendanceData['student_id'])
                ->first();

            if ($existingAttendance) {
                $errors[] = [
                    'student_id' => $attendanceData['student_id'],
                    'message' => 'Attendance already marked',
                ];
                continue;
            }

            // Create attendance
            $attendance = Attendance::create([
                'session_id' => $sessionId,
                'student_id' => $attendanceData['student_id'],
                'enrollment_id' => $enrollment->id,
                'status' => $attendanceData['status'],
                'attended_at' => now(),
                'notes' => $attendanceData['notes'] ?? null,
            ]);

            $created[] = $attendance->load(['student']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk attendance marking completed',
            'data' => [
                'created' => $created,
                'errors' => $errors,
                'total_processed' => count($request->attendances),
                'total_created' => count($created),
                'total_errors' => count($errors),
            ],
        ], 201);
    }
}
