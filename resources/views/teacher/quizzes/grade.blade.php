@extends('layouts.dashboard')

@section('title', 'تصحيح محاولة')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #0071AA, #005a88);">
        <div class="flex items-center gap-4">
            <a href="{{ route('teacher.quizzes.results', [$subject->id, $quiz->id]) }}"
               class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
               style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1">
                <p class="text-white/80 text-sm">{{ $quiz->title_ar }}</p>
                <h1 class="text-2xl font-bold">تصحيح محاولة {{ $attempt->student->name }}</h1>
            </div>
            <div class="text-left">
                <p class="text-white/80 text-sm">الدرجة الحالية</p>
                <p class="text-2xl font-bold">{{ $attempt->score ?? 0 }} / {{ $quiz->total_marks }}</p>
            </div>
        </div>
    </div>

    <!-- Student Info -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">الطالب</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $attempt->student->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">تاريخ البدء</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $attempt->started_at->format('Y/m/d H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">تاريخ التسليم</p>
                <p class="font-medium text-gray-900 dark:text-white">{{ $attempt->completed_at ? $attempt->completed_at->format('Y/m/d H:i') : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 dark:text-gray-400">المدة</p>
                <p class="font-medium text-gray-900 dark:text-white">
                    {{ $attempt->completed_at ? $attempt->started_at->diffInMinutes($attempt->completed_at) . ' دقيقة' : '-' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Answers -->
    <form action="{{ route('teacher.quizzes.submit-grade', [$subject->id, $quiz->id, $attempt->id]) }}" method="POST">
        @csrf

        <div class="space-y-6">
            @foreach($attempt->answers as $index => $answer)
            @php
                $question = $answer->question;
                $needsGrading = in_array($question->type, ['short_answer', 'essay']) && $answer->marks_obtained === null;
            @endphp
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 {{ $needsGrading ? 'ring-2 ring-amber-400' : '' }}">
                <!-- Question Header -->
                <div class="flex items-start gap-4 mb-4">
                    <span class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white flex-shrink-0"
                          style="background-color: {{ $answer->is_correct ? '#10b981' : ($answer->is_correct === null ? '#f59e0b' : '#ef4444') }};">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-sm text-gray-500">
                                @switch($question->type)
                                    @case('multiple_choice') اختيار من متعدد @break
                                    @case('true_false') صح أو خطأ @break
                                    @case('short_answer') إجابة قصيرة @break
                                    @case('essay') مقالي @break
                                @endswitch
                            </span>
                            <span class="text-sm font-medium" style="color: #0071AA;">{{ $question->marks }} درجة</span>
                            @if($needsGrading)
                            <span class="text-xs px-2 py-0.5 rounded-full" style="background-color: #fef3c7; color: #92400e;">
                                يحتاج تصحيح
                            </span>
                            @endif
                        </div>
                        <p class="text-gray-900 dark:text-white font-medium">{{ $question->question_text_ar }}</p>
                    </div>
                </div>

                <!-- Answer Display -->
                @if(in_array($question->type, ['multiple_choice', 'true_false']))
                    <div class="mr-14 space-y-2">
                        @foreach($question->options as $option)
                        <div class="flex items-center gap-3 p-3 rounded-xl"
                             style="{{ $option->is_correct ? 'background-color: #ecfdf5; border: 2px solid #10b981;' : ($answer->selected_option_id === $option->id && !$option->is_correct ? 'background-color: #fef2f2; border: 2px solid #ef4444;' : 'background-color: #f9fafb;') }}">
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
                            <span class="{{ $option->is_correct ? 'text-emerald-700 font-medium' : ($answer->selected_option_id === $option->id ? 'text-red-700' : 'text-gray-700') }}">
                                {{ $option->option_text_ar }}
                            </span>
                            @if($answer->selected_option_id === $option->id)
                                <span class="text-xs px-2 py-0.5 rounded-full mr-auto"
                                      style="background-color: {{ $option->is_correct ? '#d1fae5' : '#fee2e2' }}; color: {{ $option->is_correct ? '#065f46' : '#991b1b' }};">
                                    إجابة الطالب
                                </span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <div class="mr-14 mt-4 flex items-center gap-2">
                        <span class="text-sm text-gray-500">الدرجة:</span>
                        <span class="font-bold {{ $answer->is_correct ? 'text-emerald-600' : 'text-red-600' }}">
                            {{ $answer->marks_obtained ?? 0 }} / {{ $question->marks }}
                        </span>
                    </div>
                @else
                    <!-- Essay/Short Answer -->
                    <div class="mr-14">
                        <div class="p-4 rounded-xl mb-4" style="background-color: #f9fafb;">
                            <p class="text-sm text-gray-500 mb-2">إجابة الطالب:</p>
                            <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $answer->answer_text ?: 'لم يتم الإجابة' }}</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    الدرجة (من {{ $question->marks }})
                                </label>
                                <input type="number"
                                       name="grades[{{ $answer->id }}][marks]"
                                       value="{{ old('grades.' . $answer->id . '.marks', $answer->marks_obtained) }}"
                                       min="0"
                                       max="{{ $question->marks }}"
                                       step="0.5"
                                       class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    ملاحظات للطالب
                                </label>
                                <input type="text"
                                       name="grades[{{ $answer->id }}][feedback]"
                                       value="{{ old('grades.' . $answer->id . '.feedback', $answer->teacher_feedback) }}"
                                       class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                                       placeholder="اختياري...">
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Submit -->
        <div class="flex items-center justify-end gap-3 mt-6">
            <a href="{{ route('teacher.quizzes.results', [$subject->id, $quiz->id]) }}"
               class="px-6 py-2.5 rounded-xl font-medium text-gray-700 dark:text-gray-300"
               style="background-color: #f3f4f6;">
                إلغاء
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl font-bold text-white transition-all"
                    style="background-color: #10b981;">
                حفظ التصحيح
            </button>
        </div>
    </form>
</div>
@endsection
