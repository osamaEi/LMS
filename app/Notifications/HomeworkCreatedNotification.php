<?php

namespace App\Notifications;

use App\Models\Homework;
use Illuminate\Notifications\Notification;

class HomeworkCreatedNotification extends Notification
{
    public function __construct(protected Homework $homework) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $homework = $this->homework->loadMissing('session.subject');
        $session  = $homework->session;
        $subject  = $session?->subject;

        $title   = $homework->title_ar ?: ($homework->title_en ?: 'واجب جديد');
        $dateStr = $homework->due_date
            ? ' — موعد التسليم: ' . \Carbon\Carbon::parse($homework->due_date)->format('Y/m/d')
            : '';

        $body = "تم إضافة واجب منزلي جديد «{$title}» في مادة " .
                ($subject?->name_ar ?? $subject?->name ?? '') .
                "{$dateStr}";

        return [
            'notification_type' => 'homework_created',
            'homework_id'       => $homework->id,
            'homework_title'    => $title,
            'subject_id'        => $subject?->id,
            'subject_name'      => $subject?->name_ar ?? $subject?->name ?? '',
            'session_id'        => $session?->id,
            'due_date'          => $homework->due_date,
            'title'             => 'واجب منزلي جديد',
            'body'              => $body,
            'message_ar'        => $body,
            'icon'              => 'document-text',
            'action_url'        => route('student.homework.index'),
        ];
    }
}
