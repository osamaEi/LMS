<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use App\Services\PasswordRecoveryService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    public function __construct(protected OtpService $otpService)
    {
    }

    /**
     * POST /api/v1/auth/forgot-password
     * Send a password-reset OTP to the account's registered phone.
     *
     * Fields: phone
     */
    public function forgot(ForgotPasswordRequest $request)
    {
        $phone = $request->validated('phone');
        $user = app(PasswordRecoveryService::class)->findAccount($phone);

        // Do not leak which accounts exist — always answer the same way.
        if (!$user || !$user->phone) {
            return response()->json([
                'success' => true,
                'message' => 'إذا كان الحساب مسجلاً لدينا فسيتم إرسال رمز التحقق إلى رقم الجوال المرتبط به.',
            ]);
        }

        // An SMS failure must not turn into a stack trace: that would answer
        // differently for real and unknown accounts and leak which exist.
        try {
            $otp = $this->otpService->send($user->phone, 'password_reset');
        } catch (\Throwable $e) {
            Log::error('Password reset OTP failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'تعذر إرسال رمز التحقق حالياً. يرجى المحاولة لاحقاً.',
            ], 503);
        }

        return response()->json([
            'success' => true,
            'message' => 'إذا كان الحساب مسجلاً لدينا فسيتم إرسال رمز التحقق إلى رقم الجوال المرتبط به.',
            'data'    => [
                'phone'      => $this->maskPhone($user->phone),
                'expires_at' => $otp->expires_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * POST /api/v1/auth/reset-password
     * Set a new password after validating a single-use SMS code.
     *
     * Fields: national_id, otp, password
     */
    public function reset(Request $request)
    {
        $data = $request->validate([
            'national_id' => ['required', 'digits:10'],
            'otp' => ['required', 'digits:6'],
            'password' => ['required', Password::min(8)],
        ], [
            'password.required'  => 'كلمة المرور مطلوبة',
        ]);

        $user = User::where('national_id', $data['national_id'])->first();

        if (!$user || !app(PasswordRecoveryService::class)->reset($user, $data['otp'], $data['password'])) {
            return response()->json([
                'success' => false,
                'message' => 'الرمز غير صحيح أو انتهت صلاحيته.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح. يرجى تسجيل الدخول من جديد.',
        ]);
    }

    protected function maskPhone(string $phone): string
    {
        return substr($phone, 0, 3) . str_repeat('*', max(0, strlen($phone) - 5)) . substr($phone, -2);
    }
}
