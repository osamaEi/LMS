@extends('layouts.dashboard')

@section('title', 'نتيجة الاختبار')

@section('content')
@php
    // The real maximum is the sum of the question marks on this attempt, which
    // is the true denominator for the score — the quiz's stored total_marks may
    // drift from the questions actually attached. Fall back to total_marks.
    $maxMarks = $attempt->answers->sum(fn($a) => $a->question->marks ?? 0);
    if ($maxMarks <= 0) {
        $maxMarks = $quiz->total_marks ?: 0;
    }
    $percentage = $maxMarks > 0 ? ($attempt->score / $maxMarks) * 100 : 0;

    // Manual questions (short answer / essay) may still be awaiting the teacher's
    // grade. Until then the pass/fail verdict isn't final.
    $pendingGrading = $attempt->answers->contains(fn($a) =>
        in_array($a->question->type ?? '', ['short_answer', 'essay']) && $a->marks_obtained === null
    );

    // The teacher must release the grade before the student sees it.
    $released = $attempt->isReleased();
@endphp
<div class="space-y-6">

@unless($released)
    {{-- Awaiting teacher review: hide grade and answers entirely. --}}
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.quizzes.program.show', [$program->id, $quiz->id]) }}"
               class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1">
                <p class="text-white/80 text-sm">{{ $program->name }} • {{ $quiz->title_ar }}</p>
                <h1 class="text-2xl font-bold">تم تسليم إجابتك</h1>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-10 text-center">
        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-5" style="background-color: #fef3c7;">
            <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">نتيجتك قيد المراجعة</h2>
        <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">تم استلام إجاباتك بنجاح. ستظهر لك الدرجة والإجابات الصحيحة بعد أن يعتمدها المدرب.</p>
        <a href="{{ route('student.quizzes.program.index', $program->id) }}"
           class="inline-block mt-6 px-6 py-2.5 rounded-xl font-bold text-white" style="background-color: #0071AA;">
            جميع الاختبارات
        </a>
    </div>
