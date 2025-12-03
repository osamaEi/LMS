<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProgramController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Program::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $programs = $query->withCount('tracks')->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $programs,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:programs,code',
            'description' => 'nullable|string',
            'duration_months' => 'required|integer|min:1',
            'type' => 'required|in:diploma,training',
            'status' => 'nullable|in:active,inactive',
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

        $program = Program::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Program created successfully',
            'data' => $program,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $program = Program::withCount('tracks')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $program,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $program = Program::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'code' => 'sometimes|string|unique:programs,code,' . $id,
            'description' => 'nullable|string',
            'duration_months' => 'sometimes|integer|min:1',
            'type' => 'sometimes|in:diploma,training',
            'status' => 'sometimes|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $program->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Program updated successfully',
            'data' => $program,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $program = Program::findOrFail($id);

        if ($program->tracks()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete program with existing tracks',
            ], 422);
        }

        $program->delete();

        return response()->json([
            'success' => true,
            'message' => 'Program deleted successfully',
        ]);
    }
}
