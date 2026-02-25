<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class CustomNotification extends Notification
{
    public function __construct(
        protected string $title,
        protected string $body,
        protected string $actionUrl = '#',
        protected string $senderName = '',
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'notification_type' => 'custom',
            'title'             => $this->title,
            'session_title'     => $this->title,
            'body'              => $this->body,
            'message_ar'        => $this->body,
            'action_url'        => $this->actionUrl,
            'sender_name'       => $this->senderName,
            'icon'              => 'bell',
        ];
    }
}
