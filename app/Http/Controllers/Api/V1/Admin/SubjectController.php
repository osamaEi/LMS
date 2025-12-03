<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubjectController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Subject::with(['term:id,term_number,name', 'teacher:id,name']);

        if ($request->has('term_id')) {
            $query->where('term_id', $request->term_id);
        }

        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $subjects = $query->withCount('units')->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $subjects,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'term_id' => 'required|exists:terms,id',
            'teacher_id' => 'nullable|exists:users,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'description' => 'nullable|string',
            'banner_photo' => 'nullable|string',
            'credits' => 'required|integer|min:1',
            'total_hours' => 'nullable|integer|min:0',
            'max_students' => 'nullable|integer|min:1',
            'status' => 'nullable|in:active,inactive,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['status'] = $data['status'] ?? 'active';

        $subject = Subject::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Subject created successfully',
            'data' => $subject->load(['term', 'teacher']),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $subject = Subject::with([
            'term:id,term_number,name,track_id',
            'term.track:id,name',
            'teacher:id,name,email,specialization',
            'units' => function($q) {
                $q->withCount('sessions');
            }
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $subject,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'teacher_id' => 'nullable|exists:users,id',
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|max:50',
            'description' => 'nullable|string',
            'banner_photo' => 'nullable|string',
            'credits' => 'sometimes|integer|min:1',
            'total_hours' => 'nullable|integer|min:0',
            'max_students' => 'nullable|integer|min:1',
            'status' => 'sometimes|in:active,inactive,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $subject->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Subject updated successfully',
            'data' => $subject->load(['term', 'teacher']),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subject deleted successfully',
        ]);
    }
}
