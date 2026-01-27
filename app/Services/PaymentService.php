<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentInstallment;
use App\Models\PaymentTransaction;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentService
{
    /**
     * Create a new payment for a student
     */
    public function createPayment(
        int $userId,
        int $programId,
        string $paymentType = 'full',
        array $options = []
    ): Payment {
        $program = Program::findOrFail($programId);
        $user = User::findOrFail($userId);

        $totalAmount = $program->price;
        $discountAmount = $options['discount_amount'] ?? 0;
        $remainingAmount = $totalAmount - $discountAmount;

        $payment = Payment::create([
            'user_id' => $userId,
            'program_id' => $programId,
            'created_by' => $options['created_by'] ?? auth()->id(),
            'total_amount' => $totalAmount,
            'paid_amount' => 0,
            'discount_amount' => $discountAmount,
            'remaining_amount' => $remainingAmount,
            'payment_type' => $paymentType,
            'payment_method' => $options['payment_method'] ?? null,
            'status' => 'pending',
            'notes' => $options['notes'] ?? null,
        ]);

        return $payment;
    }

    /**
     * Create installment plan for a payment
     */
    public function createInstallmentPlan(
        int $paymentId,
        int $numberOfInstallments,
        Carbon $startDate
    ): array {
        $payment = Payment::findOrFail($paymentId);

        // Calculate installment amount
        $installmentAmount = $this->calculateInstallmentAmounts(
            $payment->remaining_amount,
            $numberOfInstallments
        );

        $installments = [];
        $dueDate = $startDate->copy();

        for ($i = 1; $i <= $numberOfInstallments; $i++) {
            $installments[] = PaymentInstallment::create([
                'payment_id' => $payment->id,
                'installment_number' => $i,
                'amount' => $installmentAmount,
                'due_date' => $dueDate->copy(),
                'status' => 'pending',
            ]);

            // Next month
            $dueDate->addMonth();
        }

        // Update payment type to installment
        $payment->update(['payment_type' => 'installment']);

        return $installments;
    }

    /**
     * Record a manual payment
     */
    public function recordManualPayment(
        int $paymentId,
        float $amount,
        string $method,
        ?string $reference = null,
        ?int $adminId = null
    ): PaymentTransaction {
        $payment = Payment::findOrFail($paymentId);

        DB::beginTransaction();
        try {
            $transaction = PaymentTransaction::create([
                'payment_id' => $payment->id,
                'amount' => $amount,
                'type' => 'payment',
                'payment_method' => $method,
                'transaction_reference' => $reference,
                'status' => 'success',
                'created_by' => $adminId ?? auth()->id(),
            ]);

            // Update payment amounts
            $payment->updatePaidAmount();

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Record installment payment
     */
    public function recordInstallmentPayment(
        int $installmentId,
        string $method,
        ?string $reference = null,
        ?int $adminId = null
    ): PaymentTransaction {
        $installment = PaymentInstallment::findOrFail($installmentId);

        DB::beginTransaction();
        try {
            // Create transaction
            $transaction = PaymentTransaction::create([
                'payment_id' => $installment->payment_id,
                'installment_id' => $installment->id,
                'amount' => $installment->amount,
                'type' => 'payment',
                'payment_method' => $method,
                'transaction_reference' => $reference,
                'status' => 'success',
                'created_by' => $adminId ?? auth()->id(),
            ]);

            // Mark installment as paid
            $installment->markAsPaid($method, $reference, $adminId);

            DB::commit();
            return $transaction;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Waive payment (full or partial)
     */
    public function waivePayment(
        int $paymentId,
        float $waiveAmount,
        ?string $reason = null,
        ?int $adminId = null
    ): Payment {
        $payment = Payment::findOrFail($paymentId);

        DB::beginTransaction();
        try {
            // Add discount
            $payment->discount_amount += $waiveAmount;
            $payment->updateRemainingAmount();
            $payment->notes = ($payment->notes ?? '') . "\nإعفاء: " . $waiveAmount . " - " . $reason;
            $payment->save();

            // Create waive transaction
            PaymentTransaction::create([
                'payment_id' => $payment->id,
                'amount' => $waiveAmount,
                'type' => 'adjustment',
                'payment_method' => 'waived',
                'status' => 'success',
                'notes' => $reason,
                'created_by' => $adminId ?? auth()->id(),
            ]);

            $payment->updateStatus();
            $payment->save();

            DB::commit();
            return $payment->fresh();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Cancel payment
     */
    public function cancelPayment(
        int $paymentId,
        ?string $reason = null,
        ?int $adminId = null
    ): Payment {
        $payment = Payment::findOrFail($paymentId);

        $payment->update([
            'status' => 'cancelled',
            'notes' => ($payment->notes ?? '') . "\nملغي: " . $reason,
        ]);

        // Cancel all pending installments
        $payment->installments()
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        return $payment->fresh();
    }

    /**
     * Calculate installment amounts
     */
    public function calculateInstallmentAmounts(
        float $totalAmount,
        int $numberOfInstallments
    ): float {
        return round($totalAmount / $numberOfInstallments, 2);
    }

    /**
     * Get overdue installments
     */
    public function getOverdueInstallments()
    {
        return PaymentInstallment::with(['payment.user', 'payment.program'])
            ->overdue()
            ->get();
    }

    /**
     * Get student payment summary
     */
    public function getStudentPaymentSummary(int $userId): array
    {
        $payments = Payment::forUser($userId)->with('installments', 'transactions')->get();

        $totalAmount = $payments->sum('total_amount');
        $paidAmount = $payments->sum('paid_amount');
        $remainingAmount = $payments->sum('remaining_amount');

        $pendingInstallments = PaymentInstallment::whereIn('payment_id', $payments->pluck('id'))
            ->pending()
            ->count();

        $overdueInstallments = PaymentInstallment::whereIn('payment_id', $payments->pluck('id'))
            ->overdue()
            ->count();

        return [
            'total_amount' => $totalAmount,
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount,
            'pending_installments' => $pendingInstallments,
            'overdue_installments' => $overdueInstallments,
            'payments' => $payments,
        ];
    }
}
