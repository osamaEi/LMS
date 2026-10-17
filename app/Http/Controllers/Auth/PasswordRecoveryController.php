<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\OtpService;
use App\Services\PasswordRecoveryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class PasswordRecoveryController extends Controller
{
    public function show(Request $request)
    {
        return view('auth.forgot-password', ['sent' => $request->session()->has('password_recovery')]);
    }

    public function send(Request $request, PasswordRecoveryService $recovery, OtpService $otp)
    {
        $data = $request->validate(['identifier' => 'required|string|max:255']);
        $request->session()->forget('password_recovery');
        $user = $recovery->findAccount(trim($data['identifier']));
        if ($user?->phone) {
            try {
                $otp->send($user->phone, 'password_reset');
            } catch (\Throwable $exception) {
                report($exception);
                return back()->withInput()->withErrors(['identifier' => __('تعذر إرسال الرمز الآن. يرجى المحاولة لاحقاً.')]);
            }
        }
        $request->session()->put('password_recovery', ['user_id' => $user?->id, 'identifier' => $data['identifier']]);

        return redirect()->route('password.request')->with('status', __('إذا كان الحساب مسجلاً، تم إرسال رمز إلى رقم الجوال المرتبط به.'));
    }

    public function reset(Request $request, PasswordRecoveryService $recovery)
    {
        $data = $request->validate([
            'otp' => 'required|digits:6',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $user = User::find($request->session()->get('password_recovery.user_id'));
        if (! $user || ! $recovery->reset($user, $data['otp'], $data['password'])) {
            return back()->withErrors(['otp' => __('الرمز غير صحيح أو انتهت صلاحيته. اطلب رمزاً جديداً عند الحاجة.')]);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', __('تم تغيير كلمة المرور بنجاح. يمكنك تسجيل الدخول الآن.'));
    }
}
