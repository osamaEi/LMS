<?php

namespace App\Notifications;

use App\Models\AttendanceApology;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApologyReviewedNotification extends Notification
{
    use Queueable;

    public function __construct(protected AttendanceApology $apology)
    {
        $this->apology->loadMissing('session');
    }

    public function via($notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $sessionTitle = $this->apology->session?->title ?: 'محاضرة';
        $approved = $this->apology->isApproved();

        $mail = (new MailMessage)
            ->subject($approved ? 'تم قبول عذر الغياب' : 'تم رفض عذر الغياب')
            ->greeting('مرحباً')
            ->line($approved
                ? "تم قبول عذر غيابك عن المحاضرة: {$sessionTitle}، وتم تسجيلك كـ \"معذور\"."
                : "نأسف، تم رفض عذر غيابك عن المحاضرة: {$sessionTitle}.");

        if (!empty($this->apology->review_note)) {
            $mail->line('ملاحظة الإدارة: ' . $this->apology->review_note);
        }

        return $mail->action('عرض محاضراتي', route('student.my-sessions'));
    }

    public function toArray($notifiable): array
    {
        $approved = $this->apology->isApproved();

        return [
            'apology_id'       => $this->apology->id,
            'session_id'       => $this->apology->session_id,
            'session_title'    => ($this->apology->session?->title ?: 'محاضرة'),
            'status'           => $this->apology->status,
            'review_note'      => $this->apology->review_note,
            'notification_type'=> 'apology_reviewed',
            'icon'             => $approved ? 'check-circle' : 'x-circle',
            'action_url'       => route('student.my-sessions'),
            'message_ar'       => $approved
                ? 'تم قبول عذر غيابك عن ' . ($this->apology->session->title ?? 'المحاضرة')
                : 'تم رفض عذر غيابك عن ' . ($this->apology->session->title ?? 'المحاضرة'),
        ];
    }
}
