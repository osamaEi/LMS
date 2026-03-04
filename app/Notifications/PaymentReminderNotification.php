<?php

namespace App\Notifications;

use App\Models\PaymentInstallment;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification
{
    public function __construct(
        protected PaymentInstallment $installment,
        protected int $daysUntilDue
    ) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $installment = $this->installment->loadMissing(['payment.program']);
        $payment     = $installment->payment;
        $program     = $payment?->program;

        $dueDate  = $installment->due_date->format('Y/m/d');
        $amount   = number_format($installment->amount, 2);
        $days     = $this->daysUntilDue;

        $urgency = match(true) {
            $days <= 1 => 'غداً',
            $days <= 3 => "خلال {$days} أيام",
            default    => "بتاريخ {$dueDate}",
        };

        $body = "تذكير: القسط رقم {$installment->installment_number} بمبلغ {$amount} ريال يستحق {$urgency} ({$dueDate}).";

        return [
            'notification_type'    => 'payment_reminder',
            'installment_id'       => $installment->id,
            'payment_id'           => $payment?->id,
            'program_id'           => $payment?->program_id,
            'program_name'         => $program?->name_ar ?? $program?->name ?? '',
            'installment_number'   => $installment->installment_number,
            'amount'               => $installment->amount,
            'due_date'             => $installment->due_date->toDateString(),
            'days_until_due'       => $days,
            'title'                => 'تذكير بموعد القسط',
            'body'                 => $body,
            'message_ar'           => $body,
            'icon'                 => 'bell',
            'action_url'           => $payment ? route('student.payments.show', $payment->id) : '#',
        ];
    }
}
