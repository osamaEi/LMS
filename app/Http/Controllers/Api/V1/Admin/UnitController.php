<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Unit::with(['subject:id,name,code']);

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $units = $query->withCount('sessions')->orderBy('order', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $units,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_number' => 'required|integer|min:1',
            'duration_hours' => 'nullable|integer|min:0',
            'learning_objectives' => 'nullable|string',
            'status' => 'nullable|in:draft,published,archived',
            'order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['status'] = $data['status'] ?? 'draft';
        $data['order'] = $data['order'] ?? $data['unit_number'];

        $unit = Unit::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Unit created successfully',
            'data' => $unit->load('subject'),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $unit = Unit::with([
            'subject:id,name,code,term_id',
            'subject.term:id,term_number,name',
            'sessions' => function($q) {
                $q->orderBy('session_number', 'asc')->with('files');
            }
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $unit,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $unit = Unit::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'unit_number' => 'sometimes|integer|min:1',
            'duration_hours' => 'nullable|integer|min:0',
            'learning_objectives' => 'nullable|string',
            'status' => 'sometimes|in:draft,published,archived',
            'order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $unit->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Unit updated successfully',
            'data' => $unit->load('subject'),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Unit deleted successfully',
        ]);
    }
}
