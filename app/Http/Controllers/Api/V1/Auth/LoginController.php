<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * POST /api/v1/auth/login
     * Login and get Sanctum token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['البريد الإلكتروني أو كلمة المرور غير صحيحة'],
            ]);
        }

        // Check if account is active
        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'حسابك غير نشط. يرجى التواصل مع الإدارة.',
            ], 403);
        }

        // Create token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'status' => $user->status,
                    'program_id' => $user->program_id,
                    'program_status' => $user->program_status,
                ],
                'token' => $token,
            ],
        ]);
    }

    /**
     * POST /api/v1/auth/logout
     * Revoke current token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تسجيل الخروج بنجاح',
        ]);
    }

    /**
     * GET /api/v1/profile
     * Get authenticated user profile
     */
    public function profile(Request $request)
    {
        $user = $request->user()->load(['program', 'track']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }
}
