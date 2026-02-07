<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\NafathTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NafathWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('Nafath Webhook Received', [
            'payload' => $payload,
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
        ]);

        $transId = $payload['transId'] ?? $payload['transactionId'] ?? $payload['trans_id'] ?? null;
        $status = $payload['status'] ?? null;

        if (!$transId || !$status) {
            Log::warning('Nafath Webhook: Missing transId or status', ['payload' => $payload]);
            return response()->json(['error' => 'Missing transId or status'], 400);
        }

        $transaction = NafathTransaction::where('transaction_id', $transId)->first();

        if (!$transaction) {
            Log::warning('Nafath Webhook: Transaction not found', ['transId' => $transId]);
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        // Skip if already completed
        if (in_array($transaction->status, ['approved', 'rejected', 'expired'])) {
            Log::info('Nafath Webhook: Transaction already completed', [
                'transId' => $transId,
                'current_status' => $transaction->status,
            ]);
            return response()->json(['success' => true, 'message' => 'Already processed']);
        }

        $nafathStatus = strtoupper($status);

        if ($nafathStatus === 'COMPLETED' || $nafathStatus === 'APPROVED') {
            $transaction->markAsApproved($payload);

            if ($transaction->user_id) {
                User::find($transaction->user_id)?->update([
                    'nafath_verified_at' => now(),
                    'nafath_transaction_id' => $transaction->transaction_id,
                ]);
            }

            Log::info('Nafath Webhook: Transaction approved', ['transId' => $transId]);
        } elseif ($nafathStatus === 'REJECTED') {
            $transaction->markAsRejected($payload);
            Log::info('Nafath Webhook: Transaction rejected', ['transId' => $transId]);
        } elseif ($nafathStatus === 'EXPIRED') {
            $transaction->update([
                'status' => 'expired',
                'response_payload' => $payload,
                'completed_at' => now(),
            ]);
            Log::info('Nafath Webhook: Transaction expired', ['transId' => $transId]);
        }

        return response()->json(['success' => true]);
    }
}
