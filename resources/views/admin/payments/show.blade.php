@extends('layouts.dashboard')

@section('title', 'تفاصيل الدفعة')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary me-3">
                    <i class="bi bi-arrow-right"></i>
                </a>
                <div>
                    <h2 class="mb-1">تفاصيل الدفعة #{{ $payment->id }}</h2>
                    <p class="text-muted mb-0">عرض التفاصيل الكاملة للدفعة</p>
                </div>
            </div>
            @if(!$payment->isCancelled() && !$payment->isFullyPaid())
                <div class="btn-group">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                        <i class="bi bi-cash-coin me-1"></i> تسجيل دفعة
                    </button>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#waiveModal">
                        <i class="bi bi-gift me-1"></i> إعفاء
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        <i class="bi bi-x-circle me-1"></i> إلغاء
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Payment Overview -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">ملخص الدفعة</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">الحالة</small>
                        <div>
                            @if($payment->status == 'pending')
                                <span class="badge bg-warning">قيد الانتظار</span>
                            @elseif($payment->status == 'partial')
                                <span class="badge bg-info">جزئية</span>
                            @elseif($payment->status == 'completed')
                                <span class="badge bg-success">مكتملة</span>
                            @elseif($payment->status == 'cancelled')
                                <span class="badge bg-danger">ملغاة</span>
                            @elseif($payment->status == 'refunded')
                                <span class="badge bg-secondary">مستردة</span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">إجمالي المبلغ:</span>
                            <strong>{{ number_format($payment->total_amount, 2) }} ر.س</strong>
                        </div>
                    </div>

                    @if($payment->discount_amount > 0)
                        <div class="mb-2">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">الخصم:</span>
                                <strong class="text-danger">- {{ number_format($payment->discount_amount, 2) }} ر.س</strong>
                            </div>
                        </div>
                    @endif

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">المدفوع:</span>
                            <strong class="text-success">{{ number_format($payment->paid_amount, 2) }} ر.س</strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">المتبقي:</span>
                            <strong class="text-primary">{{ number_format($payment->remaining_amount, 2) }} ر.س</strong>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    @php
                        $percentage = $payment->total_amount > 0 ? ($payment->paid_amount / $payment->total_amount) * 100 : 0;
                    @endphp
                    <div class="progress mb-2" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($percentage, 1) }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student & Program Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">معلومات الطالب والبرنامج</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">الطالب</small>
                        <div><strong>{{ $payment->user->name }}</strong></div>
                        <div><small>{{ $payment->user->email }}</small></div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">البرنامج</small>
                        <div><strong>{{ $payment->program->name_ar }}</strong></div>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted">نوع الدفع</small>
                        <div>
                            @if($payment->payment_type == 'full')
                                <span class="badge bg-info">دفعة كاملة</span>
                            @else
                                <span class="badge bg-secondary">تقسيط</span>
                            @endif
                        </div>
                    </div>

                    @if($payment->payment_method)
                        <div class="mb-3">
                            <small class="text-muted">طريقة الدفع</small>
                            <div>
                                @if($payment->payment_method == 'cash')
                                    نقدي
                                @elseif($payment->payment_method == 'bank_transfer')
                                    تحويل بنكي
                                @elseif($payment->payment_method == 'tamara')
                                    تمارا
                                @elseif($payment->payment_method == 'waived')
                                    معفي
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mb-0">
                        <small class="text-muted">تاريخ الإنشاء</small>
                        <div>{{ $payment->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($payment->notes)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">ملاحظات</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $payment->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-8">
            <!-- Installments -->
            @if($payment->payment_type == 'installment')
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">الأقساط</h5>
                        @if($payment->installments->count() == 0 && !$payment->isCancelled())
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createInstallmentModal">
                                <i class="bi bi-plus-circle me-1"></i> إنشاء خطة تقسيط
                            </button>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($payment->installments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>القسط</th>
                                            <th>المبلغ</th>
                                            <th>تاريخ الاستحقاق</th>
                                            <th>الحالة</th>
                                            <th>تاريخ الدفع</th>
                                            <th>الإجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payment->installments as $installment)
                                            <tr>
                                                <td>القسط #{{ $installment->installment_number }}</td>
                                                <td>{{ number_format($installment->amount, 2) }} ر.س</td>
                                                <td>{{ $installment->due_date->format('Y-m-d') }}</td>
                                                <td>
                                                    @if($installment->status == 'pending')
                                                        @if($installment->isOverdue())
                                                            <span class="badge bg-danger">متأخر</span>
                                                        @else
                                                            <span class="badge bg-warning">قيد الانتظار</span>
                                                        @endif
                                                    @elseif($installment->status == 'paid')
                                                        <span class="badge bg-success">مدفوع</span>
                                                    @elseif($installment->status == 'cancelled')
                                                        <span class="badge bg-secondary">ملغي</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($installment->paid_at)
                                                        {{ $installment->paid_at->format('Y-m-d') }}
                                                    @else
                                                        --
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($installment->status == 'pending')
                                                        <button type="button" class="btn btn-sm btn-success" onclick="recordInstallmentPayment({{ $installment->id }})">
                                                            <i class="bi bi-check-circle"></i> تسجيل
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">لم يتم إنشاء خطة تقسيط بعد</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Transactions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">سجل المعاملات</h5>
                </div>
                <div class="card-body">
                    @if($payment->transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>المبلغ</th>
                                        <th>النوع</th>
                                        <th>طريقة الدفع</th>
                                        <th>المرجع</th>
                                        <th>الحالة</th>
                                        <th>تم بواسطة</th>
                                        <th>التاريخ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment->transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>{{ number_format($transaction->amount, 2) }} ر.س</td>
                                            <td>
                                                @if($transaction->type == 'payment')
                                                    <span class="badge bg-success">دفعة</span>
                                                @elseif($transaction->type == 'refund')
                                                    <span class="badge bg-danger">استرداد</span>
                                                @elseif($transaction->type == 'adjustment')
                                                    <span class="badge bg-info">تعديل</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($transaction->payment_method == 'cash')
                                                    نقدي
                                                @elseif($transaction->payment_method == 'bank_transfer')
                                                    تحويل بنكي
                                                @elseif($transaction->payment_method == 'tamara')
                                                    تمارا
                                                @elseif($transaction->payment_method == 'waived')
                                                    معفي
                                                @endif
                                            </td>
                                            <td>{{ $transaction->transaction_reference ?? '--' }}</td>
                                            <td>
                                                @if($transaction->status == 'success')
                                                    <span class="badge bg-success">ناجح</span>
                                                @elseif($transaction->status == 'pending')
                                                    <span class="badge bg-warning">قيد المعالجة</span>
                                                @elseif($transaction->status == 'failed')
                                                    <span class="badge bg-danger">فشل</span>
                                                @endif
                                            </td>
                                            <td>{{ $transaction->creator->name ?? '--' }}</td>
                                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-receipt text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">لا توجد معاملات بعد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.record-payment', $payment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">تسجيل دفعة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" max="{{ $payment->remaining_amount }}" required>
                        <small class="text-muted">المتبقي: {{ number_format($payment->remaining_amount, 2) }} ر.س</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">طريقة الدفع <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">نقدي</option>
                            <option value="bank_transfer">تحويل بنكي</option>
                            <option value="waived">معفي</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">رقم المرجع/الإيصال</label>
                        <input type="text" name="transaction_reference" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">تسجيل الدفعة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Waive Modal -->
<div class="modal fade" id="waiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.waive', $payment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إعفاء من الدفع</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">مبلغ الإعفاء <span class="text-danger">*</span></label>
                        <input type="number" name="waive_amount" class="form-control" step="0.01" min="0.01" max="{{ $payment->remaining_amount }}" required>
                        <small class="text-muted">المتبقي: {{ number_format($payment->remaining_amount, 2) }} ر.س</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">السبب <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">إعفاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.cancel', $payment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إلغاء الدفعة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        هل أنت متأكد من إلغاء هذه الدفعة؟ سيتم إلغاء جميع الأقساط المعلقة.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">السبب <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Installment Plan Modal -->
@if($payment->payment_type == 'installment' && $payment->installments->count() == 0)
<div class="modal fade" id="createInstallmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.installment-plan', $payment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إنشاء خطة تقسيط</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">عدد الأقساط <span class="text-danger">*</span></label>
                        <input type="number" name="number_of_installments" class="form-control" min="2" max="12" value="3" required>
                        <small class="text-muted">من 2 إلى 12 قسط</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">تاريخ بداية التقسيط <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إنشاء خطة التقسيط</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Record Installment Payment Form (Hidden) -->
<form id="recordInstallmentForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="payment_method" value="cash">
</form>

@push('scripts')
<script>
    function recordInstallmentPayment(installmentId) {
        if (confirm('هل تريد تسجيل دفع هذا القسط؟')) {
            const form = document.getElementById('recordInstallmentForm');
            form.action = `/admin/payments/installments/${installmentId}/record-payment`;
            form.submit();
        }
    }
</script>
@endpush
@endsection
