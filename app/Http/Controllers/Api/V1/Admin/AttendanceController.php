<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of attendance records
     * GET /api/v1/admin/attendances
     */
    public function index(Request $request): JsonResponse
    {
        $query = Attendance::with(['student:id,name,email', 'session:id,title,type,scheduled_at']);

        // Filter by student
        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by session
        if ($request->has('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        // Filter by attended status
        if ($request->has('attended')) {
            $query->where('attended', $request->attended);
        }

        // Filter by video completion
        if ($request->has('video_completed')) {
            $query->where('video_completed', $request->video_completed);
        }

        $perPage = $request->get('per_page', 15);
        $attendances = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $attendances,
        ]);
    }

    /**
     * Store a newly created attendance record
     * POST /api/v1/admin/attendances
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:users,id',
            'session_id' => 'required|exists:class_sessions,id',
            'attended' => 'required|boolean',
            'watch_percentage' => 'nullable|numeric|min:0|max:100',
            'video_completed' => 'nullable|boolean',
            'joined_at' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if attendance already exists
        $existingAttendance = Attendance::where('student_id', $request->student_id)
            ->where('session_id', $request->session_id)
            ->first();

        if ($existingAttendance) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance record already exists for this student and session',
            ], 422);
        }

        $attendance = Attendance::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Attendance created successfully',
            'data' => $attendance->load(['student', 'session']),
        ], 201);
    }

    /**
     * Display the specified attendance record
     * GET /api/v1/admin/attendances/{id}
     */
    public function show(int $id): JsonResponse
    {
        $attendance = Attendance::with([
            'student:id,name,email,profile_photo',
            'session:id,title,type,scheduled_at,duration_minutes',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $attendance,
        ]);
    }

    /**
     * Update the specified attendance record
     * PUT /api/v1/admin/attendances/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $attendance = Attendance::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'attended' => 'sometimes|boolean',
            'watch_percentage' => 'nullable|numeric|min:0|max:100',
            'video_completed' => 'nullable|boolean',
            'joined_at' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $attendance->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Attendance updated successfully',
            'data' => $attendance->load(['student', 'session']),
        ]);
    }

    /**
     * Remove the specified attendance record
     * DELETE /api/v1/admin/attendances/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return response()->json([
            'success' => true,
            'message' => 'Attendance deleted successfully',
        ]);
    }
}
