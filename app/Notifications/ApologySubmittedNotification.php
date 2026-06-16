<?php

namespace App\Notifications;

use App\Models\AttendanceApology;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApologySubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(protected AttendanceApology $apology)
    {
        $this->apology->loadMissing(['student', 'session']);
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $studentName = $this->apology->student?->name ?: 'طالب';
        $sessionTitle = $this->apology->session?->title ?: 'محاضرة';

        return (new MailMessage)
            ->subject('عذر غياب جديد بانتظار المراجعة')
            ->greeting('مرحباً')
            ->line("قام الطالب {$studentName} بتقديم عذر غياب عن المحاضرة: {$sessionTitle}.")
            ->line('السبب: ' . $this->apology->reason)
            ->action('مراجعة العذر', route('admin.apologies.index'))
            ->line('يرجى مراجعة الطلب والموافقة عليه أو رفضه.');
    }

    public function toArray($notifiable): array
    {
        return [
            'apology_id'       => $this->apology->id,
            'student_id'       => $this->apology->student_id,
            'student_name'     => $this->apology->student?->name ?: 'طالب',
            'session_id'       => $this->apology->session_id,
            'session_title'    => $this->apology->session?->title ?: 'محاضرة',
            'notification_type'=> 'apology_submitted',
            'icon'             => 'clipboard-check',
            'action_url'       => route('admin.apologies.index'),
            'message_ar'       => 'عذر غياب جديد من ' . ($this->apology->student?->name ?: 'طالب') . ' بانتظار المراجعة',
        ];
    }
}
