@extends('layouts.dashboard')

@section('title', 'إنشاء دفعة جديدة')

@push('styles')
<style>
    .create-payment-page { max-width: 1200px; margin: 0 auto; }

    /* Header */
    .page-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .page-header::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .page-header::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -5%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }
    .header-title {
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }
    .header-subtitle {
        font-size: 0.95rem;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }
    .header-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.25rem;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.25);
        color: #fff;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
        position: relative;
        z-index: 1;
    }
    .header-back:hover {
        background: rgba(255,255,255,0.25);
        transform: translateY(-1px);
    }
    .header-back svg { width: 18px; height: 18px; }

    /* Form Card */
    .form-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .form-card { background: #1f2937; }

    .form-card-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 2px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .form-card-header { border-color: #374151; }
    .form-card-header h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
    }
    .dark .form-card-header h3 { color: #f9fafb; }
    .form-card-header .icon-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .form-card-header .icon-box svg { width: 20px; height: 20px; color: #fff; }

    .form-card-body {
        padding: 1.75rem;
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group:last-child {
        margin-bottom: 0;
    }
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    .dark .form-label { color: #d1d5db; }
    .form-label .required {
        color: #ef4444;
        margin-right: 0.25rem;
    }

    .form-input,
    .form-select,
    .form-textarea {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.9375rem;
        color: #111827;
        background: #fff;
        transition: all 0.2s;
    }
    .dark .form-input,
    .dark .form-select,
    .dark .form-textarea {
        background: #111827;
        border-color: #374151;
        color: #f3f4f6;
    }
    .form-input:focus,
    .form-select:focus,
    .form-textarea:focus {
        outline: none;
        border-color: #0071AA;
        box-shadow: 0 0 0 3px rgba(0, 113, 170, 0.1);
    }
    .form-input.is-invalid,
    .form-select.is-invalid {
        border-color: #ef4444;
    }
    .form-error {
        margin-top: 0.375rem;
        font-size: 0.8125rem;
        color: #ef4444;
        font-weight: 500;
    }
    .form-hint {
        margin-top: 0.375rem;
        font-size: 0.8125rem;
        color: #6b7280;
    }
    .dark .form-hint { color: #9ca3af; }

    /* Installment Box */
    .installment-box {
        background: #eff6ff;
        border: 2px solid #bfdbfe;
        border-radius: 14px;
        padding: 1.25rem;
        margin-top: 1rem;
    }
    .dark .installment-box {
        background: rgba(59, 130, 246, 0.1);
        border-color: rgba(59, 130, 246, 0.3);
    }

    /* Summary Card */
    .summary-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        position: sticky;
        top: 1rem;
    }
    .dark .summary-card { background: #1f2937; }

    .summary-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 2px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .summary-header { border-color: #374151; }
    .summary-header h3 {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
    }
    .dark .summary-header h3 { color: #f9fafb; }

    .summary-body {
        padding: 1.75rem;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .summary-row:last-child { margin-bottom: 0; }
    .summary-label {
        font-size: 0.9rem;
        color: #6b7280;
        font-weight: 500;
    }
    .dark .summary-label { color: #9ca3af; }
    .summary-value {
        font-size: 0.9rem;
        font-weight: 700;
        color: #111827;
    }
    .dark .summary-value { color: #f9fafb; }
    .summary-total {
        font-size: 1.375rem;
        font-weight: 800;
        color: #0071AA;
    }
    .dark .summary-total { color: #38bdf8; }
    .summary-divider {
        height: 2px;
        background: #f3f4f6;
        margin: 1rem 0;
    }
    .dark .summary-divider { background: #374151; }

    .info-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        margin-top: 1.25rem;
    }
    .dark .info-box {
        background: rgba(59, 130, 246, 0.1);
        border-color: rgba(59, 130, 246, 0.3);
    }
    .info-box svg { width: 20px; height: 20px; color: #0071AA; flex-shrink: 0; margin-top: 0.125rem; }
    .dark .info-box svg { color: #38bdf8; }
    .info-box p {
        font-size: 0.8125rem;
        color: #1e40af;
        line-height: 1.5;
    }
    .dark .info-box p { color: #93c5fd; }

    /* Buttons */
    .btn-submit {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        color: #fff;
        border: none;
        border-radius: 14px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 1.25rem;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px -4px rgba(0, 113, 170, 0.4);
    }
    .btn-submit svg { width: 22px; height: 22px; }

    .btn-cancel {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.875rem 1.5rem;
        background: #f3f4f6;
        color: #4b5563;
        border: none;
        border-radius: 14px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 0.75rem;
        text-decoration: none;
    }
    .dark .btn-cancel { background: #374151; color: #d1d5db; }
    .btn-cancel:hover {
        background: #e5e7eb;
        transform: translateY(-1px);
    }
    .dark .btn-cancel:hover { background: #4b5563; }

    /* Alert */
    .alert-error {
        background: #fef2f2;
        border: 2px solid #fecaca;
        color: #991b1b;
        padding: 1rem 1.5rem;
        border-radius: 14px;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-weight: 600;
    }
    .dark .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border-color: rgba(239, 68, 68, 0.3);
        color: #fca5a5;
    }
    .alert-error-close {
        background: none;
        border: none;
        color: inherit;
        cursor: pointer;
        padding: 0.25rem;
        opacity: 0.7;
    }
    .alert-error-close:hover { opacity: 1; }
    .alert-error-close svg { width: 18px; height: 18px; }
</style>
@endpush

@section('content')
<div class="create-payment-page">
    <!-- Header -->
    <div class="page-header">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; position: relative; z-index: 1;">
            <div>
                <h1 class="header-title">إنشاء دفعة جديدة</h1>
                <p class="header-subtitle">إنشاء دفعة جديدة لطالب في برنامج معين</p>
            </div>
            <a href="{{ route('admin.payments.index') }}" class="header-back">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                </svg>
                العودة للدفعات
            </a>
        </div>
    </div>

    <!-- Error Alert -->
    @if(session('error'))
        <div class="alert-error">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 22px; height: 22px; flex-shrink: 0;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="alert-error-close">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Form -->
    <form action="{{ route('admin.payments.store') }}" method="POST">
        @csrf
        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
            <!-- Main Form - Desktop: 2/3 -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                <div>
                    <div class="form-card">
                        <div class="form-card-header">
                            <div class="icon-box" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h3>بيانات الدفعة</h3>
                        </div>
                        <div class="form-card-body">
                            <!-- Student -->
                            <div class="form-group">
                                <label class="form-label">الطالب <span class="required">*</span></label>
                                <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                    <option value="">اختر الطالب</option>
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" {{ old('user_id') == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Program -->
                            <div class="form-group">
                                <label class="form-label">البرنامج <span class="required">*</span></label>
                                <select name="program_id" id="program_id" class="form-select @error('program_id') is-invalid @enderror" required>
                                    <option value="">اختر البرنامج</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" data-price="{{ $program->price }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                            {{ $program->name_ar }} ({{ number_format($program->price, 2) }} ر.س)
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_id')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Type -->
                            <div class="form-group">
                                <label class="form-label">نوع الدفع <span class="required">*</span></label>
                                <select name="payment_type" id="payment_type" class="form-select @error('payment_type') is-invalid @enderror" required>
                                    <option value="full" {{ old('payment_type') == 'full' ? 'selected' : '' }}>دفعة كاملة</option>
                                    <option value="installment" {{ old('payment_type') == 'installment' ? 'selected' : '' }}>تقسيط</option>
                                </select>
                                @error('payment_type')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Installment Options -->
                            <div id="installment_options" class="installment-box" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">عدد الأقساط <span class="required">*</span></label>
                                    <input type="number" name="number_of_installments" class="form-input @error('number_of_installments') is-invalid @enderror" min="2" max="12" value="{{ old('number_of_installments', 3) }}">
                                    @error('number_of_installments')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                    <p class="form-hint">من 2 إلى 12 قسط</p>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">تاريخ بداية التقسيط <span class="required">*</span></label>
                                    <input type="date" name="installment_start_date" class="form-input @error('installment_start_date') is-invalid @enderror" value="{{ old('installment_start_date', date('Y-m-d')) }}">
                                    @error('installment_start_date')
                                        <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Discount -->
                            <div class="form-group">
                                <label class="form-label">مبلغ الخصم (اختياري)</label>
                                <input type="number" name="discount_amount" class="form-input @error('discount_amount') is-invalid @enderror" step="0.01" min="0" value="{{ old('discount_amount', 0) }}">
                                @error('discount_amount')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                                <p class="form-hint">اترك صفر إذا لم يكن هناك خصم</p>
                            </div>

                            <!-- Payment Method -->
                            <div class="form-group">
                                <label class="form-label">طريقة الدفع</label>
                                <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror">
                                    <option value="">لم يتم التحديد بعد</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                    <option value="tamara" {{ old('payment_method') == 'tamara' ? 'selected' : '' }}>تمارا</option>
                                    <option value="waived" {{ old('payment_method') == 'waived' ? 'selected' : '' }}>معفي</option>
                                </select>
                                @error('payment_method')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Notes -->
                            <div class="form-group">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-textarea @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar - Summary -->
                <div>
                    <div class="summary-card">
                        <div class="summary-header">
                            <div class="icon-box" style="width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #fff;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3>ملخص الدفعة</h3>
                        </div>
                        <div class="summary-body">
                            <div class="summary-row">
                                <span class="summary-label">سعر البرنامج:</span>
                                <span class="summary-value" id="program_price">0.00 ر.س</span>
                            </div>
                            <div class="summary-row">
                                <span class="summary-label">الخصم:</span>
                                <span class="summary-value" id="discount_display" style="color: #ef4444;">0.00 ر.س</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row">
                                <span class="summary-label" style="font-size: 1rem; font-weight: 600;">المبلغ المطلوب:</span>
                                <span class="summary-total" id="total_required">0.00 ر.س</span>
                            </div>

                            <div class="info-box">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p>سيتم إنشاء سجل الدفعة ويمكنك تسجيل المدفوعات لاحقاً</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        إنشاء الدفعة
                    </button>
                    <a href="{{ route('admin.payments.index') }}" class="btn-cancel">
                        إلغاء
                    </a>
                </div>
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
            installmentOptions.style.display = this.value === 'installment' ? 'block' : 'none';
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
