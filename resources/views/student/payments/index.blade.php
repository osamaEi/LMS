@extends('layouts.dashboard')

@section('title', 'المدفوعات')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Hero Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 via-cyan-600 to-teal-700 rounded-2xl shadow-2xl p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-72 h-72 bg-white opacity-5 rounded-full -mr-36 -mt-36"></div>
                <div class="absolute bottom-0 left-0 w-56 h-56 bg-white opacity-5 rounded-full -ml-28 -mb-28"></div>

                <div class="relative z-10">
                    <div class="flex items-center gap-4 mb-3">
                        <div class="w-16 h-16 rounded-xl bg-white bg-opacity-20 flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-9 h-9" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">{{ __('المدفوعات والرسوم الدراسية') }}</h1>
                            <p class="text-blue-100 text-lg mt-1">{{ __('عرض وإدارة جميع مدفوعاتك') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4 rounded-xl shadow-lg flex items-center space-x-3 rtl:space-x-reverse animate-fade-in">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4 rounded-xl shadow-lg flex items-center space-x-3 rtl:space-x-reverse animate-fade-in">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 bg-gradient-to-r from-yellow-500 to-orange-600 px-6 py-4 rounded-xl shadow-lg flex items-center space-x-3 rtl:space-x-reverse animate-fade-in">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('warning') }}</span>
            </div>
        @endif

        @if($payments->count() > 0)
            <!-- Payments Table -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-blue-600 to-cyan-600">
                            <tr>
                                <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">#</th>
                                <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">البرنامج</th>
                                <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">إجمالي المبلغ</th>
                                <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">الخصم</th>
                                <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">المدفوع</th>
                                <th class="px-6 py-4 text-right text-sm font-bold uppercase tracking-wider">المتبقي</th>
                                <th class="px-6 py-4 text-center text-sm font-bold uppercase tracking-wider">نسبة الدفع</th>
                                <th class="px-6 py-4 text-center text-sm font-bold uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-4 text-center text-sm font-bold uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900">#{{ $payment->id }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $payment->program->name_ar }}</div>
                                            <div class="text-xs text-gray-500">{{ $payment->program->name_en }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-gray-900">{{ number_format($payment->total_amount, 0) }} ر.س</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($payment->discount_amount > 0)
                                            <span class="text-sm font-semibold text-green-600">{{ number_format($payment->discount_amount, 0) }} ر.س</span>
                                        @else
                                            <span class="text-sm text-gray-400">--</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-blue-600">{{ number_format($payment->paid_amount, 0) }} ر.س</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-orange-600">{{ number_format($payment->remaining_amount, 0) }} ر.س</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @php
                                            $percentage = $payment->total_amount > 0 ? ($payment->paid_amount / $payment->total_amount) * 100 : 0;
                                        @endphp
                                        <div class="flex items-center justify-center">
                                            <div class="w-24">
                                                <div class="flex items-center gap-2">
                                                    <div class="flex-1 bg-gray-200 rounded-full h-2 overflow-hidden">
                                                        <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-2 transition-all duration-500" style="width: {{ $percentage }}%"></div>
                                                    </div>
                                                    <span class="text-xs font-bold text-gray-700">{{ number_format($percentage, 0) }}%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($payment->status == 'pending')
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-700 inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                                قيد الانتظار
                                            </span>
                                        @elseif($payment->status == 'partial')
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-700 inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                مدفوعة جزئياً
                                            </span>
                                        @elseif($payment->status == 'completed')
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-700 inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                مكتملة
                                            </span>
                                        @elseif($payment->status == 'cancelled')
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-red-100 text-red-700 inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                                ملغاة
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('student.payments.show', $payment) }}" class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 rounded-lg transition-all shadow-md hover:shadow-lg">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </a>

                                            @if(!$payment->isFullyPaid() && !$payment->isCancelled() && ($tamaraConfigured ?? false))
                                                <form action="{{ route('student.payments.pay-tamara', $payment) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 rounded-lg transition-all shadow-md hover:shadow-lg">
                                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"></path>
                                                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between text-sm text-gray-600">
                        <span>إجمالي المدفوعات: <strong class="text-gray-900">{{ $payments->count() }}</strong></span>
                        <span>آخر تحديث: <strong class="text-gray-900">{{ now()->format('Y-m-d H:i') }}</strong></span>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="p-16 text-center">
                    <div class="w-32 h-32 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">لا توجد دفعات</h3>
                    <p class="text-gray-500">لم يتم إنشاء أي دفعة لك بعد. تواصل مع الإدارة لمزيد من المعلومات.</p>
                </div>
            </div>
        @endif

    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection
