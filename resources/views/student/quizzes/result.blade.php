@extends('layouts.dashboard')

@section('title', 'نتيجة الاختبار')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, {{ $attempt->is_passed ? '#10b981, #059669' : '#ef4444, #dc2626' }});">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.quizzes.show', [$subject->id, $quiz->id]) }}"
               class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
               style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1">
                <p class="text-white/80 text-sm">{{ $subject->name }} • {{ $quiz->title_ar }}</p>
                <h1 class="text-2xl font-bold">نتيجة الاختبار</h1>
            </div>
            <div class="text-left">
                <div class="w-20 h-20 rounded-full flex items-center justify-center" style="background-color: rgba(255,255,255,0.2);">
                    @if($attempt->is_passed)
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @else
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Score Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 text-center">
            <div class="text-3xl font-bold mb-2" style="color: {{ $attempt->is_passed ? '#10b981' : '#ef4444' }};">
                {{ number_format($attempt->score, 1) }}
            </div>
            <p class="text-gray-500 dark:text-gray-400">الدرجة من {{ $quiz->total_marks }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 text-center">
            <div class="text-3xl font-bold mb-2" style="color: #8b5cf6;">
                {{ $quiz->total_marks > 0 ? number_format(($attempt->score / $quiz->total_marks) * 100, 0) : 0 }}%
            </div>
            <p class="text-gray-500 dark:text-gray-400">النسبة المئوية</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 text-center">
            <div class="text-3xl font-bold mb-2" style="color: {{ $attempt->is_passed ? '#10b981' : '#ef4444' }};">
                {{ $attempt->is_passed ? 'ناجح' : 'راسب' }}
            </div>
            <p class="text-gray-500 dark:text-gray-400">الحالة (النجاح: {{ $quiz->pass_marks }})</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 text-center">
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ $attempt->started_at->diffInMinutes($attempt->completed_at) }}
            </div>
            <p class="text-gray-500 dark:text-gray-400">دقيقة (المدة)</p>
        </div>
    </div>

    <!-- Detailed Results -->
    @if($quiz->show_correct_answers)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">مراجعة الإجابات</h2>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($attempt->answers as $index => $answer)
            @php
                $question = $answer->question;
                $isCorrect = $answer->is_correct;
            @endphp
            <div class="p-6">
                <!-- Question Header -->
                <div class="flex items-start gap-4 mb-4">
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white flex-shrink-0"
                          style="background-color: {{ $isCorrect ? '#10b981' : ($answer->is_correct === null ? '#f59e0b' : '#ef4444') }};">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm px-2 py-0.5 rounded-full"
                                  style="background-color: {{ $isCorrect ? '#d1fae5' : ($answer->is_correct === null ? '#fef3c7' : '#fee2e2') }}; color: {{ $isCorrect ? '#065f46' : ($answer->is_correct === null ? '#92400e' : '#991b1b') }};">
                                @if($isCorrect)
                                    إجابة صحيحة
                                @elseif($answer->is_correct === null)
                                    قيد التقييم
                                @else
                                    إجابة خاطئة
                                @endif
                            </span>
                            <span class="text-sm text-gray-500">
                                {{ $answer->marks_obtained ?? 0 }} / {{ $question->marks }} درجة
                            </span>
                        </div>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $question->question_text_ar }}</p>
                    </div>
                </div>

                <!-- Answer Details -->
                @if(in_array($question->type, ['multiple_choice', 'true_false']))
                    <div class="mr-14 space-y-2">
                        @foreach($question->options as $option)
                        <div class="flex items-center gap-3 p-3 rounded-xl {{ $option->is_correct ? 'border-2' : '' }}"
                             style="{{ $option->is_correct ? 'border-color: #10b981; background-color: #ecfdf5;' : ($answer->selected_option_id === $option->id && !$option->is_correct ? 'background-color: #fef2f2;' : 'background-color: #f9fafb;') }}">
                            @if($option->is_correct)
                                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            @elseif($answer->selected_option_id === $option->id)
                                <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            @else
                                <span class="w-5 h-5 rounded-full border-2 border-gray-300 flex-shrink-0"></span>
                            @endif
                            <span class="{{ $option->is_correct ? 'text-emerald-700 font-medium' : ($answer->selected_option_id === $option->id ? 'text-red-700' : 'text-gray-700 dark:text-gray-300') }}">
                                {{ $option->option_text_ar }}
                            </span>
                            @if($answer->selected_option_id === $option->id)
                                <span class="text-xs px-2 py-0.5 rounded-full mr-auto"
                                      style="background-color: {{ $option->is_correct ? '#d1fae5' : '#fee2e2' }}; color: {{ $option->is_correct ? '#065f46' : '#991b1b' }};">
                                    إجابتك
                                </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="mr-14">
                        <div class="p-4 rounded-xl" style="background-color: #f9fafb;">
                            <p class="text-sm text-gray-500 mb-1">إجابتك:</p>
                            <p class="text-gray-900 dark:text-white">{{ $answer->answer_text ?: 'لم يتم الإجابة' }}</p>
                        </div>
                        @if($answer->teacher_feedback)
                        <div class="mt-3 p-4 rounded-xl" style="background-color: #eff6ff;">
                            <p class="text-sm text-blue-600 mb-1">ملاحظات المدرس:</p>
                            <p class="text-blue-900">{{ $answer->teacher_feedback }}</p>
                        </div>
                        @endif
                    </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-8 text-center">
        <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: #f3f4f6;">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">مراجعة الإجابات غير متاحة</h3>
        <p class="text-gray-600 dark:text-gray-400">المدرس لم يسمح بعرض الإجابات الصحيحة لهذا الاختبار</p>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex items-center justify-center gap-4">
        <a href="{{ route('student.quizzes.show', [$subject->id, $quiz->id]) }}"
           class="px-6 py-2.5 rounded-xl font-medium text-gray-700 dark:text-gray-300 transition-colors"
           style="background-color: #f3f4f6;">
            العودة للاختبار
        </a>
        <a href="{{ route('student.quizzes.index', $subject->id) }}"
           class="px-6 py-2.5 rounded-xl font-bold text-white transition-all"
           style="background-color: #8b5cf6;">
            جميع الاختبارات
        </a>
    </div>
</div>
@endsection
