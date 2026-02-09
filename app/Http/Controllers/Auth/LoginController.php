<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Log login activity (NELC Compliance)
            $this->activityLogService->logAuth(ActivityLog::AUTH_LOGIN, $user);

            // Redirect based on role
            return match ($user->role) {
                'admin', 'super_admin' => redirect()->intended(route('admin.dashboard')),
                'teacher' => redirect()->intended(route('teacher.dashboard')),
                'student' => redirect()->intended(route('student.dashboard')),
                default => redirect()->intended('/dashboard'),
            };
        }

        throw ValidationException::withMessages([
            'email' => __('البريد الإلكتروني أو كلمة المرور غير صحيحة.'),
        ]);
    }
}
