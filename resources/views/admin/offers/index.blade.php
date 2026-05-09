@extends('layouts.dashboard')

@section('title', 'إدارة العروض والخصومات')

@push('styles')
<style>
    :root { --offer-primary:#0071AA; --offer-dark:#005a88; }

    .offers-hero {
        background: linear-gradient(135deg,#0071AA 0%,#005a88 50%,#003d5c 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.75rem;
    }
    .offers-hero::before {
        content:'';position:absolute;inset:0;
        background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        animation: slidePattern 25s linear infinite;
    }
    @keyframes slidePattern { 0%{transform:translateX(0)} 100%{transform:translateX(-60px)} }

    .hero-stat { background:rgba(255,255,255,.12); border-radius:16px; padding:1rem 1.25rem; text-align:center; backdrop-filter:blur(4px); }
    .hero-stat-val { font-size:1.8rem; font-weight:900; color:#fff; line-height:1; }
    .hero-stat-lbl { font-size:.75rem; color:rgba(255,255,255,.75); margin-top:.25rem; font-weight:600; }

    .filter-bar { background:#fff; border-radius:16px; padding:1rem 1.25rem; box-shadow:0 1px 3px rgba(0,0,0,.05); margin-bottom:1.5rem; display:flex; gap:.75rem; flex-wrap:wrap; align-items:center; }
    .dark .filter-bar { background:#1f2937; }

    .filter-input { padding:.55rem .9rem; border:2px solid #e5e7eb; border-radius:10px; font-size:.85rem; background:#fff; color:#374151; transition:border .2s; min-width:150px; }
    .filter-input:focus { outline:none; border-color:#0071AA; }
    .dark .filter-input { background:#374151; border-color:#4b5563; color:#f9fafb; }

    .offer-card { background:#fff; border-radius:20px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,.06); transition:all .25s; display:flex; flex-direction:column; }
    .dark .offer-card { background:#1f2937; }
    .offer-card:hover { transform:translateY(-4px); box-shadow:0 12px 30px rgba(0,0,0,.1); }

    .offer-card-img { height:140px; object-fit:cover; width:100%; display:block; }
    .offer-card-img-placeholder { height:140px; display:flex; align-items:center; justify-content:center; font-size:3.5rem; }

    .offer-card-body { padding:1.25rem; flex:1; display:flex; flex-direction:column; }
    .offer-card-footer { padding:.75rem 1.25rem; border-top:1px solid #f1f5f9; display:flex; gap:.5rem; align-items:center; }
    .dark .offer-card-footer { border-color:#374151; }

    .discount-badge { display:inline-flex; align-items:center; gap:.3rem; padding:.35rem .85rem; border-radius:20px; font-size:.95rem; font-weight:900; }
    .discount-pct { background:linear-gradient(135deg,#0071AA,#005a88); color:#fff; }
    .discount-fixed { background:linear-gradient(135deg,#10b981,#059669); color:#fff; }

    .status-dot { width:8px; height:8px; border-radius:50%; display:inline-block; margin-left:.35rem; }
    .dot-active { background:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,.2); }
    .dot-inactive { background:#9ca3af; }
    .dot-expired { background:#ef4444; }
    .dot-upcoming { background:#f59e0b; }

    .s-action { display:inline-flex; align-items:center; gap:.3rem; padding:.4rem .8rem; border-radius:8px; font-size:.78rem; font-weight:700; border:none; cursor:pointer; transition:all .15s; text-decoration:none; }
    .s-edit { background:rgba(0,113,170,.1); color:#0071AA; }
    .s-edit:hover { background:rgba(0,113,170,.2); color:#005a88; }
    .s-del  { background:rgba(239,68,68,.1); color:#ef4444; }
    .s-del:hover { background:rgba(239,68,68,.2); color:#dc2626; }
    .s-toggle-on  { background:rgba(245,158,11,.1); color:#d97706; }
    .s-toggle-on:hover { background:rgba(245,158,11,.2); }
    .s-toggle-off { background:rgba(16,185,129,.1); color:#10b981; }
    .s-toggle-off:hover { background:rgba(16,185,129,.2); }

    .offer-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1.25rem; }

    .empty-offers { text-align:center; padding:4rem 2rem; background:#fff; border-radius:20px; }
    .dark .empty-offers { background:#1f2937; }

    .tag { display:inline-flex; align-items:center; padding:.2rem .6rem; border-radius:6px; font-size:.7rem; font-weight:700; }
    .tag-program { background:rgba(139,92,246,.1); color:#7c3aed; }
    .tag-global  { background:rgba(59,130,246,.1);  color:#2563eb; }

    .new-btn { display:inline-flex; align-items:center; gap:.5rem; padding:.7rem 1.4rem; border-radius:12px; font-weight:800; font-size:.9rem; background:#fff; color:#0071AA; border:none; cursor:pointer; transition:all .2s; text-decoration:none; }
    .new-btn:hover { background:rgba(255,255,255,.85); transform:translateY(-1px); color:#0071AA; }
</style>
@endpush

@section('content')
<div style="max-width:1200px;margin:0 auto;" dir="rtl">

{{-- Flash --}}
@foreach(['success'=>'#d1fae5|#065f46|#bbf7d0','error'=>'#fee2e2|#991b1b|#fecaca'] as $k=>$v)
@if(session($k))
@php [$bg,$tc,$br] = explode('|',$v); @endphp
<div style="display:flex;align-items:center;gap:.75rem;padding:.9rem 1.25rem;border-radius:14px;background:{{ $bg }};border:1px solid {{ $br }};color:{{ $tc }};font-weight:600;font-size:.88rem;margin-bottom:1rem;">
    <svg style="width:18px;height:18px;flex-shrink:0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
    {{ session($k) }}
</div>
@endif
@endforeach

{{-- Hero --}}
<div class="offers-hero">
    <div style="position:relative;z-index:5;">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <p style="font-size:.78rem;font-weight:600;color:rgba(255,255,255,.65);margin:0 0 .3rem;">لوحة الإدارة</p>
                <h1 style="font-size:1.8rem;font-weight:900;color:#fff;margin:0;">العروض والخصومات</h1>
                <p style="font-size:.88rem;color:rgba(255,255,255,.7);margin:.3rem 0 0;">أنشئ وأدِر العروض الترويجية وكودات الخصم للبرامج</p>
            </div>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                <div class="hero-stat"><div class="hero-stat-val">{{ $stats['total'] }}</div><div class="hero-stat-lbl">إجمالي العروض</div></div>
                <div class="hero-stat"><div class="hero-stat-val" style="color:#86efac;">{{ $stats['active'] }}</div><div class="hero-stat-lbl">نشطة الآن</div></div>
                <div class="hero-stat"><div class="hero-stat-val" style="color:#fde68a;">{{ $stats['upcoming'] }}</div><div class="hero-stat-lbl">قادمة</div></div>
                <div class="hero-stat"><div class="hero-stat-val" style="color:#fca5a5;">{{ $stats['expired'] }}</div><div class="hero-stat-lbl">منتهية</div></div>
            </div>
            <a href="{{ route('admin.offers.create') }}" class="new-btn">
                <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                إنشاء عرض جديد
            </a>
        </div>
    </div>
</div>

{{-- Filters --}}
<form method="GET" class="filter-bar">
    <input type="text" name="search" class="filter-input" placeholder="🔍 بحث بالعنوان أو الكود..." value="{{ request('search') }}">
    <select name="status" class="filter-input">
        <option value="">كل الحالات</option>
        <option value="active" @selected(request('status')=='active')>نشط</option>
        <option value="inactive" @selected(request('status')=='inactive')>غير نشط</option>
    </select>
    <select name="program_id" class="filter-input">
        <option value="">كل البرامج</option>
        @foreach($programs as $prog)
        <option value="{{ $prog->id }}" @selected(request('program_id')==$prog->id)>{{ $prog->name_ar }}</option>
        @endforeach
    </select>
    <button type="submit" style="padding:.55rem 1.1rem;border-radius:10px;background:#0071AA;color:#fff;border:none;font-weight:700;cursor:pointer;font-size:.85rem;">بحث</button>
    @if(request()->hasAny(['search','status','program_id']))
    <a href="{{ route('admin.offers.index') }}" style="padding:.55rem 1rem;border-radius:10px;background:#f3f4f6;color:#6b7280;font-weight:700;font-size:.85rem;text-decoration:none;">مسح</a>
    @endif
</form>

{{-- Cards grid --}}
@if($offers->count())
<div class="offer-grid">
@foreach($offers as $offer)
@php
    $isActive   = $offer->is_active;
    $isExpired  = $offer->is_expired;
    $isUpcoming = $offer->is_upcoming;
    $dotClass   = $isActive ? 'dot-active' : ($isExpired ? 'dot-expired' : ($isUpcoming ? 'dot-upcoming' : 'dot-inactive'));
    $statusLabel= $isActive ? 'نشط' : ($isExpired ? 'منتهي' : ($isUpcoming ? 'قادم' : 'غير نشط'));
@endphp
<div class="offer-card">
    @if($offer->image)
        <img src="{{ asset('storage/'.$offer->image) }}" alt="{{ $offer->title_ar }}" class="offer-card-img">
    @else
        <div class="offer-card-img-placeholder" style="background:linear-gradient(135deg,{{ $offer->discount_type=='percentage'?'#eff6ff,#dbeafe':'#f0fdf4,#dcfce7' }});">🎁</div>
    @endif

    <div class="offer-card-body">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:.5rem;margin-bottom:.6rem;">
            <span class="discount-badge {{ $offer->discount_type=='percentage'?'discount-pct':'discount-fixed' }}">
                {{ $offer->discount_label }}
                {{ $offer->discount_type=='percentage'?'خصم':'خصم ثابت' }}
            </span>
            <span style="font-size:.75rem;color:#6b7280;display:flex;align-items:center;">
                <span class="status-dot {{ $dotClass }}"></span>{{ $statusLabel }}
            </span>
        </div>

        <h3 style="font-size:.95rem;font-weight:800;color:#111827;margin:0 0 .4rem;line-height:1.35;">{{ $offer->title_ar }}</h3>

        @if($offer->description_ar)
        <p style="font-size:.78rem;color:#6b7280;margin:0 0 .75rem;line-height:1.5;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">{{ $offer->description_ar }}</p>
        @endif

        <div style="display:flex;gap:.4rem;flex-wrap:wrap;margin-bottom:.75rem;">
            @if($offer->program)
                <span class="tag tag-program">🎓 {{ $offer->program->name_ar }}</span>
            @else
                <span class="tag tag-global">🌐 جميع البرامج</span>
            @endif
            @if($offer->code)
                <span class="tag" style="background:#fef3c7;color:#92400e;font-family:monospace;letter-spacing:.5px;">{{ $offer->code }}</span>
            @endif
        </div>

        <div style="display:flex;gap:1rem;font-size:.72rem;color:#9ca3af;margin-top:auto;flex-wrap:wrap;">
            <span>📅 {{ $offer->start_date->format('Y/m/d') }} → {{ $offer->end_date->format('Y/m/d') }}</span>
            @if($isActive)
            <span style="color:#10b981;font-weight:700;">⏳ {{ $offer->days_left }} يوم متبقي</span>
            @endif
            @if($offer->max_uses)
            <span>🔢 {{ $offer->uses_count }}/{{ $offer->max_uses }} استخدام</span>
            @endif
        </div>
    </div>

    <div class="offer-card-footer">
        <a href="{{ route('admin.offers.edit', $offer) }}" class="s-action s-edit">
            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            تعديل
        </a>
        <form action="{{ route('admin.offers.toggle-status', $offer) }}" method="POST" style="display:inline;">
            @csrf @method('PATCH')
            <button type="submit" class="s-action {{ $offer->status=='active'?'s-toggle-on':'s-toggle-off' }}">
                {{ $offer->status=='active'?'إيقاف':'تفعيل' }}
            </button>
        </form>
        <form action="{{ route('admin.offers.destroy', $offer) }}" method="POST" style="display:inline;margin-right:auto;" onsubmit="return confirm('هل تريد حذف هذا العرض؟')">
            @csrf @method('DELETE')
            <button type="submit" class="s-action s-del">
                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                حذف
            </button>
        </form>
    </div>
</div>
@endforeach
</div>

{{-- Pagination --}}
<div style="margin-top:1.5rem;">{{ $offers->withQueryString()->links() }}</div>

@else
<div class="empty-offers">
    <div style="font-size:4rem;margin-bottom:1rem;">🎁</div>
    <h3 style="font-size:1.1rem;font-weight:800;color:#374151;margin:0 0 .5rem;">لا توجد عروض بعد</h3>
    <p style="font-size:.88rem;color:#9ca3af;margin:0 0 1.5rem;">أنشئ أول عرض أو خصم لجذب الطلاب</p>
    <a href="{{ route('admin.offers.create') }}" style="display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;border-radius:12px;background:#0071AA;color:#fff;font-weight:700;text-decoration:none;">
        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        إنشاء عرض جديد
    </a>
</div>
@endif

</div>
@endsection
