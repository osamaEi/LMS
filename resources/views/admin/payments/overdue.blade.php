@extends('layouts.dashboard')

@section('title', 'الأقساط المتأخرة')

@section('content')
<div class="px-4 py-4">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                <i class="bi bi-arrow-right"></i>
            </a>
            <div>
                <h3 class="text-2xl font-bold text-gray-800">الأقساط المتأخرة</h3>
                <p class="text-gray-500 text-sm">عرض جميع الأقساط المتأخرة عن موعد الاستحقاق</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg flex items-center justify-between">
            <div class="flex items-center">
                <i class="bi bi-check-circle text-green-500 me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="p-6">
            <form action="{{ route('admin.payments.overdue') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الطالب</label>
                        <select name="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">الكل</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ request('user_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">البرنامج</label>
                        <select name="program_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">الكل</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center">
                            <i class="bi bi-funnel me-2"></i> تصفية
                        </button>
                        <a href="{{ route('admin.payments.overdue') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition flex items-center">
                            <i class="bi bi-arrow-clockwise me-2"></i> إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border-2 border-red-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 mb-1">إجمالي الأقساط المتأخرة</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $installments->total() }}</h3>
                </div>
                <div class="text-red-500">
                    <i class="bi bi-exclamation-triangle text-5xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border-2 border-yellow-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 mb-1">إجمالي المبالغ المتأخرة</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ number_format($installments->sum('amount'), 2) }} ر.س</h3>
                </div>
                <div class="text-yellow-500">
                    <i class="bi bi-currency-dollar text-5xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border-2 border-blue-200 p-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-500 mb-1">عدد الطلاب المتأخرين</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $installments->pluck('payment.user_id')->unique()->count() }}</h3>
                </div>
                <div class="text-blue-500">
                    <i class="bi bi-people text-5xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Overdue Installments Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">#</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">الطالب</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">البرنامج</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">رقم القسط</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">المبلغ</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">تاريخ الاستحقاق</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">الأيام المتأخرة</th>
                            <th class="px-4 py-3 text-right text-sm font-semibold text-gray-600">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($installments as $installment)
                            @php
                                $daysOverdue = now()->diffInDays($installment->due_date);
                            @endphp
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm">{{ $installment->id }}</td>
                                <td class="px-4 py-3">
                                    <div>
                                        <div class="font-semibold text-gray-800">{{ $installment->payment->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $installment->payment->user->email }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $installment->payment->program->name_ar }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">القسط #{{ $installment->installment_number }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ number_format($installment->amount, 2) }} ر.س</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $installment->due_date->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">
                                    @if($daysOverdue < 7)
                                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">{{ $daysOverdue }} يوم</span>
                                    @elseif($daysOverdue < 30)
                                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded">{{ $daysOverdue }} يوم</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-medium bg-gray-800 text-white rounded">{{ $daysOverdue }} يوم</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2">
                                        <a href="{{ route('admin.payments.show', $installment->payment) }}" class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded transition">
                                            <i class="bi bi-eye me-1"></i> عرض
                                        </a>
                                        <button type="button" class="inline-flex items-center px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-sm rounded transition" onclick="recordInstallmentPayment({{ $installment->id }})">
                                            <i class="bi bi-check-circle me-1"></i> تسجيل
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-12">
                                    <i class="bi bi-check-circle text-green-500 text-6xl"></i>
                                    <p class="text-gray-500 mt-2">لا توجد أقساط متأخرة</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $installments->links() }}
            </div>
        </div>
    </div>
</div>

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
