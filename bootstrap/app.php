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
        ]);

        // Add SetLocale middleware to web group
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\LogActivity::class,
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
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
