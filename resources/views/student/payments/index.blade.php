@extends('layouts.dashboard')

@section('title', 'حالة الدفع')

@section('content')
<div class="px-4 py-4">
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-800 dark:text-white">حالة الدفع</h3>
        <p class="text-gray-500 dark:text-gray-400 text-sm">عرض حالة الدفع للبرامج المسجلة</p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg flex items-center justify-between">
            <span>{{ session('warning') }}</span>
            <button onclick="this.parentElement.remove()" class="text-yellow-500 hover:text-yellow-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div class="mb-4 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg flex items-center justify-between">
            <span>{{ session('info') }}</span>
            <button onclick="this.parentElement.remove()" class="text-blue-500 hover:text-blue-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if($payments->count() > 0)
        @foreach($payments as $payment)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h5 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $payment->program->name_ar }}</h5>
                    @if($payment->status == 'pending')
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">قيد الانتظار</span>
                    @elseif($payment->status == 'partial')
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">جزئية</span>
                    @elseif($payment->status == 'completed')
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">مكتملة</span>
                    @elseif($payment->status == 'cancelled')
                        <span class="px-3 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">ملغاة</span>
                    @endif
                </div>

                <!-- Body -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Payment Summary -->
                        <div>
                            <h6 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">ملخص الدفعة</h6>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-700 dark:text-gray-300">إجمالي المبلغ:</span>
                                    <strong class="text-gray-900 dark:text-white">{{ number_format($payment->total_amount, 2) }} ر.س</strong>
                                </div>
                                @if($payment->discount_amount > 0)
                                    <div class="flex justify-between">
                                        <span class="text-gray-700 dark:text-gray-300">الخصم:</span>
                                        <strong class="text-green-600 dark:text-green-400">- {{ number_format($payment->discount_amount, 2) }} ر.س</strong>
                                    </div>
                                @endif
                                <div class="flex justify-between">
                                    <span class="text-gray-700 dark:text-gray-300">المدفوع:</span>
                                    <strong class="text-green-600 dark:text-green-400">{{ number_format($payment->paid_amount, 2) }} ر.س</strong>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700 dark:text-gray-300">المتبقي:</span>
                                    <strong class="text-blue-600 dark:text-blue-400">{{ number_format($payment->remaining_amount, 2) }} ر.س</strong>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            @php
                                $percentage = $payment->total_amount > 0 ? ($payment->paid_amount / $payment->total_amount) * 100 : 0;
                            @endphp
                            <div class="mt-4">
                                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                                    <span>نسبة الدفع</span>
                                    <span>{{ number_format($percentage, 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-6 dark:bg-gray-700">
                                    <div class="bg-green-600 h-6 rounded-full flex items-center justify-center text-white text-xs font-medium" style="width: {{ $percentage }}%">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Actions -->
                        <div>
                            <h6 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">خيارات الدفع</h6>

                            @if(!$payment->isFullyPaid() && !$payment->isCancelled())
                                @if($payment->payment_type == 'installment' && $payment->installments->count() > 0)
                                    <div class="p-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg mb-3 text-sm">
                                        <svg class="w-5 h-5 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        لديك خطة تقسيط. يرجى مراجعة الأقساط أدناه.
                                    </div>
                                @else
                                    <!-- Tamara Payment Option -->
                                    @if($tamaraConfigured ?? false)
                                        <form action="{{ route('student.payments.pay-tamara', $payment) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center justify-center font-medium mb-3">
                                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                                </svg>
                                                الدفع عبر تمارا (بالتقسيط)
                                            </button>
                                        </form>
                                    @endif

                                    <div class="p-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg text-sm">
                                        <svg class="w-5 h-5 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        يمكنك الدفع نقداً أو عبر التحويل البنكي بالتواصل مع الإدارة
                                    </div>
                                @endif
                            @elseif($payment->isFullyPaid())
                                <div class="p-3 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">
                                    <svg class="w-5 h-5 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    تم دفع المبلغ بالكامل!
                                </div>
                            @endif

                            <a href="{{ route('student.payments.show', $payment) }}" class="block w-full mt-3 px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition text-center font-medium">
                                <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                عرض التفاصيل
                            </a>
                        </div>
                    </div>

                    <!-- Installments Table -->
                    @if($payment->payment_type == 'installment' && $payment->installments->count() > 0)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h6 class="text-sm font-semibold text-gray-500 dark:text-gray-400 mb-3">الأقساط</h6>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-right">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                        <tr>
                                            <th class="px-4 py-3">القسط</th>
                                            <th class="px-4 py-3">المبلغ</th>
                                            <th class="px-4 py-3">تاريخ الاستحقاق</th>
                                            <th class="px-4 py-3">الحالة</th>
                                            <th class="px-4 py-3">تاريخ الدفع</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($payment->installments as $installment)
                                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">القسط #{{ $installment->installment_number }}</td>
                                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ number_format($installment->amount, 2) }} ر.س</td>
                                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ $installment->due_date->format('Y-m-d') }}</td>
                                                <td class="px-4 py-3">
                                                    @if($installment->status == 'pending')
                                                        @if($installment->isOverdue())
                                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">متأخر</span>
                                                        @else
                                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300">قيد الانتظار</span>
                                                        @endif
                                                    @elseif($installment->status == 'paid')
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">مدفوع</span>
                                                    @elseif($installment->status == 'cancelled')
                                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">ملغي</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
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
                            @endphp
                            @if($overdueCount > 0)
                                <div class="mt-4 p-3 bg-red-50 border border-red-200 text-red-800 rounded-lg text-sm">
                                    <svg class="w-5 h-5 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    لديك {{ $overdueCount }} قسط متأخر. يرجى التواصل مع الإدارة للدفع.
                                </div>
                            @endif
                        </div>
                    @endif

                    @if($payment->notes)
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <strong class="text-gray-800 dark:text-white">ملاحظات:</strong><br>
                                <span class="text-gray-600 dark:text-gray-300">{{ $payment->notes }}</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Footer -->
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                    <small class="text-gray-500 dark:text-gray-400">تاريخ الإنشاء: {{ $payment->created_at->format('Y-m-d H:i') }}</small>
                </div>
            </div>
        @endforeach
    @else
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h4 class="text-xl font-semibold text-gray-600 dark:text-gray-300 mb-2">لا توجد دفعات</h4>
                <p class="text-gray-500 dark:text-gray-400">لم يتم إنشاء أي دفعة لك بعد</p>
            </div>
        </div>
    @endif
</div>
@endsection
