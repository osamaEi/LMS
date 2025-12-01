<?php

namespace App\Services;

use App\Models\NafathTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NafathService
{
    protected string $apiUrl;
    protected string $apiKey;
    protected int $timeout;

    public function __construct()
    {
        $this->apiUrl = config('services.nafath.api_url', 'https://api.nafath.sa');
        $this->apiKey = config('services.nafath.api_key', '');
        $this->timeout = config('services.nafath.timeout', 300); // 5 minutes
    }

    /**
     * Initiate Nafath OpenAccount verification
     */
    public function initiateOpenAccount(string $nationalId, ?int $userId = null): NafathTransaction
    {
        // Generate unique transaction ID
        $transactionId = $this->generateTransactionId();

        // Prepare request payload
        $requestPayload = [
            'national_id' => $nationalId,
            'service' => 'OpenAccount',
            'transaction_id' => $transactionId,
            'timestamp' => now()->toIso8601String(),
        ];

        // Create transaction record
        $transaction = NafathTransaction::create([
            'user_id' => $userId,
            'transaction_id' => $transactionId,
            'national_id' => $nationalId,
            'status' => 'pending',
            'request_payload' => $requestPayload,
        ]);

        // Call Nafath API
        $this->callNafathApi($transaction, $requestPayload);

        return $transaction;
    }

    /**
     * Poll Nafath transaction status
     */
    public function pollStatus(string $transactionId): NafathTransaction
    {
        $transaction = NafathTransaction::where('transaction_id', $transactionId)->firstOrFail();

        // If already completed, return current status
        if (in_array($transaction->status, ['approved', 'rejected', 'expired'])) {
            return $transaction;
        }

        // Check if transaction has expired (5 minutes timeout)
        if ($transaction->created_at->addSeconds($this->timeout)->isPast()) {
            $transaction->update(['status' => 'expired']);
            return $transaction;
        }

        // Update poll timestamp
        $transaction->updatePollTimestamp();

        // Poll Nafath API for status
        $this->pollNafathApi($transaction);

        return $transaction->fresh();
    }

    /**
     * Generate unique transaction ID
     */
    protected function generateTransactionId(): string
    {
        return 'NFT-' . strtoupper(Str::random(16)) . '-' . time();
    }

    /**
     * Call Nafath API to initiate verification
     */
    protected function callNafathApi(NafathTransaction $transaction, array $payload): void
    {
        // TODO: Implement actual Nafath API integration
        // For now, just log the request

        Log::info('Nafath OpenAccount initiated', [
            'transaction_id' => $transaction->transaction_id,
            'national_id' => $transaction->national_id,
        ]);

        // In production, make actual API call:
        // try {
        //     $response = Http::withHeaders([
        //         'Authorization' => 'Bearer ' . $this->apiKey,
        //         'Content-Type' => 'application/json',
        //     ])->post($this->apiUrl . '/openaccount/initiate', $payload);
        //
        //     if ($response->successful()) {
        //         $transaction->update([
        //             'response_payload' => $response->json(),
        //         ]);
        //     } else {
        //         Log::error('Nafath API error', [
        //             'status' => $response->status(),
        //             'body' => $response->body(),
        //         ]);
        //     }
        // } catch (\Exception $e) {
        //     Log::error('Nafath API exception', [
        //         'message' => $e->getMessage(),
        //     ]);
        // }
    }

    /**
     * Poll Nafath API for transaction status
     */
    protected function pollNafathApi(NafathTransaction $transaction): void
    {
        // TODO: Implement actual Nafath polling
        // For development/testing, simulate random approval after some time

        // Simulate: if polled more than 3 times, approve it
        $pollCount = NafathTransaction::where('transaction_id', $transaction->transaction_id)
            ->whereNotNull('polled_at')
            ->count();

        if ($pollCount >= 3) {
            // Simulate approval
            $transaction->markAsApproved([
                'status' => 'approved',
                'message' => 'Identity verified successfully',
                'verified_at' => now()->toIso8601String(),
            ]);

            // Update user if exists
            if ($transaction->user_id) {
                User::find($transaction->user_id)?->update([
                    'nafath_verified_at' => now(),
                    'nafath_transaction_id' => $transaction->transaction_id,
                ]);
            }

            Log::info('Nafath transaction approved (simulated)', [
                'transaction_id' => $transaction->transaction_id,
            ]);
        }

        // In production, make actual API call:
        // try {
        //     $response = Http::withHeaders([
        //         'Authorization' => 'Bearer ' . $this->apiKey,
        //         'Content-Type' => 'application/json',
        //     ])->get($this->apiUrl . '/openaccount/status/' . $transaction->transaction_id);
        //
        //     if ($response->successful()) {
        //         $data = $response->json();
        //         $status = $data['status'] ?? 'pending';
        //
        //         if ($status === 'approved') {
        //             $transaction->markAsApproved($data);
        //
        //             // Update user verification
        //             if ($transaction->user_id) {
        //                 User::find($transaction->user_id)?->update([
        //                     'nafath_verified_at' => now(),
        //                     'nafath_transaction_id' => $transaction->transaction_id,
        //                 ]);
        //             }
        //         } elseif ($status === 'rejected') {
        //             $transaction->markAsRejected($data);
        //         }
        //     }
        // } catch (\Exception $e) {
        //     Log::error('Nafath polling exception', [
        //         'message' => $e->getMessage(),
        //     ]);
        // }
    }

    /**
     * Check if national ID has been verified via Nafath
     */
    public function isNationalIdVerified(string $nationalId): bool
    {
        return NafathTransaction::where('national_id', $nationalId)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Get transaction by ID
     */
    public function getTransaction(string $transactionId): ?NafathTransaction
    {
        return NafathTransaction::where('transaction_id', $transactionId)->first();
    }
}
