@extends('layouts.dashboard')

@section('title', 'تذكرة جديدة')

@push('styles')
<style>
    .ticket-page { max-width: 900px; margin: 0 auto; }

    /* Header */
    .ticket-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .ticket-header::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .ticket-header::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -5%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    /* Card */
    .ticket-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .ticket-card { background: #1f2937; }

    .ticket-card-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .dark .ticket-card-head { border-color: #374151; }

    .ticket-card-title {
        font-size: 1rem;
        font-weight: 800;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .ticket-card-title { color: #f9fafb; }

    .ticket-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Form */
    .form-group { margin-bottom: 1.5rem; }
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.5rem;
    }
    .dark .form-label { color: #d1d5db; }

    .form-input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        font-size: 0.95rem;
        transition: all 0.2s;
        background: #fff;
        color: #111827;
    }
    .dark .form-input {
        background: #374151;
        border-color: #4b5563;
        color: #f9fafb;
    }
    .form-input:focus {
        outline: none;
        border-color: #0071AA;
        box-shadow: 0 0 0 3px rgba(0, 113, 170, 0.1);
    }
    .form-input::placeholder {
        color: #9ca3af;
    }

    .form-textarea {
        resize: vertical;
        min-height: 150px;
    }

    /* Category Cards */
    .category-options {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    @media (min-width: 768px) {
        .category-options { grid-template-columns: repeat(4, 1fr); }
    }

    .category-option {
        position: relative;
        cursor: pointer;
    }
    .category-option input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .category-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 1.25rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        transition: all 0.2s;
        background: #fff;
    }
    .dark .category-card {
        background: #374151;
        border-color: #4b5563;
    }
    .category-option input:checked + .category-card {
        border-color: #0071AA;
        background: #f0f9ff;
    }
    .dark .category-option input:checked + .category-card {
        background: rgba(0, 113, 170, 0.15);
    }
    .category-option:hover .category-card {
        border-color: #0071AA;
    }
    .category-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
    }
    .category-name {
        font-size: 0.875rem;
        font-weight: 700;
        color: #374151;
    }
    .dark .category-name { color: #e5e7eb; }

    /* Priority Options */
    .priority-options {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .priority-option {
        position: relative;
        flex: 1;
        min-width: 100px;
        cursor: pointer;
    }
    .priority-option input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .priority-card {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        transition: all 0.2s;
        background: #fff;
    }
    .dark .priority-card {
        background: #374151;
        border-color: #4b5563;
    }
    .priority-option:hover .priority-card {
        border-color: currentColor;
    }
    .priority-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .priority-name {
        font-size: 0.875rem;
        font-weight: 700;
    }

    .priority-low .priority-card { color: #059669; }
    .priority-low .priority-dot { background: #10b981; }
    .priority-low input:checked + .priority-card {
        border-color: #10b981;
        background: #ecfdf5;
    }
    .dark .priority-low input:checked + .priority-card {
        background: rgba(16, 185, 129, 0.15);
    }

    .priority-medium .priority-card { color: #d97706; }
    .priority-medium .priority-dot { background: #f59e0b; }
    .priority-medium input:checked + .priority-card {
        border-color: #f59e0b;
        background: #fffbeb;
    }
    .dark .priority-medium input:checked + .priority-card {
        background: rgba(245, 158, 11, 0.15);
    }

    .priority-high .priority-card { color: #dc2626; }
    .priority-high .priority-dot { background: #ef4444; }
    .priority-high input:checked + .priority-card {
        border-color: #ef4444;
        background: #fef2f2;
    }
    .dark .priority-high input:checked + .priority-card {
        background: rgba(239, 68, 68, 0.15);
    }

    /* Tips Card */
    .tips-card {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        border: 1px solid #bae6fd;
    }
    .dark .tips-card {
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.1) 0%, rgba(0, 113, 170, 0.05) 100%);
        border-color: rgba(0, 113, 170, 0.3);
    }
    .tips-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .tips-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #0071AA;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tips-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #0369a1;
    }
    .dark .tips-title { color: #38bdf8; }
    .tips-list {
        display: grid;
        gap: 0.75rem;
    }
    .tip-item {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        font-size: 0.875rem;
        color: #0369a1;
    }
    .dark .tip-item { color: #7dd3fc; }
    .tip-bullet {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: rgba(0, 113, 170, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 0.1rem;
    }
    .tip-bullet svg {
        width: 12px;
        height: 12px;
        color: #0071AA;
    }

    /* Buttons */
    .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        color: #fff;
        font-weight: 700;
        font-size: 0.95rem;
        border-radius: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(0, 113, 170, 0.25);
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0, 113, 170, 0.35);
    }

    .btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        background: #f3f4f6;
        color: #374151;
        font-weight: 700;
        font-size: 0.95rem;
        border-radius: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .dark .btn-secondary {
        background: #374151;
        color: #e5e7eb;
    }
    .btn-secondary:hover {
        background: #e5e7eb;
    }
    .dark .btn-secondary:hover {
        background: #4b5563;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid #f1f5f9;
    }
    .dark .form-actions { border-color: #374151; }

    /* Error */
    .form-error {
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: #dc2626;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    /* Back button */
    .back-btn {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255,255,255,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        color: #fff;
    }
    .back-btn:hover {
        background: rgba(255,255,255,0.25);
    }
</style>
@endpush

@section('content')
<div class="ticket-page space-y-6">
    <!-- Header -->
    <div class="ticket-header">
        <div class="flex items-center gap-4 relative z-10">
            <a href="{{ route('student.tickets.index') }}" class="back-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.15);">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold">تذكرة دعم جديدة</h1>
                <p class="opacity-75 text-sm mt-0.5">أرسل استفسارك وسنقوم بالرد عليك في أقرب وقت</p>
            </div>
        </div>
    </div>

    <form action="{{ route('student.tickets.store') }}" method="POST">
        @csrf

        <!-- Main Form Card -->
        <div class="ticket-card mb-6">
            <div class="ticket-card-head">
                <div class="ticket-card-title">
                    <div class="ticket-card-icon" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    تفاصيل التذكرة
                </div>
            </div>

            <div class="p-6">
                <!-- Subject -->
                <div class="form-group">
                    <label class="form-label">
                        عنوان التذكرة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required
                           class="form-input"
                           placeholder="مثال: مشكلة في تسجيل الدخول للمحاضرة">
                    @error('subject')
                        <p class="form-error">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Category -->
                <div class="form-group">
                    <label class="form-label">
                        تصنيف التذكرة <span class="text-red-500">*</span>
                    </label>
                    <div class="category-options">
                        <label class="category-option">
                            <input type="radio" name="category" value="technical" {{ old('category') == 'technical' ? 'checked' : '' }} required>
                            <div class="category-card">
                                <div class="category-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="category-name">دعم فني</span>
                            </div>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="academic" {{ old('category') == 'academic' ? 'checked' : '' }}>
                            <div class="category-card">
                                <div class="category-icon" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <span class="category-name">أكاديمي</span>
                            </div>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="financial" {{ old('category') == 'financial' ? 'checked' : '' }}>
                            <div class="category-card">
                                <div class="category-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="category-name">مالي</span>
                            </div>
                        </label>
                        <label class="category-option">
                            <input type="radio" name="category" value="other" {{ old('category') == 'other' ? 'checked' : '' }}>
                            <div class="category-card">
                                <div class="category-icon" style="background: linear-gradient(135deg, #6b7280, #4b5563);">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="category-name">أخرى</span>
                            </div>
                        </label>
                    </div>
                    @error('category')
                        <p class="form-error">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Priority -->
                <div class="form-group">
                    <label class="form-label">
                        أولوية التذكرة <span class="text-red-500">*</span>
                    </label>
                    <div class="priority-options">
                        <label class="priority-option priority-low">
                            <input type="radio" name="priority" value="low" {{ old('priority') == 'low' ? 'checked' : '' }} required>
                            <div class="priority-card">
                                <span class="priority-dot"></span>
                                <span class="priority-name">منخفضة</span>
                            </div>
                        </label>
                        <label class="priority-option priority-medium">
                            <input type="radio" name="priority" value="medium" {{ old('priority', 'medium') == 'medium' ? 'checked' : '' }}>
                            <div class="priority-card">
                                <span class="priority-dot"></span>
                                <span class="priority-name">متوسطة</span>
                            </div>
                        </label>
                        <label class="priority-option priority-high">
                            <input type="radio" name="priority" value="high" {{ old('priority') == 'high' ? 'checked' : '' }}>
                            <div class="priority-card">
                                <span class="priority-dot"></span>
                                <span class="priority-name">عالية</span>
                            </div>
                        </label>
                    </div>
                    @error('priority')
                        <p class="form-error">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">
                        تفاصيل المشكلة <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="6" required
                              class="form-input form-textarea"
                              placeholder="اشرح مشكلتك بالتفصيل لنتمكن من مساعدتك بشكل أفضل...

مثال:
- ما هي المشكلة التي تواجهها؟
- متى بدأت المشكلة؟
- ما هي الخطوات التي قمت بها؟">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="form-error">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Tips Card -->
        <div class="tips-card mb-6">
            <div class="tips-header">
                <div class="tips-icon">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <span class="tips-title">نصائح للحصول على رد سريع</span>
            </div>
            <div class="tips-list">
                <div class="tip-item">
                    <div class="tip-bullet">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span>اكتب عنوان واضح ومحدد يصف المشكلة</span>
                </div>
                <div class="tip-item">
                    <div class="tip-bullet">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span>اشرح الخطوات التي قمت بها قبل حدوث المشكلة</span>
                </div>
                <div class="tip-item">
                    <div class="tip-bullet">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span>أرفق أي رسائل خطأ ظهرت لك إن وجدت</span>
                </div>
                <div class="tip-item">
                    <div class="tip-bullet">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span>اختر التصنيف المناسب للحصول على دعم متخصص</span>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="ticket-card">
            <div class="p-6">
                <div class="form-actions" style="padding-top: 0; border-top: none;">
                    <a href="{{ route('student.tickets.index') }}" class="btn-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        إلغاء
                    </a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        إرسال التذكرة
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
