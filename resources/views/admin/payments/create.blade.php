@extends('layouts.dashboard')

@section('title', 'إنشاء دفعة جديدة')

@section('content')
<div class="px-4 py-4">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.payments.index') }}" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                <i class="bi bi-arrow-right"></i>
            </a>
            <div>
                <h3 class="text-2xl font-bold text-gray-800">إنشاء دفعة جديدة</h3>
                <p class="text-gray-500 text-sm">إنشاء دفعة جديدة لطالب في برنامج معين</p>
            </div>
        </div>
    </div>

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-800 rounded-lg flex items-center justify-between">
            <div class="flex items-center">
                <i class="bi bi-exclamation-circle text-red-500 me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    <form action="{{ route('admin.payments.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-800">بيانات الدفعة</h5>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الطالب <span class="text-red-500">*</span></label>
                            <select name="user_id" id="user_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('user_id') border-red-500 @enderror" required>
                                <option value="">اختر الطالب</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">البرنامج <span class="text-red-500">*</span></label>
                            <select name="program_id" id="program_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('program_id') border-red-500 @enderror" required>
                                <option value="">اختر البرنامج</option>
                                @foreach($programs as $program)
                                    <option value="{{ $program->id }}" data-price="{{ $program->price }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                        {{ $program->name_ar }} ({{ number_format($program->price, 2) }} ر.س)
                                    </option>
                                @endforeach
                            </select>
                            @error('program_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">نوع الدفع <span class="text-red-500">*</span></label>
                            <select name="payment_type" id="payment_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('payment_type') border-red-500 @enderror" required>
                                <option value="full" {{ old('payment_type') == 'full' ? 'selected' : '' }}>دفعة كاملة</option>
                                <option value="installment" {{ old('payment_type') == 'installment' ? 'selected' : '' }}>تقسيط</option>
                            </select>
                            @error('payment_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="installment_options" class="space-y-4 p-4 bg-blue-50 border border-blue-200 rounded-lg" style="display: none;">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">عدد الأقساط <span class="text-red-500">*</span></label>
                                <input type="number" name="number_of_installments" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('number_of_installments') border-red-500 @enderror" min="2" max="12" value="{{ old('number_of_installments', 3) }}">
                                @error('number_of_installments')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">من 2 إلى 12 قسط</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ بداية التقسيط <span class="text-red-500">*</span></label>
                                <input type="date" name="installment_start_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('installment_start_date') border-red-500 @enderror" value="{{ old('installment_start_date', date('Y-m-d')) }}">
                                @error('installment_start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">مبلغ الخصم (اختياري)</label>
                            <input type="number" name="discount_amount" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('discount_amount') border-red-500 @enderror" step="0.01" min="0" value="{{ old('discount_amount', 0) }}">
                            @error('discount_amount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-gray-500">اترك صفر إذا لم يكن هناك خصم</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع</label>
                            <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('payment_method') border-red-500 @enderror">
                                <option value="">لم يتم التحديد بعد</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                <option value="tamara" {{ old('payment_method') == 'tamara' ? 'selected' : '' }}>تمارا</option>
                                <option value="waived" {{ old('payment_method') == 'waived' ? 'selected' : '' }}>معفي</option>
                            </select>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                            <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 sticky top-4">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h5 class="text-lg font-semibold text-gray-800">ملخص الدفعة</h5>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-600">سعر البرنامج:</span>
                            <strong class="text-gray-800" id="program_price">0.00 ر.س</strong>
                        </div>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-600">الخصم:</span>
                            <strong class="text-gray-800" id="discount_display">0.00 ر.س</strong>
                        </div>
                        <div class="border-t border-gray-200 my-3"></div>
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-lg font-semibold text-gray-700">المبلغ المطلوب:</span>
                            <strong class="text-xl font-bold text-blue-600" id="total_required">0.00 ر.س</strong>
                        </div>

                        <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg flex items-start gap-2">
                            <i class="bi bi-info-circle text-blue-600 mt-0.5"></i>
                            <p class="text-xs text-blue-800">سيتم إنشاء سجل الدفعة ويمكنك تسجيل المدفوعات لاحقاً</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full mt-4 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition flex items-center justify-center font-medium">
                    <i class="bi bi-check-circle me-2"></i> إنشاء الدفعة
                </button>
                <a href="{{ route('admin.payments.index') }}" class="w-full mt-2 px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition flex items-center justify-center font-medium">
                    إلغاء
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentTypeSelect = document.getElementById('payment_type');
        const installmentOptions = document.getElementById('installment_options');
        const programSelect = document.getElementById('program_id');
        const discountInput = document.querySelector('input[name="discount_amount"]');

        // Toggle installment options
        paymentTypeSelect.addEventListener('change', function() {
            if (this.value === 'installment') {
                installmentOptions.style.display = 'block';
            } else {
                installmentOptions.style.display = 'none';
            }
        });

        // Update summary on change
        programSelect.addEventListener('change', updateSummary);
        discountInput.addEventListener('input', updateSummary);

        // Initialize if page loads with installment selected
        if (paymentTypeSelect.value === 'installment') {
            installmentOptions.style.display = 'block';
        }

        function updateSummary() {
            const selectedOption = programSelect.options[programSelect.selectedIndex];
            const price = parseFloat(selectedOption.dataset.price || 0);
            const discount = parseFloat(discountInput.value || 0);
            const total = Math.max(0, price - discount);

            document.getElementById('program_price').textContent = price.toFixed(2) + ' ر.س';
            document.getElementById('discount_display').textContent = discount.toFixed(2) + ' ر.س';
            document.getElementById('total_required').textContent = total.toFixed(2) + ' ر.س';
        }

        // Initialize summary
        updateSummary();
    });
</script>
@endpush
@endsection
