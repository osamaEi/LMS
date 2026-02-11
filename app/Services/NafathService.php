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
    protected string $appId;
    protected string $appKey;
    protected int $timeout;

    public function __construct()
    {
        $this->apiUrl = config('services.nafath.api_url', 'https://rabet-nafath.api.elm.sa');
        $this->appId = config('services.nafath.app_id', '');
        $this->appKey = config('services.nafath.app_key', '');
        $this->timeout = config('services.nafath.timeout', 300);
    }

    /**
     * Initiate Nafath OpenAccount verification
     */
    public function initiateOpenAccount(string $nationalId, ?int $userId = null): NafathTransaction
    {
        $requestId = Str::uuid()->toString();

        $requestPayload = [
            'nationalId' => $nationalId,
            'service' => 'OpenAccount',
        ];

        // Create transaction record first
        $transaction = NafathTransaction::create([
            'user_id' => $userId,
            'transaction_id' => $requestId,
            'national_id' => $nationalId,
            'status' => 'pending',
            'request_payload' => array_merge($requestPayload, ['requestId' => $requestId]),
        ]);

        // Call Nafath API
        $this->callNafathApi($transaction, $requestPayload, $requestId);

        return $transaction->fresh();
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
     * Call Nafath API to initiate MFA request
     */
    protected function callNafathApi(NafathTransaction $transaction, array $payload, string $requestId): void
    {
        try {
            // Check if using local mock (bypass HTTP call to avoid timeout)
            $isLocalMock = str_contains($this->apiUrl, '127.0.0.1') || str_contains($this->apiUrl, 'localhost');

            if ($isLocalMock) {
                // Simulate local mock response directly
                $result = [
                    'transId' => Str::uuid()->toString(),
                    'random' => rand(10, 99),
                    'status' => 'WAITING',
                    'message' => 'MFA request created successfully (LOCAL MOCK)',
                ];

                $transaction->update([
                    'transaction_id' => $result['transId'],
                    'response_payload' => $result,
                ]);

                Log::info('Nafath OpenAccount (Local Mock - Direct)', [
                    'transaction_id' => $result['transId'],
                    'random' => $result['random'],
                ]);
                return;
            }

            // Check if using external mock service
            $isMockService = str_contains($this->apiUrl, 'mock-service');

            // Build endpoint and headers based on environment
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            if ($isMockService) {
                // Mock service expects credentials as headers with underscores
                $endpoint = $this->apiUrl . '/api/v1/mfa/request?local=ar&requestId=' . $requestId;
                $headers['app_id'] = $this->appId;
                $headers['app_key'] = $this->appKey;
            } else {
                $endpoint = $this->apiUrl . '/stg/api/v1/mfa/request?local=ar&requestId=' . $requestId;
                $headers['APP-ID'] = $this->appId;
                $headers['APP-KEY'] = $this->appKey;
            }

            $response = Http::withoutVerifying()
                ->withHeaders($headers)
                ->post($endpoint, $payload);

            $result = $response->json();

            Log::info('Nafath OpenAccount API request/response', [
                'endpoint' => $endpoint,
                'is_mock' => $isMockService,
                'headers_sent' => array_keys($headers),
                'app_id' => $this->appId,
                'app_key' => substr($this->appKey, 0, 8) . '...',
                'payload' => $payload,
                'http_status' => $response->status(),
                'raw_body' => $response->body(),
                'json_body' => $result,
            ]);

            if ($response->successful() && isset($result['transId'])) {
                $transaction->update([
                    'transaction_id' => $result['transId'],
                    'response_payload' => $result,
                ]);
            } else {
                Log::error('Nafath API initiation failed', [
                    'status' => $response->status(),
                    'body' => $result,
                ]);

                $transaction->update([
                    'status' => 'rejected',
                    'response_payload' => $result ?? ['error' => 'API call failed with status ' . $response->status()],
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Nafath API exception', [
                'message' => $e->getMessage(),
            ]);

            $transaction->update([
                'status' => 'rejected',
                'response_payload' => ['error' => $e->getMessage()],
            ]);
        }
    }

    /**
     * Poll Nafath API for transaction status
     */
    protected function pollNafathApi(NafathTransaction $transaction): void
    {
        try {
            // Check if using local mock (bypass HTTP call to avoid timeout)
            $isLocalMock = str_contains($this->apiUrl, '127.0.0.1') || str_contains($this->apiUrl, 'localhost');

            if ($isLocalMock) {
                // Simulate local mock response - always return COMPLETED for immediate approval
                $result = [
                    'transId' => $transaction->transaction_id,
                    'status' => 'COMPLETED',
                    'message' => 'Status retrieved successfully (LOCAL MOCK)',
                ];

                Log::info('Nafath poll response (Local Mock - Direct)', [
                    'transaction_id' => $transaction->transaction_id,
                    'status' => 'COMPLETED',
                ]);

                // Process the result
                $transaction->markAsApproved($result);

                if ($transaction->user_id) {
                    User::find($transaction->user_id)?->update([
                        'nafath_verified_at' => now(),
                        'nafath_transaction_id' => $transaction->transaction_id,
                    ]);
                }

                return;
            }

            // Check if using external mock service
            $isMockService = str_contains($this->apiUrl, 'mock-service');

            // Build endpoint and headers based on environment
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            if ($isMockService) {
                // Mock service expects credentials as headers with underscores
                $endpoint = $this->apiUrl . '/api/v1/mfa/request/status/' . $transaction->transaction_id . '?local=ar';
                $headers['app_id'] = $this->appId;
                $headers['app_key'] = $this->appKey;
            } else {
                $endpoint = $this->apiUrl . '/stg/api/v1/mfa/request/status/' . $transaction->transaction_id . '?local=ar';
                $headers['APP-ID'] = $this->appId;
                $headers['APP-KEY'] = $this->appKey;
            }

            $response = Http::withoutVerifying()
                ->withHeaders($headers)
                ->get($endpoint);

            $result = $response->json();

            Log::info('Nafath poll response', [
                'transaction_id' => $transaction->transaction_id,
                'status' => $response->status(),
                'body' => $result,
            ]);

            if ($response->successful() && isset($result['status'])) {
                $nafathStatus = strtoupper($result['status']);

                if ($nafathStatus === 'COMPLETED' || $nafathStatus === 'APPROVED') {
                    $transaction->markAsApproved($result);

                    if ($transaction->user_id) {
                        User::find($transaction->user_id)?->update([
                            'nafath_verified_at' => now(),
                            'nafath_transaction_id' => $transaction->transaction_id,
                        ]);
                    }
                } elseif ($nafathStatus === 'REJECTED') {
                    $transaction->markAsRejected($result);
                } elseif ($nafathStatus === 'EXPIRED') {
                    $transaction->update([
                        'status' => 'expired',
                        'response_payload' => $result,
                        'completed_at' => now(),
                    ]);
                }
                // If WAITING, do nothing - still pending
            }
        } catch (\Exception $e) {
            Log::error('Nafath polling exception', [
                'transaction_id' => $transaction->transaction_id,
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get the random number from a transaction response
     */
    public function getRandomNumber(NafathTransaction $transaction): ?string
    {
        $response = $transaction->response_payload;
        return $response['random'] ?? $response['Random'] ?? null;
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
