@extends('layouts.dashboard')

@section('title', 'الاختبارات والامتحانات')

@section('content')
@php
    // Reusable renderer for one quiz card.
    $statusPill = function ($qz) {
        if ($qz->in_progress)                  return ['جارٍ الحل', '#92400e', '#fef3c7'];
        if ($qz->completed && $qz->released)   return ['النتيجة: ' . rtrim(rtrim((string)$qz->score,'0'),'.'), '#166534', '#dcfce7'];
        if ($qz->completed)                    return ['قيد المراجعة', '#475569', '#f1f5f9'];
        if ($qz->available)                    return ['متاح الآن', '#0369a1', '#e0f2fe'];
        return ['غير متاح', '#94a3b8', '#f8fafc'];
    };
@endphp

<div style="direction:rtl;max-width:1100px;margin:0 auto;">

    {{-- Header --}}
    <div style="background:linear-gradient(135deg,#0071AA,#004d77);border-radius:18px;padding:22px 26px;color:#fff;margin-bottom:22px;">
        <div style="font-size:20px;font-weight:800;">الاختبارات والامتحانات</div>
        <div style="font-size:13px;opacity:.85;margin-top:4px;">جميع اختباراتك عبر المقررات والبرامج — المتاحة والقادمة والمنتهية.</div>
        <div style="display:flex;gap:22px;margin-top:16px;flex-wrap:wrap;">
            <div><div style="font-size:22px;font-weight:800;">{{ $available->count() }}</div><div style="font-size:11px;opacity:.8;">متاحة</div></div>
            <div><div style="font-size:22px;font-weight:800;">{{ $upcoming->count() }}</div><div style="font-size:11px;opacity:.8;">قادمة</div></div>
            <div><div style="font-size:22px;font-weight:800;">{{ $past->count() }}</div><div style="font-size:11px;opacity:.8;">منتهية</div></div>
        </div>
    </div>

    @php
        $sections = [
            ['متاحة الآن', $available, '#10b981'],
            ['قادمة', $upcoming, '#0071AA'],
            ['منتهية', $past, '#94a3b8'],
        ];
    @endphp

    @foreach($sections as [$label, $items, $color])
    <div style="margin-bottom:26px;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:12px;">
            <span style="width:10px;height:10px;border-radius:50%;background:{{ $color }};"></span>
            <span style="font-size:15px;font-weight:800;color:#1e293b;">{{ $label }}</span>
            <span style="background:#f1f5f9;color:#64748b;font-size:.7rem;font-weight:700;padding:.15rem .6rem;border-radius:999px;">{{ $items->count() }}</span>
        </div>

        @if($items->isEmpty())
        <div style="background:#fff;border:1px dashed #e2e8f0;border-radius:14px;padding:26px;text-align:center;color:#94a3b8;font-size:13px;">لا توجد اختبارات في هذه الفئة</div>
        @else
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:14px;">
            @foreach($items as $qz)
            @php [$pillText, $pillColor, $pillBg] = $statusPill($qz); @endphp
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:18px 20px;box-shadow:0 1px 4px rgba(0,0,0,.04);display:flex;flex-direction:column;gap:12px;">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;">
                    <div>
                        <div style="font-weight:800;color:#111827;font-size:14px;">{{ $qz->title }}</div>
                        <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $qz->container }} · {{ $qz->type_label }}</div>
                    </div>
                    <span style="flex-shrink:0;background:{{ $pillBg }};color:{{ $pillColor }};border-radius:999px;padding:.2rem .7rem;font-size:.68rem;font-weight:700;white-space:nowrap;">{{ $pillText }}</span>
                </div>

                <div style="display:flex;flex-wrap:wrap;gap:8px;font-size:11.5px;color:#475569;">
                    <span style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:8px;padding:4px 9px;">🗓 يبدأ: {{ $qz->starts_at ? $qz->starts_at->format('Y/m/d · H:i') : '—' }}</span>
                    <span style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:8px;padding:4px 9px;">⏹ ينتهي: {{ $qz->ends_at ? $qz->ends_at->format('Y/m/d · H:i') : '—' }}</span>
                    <span style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:8px;padding:4px 9px;">⏱ المدة: {{ $qz->duration ? $qz->duration . ' دقيقة' : 'غير محددة' }}</span>
                    <span style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:8px;padding:4px 9px;">📝 {{ $qz->questions }} سؤال</span>
                    <span style="background:#f8fafc;border:1px solid #f1f5f9;border-radius:8px;padding:4px 9px;">🎯 {{ rtrim(rtrim((string)$qz->total_marks,'0'),'.') }} درجة</span>
                </div>

                <a href="{{ $qz->url }}"
                   style="align-self:flex-start;background:#0071AA;color:#fff;border-radius:10px;padding:8px 18px;font-size:12.5px;font-weight:700;text-decoration:none;">
                    {{ $qz->available && !$qz->completed ? 'ابدأ الاختبار' : 'عرض التفاصيل' }}
                </a>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    @endforeach

</div>
@endsection
