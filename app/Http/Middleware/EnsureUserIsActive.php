<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Check if user is suspended
            if ($user->status === 'suspended') {
                Auth::logout();

                return redirect()->route('login')
                    ->with('error', 'تم إيقاف حسابك. يرجى التواصل مع الإدارة.');
            }

            // Check if user is inactive
            if ($user->status === 'inactive') {
                Auth::logout();

                return redirect()->route('login')
                    ->with('error', 'حسابك غير نشط. يرجى التواصل مع الإدارة.');
            }

            // Allow pending users to login but they'll see pending message in pages
        }

        return $next($request);
    }
}
