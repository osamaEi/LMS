<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Register a new student
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        try {
            // Create user with pending status
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'national_id' => $request->national_id,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Registration successful. Please verify your phone number.',
                'data' => [
                    'user' => new UserResource($user),
                ],
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
