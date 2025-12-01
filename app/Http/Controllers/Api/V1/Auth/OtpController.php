<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;

class OtpController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Send OTP to phone number
     */
    public function send(SendOtpRequest $request): JsonResponse
    {
        try {
            $phone = $request->phone;
            $type = $request->input('type', 'registration');

            // For registration type, verify phone belongs to registered user
            if ($type === 'registration') {
                $user = User::where('phone', $phone)->first();
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Phone number not found. Please register first.',
                    ], 404);
                }
            }

            // Send OTP
            $otp = $this->otpService->send($phone, $type);

            $responseData = [
                'phone' => $phone,
                'expires_at' => $otp->expires_at->toIso8601String(),
            ];

            // Include OTP in response for development/testing
            if (config('app.debug')) {
                $responseData['otp'] = $otp->otp;
            }

            return response()->json([
                'success' => true,
                'message' => 'OTP sent successfully to ' . $phone,
                'data' => $responseData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Verify OTP
     */
    public function verify(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $phone = $request->phone;
            $otpCode = $request->otp;
            $type = $request->input('type', 'registration');

            // Verify OTP
            $verified = $this->otpService->verify($phone, $otpCode, $type);

            if (!$verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP. Please try again.',
                ], 400);
            }

            // Update user phone_verified_at if registration type
            if ($type === 'registration') {
                $user = User::where('phone', $phone)->first();
                if ($user && !$user->phone_verified_at) {
                    $user->update(['phone_verified_at' => now()]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Phone number verified successfully.',
                'data' => [
                    'phone' => $phone,
                    'verified' => true,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OTP verification failed. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
