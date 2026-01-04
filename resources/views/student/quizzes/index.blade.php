@extends('layouts.dashboard')

@section('title', 'الاختبارات')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(180deg, #0071AA 0%, #005a88 100%);">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold">الاختبارات</h1>
                <p class="mt-1" style="color: rgba(255,255,255,0.8);">جميع الاختبارات المتاحة لك</p>
            </div>
        </div>
    </div>

    <!-- Available Quizzes -->
    @if($availableQuizzes->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #10b981;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">الاختبارات المتاحة</h2>
                </div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($availableQuizzes as $quiz)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                                     style="background-color: {{ $quiz->type === 'exam' ? '#ef4444' : ($quiz->type === 'homework' ? '#f59e0b' : '#0071AA') }};">
                                    @if($quiz->type === 'exam')
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    @elseif($quiz->type === 'homework')
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $quiz->title_ar }}</h3>
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                              style="background-color: {{ $quiz->type === 'exam' ? '#fee2e2' : ($quiz->type === 'homework' ? '#fef3c7' : '#e6f4fa') }};
                                                     color: {{ $quiz->type === 'exam' ? '#991b1b' : ($quiz->type === 'homework' ? '#92400e' : '#0071AA') }};">
                                            {{ $quiz->type_label }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $quiz->subject->name }}</p>
                                    <div class="flex flex-wrap items-center gap-3 mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $quiz->questions_count }} سؤال
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $quiz->duration_minutes ?? '∞' }} دقيقة
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                            {{ $quiz->student_attempts }}/{{ $quiz->max_attempts }} محاولات
                                        </span>
                                    </div>
                                    @if($quiz->best_score !== null)
                                        <div class="mt-2">
                                            <span class="text-sm font-medium" style="color: #10b981;">
                                                أفضل درجة: {{ $quiz->best_score }}/{{ $quiz->total_marks }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                @if($quiz->in_progress_attempt)
                                    <a href="{{ route('student.quizzes.take', [$subject->id, $quiz->id]) }}"
                                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-white transition-all"
                                       style="background-color: #f59e0b;">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        متابعة
                                    </a>
                                @elseif($quiz->can_attempt)
                                    <a href="{{ route('student.quizzes.show', [$subject->id, $quiz->id]) }}"
                                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-white transition-all"
                                       style="background-color: #10b981;">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        ابدأ الاختبار
                                    </a>
                                @else
                                    <span class="px-4 py-2 rounded-xl font-medium" style="background-color: #f3f4f6; color: #6b7280;">
                                        استنفدت المحاولات
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Upcoming Quizzes -->
    @if($upcomingQuizzes->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #f59e0b;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">اختبارات قادمة</h2>
                </div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($upcomingQuizzes as $quiz)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #fef3c7;">
                                    <svg class="w-5 h-5" style="color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $quiz->title_ar }}</h3>
                                    <p class="text-sm text-gray-500">{{ $quiz->subject->name }}</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="text-sm text-gray-500">يبدأ في</p>
                                <p class="font-bold" style="color: #f59e0b;">{{ $quiz->starts_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Past Quizzes -->
    @if($pastQuizzes->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #6b7280;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">اختبارات سابقة</h2>
                </div>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($pastQuizzes as $quiz)
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #f3f4f6;">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-medium text-gray-700 dark:text-gray-300">{{ $quiz->title_ar }}</h3>
                                    <p class="text-sm text-gray-500">{{ $quiz->subject->name }}</p>
                                </div>
                            </div>
                            @if($quiz->best_score !== null)
                                <span class="font-bold" style="color: {{ $quiz->best_score >= $quiz->pass_marks ? '#10b981' : '#ef4444' }};">
                                    {{ $quiz->best_score }}/{{ $quiz->total_marks }}
                                </span>
                            @else
                                <span class="text-gray-500">لم تشارك</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Empty State -->
    @if($availableQuizzes->count() === 0 && $upcomingQuizzes->count() === 0 && $pastQuizzes->count() === 0)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-16 text-center">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6" style="background-color: #f3f4f6;">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">لا توجد اختبارات</h3>
            <p class="mt-2 text-gray-500 dark:text-gray-400">سيتم عرض الاختبارات هنا عند توفرها</p>
        </div>
    @endif
</div>
@endsection
