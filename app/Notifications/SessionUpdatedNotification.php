<?php

namespace App\Notifications;

use App\Models\Session;
use Illuminate\Notifications\Notification;

class SessionUpdatedNotification extends Notification
{
    protected Session $session;
    protected string $recipientRole;

    public function __construct(Session $session, string $recipientRole)
    {
        $this->session = $session->load('subject');
        $this->recipientRole = $recipientRole;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $scheduledAt = $this->session->scheduled_at;

        return [
            'session_id' => $this->session->id,
            'session_title' => $this->session->title_ar ?? $this->session->title_en,
            'subject_id' => $this->session->subject_id,
            'subject_name' => $this->session->subject->name ?? 'N/A',
            'scheduled_at' => $scheduledAt ? $scheduledAt->toIso8601String() : null,
            'scheduled_at_formatted' => $scheduledAt ? $scheduledAt->format('Y-m-d H:i') : null,
            'duration_minutes' => $this->session->duration_minutes,
            'notification_type' => 'session_updated',
            'recipient_role' => $this->recipientRole,
            'icon' => 'edit',
            'action_url' => $this->getActionUrl(),
            'message_ar' => $this->getMessageAr(),
        ];
    }

    protected function getActionUrl(): string
    {
        if ($this->recipientRole === 'teacher') {
            return route('teacher.sessions.show', $this->session->id);
        }

        return route('student.subjects.show', $this->session->subject_id);
    }

    protected function getMessageAr(): string
    {
        $title = $this->session->title_ar ?? $this->session->title_en;
        $subject = $this->session->subject->name ?? 'N/A';

        return "تم تحديث جلسة: {$title} في مادة {$subject}";
    }
}
