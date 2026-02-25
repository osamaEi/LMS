<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as BaseController;
use App\Models\User;
use App\Notifications\CustomNotification;
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

    /**
     * Send a custom notification to students / teachers / all (admin only)
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'target'     => 'required|in:student,teacher,all',
            'title'      => 'required|string|max:255',
            'body'       => 'required|string|max:1000',
            'action_url' => 'nullable|url|max:500',
        ]);

        $query = User::query();

        if ($validated['target'] !== 'all') {
            $query->where('role', $validated['target']);
        }

        $users = $query->get();
        $notification = new CustomNotification(
            title:      $validated['title'],
            body:       $validated['body'],
            actionUrl:  $validated['action_url'] ?? '#',
            senderName: auth()->user()->name,
        );

        foreach ($users as $user) {
            $user->notify($notification);
        }

        $targetLabel = match($validated['target']) {
            'student' => 'الطلاب',
            'teacher' => 'المعلمين',
            'all'     => 'جميع المستخدمين',
        };

        return redirect()->route('notifications.page')
            ->with('success', "تم إرسال الإشعار بنجاح إلى {$targetLabel} ({$users->count()} مستخدم)");
    }
}