</div>
@else
    <!-- Header -->
    <div class="rounded-2xl p-6 text-white" style="background: linear-gradient(135deg, {{ $pendingGrading ? '#f59e0b, #d97706' : '#10b981, #059669' }});">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.quizzes.program.show', [$program->id, $quiz->id]) }}"
               class="w-10 h-10 rounded-xl flex items-center justify-center transition-colors"
               style="background-color: rgba(255,255,255,0.2);">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1">
                <p class="text-white/80 text-sm">{{ $program->name }} • {{ $quiz->title_ar }}</p>
                <h1 class="text-2xl font-bold">نتيجة الاختبار</h1>
            </div>
            <div class="text-left">
                <div class="w-20 h-20 rounded-full flex items-center justify-center" style="background-color: rgba(255,255,255,0.2);">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Summary -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 text-center">
            <div class="text-3xl font-bold mb-2" style="color: #0071AA;">
                {{ number_format($attempt->score, 1) }}
            </div>
            <p class="text-gray-500 dark:text-gray-400">الدرجة من {{ number_format($maxMarks, 1) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 text-center">
            <div class="text-3xl font-bold mb-2" style="color: #0071AA;">
                {{ number_format($percentage, 0) }}%
            </div>
            <p class="text-gray-500 dark:text-gray-400">النسبة المئوية</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 text-center">
            @if($pendingGrading)
            <div class="text-3xl font-bold mb-2" style="color: #f59e0b;">
                قيد التصحيح
            </div>
            <p class="text-gray-500 dark:text-gray-400">بانتظار تصحيح المدرس للأسئلة المقالية</p>
            @else
            <div class="text-3xl font-bold mb-2" style="color: #10b981;">
                تم التصحيح
            </div>
            <p class="text-gray-500 dark:text-gray-400">تم اعتماد نتيجتك</p>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-6 text-center">
            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                {{ $attempt->started_at->diffInMinutes($attempt->submitted_at) }}
            </div>
            <p class="text-gray-500 dark:text-gray-400">دقيقة (المدة)</p>
        </div>
    </div>

    {{-- Answer review — teacher-style read-only cards (result is released) --}}
    <div style="margin-top:4px;">
        <h2 style="font-size:16px;font-weight:800;color:#1e293b;margin-bottom:12px;">مراجعة إجاباتي</h2>

        @foreach($attempt->answers as $index => $answer)
        @php $q = $answer->question; @endphp
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:18px 22px;margin-bottom:14px;box-shadow:0 1px 4px rgba(0,0,0,.04);">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:12px;">
                <div style="font-weight:700;color:#1e293b;font-size:14px;">
                    <span style="color:#94a3b8;">س{{ $index+1 }}.</span> {{ $q->question_ar ?: 'سؤال' }}
                    @if($q->question_en)
                    <div style="margin-top:4px;color:#0071AA;" dir="ltr">{{ $q->question_en }}</div>
                    @endif
                </div>
                <span style="flex-shrink:0;background:#f1f5f9;color:#475569;border-radius:7px;padding:3px 10px;font-size:11px;font-weight:700;">{{ $q->type_label }}</span>
            </div>

            @if(in_array($q->type, ['multiple_choice','true_false']))
                @foreach($q->options as $opt)
                @php
                    $isCorrect  = $opt->is_correct;
                    $isSelected = $answer->selected_option_id == $opt->id;
                    $bg = $isCorrect ? '#f0fdf4' : ($isSelected ? '#fef2f2' : '#fff');
                    $bd = $isCorrect ? '#86efac' : ($isSelected ? '#fecaca' : '#e5e7eb');
                @endphp
                <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;border:1px solid {{ $bd }};background:{{ $bg }};border-radius:9px;margin-bottom:6px;">
                    <span style="font-size:13px;color:#1e293b;flex:1;">{{ $opt->option }}</span>
                    @if($isCorrect)<span style="font-size:11px;color:#16a34a;font-weight:700;">✓ الإجابة الصحيحة</span>@endif
                    @if($isSelected)<span style="font-size:11px;color:{{ $isCorrect ? '#16a34a' : '#dc2626' }};font-weight:700;">{{ $isCorrect ? '(إجابتك)' : '✗ إجابتك' }}</span>@endif
                </div>
                @endforeach
            @else
                <div style="margin-bottom:8px;">
                    <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:4px;">إجابتك</div>
                    <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:9px;padding:10px 12px;font-size:13px;color:#1e293b;white-space:pre-wrap;">{{ $answer->answer_text ?: '— لم تُجب —' }}</div>
                </div>
                @if($answer->teacher_feedback)
                <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:9px;padding:8px 12px;font-size:12px;color:#1d4ed8;">💬 ملاحظة المدرب: {{ $answer->teacher_feedback }}</div>
                @endif
            @endif

            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding-top:10px;border-top:1px solid #f1f5f9;">
                <span style="font-size:12px;color:#94a3b8;">الدرجة: {{ rtrim(rtrim((string)$q->marks,'0'),'.') }}</span>
                @php $obtained = $answer->marks_obtained; @endphp
                <span style="font-size:13px;font-weight:700;color:{{ $obtained !== null && $obtained > 0 ? '#16a34a' : '#dc2626' }};">
                    حصلت على: {{ $obtained !== null ? rtrim(rtrim((string)$obtained,'0'),'.') : '—' }}
                </span>
            </div>

            @if($q->explanation_ar)
            <div style="margin-top:10px;background:#fffbeb;border:1px solid #fde68a;border-radius:9px;padding:8px 12px;font-size:12px;color:#92400e;">📘 الشرح: {{ $q->explanation_ar }}</div>
            @endif
        </div>
        @endforeach
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-center gap-4">
        <a href="{{ route('student.quizzes.program.show', [$program->id, $quiz->id]) }}"
           class="px-6 py-2.5 rounded-xl font-medium text-gray-700 dark:text-gray-300 transition-colors"
           style="background-color: #f3f4f6;">
            العودة للاختبار
        </a>
        <a href="{{ route('student.quizzes.program.index', $program->id) }}"
           class="px-6 py-2.5 rounded-xl font-bold text-white transition-all"
           style="background-color: #0071AA;">
            جميع الاختبارات
        </a>
    </div>
</div>
@endunless
@endsection
