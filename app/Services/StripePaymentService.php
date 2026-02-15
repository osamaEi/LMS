<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentTransaction;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripePaymentService
{
    public function __construct()
    {
        if ($this->isConfigured()) {
            Stripe::setApiKey(config('services.stripe.secret_key'));
        }
    }

    public function isConfigured(): bool
    {
        return !empty(config('services.stripe.secret_key'))
            && !empty(config('services.stripe.publishable_key'));
    }

    /**
     * Create a Stripe Checkout Session for a payment
     */
    public function createCheckoutSession(
        Payment $payment,
        string $successUrl,
        string $cancelUrl
    ): array {
        try {
            $program = $payment->program;
            $currency = strtolower(config('services.stripe.currency', 'SAR'));

            $session = StripeSession::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => $program->name_ar ?? $program->name_en ?? 'برنامج دراسي',
                            'description' => $program->description_ar ?? $program->description_en ?? '',
                        ],
                        'unit_amount' => (int) ($payment->remaining_amount * 100), // Stripe uses cents/halalas
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $successUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $cancelUrl . '?session_id={CHECKOUT_SESSION_ID}',
                'customer_email' => $payment->user->email,
                'metadata' => [
                    'payment_id' => $payment->id,
                    'user_id' => $payment->user_id,
                    'program_id' => $payment->program_id,
                ],
            ]);

            // Save session ID to payment
            $payment->update([
                'stripe_session_id' => $session->id,
                'payment_method' => 'stripe',
            ]);

            return [
                'success' => true,
                'checkout_url' => $session->url,
                'session_id' => $session->id,
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe Checkout Error: ' . $e->getMessage(), [
                'payment_id' => $payment->id,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify a Stripe Checkout Session and process payment
     */
    public function verifySession(string $sessionId): array
    {
        try {
            $session = StripeSession::retrieve($sessionId);

            $payment = Payment::where('stripe_session_id', $sessionId)->first();

            if (!$payment) {
                return [
                    'success' => false,
                    'message' => 'لم يتم العثور على الدفعة',
                ];
            }

            if ($session->payment_status === 'paid') {
                // Update payment intent ID
                $payment->update([
                    'stripe_payment_intent_id' => $session->payment_intent,
                ]);

                // Create transaction record
                PaymentTransaction::create([
                    'payment_id' => $payment->id,
                    'amount' => $payment->remaining_amount,
                    'type' => 'payment',
                    'payment_method' => 'stripe',
                    'transaction_reference' => $session->payment_intent,
                    'status' => 'success',
                    'metadata' => [
                        'stripe_session_id' => $sessionId,
                        'stripe_payment_intent' => $session->payment_intent,
                        'stripe_customer_email' => $session->customer_email,
                    ],
                ]);

                // Update payment amounts and status
                $payment->updatePaidAmount();

                return [
                    'success' => true,
                    'payment' => $payment,
                    'status' => 'paid',
                ];
            }

            return [
                'success' => false,
                'payment' => $payment,
                'status' => $session->payment_status,
                'message' => 'الدفعة لم تكتمل بعد',
            ];

        } catch (ApiErrorException $e) {
            Log::error('Stripe Verify Error: ' . $e->getMessage(), [
                'session_id' => $sessionId,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
