@extends('layouts.dashboard')

@section('title', 'حل الطالب — ' . ($attempt->student->name ?? ''))

@section('content')
<div style="direction:rtl;max-width:900px;margin:0 auto;">

    <a href="{{ route('teacher.quizzes.overview.show', $quiz->id) }}" style="display:inline-flex;align-items:center;gap:6px;color:#64748b;font-size:13px;text-decoration:none;margin-bottom:14px;">→ رجوع لمحاولات الاختبار</a>

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

    @if(session('success'))
    <div style="background:#dcfce7;border:1px solid #86efac;color:#166534;border-radius:12px;padding:12px 16px;margin-bottom:14px;font-size:13px;">{{ session('success') }}</div>
    @endif

    @if($attempt->results_released_at)
    <div style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8;border-radius:12px;padding:12px 16px;margin-bottom:14px;font-size:13px;">
        ✅ تم اعتماد هذه النتيجة وإرسالها للطالب بتاريخ {{ $attempt->results_released_at->format('Y/m/d H:i') }}. يمكنك تعديل الدرجات وإعادة الاعتماد.
    </div>
    @else
    <div style="background:#fffbeb;border:1px solid #fde68a;color:#92400e;border-radius:12px;padding:12px 16px;margin-bottom:14px;font-size:13px;">
        ⏳ لم يتم اعتماد النتيجة بعد — الطالب لا يرى درجته أو إجاباته حتى تعتمدها.
    </div>
    @endif

    <form method="POST" action="{{ route('teacher.quizzes.overview.attempt.release', [$quiz->id, $attempt->id]) }}">
        @csrf

    {{-- Questions & answers --}}
    @foreach($questions as $idx => $q)
    @php $ans = $answersByQuestion[$q->id] ?? null; @endphp
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:18px 22px;margin-bottom:14px;box-shadow:0 1px 4px rgba(0,0,0,.04);">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:12px;">
            <div style="font-weight:700;color:#1e293b;font-size:14px;">
                <span style="color:#94a3b8;">س{{ $idx+1 }}.</span> {{ $q->question_ar ?: 'سؤال' }}
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
            <div style="margin-bottom:8px;">
                <div style="font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:4px;">إجابة الطالب</div>
                <div style="background:#f8fafc;border:1px solid #e5e7eb;border-radius:9px;padding:10px 12px;font-size:13px;color:#1e293b;white-space:pre-wrap;">{{ $ans->answer_text ?? '— لم يُجب —' }}</div>
            </div>
            {{-- Manual grading inputs (essay / short answer) --}}
            @if($ans)
            <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap;margin-top:8px;">
                <div>
                    <label style="display:block;font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:4px;">الدرجة (من {{ rtrim(rtrim((string)$q->marks,'0'),'.') }})</label>
                    <input type="number" name="marks[{{ $ans->id }}]" min="0" max="{{ $q->marks }}" step="0.5"
                           value="{{ $ans->marks_obtained !== null ? rtrim(rtrim((string)$ans->marks_obtained,'0'),'.') : '' }}"
                           style="width:120px;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;outline:none;">
                </div>
                <div style="flex:1;min-width:200px;">
                    <label style="display:block;font-size:11px;color:#94a3b8;font-weight:600;margin-bottom:4px;">ملاحظة للطالب (اختياري)</label>
                    <input type="text" name="feedback[{{ $ans->id }}]" value="{{ $ans->teacher_feedback }}"
                           style="width:100%;padding:8px 10px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;outline:none;">
                </div>
            </div>
            @endif
        @endif

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

        <div style="display:flex;justify-content:flex-end;gap:10px;margin-top:6px;">
            <button type="submit"
                    style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:11px;padding:12px 26px;font-size:14px;font-weight:800;cursor:pointer;box-shadow:0 4px 12px rgba(16,185,129,.3);">
                {{ $attempt->results_released_at ? 'حفظ الدرجات وإعادة الاعتماد' : 'حفظ الدرجات واعتماد النتيجة للطالب' }}
            </button>
        </div>
    </form>

</div>
@endsection
