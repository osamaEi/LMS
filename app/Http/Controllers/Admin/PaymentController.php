<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\PaymentInstallment;
use App\Models\Program;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'program', 'creator']);

        // Filters
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('program_id')) {
            $query->where('program_id', $request->program_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        $students = User::where('role', 'student')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $programs = Program::where('status', 'active')->orderBy('name_ar')->get();

        return view('admin.payments.index', compact('payments', 'students', 'programs'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create()
    {
        $students = User::where('role', 'student')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $programs = Program::where('status', 'active')->orderBy('name_ar')->get();

        return view('admin.payments.create', compact('students', 'programs'));
    }

    /**
     * Store a newly created payment
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'program_id' => 'required|exists:programs,id',
            'payment_type' => 'required|in:full,installment',
            'discount_amount' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,bank_transfer,tamara,waived',
            'notes' => 'nullable|string',
            'number_of_installments' => 'required_if:payment_type,installment|integer|min:2|max:12',
            'installment_start_date' => 'required_if:payment_type,installment|date',
        ]);

        // Check if student already has payment for this program
        $existingPayment = Payment::where('user_id', $request->user_id)
            ->where('program_id', $request->program_id)
            ->whereIn('status', ['pending', 'partial'])
            ->first();

        if ($existingPayment) {
            return redirect()->back()
                ->with('error', 'الطالب لديه دفعة قائمة بالفعل لهذا البرنامج')
                ->withInput();
        }

        // Create payment
        $payment = $this->paymentService->createPayment(
            userId: $request->user_id,
            programId: $request->program_id,
            paymentType: $request->payment_type,
            options: [
                'discount_amount' => $request->discount_amount ?? 0,
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
            ]
        );

        // Create installment plan if needed
        if ($request->payment_type === 'installment') {
            $this->paymentService->createInstallmentPlan(
                paymentId: $payment->id,
                numberOfInstallments: $request->number_of_installments,
                startDate: Carbon::parse($request->installment_start_date)
            );
        }

        return redirect()->route('admin.payments.show', $payment)
            ->with('success', 'تم إنشاء الدفعة بنجاح');
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        $payment->load(['user', 'program', 'creator', 'installments.paidBy', 'transactions.creator']);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Create installment plan for existing payment
     */
    public function createInstallmentPlan(Request $request, Payment $payment)
    {
        $request->validate([
            'number_of_installments' => 'required|integer|min:2|max:12',
            'start_date' => 'required|date',
        ]);

        if ($payment->installments()->count() > 0) {
            return redirect()->back()->with('error', 'الدفعة لديها خطة تقسيط بالفعل');
        }

        $this->paymentService->createInstallmentPlan(
            paymentId: $payment->id,
            numberOfInstallments: $request->number_of_installments,
            startDate: Carbon::parse($request->start_date)
        );

        return redirect()->back()->with('success', 'تم إنشاء خطة التقسيط بنجاح');
    }

    /**
     * Record a manual payment
     */
    public function recordPayment(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,waived',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($payment->isFullyPaid()) {
            return redirect()->back()->with('error', 'الدفعة مكتملة بالفعل');
        }

        if ($request->amount > $payment->remaining_amount) {
            return redirect()->back()
                ->with('error', 'المبلغ أكبر من المبلغ المتبقي')
                ->withInput();
        }

        $this->paymentService->recordManualPayment(
            paymentId: $payment->id,
            amount: $request->amount,
            method: $request->payment_method,
            reference: $request->transaction_reference,
            adminId: auth()->id()
        );

        return redirect()->back()->with('success', 'تم تسجيل الدفعة بنجاح');
    }

    /**
     * Record installment payment
     */
    public function recordInstallmentPayment(Request $request, PaymentInstallment $installment)
    {
        $request->validate([
            'payment_method' => 'required|in:cash,bank_transfer,waived',
            'transaction_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if ($installment->isPaid()) {
            return redirect()->back()->with('error', 'القسط مدفوع بالفعل');
        }

        $this->paymentService->recordInstallmentPayment(
            installmentId: $installment->id,
            method: $request->payment_method,
            reference: $request->transaction_reference,
            adminId: auth()->id()
        );

        return redirect()->back()->with('success', 'تم تسجيل دفع القسط بنجاح');
    }

    /**
     * Waive payment
     */
    public function waive(Request $request, Payment $payment)
    {
        $request->validate([
            'waive_amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string',
        ]);

        if ($request->waive_amount > $payment->remaining_amount) {
            return redirect()->back()
                ->with('error', 'مبلغ الإعفاء أكبر من المبلغ المتبقي')
                ->withInput();
        }

        $this->paymentService->waivePayment(
            paymentId: $payment->id,
            waiveAmount: $request->waive_amount,
            reason: $request->reason,
            adminId: auth()->id()
        );

        return redirect()->back()->with('success', 'تم إعفاء الطالب بنجاح');
    }

    /**
     * Cancel payment
     */
    public function cancel(Request $request, Payment $payment)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        if ($payment->isCancelled()) {
            return redirect()->back()->with('error', 'الدفعة ملغاة بالفعل');
        }

        $this->paymentService->cancelPayment(
            paymentId: $payment->id,
            reason: $request->reason,
            adminId: auth()->id()
        );

        return redirect()->route('admin.payments.index')
            ->with('success', 'تم إلغاء الدفعة بنجاح');
    }

    /**
     * Update payment amount / notes
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'total_amount'    => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'reason'          => 'nullable|string',
            'notes'           => 'nullable|string',
        ]);

        $data = [];

        if ($request->filled('total_amount')) {
            $data['total_amount'] = $request->total_amount;
            $data['discount_amount'] = $request->discount_amount ?? $payment->discount_amount;
            $netAmount = $data['total_amount'] - $data['discount_amount'];
            $data['remaining_amount'] = max(0, $netAmount - $payment->paid_amount);
        }

        if ($request->has('notes')) {
            $data['notes'] = $request->notes;
        }

        if (!empty($data)) {
            $payment->update($data);
        }

        return redirect()->back()->with('success', 'تم تحديث بيانات الدفعة بنجاح');
    }

    /**
     * Refund a completed payment
     */
    public function refund(Request $request, Payment $payment)
    {
        $request->validate([
            'refund_method' => 'required|in:cash,bank_transfer,original_method',
            'reason'        => 'required|string',
        ]);

        if (!$payment->isFullyPaid()) {
            return redirect()->back()->with('error', 'يمكن استرداد الدفعات المكتملة فقط');
        }

        $payment->update([
            'status'          => 'cancelled',
            'remaining_amount' => 0,
            'notes'           => ($payment->notes ? $payment->notes . "\n" : '')
                                 . 'استرداد: ' . $request->reason
                                 . ' | طريقة الاسترداد: ' . $request->refund_method
                                 . ' | بواسطة: ' . auth()->user()->name
                                 . ' | ' . now()->format('Y-m-d H:i'),
        ]);

        // Log refund transaction
        $payment->transactions()->create([
            'amount'         => -$payment->paid_amount,
            'payment_method' => $request->refund_method === 'original_method' ? $payment->payment_method : $request->refund_method,
            'type'           => 'refund',
            'status'         => 'refunded',
            'notes'          => $request->reason,
            'created_by'     => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'تم تسجيل الاسترداد بنجاح');
    }

    /**
     * Display overdue installments
     */
    public function overdueInstallments(Request $request)
    {
        $query = PaymentInstallment::with(['payment.user', 'payment.program'])
            ->overdue();

        if ($request->filled('user_id')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }

        if ($request->filled('program_id')) {
            $query->whereHas('payment', function ($q) use ($request) {
                $q->where('program_id', $request->program_id);
            });
        }

        $installments = $query->orderBy('due_date', 'asc')->paginate(15);

        $students = User::where('role', 'student')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        $programs = Program::where('status', 'active')->orderBy('name_ar')->get();

        return view('admin.payments.overdue', compact('installments', 'students', 'programs'));
    }
}
