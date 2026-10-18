<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!in_array($request->user()->role, $roles)) {
            if ($request->isMethod('GET') && $request->routeIs('admin.*')
                && $request->user()->role === 'student'
                && $request->session()->has('student_dashboard_admin_id')) {
                app(\App\Services\StudentDashboardAccessService::class)->restoreAdmin($request);

                return redirect()->to($request->getRequestUri());
            }

            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
