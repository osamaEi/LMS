<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Initiate password reset by sending OTP to user's phone
     */
    public function forgot(ForgotPasswordRequest $request): JsonResponse
    {
        try {
            // Find user by email
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found with this email address.',
                ], 404);
            }

            // Check if user has a phone number
            if (!$user->phone) {
                return response()->json([
                    'success' => false,
                    'message' => 'No phone number associated with this account. Please contact support.',
                ], 400);
            }

            // Send OTP to user's phone
            $otp = $this->otpService->send($user->phone, 'password_reset');

            $responseData = [
                'phone' => $this->maskPhoneNumber($user->phone),
                'expires_at' => $otp->expires_at->toIso8601String(),
            ];

            // Include OTP in response for development/testing
            if (config('app.debug')) {
                $responseData['otp'] = $otp->otp;
            }

            return response()->json([
                'success' => true,
                'message' => 'Password reset OTP sent to your registered phone number.',
                'data' => $responseData,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send password reset OTP. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Reset password using OTP verification
     */
    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $phone = $request->phone;
            $otpCode = $request->otp;
            $newPassword = $request->password;

            // Verify OTP
            $verified = $this->otpService->verify($phone, $otpCode, 'password_reset');

            if (!$verified) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP. Please request a new one.',
                ], 400);
            }

            // Find user by phone
            $user = User::where('phone', $phone)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found with this phone number.',
                ], 404);
            }

            // Update password
            $user->update([
                'password' => Hash::make($newPassword),
            ]);

            // Revoke all existing tokens for security
            $user->tokens()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully. Please login with your new password.',
                'data' => [
                    'email' => $user->email,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Mask phone number for privacy (show last 4 digits only)
     */
    protected function maskPhoneNumber(string $phone): string
    {
        $length = strlen($phone);
        if ($length <= 4) {
            return $phone;
        }

        $lastFour = substr($phone, -4);
        $masked = str_repeat('*', $length - 4) . $lastFour;

        return $masked;
    }
}
