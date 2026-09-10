<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class TeacherGradesNotification extends Notification
{
    public function __construct(
        public int $teacherId,
        public string $teacherName,
        public array $grades,
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $body = 'أرسل المعلم ' . $this->teacherName . ' درجاتك: '
            . collect($this->grades)->map(fn ($grade) => $grade['name'] . ': ' . $grade['total'] . ' / 100')->implode('، ');

        return [
            'notification_type' => 'teacher_grades',
            'title' => 'درجاتك الدراسية',
            'body' => $body,
            'message_ar' => $body,
            'sender_name' => $this->teacherName,
            'teacher_id' => $this->teacherId,
            'icon' => 'academic-cap',
            'action_url' => route('notifications.index'),
            'grades' => $this->grades,
        ];
    }
}
