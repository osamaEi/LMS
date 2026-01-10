@extends('layouts.dashboard')

@section('title', 'الاختبار: ' . $quiz->title_ar)

@push('styles')
<style>
    .quiz-container {
        min-height: calc(100vh - 200px);
    }

    .quiz-header {
        position: sticky;
        top: 0;
        z-index: 20;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .timer-box {
        background: linear-gradient(135deg, #0071AA, #005a88);
        animation: pulse-border 2s ease-in-out infinite;
    }

    .timer-box.warning {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        animation: pulse-warning 1s ease-in-out infinite;
    }

    @keyframes pulse-border {
        0%, 100% { box-shadow: 0 0 0 0 rgba(0, 113, 170, 0.4); }
        50% { box-shadow: 0 0 0 8px rgba(0, 113, 170, 0); }
    }

    @keyframes pulse-warning {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.6); }
        50% { box-shadow: 0 0 0 12px rgba(220, 38, 38, 0); }
    }

    .question-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        opacity: 1;
        transform: translateX(0);
    }

    .question-card.hidden {
        display: none;
    }

    .question-card.slide-out-left {
        opacity: 0;
        transform: translateX(-30px);
    }

    .question-card.slide-in-right {
        opacity: 0;
        transform: translateX(30px);
    }

    .option-card {
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        background: linear-gradient(white, white) padding-box,
                    linear-gradient(135deg, #e5e7eb, #f3f4f6) border-box;
    }

    .dark .option-card {
        background: linear-gradient(#1f2937, #1f2937) padding-box,
                    linear-gradient(135deg, #374151, #4b5563) border-box;
    }

    .option-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(0, 113, 170, 0.15);
        background: linear-gradient(white, white) padding-box,
                    linear-gradient(135deg, #0071AA, #00a8ff) border-box;
    }

    .dark .option-card:hover {
        background: linear-gradient(#1f2937, #1f2937) padding-box,
                    linear-gradient(135deg, #0071AA, #00a8ff) border-box;
    }

    .option-card.selected {
        background: linear-gradient(#f0f9ff, #e0f2fe) padding-box,
                    linear-gradient(135deg, #0071AA, #00a8ff) border-box;
        box-shadow: 0 8px 25px -5px rgba(0, 113, 170, 0.25);
    }

    .dark .option-card.selected {
        background: linear-gradient(#0c4a6e, #0369a1) padding-box,
                    linear-gradient(135deg, #0071AA, #00a8ff) border-box;
    }

    .option-card input[type="radio"] {
        appearance: none;
        -webkit-appearance: none;
        width: 24px;
        height: 24px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        transition: all 0.3s ease;
        position: relative;
        flex-shrink: 0;
    }

    .dark .option-card input[type="radio"] {
        border-color: #4b5563;
    }

    .option-card input[type="radio"]:checked {
        border-color: #0071AA;
        background-color: #0071AA;
    }

    .option-card input[type="radio"]:checked::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
    }

    .question-nav-btn {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        font-weight: 700;
        font-size: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .question-nav-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .question-nav-btn:hover::before {
        opacity: 1;
    }

    .question-nav-btn.answered {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 12px -2px rgba(16, 185, 129, 0.4);
    }

    .question-nav-btn.answered::after {
        content: '';
        position: absolute;
        top: 2px;
        right: 2px;
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
    }

    .question-nav-btn.current {
        background: linear-gradient(135deg, #0071AA, #005a88);
        color: white;
        box-shadow: 0 4px 12px -2px rgba(0, 113, 170, 0.4);
        transform: scale(1.1);
    }

    .question-nav-btn.unanswered {
        background-color: #f3f4f6;
        color: #6b7280;
    }

    .dark .question-nav-btn.unanswered {
        background-color: #374151;
        color: #9ca3af;
    }

    .question-nav-btn:hover {
        transform: scale(1.1);
    }

    .question-nav-btn.current:hover {
        transform: scale(1.15);
    }

    .progress-ring {
        transform: rotate(-90deg);
    }

    .progress-ring circle {
        transition: stroke-dashoffset 0.5s ease;
    }

    .nav-button {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .nav-button::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.2), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .nav-button:hover::after {
        opacity: 1;
    }

    .nav-button:active {
        transform: scale(0.97);
    }

    .sidebar-card {
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    /* True/False special styling */
    .tf-option {
        flex: 1;
        min-width: 120px;
    }

    .tf-option.true-option:hover,
    .tf-option.true-option.selected {
        background: linear-gradient(#ecfdf5, #d1fae5) padding-box,
                    linear-gradient(135deg, #10b981, #059669) border-box !important;
    }

    .tf-option.false-option:hover,
    .tf-option.false-option.selected {
        background: linear-gradient(#fef2f2, #fecaca) padding-box,
                    linear-gradient(135deg, #ef4444, #dc2626) border-box !important;
    }

    .dark .tf-option.true-option:hover,
    .dark .tf-option.true-option.selected {
        background: linear-gradient(#064e3b, #065f46) padding-box,
                    linear-gradient(135deg, #10b981, #059669) border-box !important;
    }

    .dark .tf-option.false-option:hover,
    .dark .tf-option.false-option.selected {
        background: linear-gradient(#7f1d1d, #991b1b) padding-box,
                    linear-gradient(135deg, #ef4444, #dc2626) border-box !important;
    }

    /* Input styling */
    .answer-input {
        transition: all 0.3s ease;
        border: 2px solid #e5e7eb;
    }

    .answer-input:focus {
        border-color: #0071AA;
        box-shadow: 0 0 0 4px rgba(0, 113, 170, 0.1);
    }

    .dark .answer-input {
        border-color: #374151;
    }

    .dark .answer-input:focus {
        border-color: #0071AA;
        box-shadow: 0 0 0 4px rgba(0, 113, 170, 0.2);
    }

    /* Modal animation */
    .modal-backdrop {
        transition: opacity 0.3s ease;
    }

    .modal-content {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modal-hidden .modal-backdrop {
        opacity: 0;
    }

    .modal-hidden .modal-content {
        opacity: 0;
        transform: scale(0.95) translateY(20px);
    }
</style>
@endpush

@section('content')
<div class="quiz-container px-4 sm:px-6 lg:px-8 py-6">
    <!-- Floating Header -->
    <div class="quiz-header mb-8">
        <div class="bg-white/95 dark:bg-gray-800/95 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-5 sm:p-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <!-- Quiz Info -->
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $subject->name_ar ?? $subject->name }}</p>
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white">{{ $quiz->title_ar }}</h1>
                    </div>
                </div>

                <!-- Timer -->
                @if($quiz->duration_minutes)
                <div id="timerBox" class="timer-box flex items-center gap-3 px-5 py-3 rounded-2xl text-white">
                    <div class="relative">
                        <svg class="progress-ring w-12 h-12" viewBox="0 0 48 48">
                            <circle cx="24" cy="24" r="20" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="4"/>
                            <circle id="timerRing" cx="24" cy="24" r="20" fill="none" stroke="white" stroke-width="4"
                                    stroke-dasharray="125.6" stroke-dashoffset="0" stroke-linecap="round"/>
                        </svg>
                        <svg class="w-5 h-5 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-white/70">الوقت المتبقي</p>
                        <p id="timer" class="text-2xl font-bold font-mono tracking-wider">--:--</p>
                    </div>
                </div>
                @endif

                <!-- Question Counter -->
                <div class="flex items-center gap-3 px-5 py-3 rounded-2xl bg-gray-100 dark:bg-gray-700">
                    <div class="text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400">السؤال الحالي</p>
                        <p class="text-2xl font-bold" style="color: #0071AA;">
                            <span id="currentQuestion">1</span>
                            <span class="text-gray-400 text-lg">/</span>
                            <span class="text-gray-500 text-lg">{{ $questions->count() }}</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-8 xl:col-span-9">
            <form id="quizForm" action="{{ route('student.quizzes.submit', [$subject->id, $quiz->id]) }}" method="POST">
                @csrf

                @foreach($questions as $index => $question)
                <div class="question-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden {{ $index === 0 ? '' : 'hidden' }}" data-question="{{ $index }}">
                    <!-- Question Header -->
                    <div class="px-6 py-5 sm:px-8 sm:py-6 border-b border-gray-100 dark:border-gray-700" style="background: linear-gradient(135deg, rgba(0,113,170,0.05), rgba(0,113,170,0.02));">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold text-xl text-white shadow-lg" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        @switch($question->type)
                                            @case('multiple_choice')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
                                                    </svg>
                                                    اختيار من متعدد
                                                </span>
                                                @break
                                            @case('true_false')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd"/>
                                                    </svg>
                                                    صح أو خطأ
                                                </span>
                                                @break
                                            @case('short_answer')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                                    </svg>
                                                    إجابة قصيرة
                                                </span>
                                                @break
                                            @case('essay')
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300">
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                                    </svg>
                                                    مقالي
                                                </span>
                                                @break
                                        @endswitch
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="inline-flex items-center gap-1 text-sm font-semibold" style="color: #0071AA;">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            {{ $question->marks }} درجة
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Question Content -->
                    <div class="px-6 py-6 sm:px-8 sm:py-8">
                        <!-- Question Text -->
                        <div class="mb-8">
                            <p class="text-xl sm:text-2xl font-medium text-gray-900 dark:text-white leading-relaxed">{{ $question->question_ar }}</p>
                            @if($question->image)
                            <div class="mt-4 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
                                <img src="{{ Storage::url($question->image) }}" alt="صورة السؤال" class="w-full h-auto">
                            </div>
                            @endif
                        </div>

                        <!-- Answer Options -->
                        @if($question->type === 'true_false')
                            <div class="flex gap-4 sm:gap-6 flex-wrap">
                                @foreach($question->options as $optIndex => $option)
                                <label class="tf-option option-card {{ $optIndex === 0 ? 'true-option' : 'false-option' }} flex items-center justify-center gap-3 p-6 sm:p-8 rounded-2xl cursor-pointer">
                                    <input type="radio"
                                           name="answers[{{ $question->id }}][option_id]"
                                           value="{{ $option->id }}"
                                           class="hidden"
                                           onchange="selectOption(this, {{ $index }})">
                                    @if($optIndex === 0)
                                    <svg class="w-6 h-6 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    @else
                                    <svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    @endif
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $option->option_ar }}</span>
                                </label>
                                @endforeach
                            </div>
                        @elseif($question->type === 'multiple_choice')
                            <div class="space-y-4">
                                @foreach($question->options as $optIndex => $option)
                                <label class="option-card flex items-center gap-4 p-5 sm:p-6 rounded-2xl cursor-pointer">
                                    <input type="radio"
                                           name="answers[{{ $question->id }}][option_id]"
                                           value="{{ $option->id }}"
                                           onchange="selectOption(this, {{ $index }})">
                                    <span class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex-shrink-0">
                                        {{ ['أ', 'ب', 'ج', 'د', 'هـ'][$optIndex] ?? ($optIndex + 1) }}
                                    </span>
                                    <span class="text-gray-900 dark:text-white text-lg flex-1">{{ $option->option_ar }}</span>
                                </label>
                                @endforeach
                            </div>
                        @elseif($question->type === 'short_answer')
                            <div>
                                <input type="text"
                                       name="answers[{{ $question->id }}][text]"
                                       class="answer-input w-full rounded-2xl px-5 py-4 text-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="اكتب إجابتك هنا..."
                                       oninput="markAnswered({{ $index }})">
                            </div>
                        @elseif($question->type === 'essay')
                            <div>
                                <textarea name="answers[{{ $question->id }}][text]"
                                          rows="8"
                                          class="answer-input w-full rounded-2xl px-5 py-4 text-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"
                                          placeholder="اكتب إجابتك التفصيلية هنا..."
                                          oninput="markAnswered({{ $index }})"></textarea>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4 inline-block ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                    يتم تصحيح هذا السؤال يدوياً من قبل المعلم
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Question Footer Navigation -->
                    <div class="px-6 py-5 sm:px-8 sm:py-6 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex items-center justify-between gap-4">
                            <button type="button" onclick="navigateQuestion(-1)"
                                    class="nav-button prev-btn px-5 py-3 sm:px-6 sm:py-3.5 rounded-xl font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 shadow-sm border border-gray-200 dark:border-gray-600 {{ $index === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                    {{ $index === 0 ? 'disabled' : '' }}>
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                    السابق
                                </span>
                            </button>

                            @if($index === $questions->count() - 1)
                            <button type="button" onclick="confirmSubmit()"
                                    class="nav-button px-6 py-3 sm:px-8 sm:py-3.5 rounded-xl font-bold text-white shadow-lg"
                                    style="background: linear-gradient(135deg, #10b981, #059669);">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    تسليم الاختبار
                                </span>
                            </button>
                            @else
                            <button type="button" onclick="navigateQuestion(1)"
                                    class="nav-button px-6 py-3 sm:px-8 sm:py-3.5 rounded-xl font-bold text-white shadow-lg"
                                    style="background: linear-gradient(135deg, #0071AA, #005a88);">
                                <span class="flex items-center gap-2">
                                    التالي
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </span>
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </form>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4 xl:col-span-3">
            <div class="sidebar-card bg-white/95 dark:bg-gray-800/95 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 sticky top-32">
                <!-- Progress -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">التقدم</h3>
                        <span id="progressPercent" class="text-sm font-bold" style="color: #0071AA;">0%</span>
                    </div>
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div id="progressBar" class="h-full rounded-full transition-all duration-500" style="background: linear-gradient(135deg, #10b981, #059669); width: 0%;"></div>
                    </div>
                </div>

                <!-- Question Navigation -->
                <div class="mb-8">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 mb-4">انتقل إلى سؤال</h3>
                    <div class="flex flex-wrap gap-3 justify-end" id="questionNav">
                        @foreach($questions as $index => $question)
                        <button type="button"
                                class="question-nav-btn {{ $index === 0 ? 'current' : 'unanswered' }}"
                                onclick="goToQuestion({{ $index }})"
                                data-index="{{ $index }}">
                            {{ $index + 1 }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <!-- Stats -->
                <div class="space-y-4 mb-8 p-5 rounded-xl bg-gray-50 dark:bg-gray-700/50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 rounded-full bg-emerald-500"></div>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">تم الإجابة</span>
                        </div>
                        <span id="answeredCount" class="text-lg font-bold text-emerald-600">0</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 rounded-full bg-gray-300 dark:bg-gray-600"></div>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">لم يتم الإجابة</span>
                        </div>
                        <span id="unansweredCount" class="text-lg font-bold text-gray-500">{{ $questions->count() }}</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="button" onclick="confirmSubmit()"
                        class="nav-button w-full py-4 rounded-xl font-bold text-white shadow-lg"
                        style="background: linear-gradient(135deg, #10b981, #059669);">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        تسليم الاختبار
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div id="submitModal" class="fixed inset-0 z-50 hidden modal-hidden">
    <div class="modal-backdrop absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="modal-content bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full p-8">
            <div class="text-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6" style="background: linear-gradient(135deg, #fef3c7, #fde68a);">
                    <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">تأكيد التسليم</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">هل أنت متأكد من تسليم الاختبار؟</p>
                <div id="unansweredWarning" class="hidden mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800">
                    <p class="text-red-600 dark:text-red-400 font-medium">
                        <svg class="w-5 h-5 inline-block ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        لديك <span id="unansweredNum" class="font-bold">0</span> سؤال بدون إجابة
                    </p>
                </div>
                <div class="flex gap-4">
                    <button type="button" onclick="closeModal()"
                            class="flex-1 py-3 rounded-xl font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        إلغاء
                    </button>
                    <button type="button" onclick="submitQuiz()"
                            class="flex-1 py-3 rounded-xl font-bold text-white transition-all shadow-lg"
                            style="background: linear-gradient(135deg, #10b981, #059669);">
                        تأكيد التسليم
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Time Expired Modal -->
<div id="timeExpiredModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl max-w-md w-full p-8">
            <div class="text-center">
                <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6" style="background: linear-gradient(135deg, #fee2e2, #fecaca);">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">انتهى الوقت!</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">لقد انتهى وقت الاختبار. سيتم تسليم إجاباتك تلقائياً.</p>
                <div class="flex items-center justify-center gap-2 text-lg font-bold text-red-600 mb-6">
                    <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    جاري التسليم...
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentQuestion = 0;
    const totalQuestions = {{ $questions->count() }};
    const answeredQuestions = new Set();
    let timerInterval = null;
    let isSubmitting = false;

    @if($quiz->duration_minutes)
        @if(isset($attempt) && $attempt->ends_at)
        const endTime = new Date('{{ $attempt->ends_at->toISOString() }}').getTime();
        @else
        // Fallback: calculate end time from quiz start + duration
        const endTime = new Date().getTime() + ({{ $quiz->duration_minutes }} * 60 * 1000);
        @endif
    const totalDuration = {{ $quiz->duration_minutes }} * 60 * 1000;
    const circumference = 125.6;

    function updateTimer() {
        if (isSubmitting) return;

        const now = new Date().getTime();
        const remaining = endTime - now;

        if (remaining <= 0) {
            // Time is up - show modal and auto submit
            clearInterval(timerInterval);
            document.getElementById('timer').textContent = '00:00';
            document.getElementById('timerRing').style.strokeDashoffset = circumference;
            showTimeExpiredModal();
            return;
        }

        const hours = Math.floor(remaining / (1000 * 60 * 60));
        const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((remaining % (1000 * 60)) / 1000);

        // Format time display
        let timeDisplay;
        if (hours > 0) {
            timeDisplay = String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        } else {
            timeDisplay = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        }
        document.getElementById('timer').textContent = timeDisplay;

        // Update progress ring
        const progress = remaining / totalDuration;
        const offset = circumference * (1 - progress);
        document.getElementById('timerRing').style.strokeDashoffset = offset;

        // Warning when 5 minutes left
        const timerBox = document.getElementById('timerBox');
        if (timerBox) {
            if (minutes < 5 && hours === 0) {
                timerBox.classList.add('warning');
            }
            // Critical warning when 1 minute left
            if (minutes < 1 && hours === 0) {
                timerBox.style.animation = 'pulse-warning 0.5s ease-in-out infinite';
            }
        }
    }

    function showTimeExpiredModal() {
        isSubmitting = true;
        window.onbeforeunload = null;
        document.getElementById('timeExpiredModal').classList.remove('hidden');

        // Auto submit after showing modal for 2 seconds
        setTimeout(function() {
            document.getElementById('quizForm').submit();
        }, 2000);
    }

    // Start timer
    updateTimer();
    timerInterval = setInterval(updateTimer, 1000);
    @endif

    function selectOption(input, questionIndex) {
        const card = input.closest('.question-card');
        card.querySelectorAll('.option-card').forEach(opt => opt.classList.remove('selected'));
        input.closest('.option-card').classList.add('selected');
        markAnswered(questionIndex);
    }

    function navigateQuestion(direction) {
        const newIndex = currentQuestion + direction;
        if (newIndex >= 0 && newIndex < totalQuestions) {
            goToQuestion(newIndex);
        }
    }

    function goToQuestion(index) {
        // Hide current question
        document.querySelectorAll('.question-card').forEach(card => card.classList.add('hidden'));

        // Show new question
        document.querySelector(`[data-question="${index}"]`).classList.remove('hidden');

        // Update question nav buttons
        document.querySelectorAll('.question-nav-btn').forEach(btn => {
            const btnIndex = parseInt(btn.dataset.index);
            btn.classList.remove('current');
            if (btnIndex === index) {
                btn.classList.add('current');
            }
        });

        currentQuestion = index;
        document.getElementById('currentQuestion').textContent = index + 1;
    }

    function markAnswered(index) {
        answeredQuestions.add(index);

        const navBtn = document.querySelector(`.question-nav-btn[data-index="${index}"]`);
        navBtn.classList.remove('unanswered');
        navBtn.classList.add('answered');

        updateCounts();
    }

    function updateCounts() {
        const answered = answeredQuestions.size;
        const unanswered = totalQuestions - answered;
        const percent = Math.round((answered / totalQuestions) * 100);

        document.getElementById('answeredCount').textContent = answered;
        document.getElementById('unansweredCount').textContent = unanswered;
        document.getElementById('progressPercent').textContent = percent + '%';
        document.getElementById('progressBar').style.width = percent + '%';
    }

    function confirmSubmit() {
        const unanswered = totalQuestions - answeredQuestions.size;
        const warningEl = document.getElementById('unansweredWarning');
        const numEl = document.getElementById('unansweredNum');
        const modal = document.getElementById('submitModal');

        if (unanswered > 0) {
            warningEl.classList.remove('hidden');
            numEl.textContent = unanswered;
        } else {
            warningEl.classList.add('hidden');
        }

        modal.classList.remove('hidden');
        setTimeout(() => modal.classList.remove('modal-hidden'), 10);
    }

    function closeModal() {
        const modal = document.getElementById('submitModal');
        modal.classList.add('modal-hidden');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    function submitQuiz() {
        if (isSubmitting) return;
        isSubmitting = true;
        window.onbeforeunload = null;
        if (timerInterval) clearInterval(timerInterval);
        document.getElementById('quizForm').submit();
    }

    // Prevent accidental navigation
    window.onbeforeunload = function() {
        return 'هل أنت متأكد من مغادرة الصفحة؟ سيتم فقدان إجاباتك غير المحفوظة.';
    };

    // Remove warning when submitting
    document.getElementById('quizForm').addEventListener('submit', function() {
        window.onbeforeunload = null;
    });

    // Check for pre-filled answers
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[type="radio"]:checked').forEach(function(input) {
            const card = input.closest('.question-card');
            if (card) {
                const index = parseInt(card.dataset.question);
                input.closest('.option-card').classList.add('selected');
                markAnswered(index);
            }
        });

        document.querySelectorAll('input[type="text"], textarea').forEach(function(input) {
            if (input.value.trim()) {
                const card = input.closest('.question-card');
                if (card) {
                    const index = parseInt(card.dataset.question);
                    markAnswered(index);
                }
            }
        });
    });
</script>
@endpush
@endsection
