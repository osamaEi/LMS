<?php

namespace App\Http\Controllers\Student;

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
     * Display student's payments
     */
    public function index()
    {
        $user = auth()->user();

        // Get payment summary
        $summary = $this->paymentService->getStudentPaymentSummary($user->id);

        $payments = Payment::with(['program', 'installments', 'transactions'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $tamaraConfigured = $this->tamaraService->isConfigured();

        return view('student.payments.index', compact('payments', 'summary', 'tamaraConfigured'));
    }

    /**
     * Display payment details
     */
    public function show(Payment $payment)
    {
        // Ensure student can only view their own payments
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بعرض هذه الدفعة');
        }

        $payment->load(['program', 'installments', 'transactions']);

        return view('student.payments.show', compact('payment'));
    }

    /**
     * Initiate Tamara payment
     */
    public function payWithTamara(Payment $payment)
    {
        // Ensure student can only pay their own payments
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بدفع هذه الدفعة');
        }

        // Check if Tamara is configured
        if (!$this->tamaraService->isConfigured()) {
            return redirect()->route('student.payments.show', $payment)
                ->with('error', 'خدمة الدفع عبر تمارا غير متاحة حالياً');
        }

        if ($payment->isFullyPaid()) {
            return redirect()->route('student.payments.show', $payment)
                ->with('error', 'الدفعة مكتملة بالفعل');
        }

        if ($payment->isCancelled()) {
            return redirect()->route('student.payments.show', $payment)
                ->with('error', 'الدفعة ملغاة');
        }

        try {
            $user = auth()->user();

            // Prepare student info
            $studentInfo = [
                'first_name' => $user->name,
                'last_name' => '',
                'city' => 'Riyadh',
                'address' => 'Address',
            ];

            // Create Tamara checkout
            $checkoutData = $this->tamaraService->createCheckout(
                payment: $payment,
                studentInfo: $studentInfo,
                returnUrl: route('student.payments.tamara.return', ['payment_id' => $payment->id]),
                cancelUrl: route('student.payments.tamara.cancel', ['payment_id' => $payment->id])
            );

            // Redirect to Tamara checkout URL
            if (isset($checkoutData['checkout_url'])) {
                return redirect($checkoutData['checkout_url']);
            }

            return redirect()->back()->with('error', 'فشل إنشاء جلسة الدفع');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Handle Tamara return (success)
     */
    public function tamaraReturn(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $payment = Payment::findOrFail($paymentId);

        // Ensure student can only access their own payments
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        // Check payment status from Tamara
        try {
            if ($payment->tamara_order_id) {
                $status = $this->tamaraService->getPaymentStatus($payment->tamara_order_id);

                if ($status['status'] === 'approved') {
                    return redirect()->route('student.payments.show', $payment)
                        ->with('success', 'تم الدفع بنجاح! سيتم تأكيد الدفعة قريباً');
                }
            }
        } catch (\Exception $e) {
            // Log error
        }

        return redirect()->route('student.payments.show', $payment)
            ->with('info', 'جاري معالجة الدفعة...');
    }

    /**
     * Handle Tamara cancel
     */
    public function tamaraCancel(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $payment = Payment::findOrFail($paymentId);

        // Ensure student can only access their own payments
        if ($payment->user_id !== auth()->id()) {
            abort(403);
        }

        return redirect()->route('student.payments.show', $payment)
            ->with('warning', 'تم إلغاء عملية الدفع');
    }

    /**
     * Initiate PayTabs payment (Credit Card / Apple Pay)
     */
    public function payWithPayTabs(Payment $payment)
    {
        // Ensure student can only pay their own payments
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بدفع هذه الدفعة');
        }

        // Check if PayTabs is configured
        if (!config('services.paytabs.profile_id') || !config('services.paytabs.server_key')) {
            return redirect()->route('student.payments.show', $payment)
                ->with('error', 'خدمة الدفع عبر البطاقة غير متاحة حالياً');
        }

        if ($payment->isFullyPaid()) {
            return redirect()->route('student.payments.show', $payment)
                ->with('error', 'الدفعة مكتملة بالفعل');
        }

        if ($payment->isCancelled()) {
            return redirect()->route('student.payments.show', $payment)
                ->with('error', 'الدفعة ملغاة');
        }

        try {
            $user = auth()->user();

            // Prepare customer info
            $customerData = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
            ];

            // Create PayTabs payment page
            $result = $this->payTabsService->createPaymentPage($payment, $customerData);

            if ($result['success'] && isset($result['redirect_url'])) {
                // Update payment method
                $payment->update(['payment_method' => 'paytabs']);

                return redirect($result['redirect_url']);
            }

            return redirect()->back()->with('error', $result['message'] ?? 'فشل إنشاء صفحة الدفع');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    /**
     * Handle PayTabs callback (server-to-server)
     */
    public function payTabsCallback(Request $request)
    {
        $data = $request->all();

        $result = $this->payTabsService->processCallback($data);

        // Return response for PayTabs server
        return response()->json(['status' => $result['success'] ? 'success' : 'failed']);
    }

    /**
     * Handle PayTabs return (redirect back to site)
     */
    public function payTabsReturn(Request $request)
    {
        $data = $request->all();

        $result = $this->payTabsService->processCallback($data);

        if ($result['success'] && isset($result['payment'])) {
            return redirect()->route('student.payments.show', $result['payment'])
                ->with('success', 'تم الدفع بنجاح!');
        }

        // Try to find the payment from the request data
        $paymentId = null;
        $cartId = $data['cartId'] ?? $data['cart_id'] ?? null;
        if ($cartId) {
            $parts = explode('-', $cartId);
            if (count($parts) >= 2) {
                $paymentId = $parts[1];
            }
        }

        if ($paymentId) {
            $payment = Payment::find($paymentId);
            if ($payment) {
                return redirect()->route('student.payments.show', $payment)
                    ->with('error', $result['message'] ?? 'فشلت عملية الدفع');
            }
        }

        return redirect()->route('student.payments.index')
            ->with('error', $result['message'] ?? 'فشلت عملية الدفع');
    }
}
