<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Show notifications page (Blade view)
     */
    public function page(Request $request)
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->paginate(20);
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Get user's notifications (JSON API)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = $request->input('limit', 10);

        return response()->json([
            'notifications' => $this->notificationService->getRecentNotifications($user, $limit),
            'unread_count' => $this->notificationService->getUnreadCount($user),
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'count' => 0,
                    'error' => 'User not authenticated'
                ], 401);
            }

            return response()->json([
                'count' => $this->notificationService->getUnreadCount($user),
            ]);
        } catch (\Exception $e) {
            \Log::error('Notification unread count error: ' . $e->getMessage());
            return response()->json([
                'count' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $success = $this->notificationService->markAsRead($request->user(), $id);

        return response()->json([
            'success' => $success,
            'unread_count' => $this->notificationService->getUnreadCount($request->user()),
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $count = $this->notificationService->markAllAsRead($request->user());

        return response()->json([
            'success' => true,
            'marked_count' => $count,
            'unread_count' => 0,
        ]);
    }
}
