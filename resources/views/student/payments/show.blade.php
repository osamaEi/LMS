@extends('layouts.dashboard')

@section('title', 'تفاصيل الدفعة')

@section('content')
<div class="min-h-screen py-6" style="background: linear-gradient(135deg, #f0f9ff, #e0f2fe);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('student.payments.index') }}"
                   class="p-3 rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-105" style="background: #3b82f6;">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold" style="color: #1e40af;">تفاصيل الدفعة</h1>
                    <p class="mt-1 font-medium" style="color: #3b82f6;">{{ $payment->program->name_ar }}</p>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
        <div class="mb-6 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-4" style="background: #22c55e;">
            <div class="p-2 rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-4" style="background: #f97316;">
            <div class="p-2 rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="font-semibold">{{ session('error') }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Payment Summary Card -->
                <div class="rounded-2xl shadow-xl overflow-hidden" style="background: #3b82f6;">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-white">ملخص الدفعة</h3>
                            @if($payment->status == 'pending')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: #f97316; color: white;">قيد الانتظار</span>
                            @elseif($payment->status == 'partial')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: #60a5fa; color: white;">جزئية</span>
                            @elseif($payment->status == 'completed')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: #22c55e; color: white;">مكتملة</span>
                            @elseif($payment->status == 'cancelled')
                                <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background: #ef4444; color: white;">ملغاة</span>
                            @endif
                        </div>

                        <!-- Progress Circle -->
                        @php
                            $percentage = $payment->total_amount > 0 ? ($payment->paid_amount / $payment->total_amount) * 100 : 0;
                        @endphp
                        <div class="flex justify-center mb-6">
                            <div class="relative w-40 h-40">
                                <svg class="w-full h-full transform -rotate-90">
                                    <circle cx="80" cy="80" r="70" stroke="rgba(255,255,255,0.2)" stroke-width="12" fill="none"/>
                                    <circle cx="80" cy="80" r="70" stroke="white" stroke-width="12" fill="none"
                                            stroke-dasharray="{{ 2 * pi() * 70 }}"
                                            stroke-dashoffset="{{ 2 * pi() * 70 * (1 - $percentage / 100) }}"
                                            class="transition-all duration-1000"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="text-center">
                                        <div class="text-3xl font-bold text-white">{{ number_format($percentage, 0) }}%</div>
                                        <div class="text-xs text-white/80">مكتمل</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amounts -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 rounded-xl" style="background: rgba(255,255,255,0.15);">
                                <span class="text-white/80 text-sm">إجمالي المبلغ</span>
                                <span class="text-white font-bold">{{ number_format($payment->total_amount, 2) }} ر.س</span>
                            </div>

                            @if($payment->discount_amount > 0)
                            <div class="flex items-center justify-between p-3 rounded-xl" style="background: rgba(255,255,255,0.15);">
                                <span class="text-white/80 text-sm">الخصم</span>
                                <span class="font-bold" style="color: #86efac;">- {{ number_format($payment->discount_amount, 2) }} ر.س</span>
                            </div>
                            @endif

                            <div class="flex items-center justify-between p-3 rounded-xl" style="background: rgba(255,255,255,0.15);">
                                <span class="text-white/80 text-sm">المدفوع</span>
                                <span class="font-bold" style="color: #86efac;">{{ number_format($payment->paid_amount, 2) }} ر.س</span>
                            </div>

                            <div class="flex items-center justify-between p-3 rounded-xl border-2" style="background: rgba(255,255,255,0.2); border-color: rgba(255,255,255,0.4);">
                                <span class="text-white font-medium">المتبقي</span>
                                <span class="text-2xl font-bold text-white">{{ number_format($payment->remaining_amount, 2) }} ر.س</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Actions -->
                @if(!$payment->isFullyPaid() && !$payment->isCancelled())
                <div class="rounded-2xl shadow-xl overflow-hidden bg-white">
                    <div class="px-6 py-4" style="background: #22c55e;">
                        <h3 class="font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            خيارات الدفع
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if($payment->payment_type == 'installment' && $payment->installments->count() > 0)
                            <div class="p-4 rounded-xl" style="background: #fef3c7; border: 1px solid #f97316;">
                                <div class="flex gap-3">
                                    <svg class="w-5 h-5 flex-shrink-0" style="color: #f97316;" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    <p class="text-sm" style="color: #92400e;">لديك خطة تقسيط. يرجى مراجعة الأقساط أدناه للدفع.</p>
                                </div>
                            </div>
                        @else
                            <!-- Stripe -->
                            <form action="{{ route('student.payments.pay-stripe', $payment) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-3 px-6 py-4 font-bold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-105" style="background: #635bff; color: white;">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    الدفع عبر Stripe
                                </button>
                            </form>

                            <div class="p-4 rounded-xl" style="background: #dbeafe;">
                                <p class="text-xs" style="color: #1e40af;">
                                    <svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    يمكنك أيضاً الدفع نقداً أو عبر التحويل البنكي بالتواصل مع الإدارة
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Program Info -->
                <div class="rounded-2xl shadow-xl overflow-hidden bg-white">
                    <div class="px-6 py-4" style="background: #3b82f6;">
                        <h3 class="font-bold text-white flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            معلومات البرنامج
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="p-3 rounded-xl" style="background: #f0f9ff;">
                            <p class="text-xs mb-1" style="color: #64748b;">البرنامج</p>
                            <p class="font-bold" style="color: #1e40af;">{{ $payment->program->name_ar }}</p>
                        </div>

                        <div class="p-3 rounded-xl" style="background: #f0f9ff;">
                            <p class="text-xs mb-1" style="color: #64748b;">نوع الدفع</p>
                            @if($payment->payment_type == 'full')
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold shadow-sm" style="background: #22c55e; color: white;">دفعة كاملة</span>
                            @else
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold shadow-sm" style="background: #3b82f6; color: white;">تقسيط</span>
                            @endif
                        </div>

                        <div class="p-3 rounded-xl" style="background: #f0f9ff;">
                            <p class="text-xs mb-1" style="color: #64748b;">تاريخ الإنشاء</p>
                            <p class="text-sm font-semibold" style="color: #1e40af;">{{ $payment->created_at->format('Y-m-d') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Installments -->
                @if($payment->payment_type == 'installment' && $payment->installments->count() > 0)
                <div class="rounded-2xl shadow-xl border-2 overflow-hidden bg-white" style="border-color: #93c5fd;">
                    <div class="px-8 py-6" style="background: #3b82f6;">
                        <h2 class="text-xl font-bold text-white flex items-center gap-3">
                            <div class="p-2.5 rounded-xl" style="background: rgba(255,255,255,0.2);">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            الأقساط
                        </h2>
                        <p class="text-sm text-white/80 mt-1 mr-12">خطة الدفع بالتقسيط</p>
                    </div>

                    <div class="p-8">
                        <!-- Statistics -->
                        @php
                            $overdueCount = $payment->installments->filter(fn($i) => $i->isOverdue())->count();
                            $pendingCount = $payment->installments->filter(fn($i) => $i->status == 'pending' && !$i->isOverdue())->count();
                            $paidCount = $payment->installments->filter(fn($i) => $i->status == 'paid')->count();
                        @endphp

                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="relative overflow-hidden text-center p-5 rounded-2xl shadow-lg" style="background: #22c55e;">
                                <div class="absolute top-0 right-0 w-16 h-16 rounded-full -mr-8 -mt-8" style="background: rgba(255,255,255,0.1);"></div>
                                <div class="text-4xl font-bold text-white">{{ $paidCount }}</div>
                                <div class="text-xs mt-1 font-medium" style="color: #dcfce7;">أقساط مدفوعة</div>
                            </div>
                            <div class="relative overflow-hidden text-center p-5 rounded-2xl shadow-lg" style="background: #3b82f6;">
                                <div class="absolute top-0 right-0 w-16 h-16 rounded-full -mr-8 -mt-8" style="background: rgba(255,255,255,0.1);"></div>
                                <div class="text-4xl font-bold text-white">{{ $pendingCount }}</div>
                                <div class="text-xs mt-1 font-medium" style="color: #dbeafe;">أقساط قادمة</div>
                            </div>
                            <div class="relative overflow-hidden text-center p-5 rounded-2xl shadow-lg" style="background: #f97316;">
                                <div class="absolute top-0 right-0 w-16 h-16 rounded-full -mr-8 -mt-8" style="background: rgba(255,255,255,0.1);"></div>
                                <div class="text-4xl font-bold text-white">{{ $overdueCount }}</div>
                                <div class="text-xs mt-1 font-medium" style="color: #ffedd5;">أقساط متأخرة</div>
                            </div>
                        </div>

                        @if($overdueCount > 0)
                        <div class="mb-6 p-5 rounded-2xl shadow-lg" style="background: #f97316;">
                            <div class="flex gap-4 items-center">
                                <div class="p-2 rounded-xl" style="background: rgba(255,255,255,0.2);">
                                    <svg class="w-6 h-6 text-white flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <p class="text-white font-semibold">
                                    لديك {{ $overdueCount }} قسط متأخر. يرجى التواصل مع الإدارة للدفع في أقرب وقت.
                                </p>
                            </div>
                        </div>
                        @endif

                        <!-- Installments List -->
                        <div class="space-y-4">
                            @foreach($payment->installments as $installment)
                            @php
                                $bgStyle = $installment->isOverdue()
                                    ? 'background: #fff7ed; border-color: #fdba74;'
                                    : ($installment->status == 'paid'
                                        ? 'background: #f0fdf4; border-color: #86efac;'
                                        : 'background: #f0f9ff; border-color: #93c5fd;');
                                $badgeColor = $installment->status == 'paid'
                                    ? '#22c55e'
                                    : ($installment->isOverdue()
                                        ? '#f97316'
                                        : '#3b82f6');
                            @endphp
                            <div class="p-5 rounded-2xl border-2 shadow-md hover:shadow-lg transition-all" style="{{ $bgStyle }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center justify-center w-12 h-12 rounded-xl text-white shadow-lg" style="background: {{ $badgeColor }};">
                                            <span class="text-sm font-bold">#{{ $installment->installment_number }}</span>
                                        </div>
                                        <div>
                                            <p class="font-bold text-lg" style="color: #1e3a5f;">القسط #{{ $installment->installment_number }}</p>
                                            <p class="text-sm font-medium" style="color: #64748b;">تاريخ الاستحقاق: {{ $installment->due_date->format('Y-m-d') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-xl font-bold" style="color: #1e3a5f;">{{ number_format($installment->amount, 2) }} ر.س</p>
                                        @if($installment->status == 'pending')
                                            @if($installment->isOverdue())
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold text-white shadow-md" style="background: #f97316;">متأخر</span>
                                            @else
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold text-white shadow-md" style="background: #3b82f6;">قيد الانتظار</span>
                                            @endif
                                        @elseif($installment->status == 'paid')
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold text-white shadow-md" style="background: #22c55e;">مدفوع</span>
                                        @elseif($installment->status == 'cancelled')
                                            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold text-white shadow-md" style="background: #6b7280;">ملغي</span>
                                        @endif
                                    </div>
                                </div>
                                @if($installment->paid_at)
                                <p class="text-xs mt-3 mr-16 font-medium" style="color: #16a34a;">تاريخ الدفع: {{ $installment->paid_at->format('Y-m-d') }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Transactions -->
                <div class="rounded-2xl shadow-xl border-2 overflow-hidden bg-white" style="border-color: #93c5fd;">
                    <div class="px-8 py-6" style="background: #3b82f6;">
                        <h2 class="text-xl font-bold text-white flex items-center gap-3">
                            <div class="p-2.5 rounded-xl" style="background: rgba(255,255,255,0.2);">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            سجل المدفوعات
                        </h2>
                        <p class="text-sm text-white/80 mt-1 mr-12">جميع المعاملات المالية</p>
                    </div>

                    <div class="p-8">
                        @if($payment->transactions->count() > 0)
                            <div class="space-y-4">
                                @foreach($payment->transactions as $transaction)
                                @php
                                    $tranBg = $transaction->status == 'success'
                                        ? 'background: #f0fdf4; border-color: #86efac;'
                                        : ($transaction->status == 'pending'
                                            ? 'background: #fff7ed; border-color: #fdba74;'
                                            : 'background: #fef2f2; border-color: #fca5a5;');
                                    $tranIconBg = $transaction->status == 'success'
                                        ? '#22c55e'
                                        : ($transaction->status == 'pending'
                                            ? '#f97316'
                                            : '#ef4444');
                                @endphp
                                <div class="flex items-start gap-4 p-5 rounded-2xl border-2 shadow-md hover:shadow-lg transition-all" style="{{ $tranBg }}">
                                    <div class="flex items-center justify-center w-14 h-14 rounded-xl shadow-lg" style="background: {{ $tranIconBg }};">
                                        @if($transaction->type == 'payment')
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @else
                                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="font-bold text-xl" style="color: #1e3a5f;">{{ number_format($transaction->amount, 2) }} ر.س</p>
                                            @if($transaction->status == 'success')
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold text-white shadow-md" style="background: #22c55e;">ناجح</span>
                                            @elseif($transaction->status == 'pending')
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold text-white shadow-md" style="background: #f97316;">قيد المعالجة</span>
                                            @elseif($transaction->status == 'failed')
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold text-white shadow-md" style="background: #ef4444;">فشل</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 text-sm font-medium" style="color: #3b82f6;">
                                            <span>{{ $transaction->created_at->format('Y-m-d H:i') }}</span>
                                            <span style="color: #93c5fd;">•</span>
                                            <span class="px-2 py-0.5 rounded-full" style="background: #dbeafe;">
                                                @if($transaction->payment_method == 'cash')
                                                    نقدي
                                                @elseif($transaction->payment_method == 'bank_transfer')
                                                    تحويل بنكي
                                                @elseif($transaction->payment_method == 'tamara')
                                                    تمارا
                                                @elseif($transaction->payment_method == 'paytabs')
                                                    بطاقة/PayTabs
                                                @elseif($transaction->payment_method == 'stripe')
                                                    Stripe
                                                @elseif($transaction->payment_method == 'waived')
                                                    معفي
                                                @else
                                                    {{ $transaction->payment_method }}
                                                @endif
                                            </span>
                                            @if($transaction->transaction_reference)
                                            <span style="color: #93c5fd;">•</span>
                                            <span>المرجع: {{ $transaction->transaction_reference }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-24 h-24 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-xl" style="background: #3b82f6;">
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-lg font-medium" style="color: #1e40af;">لا توجد معاملات بعد</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Notes -->
                @if($payment->notes)
                <div class="rounded-2xl shadow-xl border-2 overflow-hidden bg-white" style="border-color: #fdba74;">
                    <div class="px-8 py-6" style="background: #f97316;">
                        <h2 class="text-xl font-bold text-white flex items-center gap-3">
                            <div class="p-2.5 rounded-xl" style="background: rgba(255,255,255,0.2);">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            ملاحظات
                        </h2>
                    </div>
                    <div class="p-8">
                        <p class="font-medium leading-relaxed" style="color: #9a3412;">{{ $payment->notes }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
