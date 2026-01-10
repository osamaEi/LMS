@extends('layouts.dashboard')

@section('title', 'إنشاء اختبار جديد')

@push('styles')
<style>
    /* Header Styles */
    .page-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004466 100%);
        border-radius: 24px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 60%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Back Button */
    .back-btn {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .back-btn:hover {
        background: rgba(255,255,255,0.25);
        transform: translateX(4px);
    }

    /* Form Card */
    .form-card {
        background: white;
        border-radius: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 113, 170, 0.08);
        overflow: hidden;
    }

    .form-section {
        padding: 2rem;
    }

    .form-section + .form-section {
        border-top: 2px solid #f1f5f9;
    }

    .section-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-title .icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0071AA, #005a88);
    }

    .section-title .icon svg {
        width: 20px;
        height: 20px;
        color: white;
    }

    /* Form Group */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.5rem;
    }

    .form-group label .required {
        color: #ef4444;
        margin-right: 0.25rem;
    }

    /* Input Styles */
    .form-input {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        font-size: 0.95rem;
        color: #1e293b;
        background: #f8fafc;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: #0071AA;
        background: white;
        box-shadow: 0 0 0 4px rgba(0, 113, 170, 0.1);
    }

    .form-input::placeholder {
        color: #94a3b8;
    }

    .form-input.select {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: left 1rem center;
        background-size: 1.25rem;
        padding-left: 2.5rem;
    }

    .form-input.textarea {
        resize: vertical;
        min-height: 100px;
    }

    /* Option Cards */
    .option-card {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem;
        border-radius: 16px;
        border: 2px solid #e2e8f0;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .option-card:hover {
        border-color: #0071AA;
        background: rgba(0, 113, 170, 0.03);
    }

    .option-card.checked {
        border-color: #0071AA;
        background: rgba(0, 113, 170, 0.05);
    }

    .option-card input[type="checkbox"] {
        width: 22px;
        height: 22px;
        border-radius: 6px;
        border: 2px solid #cbd5e1;
        cursor: pointer;
        margin-top: 2px;
        accent-color: #0071AA;
    }

    .option-card .content {
        flex: 1;
    }

    .option-card .title {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .option-card .description {
        font-size: 0.8rem;
        color: #64748b;
    }

    /* Button Styles */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0071AA, #005a88);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 113, 170, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 113, 170, 0.4);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #64748b;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
        color: #475569;
    }

    /* Grid */
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .form-grid.cols-4 {
        grid-template-columns: repeat(4, 1fr);
    }

    @media (max-width: 1024px) {
        .form-grid.cols-4 {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    .col-span-2 {
        grid-column: span 2;
    }

    @media (max-width: 768px) {
        .col-span-2 {
            grid-column: span 1;
        }
    }

    /* Error Message */
    .error-message {
        margin-top: 0.5rem;
        font-size: 0.8rem;
        color: #ef4444;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* Timezone Badge */
    .timezone-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 50px;
        margin-right: auto;
    }

    /* Input Hint */
    .input-hint {
        margin-top: 0.375rem;
        font-size: 0.75rem;
        color: #64748b;
    }

    /* Current Time Display */
    .current-time-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1.25rem;
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        border: 1px solid #bae6fd;
        border-radius: 12px;
        color: #0369a1;
        font-size: 0.9rem;
    }

    .current-time-display svg {
        flex-shrink: 0;
    }

    /* Dark Mode */
    .dark .form-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.1);
    }

    .dark .form-section + .form-section {
        border-color: #334155;
    }

    .dark .section-title {
        color: #f1f5f9;
    }

    .dark .form-group label {
        color: #94a3b8;
    }

    .dark .form-input {
        background: #334155;
        border-color: #475569;
        color: #f1f5f9;
    }

    .dark .form-input:focus {
        background: #1e293b;
        border-color: #0071AA;
    }

    .dark .option-card {
        background: #334155;
        border-color: #475569;
    }

    .dark .option-card:hover {
        border-color: #0071AA;
        background: rgba(0, 113, 170, 0.1);
    }

    .dark .option-card .title {
        color: #f1f5f9;
    }

    .dark .option-card .description {
        color: #94a3b8;
    }

    .dark .btn-secondary {
        background: #334155;
        color: #94a3b8;
    }

    .dark .btn-secondary:hover {
        background: #475569;
        color: #f1f5f9;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="page-header">
        <div class="relative z-10">
            <div class="flex items-center gap-4">
                <a href="{{ route('teacher.quizzes.index', $subject->id) }}" class="back-btn">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <p class="text-white/70 text-sm font-medium">{{ $subject->name }}</p>
                    <h1 class="text-2xl lg:text-3xl font-bold text-white">إنشاء اختبار جديد</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('teacher.quizzes.store', $subject->id) }}" method="POST">
        @csrf

        @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200">
            <div class="flex items-center gap-3 mb-2">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="font-bold text-red-700">يوجد أخطاء في البيانات المدخلة:</h3>
            </div>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 border border-green-200">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-green-700">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <div class="form-card">
            <!-- Basic Info Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <span class="icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    المعلومات الأساسية
                </h2>

                <div class="form-grid">
                    <!-- Title Arabic -->
                    <div class="form-group">
                        <label for="title_ar">
                            عنوان الاختبار (عربي) <span class="required">*</span>
                        </label>
                        <input type="text" name="title_ar" id="title_ar" value="{{ old('title_ar') }}" required
                               class="form-input" placeholder="مثال: الاختبار الأول - الفصل الثاني">
                        @error('title_ar')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Title English -->
                    <div class="form-group">
                        <label for="title_en">
                            عنوان الاختبار (إنجليزي)
                        </label>
                        <input type="text" name="title_en" id="title_en" value="{{ old('title_en') }}"
                               class="form-input" placeholder="Example: First Quiz - Chapter 2">
                    </div>

                    <!-- Type -->
                    <div class="form-group">
                        <label for="type">
                            نوع الاختبار <span class="required">*</span>
                        </label>
                        <select name="type" id="type" required class="form-input select">
                            <option value="quiz" {{ old('type') === 'quiz' ? 'selected' : '' }}>اختبار قصير</option>
                            <option value="exam" {{ old('type') === 'exam' ? 'selected' : '' }}>امتحان نهائي</option>
                            <option value="homework" {{ old('type') === 'homework' ? 'selected' : '' }}>واجب منزلي</option>
                        </select>
                    </div>

                    <!-- Duration -->
                    <div class="form-group">
                        <label for="duration_minutes">
                            المدة (بالدقائق)
                        </label>
                        <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes') }}" min="1"
                               class="form-input" placeholder="اتركه فارغاً لوقت غير محدود">
                    </div>
                </div>
            </div>

            <!-- Grades Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <span class="icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </span>
                    الدرجات والمحاولات
                </h2>

                <div class="form-grid">
                    <!-- Total Marks -->
                    <div class="form-group">
                        <label for="total_marks">
                            الدرجة الكلية <span class="required">*</span>
                        </label>
                        <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', 100) }}" min="1" step="0.5" required
                               class="form-input" placeholder="100">
                    </div>

                    <!-- Pass Marks -->
                    <div class="form-group">
                        <label for="pass_marks">
                            درجة النجاح <span class="required">*</span>
                        </label>
                        <input type="number" name="pass_marks" id="pass_marks" value="{{ old('pass_marks', 50) }}" min="0" step="0.5" required
                               class="form-input" placeholder="50">
                    </div>

                    <!-- Max Attempts -->
                    <div class="form-group">
                        <label for="max_attempts">
                            عدد المحاولات المسموحة <span class="required">*</span>
                        </label>
                        <input type="number" name="max_attempts" id="max_attempts" value="{{ old('max_attempts', 1) }}" min="1" required
                               class="form-input" placeholder="1">
                    </div>
                </div>
            </div>

            <!-- Schedule Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <span class="icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    الجدولة الزمنية
                    <span class="timezone-badge">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        توقيت السعودية (UTC+3)
                    </span>
                </h2>

                <div class="form-grid">
                    <!-- Starts At -->
                    <div class="form-group">
                        <label for="starts_at">
                            تاريخ ووقت البداية
                        </label>
                        <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at') }}"
                               class="form-input">
                        <p class="input-hint">الوقت بتوقيت الرياض (Arabia Standard Time)</p>
                    </div>

                    <!-- Ends At -->
                    <div class="form-group">
                        <label for="ends_at">
                            تاريخ ووقت النهاية
                        </label>
                        <input type="datetime-local" name="ends_at" id="ends_at" value="{{ old('ends_at') }}"
                               class="form-input">
                        <p class="input-hint">الوقت بتوقيت الرياض (Arabia Standard Time)</p>
                    </div>
                </div>

                <!-- Current Saudi Time Display -->
                <div class="current-time-display">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>الوقت الحالي في السعودية:</span>
                    <span id="saudi-time" class="font-bold">{{ now()->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>

            <!-- Description Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <span class="icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </span>
                    الوصف والتعليمات
                </h2>

                <div class="form-group">
                    <label for="description_ar">
                        وصف الاختبار وتعليمات للطلاب
                    </label>
                    <textarea name="description_ar" id="description_ar" rows="4"
                              class="form-input textarea" placeholder="أدخل تعليمات الاختبار هنا...">{{ old('description_ar') }}</textarea>
                </div>
            </div>

            <!-- Options Section -->
            <div class="form-section">
                <h2 class="section-title">
                    <span class="icon" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    خيارات الاختبار
                </h2>

                <div class="form-grid cols-4">
                    <label class="option-card" onclick="this.classList.toggle('checked')">
                        <input type="checkbox" name="shuffle_questions" value="1" {{ old('shuffle_questions') ? 'checked' : '' }}>
                        <div class="content">
                            <span class="title">ترتيب عشوائي للأسئلة</span>
                            <span class="description">تغيير ترتيب الأسئلة لكل طالب</span>
                        </div>
                    </label>

                    <label class="option-card" onclick="this.classList.toggle('checked')">
                        <input type="checkbox" name="shuffle_answers" value="1" {{ old('shuffle_answers') ? 'checked' : '' }}>
                        <div class="content">
                            <span class="title">ترتيب عشوائي للإجابات</span>
                            <span class="description">تغيير ترتيب الخيارات لكل سؤال</span>
                        </div>
                    </label>

                    <label class="option-card checked" onclick="this.classList.toggle('checked')">
                        <input type="checkbox" name="show_results" value="1" {{ old('show_results', true) ? 'checked' : '' }} checked>
                        <div class="content">
                            <span class="title">عرض النتيجة</span>
                            <span class="description">عرض الدرجة مباشرة بعد التسليم</span>
                        </div>
                    </label>

                    <label class="option-card" onclick="this.classList.toggle('checked')">
                        <input type="checkbox" name="show_correct_answers" value="1" {{ old('show_correct_answers') ? 'checked' : '' }}>
                        <div class="content">
                            <span class="title">عرض الإجابات الصحيحة</span>
                            <span class="description">إظهار التصحيح للطالب</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Active Status & Submit -->
            <div class="form-section">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <label class="option-card checked" style="width: auto;" onclick="this.classList.toggle('checked')">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} checked>
                        <div class="content">
                            <span class="title">تفعيل الاختبار</span>
                            <span class="description">سيكون الاختبار متاحاً للطلاب</span>
                        </div>
                    </label>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('teacher.quizzes.index', $subject->id) }}" class="btn btn-secondary">
                            إلغاء
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            إنشاء الاختبار
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
