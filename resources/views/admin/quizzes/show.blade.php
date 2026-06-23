@extends('layouts.dashboard')

@section('title', $quiz->title_ar . ' — المحاولات')

@section('content')
<div style="direction:rtl;max-width:1200px;margin:0 auto;">

    {{-- Back --}}
    <a href="{{ route('admin.quizzes.index') }}" style="display:inline-flex;align-items:center;gap:6px;color:#64748b;font-size:13px;text-decoration:none;margin-bottom:14px;">→ رجوع لكل الاختبارات</a>

    {{-- Header --}}
    <div style="background:linear-gradient(135deg,#0071AA,#004d77);border-radius:18px;padding:22px 26px;color:#fff;margin-bottom:20px;">
        <div style="font-size:19px;font-weight:800;">{{ $quiz->title_ar }}</div>
        <div style="font-size:13px;opacity:.85;margin-top:6px;">
            {{ $quiz->subject->name_ar ?? '—' }} · {{ $quiz->type_label }}
            · {{ $quiz->questions->count() }} سؤال
            · الدرجة الكلية {{ rtrim(rtrim((string)$quiz->total_marks,'0'),'.') }}
            · النجاح {{ rtrim(rtrim((string)$quiz->pass_marks,'0'),'.') }}
            @if($quiz->creator) · أنشأه: {{ $quiz->creator->name }} @endif
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(6,1fr);gap:12px;margin-bottom:20px;">
        @php
        $cards = [
            ['الطلاب المستهدفون', $stats['eligible'], '#0071AA'],
            ['لم يدخلوا الاختبار', $stats['not_attempted'], '#dc2626'],
            ['مكتملة', $stats['completed'], '#16a34a'],
            ['قيد التنفيذ', $stats['in_progress'], '#d97706'],
            ['ناجحون', $stats['passed'], '#7c3aed'],
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
        <div style="padding:32px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد أسئلة لهذا الاختبار</div>
        @endforelse
    </div>

    {{-- Eligible students --}}
    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.04);margin-bottom:20px;">
        <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#111827;">الطلاب المستهدفون بالاختبار ({{ $eligibleStudents->count() }})</div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:11px 16px;text-align:right;font-weight:700;color:#374151;">الطالب</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">الحالة</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">الدرجة</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">النتيجة</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($eligibleStudents as $st)
                @php $at = $st->attempt; @endphp
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:11px 16px;">
                        <div style="font-weight:600;color:#1e293b;">{{ $st->name }}</div>
                        <div style="font-size:11px;color:#94a3b8;" dir="ltr">{{ $st->email }}</div>
                    </td>
                    <td style="padding:11px 16px;text-align:center;">
                        @if(!$at)
                        <span style="background:#fee2e2;color:#dc2626;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">لم يدخل</span>
                        @elseif(!$at->submitted_at)
                        <span style="background:#fef3c7;color:#d97706;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">قيد التنفيذ</span>
                        @else
                        <span style="background:#dcfce7;color:#16a34a;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">سلّم</span>
                        @endif
                    </td>
                    <td style="padding:11px 16px;text-align:center;color:#1e293b;font-weight:700;">
                        {{ $at && $at->submitted_at ? rtrim(rtrim((string)$at->score,'0'),'.') : '—' }}
                    </td>
                    <td style="padding:11px 16px;text-align:center;">
                        @if($at && $at->submitted_at)
                            @if($at->passed)
                            <span style="color:#16a34a;font-weight:700;font-size:12px;">ناجح ({{ rtrim(rtrim((string)$at->percentage,'0'),'.') }}%)</span>
                            @else
                            <span style="color:#dc2626;font-weight:700;font-size:12px;">راسب ({{ rtrim(rtrim((string)$at->percentage,'0'),'.') }}%)</span>
                            @endif
                        @else
                        <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td style="padding:11px 16px;text-align:center;">
                        @if($at)
                        <a href="{{ route('admin.quizzes.attempt', [$quiz->id, $at->id]) }}" style="padding:5px 14px;font-size:11px;color:#7c3aed;background:#f5f3ff;border:1px solid #e9d5ff;border-radius:7px;font-weight:600;text-decoration:none;">عرض الحل</a>
                        @else
                        <span style="color:#cbd5e1;font-size:11px;">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="padding:32px;text-align:center;color:#94a3b8;font-size:13px;">لا يوجد طلاب مستهدفون</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Attempts table --}}
    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.04);">
        <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;font-weight:700;color:#111827;">حلول الطلاب ({{ $attempts->count() }})</div>
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:11px 16px;text-align:right;font-weight:700;color:#374151;">الطالب</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">الدرجة</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">النسبة</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">النتيجة</th>
                    <th style="padding:11px 16px;text-align:center;font-weight:700;color:#374151;">الوقت المستغرق</th>
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
                    <td style="padding:11px 16px;text-align:center;color:#1e293b;font-weight:700;">
                        {{ $a->submitted_at ? rtrim(rtrim((string)$a->score,'0'),'.') : '—' }}
                    </td>
                    <td style="padding:11px 16px;text-align:center;color:#64748b;">
                        {{ $a->submitted_at ? rtrim(rtrim((string)$a->percentage,'0'),'.').'%' : '—' }}
                    </td>
                    <td style="padding:11px 16px;text-align:center;">
                        @if(!$a->submitted_at)
                        <span style="background:#fef3c7;color:#d97706;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">قيد التنفيذ</span>
                        @elseif($a->passed)
                        <span style="background:#dcfce7;color:#16a34a;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">ناجح</span>
                        @else
                        <span style="background:#fee2e2;color:#dc2626;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">راسب</span>
                        @endif
                    </td>
                    <td style="padding:11px 16px;text-align:center;color:#64748b;font-family:monospace;">{{ $a->formatted_time_spent }}</td>
                    <td style="padding:11px 16px;text-align:center;color:#64748b;font-size:12px;">{{ $a->submitted_at?->format('Y/m/d H:i') ?? '—' }}</td>
                    <td style="padding:11px 16px;text-align:center;">
                        <a href="{{ route('admin.quizzes.attempt', [$quiz->id, $a->id]) }}" style="padding:5px 14px;font-size:11px;color:#7c3aed;background:#f5f3ff;border:1px solid #e9d5ff;border-radius:7px;font-weight:600;text-decoration:none;">عرض الحل</a>
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
