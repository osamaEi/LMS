@extends('layouts.dashboard')

@section('title', $quiz->title_ar)

@push('styles')
<style>
    /* Ensure Cairo font */
    * {
        font-family: 'Cairo', sans-serif !important;
    }

    .page-container {
        padding: 1.5rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .page-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004466 100%);
        border-radius: 24px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
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

    .quiz-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: white;
        margin-bottom: 0.5rem;
    }

    .quiz-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.875rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-type {
        background: rgba(255,255,255,0.2);
        color: white;
    }

    .badge-active {
        background: rgba(16, 185, 129, 0.3);
        color: #a7f3d0;
    }

    .badge-inactive {
        background: rgba(255,255,255,0.15);
        color: rgba(255,255,255,0.7);
    }

    .header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .header-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        background: rgba(255,255,255,0.15);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .header-btn:hover {
        background: rgba(255,255,255,0.25);
    }

    .header-btn-primary {
        background: white;
        color: #005a88;
        border: none;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .header-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        text-align: center;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 0.75rem;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.375rem;
        font-weight: 500;
    }

    /* Questions Card */
    .questions-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .questions-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fafbfc;
    }

    .questions-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .questions-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 28px;
        padding: 0 0.5rem;
        border-radius: 8px;
        background: #0071AA;
        color: white;
        font-size: 0.8rem;
        font-weight: 700;
    }

    .total-marks {
        font-size: 0.9rem;
        color: #6b7280;
        font-weight: 500;
    }

    .total-marks span {
        font-weight: 700;
        color: #0071AA;
    }

    /* Question Item */
    .question-item {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s ease;
    }

    .question-item:last-child {
        border-bottom: none;
    }

    .question-item:hover {
        background: #f9fafb;
    }

    .question-number {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        color: white;
        background: linear-gradient(135deg, #0071AA, #005a88);
        flex-shrink: 0;
    }

    .question-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .question-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #e6f4fa;
        color: #0071AA;
    }

    .question-marks {
        font-size: 0.85rem;
        color: #6b7280;
        font-weight: 500;
    }

    .question-marks span {
        font-weight: 700;
        color: #10b981;
    }

    .question-text {
        font-size: 1rem;
        color: #1f2937;
        line-height: 1.7;
        font-weight: 500;
    }

    .question-image {
        margin-top: 1rem;
        max-width: 400px;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    /* Options */
    .options-list {
        margin-top: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .option-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem 1rem;
        border-radius: 12px;
        background: #f9fafb;
        border: 2px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .option-item.correct {
        background: #ecfdf5;
        border-color: #a7f3d0;
    }

    .option-indicator {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .option-indicator.correct {
        background: #10b981;
        color: white;
    }

    .option-indicator.incorrect {
        border: 2px solid #d1d5db;
        background: white;
    }

    .option-text {
        font-size: 0.95rem;
        color: #374151;
    }

    .option-item.correct .option-text {
        color: #047857;
        font-weight: 600;
    }

    /* Explanation */
    .explanation-box {
        margin-top: 1rem;
        padding: 1rem 1.25rem;
        border-radius: 12px;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border: 1px solid #bfdbfe;
    }

    .explanation-label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #1e40af;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.375rem;
    }

    .explanation-text {
        font-size: 0.9rem;
        color: #1e3a8a;
        line-height: 1.6;
    }

    /* Action Buttons */
    .question-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .action-btn {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        background: transparent;
    }

    .action-btn.edit {
        color: #f59e0b;
    }

    .action-btn.edit:hover {
        background: #fef3c7;
        color: #b45309;
    }

    .action-btn.delete {
        color: #ef4444;
    }

    .action-btn.delete:hover {
        background: #fee2e2;
        color: #b91c1c;
    }

    /* Empty State */
    .empty-state {
        padding: 5rem 2rem;
        text-align: center;
    }

    .empty-icon {
        width: 88px;
        height: 88px;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
    }

    .empty-title {
        font-size: 1.375rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }

    .empty-text {
        color: #6b7280;
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }

    .empty-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border-radius: 12px;
        font-weight: 700;
        color: white;
        background: linear-gradient(135deg, #0071AA, #005a88);
        box-shadow: 0 4px 15px rgba(0, 113, 170, 0.25);
        transition: all 0.3s ease;
    }

    .empty-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 113, 170, 0.35);
    }

    /* Dark Mode */
    .dark .stat-card,
    .dark .questions-card {
        background: #1f2937;
        border-color: #374151;
    }

    .dark .stat-value,
    .dark .questions-title,
    .dark .question-text,
    .dark .empty-title {
        color: #f9fafb;
    }

    .dark .questions-header {
        background: #1a2332;
        border-color: #374151;
    }

    .dark .question-item {
        border-color: #374151;
    }

    .dark .question-item:hover {
        background: #374151;
    }

    .dark .option-item {
        background: #374151;
        border-color: #4b5563;
    }

    .dark .option-item.correct {
        background: rgba(16, 185, 129, 0.15);
        border-color: rgba(16, 185, 129, 0.3);
    }

    .dark .option-text {
        color: #d1d5db;
    }

    .dark .empty-icon {
        background: linear-gradient(135deg, #374151, #4b5563);
    }
</style>
@endpush

@section('content')
<div class="page-container">
    <!-- Header -->
    <div class="page-header">
        <div class="relative z-10">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-start gap-4">
                    <a href="{{ route('teacher.quizzes.index', $subject->id) }}" class="back-btn">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <p class="text-white/70 text-sm font-medium mb-1">{{ $subject->name }}</p>
                        <h1 class="quiz-title">{{ $quiz->title_ar }}</h1>
                        <div class="quiz-badges">
                            <span class="badge badge-type">
                                @if($quiz->type === 'exam')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @elseif($quiz->type === 'homework')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                @endif
                                {{ $quiz->type_label }}
                            </span>
                            @if($quiz->is_active)
                                <span class="badge badge-active">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    نشط
                                </span>
                            @else
                                <span class="badge badge-inactive">غير نشط</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="header-actions">
                    <a href="{{ route('teacher.quizzes.results', [$subject->id, $quiz->id]) }}" class="header-btn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        النتائج ({{ $quiz->attempts_count }})
                    </a>
                    <a href="{{ route('teacher.quizzes.edit', [$subject->id, $quiz->id]) }}" class="header-btn">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        تعديل
                    </a>
                    <a href="{{ route('teacher.quizzes.questions.create', [$subject->id, $quiz->id]) }}" class="header-btn header-btn-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        إضافة سؤال
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #e6f4fa, #cce9f5);">
                <svg class="w-6 h-6" style="color: #0071AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-value" style="color: #0071AA;">{{ $quiz->questions->count() }}</div>
            <div class="stat-label">عدد الأسئلة</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0);">
                <svg class="w-6 h-6" style="color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div class="stat-value" style="color: #10b981;">{{ $quiz->total_marks }}</div>
            <div class="stat-label">الدرجة الكلية</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                <svg class="w-6 h-6" style="color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-value" style="color: #f59e0b;">{{ $quiz->pass_marks }}</div>
            <div class="stat-label">درجة النجاح</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #e0e7ff, #c7d2fe);">
                <svg class="w-6 h-6" style="color: #6366f1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-value" style="color: #6366f1;">{{ $quiz->duration_minutes ?? '∞' }}</div>
            <div class="stat-label">المدة (دقيقة)</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #fce7f3, #fbcfe8);">
                <svg class="w-6 h-6" style="color: #ec4899;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div class="stat-value" style="color: #ec4899;">{{ $quiz->max_attempts }}</div>
            <div class="stat-label">المحاولات المسموحة</div>
        </div>
    </div>

    <!-- Questions List -->
    <div class="questions-card">
        <div class="questions-header">
            <div class="questions-title">
                <svg class="w-5 h-5" style="color: #0071AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                الأسئلة
                <span class="questions-count">{{ $quiz->questions->count() }}</span>
            </div>
            <div class="total-marks">
                مجموع الدرجات: <span>{{ $quiz->questions->sum('marks') }}</span>
            </div>
        </div>

        @if($quiz->questions->count() > 0)
            <div>
                @foreach($quiz->questions as $index => $question)
                    <div class="question-item">
                        <div class="flex items-start gap-4">
                            <div class="question-number">{{ $index + 1 }}</div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="question-meta">
                                            <span class="question-type-badge">
                                                @if($question->type === 'multiple_choice')
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                                    </svg>
                                                @elseif($question->type === 'true_false')
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                @endif
                                                {{ $question->type_label }}
                                            </span>
                                            <span class="question-marks">
                                                <span>{{ $question->marks }}</span> درجة
                                            </span>
                                        </div>
                                        <p class="question-text">{!! nl2br(e($question->question_ar)) !!}</p>

                                        @if($question->image)
                                            <img src="{{ Storage::url($question->image) }}" alt="صورة السؤال" class="question-image">
                                        @endif

                                        @if($question->options->count() > 0)
                                            <div class="options-list">
                                                @foreach($question->options as $option)
                                                    <div class="option-item {{ $option->is_correct ? 'correct' : '' }}">
                                                        <div class="option-indicator {{ $option->is_correct ? 'correct' : 'incorrect' }}">
                                                            @if($option->is_correct)
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                            @endif
                                                        </div>
                                                        <span class="option-text">{{ $option->option_ar }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($question->explanation_ar)
                                            <div class="explanation-box">
                                                <div class="explanation-label">الشرح</div>
                                                <p class="explanation-text">{{ $question->explanation_ar }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="question-actions">
                                        <a href="{{ route('teacher.quizzes.questions.edit', [$subject->id, $quiz->id, $question->id]) }}"
                                           class="action-btn edit" title="تعديل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('teacher.quizzes.questions.destroy', [$subject->id, $quiz->id, $question->id]) }}" method="POST"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="حذف">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="empty-title">لا توجد أسئلة بعد</h3>
                <p class="empty-text">ابدأ بإضافة أسئلة لهذا الاختبار</p>
                <a href="{{ route('teacher.quizzes.questions.create', [$subject->id, $quiz->id]) }}" class="empty-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إضافة سؤال
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
