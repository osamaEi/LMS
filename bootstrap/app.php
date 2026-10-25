<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);

        // Add SetLocale middleware to web group
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\EnsureUserIsActive::class,
        ]);

        // Add SetLocale to API group so ?lang= and Accept-Language work for all API endpoints
        $middleware->api(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Exclude webhooks from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Sync Zoom recordings every hour
        $schedule->command('zoom:sync-recordings')->hourly();

        // Clean old activity logs monthly (NELC Compliance)
        $schedule->command('activity-logs:clean')->monthly();

        // Process xAPI statements every 5 minutes (NELC Compliance)
        $schedule->command('xapi:process')->everyFiveMinutes();

        // Retry failed xAPI statements hourly (NELC Compliance)
        $schedule->command('xapi:retry-failed')->hourly();

        // Send payment reminders daily at 9 AM for installments due in the next 3 days
        $schedule->command('payments:send-reminders --days=3')->dailyAt('09:00');

        // Deactivate subjects whose quarter ended and courses past their end date
        $schedule->command('academics:deactivate-ended')->dailyAt('00:05');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // 419 (expired session / stale CSRF token): instead of a dead "Page
        // Expired" screen, send the user back to the form with a fresh token.
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 419 || $request->expectsJson()) {
                return null;
            }

            $message = 'انتهت صلاحية الصفحة، يرجى المحاولة مرة أخرى.';

            // A stale login form: go back to a fresh login page.
            if ($request->routeIs('login', 'login.*') || !auth()->check()) {
                return redirect()->route('login')
                    ->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->with('error', $message);
            }

            return redirect()->back()
                ->withInput($request->except('password', 'password_confirmation', '_token'))
                ->with('error', $message);
        });
    })->create();
