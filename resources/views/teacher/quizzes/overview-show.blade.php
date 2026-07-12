@extends('layouts.dashboard')

@section('title', $quiz->title_ar . ' — الحلول')

@section('content')
<div style="direction:rtl;max-width:1200px;margin:0 auto;">

    <a href="{{ route('teacher.quizzes.overview') }}" style="display:inline-flex;align-items:center;gap:6px;color:#64748b;font-size:13px;text-decoration:none;margin-bottom:14px;">→ رجوع لكل الاختبارات</a>

    {{-- Header --}}
    <div style="background:linear-gradient(135deg,#0071AA,#004d77);border-radius:18px;padding:22px 26px;color:#fff;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-size:19px;font-weight:800;">{{ $quiz->title_ar }}</div>
            <div style="font-size:13px;opacity:.85;margin-top:6px;">
                {{ $quiz->subject->name_ar ?? $quiz->program->name_ar ?? '—' }} · {{ $quiz->type_label }}
                · {{ $quiz->questions->count() }} سؤال
                · الدرجة الكلية {{ rtrim(rtrim((string)$quiz->total_marks,'0'),'.') }}
                · النجاح {{ rtrim(rtrim((string)$quiz->pass_marks,'0'),'.') }}
            </div>
        </div>
        @if($quiz->questions->count())
        <a href="{{ route('teacher.quizzes.overview.pdf', $quiz->id) }}"
           style="display:inline-flex;align-items:center;gap:7px;background:#fff;color:#0071AA;border-radius:10px;padding:10px 18px;font-size:13px;font-weight:800;text-decoration:none;white-space:nowrap;box-shadow:0 2px 8px rgba(0,0,0,.12);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
            تحميل الأسئلة PDF
        </a>
        @endif
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:20px;">
        @php
        $cards = [
            ['الطلاب المستهدفون', $stats['eligible'], '#0071AA'],
            ['لم يدخلوا', $stats['not_attempted'], '#dc2626'],
            ['مكتملة', $stats['completed'], '#16a34a'],
            ['قيد التنفيذ', $stats['in_progress'], '#d97706'],
            ['متوسط النسبة', $stats['avg_percentage'].'%', '#0891b2'],
        ];
        @endphp
        @foreach($cards as [$label,$val,$c])
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px;text-align:center;">
            <div style="font-size:11px;color:#64748b;margin-bottom:5px;">{{ $label }}</div>
            <div style="font-size:20px;font-weight:800;color:{{ $c }};">{{ $val }}</div>
        </div>
        @endforeach
    </div>

    {{-- Questions --}}
    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.04);margin-bottom:20px;">
        <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#111827;">أسئلة الاختبار ({{ $quiz->questions->count() }})</div>
        @forelse($quiz->questions as $i => $q)
        <div style="padding:14px 18px;border-bottom:1px solid #f8fafc;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                <div style="font-size:13px;color:#1e293b;font-weight:600;flex:1;">
                    <span style="color:#94a3b8;">س{{ $i+1 }}.</span> {{ $q->question_ar ?: 'سؤال' }}
                    @if($q->question_en)
                    <div style="margin-top:4px;font-weight:600;color:#0071AA;" dir="ltr">{{ $q->question_en }}</div>
                    @endif
                    @if($q->image)
                    <div style="margin-top:8px;">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($q->image) }}" alt="صورة السؤال" style="max-width:100%;max-height:240px;border-radius:10px;border:1px solid #e5e7eb;">
                    </div>
                    @endif
                </div>
                <div style="display:flex;gap:6px;flex-shrink:0;">
                    <span style="background:#f1f5f9;color:#475569;border-radius:7px;padding:3px 9px;font-size:10px;font-weight:700;">{{ $q->type_label }}</span>
                    <span style="background:#e0f2fe;color:#0071AA;border-radius:7px;padding:3px 9px;font-size:10px;font-weight:700;">{{ rtrim(rtrim((string)$q->marks,'0'),'.') }} درجة</span>
                </div>
            </div>
            @if(in_array($q->type, ['multiple_choice','true_false']) && $q->options->count())
            <div style="margin-top:8px;display:flex;flex-direction:column;gap:4px;">
                @foreach($q->options as $opt)
                <div style="font-size:12px;color:{{ $opt->is_correct ? '#16a34a' : '#64748b' }};">
                    {{ $opt->is_correct ? '✓' : '•' }} {{ $opt->option_ar ?: $opt->option_en }}
                </div>
                @endforeach
            </div>
            @endif
        </div>
        @empty
        <div style="padding:32px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد أسئلة</div>
        @endforelse
    </div>

    {{-- All attempts --}}
    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.04);">
        <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#111827;">حلول الطلاب ({{ $attempts->count() }})</div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:11px 16px;text-align:right;font-weight:700;color:#374151;">الطالب</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">الدرجة</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">النسبة</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">النتيجة</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">الوقت</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">تاريخ التسليم</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attempts as $a)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:11px 16px;">
                        <div style="font-weight:600;color:#1e293b;">{{ $a->student->name ?? '—' }}</div>
                        <div style="font-size:11px;color:#94a3b8;" dir="ltr">{{ $a->student->email ?? '' }}</div>
                    </td>
                    <td style="padding:11px 16px;text-align:center;font-weight:700;color:#1e293b;">
                        {{ $a->submitted_at ? rtrim(rtrim((string)$a->score,'0'),'.') : '—' }}
                    </td>
                    <td style="padding:11px 16px;text-align:center;color:#64748b;">
                        {{ $a->submitted_at ? rtrim(rtrim((string)$a->percentage,'0'),'.').'%' : '—' }}
                    </td>
                    <td style="padding:11px 16px;text-align:center;">
                        @if(!$a->submitted_at)
                        <span style="background:#fef3c7;color:#d97706;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">قيد التنفيذ</span>
                        @else
                        <span style="background:#dcfce7;color:#16a34a;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">تم التصحيح</span>
                        @endif
                    </td>
                    <td style="padding:11px 16px;text-align:center;color:#64748b;font-family:monospace;">{{ $a->formatted_time_spent }}</td>
                    <td style="padding:11px 16px;text-align:center;color:#64748b;font-size:12px;">{{ $a->submitted_at?->format('Y/m/d H:i') ?? '—' }}</td>
                    <td style="padding:11px 16px;text-align:center;">
                        <a href="{{ route('teacher.quizzes.overview.attempt', [$quiz->id, $a->id]) }}"
                           style="padding:5px 14px;font-size:11px;color:#7c3aed;background:#f5f3ff;border:1px solid #e9d5ff;border-radius:7px;font-weight:600;text-decoration:none;">عرض الحل</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="padding:40px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد محاولات بعد</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
