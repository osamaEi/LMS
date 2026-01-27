<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TamaraPaymentService
{
    protected ?string $apiUrl;
    protected ?string $apiToken;
    protected ?string $merchantUrl;
    protected ?string $notificationToken;

    public function __construct()
    {
        $this->apiUrl = config('services.tamara.api_url', 'https://api.tamara.co');
        $this->apiToken = config('services.tamara.api_token');
        $this->merchantUrl = config('services.tamara.merchant_url');
        $this->notificationToken = config('services.tamara.notification_token');
    }

    /**
     * Check if Tamara is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiToken) && !empty($this->merchantUrl);
    }

    /**
     * Create Tamara checkout session
     */
    public function createCheckout(
        Payment $payment,
        array $studentInfo,
        string $returnUrl,
        string $cancelUrl
    ): array {
        $payload = [
            'order_reference_id' => 'PAY-' . $payment->id . '-' . time(),
            'total_amount' => [
                'amount' => (float) $payment->remaining_amount,
                'currency' => 'SAR',
            ],
            'description' => 'دفع رسوم البرنامج: ' . $payment->program->name,
            'country_code' => 'SA',
            'payment_type' => 'PAY_BY_INSTALMENTS',
            'locale' => 'ar_SA',
            'items' => [
                [
                    'reference_id' => 'PROG-' . $payment->program_id,
                    'type' => 'Digital',
                    'name' => $payment->program->name,
                    'sku' => $payment->program->code,
                    'quantity' => 1,
                    'total_amount' => [
                        'amount' => (float) $payment->remaining_amount,
                        'currency' => 'SAR',
                    ],
                ],
            ],
            'consumer' => [
                'first_name' => $studentInfo['first_name'] ?? $payment->user->name,
                'last_name' => $studentInfo['last_name'] ?? '',
                'phone_number' => $payment->user->phone ?? '',
                'email' => $payment->user->email,
            ],
            'billing_address' => [
                'city' => $studentInfo['city'] ?? 'Riyadh',
                'country_code' => 'SA',
                'line1' => $studentInfo['address'] ?? 'Address',
            ],
            'shipping_address' => [
                'city' => $studentInfo['city'] ?? 'Riyadh',
                'country_code' => 'SA',
                'line1' => $studentInfo['address'] ?? 'Address',
            ],
            'merchant_url' => [
                'success' => $returnUrl,
                'failure' => $cancelUrl,
                'cancel' => $cancelUrl,
                'notification' => route('webhooks.tamara'),
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/checkout', $payload);

            if ($response->successful()) {
                $data = $response->json();

                // Update payment with Tamara info
                $payment->update([
                    'tamara_checkout_id' => $data['checkout_id'] ?? null,
                    'tamara_order_id' => $data['order_id'] ?? null,
                    'tamara_metadata' => $data,
                    'payment_method' => 'tamara',
                ]);

                return $data;
            }

            Log::error('Tamara Checkout Error', ['response' => $response->json()]);
            throw new \Exception('Failed to create Tamara checkout: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Tamara Exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Capture payment
     */
    public function capturePayment(string $orderId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/orders/' . $orderId . '/capture');

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to capture payment');
        } catch (\Exception $e) {
            Log::error('Tamara Capture Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Cancel payment
     */
    public function cancelPayment(string $orderId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/orders/' . $orderId . '/cancel');

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to cancel payment');
        } catch (\Exception $e) {
            Log::error('Tamara Cancel Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $orderId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
            ])->get($this->apiUrl . '/orders/' . $orderId);

            if ($response->successful()) {
                return $response->json();
            }

            throw new \Exception('Failed to get payment status');
        } catch (\Exception $e) {
            Log::error('Tamara Status Error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Handle webhook
     */
    public function handleWebhook(array $payload): void
    {
        Log::info('Tamara Webhook Received', $payload);

        $eventType = $payload['event_type'] ?? null;
        $orderId = $payload['order_id'] ?? null;

        if (!$orderId) {
            return;
        }

        $payment = Payment::where('tamara_order_id', $orderId)->first();

        if (!$payment) {
            Log::warning('Payment not found for Tamara order', ['order_id' => $orderId]);
            return;
        }

        switch ($eventType) {
            case 'order_approved':
                $this->handleApproved($payment, $payload);
                break;
            case 'order_declined':
                $this->handleDeclined($payment, $payload);
                break;
            case 'order_expired':
                $this->handleExpired($payment, $payload);
                break;
            case 'order_canceled':
                $this->handleCanceled($payment, $payload);
                break;
        }
    }

    protected function handleApproved(Payment $payment, array $payload): void
    {
        $payment->update([
            'status' => 'completed',
            'completed_at' => now(),
            'tamara_metadata' => array_merge($payment->tamara_metadata ?? [], $payload),
        ]);

        // Capture the payment
        try {
            $this->capturePayment($payment->tamara_order_id);
        } catch (\Exception $e) {
            Log::error('Failed to capture Tamara payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function handleDeclined(Payment $payment, array $payload): void
    {
        $payment->update([
            'status' => 'cancelled',
            'tamara_metadata' => array_merge($payment->tamara_metadata ?? [], $payload),
            'notes' => ($payment->notes ?? '') . "\nTamara: Payment declined",
        ]);
    }

    protected function handleExpired(Payment $payment, array $payload): void
    {
        $payment->update([
            'status' => 'cancelled',
            'tamara_metadata' => array_merge($payment->tamara_metadata ?? [], $payload),
            'notes' => ($payment->notes ?? '') . "\nTamara: Payment expired",
        ]);
    }

    protected function handleCanceled(Payment $payment, array $payload): void
    {
        $payment->update([
            'status' => 'cancelled',
            'tamara_metadata' => array_merge($payment->tamara_metadata ?? [], $payload),
            'notes' => ($payment->notes ?? '') . "\nTamara: Payment canceled by user",
        ]);
    }

    /**
     * Verify webhook signature
     */
    public function verifyWebhookSignature(array $payload, string $signature): bool
    {
        $secret = config('services.tamara.webhook_secret');
        $calculatedSignature = hash_hmac('sha256', json_encode($payload), $secret);

        return hash_equals($calculatedSignature, $signature);
    }
}
