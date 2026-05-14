@extends('layouts.dashboard')

@section('title', 'تفاصيل الدفعة')

@push('styles')
<style>
    .dash-page { max-width: 1240px; margin: 0 auto; }

    .dash-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 40%, #003d5c 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    .dash-header::before {
        content: ''; position: absolute; top: -40%; left: -5%;
        width: 380px; height: 380px;
        background: radial-gradient(circle, rgba(255,255,255,.07) 0%, transparent 65%);
        border-radius: 50%; pointer-events: none;
    }
    .dash-header::after {
        content: ''; position: absolute; bottom: -60%; right: -2%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,.04) 0%, transparent 65%);
        border-radius: 50%; pointer-events: none;
    }

    .d-card { background: #fff; border-radius: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 1px 2px rgba(0,0,0,.06); overflow: hidden; }
    .dark .d-card { background: #1f2937; }

    .d-card-head {
        padding: 1.1rem 1.5rem; border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: .75rem;
    }
    .dark .d-card-head { border-color: #374151; }

    .icon-wrap { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

    /* Stat boxes */
    .stats-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; }
    @media(max-width:768px){ .stats-row{ grid-template-columns: repeat(2,1fr); } }
    .stat-box { background: #fff; border-radius: 18px; padding: 1.2rem 1.5rem; display: flex; align-items: center; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,.04); }
    .dark .stat-box { background: #1f2937; }
    .s-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .s-val  { font-size: 1.3rem; font-weight: 800; line-height: 1.1; color: #111827; }
    .dark .s-val { color: #f9fafb; }
    .s-lbl  { font-size: .73rem; color: #6b7280; margin-top: .2rem; font-weight: 500; }
    .dark .s-lbl { color: #9ca3af; }

    /* Progress bar */
    .prog-track { height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden; }
    .dark .prog-track { background: #374151; }
    .prog-fill  { height: 100%; border-radius: 4px; transition: width .8s ease; }

    /* Installment rows */
    .inst-row {
        display: flex; align-items: center; gap: 1rem;
        padding: .9rem 1.5rem; border-bottom: 1px solid #f8fafc;
        transition: background .15s;
    }
    .inst-row:last-child { border-bottom: none; }
    .inst-row:hover { background: #f8fafc; }
    .dark .inst-row { border-color: #1f2937; }
    .dark .inst-row:hover { background: #111827; }

    .inst-num { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: .78rem; font-weight: 800; color: #fff; flex-shrink: 0; }

    /* Transaction rows */
    .trx-row {
        display: flex; align-items: center; gap: 1rem;
        padding: .9rem 1.5rem; border-bottom: 1px solid #f8fafc;
        transition: background .15s;
    }
    .trx-row:last-child { border-bottom: none; }
    .trx-row:hover { background: #f8fafc; }
    .dark .trx-row { border-color: #1f2937; }
    .dark .trx-row:hover { background: #111827; }

    .trx-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; gap: .3rem; padding: .25rem .7rem; border-radius: 20px; font-size: .7rem; font-weight: 700; }
    .badge-pending   { background: #fef9c3; color: #92400e; }
    .badge-partial   { background: #dbeafe; color: #1d4ed8; }
    .badge-completed { background: #dcfce7; color: #15803d; }
    .badge-cancelled { background: #fee2e2; color: #dc2626; }
    .badge-overdue   { background: #fff7ed; color: #c2410c; }

    /* Info rows */
    .info-row { display: flex; justify-content: space-between; align-items: center; padding: .75rem 1.5rem; border-bottom: 1px solid #f8fafc; font-size: .85rem; }
    .dark .info-row { border-color: #1f2937; }
    .info-row:last-child { border-bottom: none; }

    /* Flash */
    .flash { display: flex; align-items: center; gap: .7rem; padding: .9rem 1.25rem; border-radius: 14px; font-size: .88rem; font-weight: 600; }
    .flash-success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #15803d; }
    .flash-error   { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }
    .flash-warning { background: #fffbeb; border: 1px solid #fde68a; color: #92400e; }
    .flash-info    { background: #eff6ff; border: 1px solid #bfdbfe; color: #1d4ed8; }

    /* Payment method badge */
    .method-badge { display: inline-flex; align-items: center; gap: .3rem; padding: .2rem .65rem; border-radius: 8px; font-size: .7rem; font-weight: 700; background: #f3f4f6; color: #374151; }

    /* Action buttons */
    .btn-back {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .5rem 1rem; border-radius: 10px; font-size: .8rem; font-weight: 700;
        background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.2);
        color: #fff; text-decoration: none; transition: background .2s;
    }
    .btn-back:hover { background: rgba(255,255,255,.25); color: #fff; }

    /* Donut via SVG */
    .donut-wrap { position: relative; width: 110px; height: 110px; flex-shrink: 0; }
    .donut-label { position: absolute; inset: 0; display: flex; flex-direction: column; align-items: center; justify-content: center; }

    /* Dynamic-width helpers — values injected via JS CSS vars */
    .prog-paid { width: var(--dyn-pct, 0%); background: #10b981; }
    .prog-rem  { width: var(--dyn-rem, 0%); background: #f59e0b; }

    /* Transaction icon gradients */
    .trx-success { background: linear-gradient(135deg,#10b981,#059669); }
    .trx-pending { background: linear-gradient(135deg,#f59e0b,#d97706); }
    .trx-failed  { background: linear-gradient(135deg,#ef4444,#dc2626); }

    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

    /* Receipt upload */
    .bank-card { background: linear-gradient(135deg,#f0f9ff,#e0f2fe); border: 1px solid #bae6fd; border-radius: 16px; padding: 1.25rem 1.5rem; }
    .bank-row { display: flex; justify-content: space-between; align-items: center; padding: .5rem 0; border-bottom: 1px solid rgba(186,230,253,.6); font-size: .83rem; }
    .bank-row:last-child { border-bottom: none; }
    .upload-area {
        border: 2px dashed #bae6fd; border-radius: 14px; padding: 2rem;
        text-align: center; cursor: pointer; transition: all .2s;
        background: #f0f9ff;
    }
    .upload-area:hover { border-color: #0071AA; background: #e0f2fe; }
    .upload-area.has-file { border-color: #10b981; background: #f0fdf4; }
    .upload-btn {
        display: inline-flex; align-items: center; gap: .5rem;
        padding: .75rem 2rem; border-radius: 12px; font-size: .88rem; font-weight: 700;
        background: linear-gradient(135deg,#0071AA,#004d77); color: #fff;
        border: none; cursor: pointer; transition: opacity .2s;
    }
    .upload-btn:hover { opacity: .9; }
    .upload-btn:disabled { opacity: .5; cursor: not-allowed; }
    .receipt-badge-approved { background: #dcfce7; color: #15803d; }
    .receipt-badge-pending  { background: #fef9c3; color: #92400e; }
    .receipt-badge-rejected { background: #fee2e2; color: #dc2626; }
</style>
@endpush

@section('content')
@php
    $pct      = $payment->total_amount > 0 ? ($payment->paid_amount / $payment->total_amount) * 100 : 0;
    $barColor = $pct >= 100 ? '#10b981' : ($pct >= 50 ? '#0071AA' : '#f59e0b');
    $r        = 46;
    $circ     = 2 * pi() * $r;
    $dash     = $circ * (1 - $pct / 100);
    $pctRound = round($pct, 1);
    $remRound = round(100 - $pct, 1);
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var root = document.documentElement;
        root.style.setProperty('--dyn-pct', '{{ $pctRound }}%');
        root.style.setProperty('--dyn-rem',  '{{ $remRound }}%');
    });
</script>

<div class="dash-page space-y-6" dir="rtl">

    {{-- ══ HEADER ══ --}}
    <div class="dash-header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative z-10">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0" style="background:rgba(255,255,255,.15);backdrop-filter:blur(8px);">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold mb-1" style="opacity:.55;">تفاصيل الدفعة #{{ $payment->id }}</p>
                    <h1 class="text-2xl font-black tracking-tight">{{ $payment->program->name_ar }}</h1>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        @if($payment->status == 'pending')
                            <span class="badge badge-pending">⏳ قيد الانتظار</span>
                        @elseif($payment->status == 'partial')
                            <span class="badge badge-partial">◑ مدفوعة جزئياً</span>
                        @elseif($payment->status == 'completed')
                            <span class="badge badge-completed">✓ مكتملة</span>
                        @elseif($payment->status == 'cancelled')
                            <span class="badge badge-cancelled">✕ ملغاة</span>
                        @endif
                        <span class="text-xs" style="opacity:.55;">{{ $payment->created_at->format('Y/m/d') }}</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('student.payments.index') }}" class="btn-back">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                العودة
            </a>
        </div>
    </div>

    {{-- ══ FLASH ══ --}}
    @foreach(['success'=>'flash-success','error'=>'flash-error','warning'=>'flash-warning','info'=>'flash-info'] as $key=>$cls)
    @if(session($key))
    <div class="flash {{ $cls }}">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        {{ session($key) }}
    </div>
    @endif
    @endforeach

    {{-- ══ STAT BOXES ══ --}}
    <div class="stats-row">
        <div class="stat-box">
            <div class="s-icon" style="background:linear-gradient(135deg,#0071AA,#004d77);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="s-val">{{ number_format($payment->total_amount,0) }}<small style="font-size:.8rem;"> <x-riyal /></small></div>
                <div class="s-lbl">إجمالي المبلغ</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="s-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="s-val">{{ number_format($payment->paid_amount,0) }}<small style="font-size:.8rem;"> <x-riyal /></small></div>
                <div class="s-lbl">المبلغ المدفوع</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="s-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="s-val">{{ number_format($payment->remaining_amount,0) }}<small style="font-size:.8rem;"> <x-riyal /></small></div>
                <div class="s-lbl">المبلغ المتبقي</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="s-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <div class="s-val">{{ number_format($pct,0) }}<small style="font-size:.85rem;">%</small></div>
                <div class="s-lbl">نسبة الدفع</div>
            </div>
        </div>
    </div>

    {{-- ══ ROW 1: Progress card + Info card ══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Progress + donut --}}
        <div class="lg:col-span-2 d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background:#eff6ff;">
                    <svg class="w-4 h-4" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">تقدم السداد</span>
                <span class="text-xs text-gray-400">{{ $payment->payment_type == 'full' ? 'دفعة كاملة' : 'تقسيط' }}</span>
            </div>
            <div style="padding:1.5rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap;">
                {{-- SVG Donut --}}
                <div class="donut-wrap">
                    <svg width="110" height="110" viewBox="0 0 110 110">
                        <circle cx="55" cy="55" r="{{ $r }}" fill="none" stroke="#f3f4f6" stroke-width="10"/>
                        <circle cx="55" cy="55" r="{{ $r }}" fill="none" stroke="{{ $barColor }}" stroke-width="10"
                                stroke-dasharray="{{ $circ }}"
                                stroke-dashoffset="{{ $dash }}"
                                stroke-linecap="round"
                                transform="rotate(-90 55 55)"
                                style="transition:stroke-dashoffset 1s ease;"/>
                    </svg>
                    <div class="donut-label">
                        <span style="font-size:1.3rem;font-weight:900;color:#111827;">{{ number_format($pct,0) }}%</span>
                        <span style="font-size:.65rem;color:#9ca3af;margin-top:.1rem;">مكتمل</span>
                    </div>
                </div>

                {{-- Bars --}}
                <div style="flex:1;min-width:200px;space-y:.75rem;">
                    <div style="margin-bottom:.9rem;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:.3rem;">
                            <span style="font-size:.78rem;color:#6b7280;">المدفوع</span>
                            <span style="font-size:.78rem;font-weight:700;color:#10b981;">{{ number_format($payment->paid_amount,0) }} <x-riyal /></span>
                        </div>
                        <div class="prog-track">
                            <div class="prog-fill prog-paid"></div>
                        </div>
                    </div>
                    <div style="margin-bottom:.9rem;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:.3rem;">
                            <span style="font-size:.78rem;color:#6b7280;">المتبقي</span>
                            <span style="font-size:.78rem;font-weight:700;color:#f59e0b;">{{ number_format($payment->remaining_amount,0) }} <x-riyal /></span>
                        </div>
                        <div class="prog-track">
                            <div class="prog-fill prog-rem"></div>
                        </div>
                    </div>
                    @if($payment->discount_amount > 0)
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:.6rem .9rem;display:flex;justify-content:space-between;">
                        <span style="font-size:.78rem;color:#15803d;">خصم مطبّق</span>
                        <span style="font-size:.78rem;font-weight:700;color:#15803d;">{{ number_format($payment->discount_amount,0) }} <x-riyal /></span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Payment actions --}}
            @if(!$payment->isFullyPaid() && !$payment->isCancelled())
            <div style="padding:1rem 1.5rem;border-top:1px solid #f1f5f9;background:#fafafa;display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;">
                <span style="font-size:.8rem;color:#6b7280;font-weight:600;">خيارات الدفع:</span>
                @if($payment->payment_type == 'installment' && $payment->installments->count() > 0)
                    <span style="font-size:.78rem;color:#92400e;background:#fef9c3;padding:.3rem .75rem;border-radius:8px;font-weight:600;">راجع الأقساط أدناه للدفع</span>
                @else
                    @if($tamaraConfigured ?? false)
                    <form action="{{ route('student.payments.pay-tamara', $payment) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" style="display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1.1rem;border-radius:10px;font-size:.8rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#10b981,#059669);border:none;cursor:pointer;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            الدفع عبر تمارا
                        </button>
                    </form>
                    @endif
                    @if(config('services.paytabs.profile_id'))
                    <form action="{{ route('student.payments.pay-paytabs', $payment) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" style="display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1.1rem;border-radius:10px;font-size:.8rem;font-weight:700;color:#fff;background:linear-gradient(135deg,#0071AA,#004d77);border:none;cursor:pointer;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            بطاقة / PayTabs
                        </button>
                    </form>
                    @endif
                    <span style="font-size:.75rem;color:#9ca3af;">أو تواصل مع الإدارة للدفع نقداً</span>
                @endif
            </div>
            @endif
        </div>

        {{-- Program Info --}}
        <div class="d-card">
            <div class="d-card-head">
                <div class="icon-wrap" style="background:#f0fdf4;">
                    <svg class="w-4 h-4" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-sm font-bold text-gray-900 dark:text-white">معلومات الدفعة</span>
            </div>
            <div class="info-row">
                <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">رقم الدفعة</span>
                <span class="font-bold text-gray-900 dark:text-white text-sm">#{{ $payment->id }}</span>
            </div>
            <div class="info-row">
                <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">البرنامج</span>
                <span class="font-bold text-gray-900 dark:text-white text-sm" style="max-width:160px;text-align:left;word-break:break-word;">{{ $payment->program->name_ar }}</span>
            </div>
            <div class="info-row">
                <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">نوع الدفع</span>
                @if($payment->payment_type == 'full')
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background:#f0f9ff;color:#0369a1;">دفعة كاملة</span>
                @else
                    <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background:#f5f3ff;color:#6d28d9;">تقسيط</span>
                @endif
            </div>
            <div class="info-row">
                <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">الحالة</span>
                @if($payment->status == 'pending')   <span class="badge badge-pending">⏳ قيد الانتظار</span>
                @elseif($payment->status == 'partial') <span class="badge badge-partial">◑ جزئية</span>
                @elseif($payment->status == 'completed') <span class="badge badge-completed">✓ مكتملة</span>
                @elseif($payment->status == 'cancelled') <span class="badge badge-cancelled">✕ ملغاة</span>
                @endif
            </div>
            <div class="info-row">
                <span class="text-gray-500 dark:text-gray-400 text-xs font-medium">تاريخ الإنشاء</span>
                <span class="font-bold text-sm" style="color:#0071AA;">{{ $payment->created_at->format('Y/m/d') }}</span>
            </div>
            @if($payment->notes)
            <div style="padding:.85rem 1.5rem;">
                <div class="text-xs text-gray-400 mb-1 font-medium">ملاحظات</div>
                <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $payment->notes }}</div>
            </div>
            @endif
        </div>
    </div>

    {{-- ══ ROW 2: Installments (if any) ══ --}}
    @if($payment->payment_type == 'installment' && $payment->installments->count() > 0)
    @php
        $overdueCount = $payment->installments->filter(fn($i) => $i->isOverdue())->count();
        $pendingCount = $payment->installments->filter(fn($i) => $i->status == 'pending' && !$i->isOverdue())->count();
        $paidCount    = $payment->installments->filter(fn($i) => $i->status == 'paid')->count();
    @endphp
    <div class="d-card">
        <div class="d-card-head">
            <div class="icon-wrap" style="background:#fef3c7;">
                <svg class="w-4 h-4" style="color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">الأقساط</span>
            <div style="display:flex;gap:.5rem;">
                <span class="text-xs font-bold px-2 py-0.5 rounded-lg" style="background:#dcfce7;color:#15803d;">{{ $paidCount }} مدفوع</span>
                <span class="text-xs font-bold px-2 py-0.5 rounded-lg" style="background:#dbeafe;color:#1d4ed8;">{{ $pendingCount }} قادم</span>
                @if($overdueCount > 0)
                <span class="text-xs font-bold px-2 py-0.5 rounded-lg" style="background:#fee2e2;color:#dc2626;">{{ $overdueCount }} متأخر</span>
                @endif
            </div>
        </div>

        @if($overdueCount > 0)
        <div style="margin:.75rem 1.5rem 0;padding:.75rem 1rem;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;display:flex;align-items:center;gap:.6rem;">
            <svg class="w-4 h-4 flex-shrink-0" style="color:#dc2626;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span style="font-size:.82rem;color:#dc2626;font-weight:600;">لديك {{ $overdueCount }} قسط متأخر. يرجى التواصل مع الإدارة.</span>
        </div>
        @endif

        <div style="padding-top:.5rem;">
            @foreach($payment->installments as $inst)
            @php
                $instColor = $inst->status == 'paid' ? '#10b981' : ($inst->isOverdue() ? '#ef4444' : '#0071AA');
            @endphp
            <div class="inst-row">
                <div class="inst-num" style="background:{{ $instColor }};">{{ $inst->installment_number }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="text-sm font-bold text-gray-900 dark:text-white">القسط #{{ $inst->installment_number }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">استحقاق: {{ $inst->due_date->format('Y/m/d') }}</div>
                    @if($inst->paid_at)
                    <div class="text-xs mt-0.5" style="color:#10b981;">دُفع في: {{ $inst->paid_at->format('Y/m/d') }}</div>
                    @endif
                </div>
                <div style="text-align:left;flex-shrink:0;">
                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($inst->amount,0) }} <x-riyal /></div>
                    @if($inst->status == 'paid')
                        <span class="badge badge-completed" style="font-size:.65rem;">✓ مدفوع</span>
                    @elseif($inst->isOverdue())
                        <span class="badge badge-overdue" style="font-size:.65rem;">متأخر</span>
                    @elseif($inst->status == 'cancelled')
                        <span class="badge badge-cancelled" style="font-size:.65rem;">ملغي</span>
                    @else
                        <span class="badge badge-partial" style="font-size:.65rem;">قيد الانتظار</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ══ BANK TRANSFER RECEIPT UPLOAD ══ --}}
    @if(!$payment->isFullyPaid() && !$payment->isCancelled())
    @php
        $hasPendingReceipt = $payment->transactions->where('payment_method','bank_transfer')->where('receipt_status','pending')->count() > 0;
    @endphp
    <div class="d-card">
        <div class="d-card-head">
            <div class="icon-wrap" style="background:#f0f9ff;">
                <svg class="w-4 h-4" style="color:#0369a1;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">الدفع بالتحويل البنكي</span>
            @if($hasPendingReceipt)
            <span class="badge receipt-badge-pending">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></path></svg>
                إيصال قيد المراجعة
            </span>
            @endif
        </div>
        <div style="padding:1.5rem;display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;" class="max-md:!grid-cols-1">

            {{-- Bank details --}}
            <div>
                <p class="text-xs text-gray-500 font-semibold mb-3">تفاصيل الحساب البنكي</p>
                <div class="bank-card">
                    <div class="bank-row">
                        <span class="text-gray-500 font-medium">البنك</span>
                        <span class="font-bold text-gray-800">بنك الأهلي التجاري</span>
                    </div>
                    <div class="bank-row">
                        <span class="text-gray-500 font-medium">اسم المستفيد</span>
                        <span class="font-bold text-gray-800">أكاديمية LOOP</span>
                    </div>
                    <div class="bank-row">
                        <span class="text-gray-500 font-medium">رقم الحساب</span>
                        <span class="font-bold text-gray-800" dir="ltr">SA12 3456 7890 1234 5678 90</span>
                    </div>
                    <div class="bank-row">
                        <span class="text-gray-500 font-medium">المبلغ المطلوب</span>
                        <span class="font-bold" style="color:#0071AA;">{{ number_format($payment->remaining_amount,2) }} <x-riyal /></span>
                    </div>
                </div>
                <div style="margin-top:1rem;padding:.85rem 1rem;background:#fffbeb;border:1px solid #fde68a;border-radius:12px;display:flex;align-items:flex-start;gap:.6rem;">
                    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" style="color:#d97706;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    <p class="text-xs text-yellow-800 leading-relaxed">بعد إتمام التحويل، ارفع صورة الإيصال أدناه وسيتم تأكيد دفعتك خلال 24 ساعة.</p>
                </div>
            </div>

            {{-- Upload form --}}
            <div>
                <p class="text-xs text-gray-500 font-semibold mb-3">رفع إيصال التحويل</p>
                @if($hasPendingReceipt)
                <div style="padding:1.5rem;text-align:center;background:#fefce8;border:1px dashed #fde68a;border-radius:14px;">
                    <svg class="w-10 h-10 mx-auto mb-2" style="color:#d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-bold text-yellow-800">لديك إيصال قيد المراجعة</p>
                    <p class="text-xs text-yellow-700 mt-1">سيتم إشعارك بمجرد مراجعة الإيصال من قبل الإدارة.</p>
                </div>
                @else
                <form action="{{ route('student.payments.upload-receipt', $payment) }}" method="POST" enctype="multipart/form-data" id="receiptForm">
                    @csrf

                    {{-- Amount field --}}
                    <div style="margin-bottom:1rem;">
                        <label style="display:block;font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.4rem;">المبلغ المحوّل (<x-riyal />) <span style="color:#dc2626;">*</span></label>
                        <input type="number" name="amount" step="0.01" min="1" max="{{ $payment->remaining_amount }}"
                               value="{{ old('amount', $payment->remaining_amount) }}"
                               style="width:100%;padding:.6rem .9rem;border:1.5px solid #e5e7eb;border-radius:10px;font-size:.88rem;transition:border-color .2s;"
                               onfocus="this.style.borderColor='#0071AA'" onblur="this.style.borderColor='#e5e7eb'"
                               placeholder="{{ number_format($payment->remaining_amount,2) }}" required>
                        @error('amount')<p style="color:#dc2626;font-size:.75rem;margin-top:.3rem;">{{ $message }}</p>@enderror
                    </div>

                    {{-- File upload --}}
                    <div style="margin-bottom:1rem;">
                        <label style="display:block;font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.4rem;">صورة الإيصال <span style="color:#dc2626;">*</span></label>
                        <div class="upload-area" id="uploadArea" onclick="document.getElementById('receiptFile').click()">
                            <div id="uploadPlaceholder">
                                <svg class="w-8 h-8 mx-auto mb-2" style="color:#93c5fd;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v8"/></svg>
                                <p class="text-xs font-bold" style="color:#0369a1;">اضغط لرفع الإيصال</p>
                                <p class="text-xs text-gray-400 mt-1">JPG، PNG، أو PDF — حتى 5 ميغابايت</p>
                            </div>
                            <div id="fileInfo" style="display:none;">
                                <svg class="w-8 h-8 mx-auto mb-2" style="color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-xs font-bold" style="color:#15803d;" id="fileName">-</p>
                            </div>
                        </div>
                        <input type="file" id="receiptFile" name="receipt" accept=".jpg,.jpeg,.png,.pdf" style="display:none;"
                               onchange="handleFileSelect(this)">
                        @error('receipt')<p style="color:#dc2626;font-size:.75rem;margin-top:.3rem;">{{ $message }}</p>@enderror
                    </div>

                    {{-- Notes --}}
                    <div style="margin-bottom:1.25rem;">
                        <label style="display:block;font-size:.8rem;font-weight:700;color:#374151;margin-bottom:.4rem;">ملاحظات (اختياري)</label>
                        <textarea name="notes" rows="2"
                                  style="width:100%;padding:.6rem .9rem;border:1.5px solid #e5e7eb;border-radius:10px;font-size:.83rem;resize:none;transition:border-color .2s;"
                                  onfocus="this.style.borderColor='#0071AA'" onblur="this.style.borderColor='#e5e7eb'"
                                  placeholder="رقم المرجع أو أي ملاحظة...">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="upload-btn w-full justify-center" id="submitBtn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v8"/></svg>
                        إرسال الإيصال للمراجعة
                    </button>
                </form>
                @endif
            </div>

        </div>
    </div>
    @endif

    {{-- ══ TRANSACTIONS ══ --}}
    <div class="d-card">
        <div class="d-card-head">
            <div class="icon-wrap" style="background:#ecfdf5;">
                <svg class="w-4 h-4" style="color:#059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">سجل المعاملات</span>
            <span class="text-xs text-gray-400">{{ $payment->transactions->count() }} معاملة</span>
        </div>

        @if($payment->transactions->count() > 0)
        @foreach($payment->transactions as $trx)
        @php
            $trxClass = $trx->status == 'success' ? 'trx-success' : ($trx->status == 'pending' ? 'trx-pending' : 'trx-failed');
        @endphp
        <div class="trx-row">
            <div class="trx-icon {{ $trxClass }}">
                @if($trx->status == 'success')
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @elseif($trx->status == 'pending')
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @else
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                @endif
            </div>
            <div style="flex:1;min-width:0;">
                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($trx->amount,2) }} <x-riyal /></div>
                <div style="display:flex;align-items:center;gap:.5rem;margin-top:.2rem;flex-wrap:wrap;">
                    <span class="text-xs text-gray-400">{{ $trx->created_at->format('Y/m/d H:i') }}</span>
                    <span class="method-badge">
                        @if($trx->payment_method == 'cash') نقدي
                        @elseif($trx->payment_method == 'bank_transfer') تحويل بنكي
                        @elseif($trx->payment_method == 'tamara') تمارا
                        @elseif($trx->payment_method == 'paytabs') PayTabs
                        @elseif($trx->payment_method == 'waived') معفي
                        @else {{ $trx->payment_method }}
                        @endif
                    </span>
                    @if($trx->transaction_reference)
                    <span class="text-xs text-gray-400">{{ $trx->transaction_reference }}</span>
                    @endif
                    @if($trx->receipt_status == 'pending')
                        <span class="badge receipt-badge-pending" style="font-size:.65rem;">⏳ إيصال قيد المراجعة</span>
                    @elseif($trx->receipt_status == 'approved')
                        <span class="badge receipt-badge-approved" style="font-size:.65rem;">✓ إيصال مقبول</span>
                    @elseif($trx->receipt_status == 'rejected')
                        <span class="badge receipt-badge-rejected" style="font-size:.65rem;">✕ إيصال مرفوض</span>
                    @endif
                </div>
                @if($trx->receipt_status == 'rejected' && $trx->receipt_rejection_reason)
                <div style="margin-top:.4rem;font-size:.72rem;color:#dc2626;background:#fef2f2;padding:.3rem .6rem;border-radius:6px;display:inline-block;">
                    سبب الرفض: {{ $trx->receipt_rejection_reason }}
                </div>
                @endif
                @if($trx->notes && !$trx->receipt_rejection_reason)
                <div class="text-xs text-gray-400 mt-0.5">{{ $trx->notes }}</div>
                @endif
            </div>
            <div style="flex-shrink:0;">
                @if($trx->status == 'success')
                    <span class="badge badge-completed">ناجح</span>
                @elseif($trx->status == 'pending')
                    <span class="badge badge-pending">معالجة</span>
                @else
                    <span class="badge badge-cancelled">فشل</span>
                @endif
            </div>
        </div>
        @endforeach

        @else
        <div style="padding:3rem;text-align:center;">
            <div style="width:52px;height:52px;border-radius:14px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;">
                <svg class="w-6 h-6" style="color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <p class="text-sm text-gray-400">لا توجد معاملات بعد</p>
        </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
function handleFileSelect(input) {
    var area  = document.getElementById('uploadArea');
    var ph    = document.getElementById('uploadPlaceholder');
    var info  = document.getElementById('fileInfo');
    var fname = document.getElementById('fileName');

    if (input.files && input.files[0]) {
        var file = input.files[0];
        area.classList.add('has-file');
        ph.style.display   = 'none';
        info.style.display = 'block';
        fname.textContent  = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
    } else {
        area.classList.remove('has-file');
        ph.style.display   = 'block';
        info.style.display = 'none';
    }
}

var form = document.getElementById('receiptForm');
if (form) {
    form.addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        if (btn) {
            btn.disabled    = true;
            btn.textContent = 'جاري الرفع...';
        }
    });
}
</script>
@endpush
@endsection
