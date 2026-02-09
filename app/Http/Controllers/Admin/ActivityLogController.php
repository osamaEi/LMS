<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ActivityLogController extends Controller
{
    public function __construct(
        private ActivityLogService $activityLogService
    ) {}

    /**
     * Display activity logs with filters
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'loggable']);

        // Apply filters
        if ($request->filled('user_id')) {
            $query->forUser($request->user_id);
        }

        if ($request->filled('action')) {
            $query->byAction($request->action);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $logs = $query->latest()->paginate(50);

        // Get available actions and categories for filters
        $actions = ActivityLog::select('action')->distinct()->pluck('action');
        $categories = ActivityLog::select('action_category')->distinct()->pluck('action_category');

        return view('admin.activity-logs.index', compact('logs', 'actions', 'categories'));
    }

    /**
     * Show detailed view of a single activity log
     */
    public function show(ActivityLog $log)
    {
        $log->load(['user', 'loggable']);
        return view('admin.activity-logs.show', compact('log'));
    }

    /**
     * Display statistics dashboard
     */
    public function stats(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        $stats = $this->activityLogService->getActivityStats($dateFrom, $dateTo);

        // Additional stats
        $totalLogs = ActivityLog::count();
        $xapiSyncedCount = ActivityLog::where('xapi_sent', true)->count();
        $xapiPendingCount = ActivityLog::where('xapi_sent', false)->count();

        $topUsers = ActivityLog::selectRaw('user_id, COUNT(*) as activity_count')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('activity_count')
            ->limit(10)
            ->with('user')
            ->get();

        return view('admin.activity-logs.stats', compact(
            'stats',
            'totalLogs',
            'xapiSyncedCount',
            'xapiPendingCount',
            'topUsers',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Export activity logs
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv'); // csv or json

        $filters = $request->only(['user_id', 'action', 'category', 'date_from', 'date_to']);
        $activities = $this->activityLogService->exportActivities($filters);

        if ($format === 'json') {
            return Response::json($activities);
        }

        // CSV export
        $filename = 'activity-logs-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($activities) {
            $file = fopen('php://output', 'w');

            // CSV header
            fputcsv($file, ['ID', 'User', 'Email', 'Action', 'Category', 'Description', 'Related Type', 'Related ID', 'IP Address', 'Created At']);

            // CSV rows
            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity['id'],
                    $activity['user'],
                    $activity['user_email'],
                    $activity['action'],
                    $activity['category'],
                    $activity['description'],
                    $activity['related_type'],
                    $activity['related_id'],
                    $activity['ip_address'],
                    $activity['created_at'],
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
