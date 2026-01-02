@extends('layouts.dashboard')

@section('title', 'الاختبار: ' . $quiz->title_ar)

@push('styles')
<style>
    .quiz-timer {
        position: sticky;
        top: 1rem;
        z-index: 10;
    }
    .question-nav-btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .question-nav-btn.answered {
        background-color: #10b981;
        color: white;
    }
    .question-nav-btn.current {
        background-color: #8b5cf6;
        color: white;
    }
    .question-nav-btn.unanswered {
        background-color: #f3f4f6;
        color: #374151;
    }
    .dark .question-nav-btn.unanswered {
        background-color: #374151;
        color: #f3f4f6;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header with Timer -->
    <div class="quiz-timer">
        <div class="rounded-2xl p-4 text-white" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="text-white/80 text-sm">{{ $subject->name }}</p>
                    <h1 class="text-xl font-bold">{{ $quiz->title_ar }}</h1>
                </div>
                @if($quiz->duration_minutes)
                <div class="flex items-center gap-3 px-4 py-2 rounded-xl" style="background-color: rgba(255,255,255,0.2);">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-xs text-white/80">الوقت المتبقي</p>
                        <p id="timer" class="text-xl font-bold font-mono">--:--</p>
                    </div>
                </div>
                @endif
                <div class="text-left">
                    <p class="text-white/80 text-sm">السؤال</p>
                    <p class="text-xl font-bold"><span id="currentQuestion">1</span> / {{ $questions->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Questions -->
        <div class="lg:col-span-3">
            <form id="quizForm" action="{{ route('student.quizzes.submit', [$subject->id, $quiz->id]) }}" method="POST">
                @csrf

                @foreach($questions as $index => $question)
                <div class="question-card bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 mb-6 {{ $index === 0 ? '' : 'hidden' }}" data-question="{{ $index }}">
                    <!-- Question Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white" style="background-color: #8b5cf6;">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    @switch($question->type)
                                        @case('multiple_choice') اختيار من متعدد @break
                                        @case('true_false') صح أو خطأ @break
                                        @case('short_answer') إجابة قصيرة @break
                                        @case('essay') مقالي @break
                                    @endswitch
                                </span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 mx-2">•</span>
                                <span class="text-sm font-medium" style="color: #8b5cf6;">{{ $question->marks }} درجة</span>
                            </div>
                        </div>
                    </div>

                    <!-- Question Text -->
                    <div class="mb-6">
                        <p class="text-lg font-medium text-gray-900 dark:text-white leading-relaxed">{{ $question->question_text_ar }}</p>
                        @if($question->image_path)
                        <img src="{{ Storage::url($question->image_path) }}" alt="صورة السؤال" class="mt-4 rounded-xl max-w-full h-auto">
                        @endif
                    </div>

                    <!-- Answer Options -->
                    @if(in_array($question->type, ['multiple_choice', 'true_false']))
                        <div class="space-y-3">
                            @foreach($question->options as $option)
                            <label class="flex items-center gap-4 p-4 rounded-xl border-2 border-gray-200 dark:border-gray-700 cursor-pointer hover:border-purple-300 dark:hover:border-purple-600 transition-colors option-label">
                                <input type="radio"
                                       name="answers[{{ $question->id }}][option_id]"
                                       value="{{ $option->id }}"
                                       class="w-5 h-5 text-purple-600 focus:ring-purple-500"
                                       onchange="markAnswered({{ $index }})">
                                <span class="text-gray-900 dark:text-white">{{ $option->option_text_ar }}</span>
                            </label>
                            @endforeach
                        </div>
                    @elseif($question->type === 'short_answer')
                        <div>
                            <input type="text"
                                   name="answers[{{ $question->id }}][text]"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                                   placeholder="اكتب إجابتك هنا..."
                                   onchange="markAnswered({{ $index }})">
                        </div>
                    @elseif($question->type === 'essay')
                        <div>
                            <textarea name="answers[{{ $question->id }}][text]"
                                      rows="6"
                                      class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:border-purple-500 focus:ring-purple-500"
                                      placeholder="اكتب إجابتك التفصيلية هنا..."
                                      onchange="markAnswered({{ $index }})"></textarea>
                        </div>
                    @endif
                </div>
                @endforeach

                <!-- Navigation Buttons -->
                <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4">
                    <button type="button" id="prevBtn" onclick="navigateQuestion(-1)"
                            class="px-6 py-2.5 rounded-xl font-medium text-gray-700 dark:text-gray-300 transition-colors"
                            style="background-color: #f3f4f6;" disabled>
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            السابق
                        </span>
                    </button>

                    <button type="button" id="nextBtn" onclick="navigateQuestion(1)"
                            class="px-6 py-2.5 rounded-xl font-bold text-white transition-all"
                            style="background-color: #8b5cf6;">
                        <span class="flex items-center gap-2">
                            التالي
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </span>
                    </button>

                    <button type="button" id="submitBtn" onclick="confirmSubmit()"
                            class="px-6 py-2.5 rounded-xl font-bold text-white transition-all hidden"
                            style="background-color: #10b981;">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            تسليم الاختبار
                        </span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar - Question Navigation -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 sticky top-28">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">الأسئلة</h3>
                <div class="grid grid-cols-5 gap-2" id="questionNav">
                    @foreach($questions as $index => $question)
                    <button type="button"
                            class="question-nav-btn {{ $index === 0 ? 'current' : 'unanswered' }}"
                            onclick="goToQuestion({{ $index }})"
                            data-index="{{ $index }}">
                        {{ $index + 1 }}
                    </button>
                    @endforeach
                </div>

                <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between text-sm mb-2">
                        <span class="text-gray-600 dark:text-gray-400">تم الإجابة</span>
                        <span id="answeredCount" class="font-bold text-emerald-600">0</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">لم يتم الإجابة</span>
                        <span id="unansweredCount" class="font-bold text-red-600">{{ $questions->count() }}</span>
                    </div>
                </div>

                <button type="button" onclick="confirmSubmit()"
                        class="w-full mt-4 py-3 rounded-xl font-bold text-white transition-all"
                        style="background-color: #10b981;">
                    تسليم الاختبار
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div id="submitModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4" style="background-color: #fef3c7;">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">تأكيد التسليم</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-2">هل أنت متأكد من تسليم الاختبار؟</p>
                <p id="unansweredWarning" class="text-red-600 font-medium mb-4 hidden">
                    لديك <span id="unansweredNum">0</span> سؤال بدون إجابة
                </p>
                <div class="flex gap-3">
                    <button type="button" onclick="closeModal()"
                            class="flex-1 py-2.5 rounded-xl font-medium text-gray-700 dark:text-gray-300"
                            style="background-color: #f3f4f6;">
                        إلغاء
                    </button>
                    <button type="button" onclick="submitQuiz()"
                            class="flex-1 py-2.5 rounded-xl font-bold text-white"
                            style="background-color: #10b981;">
                        تأكيد التسليم
                    </button>
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

    @if($quiz->duration_minutes && $attempt->ends_at)
    const endTime = new Date('{{ $attempt->ends_at->toISOString() }}').getTime();

    function updateTimer() {
        const now = new Date().getTime();
        const remaining = endTime - now;

        if (remaining <= 0) {
            document.getElementById('timer').textContent = '00:00';
            submitQuiz();
            return;
        }

        const minutes = Math.floor(remaining / (1000 * 60));
        const seconds = Math.floor((remaining % (1000 * 60)) / 1000);

        document.getElementById('timer').textContent =
            String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');

        // Warning when 5 minutes left
        if (minutes < 5) {
            document.getElementById('timer').parentElement.style.backgroundColor = 'rgba(239, 68, 68, 0.3)';
        }
    }

    updateTimer();
    setInterval(updateTimer, 1000);
    @endif

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

        // Update navigation buttons state
        updateNavButtons(index);

        // Update question nav
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

    function updateNavButtons(index) {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        prevBtn.disabled = index === 0;
        prevBtn.style.opacity = index === 0 ? '0.5' : '1';

        if (index === totalQuestions - 1) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
    }

    function markAnswered(index) {
        answeredQuestions.add(index);

        const navBtn = document.querySelector(`.question-nav-btn[data-index="${index}"]`);
        navBtn.classList.remove('unanswered');
        navBtn.classList.add('answered');

        updateCounts();
    }

    function updateCounts() {
        document.getElementById('answeredCount').textContent = answeredQuestions.size;
        document.getElementById('unansweredCount').textContent = totalQuestions - answeredQuestions.size;
    }

    function confirmSubmit() {
        const unanswered = totalQuestions - answeredQuestions.size;
        const warningEl = document.getElementById('unansweredWarning');
        const numEl = document.getElementById('unansweredNum');

        if (unanswered > 0) {
            warningEl.classList.remove('hidden');
            numEl.textContent = unanswered;
        } else {
            warningEl.classList.add('hidden');
        }

        document.getElementById('submitModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('submitModal').classList.add('hidden');
    }

    function submitQuiz() {
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

    // Check for pre-filled answers (in case of page reload)
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[type="radio"]:checked, input[type="text"], textarea').forEach(function(input) {
            if (input.value) {
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
