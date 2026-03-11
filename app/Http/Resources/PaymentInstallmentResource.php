<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentInstallmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'installment_number'    => $this->installment_number,
            'amount'                => $this->amount,
            'due_date'              => $this->due_date?->format('Y-m-d'),
            'status'                => $this->status,
            'status_label'          => $this->status_display_name,
            'is_overdue'            => $this->isOverdue(),
            'payment_method'        => $this->payment_method,
            'payment_method_label'  => $this->payment_method_display_name,
            'transaction_reference' => $this->transaction_reference,
            'paid_at'               => $this->paid_at?->toIso8601String(),
            'notes'                 => $this->notes,
        ];
    }
}
