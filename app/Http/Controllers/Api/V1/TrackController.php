<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\TrackService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TrackController extends Controller
{
    public function __construct(
        protected TrackService $trackService
    ) {
    }

    /**
     * عرض جميع المسارات
     * GET /api/v1/tracks
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'program_id' => $request->input('program_id'),
                'status' => $request->input('status'),
            ];

            $tracks = $this->trackService->getAllTracks(array_filter($filters));

            return response()->json([
                'success' => true,
                'data' => $tracks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * عرض مسار واحد
     * GET /api/v1/tracks/{id}
     */
    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $withTerms = $request->boolean('with_terms', false);
            $withStudents = $request->boolean('with_students', false);

            $track = $this->trackService->getTrack($id, $withTerms, $withStudents);

            return response()->json([
                'success' => true,
                'data' => $track,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * إنشاء مسار جديد
     * POST /api/v1/tracks
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'program_id' => 'required|exists:programs,id',
                'name' => 'required|string|max:255',
                'code' => 'required|string|unique:tracks,code|max:50',
                'description' => 'nullable|string',
                'total_terms' => 'integer|min:1|max:20',
                'duration_months' => 'nullable|integer|min:1',
                'status' => 'in:active,inactive,archived',
                'auto_create_terms' => 'boolean',
                'term_duration_months' => 'integer|min:1|max:12',
            ]);

            $track = $this->trackService->createTrack($validated);

            return response()->json([
                'success' => true,
                'message' => 'Track created successfully',
                'data' => $track,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * تحديث مسار
     * PUT /api/v1/tracks/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'program_id' => 'exists:programs,id',
                'name' => 'string|max:255',
                'code' => 'string|unique:tracks,code,' . $id . '|max:50',
                'description' => 'nullable|string',
                'total_terms' => 'integer|min:1|max:20',
                'duration_months' => 'nullable|integer|min:1',
                'status' => 'in:active,inactive,archived',
            ]);

            $this->trackService->updateTrack($id, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Track updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * حذف مسار
     * DELETE /api/v1/tracks/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->trackService->deleteTrack($id);

            return response()->json([
                'success' => true,
                'message' => 'Track deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * عرض الأرباع الخاصة بالمسار
     * GET /api/v1/tracks/{id}/terms
     */
    public function terms(int $id): JsonResponse
    {
        try {
            $track = $this->trackService->getTrackWithTerms($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'track' => $track,
                    'terms' => $track->terms,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * تعيين طالب إلى مسار
     * POST /api/v1/tracks/{id}/assign-student
     */
    public function assignStudent(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:users,id',
                'term_number' => 'integer|min:1|max:10',
            ]);

            $this->trackService->assignStudentToTrack(
                $validated['student_id'],
                $id,
                $validated['term_number'] ?? 1
            );

            return response()->json([
                'success' => true,
                'message' => 'Student assigned to track successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * ترقية طالب للربع التالي
     * POST /api/v1/tracks/promote-student
     */
    public function promoteStudent(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'student_id' => 'required|exists:users,id',
            ]);

            $this->trackService->promoteStudentToNextTerm($validated['student_id']);

            return response()->json([
                'success' => true,
                'message' => 'Student promoted to next term successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
