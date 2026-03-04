<?php

namespace App\Console\Commands;

use App\Models\PaymentInstallment;
use App\Notifications\PaymentReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendPaymentReminders extends Command
{
    protected $signature = 'payments:send-reminders
                            {--days=3 : Send reminders for installments due within this many days}';

    protected $description = 'Send payment reminder notifications to students for upcoming installments';

    public function handle(): int
    {
        $maxDays = (int) $this->option('days');

        // Find pending installments due within the next N days
        // We send on the exact day the installment is N days away,
        // and again 1 day before — using whereIn to catch both milestones.
        $targetDays = collect(range(1, $maxDays));

        $sent = 0;
        $failed = 0;

        foreach ($targetDays as $days) {
            $targetDate = now()->addDays($days)->toDateString();

            $installments = PaymentInstallment::with(['payment.user', 'payment.program'])
                ->where('status', 'pending')
                ->whereDate('due_date', $targetDate)
                ->get();

            foreach ($installments as $installment) {
                $student = $installment->payment?->user;

                if (!$student) {
                    continue;
                }

                // Skip if we already sent a reminder for this installment today
                $alreadySent = $student->notifications()
                    ->where('type', PaymentReminderNotification::class)
                    ->whereDate('created_at', today())
                    ->where('data->installment_id', $installment->id)
                    ->exists();

                if ($alreadySent) {
                    continue;
                }

                try {
                    $student->notify(new PaymentReminderNotification($installment, $days));
                    $sent++;
                } catch (\Exception $e) {
                    Log::error("Failed to send payment reminder to student {$student->id}: {$e->getMessage()}");
                    $failed++;
                }
            }
        }

        $this->info("Payment reminders sent: {$sent}, failed: {$failed}");
        Log::info("SendPaymentReminders: sent={$sent}, failed={$failed}");

        return Command::SUCCESS;
    }
}
