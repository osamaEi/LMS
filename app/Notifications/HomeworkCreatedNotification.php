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
        $homework = $this->homework->loadMissing('subject', 'program');
        $subject  = $homework->subject;
        $program  = $homework->program;
        $entity   = $subject ?? $program;

        $title   = $homework->title_ar ?: ($homework->title_en ?: 'واجب جديد');
        $dateStr = $homework->due_date
            ? ' — موعد التسليم: ' . \Carbon\Carbon::parse($homework->due_date)->format('Y/m/d')
            : '';

        $body = "تم إضافة واجب منزلي جديد «{$title}» في " .
                ($entity?->name_ar ?? $entity?->name ?? '') .
                "{$dateStr}";

        return [
            'notification_type' => 'homework_created',
            'homework_id'       => $homework->id,
            'homework_title'    => $title,
            'subject_id'        => $subject?->id,
            'subject_name'      => $entity?->name_ar ?? $entity?->name ?? '',
            'program_id'        => $program?->id,
            'due_date'          => $homework->due_date,
            'title'             => 'واجب منزلي جديد',
            'body'              => $body,
            'message_ar'        => $body,
            'icon'              => 'document-text',
            'action_url'        => route('student.homework.index'),
        ];
    }
}
