@extends('layouts.dashboard')

@section('title', $quiz->title_ar)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #0071AA, #005a88);">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('teacher.quizzes.index', $subject->id) }}"
                   class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
                   style="background-color: rgba(255,255,255,0.2);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <p class="text-white/80 text-sm">{{ $subject->name }}</p>
                    <h1 class="text-2xl font-bold">{{ $quiz->title_ar }}</h1>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: rgba(255,255,255,0.2);">
                            {{ $quiz->type_label }}
                        </span>
                        @if($quiz->is_active)
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: rgba(16, 185, 129, 0.3);">نشط</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium" style="background-color: rgba(255,255,255,0.2);">غير نشط</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('teacher.quizzes.results', [$subject->id, $quiz->id]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-medium transition-all"
                   style="background-color: rgba(255,255,255,0.2);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    النتائج ({{ $quiz->attempts_count }})
                </a>
                <a href="{{ route('teacher.quizzes.edit', [$subject->id, $quiz->id]) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-medium transition-all"
                   style="background-color: rgba(255,255,255,0.2);">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    تعديل
                </a>
                <a href="{{ route('teacher.quizzes.questions.create', [$subject->id, $quiz->id]) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold transition-all"
                   style="background-color: white; color: #005a88;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إضافة سؤال
                </a>
            </div>
        </div>
    </div>

    <!-- Quiz Info -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">عدد الأسئلة</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $quiz->questions->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">الدرجة الكلية</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $quiz->total_marks }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">درجة النجاح</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $quiz->pass_marks }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">المدة</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $quiz->duration_minutes ?? '∞' }} <span class="text-sm font-normal">دقيقة</span></p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 text-center">
            <p class="text-sm text-gray-500 dark:text-gray-400">المحاولات</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $quiz->max_attempts }}</p>
        </div>
    </div>

    <!-- Questions List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">الأسئلة</h2>
            <span class="text-sm text-gray-500">مجموع الدرجات: {{ $quiz->questions->sum('marks') }}</span>
        </div>

        @if($quiz->questions->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($quiz->questions as $index => $question)
                    <div class="p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 font-bold text-white"
                                 style="background-color: #0071AA;">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                                  style="background-color: #e6f4fa; color: #0071AA;">
                                                {{ $question->type_label }}
                                            </span>
                                            <span class="text-sm text-gray-500">{{ $question->marks }} درجة</span>
                                        </div>
                                        <p class="text-gray-900 dark:text-white font-medium">{!! nl2br(e($question->question_ar)) !!}</p>

                                        @if($question->image)
                                            <img src="{{ Storage::url($question->image) }}" alt="صورة السؤال" class="mt-3 max-w-md rounded-lg">
                                        @endif

                                        @if($question->options->count() > 0)
                                            <div class="mt-4 space-y-2">
                                                @foreach($question->options as $option)
                                                    <div class="flex items-center gap-3 p-3 rounded-lg {{ $option->is_correct ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-gray-50 dark:bg-gray-700/50' }}">
                                                        @if($option->is_correct)
                                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                            </svg>
                                                        @else
                                                            <div class="w-5 h-5 rounded-full border-2 border-gray-300 dark:border-gray-500"></div>
                                                        @endif
                                                        <span class="{{ $option->is_correct ? 'text-green-700 dark:text-green-400 font-medium' : 'text-gray-700 dark:text-gray-300' }}">
                                                            {{ $option->option_ar }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($question->explanation_ar)
                                            <div class="mt-4 p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                                                <p class="text-sm text-blue-700 dark:text-blue-400">
                                                    <strong>الشرح:</strong> {{ $question->explanation_ar }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <a href="{{ route('teacher.quizzes.questions.edit', [$subject->id, $quiz->id, $question->id]) }}"
                                           class="p-2 rounded-lg text-gray-500 hover:text-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('teacher.quizzes.questions.destroy', [$subject->id, $quiz->id, $question->id]) }}" method="POST"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="p-2 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
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
            <div class="p-16 text-center">
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center mx-auto mb-6" style="background-color: #f3f4f6;">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">لا توجد أسئلة</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">ابدأ بإضافة أسئلة لهذا الاختبار</p>
                <a href="{{ route('teacher.quizzes.questions.create', [$subject->id, $quiz->id]) }}"
                   class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl font-bold transition-all text-white"
                   style="background-color: #0071AA;">
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
