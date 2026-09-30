@extends('layouts.front')

@section('title', 'العروض والخصومات — أكاديمية الارتقاء')

@section('styles')
<style>
    /* Featured Banner */
    .featured-banner {
        margin: 0 clamp(1rem, 3vw, 3rem) 2rem;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        height: 280px;
    }
    .featured-banner img { width: 100%; height: 100%; object-fit: cover; object-position: center 30%; }
    .featured-banner .overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, rgba(0,71,130,0.85) 0%, rgba(0,113,170,0.45) 65%);
        display: flex;
        align-items: center;
        padding: 2rem clamp(1rem, 4vw, 4rem);
    }
    [dir="ltr"] .featured-banner .overlay { background: linear-gradient(to left, rgba(0,71,130,0.85) 0%, rgba(0,113,170,0.45) 65%); }
    .featured-banner .overlay h3 { color: #fff; font-size: clamp(1.2rem, 3vw, 1.8rem); margin-bottom: .5rem; font-weight: 800; }
    .featured-banner .overlay p  { color: rgba(255,255,255,.85); font-size: .95rem; margin: 0; }

    .fb-stats { display: flex; gap: .75rem; margin-top: 1.1rem; flex-wrap: wrap; }
    .fb-stat {
        background: rgba(255,255,255,.14);
        border: 1px solid rgba(255,255,255,.25);
        backdrop-filter: blur(6px);
        border-radius: 12px;
        padding: .45rem .9rem;
        color: #fff;
        text-align: center;
        min-width: 90px;
    }
    .fb-stat b { display: block; font-size: 1.25rem; font-weight: 900; line-height: 1.2; }
    .fb-stat span { font-size: .72rem; opacity: .85; font-weight: 700; }

    /* Filter bar */
    .offer-filter-bar {
        background: #fff;
        border-bottom: 1px solid #e5e7eb;
        position: sticky;
        top: 0;
        z-index: 200;
        box-shadow: 0 2px 12px rgba(0,0,0,.06);
    }
    .offer-filter-inner {
        display: flex;
        align-items: center;
        gap: .5rem;
        padding: .75rem 1rem;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .offer-filter-inner::-webkit-scrollbar { display: none; }
    .offer-flt {
        display: inline-flex; align-items: center; gap: 6px;
        padding: .42rem 1rem; border-radius: 999px;
        font-size: .82rem; font-weight: 700;
        border: 1.5px solid #e5e7eb; background: #f9fafb; color: #6b7280;
        cursor: pointer; transition: all .17s; white-space: nowrap; flex-shrink: 0;
    }
    .offer-flt:hover  { background: #eaf5fb; color: #0071AA; border-color: #0071AA; }
    .offer-flt.active { background: #0071AA; color: #fff; border-color: #0071AA; }
    .offer-count { margin-inline-start: auto; flex-shrink: 0; font-size: .8rem; font-weight: 700; color: #6b7280; white-space: nowrap; }
    .offer-count i { color: #0071AA; }

    /* Section */
    .offers-section { padding: 2.5rem clamp(1rem, 3vw, 3rem); background: #f8fafc; min-height: 50vh; }
    .offers-section .head { text-align: center; margin-bottom: 2rem; }
    .offers-section .head h2 { margin: 1rem 0 .5rem; font-weight: 800; }
    .offers-section .head p { max-width: 700px; margin: 0 auto; line-height: 1.8; color: #384250; font-size: .95rem; }

    /* Empty state */
    .offers-empty { text-align: center; padding: 5rem 1rem; color: #94a3b8; grid-column: 1 / -1; }
    .offers-empty-ico {
        width: 80px; height: 80px; border-radius: 20px; background: #f1f5f9; border: 1.5px solid #e2e8f0;
        display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 1.25rem; color: #cbd5e1;
    }
    .offers-empty h3 { font-size: 1.05rem; font-weight: 800; color: #1e293b; margin-bottom: .4rem; }
    .offers-empty p  { font-size: .88rem; margin: 0; }
    #filterEmpty { display: none; }

    @media (max-width: 768px) {
        .featured-banner { height: auto; min-height: 220px; margin: 0 1rem 1.5rem; }
        .featured-banner img { position: absolute; inset: 0; }
        .featured-banner .overlay { position: relative; min-height: 220px; }
        .offers-section { padding: 1.5rem 1rem; }
    }

    /* CTA image */
    .cta-formal-wrap { position: relative; padding: 18px 18px 18px 0; max-width: 460px; width: 100%; }
    [dir="ltr"] .cta-formal-wrap { padding: 18px 0 18px 18px; }
    .cta-formal-wrap::before {
        content: ''; position: absolute; top: 0; right: 0; width: 72%; height: 78%;
        background: rgba(255,255,255,.08); border-radius: 22px; border: 1.5px solid rgba(255,255,255,.15);
    }
    [dir="ltr"] .cta-formal-wrap::before { right: auto; left: 0; }
    .cta-formal-wrap::after {
        content: ''; position: absolute; bottom: 0; left: 0; width: 48%; height: 52%;
        border: 2px solid rgba(255,255,255,.2); border-radius: 18px;
    }
    [dir="ltr"] .cta-formal-wrap::after { left: auto; right: 0; }
    .cta-formal-inner { position: relative; border-radius: 18px; overflow: hidden; box-shadow: 0 24px 60px rgba(0,0,0,.3); }
    .cta-formal-inner img { width: 100%; height: 340px; object-fit: cover; display: block; filter: brightness(.92) contrast(1.06) saturate(1.08); }
    .cta-formal-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,40,80,.6) 0%, transparent 50%); border-radius: 18px; }
    .cta-corner-tl {
        position: absolute; top: -10px; left: -10px; width: 48px; height: 48px;
        background: rgba(255,255,255,.18); backdrop-filter: blur(6px); border: 1.5px solid rgba(255,255,255,.3);
        border-radius: 13px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 16px rgba(0,0,0,.2);
    }
    [dir="ltr"] .cta-corner-tl { left: auto; right: -10px; }
    .cta-formal-badge {
        position: absolute; bottom: 16px; left: 16px; display: flex; align-items: center; gap: 9px;
        background: rgba(255,255,255,.95); backdrop-filter: blur(8px); border-radius: 12px; padding: 9px 14px; box-shadow: 0 4px 20px rgba(0,0,0,.18);
    }
    [dir="ltr"] .cta-formal-badge { left: auto; right: 16px; }
    .cta-badge-icon { width: 34px; height: 34px; background: linear-gradient(135deg,#0071AA,#004d77); border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .cta-formal-badge strong { display: block; font-size: 12px; font-weight: 800; color: #111827; line-height: 1.2; }
    .cta-formal-badge span   { font-size: 10px; color: #6b7280; }
    .cta-disc-tag {
        position: absolute; top: 16px; right: 16px; background: linear-gradient(135deg,#ef4444,#dc2626);
        border-radius: 12px; padding: 8px 12px; text-align: center; box-shadow: 0 4px 16px rgba(239,68,68,.4);
    }
    [dir="ltr"] .cta-disc-tag { right: auto; left: 16px; }
    .cta-disc-pct { display: block; font-size: 1.3rem; font-weight: 900; color: #fff; line-height: 1; }
    .cta-disc-lbl { display: block; font-size: 9px; font-weight: 700; color: rgba(255,255,255,.85); margin-top: 2px; white-space: nowrap; }
    @media (max-width: 768px) { .cta-formal-inner img { height: 240px; } }
</style>
@endsection

@section('content')
@include('front.partials.offer-card-assets')

{{-- Hero Section --}}
<section class="hero-section">
    <div class="breadcrumb-nav">
        <a href="{{ route('home') }}">الرئيسية</a>
        <span>></span>
        <span>العروض والخصومات</span>
    </div>
    <h2>العروض والخصومات</h2>
    <p>استفد من عروضنا الحصرية على برامجنا التدريبية المعتمدة — خصومات محدودة المدة لا تفوّتها.</p>
</section>

{{-- Featured Banner --}}
<div class="featured-banner">
    <img loading="lazy" src="{{ asset('lms-photos/3.png') }}" alt="العروض والخصومات" onerror="this.src='{{ asset('lms-photos/1.png') }}'" />
    <div class="overlay">
        <div>
            <h3>عروض حصرية على برامجنا التدريبية</h3>
            <p>سجّل الآن واستفد من أفضل الأسعار والخصومات المتاحة</p>
            <div class="fb-stats">
                <div class="fb-stat"><b>{{ $stats['active'] }}</b><span>عرض نشط</span></div>
                @if($stats['upcoming'])
                <div class="fb-stat"><b>{{ $stats['upcoming'] }}</b><span>عرض قادم</span></div>
                @endif
                @php $maxPct = $offers->filter(fn($o) => $o->is_active && $o->discount_type === 'percentage')->max('discount_value'); @endphp
                @if($maxPct)
                <div class="fb-stat"><b>{{ number_format($maxPct, 0) }}%</b><span>أعلى خصم</span></div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="offer-filter-bar">
    <div class="container">
        <div class="offer-filter-inner">
            <button class="offer-flt active" onclick="filterOffers('all',this)"><i class="bi bi-grid-3x3-gap-fill"></i> الكل</button>
            <button class="offer-flt" onclick="filterOffers('percentage',this)"><i class="bi bi-percent"></i> نسبة مئوية</button>
            <button class="offer-flt" onclick="filterOffers('fixed',this)"><i class="bi bi-cash-coin"></i> مبلغ ثابت</button>
            @if($stats['has_override'])
            <button class="offer-flt" onclick="filterOffers('override',this)"><i class="bi bi-tag-fill"></i> سعر مباشر</button>
            @endif
            <span class="offer-count" id="visibleCount"><i class="bi bi-card-list"></i> {{ $offers->count() }} عرض</span>
        </div>
    </div>
</div>

{{-- Offers Grid --}}
<section class="offers-section">
    <div class="head">
        <p class="st-p">عروض وخصومات</p>
        <h2>اختر العرض المناسب لك</h2>
        <p>خصومات حصرية على برامجنا التدريبية المعتمدة — عروض محدودة المدة تنتهي قريباً.</p>
    </div>

    <div class="oc-grid" id="offersGrid">
        @forelse($offers as $offer)
            @include('front.partials.offer-card', ['offer' => $offer])
        @empty
            <div class="offers-empty">
                <div class="offers-empty-ico"><i class="bi bi-tags"></i></div>
                <h3>لا توجد عروض حالياً</h3>
                <p>تابعنا للاطلاع على أحدث العروض والخصومات</p>
            </div>
        @endforelse

        <div class="offers-empty" id="filterEmpty">
            <div class="offers-empty-ico"><i class="bi bi-funnel"></i></div>
            <h3>لا توجد عروض من هذا النوع</h3>
            <p>جرّب تصنيفاً آخر</p>
        </div>
    </div>
</section>

{{-- Mockup / CTA Section --}}
<section class="mockup-section" style="background:linear-gradient(135deg,#004d7a 0%,#0071aa 100%);padding:3rem clamp(1rem,3vw,3rem);color:white;">
    <div class="row align-items-center">
        <div class="col-lg-6">
            <div style="max-width:600px;">
                <p class="st-p" style="background:rgba(255,255,255,.15);color:#fff;margin-bottom:1rem;">لا تفوّت الفرصة</p>
                <h2 style="margin-bottom:1rem;font-weight:800;">سجّل الآن واستفد من أفضل العروض</h2>
                <p style="line-height:1.8;opacity:.9;margin-bottom:2rem;">
                    برامجنا التدريبية المعتمدة متاحة بأسعار مخفّضة لفترة محدودة.
                    سارع بالتسجيل قبل انتهاء العروض وابدأ مسيرتك التعليمية اليوم.
                </p>
                <a href="{{ route('register') }}" class="full-btn" style="display:inline-flex;align-items:center;gap:8px;font-size:1rem;padding:12px 28px;border-radius:10px;">
                    <i class="bi bi-arrow-left-circle-fill"></i>
                    سجّل الآن
                </a>
            </div>
        </div>
        <div class="col-lg-6 mt-4 mt-lg-0 d-flex justify-content-center">
            @include('front.partials.cta-formal-image', [
                'src'       => asset('new/1.png'),
                'badgeText' => 'برامج معتمدة',
                'badgeSub'  => 'وفق أعلى معايير الجودة',
                'tagText'   => 'خصم حصري',
            ])
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
function filterOffers(type, btn) {
    document.querySelectorAll('.offer-flt').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    let n = 0;
    document.querySelectorAll('#offersGrid .oc').forEach(c => {
        const show = type === 'all' || c.dataset.type === type;
        c.style.display = show ? '' : 'none';
        if (show) n++;
    });
    document.getElementById('visibleCount').innerHTML = `<i class="bi bi-card-list"></i> ${n} عرض`;
    const hasCards = document.querySelectorAll('#offersGrid .oc').length > 0;
    document.getElementById('filterEmpty').style.display = hasCards && n === 0 ? 'block' : 'none';
}
</script>
@endsection
