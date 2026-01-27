@extends('layouts.dashboard')

@section('title', 'تفاصيل الدفعة')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('student.payments.index') }}" class="btn btn-outline-secondary me-3">
                <i class="bi bi-arrow-right"></i>
            </a>
            <div>
                <h2 class="mb-1">تفاصيل الدفعة</h2>
                <p class="text-muted mb-0">{{ $payment->program->name_ar }}</p>
            </div>
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
        <!-- Payment Summary -->
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
                                <strong class="text-success">- {{ number_format($payment->discount_amount, 2) }} ر.س</strong>
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
                    <div class="progress mb-2" style="height: 25px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%">
                            {{ number_format($percentage, 1) }}%
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Actions -->
            @if(!$payment->isFullyPaid() && !$payment->isCancelled())
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">خيارات الدفع</h5>
                    </div>
                    <div class="card-body">
                        @if($payment->payment_type == 'installment' && $payment->installments->count() > 0)
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-1"></i>
                                <small>لديك خطة تقسيط. يرجى مراجعة الأقساط أدناه للدفع.</small>
                            </div>
                        @else
                            <form action="{{ route('student.payments.pay-tamara', $payment) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100 mb-3">
                                    <i class="bi bi-credit-card me-1"></i>
                                    الدفع عبر تمارا (بالتقسيط)
                                </button>
                            </form>

                            <div class="alert alert-warning">
                                <small>
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    يمكنك الدفع نقداً أو عبر التحويل البنكي بالتواصل مع الإدارة
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Program Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">معلومات البرنامج</h5>
                </div>
                <div class="card-body">
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

                    <div class="mb-0">
                        <small class="text-muted">تاريخ الإنشاء</small>
                        <div>{{ $payment->created_at->format('Y-m-d') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- Installments -->
            @if($payment->payment_type == 'installment' && $payment->installments->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">الأقساط</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>القسط</th>
                                        <th>المبلغ</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الدفع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment->installments as $installment)
                                        <tr class="{{ $installment->isOverdue() ? 'table-danger' : '' }}">
                                            <td><strong>القسط #{{ $installment->installment_number }}</strong></td>
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
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @php
                            $overdueCount = $payment->installments->filter(fn($i) => $i->isOverdue())->count();
                            $pendingCount = $payment->installments->filter(fn($i) => $i->status == 'pending' && !$i->isOverdue())->count();
                            $paidCount = $payment->installments->filter(fn($i) => $i->status == 'paid')->count();
                        @endphp

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="text-center p-2 border rounded">
                                    <h4 class="text-success mb-0">{{ $paidCount }}</h4>
                                    <small class="text-muted">أقساط مدفوعة</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-2 border rounded">
                                    <h4 class="text-warning mb-0">{{ $pendingCount }}</h4>
                                    <small class="text-muted">أقساط قادمة</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-2 border rounded">
                                    <h4 class="text-danger mb-0">{{ $overdueCount }}</h4>
                                    <small class="text-muted">أقساط متأخرة</small>
                                </div>
                            </div>
                        </div>

                        @if($overdueCount > 0)
                            <div class="alert alert-danger mt-3">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                لديك {{ $overdueCount }} قسط متأخر. يرجى التواصل مع الإدارة للدفع في أقرب وقت.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Transactions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">سجل المدفوعات</h5>
                </div>
                <div class="card-body">
                    @if($payment->transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>التاريخ</th>
                                        <th>المبلغ</th>
                                        <th>النوع</th>
                                        <th>طريقة الدفع</th>
                                        <th>المرجع</th>
                                        <th>الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment->transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
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

            @if($payment->notes)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">ملاحظات</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $payment->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
