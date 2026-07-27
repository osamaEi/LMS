<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentReportNotification extends Notification
{
    /**
     * @param array  $summary  Report headline figures (see StudentReportController::buildReport).
     * @param ?string $note    Optional admin note included with the report.
     * @param ?string $pdf     Raw PDF bytes to attach to the email.
     * @param bool   $hasEmail Whether to also deliver via email.
     */
    public function __construct(
        protected array $summary,
        protected ?string $note = null,
        protected ?string $pdf = null,
        protected bool $hasEmail = true,
    ) {}

    public function via($notifiable): array
    {
        $channels = ['database'];
        if ($this->hasEmail) {
            $channels[] = 'mail';
        }
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $rate = $this->summary['attendance_rate'] ?? 0;
        $avg  = $this->summary['avg_quiz'];

        $mail = (new MailMessage)
            ->subject('تقريرك الأكاديمي')
            ->greeting('مرحباً ' . ($notifiable->name ?? ''))
            ->line('تم إعداد تقرير أكاديمي بحالتك الدراسية.')
            ->line('نسبة الحضور: ' . $rate . '%')
            ->line('عدد الاختبارات: ' . ($this->summary['quizzes_count'] ?? 0)
                . ($avg !== null ? ' — متوسط الدرجات: ' . $avg . '%' : ''))
            ->line('عدد الواجبات: ' . ($this->summary['homework_count'] ?? 0));

        if ($this->note) {
            $mail->line('ملاحظة الإدارة: ' . $this->note);
        }

        if ($this->pdf) {
            $mail->attachData($this->pdf, 'academic-report.pdf', [
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }

    public function toArray($notifiable): array
    {
        return [
            'notification_type' => 'student_report',
            'title'             => 'تقريرك الأكاديمي',
            'icon'              => 'document-text',
            'message_ar'        => 'تم إرسال تقرير أكاديمي بحالتك الدراسية'
                . (($this->summary['attendance_rate'] ?? null) !== null
                    ? ' — نسبة الحضور ' . $this->summary['attendance_rate'] . '%'
                    : ''),
            'note'              => $this->note,
            'summary'           => $this->summary,
        ];
    }
}
