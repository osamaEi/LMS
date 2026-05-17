@extends('layouts.dashboard')

@section('title', 'المدفوعات')

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
        content: '';
        position: absolute;
        top: -40%; left: -5%;
        width: 380px; height: 380px;
        background: radial-gradient(circle, rgba(255,255,255,0.07) 0%, transparent 65%);
        border-radius: 50%;
        pointer-events: none;
    }
    .dash-header::after {
        content: '';
        position: absolute;
        bottom: -60%; right: -2%;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.04) 0%, transparent 65%);
        border-radius: 50%;
        pointer-events: none;
    }

    .d-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04), 0 1px 2px rgba(0,0,0,.06);
        overflow: hidden;
    }
    .dark .d-card { background: #1f2937; }

    .d-card-head {
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; gap: .75rem;
    }
    .dark .d-card-head { border-color: #374151; }

    .icon-wrap {
        width: 36px; height: 36px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }

    /* Stat boxes */
    .stats-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 1rem; }
    @media(max-width:768px){ .stats-row{ grid-template-columns: repeat(2,1fr); } }
    .stat-box {
        background: #fff; border-radius: 18px;
        padding: 1.2rem 1.5rem;
        display: flex; align-items: center; gap: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
        transition: transform .2s;
    }
    .dark .stat-box { background: #1f2937; }
    .stat-box:hover { transform: translateY(-2px); }
    .s-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .s-val  { font-size: 1.45rem; font-weight: 800; line-height: 1; color: #111827; }
    .dark .s-val { color: #f9fafb; }
    .s-lbl  { font-size: .75rem; color: #6b7280; margin-top: .2rem; font-weight: 500; }
    .dark .s-lbl { color: #9ca3af; }

    /* Progress bar */
    .prog-track { height: 6px; background: #f3f4f6; border-radius: 3px; overflow: hidden; }
    .dark .prog-track { background: #374151; }
    .prog-fill  { height: 100%; border-radius: 3px; }

    /* Payment card */
    .pay-card {
        display: flex; flex-direction: column;
        background: #fff; border-radius: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,.05), 0 1px 2px rgba(0,0,0,.07);
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }
    .dark .pay-card { background: #1f2937; }
    .pay-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }

    .pay-card-top {
        padding: 1.25rem 1.5rem 1rem;
        display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem;
    }
    .pay-card-body { padding: 0 1.5rem 1rem; flex: 1; }
    .pay-card-foot {
        padding: .85rem 1.5rem;
        border-top: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between; gap: .75rem;
    }
    .dark .pay-card-foot { border-color: #374151; }

    .amt-row { display: flex; justify-content: space-between; align-items: center; padding: .4rem 0; font-size: .83rem; }
    .amt-row + .amt-row { border-top: 1px solid #f8fafc; }
    .dark .amt-row + .amt-row { border-color: #374151; }

    /* Status badges */
    .badge { display: inline-flex; align-items: center; gap: .3rem; padding: .28rem .75rem; border-radius: 20px; font-size: .72rem; font-weight: 700; }
    .badge-pending   { background:#fef9c3; color:#92400e; }
    .badge-partial   { background:#dbeafe; color:#1d4ed8; }
    .badge-completed { background:#dcfce7; color:#15803d; }
    .badge-cancelled { background:#fee2e2; color:#dc2626; }

    /* Action btn */
    .btn-view {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .5rem 1.1rem; border-radius: 10px;
        font-size: .8rem; font-weight: 700; color: #fff;
        background: linear-gradient(135deg,#0071AA,#004d77);
        text-decoration: none; transition: opacity .2s;
    }
    .btn-view:hover { opacity: .88; color: #fff; }

    .btn-pay {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .5rem 1.1rem; border-radius: 10px;
        font-size: .8rem; font-weight: 700; color: #fff;
        background: linear-gradient(135deg,#10b981,#059669);
        border: none; cursor: pointer; transition: opacity .2s;
    }
    .btn-pay:hover { opacity: .88; }

    /* Flash */
    .flash { display: flex; align-items: center; gap: .7rem; padding: .9rem 1.25rem; border-radius: 14px; font-size: .88rem; font-weight: 600; }
    .flash-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; }
    .flash-error   { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }
    .flash-warning { background:#fffbeb; border:1px solid #fde68a; color:#92400e; }

    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }
</style>
@endpush

@section('content')
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
                    <p class="text-xs font-semibold mb-1" style="opacity:.55;">{{ auth()->user()->name }}</p>
                    <h1 class="text-2xl font-black tracking-tight">المدفوعات والرسوم التدريبية </h1>
                    <p class="text-sm mt-0.5" style="opacity:.6;">عرض وإدارة جميع مدفوعاتك</p>
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('student.dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-semibold" style="background:rgba(255,255,255,.12);color:#fff;text-decoration:none;">الرئيسية</a>
            </div>
        </div>
    </div>

    {{-- ══ FLASH ══ --}}
    @if(session('success'))
    <div class="flash flash-success">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="flash flash-error">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        {{ session('error') }}
    </div>
    @endif
    @if(session('warning'))
    <div class="flash flash-warning">
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        {{ session('warning') }}
    </div>
    @endif

    {{-- ══ STAT BOXES ══ --}}
    @php
        $totalAmt    = $payments->sum('total_amount');
        $paidAmt     = $payments->sum('paid_amount');
        $remaining   = $payments->sum('remaining_amount');
        $pendingCnt  = $payments->where('status','pending')->count();
    @endphp
    <div class="stats-row">
        <div class="stat-box">
            <div class="s-icon" style="background:linear-gradient(135deg,#0071AA,#004d77);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <div>
                <div class="s-val">{{ number_format($totalAmt,0) }}<span style="font-size:.85rem;font-weight:600;"> <x-riyal /></span></div>
                <div class="s-lbl">إجمالي الرسوم</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="s-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="s-val">{{ number_format($paidAmt,0) }}<span style="font-size:.85rem;font-weight:600;"> <x-riyal /></span></div>
                <div class="s-lbl">المبلغ المدفوع</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="s-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <div class="s-val">{{ number_format($remaining,0) }}<span style="font-size:.85rem;font-weight:600;"> <x-riyal /></span></div>
                <div class="s-lbl">المبلغ المتبقي</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="s-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            <div>
                <div class="s-val">{{ $payments->count() }}</div>
                <div class="s-lbl">إجمالي الدفعات</div>
            </div>
        </div>
    </div>

    {{-- ══ PAYMENTS ══ --}}
    @if($payments->count() > 0)

    <div class="d-card">
        <div class="d-card-head">
            <div class="icon-wrap" style="background:#eff6ff;">
                <svg class="w-4 h-4" style="color:#2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
            </div>
            <span class="text-sm font-bold text-gray-900 dark:text-white flex-1">سجل المدفوعات</span>
            <span class="text-xs text-gray-400">آخر تحديث: {{ now()->format('H:i · Y/m/d') }}</span>
        </div>

        <div style="padding:1.25rem;display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:1rem;">
            @foreach($payments as $payment)
            @php
                $pct = $payment->total_amount > 0 ? ($payment->paid_amount / $payment->total_amount) * 100 : 0;
                $barColor = $pct >= 100 ? '#10b981' : ($pct >= 50 ? '#0071AA' : '#f59e0b');
            @endphp
            <div class="pay-card">

                {{-- Top: program + badge --}}
                <div class="pay-card-top">
                    <div style="flex:1;min-width:0;">
                        <div class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $payment->program->name_ar }}</div>
                        @if($payment->program->name_en)
                        <div class="text-xs text-gray-400 mt-0.5 truncate">{{ $payment->program->name_en }}</div>
                        @endif
                        <div class="text-xs mt-1" style="color:#9ca3af;">{{ $payment->created_at->format('Y/m/d') }} · #{{ $payment->id }}</div>
                    </div>
                    <div class="flex-shrink-0">
                        @if($payment->status == 'pending')
                            <span class="badge badge-pending">⏳ قيد الانتظار</span>
                        @elseif($payment->status == 'partial')
                            <span class="badge badge-partial">◑ جزئية</span>
                        @elseif($payment->status == 'completed')
                            <span class="badge badge-completed">✓ مكتملة</span>
                        @elseif($payment->status == 'cancelled')
                            <span class="badge badge-cancelled">✕ ملغاة</span>
                        @endif
                    </div>
                </div>

                {{-- Body: amounts + progress --}}
                <div class="pay-card-body">
                    <div class="amt-row">
                        <span class="text-gray-500 dark:text-gray-400">إجمالي المبلغ</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ number_format($payment->total_amount,0) }} <x-riyal /></span>
                    </div>
                    @if($payment->discount_amount > 0)
                    <div class="amt-row">
                        <span class="text-gray-500 dark:text-gray-400">الخصم</span>
                        <span class="font-bold" style="color:#10b981;">- {{ number_format($payment->discount_amount,0) }} <x-riyal /></span>
                    </div>
                    @endif
                    <div class="amt-row">
                        <span class="text-gray-500 dark:text-gray-400">المدفوع</span>
                        <span class="font-bold" style="color:#0071AA;">{{ number_format($payment->paid_amount,0) }} <x-riyal /></span>
                    </div>
                    <div class="amt-row">
                        <span class="text-gray-500 dark:text-gray-400">المتبقي</span>
                        <span class="font-bold" style="color:#f59e0b;">{{ number_format($payment->remaining_amount,0) }} <x-riyal /></span>
                    </div>

                    {{-- Progress --}}
                    <div style="margin-top:.85rem;">
                        <div style="display:flex;justify-content:space-between;margin-bottom:.35rem;">
                            <span class="text-xs text-gray-400">نسبة الدفع</span>
                            <span class="text-xs font-bold" style="color:{{ $barColor }};">{{ number_format($pct,0) }}%</span>
                        </div>
                        <div class="prog-track">
                            <div class="prog-fill" style="width:{{ $pct }}%;background:{{ $barColor }};transition:width .8s ease;"></div>
                        </div>
                    </div>
                </div>

                {{-- Footer: type + actions --}}
                <div class="pay-card-foot">
                    <div>
                        @if($payment->payment_type == 'full')
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background:#f0f9ff;color:#0369a1;">دفعة كاملة</span>
                        @else
                            <span class="text-xs font-bold px-2.5 py-1 rounded-lg" style="background:#f5f3ff;color:#6d28d9;">تقسيط</span>
                        @endif
                    </div>
                    <div style="display:flex;gap:.5rem;align-items:center;">
                        @if(!$payment->isFullyPaid() && !$payment->isCancelled() && ($tamaraConfigured ?? false))
                        <form action="{{ route('student.payments.pay-tamara', $payment) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-pay">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                تمارا
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('student.payments.show', $payment) }}" class="btn-view">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            التفاصيل
                        </a>
                    </div>
                </div>

            </div>
            @endforeach
        </div>
    </div>

    @else

    {{-- Empty state --}}
    <div class="d-card">
        <div style="padding:4rem;text-align:center;">
            <div style="width:72px;height:72px;border-radius:20px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem;">
                <svg class="w-9 h-9" style="color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-1">لا توجد دفعات</h3>
            <p class="text-sm text-gray-400">لم يتم إنشاء أي دفعة لك بعد. تواصل مع الإدارة لمزيد من المعلومات.</p>
        </div>
    </div>

    @endif

</div>
@endsection
