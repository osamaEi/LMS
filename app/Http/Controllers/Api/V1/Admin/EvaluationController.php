<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvaluationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Evaluation::with(['student:id,name,email', 'subject:id,name,code']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $evaluations = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $evaluations,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'student_id' => 'required|exists:users,id',
            'type' => 'required|in:assignment,quiz,midterm_exam,final_exam,project,participation',
            'title' => 'required|string|max:255',
            'total_score' => 'required|numeric|min:0',
            'earned_score' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'weight' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:pending,submitted,graded',
            'due_date' => 'nullable|date',
            'graded_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $evaluation = Evaluation::create($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Evaluation created successfully',
            'data' => $evaluation->load(['student', 'subject']),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $evaluation = Evaluation::with([
            'student:id,name,email,profile_photo',
            'subject:id,name,code,credits',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $evaluation,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $evaluation = Evaluation::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'total_score' => 'sometimes|numeric|min:0',
            'earned_score' => 'nullable|numeric|min:0',
            'percentage' => 'nullable|numeric|min:0|max:100',
            'weight' => 'sometimes|numeric|min:0|max:100',
            'status' => 'sometimes|in:pending,submitted,graded',
            'due_date' => 'nullable|date',
            'graded_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $evaluation->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Evaluation updated successfully',
            'data' => $evaluation->load(['student', 'subject']),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $evaluation = Evaluation::findOrFail($id);
        $evaluation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evaluation deleted successfully',
        ]);
    }
}
