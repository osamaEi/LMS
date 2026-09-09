<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $phone = str_starts_with($phone, '0') ? $phone : '0' . $phone;
        $user = User::where('phone', $phone)->first();

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

            // A testing account must stay usable even if its stored phone is
            // not routable — the fixed code is accepted regardless.
            if ($this->otpService->isTestNationalId($user->national_id)) {
                return response()->json([
                    'success' => true,
                    'message' => 'إذا كان الحساب مسجلاً لدينا فسيتم إرسال رمز التحقق إلى رقم الجوال المرتبط به.',
                    'data'    => [
                        'phone'      => $this->maskPhone($user->phone),
                        'expires_at' => now()->addMinutes(5)->toIso8601String(),
                    ],
                ]);
            }

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
     * Set a new password using the OTP sent to the phone.
     *
     * Fields: national_id, otp, password
     */
    public function reset(Request $request)
    {
        $data = $request->validate([
            'national_id' => ['required', 'digits:10'],
            'otp'      => 'required|digits:6',
            'password' => ['required', Password::min(8)],
        ], [
            'otp.required'       => 'رمز التحقق مطلوب',
            'otp.digits'         => 'رمز التحقق يجب أن يكون 6 أرقام',
            'password.required'  => 'كلمة المرور مطلوبة',
        ]);

        $user = User::where('national_id', $data['national_id'])->first();

        if (!$user || !$user->phone) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد حساب مرتبط بهذا الرقم',
            ], 404);
        }

        if (!$this->otpService->verify($user->phone, $data['otp'], 'password_reset')) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق غير صحيح أو منتهي الصلاحية',
            ], 422);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        // Any session opened with the old password must not survive the reset.
        $user->tokens()->delete();

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
