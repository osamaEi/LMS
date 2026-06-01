<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Nearest unpaid installment due date
        $nextDueDate = null;
        if ($this->relationLoaded('installments')) {
            $next = $this->installments
                ->where('status', '!=', 'paid')
                ->sortBy('due_date')
                ->first();
            $nextDueDate = $next?->due_date?->format('Y-m-d');
        }

        // Include pending receipt transactions so student sees deduction before admin approval
        $pendingAmount = 0;
        if ($this->relationLoaded('transactions')) {
            $pendingAmount = $this->transactions
                ->where('status', 'pending')
                ->where('type', 'payment')
                ->sum('amount');
        }

        $effectivePaid      = (float) $this->paid_amount + $pendingAmount;
        $effectiveRemaining = max(0, (float) $this->total_amount - $effectivePaid - (float) $this->discount_amount);

        return [
            'id'               => $this->id,
            'program'          => $this->whenLoaded('program', fn() => [
                'id'   => $this->program->id,
                'name' => $this->program->name_ar ?? $this->program->name,
                'code' => $this->program->code,
            ]),
            'status'           => $this->status,
            'status_label'     => $this->status_display_name,
            'is_fully_paid'    => $this->isFullyPaid(),
            'due_date'         => $nextDueDate,
            'total_amount'     => $this->total_amount,
            'paid_amount'      => number_format($effectivePaid, 3, '.', ''),
            'remaining_amount' => number_format($effectiveRemaining, 3, '.', ''),
        ];
    }
}
