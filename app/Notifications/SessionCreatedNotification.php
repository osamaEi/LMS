<?php

namespace App\Notifications;

use App\Models\Session;
use Illuminate\Notifications\Notification;

class SessionCreatedNotification extends Notification
{
    protected Session $session;
    protected string $recipientRole;

    /**
     * Create a new notification instance.
     */
    public function __construct(Session $session, string $recipientRole)
    {
        $this->session = $session->load('subject');
        $this->recipientRole = $recipientRole; // 'teacher' or 'student'
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $scheduledAt = $this->session->scheduled_at;

        return [
            'session_id' => $this->session->id,
            'session_title' => $this->session->title_ar ?? $this->session->title_en,
            'subject_id' => $this->session->subject_id,
            'subject_name' => $this->session->subject->name ?? 'N/A',
            'scheduled_at' => $scheduledAt->toIso8601String(),
            'scheduled_at_formatted' => $scheduledAt->format('Y-m-d H:i'),
            'duration_minutes' => $this->session->duration_minutes,
            'zoom_join_url' => $this->session->zoom_join_url,
            'notification_type' => 'session_created',
            'recipient_role' => $this->recipientRole,
            'icon' => 'calendar',
            'action_url' => $this->getActionUrl(),
            'message_ar' => $this->getMessageAr(),
        ];
    }

    /**
     * Get the action URL based on recipient role.
     */
    protected function getActionUrl(): string
    {
        if ($this->recipientRole === 'teacher') {
            return route('teacher.sessions.show', $this->session->id);
        }

        return route('student.subjects.show', $this->session->subject_id);
    }

    /**
     * Get the Arabic message.
     */
    protected function getMessageAr(): string
    {
        $title = $this->session->title_ar ?? $this->session->title_en;
        $subject = $this->session->subject->name ?? 'N/A';
        $date = $this->session->scheduled_at->format('Y-m-d');
        $time = $this->session->scheduled_at->format('H:i');

        return "تم جدولة جلسة جديدة: {$title} في مادة {$subject} بتاريخ {$date} الساعة {$time}";
    }
}
