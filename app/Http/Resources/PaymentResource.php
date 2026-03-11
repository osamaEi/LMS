<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,

            // Amounts
            'total_amount'         => $this->total_amount,
            'paid_amount'          => $this->paid_amount,
            'discount_amount'      => $this->discount_amount,
            'remaining_amount'     => $this->remaining_amount,

            // Type & method
            'payment_type'         => $this->payment_type,
            'payment_type_label'   => $this->payment_type_display_name,
            'payment_method'       => $this->payment_method,
            'payment_method_label' => $this->payment_method_display_name,

            // Status
            'status'               => $this->status,
            'status_label'         => $this->status_display_name,
            'is_fully_paid'        => $this->isFullyPaid(),
            'is_installment'       => $this->isInstallment(),

            // Dates
            'completed_at'         => $this->completed_at?->toIso8601String(),
            'created_at'           => $this->created_at->toIso8601String(),

            // Notes
            'notes'                => $this->notes,

            // Relations
            'program'              => $this->whenLoaded('program', fn() => [
                'id'   => $this->program->id,
                'name' => $this->program->name,
                'code' => $this->program->code,
            ]),
            'installments'         => PaymentInstallmentResource::collection(
                $this->whenLoaded('installments')
            ),
            'transactions'         => PaymentTransactionResource::collection(
                $this->whenLoaded('transactions')
            ),
        ];
    }
}
