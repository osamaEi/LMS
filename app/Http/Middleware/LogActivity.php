<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Log after response, only for authenticated users and successful responses
        if ($request->user() && $response->isSuccessful() && $request->route()) {
            // Only log GET requests (page views) to avoid duplicates
            if ($request->isMethod('GET')) {
                $this->activityLogService->logNavigation(
                    ActivityLog::NAV_PAGE_VIEW,
                    [
                        'route' => $request->route()->getName(),
                        'url' => $request->path(),
                        'method' => $request->method(),
                    ]
                );
            }
        }

        return $response;
    }
}
