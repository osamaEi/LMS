<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SessionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Session::with(['subject:id,name,code', 'unit:id,title']);

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('unit_id')) {
            $query->where('unit_id', $request->unit_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $sessions = $query->withCount('files')->orderBy('scheduled_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $sessions,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'unit_id' => 'nullable|exists:units,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'session_number' => 'required|integer|min:1',
            'type' => 'required|in:recorded_video,live_zoom,mixed',
            'scheduled_at' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:1',
            'status' => 'nullable|in:scheduled,live,completed,cancelled',
            'is_mandatory' => 'nullable|boolean',
            'video_platform' => 'nullable|in:local,youtube,vimeo',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['status'] = $data['status'] ?? 'scheduled';
        $data['is_mandatory'] = $data['is_mandatory'] ?? true;
        $data['video_platform'] = $data['video_platform'] ?? 'local';

        $session = Session::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Session created successfully',
            'data' => $session->load(['subject', 'unit']),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $session = Session::with([
            'subject:id,name,code',
            'unit:id,title,unit_number',
            'files' => function($q) {
                $q->orderBy('order', 'asc');
            },
            'attendances' => function($q) {
                $q->with('student:id,name,email');
            }
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $session,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $session = Session::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'session_number' => 'sometimes|integer|min:1',
            'type' => 'sometimes|in:recorded_video,live_zoom,mixed',
            'scheduled_at' => 'nullable|date',
            'duration_minutes' => 'nullable|integer|min:1',
            'status' => 'sometimes|in:scheduled,live,completed,cancelled',
            'is_mandatory' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $session->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Session updated successfully',
            'data' => $session->load(['subject', 'unit', 'files']),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $session = Session::findOrFail($id);
        $session->delete();

        return response()->json([
            'success' => true,
            'message' => 'Session deleted successfully',
        ]);
    }
}
