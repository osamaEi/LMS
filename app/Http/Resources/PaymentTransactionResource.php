<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'amount'                => $this->amount,
            'type'                  => $this->type,
            'type_label'            => $this->type_display_name,
            'payment_method'        => $this->payment_method,
            'payment_method_label'  => $this->payment_method_display_name,
            'transaction_reference' => $this->transaction_reference,
            'transaction_id'        => $this->transaction_id,
            'status'                => $this->status,
            'status_label'          => $this->status_display_name,
            'response_message'      => $this->response_message,
            'notes'                 => $this->notes,
            'created_at'            => $this->created_at->toIso8601String(),
        ];
    }
}
