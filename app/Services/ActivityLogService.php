<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;

class ActivityLogService
{
    /**
     * Log an activity
     */
    public function log(
        string $action,
        string $category,
        ?Model $loggable = null,
        array $properties = [],
        ?User $user = null
    ): ActivityLog {
        $user = $user ?? auth()->user();

        return ActivityLog::create([
            'user_id' => $user?->id,
            'action' => $action,
            'action_category' => $category,
            'loggable_type' => $loggable ? get_class($loggable) : null,
            'loggable_id' => $loggable?->id,
            'properties' => $properties,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'session_id' => session()->getId(),
        ]);
    }

    /**
     * Log authentication activity
     */
    public function logAuth(string $action, ?User $user = null): ActivityLog
    {
        return $this->log($action, ActivityLog::CATEGORY_AUTH, null, [], $user);
    }

    /**
     * Log content activity
     */
    public function logContent(string $action, Model $content, array $props = []): ActivityLog
    {
        return $this->log($action, ActivityLog::CATEGORY_CONTENT, $content, $props);
    }

    /**
     * Log assessment activity
     */
    public function logAssessment(string $action, Model $assessment, array $props = []): ActivityLog
    {
        return $this->log($action, ActivityLog::CATEGORY_ASSESSMENT, $assessment, $props);
    }

    /**
     * Log attendance activity
     */
    public function logAttendance(string $action, Model $attendance, array $props = []): ActivityLog
    {
        return $this->log($action, ActivityLog::CATEGORY_ATTENDANCE, $attendance, $props);
    }

    /**
     * Log enrollment activity
     */
    public function logEnrollment(string $action, Model $enrollment, array $props = []): ActivityLog
    {
        return $this->log($action, ActivityLog::CATEGORY_ENROLLMENT, $enrollment, $props);
    }

    /**
     * Log communication activity
     */
    public function logCommunication(string $action, ?Model $related = null, array $props = []): ActivityLog
    {
        return $this->log($action, ActivityLog::CATEGORY_COMMUNICATION, $related, $props);
    }

    /**
     * Log admin activity
     */
    public function logAdmin(string $action, ?Model $related = null, array $props = []): ActivityLog
    {
        return $this->log($action, ActivityLog::CATEGORY_ADMIN, $related, $props);
    }

    /**
     * Log navigation activity
     */
    public function logNavigation(string $action, array $props = []): ActivityLog
    {
        return $this->log($action, ActivityLog::CATEGORY_NAVIGATION, null, $props);
    }

    /**
     * Get user activity timeline
     */
    public function getUserActivityTimeline(int $userId, int $limit = 50): Collection
    {
        return ActivityLog::forUser($userId)
            ->with('loggable')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activity statistics for a date range
     */
    public function getActivityStats(string $dateFrom, string $dateTo): array
    {
        $logs = ActivityLog::dateRange($dateFrom, $dateTo)->get();

        return [
            'total_activities' => $logs->count(),
            'unique_users' => $logs->pluck('user_id')->unique()->count(),
            'by_category' => $logs->groupBy('action_category')->map->count(),
            'by_action' => $logs->groupBy('action')->map->count(),
            'daily_breakdown' => $logs->groupBy(function ($log) {
                return $log->created_at->format('Y-m-d');
            })->map->count(),
        ];
    }

    /**
     * Get recent activities (for dashboard)
     */
    public function getRecentActivities(int $limit = 20): Collection
    {
        return ActivityLog::with(['user', 'loggable'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities by category
     */
    public function getActivitiesByCategory(string $category, int $limit = 100): Collection
    {
        return ActivityLog::byCategory($category)
            ->with(['user', 'loggable'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get activities pending xAPI sync
     */
    public function getPendingXapiActivities(int $limit = 100): Collection
    {
        return ActivityLog::notSentToXapi()
            ->oldest()
            ->limit($limit)
            ->get();
    }

    /**
     * Clean old activity logs (for scheduled command)
     */
    public function cleanOldLogs(int $retentionMonths = 12): int
    {
        $cutoffDate = now()->subMonths($retentionMonths);

        return ActivityLog::where('created_at', '<', $cutoffDate)
            ->where('xapi_sent', true) // Only delete logs that have been sent to xAPI
            ->delete();
    }

    /**
     * Export activities as array (for CSV/JSON export)
     */
    public function exportActivities(array $filters = []): Collection
    {
        $query = ActivityLog::with(['user', 'loggable']);

        if (isset($filters['user_id'])) {
            $query->forUser($filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->byAction($filters['action']);
        }

        if (isset($filters['category'])) {
            $query->byCategory($filters['category']);
        }

        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->dateRange($filters['date_from'], $filters['date_to']);
        }

        return $query->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'user' => $log->user?->name ?? 'N/A',
                'user_email' => $log->user?->email ?? 'N/A',
                'action' => $log->action,
                'category' => $log->action_category,
                'description' => $log->getDescription(),
                'related_type' => $log->loggable_type ? class_basename($log->loggable_type) : 'N/A',
                'related_id' => $log->loggable_id ?? 'N/A',
                'ip_address' => $log->ip_address,
                'created_at' => $log->created_at->toDateTimeString(),
            ];
        });
    }
}
