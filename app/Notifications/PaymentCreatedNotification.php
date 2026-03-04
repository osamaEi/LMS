<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Notifications\Notification;

class PaymentCreatedNotification extends Notification
{
    public function __construct(protected Payment $payment) {}

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $payment = $this->payment->load(['program', 'installments']);
        $program = $payment->program;

        $typeLabel = $payment->payment_type === 'installment' ? 'تقسيط' : 'دفعة كاملة';
        $installmentsCount = $payment->installments->count();

        $body = $payment->payment_type === 'installment'
            ? "تم إنشاء خطة دفع ({$typeLabel}) بمبلغ " . number_format($payment->total_amount, 2) . " ريال على {$installmentsCount} أقساط."
            : "تم إنشاء دفعة ({$typeLabel}) بمبلغ " . number_format($payment->total_amount, 2) . " ريال.";

        return [
            'notification_type' => 'payment_created',
            'payment_id'        => $payment->id,
            'program_id'        => $payment->program_id,
            'program_name'      => $program?->name_ar ?? $program?->name ?? '',
            'total_amount'      => $payment->total_amount,
            'payment_type'      => $payment->payment_type,
            'payment_method'    => $payment->payment_method,
            'installments_count' => $installmentsCount,
            'title'             => 'تم إنشاء دفعة جديدة',
            'body'              => $body,
            'message_ar'        => $body,
            'icon'              => 'payment',
            'action_url'        => route('student.payments.show', $payment->id),
        ];
    }
}
