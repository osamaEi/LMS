@extends('layouts.dashboard')

@section('title', 'الاختبارات وحلول الطلاب')

@section('content')
<div style="direction:rtl;max-width:1200px;margin:0 auto;">

    {{-- Header --}}
    <div style="margin-bottom:20px;">
        <h1 style="font-size:20px;font-weight:800;color:#1e293b;margin:0;">الاختبارات والامتحانات</h1>
        <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">عرض جميع الاختبارات وحلول الطلاب بالتفصيل</p>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;">
        @php
        $cards = [
            ['إجمالي الاختبارات', $stats['total'], '#0071AA', '#e0f2fe'],
            ['الامتحانات', $stats['exams'], '#7c3aed', '#ede9fe'],
            ['الاختبارات القصيرة', $stats['quizzes'], '#16a34a', '#dcfce7'],
            ['محاولات مكتملة', $stats['attempts'], '#d97706', '#fef3c7'],
        ];
        @endphp
        @foreach($cards as [$label, $val, $c, $bg])
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px;box-shadow:0 1px 4px rgba(0,0,0,.04);">
            <div style="font-size:12px;color:#64748b;margin-bottom:6px;">{{ $label }}</div>
            <div style="font-size:24px;font-weight:800;color:{{ $c }};">{{ $val }}</div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:18px;">
        <input name="search" value="{{ $search }}" placeholder="بحث بعنوان الاختبار..." style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;min-width:220px;">
        <select name="type" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;">
            <option value="">كل الأنواع</option>
            <option value="quiz"     {{ $type=='quiz'?'selected':'' }}>اختبار قصير</option>
            <option value="exam"     {{ $type=='exam'?'selected':'' }}>امتحان</option>
            <option value="homework" {{ $type=='homework'?'selected':'' }}>واجب</option>
            <option value="paper"    {{ $type=='paper'?'selected':'' }}>ورقة أعمال</option>
        </select>
        <select name="subject_id" style="padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:13px;">
            <option value="">كل المواد</option>
            @foreach($subjects as $s)
            <option value="{{ $s->id }}" {{ (string)$subject===(string)$s->id?'selected':'' }}>{{ $s->name_ar }}</option>
            @endforeach
        </select>
        <button type="submit" style="padding:8px 16px;border-radius:9px;background:#1e293b;color:#fff;font-size:13px;font-weight:600;border:none;cursor:pointer;">بحث</button>
        @if($search || $type || $subject)
        <a href="{{ route('admin.quizzes.index') }}" style="padding:8px 14px;border-radius:9px;background:#f1f5f9;color:#64748b;font-size:13px;font-weight:600;text-decoration:none;">مسح</a>
        @endif
    </form>

    {{-- Table --}}
    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.04);">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:12px 16px;text-align:right;font-weight:700;color:#374151;">العنوان</th>
                    <th style="padding:12px 16px;text-align:right;font-weight:700;color:#374151;">المادة</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">النوع</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">الأسئلة</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">المحاولات</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">الحالة</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quizzes as $quiz)
                @php
                $typeMap = [
                    'quiz'=>['اختبار قصير','#dcfce7','#16a34a'],
                    'exam'=>['امتحان','#ede9fe','#7c3aed'],
                    'homework'=>['واجب','#fef3c7','#d97706'],
                    'paper'=>['ورقة أعمال','#dbeafe','#2563eb'],
                ];
                $t = $typeMap[$quiz->type] ?? [$quiz->type,'#f1f5f9','#64748b'];
                @endphp
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:12px 16px;font-weight:600;color:#1e293b;">{{ $quiz->title_ar }}</td>
                    <td style="padding:12px 16px;color:#64748b;">{{ $quiz->subject->name_ar ?? '—' }}</td>
                    <td style="padding:12px 16px;text-align:center;">
                        <span style="background:{{ $t[1] }};color:{{ $t[2] }};border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">{{ $t[0] }}</span>
                    </td>
                    <td style="padding:12px 16px;text-align:center;color:#64748b;">{{ $quiz->questions_count }}</td>
                    <td style="padding:12px 16px;text-align:center;">
                        <span style="font-weight:700;color:#0071AA;">{{ $quiz->completed_attempts_count }}</span>
                        <span style="color:#94a3b8;font-size:11px;"> / {{ $quiz->attempts_count }}</span>
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if($quiz->is_active)
                        <span style="background:#dcfce7;color:#16a34a;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">نشط</span>
                        @else
                        <span style="background:#f1f5f9;color:#64748b;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">غير نشط</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        <a href="{{ route('admin.quizzes.show', $quiz->id) }}" style="padding:5px 14px;font-size:11px;color:#0071AA;background:#e0f2fe;border:1px solid #bae6fd;border-radius:7px;font-weight:600;text-decoration:none;">عرض الحلول</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="padding:48px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد اختبارات</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($quizzes->hasPages())
    <div style="margin-top:16px;">{{ $quizzes->links() }}</div>
    @endif

</div>
@endsection
