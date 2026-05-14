@extends('layouts.dashboard')

@section('title', 'مراجعة محاولة - ' . $attempt->student->name)

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #0071AA, #005a88);">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('teacher.quizzes.results', [$subject->id, $quiz->id]) }}"
                   class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors flex-shrink-0"
                   style="background-color: rgba(255,255,255,0.2);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <p class="text-white/70 text-sm">{{ $quiz->title_ar }}</p>
                    <h1 class="text-xl font-bold">مراجعة إجابات {{ $attempt->student->name }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-6 text-center">
                <div>
                    <p class="text-white/70 text-xs mb-0.5">الدرجة</p>
                    <p class="text-2xl font-bold">{{ number_format($attempt->score ?? 0, 1) }} / {{ $quiz->total_marks }}</p>
                </div>
                <div>
                    <p class="text-white/70 text-xs mb-0.5">النسبة</p>
                    <p class="text-2xl font-bold">{{ number_format($attempt->percentage ?? 0, 1) }}%</p>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-bold
                        {{ $attempt->passed ? 'bg-green-500 text-white' : 'bg-red-500 text-white' }}">
                        {{ $attempt->passed ? 'ناجح' : 'راسب' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Student Info --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <p class="text-gray-500 dark:text-gray-400 mb-0.5">الطالب</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $attempt->student->name }}</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400 mb-0.5">بدأ في</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $attempt->started_at?->format('Y/m/d H:i') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400 mb-0.5">سُلّم في</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $attempt->submitted_at?->format('Y/m/d H:i') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-gray-500 dark:text-gray-400 mb-0.5">الوقت المستغرق</p>
                <p class="font-semibold text-gray-900 dark:text-white">{{ $attempt->formatted_time_spent }}</p>
            </div>
        </div>
    </div>

    {{-- Answers --}}
    <div class="space-y-5">
        @forelse($attempt->answers->sortBy('question.order') as $index => $answer)
        @php
            $question   = $answer->question;
            $isAuto     = in_array($question->type, ['multiple_choice', 'true_false']);
            $isManual   = in_array($question->type, ['short_answer', 'essay']);
            $isCorrect  = $answer->is_correct;
            $marksObt   = $answer->marks_obtained ?? 0;
            $needsGrade = $isManual && $answer->marks_obtained === null;
        @endphp

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden
            {{ $needsGrade ? 'ring-2 ring-yellow-400' : '' }}">

            {{-- Question header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold text-white flex-shrink-0"
                          style="background:#0071AA;">{{ $index + 1 }}</span>
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full
                        {{ $isAuto ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                        @if($question->type === 'multiple_choice') اختيار متعدد
                        @elseif($question->type === 'true_false') صح/خطأ
                        @elseif($question->type === 'short_answer') إجابة قصيرة
                        @else مقالي
                        @endif
                    </span>
                    @if($needsGrade)
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-700">
                        يحتاج تصحيح
                    </span>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    @if(!$needsGrade)
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold
                        {{ $isCorrect ? 'text-green-600' : 'text-red-500' }}">
                        @if($isCorrect)
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            صحيح
                        @else
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                            خطأ
                        @endif
                    </span>
                    @endif
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-200">
                        {{ number_format($marksObt, 1) }} / {{ $question->marks }}
                    </span>
                </div>
            </div>

            {{-- Question body --}}
            <div class="px-6 py-5">

                {{-- Question text --}}
                <p class="text-gray-900 dark:text-white font-medium mb-4 leading-relaxed">
                    {{ $question->question_ar }}
                </p>

                {{-- Question image --}}
                @if($question->image)
                <img src="{{ asset('storage/'.$question->image) }}" alt="question"
                     class="rounded-xl mb-4 max-h-56 object-contain border border-gray-200">
                @endif

                {{-- MCQ / True-False options --}}
                @if($isAuto)
                <div class="space-y-2">
                    @foreach($question->options as $option)
                    @php
                        $isSelected = $answer->selected_option_id === $option->id;
                        $isRight    = $option->is_correct;
                    @endphp
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl border transition-colors
                        @if($isRight) border-green-300 bg-green-50 dark:bg-green-900/20
                        @elseif($isSelected && !$isRight) border-red-300 bg-red-50 dark:bg-red-900/20
                        @else border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30
                        @endif">
                        <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center flex-shrink-0
                            @if($isRight) border-green-500 bg-green-500
                            @elseif($isSelected && !$isRight) border-red-500 bg-red-500
                            @else border-gray-300
                            @endif">
                            @if($isRight || ($isSelected && !$isRight))
                            <span class="w-2 h-2 rounded-full bg-white"></span>
                            @endif
                        </span>
                        <span class="text-sm
                            @if($isRight) text-green-800 dark:text-green-300 font-semibold
                            @elseif($isSelected && !$isRight) text-red-700 dark:text-red-300 font-semibold
                            @else text-gray-700 dark:text-gray-300
                            @endif">
                            {{ $option->option_ar }}
                        </span>
                        @if($isSelected)
                        <span class="mr-auto text-xs font-bold
                            {{ $isRight ? 'text-green-600' : 'text-red-500' }}">
                            إجابة الطالب
                        </span>
                        @elseif($isRight)
                        <span class="mr-auto text-xs font-bold text-green-600">الإجابة الصحيحة</span>
                        @endif
                    </div>
                    @endforeach

                    @if(!$answer->selected_option_id)
                    <p class="text-sm text-gray-400 italic mt-1">لم يختر الطالب إجابة</p>
                    @endif
                </div>
                @endif

                {{-- Essay / Short answer --}}
                @if($isManual)
                <div class="rounded-xl border border-gray-200 dark:border-gray-600 p-4 bg-gray-50 dark:bg-gray-700/30 mb-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-medium">إجابة الطالب:</p>
                    @if($answer->answer_text)
                    <p class="text-gray-800 dark:text-gray-200 text-sm leading-relaxed whitespace-pre-wrap">{{ $answer->answer_text }}</p>
                    @else
                    <p class="text-gray-400 italic text-sm">لم يكتب الطالب إجابة</p>
                    @endif
                </div>

                @if($answer->teacher_feedback)
                <div class="rounded-xl border border-blue-200 bg-blue-50 dark:bg-blue-900/20 p-4 mb-4">
                    <p class="text-xs text-blue-600 font-medium mb-1">ملاحظة المعلم:</p>
                    <p class="text-sm text-blue-800 dark:text-blue-200">{{ $answer->teacher_feedback }}</p>
                </div>
                @endif

                {{-- Inline grade form for ungraded manual questions --}}
                @if($needsGrade)
                <form action="{{ route('teacher.quizzes.submit-grade', [$subject->id, $quiz->id, $attempt->id]) }}"
                      method="POST" class="flex items-end gap-3 mt-3">
                    @csrf
                    <input type="hidden" name="answer_id" value="{{ $answer->id }}">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                            الدرجة (من {{ $question->marks }})
                        </label>
                        <input type="number" name="grades[{{ $answer->id }}]"
                               min="0" max="{{ $question->marks }}" step="0.5"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                               placeholder="0">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">ملاحظة</label>
                        <input type="text" name="feedback[{{ $answer->id }}]"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm
                                      bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                               placeholder="اختياري">
                    </div>
                    <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-bold text-white flex-shrink-0"
                            style="background:#0071AA;">
                        حفظ
                    </button>
                </form>
                @endif
                @endif

                {{-- Explanation --}}
                @if($question->explanation_ar)
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <p class="text-xs text-gray-500 font-medium mb-1">التوضيح:</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $question->explanation_ar }}</p>
                </div>
                @endif

            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-12 text-center">
            <p class="text-gray-400">لا توجد إجابات مسجلة لهذه المحاولة</p>
        </div>
        @endforelse
    </div>

    {{-- Footer actions --}}
    <div class="flex items-center justify-between pt-2">
        <a href="{{ route('teacher.quizzes.results', [$subject->id, $quiz->id]) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                  bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة إلى النتائج
        </a>
        <a href="{{ route('teacher.quizzes.grade-attempt', [$subject->id, $quiz->id, $attempt->id]) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold text-white transition-colors"
           style="background:#0071AA;">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            صفحة التصحيح التفصيلية
        </a>
    </div>

</div>
@endsection
