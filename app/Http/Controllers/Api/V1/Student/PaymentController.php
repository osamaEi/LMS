<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\TamaraPaymentService;
use App\Services\PayTabsService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected TamaraPaymentService $tamaraService;
    protected PayTabsService $payTabsService;

    public function __construct(
        PaymentService $paymentService,
        TamaraPaymentService $tamaraService,
        PayTabsService $payTabsService
    ) {
        $this->paymentService = $paymentService;
        $this->tamaraService = $tamaraService;
        $this->payTabsService = $payTabsService;
    }

    /**
     * GET /api/v1/student/payments
     * List student's payments with summary
     */
    public function index()
    {
        $user = auth()->user();

        $summary = $this->paymentService->getStudentPaymentSummary($user->id);

        $payments = Payment::with(['program', 'installments', 'transactions'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'payments' => $payments,
                'summary' => $summary,
                'tamara_configured' => $this->tamaraService->isConfigured(),
                'paytabs_configured' => !empty(config('services.paytabs.profile_id')),
            ],
        ]);
    }

    /**
     * GET /api/v1/student/payments/{id}
     * Show payment details
     */
    public function show($id)
    {
        $user = auth()->user();

        $payment = Payment::with(['program', 'installments', 'transactions'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $payment,
        ]);
    }

    /**
     * POST /api/v1/student/payments/{id}/pay-with-tamara
     * Initiate Tamara payment
     */
    public function payWithTamara($id)
    {
        $user = auth()->user();

        $payment = Payment::where('user_id', $user->id)->findOrFail($id);

        if (!$this->tamaraService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'خدمة الدفع عبر تمارا غير متاحة حالياً',
            ], 400);
        }

        if ($payment->isFullyPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'الدفعة مكتملة بالفعل',
            ], 422);
        }

        if ($payment->isCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'الدفعة ملغاة',
            ], 422);
        }

        try {
            $studentInfo = [
                'first_name' => $user->name,
                'last_name' => '',
                'city' => 'Riyadh',
                'address' => 'Address',
            ];

            $checkoutData = $this->tamaraService->createCheckout(
                payment: $payment,
                studentInfo: $studentInfo,
                returnUrl: config('app.url') . '/api/v1/student/payments/tamara/return?payment_id=' . $payment->id,
                cancelUrl: config('app.url') . '/api/v1/student/payments/tamara/cancel?payment_id=' . $payment->id
            );

            if (isset($checkoutData['checkout_url'])) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'checkout_url' => $checkoutData['checkout_url'],
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'فشل إنشاء جلسة الدفع',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * POST /api/v1/student/payments/{id}/pay-with-paytabs
     * Initiate PayTabs payment
     */
    public function payWithPayTabs($id)
    {
        $user = auth()->user();

        $payment = Payment::where('user_id', $user->id)->findOrFail($id);

        if (!config('services.paytabs.profile_id') || !config('services.paytabs.server_key')) {
            return response()->json([
                'success' => false,
                'message' => 'خدمة الدفع عبر البطاقة غير متاحة حالياً',
            ], 400);
        }

        if ($payment->isFullyPaid()) {
            return response()->json([
                'success' => false,
                'message' => 'الدفعة مكتملة بالفعل',
            ], 422);
        }

        if ($payment->isCancelled()) {
            return response()->json([
                'success' => false,
                'message' => 'الدفعة ملغاة',
            ], 422);
        }

        try {
            $customerData = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
            ];

            $result = $this->payTabsService->createPaymentPage($payment, $customerData);

            if ($result['success'] && isset($result['redirect_url'])) {
                $payment->update(['payment_method' => 'paytabs']);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'redirect_url' => $result['redirect_url'],
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'فشل إنشاء صفحة الدفع',
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage(),
            ], 500);
        }
    }
}
