<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of users
     * GET /api/v1/admin/users
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by program
        if ($request->has('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        // Filter by track
        if ($request->has('track_id')) {
            $query->where('track_id', $request->track_id);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 15);
        $users = $query->with(['program:id,name', 'track:id,name'])
                      ->orderBy('created_at', 'desc')
                      ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Store a newly created user
     * POST /api/v1/admin/users
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:super_admin,admin,teacher,student',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'program_id' => 'nullable|exists:programs,id',
            'track_id' => 'nullable|exists:tracks,id',
            'current_term_number' => 'nullable|integer|min:1|max:10',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['password'] = Hash::make($data['password']);
        $data['status'] = $data['status'] ?? 'active';

        $user = User::create($data);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user->load(['program', 'track']),
        ], 201);
    }

    /**
     * Display the specified user
     * GET /api/v1/admin/users/{id}
     */
    public function show(int $id): JsonResponse
    {
        $user = User::with([
            'program:id,name,code',
            'track:id,name,code',
            'enrollments.subject:id,name,code',
            'documents',
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Update the specified user
     * PUT /api/v1/admin/users/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
            'role' => 'sometimes|in:super_admin,admin,teacher,student',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female',
            'program_id' => 'nullable|exists:programs,id',
            'track_id' => 'nullable|exists:tracks,id',
            'current_term_number' => 'nullable|integer|min:1|max:10',
            'specialization' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'status' => 'nullable|in:active,inactive,suspended',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user->load(['program', 'track']),
        ]);
    }

    /**
     * Remove the specified user
     * DELETE /api/v1/admin/users/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    /**
     * Get user statistics
     * GET /api/v1/admin/users/stats
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_users' => User::count(),
            'students' => User::where('role', 'student')->count(),
            'teachers' => User::where('role', 'teacher')->count(),
            'admins' => User::whereIn('role', ['admin', 'super_admin'])->count(),
            'active_users' => User::where('status', 'active')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'suspended_users' => User::where('status', 'suspended')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
