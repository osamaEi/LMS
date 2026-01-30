<?php

namespace App\Services;

use App\Models\Session;
use App\Models\User;
use App\Notifications\SessionCreatedNotification;
use App\Notifications\SessionUpdatedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * Notify about a single created session
     */
    public function notifySessionCreated(Session $session): void
    {
        try {
            $subject = $session->subject()->with(['teacher', 'enrollments.student'])->first();

            if (!$subject) {
                Log::warning("Subject not found for session {$session->id}");
                return;
            }

            $recipients = $this->resolveRecipients($subject);

            // Send queued notifications
            foreach ($recipients as $recipient) {
                try {
                    $recipient['user']->notify(
                        new SessionCreatedNotification($session, $recipient['role'])
                    );
                } catch (\Exception $e) {
                    Log::error("Failed to send notification to user {$recipient['user']->id}: {$e->getMessage()}");
                }
            }

            Log::info("Sent session notifications for session {$session->id} to {$recipients->count()} recipients");
        } catch (\Exception $e) {
            Log::error("Failed to notify session created {$session->id}: {$e->getMessage()}");
        }
    }

    /**
     * Notify about an updated session
     */
    public function notifySessionUpdated(Session $session): void
    {
        try {
            $subject = $session->subject()->with(['teacher', 'enrollments.student'])->first();

            if (!$subject) {
                Log::warning("Subject not found for session {$session->id}");
                return;
            }

            $recipients = $this->resolveRecipients($subject);

            foreach ($recipients as $recipient) {
                try {
                    $recipient['user']->notify(
                        new SessionUpdatedNotification($session, $recipient['role'])
                    );
                } catch (\Exception $e) {
                    Log::error("Failed to send update notification to user {$recipient['user']->id}: {$e->getMessage()}");
                }
            }

            Log::info("Sent session update notifications for session {$session->id} to {$recipients->count()} recipients");
        } catch (\Exception $e) {
            Log::error("Failed to notify session updated {$session->id}: {$e->getMessage()}");
        }
    }

    /**
     * Notify about batch created sessions
     */
    public function notifyBatchSessionsCreated(array $sessionIds): void
    {
        try {
            $sessions = Session::whereIn('id', $sessionIds)->with('subject')->get();

            foreach ($sessions as $session) {
                $this->notifySessionCreated($session);
            }

            Log::info("Processed batch notifications for " . count($sessionIds) . " sessions");
        } catch (\Exception $e) {
            Log::error("Failed to notify batch sessions: {$e->getMessage()}");
        }
    }

    /**
     * Resolve notification recipients (teacher + enrolled students)
     */
    protected function resolveRecipients($subject): Collection
    {
        $recipients = collect();

        // Add teacher if active
        if ($subject->teacher && $this->isUserActive($subject->teacher)) {
            $recipients->push([
                'user' => $subject->teacher,
                'role' => 'teacher'
            ]);
        }

        // Add enrolled students (status = 'active')
        $subject->enrollments()
            ->where('status', 'active')
            ->with('student')
            ->get()
            ->each(function ($enrollment) use (&$recipients) {
                if ($enrollment->student && $this->isUserActive($enrollment->student)) {
                    $recipients->push([
                        'user' => $enrollment->student,
                        'role' => 'student'
                    ]);
                }
            });

        return $recipients;
    }

    /**
     * Check if user is active
     */
    protected function isUserActive(User $user): bool
    {
        // Assuming users have a status field or similar
        // Adjust this based on your actual User model
        return !isset($user->status) || $user->status === 'active';
    }

    /**
     * Get recent notifications for a user
     */
    public function getRecentNotifications(User $user, int $limit = 10): array
    {
        $notifications = $user->notifications()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->toIso8601String(),
                    'created_at_human' => $notification->created_at->diffForHumans(),
                ];
            });

        return $notifications->toArray();
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(User $user, string $notificationId): bool
    {
        try {
            $notification = $user->notifications()->find($notificationId);

            if ($notification && !$notification->read_at) {
                $notification->markAsRead();
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error("Failed to mark notification as read: {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead(User $user): int
    {
        try {
            $count = $user->unreadNotifications()->count();
            $user->unreadNotifications()->update(['read_at' => now()]);
            return $count;
        } catch (\Exception $e) {
            Log::error("Failed to mark all notifications as read: {$e->getMessage()}");
            return 0;
        }
    }
}
