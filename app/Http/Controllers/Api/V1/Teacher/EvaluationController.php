<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\Subject;
use App\Models\Enrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvaluationController extends Controller
{
    /**
     * Get all evaluations for teacher's subjects
     */
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $query = Evaluation::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->with(['subject']);

        // Filter by subject
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $evaluations = $query->orderBy('due_date', 'desc')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $evaluations,
        ]);
    }

    /**
     * Get specific evaluation details with submissions
     */
    public function show(Request $request, $id): JsonResponse
    {
        $teacher = $request->user();

        $evaluation = Evaluation::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })
            ->with(['subject', 'submissions.student'])
            ->find($id);

        if (!$evaluation) {
            return response()->json([
                'success' => false,
                'message' => 'Evaluation not found or you do not have access to it',
            ], 404);
        }

        // Get enrolled students count
        $enrolledStudentsCount = Enrollment::where('subject_id', $evaluation->subject_id)->count();

        // Calculate statistics
        $submissions = $evaluation->submissions;
        $submittedCount = $submissions->count();
        $gradedCount = $submissions->whereNotNull('grade')->count();
        $avgGrade = $submissions->whereNotNull('grade')->avg('grade') ?? 0;

        $stats = [
            'total_students' => $enrolledStudentsCount,
            'submitted' => $submittedCount,
            'pending_submission' => $enrolledStudentsCount - $submittedCount,
            'graded' => $gradedCount,
            'pending_grading' => $submittedCount - $gradedCount,
            'average_grade' => round($avgGrade, 2),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'evaluation' => $evaluation,
                'statistics' => $stats,
            ],
        ]);
    }

    /**
     * Create a new evaluation
     */
    public function store(Request $request): JsonResponse
    {
        $teacher = $request->user();

        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'type' => 'required|in:assignment,quiz,exam,project',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'required|numeric|min:0',
            'passing_marks' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:draft,published,closed,graded',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Verify subject belongs to teacher
        $subject = Subject::where('id', $request->subject_id)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$subject) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to create evaluations for this subject',
            ], 403);
        }

        $evaluation = Evaluation::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Evaluation created successfully',
            'data' => $evaluation->load(['subject']),
        ], 201);
    }

    /**
     * Update evaluation details
     */
    public function update(Request $request, $id): JsonResponse
    {
        $teacher = $request->user();

        $evaluation = Evaluation::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($id);

        if (!$evaluation) {
            return response()->json([
                'success' => false,
                'message' => 'Evaluation not found or you do not have access to it',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'sometimes|required|in:assignment,quiz,exam,project',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'sometimes|required|numeric|min:0',
            'passing_marks' => 'sometimes|required|numeric|min:0',
            'due_date' => 'sometimes|required|date',
            'status' => 'sometimes|required|in:draft,published,closed,graded',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $evaluation->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Evaluation updated successfully',
            'data' => $evaluation->load(['subject']),
        ]);
    }

    /**
     * Delete an evaluation
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        $teacher = $request->user();

        $evaluation = Evaluation::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($id);

        if (!$evaluation) {
            return response()->json([
                'success' => false,
                'message' => 'Evaluation not found or you do not have access to it',
            ], 404);
        }

        $evaluation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Evaluation deleted successfully',
        ]);
    }

    /**
     * Get submissions for an evaluation
     */
    public function submissions(Request $request, $id): JsonResponse
    {
        $teacher = $request->user();

        $evaluation = Evaluation::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($id);

        if (!$evaluation) {
            return response()->json([
                'success' => false,
                'message' => 'Evaluation not found or you do not have access to it',
            ], 404);
        }

        $query = $evaluation->submissions()->with(['student', 'enrollment']);

        // Filter by grading status
        if ($request->has('graded')) {
            if ($request->graded === 'true' || $request->graded === '1') {
                $query->whereNotNull('grade');
            } else {
                $query->whereNull('grade');
            }
        }

        $submissions = $query->orderBy('submitted_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'evaluation' => [
                    'id' => $evaluation->id,
                    'title' => $evaluation->title,
                    'type' => $evaluation->type,
                    'total_marks' => $evaluation->total_marks,
                ],
                'submissions' => $submissions,
            ],
        ]);
    }

    /**
     * Grade a submission
     */
    public function gradeSubmission(Request $request, $evaluationId, $submissionId): JsonResponse
    {
        $teacher = $request->user();

        $evaluation = Evaluation::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($evaluationId);

        if (!$evaluation) {
            return response()->json([
                'success' => false,
                'message' => 'Evaluation not found or you do not have access to it',
            ], 404);
        }

        $submission = $evaluation->submissions()->find($submissionId);

        if (!$submission) {
            return response()->json([
                'success' => false,
                'message' => 'Submission not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'grade' => 'required|numeric|min:0|max:' . $evaluation->total_marks,
            'feedback' => 'nullable|string',
            'graded_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback,
            'graded_at' => $request->graded_at ?? now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Submission graded successfully',
            'data' => $submission->load(['student', 'evaluation']),
        ]);
    }

    /**
     * Bulk grade multiple submissions
     */
    public function bulkGrade(Request $request, $evaluationId): JsonResponse
    {
        $teacher = $request->user();

        $evaluation = Evaluation::whereHas('subject', function ($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->find($evaluationId);

        if (!$evaluation) {
            return response()->json([
                'success' => false,
                'message' => 'Evaluation not found or you do not have access to it',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'grades' => 'required|array|min:1',
            'grades.*.submission_id' => 'required|exists:evaluation_submissions,id',
            'grades.*.grade' => 'required|numeric|min:0|max:' . $evaluation->total_marks,
            'grades.*.feedback' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updated = [];
        $errors = [];

        foreach ($request->grades as $gradeData) {
            $submission = $evaluation->submissions()->find($gradeData['submission_id']);

            if (!$submission) {
                $errors[] = [
                    'submission_id' => $gradeData['submission_id'],
                    'message' => 'Submission not found for this evaluation',
                ];
                continue;
            }

            $submission->update([
                'grade' => $gradeData['grade'],
                'feedback' => $gradeData['feedback'] ?? null,
                'graded_at' => now(),
            ]);

            $updated[] = $submission->load(['student']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk grading completed',
            'data' => [
                'updated' => $updated,
                'errors' => $errors,
                'total_processed' => count($request->grades),
                'total_updated' => count($updated),
                'total_errors' => count($errors),
            ],
        ]);
    }
}
