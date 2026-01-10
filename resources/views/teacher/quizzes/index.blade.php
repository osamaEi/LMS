@extends('layouts.dashboard')

@section('title', 'الاختبارات - ' . $subject->name)

@push('styles')
<style>
    .page-container {
        padding: 2rem;
        max-width: 1400px;
        margin: 0 auto;
    }

    .header-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
        padding: 2rem;
        margin-bottom: 1.75rem;
    }

    .back-btn {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        transition: all 0.2s ease;
        border: 1px solid #e5e7eb;
    }

    .back-btn:hover {
        background: #e5e7eb;
        transform: translateX(2px);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 0.375rem;
        letter-spacing: -0.02em;
    }

    .page-subtitle {
        color: #6b7280;
        font-size: 1rem;
    }

    .create-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        padding: 0.875rem 1.75rem;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.95rem;
        color: white;
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 113, 170, 0.25);
    }

    .create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 113, 170, 0.35);
    }

    .stats-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #f3f4f6;
    }

    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .stat-item {
        text-align: center;
        padding: 1rem;
        border-radius: 14px;
        background: #f9fafb;
        transition: all 0.2s ease;
    }

    .stat-item:hover {
        background: #f3f4f6;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 0.375rem;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }

    .quizzes-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .quizzes-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #f3f4f6;
        background: #fafbfc;
    }

    .quizzes-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
    }

    .quiz-item {
        padding: 1.5rem 2rem;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f3f4f6;
    }

    .quiz-item:last-child {
        border-bottom: none;
    }

    .quiz-item:hover {
        background: #f9fafb;
    }

    .quiz-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .quiz-icon.quiz-type {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    }

    .quiz-icon.exam-type {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
    }

    .quiz-icon.homework-type {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
    }

    .quiz-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
        margin-left: 0.75rem;
    }

    .quiz-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.875rem;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-quiz {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-exam {
        background: #fee2e2;
        color: #b91c1c;
    }

    .badge-homework {
        background: #fef3c7;
        color: #b45309;
    }

    .badge-active {
        background: #d1fae5;
        color: #047857;
    }

    .badge-inactive {
        background: #f3f4f6;
        color: #6b7280;
    }

    .quiz-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1.5rem;
        margin-top: 0.875rem;
    }

    .quiz-meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
    }

    .action-btn {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .action-btn.view-btn {
        color: #6b7280;
    }

    .action-btn.view-btn:hover {
        background: #f3f4f6;
        color: #374151;
    }

    .action-btn.results-btn {
        color: #10b981;
    }

    .action-btn.results-btn:hover {
        background: #d1fae5;
        color: #047857;
    }

    .action-btn.edit-btn {
        color: #f59e0b;
    }

    .action-btn.edit-btn:hover {
        background: #fef3c7;
        color: #b45309;
    }

    .action-btn.delete-btn {
        color: #ef4444;
    }

    .action-btn.delete-btn:hover {
        background: #fee2e2;
        color: #b91c1c;
    }

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
        margin: 0 auto 1.75rem;
        background: linear-gradient(135deg, #e6f4fa, #cce9f5);
    }

    .empty-title {
        font-size: 1.375rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.625rem;
    }

    .empty-text {
        color: #6b7280;
        font-size: 1rem;
        margin-bottom: 2rem;
        max-width: 360px;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }

    .dark .header-card,
    .dark .quizzes-card {
        background: #1f2937;
        border-color: #374151;
    }

    .dark .back-btn {
        background: #374151;
        border-color: #4b5563;
    }

    .dark .back-btn:hover {
        background: #4b5563;
    }

    .dark .page-title,
    .dark .quizzes-title,
    .dark .quiz-title,
    .dark .empty-title {
        color: #f9fafb;
    }

    .dark .stat-item {
        background: #374151;
    }

    .dark .stat-item:hover {
        background: #4b5563;
    }

    .dark .quiz-item:hover {
        background: #374151;
    }

    .dark .quizzes-header {
        background: #1a2332;
        border-color: #374151;
    }

    .dark .quiz-item {
        border-color: #374151;
    }
</style>
@endpush

@section('content')
<div class="page-container">
    <!-- Header with Subject Info -->
    <div class="header-card">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div class="flex items-center gap-5">
                <a href="{{ route('teacher.my-subjects.show', $subject->id) }}" class="back-btn">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="page-title">{{ $subject->name }}</h1>
                    <p class="page-subtitle">إدارة الاختبارات والامتحانات</p>
                </div>
            </div>
            <a href="{{ route('teacher.quizzes.create', $subject->id) }}" class="create-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إنشاء اختبار جديد
            </a>
        </div>

        <!-- Stats -->
        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-value" style="color: #0071AA;">{{ $quizzes->count() }}</div>
                <div class="stat-label">إجمالي الاختبارات</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" style="color: #10b981;">{{ $quizzes->where('is_active', true)->count() }}</div>
                <div class="stat-label">اختبارات نشطة</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" style="color: #8b5cf6;">{{ $quizzes->sum('questions_count') }}</div>
                <div class="stat-label">إجمالي الأسئلة</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" style="color: #f59e0b;">{{ $quizzes->sum('attempts_count') }}</div>
                <div class="stat-label">محاولات الطلاب</div>
            </div>
        </div>
    </div>

    <!-- Quizzes List -->
    <div class="quizzes-card">
        <div class="quizzes-header">
            <h2 class="quizzes-title">قائمة الاختبارات</h2>
        </div>

        @if($quizzes->count() > 0)
            <div>
                @foreach($quizzes as $quiz)
                    <div class="quiz-item">
                        <div class="flex flex-col lg:flex-row lg:items-center gap-5">
                            <!-- Quiz Icon -->
                            <div class="quiz-icon {{ $quiz->type === 'exam' ? 'exam-type' : ($quiz->type === 'homework' ? 'homework-type' : 'quiz-type') }}">
                                @if($quiz->type === 'exam')
                                    <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                @elseif($quiz->type === 'homework')
                                    <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                @else
                                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                @endif
                            </div>

                            <!-- Quiz Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 flex-wrap mb-2">
                                    <h3 class="quiz-title">{{ $quiz->title_ar }}</h3>
                                    <span class="quiz-badge {{ $quiz->type === 'exam' ? 'badge-exam' : ($quiz->type === 'homework' ? 'badge-homework' : 'badge-quiz') }}">
                                        {{ $quiz->type_label }}
                                    </span>
                                    @if($quiz->is_active)
                                        <span class="quiz-badge badge-active">نشط</span>
                                    @else
                                        <span class="quiz-badge badge-inactive">غير نشط</span>
                                    @endif
                                </div>
                                <div class="quiz-meta">
                                    <span class="quiz-meta-item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $quiz->questions_count }} سؤال
                                    </span>
                                    <span class="quiz-meta-item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $quiz->duration_minutes ?? '∞' }} دقيقة
                                    </span>
                                    <span class="quiz-meta-item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        {{ $quiz->total_marks }} درجة
                                    </span>
                                    <span class="quiz-meta-item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $quiz->attempts_count }} محاولة
                                    </span>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <a href="{{ route('teacher.quizzes.show', [$subject->id, $quiz->id]) }}"
                                   class="action-btn view-btn" title="عرض">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('teacher.quizzes.results', [$subject->id, $quiz->id]) }}"
                                   class="action-btn results-btn" title="النتائج">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('teacher.quizzes.edit', [$subject->id, $quiz->id]) }}"
                                   class="action-btn edit-btn" title="تعديل">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('teacher.quizzes.destroy', [$subject->id, $quiz->id]) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا الاختبار؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete-btn" title="حذف">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <svg class="w-11 h-11" style="color: #0071AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="empty-title">لا توجد اختبارات بعد</h3>
                <p class="empty-text">
                    ابدأ بإنشاء أول اختبار لمادة <span class="font-semibold" style="color: #0071AA;">{{ $subject->name }}</span>
                </p>
                <a href="{{ route('teacher.quizzes.create', $subject->id) }}" class="create-btn">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إنشاء اختبار جديد
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
