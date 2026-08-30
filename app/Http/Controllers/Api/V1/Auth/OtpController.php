<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function __construct(protected OtpService $otpService)
    {
    }

    /**
     * POST /api/v1/auth/send-otp
     * Send a 6-digit OTP by SMS.
     *
     * Fields: phone, type (registration|login|password_reset)
     */
    public function send(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'regex:/^(05|5)\d{8}$/', 'max:12'],
            'type'  => 'nullable|in:registration,login,password_reset',
        ], [
            'phone.required' => 'رقم الجوال مطلوب',
            'phone.regex'    => 'رقم الجوال غير صالح (مثال: 0512345678)',
            'type.in'        => 'نوع رمز التحقق غير صالح',
        ]);

        $phone = $this->normalisePhone($data['phone']);
        $type  = $data['type'] ?? 'registration';

        $otp = $this->otpService->send($phone, $type);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رمز التحقق إلى رقم جوالك',
            'data'    => [
                'phone'      => $phone,
                'type'       => $type,
                'expires_at' => $otp->expires_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * POST /api/v1/auth/verify-otp
     * Verify a previously sent OTP.
     *
     * Fields: phone, otp, type
     */
    public function verify(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'regex:/^(05|5)\d{8}$/', 'max:12'],
            'otp'   => 'required|digits:6',
            'type'  => 'nullable|in:registration,login,password_reset',
        ], [
            'phone.required' => 'رقم الجوال مطلوب',
            'phone.regex'    => 'رقم الجوال غير صالح (مثال: 0512345678)',
            'otp.required'   => 'رمز التحقق مطلوب',
            'otp.digits'     => 'رمز التحقق يجب أن يكون 6 أرقام',
        ]);

        $phone = $this->normalisePhone($data['phone']);
        $type  = $data['type'] ?? 'registration';

        if (!$this->otpService->verify($phone, $data['otp'], $type)) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق غير صحيح أو منتهي الصلاحية',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم التحقق من رقم الجوال بنجاح',
            'data'    => [
                'phone'    => $phone,
                'type'     => $type,
                'verified' => true,
            ],
        ]);
    }

    /**
     * Ensure the Saudi mobile number carries its leading zero.
     */
    protected function normalisePhone(string $phone): string
    {
        return str_starts_with($phone, '0') ? $phone : '0' . $phone;
    }
}
