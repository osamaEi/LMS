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
                            <!-- Student (custom searchable picker) -->
                            <div class="form-group" style="position:relative;">
                                <label class="form-label">الطالب <span class="required">*</span></label>

                                {{-- Hidden real input for form submission --}}
                                <input type="hidden" name="user_id" id="user_id" value="{{ old('user_id') }}" required>

                                {{-- Trigger button --}}
                                <div id="student-trigger"
                                     onclick="toggleStudentPicker()"
                                     style="display:flex;align-items:center;justify-content:space-between;width:100%;padding:.75rem 1rem;border:2px solid {{ $errors->has('user_id') ? '#ef4444' : '#e5e7eb' }};border-radius:12px;font-size:.9375rem;color:#111827;background:#fff;cursor:pointer;box-sizing:border-box;transition:border-color .2s;">
                                    <span id="student-trigger-text" style="color:#6b7280;">اختر الطالب</span>
                                    <svg id="student-chevron" style="width:18px;height:18px;color:#9ca3af;transition:transform .2s;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>

                                {{-- Dropdown --}}
                                <div id="student-picker" style="display:none;position:absolute;top:100%;right:0;left:0;z-index:500;background:#fff;border:2px solid #0071AA;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.12);margin-top:4px;overflow:hidden;">
                                    <div style="padding:.6rem .75rem;border-bottom:1px solid #f1f5f9;">
                                        <input type="text" id="student-search" placeholder="بحث بالاسم أو الإيميل أو رقم الهوية..."
                                               oninput="filterStudents()"
                                               style="width:100%;border:1.5px solid #e2e8f0;border-radius:8px;padding:.5rem .75rem;font-size:.875rem;color:#374151;outline:none;box-sizing:border-box;"
                                               onfocus="this.style.borderColor='#0071AA'" onblur="this.style.borderColor='#e2e8f0'">
                                    </div>
                                    <div id="student-list" style="max-height:260px;overflow-y:auto;">
                                        @foreach($students as $s)
                                        <div class="s-row"
                                             data-id="{{ $s->id }}"
                                             data-name="{{ $s->name }}"
                                             data-email="{{ $s->email }}"
                                             data-nid="{{ $s->national_id ?? '' }}"
                                             onclick="selectStudent({{ $s->id }}, '{{ addslashes($s->name) }}', '{{ addslashes($s->email) }}')"
                                             style="display:flex;align-items:center;gap:.75rem;padding:.65rem 1rem;cursor:pointer;border-bottom:1px solid #f9fafb;transition:background .15s;"
                                             onmouseover="this.style.background='#eff6ff'"
                                             onmouseout="this.style.background='white'">
                                            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center;font-size:.85rem;font-weight:800;color:white;flex-shrink:0;">
                                                {{ mb_substr($s->name, 0, 1) }}
                                            </div>
                                            <div style="flex:1;min-width:0;">
                                                <div style="font-size:.9rem;font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $s->name }}</div>
                                                <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-top:2px;">
                                                    <span style="font-size:.75rem;color:#0071AA;">{{ $s->email }}</span>
                                                    @if($s->national_id)
                                                    <span style="font-size:.7rem;color:#9ca3af;border:1px solid #e5e7eb;border-radius:4px;padding:0 4px;">{{ $s->national_id }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <svg class="s-check" style="width:18px;height:18px;color:#0071AA;display:none;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </div>
                                        @endforeach
                                        <div id="no-students" style="display:none;padding:1.25rem;text-align:center;color:#9ca3af;font-size:.875rem;">لا يوجد نتائج</div>
                                    </div>
                                </div>

                                @error('user_id')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Program (custom picker) -->
                            <div class="form-group" style="position:relative;">
                                <label class="form-label">البرنامج <span class="required">*</span></label>
                                <input type="hidden" name="program_id" id="program_id" value="{{ old('program_id') }}" required>
                                <div id="program-trigger" onclick="togglePicker('program')"
                                     style="display:flex;align-items:center;justify-content:space-between;width:100%;padding:.75rem 1rem;border:2px solid {{ $errors->has('program_id') ? '#ef4444' : '#e5e7eb' }};border-radius:12px;font-size:.9375rem;background:#fff;cursor:pointer;box-sizing:border-box;transition:border-color .2s;">
                                    <span id="program-trigger-text" style="color:#6b7280;">اختر البرنامج</span>
                                    <svg id="program-chevron" style="width:18px;height:18px;color:#9ca3af;transition:transform .2s;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                                <div id="program-picker" style="display:none;position:absolute;top:100%;right:0;left:0;z-index:500;background:#fff;border:2px solid #0071AA;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.12);margin-top:4px;overflow:hidden;">
                                    <div id="program-list" style="max-height:240px;overflow-y:auto;">
                                        @foreach($programs as $p)
                                        <div class="p-row"
                                             data-id="{{ $p->id }}"
                                             data-price="{{ $p->price }}"
                                             data-name="{{ $p->name_ar }}"
                                             onclick="selectProgram({{ $p->id }}, '{{ addslashes($p->name_ar) }}', {{ $p->price }})"
                                             style="display:flex;align-items:center;justify-content:space-between;padding:.75rem 1rem;cursor:pointer;border-bottom:1px solid #f9fafb;transition:background .15s;"
                                             onmouseover="this.style.background='#eff6ff'"
                                             onmouseout="this.style.background='white'">
                                            <div style="flex:1;min-width:0;">
                                                <div style="font-size:.9rem;font-weight:700;color:#111827;">{{ $p->name_ar }}</div>
                                                @if($p->name_en)
                                                <div style="font-size:.75rem;color:#6b7280;">{{ $p->name_en }}</div>
                                                @endif
                                            </div>
                                            <div style="display:flex;align-items:center;gap:.5rem;flex-shrink:0;margin-right:.5rem;">
                                                <span style="font-size:.85rem;font-weight:700;color:#0071AA;">{{ number_format($p->price, 2) }} ر.س</span>
                                                <svg class="p-check" style="width:16px;height:16px;color:#0071AA;display:none;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('program_id')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Type (custom picker) -->
                            <div class="form-group">
                                <label class="form-label">نوع الدفع <span class="required">*</span></label>
                                <input type="hidden" name="payment_type" id="payment_type" value="{{ old('payment_type', 'full') }}" required>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                                    @foreach([['full','دفعة كاملة','M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],['installment','تقسيط','M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z']] as [$val,$lab,$icon])
                                    <div id="ptype-{{ $val }}" onclick="selectPaymentType('{{ $val }}')"
                                         style="display:flex;align-items:center;gap:.6rem;padding:.75rem 1rem;border:2px solid {{ old('payment_type','full') === $val ? '#0071AA' : '#e5e7eb' }};border-radius:12px;cursor:pointer;transition:all .18s;background:{{ old('payment_type','full') === $val ? '#eff6ff' : '#fff' }};">
                                        <svg style="width:20px;height:20px;color:{{ old('payment_type','full') === $val ? '#0071AA' : '#9ca3af' }};flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                                        </svg>
                                        <span style="font-size:.875rem;font-weight:700;color:{{ old('payment_type','full') === $val ? '#0071AA' : '#374151' }};">{{ $lab }}</span>
                                    </div>
                                    @endforeach
                                </div>
                                @error('payment_type')
                                    <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Installment Options -->
                            <div id="installment_options" class="installment-box" style="display: none;">
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                                    <div class="form-group" style="margin-bottom:0;">
                                        <label class="form-label">عدد الأقساط <span class="required">*</span></label>
                                        <input type="number" name="number_of_installments" id="number_of_installments"
                                               class="form-input @error('number_of_installments') is-invalid @enderror"
                                               min="2" max="12" value="{{ old('number_of_installments', 3) }}"
                                               oninput="generateInstallmentDates()">
                                        @error('number_of_installments')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                        <p class="form-hint">من 2 إلى 12 قسط</p>
                                    </div>
                                    <div class="form-group" style="margin-bottom:0;">
                                        <label class="form-label">تاريخ بداية التقسيط <span class="required">*</span></label>
                                        <input type="date" name="installment_start_date" id="installment_start_date"
                                               class="form-input @error('installment_start_date') is-invalid @enderror"
                                               value="{{ old('installment_start_date', date('Y-m-d')) }}"
                                               onchange="generateInstallmentDates()">
                                        @error('installment_start_date')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Per-installment dates -->
                                <div style="margin-top:1.25rem;border-top:1.5px dashed #bfdbfe;padding-top:1rem;">
                                    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.75rem;">
                                        <svg style="width:15px;height:15px;color:#2563eb;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span style="font-size:.8rem;font-weight:700;color:#1e40af;">تاريخ استحقاق كل قسط</span>
                                        <span style="font-size:.72rem;color:#6b7280;">(يمكنك تعديل كل تاريخ)</span>
                                    </div>
                                    <div id="installment-dates-list" style="display:flex;flex-direction:column;gap:0.5rem;">
                                        {{-- Generated by JS --}}
                                    </div>
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

                            <!-- Payment Method (custom picker) -->
                            <div class="form-group">
                                <label class="form-label">طريقة الدفع <span style="font-weight:400;color:#9ca3af;">(اختياري)</span></label>
                                <input type="hidden" name="payment_method" id="payment_method" value="{{ old('payment_method', '') }}">
                                @php
                                $methods = [
                                    [''             , 'لم يتم التحديد', 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z', '#9ca3af'],
                                    ['cash'         , 'نقدي'          , 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', '#059669'],
                                    ['bank_transfer', 'تحويل بنكي'    , 'M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z', '#2563eb'],
                                    ['tamara'       , 'تمارا'          , 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', '#7c3aed'],
                                    ['waived'       , 'معفي'           , 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z', '#f59e0b'],
                                ];
                                $oldMethod = old('payment_method', '');
                                @endphp
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem;">
                                    @foreach($methods as [$val,$lab,$icon,$col])
                                    <div id="pmethod-{{ $val ?: 'none' }}" onclick="selectPaymentMethod('{{ $val }}')"
                                         style="display:flex;align-items:center;gap:.6rem;padding:.65rem .9rem;border:2px solid {{ $oldMethod === $val ? '#0071AA' : '#e5e7eb' }};border-radius:12px;cursor:pointer;transition:all .18s;background:{{ $oldMethod === $val ? '#eff6ff' : '#fff' }};">
                                        <svg style="width:18px;height:18px;color:{{ $oldMethod === $val ? '#0071AA' : $col }};flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
                                        </svg>
                                        <span style="font-size:.82rem;font-weight:600;color:{{ $oldMethod === $val ? '#0071AA' : '#374151' }};">{{ $lab }}</span>
                                    </div>
                                    @endforeach
                                </div>
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
/* ── Student custom picker ─────────────────────────── */
let studentPickerOpen = false;

function toggleStudentPicker() {
    studentPickerOpen = !studentPickerOpen;
    const picker  = document.getElementById('student-picker');
    const chevron = document.getElementById('student-chevron');
    picker.style.display  = studentPickerOpen ? 'block' : 'none';
    chevron.style.transform = studentPickerOpen ? 'rotate(180deg)' : 'rotate(0)';
    if (studentPickerOpen) {
        setTimeout(() => document.getElementById('student-search').focus(), 50);
    }
}

function filterStudents() {
    const q = document.getElementById('student-search').value.toLowerCase();
    let visible = 0;
    document.querySelectorAll('.s-row').forEach(row => {
        const match = row.dataset.name.toLowerCase().includes(q)
                   || row.dataset.email.toLowerCase().includes(q)
                   || (row.dataset.nid || '').toLowerCase().includes(q);
        row.style.display = match ? 'flex' : 'none';
        if (match) visible++;
    });
    document.getElementById('no-students').style.display = visible === 0 ? 'block' : 'none';
}

let _selectedStudentId = null;
function selectStudent(id, name, email) {
    // deselect old
    if (_selectedStudentId) {
        const old = document.querySelector(`.s-row[data-id="${_selectedStudentId}"]`);
        if (old) { old.style.background = 'white'; old.querySelector('.s-check').style.display = 'none'; }
    }
    _selectedStudentId = id;
    document.getElementById('user_id').value = id;
    document.getElementById('student-trigger-text').textContent = name + ' — ' + email;
    document.getElementById('student-trigger-text').style.color = '#111827';

    const row = document.querySelector(`.s-row[data-id="${id}"]`);
    if (row) { row.style.background = '#eff6ff'; row.querySelector('.s-check').style.display = 'block'; }

    // close picker
    studentPickerOpen = false;
    document.getElementById('student-picker').style.display = 'none';
    document.getElementById('student-chevron').style.transform = 'rotate(0)';
}

// Close picker when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('#student-trigger') && !e.target.closest('#student-picker')) {
        studentPickerOpen = false;
        document.getElementById('student-picker').style.display = 'none';
        document.getElementById('student-chevron').style.transform = 'rotate(0)';
    }
});

/* ── Program custom picker ─────────────────────────── */
const pickerState = {};

function togglePicker(key) {
    pickerState[key] = !pickerState[key];
    const picker  = document.getElementById(key + '-picker');
    const chevron = document.getElementById(key + '-chevron');
    if (picker)  picker.style.display = pickerState[key] ? 'block' : 'none';
    if (chevron) chevron.style.transform = pickerState[key] ? 'rotate(180deg)' : 'rotate(0)';
}

let _selectedProgramPrice = 0;
let _selectedProgramId    = null;

function selectProgram(id, name, price) {
    // deselect old
    if (_selectedProgramId) {
        const old = document.querySelector(`.p-row[data-id="${_selectedProgramId}"]`);
        if (old) { old.style.background = 'white'; old.querySelector('.p-check').style.display = 'none'; }
    }
    _selectedProgramId    = id;
    _selectedProgramPrice = price;
    document.getElementById('program_id').value = id;
    document.getElementById('program-trigger-text').textContent = name + ' — ' + price.toFixed(2) + ' ر.س';
    document.getElementById('program-trigger-text').style.color = '#111827';

    const row = document.querySelector(`.p-row[data-id="${id}"]`);
    if (row) { row.style.background = '#eff6ff'; row.querySelector('.p-check').style.display = 'block'; }

    pickerState['program'] = false;
    document.getElementById('program-picker').style.display = 'none';
    document.getElementById('program-chevron').style.transform = 'rotate(0)';
    updateSummary();
}

/* ── Payment Type toggle ───────────────────────────── */
function selectPaymentType(val) {
    document.getElementById('payment_type').value = val;
    ['full', 'installment'].forEach(v => {
        const el = document.getElementById('ptype-' + v);
        if (!el) return;
        const active = v === val;
        el.style.borderColor = active ? '#0071AA' : '#e5e7eb';
        el.style.background  = active ? '#eff6ff' : '#fff';
        el.querySelector('svg').style.color  = active ? '#0071AA' : '#9ca3af';
        el.querySelector('span').style.color = active ? '#0071AA' : '#374151';
    });
    const show = val === 'installment';
    document.getElementById('installment_options').style.display = show ? 'block' : 'none';
    if (show) generateInstallmentDates();
}

/* ── Installment dates generator ──────────────────── */
function generateInstallmentDates() {
    const n = parseInt(document.getElementById('number_of_installments').value) || 0;
    const startVal = document.getElementById('installment_start_date').value;
    const list = document.getElementById('installment-dates-list');

    if (!n || n < 2 || !startVal) { list.innerHTML = ''; return; }

    // Parse Arabic month names
    const arMonths = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];

    // Collect existing dates so editing one doesn't reset others
    const existingDates = {};
    list.querySelectorAll('input[type="date"]').forEach(inp => {
        const idx = inp.getAttribute('data-idx');
        if (idx !== null && inp.value) existingDates[idx] = inp.value;
    });

    let html = '';
    for (let i = 0; i < n; i++) {
        // Calculate default date: start + i months
        const d = new Date(startVal);
        d.setMonth(d.getMonth() + i);
        const defaultDate = d.toISOString().split('T')[0];
        const usedDate = existingDates[i] || defaultDate;

        // Display label
        const dispD = new Date(usedDate);
        const dayN  = dispD.getDate();
        const monN  = dispD.getMonth();
        const yrN   = dispD.getFullYear();

        html += `
        <div style="display:flex;align-items:center;gap:0.75rem;background:white;border:1.5px solid #bfdbfe;border-radius:10px;padding:0.6rem 0.9rem;">
            <div style="width:32px;height:32px;background:linear-gradient(135deg,#0071AA,#005a88);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <span style="color:white;font-size:.8rem;font-weight:800;">${i + 1}</span>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:.78rem;color:#6b7280;margin-bottom:2px;">القسط ${i + 1}</div>
                <input type="date" name="installment_dates[]" data-idx="${i}"
                       value="${usedDate}"
                       onchange="updateInstallmentLabel(this, ${i})"
                       style="width:100%;border:1px solid #e5e7eb;border-radius:7px;padding:5px 10px;font-size:.85rem;color:#111827;outline:none;cursor:pointer;"
                       onfocus="this.style.borderColor='#0071AA'" onblur="this.style.borderColor='#e5e7eb'">
            </div>
        </div>`;
    }

    list.innerHTML = html;
}

function updateInstallmentLabel(input, idx) {
    // just keep value — no extra action needed
}

/* ── Payment Method toggle ─────────────────────────── */
const methodColors = { '': '#9ca3af', cash: '#059669', bank_transfer: '#2563eb', tamara: '#7c3aed', waived: '#f59e0b' };
function selectPaymentMethod(val) {
    document.getElementById('payment_method').value = val;
    Object.keys(methodColors).forEach(v => {
        const el = document.getElementById('pmethod-' + (v || 'none'));
        if (!el) return;
        const active = v === val;
        el.style.borderColor = active ? '#0071AA' : '#e5e7eb';
        el.style.background  = active ? '#eff6ff' : '#fff';
        el.querySelector('svg').style.color  = active ? '#0071AA' : methodColors[v];
        el.querySelector('span').style.color = active ? '#0071AA' : '#374151';
    });
}

/* ── Close pickers on outside click ───────────────── */
document.addEventListener('click', function(e) {
    ['student', 'program'].forEach(key => {
        if (!e.target.closest(`#${key}-trigger`) && !e.target.closest(`#${key}-picker`)) {
            pickerState[key] = false;
            const picker  = document.getElementById(key + '-picker');
            const chevron = document.getElementById(key + '-chevron');
            if (picker)  picker.style.display = 'none';
            if (chevron) chevron.style.transform = 'rotate(0)';
        }
    });
});

/* ── Summary update ────────────────────────────────── */
function updateSummary() {
    const price    = _selectedProgramPrice || 0;
    const discount = parseFloat(document.querySelector('input[name="discount_amount"]').value || 0);
    const total    = Math.max(0, price - discount);
    document.getElementById('program_price').textContent    = price.toFixed(2) + ' ر.س';
    document.getElementById('discount_display').textContent = discount.toFixed(2) + ' ر.س';
    document.getElementById('total_required').textContent   = total.toFixed(2) + ' ر.س';
}

document.addEventListener('DOMContentLoaded', function() {
    // Pre-select student from query string (e.g. coming from student profile)
    const preselect = {{ $preselectedUserId ? (int)$preselectedUserId : 'null' }};
    const oldStudentId = document.getElementById('user_id').value || preselect;
    if (oldStudentId) {
        const row = document.querySelector(`.s-row[data-id="${oldStudentId}"]`);
        if (row) selectStudent(parseInt(oldStudentId), row.dataset.name, row.dataset.email);
    }

    // Restore / pre-select program
    const preselectProg = {{ $preselectedProgId ? (int)$preselectedProgId : 'null' }};
    const oldProgramId  = document.getElementById('program_id').value || preselectProg;
    if (oldProgramId) {
        const row = document.querySelector(`.p-row[data-id="${oldProgramId}"]`);
        if (row) selectProgram(parseInt(oldProgramId), row.dataset.name, parseFloat(row.dataset.price));
    }

    // Init payment type visual (also triggers generateInstallmentDates if installment)
    selectPaymentType(document.getElementById('payment_type').value || 'full');

    // Init payment method visual
    selectPaymentMethod(document.getElementById('payment_method').value || '');

    // Discount change
    document.querySelector('input[name="discount_amount"]').addEventListener('input', updateSummary);

    updateSummary();
});
</script>
@endpush
@endsection
