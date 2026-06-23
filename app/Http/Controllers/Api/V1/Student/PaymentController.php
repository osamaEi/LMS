<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\PaymentTransaction;
use App\Services\PaymentService;
use App\Services\TamaraPaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;
    protected TamaraPaymentService $tamaraService;

    public function __construct(
        PaymentService $paymentService,
        TamaraPaymentService $tamaraService
    ) {
        $this->paymentService = $paymentService;
        $this->tamaraService = $tamaraService;
    }

    /**
     * GET /api/v1/student/payments
     * List student's payments with summary
     */
    public function index()
    {
        $user = auth()->user();

        $payments = Payment::with(['program', 'installments', 'transactions'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => PaymentResource::collection($payments),
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
            'data' => new PaymentResource($payment),
        ]);
    }

    /**
     * POST /api/v1/student/payments/{id}/pay-with-receipt
     * Submit bank transfer receipt with chosen amount
     */
    public function payWithReceipt(Request $request, $id)
    {
        $user = auth()->user();

        $payment = Payment::where('user_id', $user->id)->findOrFail($id);

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

        $request->validate([
            'amount'  => 'required|numeric|min:1',
            'receipt' => 'nullable',
            'notes'   => 'nullable|string|max:500',
        ]);

        $amount = (float) $request->input('amount');

        // Clamp to remaining amount
        $remaining = (float) $payment->remaining_amount;
        if ($amount > $remaining) {
            return response()->json([
                'success' => false,
                'message' => "المبلغ المدخل ({$amount}) أكبر من المتبقي ({$remaining})",
            ], 422);
        }

        // Handle receipt: file upload or string URL
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')
                ->store('payment-receipts/' . $payment->id, 'public');
        } elseif ($request->filled('receipt')) {
            $receiptPath = $request->input('receipt');
        }

        $transaction = PaymentTransaction::create([
            'payment_id'     => $payment->id,
            'amount'         => $amount,
            'type'           => 'payment',
            'payment_method' => 'bank_transfer',
            'status'         => 'pending',
            'receipt_path'   => $receiptPath,
            'receipt_status' => 'pending',
            'notes'          => $request->input('notes'),
            'created_by'     => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال إيصال التحويل البنكي بنجاح. سيتم مراجعته من قِبل الإدارة.',
            'data'    => [
                'transaction_id'  => $transaction->id,
                'amount'          => $amount,
                'receipt_status'  => 'pending',
                'receipt_url'     => $receiptPath
                    ? (filter_var($receiptPath, FILTER_VALIDATE_URL)
                        ? $receiptPath
                        : asset('storage/' . $receiptPath))
                    : null,
            ],
        ], 201);
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

}
