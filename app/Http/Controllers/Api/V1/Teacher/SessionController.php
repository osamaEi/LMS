<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\SessionFile;
use App\Models\Subject;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    /**
     * Get all sessions for teacher's subjects
     */
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $query = Session::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->with(['subject', 'unit']);

        // Filter by subject
        if ($request->has('subject_id')) {
            $query->whereHas('subject', function ($q) use ($request) {
                $q->where('id', $request->subject_id);
            });
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('scheduled_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('scheduled_at', '<=', $request->to_date);
        }

        $sessions = $query->orderBy('scheduled_at', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $sessions,
        ]);
    }

    /**
     * Get specific session details
     */
    public function show(Request $request, $id): JsonResponse
    {
        $teacher = $request->user();

        $session = Session::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
            ->with(['subject', 'unit', 'files'])
            ->find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or you do not have access to it',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $session,
        ]);
    }

    /**
     * Create a new session
     */
    public function store(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $validator = Validator::make($request->all(), [
            'unit_id' => 'required|exists:units,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session_number' => 'required|integer|min:1',
            'type' => 'required|in:video,zoom,mixed',
            'duration' => 'required|integer|min:1',
            'scheduled_at' => 'nullable|date',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if unit belongs to teacher's subject
        $unit = Unit::with('subject')->find($request->unit_id);
        if ($unit->subject->teacher_id !== $teacher->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create sessions for this unit',
            ], 403);
        }

        $session = Session::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Session created successfully',
            'data' => $session->load(['subject', 'unit']),
        ], 201);
    }

    /**
     * Update session details
     */
    public function update(Request $request, $id): JsonResponse
    {
        $teacher = $request->user();

        $session = Session::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or you do not have access to it',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'session_number' => 'sometimes|required|integer|min:1',
            'type' => 'sometimes|required|in:video,zoom,mixed',
            'duration' => 'sometimes|required|integer|min:1',
            'scheduled_at' => 'nullable|date',
            'status' => 'sometimes|required|in:scheduled,in_progress,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $session->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Session updated successfully',
            'data' => $session->load(['subject', 'unit']),
        ]);
    }

    /**
     * Delete a session
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $teacher = $request->user();

        $session = Session::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or you do not have access to it',
            ], 404);
        }

        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Session deleted successfully',
        ]);
    }

    /**
     * Add a file to a session
     */
    public function addFile(Request $request, $id): JsonResponse
    {
        $teacher = $request->user();

        $session = Session::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($id);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or you do not have access to it',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:video,pdf,zoom',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'is_mandatory' => 'boolean',

            // Video fields
            'video_path' => 'required_if:type,video|nullable|string',
            'video_url' => 'nullable|url',
            'video_platform' => 'required_if:type,video|nullable|in:local,youtube,vimeo',
            'video_duration' => 'nullable|integer|min:1',
            'video_size' => 'nullable|integer|min:0',

            // PDF fields
            'file_path' => 'required_if:type,pdf|nullable|string',
            'file_url' => 'nullable|url',
            'file_size' => 'nullable|integer|min:0',

            // Zoom fields
            'zoom_meeting_id' => 'required_if:type,zoom|nullable|string',
            'zoom_start_url' => 'nullable|url',
            'zoom_join_url' => 'nullable|url',
            'zoom_password' => 'nullable|string',
            'zoom_scheduled_at' => 'nullable|date',
            'zoom_duration' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $fileData = $request->all();
        $fileData['session_id'] = $session->id;

        $file = SessionFile::create($fileData);

        return response()->json([
            'success' => true,
            'message' => 'File added to session successfully',
            'data' => $file,
        ], 201);
    }

    /**
     * Update a session file
     */
    public function updateFile(Request $request, $sessionId, $fileId): JsonResponse
    {
        $teacher = $request->user();

        $session = Session::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or you do not have access to it',
            ], 404);
        }

        $file = SessionFile::where('session_id', $sessionId)->find($fileId);

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'sometimes|required|integer|min:0',
            'is_mandatory' => 'boolean',

            // Video fields
            'video_path' => 'nullable|string',
            'video_url' => 'nullable|url',
            'video_platform' => 'nullable|in:local,youtube,vimeo',
            'video_duration' => 'nullable|integer|min:1',
            'video_size' => 'nullable|integer|min:0',

            // PDF fields
            'file_path' => 'nullable|string',
            'file_url' => 'nullable|url',
            'file_size' => 'nullable|integer|min:0',

            // Zoom fields
            'zoom_meeting_id' => 'nullable|string',
            'zoom_start_url' => 'nullable|url',
            'zoom_join_url' => 'nullable|url',
            'zoom_password' => 'nullable|string',
            'zoom_scheduled_at' => 'nullable|date',
            'zoom_duration' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $file->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'File updated successfully',
            'data' => $file,
        ]);
    }

    /**
     * Delete a session file
     */
    public function deleteFile(Request $request, $sessionId, $fileId): JsonResponse
    {
        $teacher = $request->user();

        $session = Session::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($sessionId);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Session not found or you do not have access to it',
            ], 404);
        }

        $file = SessionFile::where('session_id', $sessionId)->find($fileId);

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        $file->delete();

        return response()->json([
            'success' => true,
            'message' => 'File deleted successfully',
        ]);
    }
}
