<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayTabsService
{
    protected string $profileId;
    protected string $serverKey;
    protected string $currency;
    protected string $region;
    protected string $baseUrl;

    public function __construct()
    {
        $this->profileId = config('services.paytabs.profile_id');
        $this->serverKey = config('services.paytabs.server_key');
        $this->currency = config('services.paytabs.currency', 'SAR');
        $this->region = config('services.paytabs.region', 'SAU');
        $this->baseUrl = $this->getBaseUrl();
    }

    protected function getBaseUrl(): string
    {
        return match($this->region) {
            'SAU' => 'https://secure.paytabs.sa',
            'ARE' => 'https://secure.paytabs.com',
            'EGY' => 'https://secure-egypt.paytabs.com',
            'OMN' => 'https://secure-oman.paytabs.com',
            'JOR' => 'https://secure-jordan.paytabs.com',
            'GLOBAL' => 'https://secure-global.paytabs.com',
            default => 'https://secure.paytabs.sa',
        };
    }

    /**
     * Create a payment page
     */
    public function createPaymentPage(Payment $payment, array $customerData = []): array
    {
        $user = $payment->user;
        $program = $payment->program;

        $cartId = 'PAY-' . $payment->id . '-' . time();
        $cartDescription = 'دفع رسوم برنامج: ' . ($program->name ?? 'غير محدد');
        $amount = $payment->remaining_amount > 0 ? $payment->remaining_amount : $payment->total_amount;

        $payload = [
            'profile_id' => (int) $this->profileId,
            'tran_type' => 'sale',
            'tran_class' => 'ecom',
            'cart_id' => $cartId,
            'cart_currency' => $this->currency,
            'cart_amount' => (float) $amount,
            'cart_description' => $cartDescription,
            'paypage_lang' => 'ar',
            'customer_details' => [
                'name' => $customerData['name'] ?? $user->name,
                'email' => $customerData['email'] ?? $user->email,
                'phone' => $customerData['phone'] ?? $user->phone ?? '',
                'street1' => $customerData['address'] ?? 'Saudi Arabia',
                'city' => $customerData['city'] ?? 'Riyadh',
                'state' => $customerData['state'] ?? 'Riyadh',
                'country' => 'SA',
                'zip' => $customerData['zip'] ?? '12345',
            ],
            'shipping_details' => [
                'name' => $customerData['name'] ?? $user->name,
                'email' => $customerData['email'] ?? $user->email,
                'phone' => $customerData['phone'] ?? $user->phone ?? '',
                'street1' => $customerData['address'] ?? 'Saudi Arabia',
                'city' => $customerData['city'] ?? 'Riyadh',
                'state' => $customerData['state'] ?? 'Riyadh',
                'country' => 'SA',
                'zip' => $customerData['zip'] ?? '12345',
            ],
            'callback' => route('webhooks.paytabs'),
            'return' => route('student.payments.paytabs.return'),
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payment/request', $payload);

            $result = $response->json();

            if (isset($result['redirect_url'])) {
                // Store transaction reference
                $payment->update([
                    'paytabs_tran_ref' => $result['tran_ref'] ?? null,
                    'paytabs_cart_id' => $cartId,
                ]);

                return [
                    'success' => true,
                    'redirect_url' => $result['redirect_url'],
                    'tran_ref' => $result['tran_ref'] ?? null,
                ];
            }

            Log::error('PayTabs Error', ['response' => $result, 'payload' => $payload]);

            return [
                'success' => false,
                'message' => $result['message'] ?? 'حدث خطأ أثناء إنشاء صفحة الدفع',
            ];

        } catch (\Exception $e) {
            Log::error('PayTabs Exception', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'حدث خطأ في الاتصال ببوابة الدفع',
            ];
        }
    }

    /**
     * Verify a transaction
     */
    public function verifyTransaction(string $tranRef): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payment/query', [
                'profile_id' => (int) $this->profileId,
                'tran_ref' => $tranRef,
            ]);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('PayTabs Verify Exception', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء التحقق من الدفع',
            ];
        }
    }

    /**
     * Process callback/return from PayTabs
     */
    public function processCallback(array $data): array
    {
        $tranRef = $data['tranRef'] ?? $data['tran_ref'] ?? null;
        $cartId = $data['cartId'] ?? $data['cart_id'] ?? null;

        if (!$tranRef) {
            return [
                'success' => false,
                'message' => 'مرجع المعاملة غير موجود',
            ];
        }

        // Verify the transaction
        $verification = $this->verifyTransaction($tranRef);

        if (!isset($verification['payment_result'])) {
            return [
                'success' => false,
                'message' => 'فشل التحقق من المعاملة',
            ];
        }

        $paymentResult = $verification['payment_result'];
        $responseStatus = $paymentResult['response_status'] ?? '';

        // Extract payment ID from cart_id (format: PAY-{id}-{timestamp})
        $paymentId = null;
        if ($cartId) {
            $parts = explode('-', $cartId);
            if (count($parts) >= 2) {
                $paymentId = $parts[1];
            }
        }

        $payment = $paymentId ? Payment::find($paymentId) : null;

        if (!$payment) {
            // Try to find by tran_ref
            $payment = Payment::where('paytabs_tran_ref', $tranRef)->first();
        }

        if (!$payment) {
            return [
                'success' => false,
                'message' => 'لم يتم العثور على الدفعة',
            ];
        }

        // Record transaction
        $transactionStatus = match($responseStatus) {
            'A' => 'success',
            'H' => 'pending',
            'P' => 'pending',
            'V' => 'cancelled',
            'E' => 'failed',
            'D' => 'failed',
            default => 'failed',
        };

        PaymentTransaction::create([
            'payment_id' => $payment->id,
            'type' => 'payment',
            'amount' => $verification['cart_amount'] ?? $verification['tran_total'] ?? 0,
            'payment_method' => 'paytabs',
            'transaction_reference' => $tranRef,
            'transaction_id' => $tranRef,
            'status' => $transactionStatus,
            'response_code' => $paymentResult['response_code'] ?? null,
            'response_message' => $paymentResult['response_message'] ?? null,
            'metadata' => $verification,
        ]);

        if ($responseStatus === 'A') {
            // Approved
            $payment->updatePaidAmount();

            return [
                'success' => true,
                'message' => 'تم الدفع بنجاح',
                'payment' => $payment,
            ];
        }

        return [
            'success' => false,
            'message' => $paymentResult['response_message'] ?? 'فشلت عملية الدفع',
            'payment' => $payment,
        ];
    }

    /**
     * Refund a transaction
     */
    public function refund(string $tranRef, float $amount, string $cartId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/payment/request', [
                'profile_id' => (int) $this->profileId,
                'tran_type' => 'refund',
                'tran_class' => 'ecom',
                'cart_id' => $cartId . '-refund-' . time(),
                'cart_currency' => $this->currency,
                'cart_amount' => $amount,
                'cart_description' => 'استرداد للمعاملة: ' . $tranRef,
                'tran_ref' => $tranRef,
            ]);

            return $response->json();

        } catch (\Exception $e) {
            Log::error('PayTabs Refund Exception', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء الاسترداد',
            ];
        }
    }
}
