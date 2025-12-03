<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EnrollmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Enrollment::with(['student:id,name,email', 'subject:id,name,code']);

        if ($request->has('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->get('per_page', 15);
        $enrollments = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $enrollments,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
            'status' => 'nullable|in:active,completed,withdrawn,failed',
            'enrolled_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $existingEnrollment = Enrollment::where('student_id', $request->student_id)
            ->where('subject_id', $request->subject_id)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Student already enrolled in this subject',
            ], 422);
        }

        $data = $validator->validated();
        $data['status'] = $data['status'] ?? 'active';
        $data['enrolled_at'] = $data['enrolled_at'] ?? now();

        $enrollment = Enrollment::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Enrollment created successfully',
            'data' => $enrollment->load(['student', 'subject']),
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $enrollment = Enrollment::with([
            'student:id,name,email,profile_photo',
            'subject:id,name,code,credits,teacher_id',
            'subject.teacher:id,name',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $enrollment,
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $enrollment = Enrollment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|in:active,completed,withdrawn,failed',
            'final_grade' => 'nullable|numeric|min:0|max:100',
            'grade_letter' => 'nullable|string|max:5',
            'completed_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $enrollment->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Enrollment updated successfully',
            'data' => $enrollment->load(['student', 'subject']),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $enrollment = Enrollment::findOrFail($id);
        $enrollment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Enrollment deleted successfully',
        ]);
    }
}
