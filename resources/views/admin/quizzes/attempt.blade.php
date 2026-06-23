@extends('layouts.dashboard')

@section('title', 'حل الطالب — ' . ($attempt->student->name ?? ''))

@section('content')
<div style="direction:rtl;max-width:900px;margin:0 auto;">

    <a href="{{ route('admin.quizzes.show', $quiz->id) }}" style="display:inline-flex;align-items:center;gap:6px;color:#64748b;font-size:13px;text-decoration:none;margin-bottom:14px;">→ رجوع لمحاولات الاختبار</a>

    {{-- Student / result header --}}
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:18px;padding:20px 24px;margin-bottom:20px;box-shadow:0 1px 6px rgba(0,0,0,.05);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
            <div>
                <div style="font-size:17px;font-weight:800;color:#1e293b;">{{ $attempt->student->name ?? '—' }}</div>
                <div style="font-size:12px;color:#94a3b8;" dir="ltr">{{ $attempt->student->email ?? '' }}</div>
                <div style="font-size:13px;color:#64748b;margin-top:8px;">
                    {{ $quiz->title_ar }} · {{ $quiz->subject->name_ar ?? '' }}
                </div>
            </div>
            <div style="text-align:center;">
                @if(!$attempt->submitted_at)
                <span style="background:#fef3c7;color:#d97706;border-radius:9999px;padding:.3rem 1rem;font-size:.75rem;font-weight:700;">قيد التنفيذ</span>
                @else
                <div style="font-size:28px;font-weight:800;color:{{ $attempt->passed ? '#16a34a' : '#dc2626' }};">
                    {{ rtrim(rtrim((string)$attempt->score,'0'),'.') }} / {{ rtrim(rtrim((string)$quiz->total_marks,'0'),'.') }}
                </div>
                <div style="font-size:13px;color:#64748b;">{{ rtrim(rtrim((string)$attempt->percentage,'0'),'.') }}% — {{ $attempt->passed ? 'ناجح' : 'راسب' }}</div>
                @endif
            </div>
        </div>
        <div style="display:flex;gap:18px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid #f1f5f9;font-size:12px;color:#64748b;">
            <span>⏱ الوقت: {{ $attempt->formatted_time_spent }}</span>
            <span>بدأ: {{ $attempt->started_at?->format('Y/m/d H:i') ?? '—' }}</span>
            <span>سلّم: {{ $attempt->submitted_at?->format('Y/m/d H:i') ?? '—' }}</span>
            @if($attempt->ip_address)<span dir="ltr">IP: {{ $attempt->ip_address }}</span>@endif
        </div>
    </div>

    {{-- Questions & answers --}}
    @foreach($questions as $idx => $q)
    @php $ans = $answersByQuestion[$q->id] ?? null; @endphp
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:18px 22px;margin-bottom:14px;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:12px;">
            <div style="font-weight:700;color:#1e293b;font-size:14px;">
                <span style="color:#94a3b8;">س{{ $idx+1 }}.</span> {{ $q->question_ar ?: ($q->question_en ?: 'سؤال بدون نص') }}
            </div>
            <span style="flex-shrink:0;background:#f1f5f9;color:#475569;border-radius:7px;padding:3px 10px;font-size:11px;font-weight:700;">{{ $q->type_label }}</span>
        </div>

        {{-- Multiple choice / true-false: show options with correctness + selection --}}
        @if(in_array($q->type, ['multiple_choice','true_false']))
            @foreach($q->options as $opt)
            @php
                $isCorrect  = $opt->is_correct;
                $isSelected = $ans && $ans->selected_option_id == $opt->id;
                $bg = $isCorrect ? '#f0fdf4' : ($isSelected ? '#fef2f2' : '#fff');
                $bd = $isCorrect ? '#86efac' : ($isSelected ? '#fecaca' : '#e5e7eb');
            @endphp
            <div style="display:flex;align-items:center;gap:10px;padding:9px 12px;border:1px solid {{ $bd }};background:{{ $bg }};border-radius:9px;margin-bottom:6px;">
                <span style="font-size:13px;color:#1e293b;flex:1;">{{ $opt->option_ar }}</span>
                @if($isCorrect)<span style="font-size:11px;color:#16a34a;font-weight:700;">✓ الإجابة الصحيحة</span>@endif
                @if($isSelected)<span style="font-size:11px;color:{{ $isCorrect ? '#16a34a' : '#dc2626' }};font-weight:700;">{{ $isCorrect ? '(اختيار الطالب)' : '✗ اختيار الطالب' }}</span>@endif
            </div>
            @endforeach
        @else
            {{-- Short answer / essay: show student's text --}}
            <div style="margin-bottom:8px;">
                <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:4px;">إجابة الطالب</div>
                <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:9px;padding:10px 12px;font-size:13px;color:#1e293b;white-space:pre-wrap;">{{ $ans->answer_text ?? '— لم يُجب —' }}</div>
            </div>
            @if($ans && $ans->teacher_feedback)
            <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:9px;padding:8px 12px;font-size:12px;color:#1d4ed8;">ملاحظة المعلّم: {{ $ans->teacher_feedback }}</div>
            @endif
        @endif

        {{-- Per-question score --}}
        <div style="display:flex;justify-content:space-between;align-items:center;margin-top:10px;padding-top:10px;border-top:1px solid #f1f5f9;">
            <span style="font-size:12px;color:#94a3b8;">الدرجة: {{ rtrim(rtrim((string)$q->marks,'0'),'.') }}</span>
            @php $obtained = $ans?->marks_obtained; @endphp
            <span style="font-size:13px;font-weight:700;color:{{ $obtained !== null && $obtained > 0 ? '#16a34a' : '#dc2626' }};">
                حصل على: {{ $obtained !== null ? rtrim(rtrim((string)$obtained,'0'),'.') : '—' }}
            </span>
        </div>

        @if($q->explanation_ar)
        <div style="margin-top:10px;background:#fffbeb;border:1px solid #fde68a;border-radius:9px;padding:8px 12px;font-size:12px;color:#92400e;">📘 الشرح: {{ $q->explanation_ar }}</div>
        @endif
    </div>
    @endforeach

</div>
@endsection
