@extends('layouts.dashboard')

@section('title', 'الاختبارات ')

@push('styles')
<style>
    .qz-wrap { direction:rtl; max-width:1200px; margin:0 auto; }

    /* Stats: 4 → 2 columns on small screens */
    .qz-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:20px; }

    /* Table scrolls horizontally rather than squashing */
    .qz-table-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .qz-table { width:100%; border-collapse:collapse; font-size:13px; min-width:920px; }

    /* Card list is the mobile rendering of the same rows */
    .qz-cards { display:none; }

    @media (max-width: 900px) {
        .qz-stats { grid-template-columns:repeat(2,1fr); }
    }

    @media (max-width: 720px) {
        /* Swap the table out for stacked cards */
        .qz-table-scroll { display:none; }
        .qz-cards { display:block; }

        .qz-header { flex-direction:column; align-items:flex-start !important; }
        .qz-header a { width:100%; text-align:center; }

        .qz-filters { flex-direction:column; align-items:stretch !important; }
        .qz-filters input, .qz-filters select, .qz-filters button { width:100%; }
    }

    .qz-card { border-bottom:1px solid #f1f5f9; padding:16px; }
    .qz-card:last-child { border-bottom:none; }
    .qz-card-row { display:flex; justify-content:space-between; gap:10px; font-size:12px; padding:3px 0; }
    .qz-card-label { color:#94a3b8; font-weight:600; flex-shrink:0; }
    .qz-card-val { color:#475569; text-align:left; }
</style>
@endpush

@section('content')
<div class="qz-wrap">

    {{-- Header --}}
    <div class="qz-header" style="background:linear-gradient(135deg,#0071AA,#004d77);border-radius:18px;padding:22px 26px;color:#fff;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
        <div>
            <div style="font-size:19px;font-weight:800;">الاختبارات </div>
            <div style="font-size:13px;opacity:.85;margin-top:4px;">جميع اختبارات مقرراتك ونتائج الطلاب</div>
        </div>
        <a href="{{ route('teacher.quizzes.create-global') }}"
           style="background:#fff;color:#0071AA;border-radius:10px;padding:10px 20px;font-size:14px;font-weight:800;text-decoration:none;white-space:nowrap;box-shadow:0 2px 8px rgba(0,0,0,.12);">
            + إنشاء اختبار
        </a>
    </div>

    {{-- Stats --}}
    <div class="qz-stats">
        @foreach([
            ['الاختبارات', $stats['total'], '#0071AA'],
            ['امتحانات', $stats['exams'], '#7c3aed'],
            ['كويز', $stats['quizzes'], '#16a34a'],
            ['محاولات مكتملة', $stats['attempts'], '#d97706'],
        ] as [$label,$val,$c])
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px;text-align:center;">
            <div style="font-size:11px;color:#64748b;margin-bottom:5px;">{{ $label }}</div>
            <div style="font-size:22px;font-weight:800;color:{{ $c }};">{{ $val }}</div>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" class="qz-filters" style="background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px 18px;margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
        <input type="text" name="search" value="{{ $search }}" placeholder="ابحث باسم الاختبار..."
               style="flex:1;min-width:180px;border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
        <select name="type" style="border:1.5px solid #e2e8f0;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;">
            <option value="">كل الأنواع</option>
            <option value="quiz" {{ $type==='quiz'?'selected':'' }}>اختبار قصير</option>
            <option value="midterm" {{ $type==='midterm'?'selected':'' }}>اختبار نصفي</option>
            <option value="exam" {{ $type==='exam'?'selected':'' }}>امتحان</option>
            <option value="homework" {{ $type==='homework'?'selected':'' }}>واجب</option>
        </select>
        <button type="submit" style="background:#0071AA;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:13px;font-weight:700;cursor:pointer;">بحث</button>
        @if($search || $type)
        <a href="{{ route('teacher.quizzes.overview') }}" style="color:#64748b;font-size:13px;text-decoration:none;padding:8px 12px;">مسح</a>
        @endif
    </form>

    {{-- Table --}}
    <div style="background:#fff;border-radius:16px;border:1px solid #e2e8f0;overflow:hidden;box-shadow:0 2px 12px rgba(0,0,0,.04);">
        <div class="qz-table-scroll">
        <table class="qz-table">
            <thead>
                <tr style="background:#f8fafc;border-bottom:1px solid #e2e8f0;">
                    <th style="padding:12px 16px;text-align:right;font-weight:700;color:#374151;">الاختبار</th>
                    <th style="padding:12px 16px;text-align:right;font-weight:700;color:#374151;">المقرر / البرنامج</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">الفصل المستهدف</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">النوع</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">التوقيت</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">الأسئلة</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">المحاولات</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">الحالة</th>
                    <th style="padding:12px 16px;text-align:center;font-weight:700;color:#374151;">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($quizzes as $quiz)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:12px 16px;">
                        <div style="font-weight:700;color:#1e293b;">{{ $quiz->title_ar }}</div>
                        @if($quiz->title_en)
                        <div style="font-size:11px;color:#94a3b8;" dir="ltr">{{ $quiz->title_en }}</div>
                        @endif
                        <div style="font-size:11px;color:#94a3b8;margin-top:2px;">
                            {{ $quiz->total_marks }} درجة · نجاح {{ $quiz->pass_marks }}
                        </div>
                    </td>
                    <td style="padding:12px 16px;color:#475569;font-size:12px;">{{ $quiz->subject->name_ar ?? $quiz->program->name_ar ?? '—' }}</td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if($quiz->programClass)
                            <span style="background:#eef2ff;color:#4338ca;border-radius:9999px;padding:.2rem .7rem;font-size:.7rem;font-weight:700;white-space:nowrap;">{{ $quiz->programClass->name }}</span>
                        @else
                            <span style="color:#cbd5e1;font-size:12px;">كل الفصول</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @php
                            $typeMap = ['quiz'=>['اختبار قصير','#e0f2fe','#0891b2'],'midterm'=>['اختبار نصفي','#fae8ff','#a21caf'],'exam'=>['امتحان','#f3e8ff','#7c3aed'],'homework'=>['واجب','#fef9c3','#ca8a04'],'paper'=>['ورقة','#f1f5f9','#475569']];
                            [$tl,$tbg,$tc] = $typeMap[$quiz->type] ?? [$quiz->type,'#f1f5f9','#475569'];
                        @endphp
                        <span style="background:{{ $tbg }};color:{{ $tc }};border-radius:9999px;padding:.2rem .7rem;font-size:.7rem;font-weight:700;white-space:nowrap;">{{ $tl }}</span>
                    </td>
                    <td style="padding:12px 16px;text-align:center;font-size:12px;color:#475569;">
                        @if($quiz->starts_at)
                            <div style="display:flex;align-items:center;justify-content:center;gap:4px;">
                                <span style="color:#16a34a;font-weight:700;">يبدأ</span>
                                <span dir="ltr">{{ $quiz->starts_at->format('Y/m/d') }}</span>
                                <span dir="ltr" style="color:#94a3b8;">{{ $quiz->starts_at->format('h:i A') }}</span>
                            </div>
                            @if($quiz->ends_at)
                            <div style="display:flex;align-items:center;justify-content:center;gap:4px;margin-top:3px;">
                                <span style="color:#dc2626;font-weight:700;">ينتهي</span>
                                <span dir="ltr">{{ $quiz->ends_at->format('Y/m/d') }}</span>
                                <span dir="ltr" style="color:#94a3b8;">{{ $quiz->ends_at->format('h:i A') }}</span>
                            </div>
                            @endif
                            @if($quiz->duration_minutes)
                            <div style="color:#6366f1;font-size:11px;margin-top:3px;">⏱ {{ $quiz->duration_minutes }} دقيقة</div>
                            @endif
                        @else
                            <span style="color:#cbd5e1;">— غير محدد —</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;color:#1e293b;font-weight:700;">{{ $quiz->questions_count }}</td>
                    <td style="padding:12px 16px;text-align:center;">
                        <span style="color:#1e293b;font-weight:700;">{{ $quiz->completed_count }}</span>
                        <span style="color:#94a3b8;font-size:11px;"> / {{ $quiz->attempts_count }}</span>
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        @if($quiz->is_active)
                            @if($quiz->starts_at && $quiz->starts_at > now())
                            <span style="background:#fef3c7;color:#d97706;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">قادم</span>
                            @elseif($quiz->ends_at && $quiz->ends_at < now())
                            <span style="background:#f1f5f9;color:#64748b;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">منتهي</span>
                            @else
                            <span style="background:#dcfce7;color:#16a34a;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">مفتوح</span>
                            @endif
                        @else
                        <span style="background:#fee2e2;color:#dc2626;border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;">موقوف</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;text-align:center;">
                        <div style="display:flex;gap:6px;justify-content:center;flex-wrap:wrap;">
                            @php $isProgramQuiz = $quiz->program_id !== null; @endphp
                            <a href="{{ route('teacher.quizzes.overview.show', $quiz->id) }}"
                               style="padding:5px 14px;font-size:11px;color:#0071AA;background:#e0f2fe;border:1px solid #bae6fd;border-radius:7px;font-weight:600;text-decoration:none;">عرض الحلول</a>
                            <a href="{{ $isProgramQuiz
                                    ? route('teacher.quizzes.program.edit', [$quiz->program_id, $quiz->id])
                                    : route('teacher.quizzes.edit', [$quiz->subject_id, $quiz->id]) }}"
                               style="padding:5px 14px;font-size:11px;color:#475569;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:7px;font-weight:600;text-decoration:none;">تعديل</a>
                            <form method="POST" action="{{ $isProgramQuiz
                                    ? route('teacher.quizzes.program.destroy', [$quiz->program_id, $quiz->id])
                                    : route('teacher.quizzes.destroy', [$quiz->subject_id, $quiz->id]) }}"
                                  onsubmit="return confirm('هل أنت متأكد من حذف الاختبار «{{ addslashes($quiz->title_ar) }}»؟ سيتم حذف جميع الأسئلة والمحاولات.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        style="padding:5px 14px;font-size:11px;color:#dc2626;background:#fee2e2;border:1px solid #fecaca;border-radius:7px;font-weight:600;cursor:pointer;">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" style="padding:48px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد اختبارات في مقرراتك</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>

        {{-- Mobile: the same rows as stacked cards --}}
        <div class="qz-cards">
            @forelse($quizzes as $quiz)
            @php
                $typeMap = ['quiz'=>['اختبار قصير','#e0f2fe','#0891b2'],'midterm'=>['اختبار نصفي','#fae8ff','#a21caf'],'exam'=>['امتحان','#f3e8ff','#7c3aed'],'homework'=>['واجب','#fef9c3','#ca8a04'],'paper'=>['ورقة','#f1f5f9','#475569']];
                [$tl,$tbg,$tc] = $typeMap[$quiz->type] ?? [$quiz->type,'#f1f5f9','#475569'];
                $isProgramQuiz = $quiz->program_id !== null;
                if (!$quiz->is_active)                              { $sl='موقوف'; $sbg='#fee2e2'; $sc='#dc2626'; }
                elseif ($quiz->starts_at && $quiz->starts_at > now()) { $sl='قادم'; $sbg='#fef3c7'; $sc='#d97706'; }
                elseif ($quiz->ends_at && $quiz->ends_at < now())     { $sl='منتهي'; $sbg='#f1f5f9'; $sc='#64748b'; }
                else                                                 { $sl='مفتوح'; $sbg='#dcfce7'; $sc='#16a34a'; }
            @endphp
            <div class="qz-card">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;margin-bottom:10px;">
                    <div style="min-width:0;">
                        <div style="font-weight:800;color:#1e293b;font-size:14px;">{{ $quiz->title_ar }}</div>
                        <div style="font-size:11px;color:#94a3b8;margin-top:2px;">{{ $quiz->total_marks }} درجة · نجاح {{ $quiz->pass_marks }}</div>
                    </div>
                    <span style="flex-shrink:0;background:{{ $sbg }};color:{{ $sc }};border-radius:9999px;padding:.18rem .7rem;font-size:.65rem;font-weight:700;white-space:nowrap;">{{ $sl }}</span>
                </div>

                <div class="qz-card-row">
                    <span class="qz-card-label">المقرر / البرنامج</span>
                    <span class="qz-card-val">{{ $quiz->subject->name_ar ?? $quiz->program->name_ar ?? '—' }}</span>
                </div>
                <div class="qz-card-row">
                    <span class="qz-card-label">الفصل المستهدف</span>
                    <span class="qz-card-val">{{ $quiz->programClass->name ?? 'كل الفصول' }}</span>
                </div>
                <div class="qz-card-row">
                    <span class="qz-card-label">النوع</span>
                    <span class="qz-card-val"><span style="background:{{ $tbg }};color:{{ $tc }};border-radius:9999px;padding:.15rem .6rem;font-size:.68rem;font-weight:700;white-space:nowrap;">{{ $tl }}</span></span>
                </div>
                <div class="qz-card-row">
                    <span class="qz-card-label">التوقيت</span>
                    <span class="qz-card-val">
                        @if($quiz->starts_at)
                            <span dir="ltr">{{ $quiz->starts_at->format('Y/m/d H:i') }}</span>
                            @if($quiz->ends_at) <span style="color:#94a3b8;">←</span> <span dir="ltr">{{ $quiz->ends_at->format('Y/m/d H:i') }}</span>@endif
                            @if($quiz->duration_minutes)<div style="color:#6366f1;font-size:11px;">⏱ {{ $quiz->duration_minutes }} دقيقة</div>@endif
                        @else
                            <span style="color:#cbd5e1;">غير محدد</span>
                        @endif
                    </span>
                </div>
                <div class="qz-card-row">
                    <span class="qz-card-label">الأسئلة / المحاولات</span>
                    <span class="qz-card-val">{{ $quiz->questions_count }} سؤال · {{ $quiz->completed_count }}/{{ $quiz->attempts_count }}</span>
                </div>

                <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:12px;">
                    <a href="{{ route('teacher.quizzes.overview.show', $quiz->id) }}"
                       style="flex:1;text-align:center;padding:7px 12px;font-size:11px;color:#0071AA;background:#e0f2fe;border:1px solid #bae6fd;border-radius:7px;font-weight:600;text-decoration:none;">عرض الحلول</a>
                    <a href="{{ $isProgramQuiz
                            ? route('teacher.quizzes.program.edit', [$quiz->program_id, $quiz->id])
                            : route('teacher.quizzes.edit', [$quiz->subject_id, $quiz->id]) }}"
                       style="flex:1;text-align:center;padding:7px 12px;font-size:11px;color:#475569;background:#f1f5f9;border:1px solid #e2e8f0;border-radius:7px;font-weight:600;text-decoration:none;">تعديل</a>
                    <form method="POST" action="{{ $isProgramQuiz
                            ? route('teacher.quizzes.program.destroy', [$quiz->program_id, $quiz->id])
                            : route('teacher.quizzes.destroy', [$quiz->subject_id, $quiz->id]) }}"
                          style="flex:1;"
                          onsubmit="return confirm('هل أنت متأكد من حذف الاختبار «{{ addslashes($quiz->title_ar) }}»؟ سيتم حذف جميع الأسئلة والمحاولات.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                style="width:100%;padding:7px 12px;font-size:11px;color:#dc2626;background:#fee2e2;border:1px solid #fecaca;border-radius:7px;font-weight:600;cursor:pointer;">حذف</button>
                    </form>
                </div>
            </div>
            @empty
            <div style="padding:40px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد اختبارات في مقرراتك</div>
            @endforelse
        </div>
    </div>

    {{-- Pagination --}}
    @if($quizzes->hasPages())
    <div style="margin-top:16px;">{{ $quizzes->links() }}</div>
    @endif

</div>
@endsection
