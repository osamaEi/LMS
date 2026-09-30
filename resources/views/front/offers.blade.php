@extends('layouts.front')

@section('title', 'العروض والخصومات — أكاديمية الارتقاء')

@section('styles')
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
    .offers-section { padding: 2.5rem clamp(1rem, 3vw, 3rem); background: #f8fafc; min-height: 420px; }
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

    /* ── Offers slider ── */
    .osl { max-width: 1240px; margin: 0 auto; outline: none; }
    .osl-stage { display: flex; align-items: center; gap: 1rem; }
    .osl-track { flex: 1; min-width: 0; touch-action: pan-y; }
    .osl-arrow {
        width: 52px; height: 52px; border-radius: 50%; flex-shrink: 0;
        border: 1.5px solid #e2e8f0; background: #fff; color: #0f172a; font-size: 1.3rem;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        box-shadow: 0 6px 18px rgba(15,23,42,.08); transition: all .2s;
    }
    .osl-arrow:hover { background: #0071AA; border-color: #0071AA; color: #fff; transform: scale(1.06); }
    .osl-arrow:disabled { opacity: .35; pointer-events: none; }
    [dir="ltr"] .osl-arrow i { display: inline-block; transform: scaleX(-1); }

    .os-slide {
        display: none;
        grid-template-columns: minmax(0, 1.2fr) minmax(0, 1fr);
        background: #fff; border: 1px solid #eef2f7; border-radius: 24px; overflow: hidden;
        box-shadow: 0 18px 50px rgba(15,23,42,.10);
        min-height: 440px;
    }
    .os-slide.is-active { display: grid; animation: slIn .45s ease; }
    .os-slide.is-active.from-prev { animation-name: slInPrev; }
    @keyframes slIn     { from { opacity: 0; transform: translateX(-40px); } to { opacity: 1; transform: none; } }
    @keyframes slInPrev { from { opacity: 0; transform: translateX(40px); }  to { opacity: 1; transform: none; } }
    [dir="ltr"] .os-slide.is-active          { animation-name: slInPrev; }
    [dir="ltr"] .os-slide.is-active.from-prev { animation-name: slIn; }
    .os-slide.is-expired .sl-media { filter: grayscale(.6); }

    .sl-media { position: relative; background: #0f172a; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .sl-media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .sl-media iframe, .sl-media video { width: 100%; aspect-ratio: 16 / 9; height: auto; border: 0; background: #000; display: block; }
    .sl-media--hero { background: linear-gradient(135deg, var(--oc-c1), var(--oc-c2)); color: #fff; flex-direction: column; }
    .sl-media--hero::before, .sl-media--hero::after { content: ''; position: absolute; border-radius: 50%; background: rgba(255,255,255,.08); }
    .sl-media--hero::before { width: 320px; height: 320px; top: -110px; right: -70px; }
    .sl-media--hero::after  { width: 200px; height: 200px; bottom: -80px; left: -40px; }
    .sl-hero-num { font-size: clamp(4rem, 9vw, 7rem); font-weight: 900; line-height: 1; position: relative; z-index: 1; text-shadow: 0 6px 24px rgba(0,0,0,.18); }
    .sl-hero-num small { font-size: .35em; margin-inline-start: .3rem; }
    .sl-hero-lbl { font-size: 1.05rem; font-weight: 700; opacity: .9; margin-top: .5rem; position: relative; z-index: 1; }

    .sl-info { padding: 1.75rem 1.9rem; display: flex; flex-direction: column; gap: .85rem; }
    .sl-top { display: flex; align-items: center; justify-content: space-between; gap: .5rem; flex-wrap: wrap; }
    .sl-type { font-size: .82rem; font-weight: 700; color: var(--oc-c1); background: var(--oc-soft); padding: .3rem .8rem; border-radius: 999px; }
    .sl-type b { font-weight: 900; }
    .sl-title { font-size: 1.6rem; font-weight: 900; line-height: 1.45; margin: 0; }
    .sl-title a { color: #0f172a; text-decoration: none; }
    .sl-title a:hover { color: var(--oc-c1); }
    .sl-desc { font-size: .92rem; line-height: 1.9; color: #475569; margin: 0; }
    .sl-price { background: var(--oc-soft); border-radius: 14px; padding: .8rem 1rem; }
    .sl-price-lbl { font-size: .74rem; font-weight: 700; color: #64748b; }
    .sl-price-row { display: flex; align-items: baseline; gap: .6rem; flex-wrap: wrap; }
    .sl-price-new { font-size: 1.8rem; font-weight: 900; color: var(--oc-c1); line-height: 1.3; }
    .sl-cd { display: grid; grid-template-columns: repeat(4, 1fr); gap: .45rem; }
    .sl-cd > div { background: #0f172a; color: #fff; border-radius: 11px; text-align: center; padding: .45rem .2rem; }
    .sl-cd b { display: block; font-size: 1.2rem; font-weight: 900; font-variant-numeric: tabular-nums; line-height: 1.2; }
    .sl-cd span { font-size: .64rem; opacity: .6; font-weight: 700; }
    .sl-actions { display: grid; grid-template-columns: 1.4fr 1fr; gap: .6rem; margin-top: auto; padding-top: .3rem; }
    .sl-actions .oc-btn { padding: .8rem .9rem; font-size: .95rem; }

    .osl-bar { display: flex; align-items: center; gap: 1rem; margin-top: 1.25rem; padding: 0 68px; }
    .osl-counter { font-size: .9rem; font-weight: 700; color: #64748b; white-space: nowrap; }
    .osl-counter b { color: #0071AA; font-size: 1.15rem; }
    .osl-thumbs { display: flex; gap: .5rem; overflow-x: auto; scrollbar-width: none; padding: .25rem; flex: 1; }
    .osl-thumbs::-webkit-scrollbar { display: none; }
    .osl-thumb {
        flex-shrink: 0; display: flex; align-items: center; gap: .5rem;
        border: 1.5px solid #e2e8f0; background: #fff; border-radius: 12px; padding: .45rem .8rem;
        cursor: pointer; transition: all .18s; font-family: inherit;
    }
    .osl-thumb-val { font-size: .85rem; font-weight: 900; color: var(--oc-c1); white-space: nowrap; }
    .osl-thumb-title { font-size: .78rem; font-weight: 700; color: #475569; white-space: nowrap; }
    .osl-thumb:first-child { margin-inline-start: auto; }  /* centred, but still scrollable when overflowing */
    .osl-thumb:last-child  { margin-inline-end: auto; }
    .osl-thumb:hover { border-color: var(--oc-c1); }
    .osl-thumb.is-active { background: var(--oc-c1); border-color: var(--oc-c1); box-shadow: 0 6px 16px rgba(15,23,42,.15); }
    .osl-thumb.is-active span { color: #fff; }

    @media (max-width: 900px) {
        .os-slide { grid-template-columns: 1fr; min-height: 0; }
        .sl-media { aspect-ratio: 16 / 9; }
        .sl-info { padding: 1.25rem; }
        .sl-title { font-size: 1.3rem; }
        .osl-stage { position: relative; }
        .osl-arrow {
            position: absolute; z-index: 5; width: 40px; height: 40px; font-size: 1rem;
            top: calc((100vw - 2rem) * 9 / 32); transform: translateY(-50%);
            background: rgba(255,255,255,.92);
        }
        .osl-arrow:hover { transform: translateY(-50%); }
        .osl-prev { right: 10px; }
        .osl-next { left: 10px; }
        [dir="ltr"] .osl-prev { right: auto; left: 10px; }
        [dir="ltr"] .osl-next { left: auto; right: 10px; }
        .osl-bar { padding: 0; flex-direction: column; align-items: stretch; gap: .6rem; }
        .osl-counter { text-align: center; }
    }

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

    @if($offers->isEmpty())
        <div class="offers-empty">
            <div class="offers-empty-ico"><i class="bi bi-tags"></i></div>
            <h3>لا توجد عروض حالياً</h3>
            <p>تابعنا للاطلاع على أحدث العروض والخصومات</p>
        </div>
    @else
    <div class="osl" id="offersSlider" aria-roledescription="carousel" tabindex="0">
        <div class="osl-stage">
            <button type="button" class="osl-arrow osl-prev" onclick="slideBy(-1)" aria-label="العرض السابق">
                <i class="bi bi-chevron-right"></i>
            </button>

            <div class="osl-track" id="offersTrack">
                @foreach($offers as $i => $offer)
                    @include('front.partials.offer-slide', ['offer' => $offer, 'index' => $i])
                @endforeach
                <div class="offers-empty" id="filterEmpty">
                    <div class="offers-empty-ico"><i class="bi bi-funnel"></i></div>
                    <h3>لا توجد عروض من هذا النوع</h3>
                    <p>جرّب تصنيفاً آخر</p>
                </div>
            </div>

            <button type="button" class="osl-arrow osl-next" onclick="slideBy(1)" aria-label="العرض التالي">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>

        <div class="osl-bar">
            <span class="osl-counter"><b id="slideNow">1</b> / <span id="slideTotal">{{ $offers->count() }}</span></span>
            <div class="osl-thumbs" id="offersThumbs">
                @foreach($offers as $i => $offer)
                <button type="button" class="osl-thumb oc--{{ $offer->display['type'] }}" data-index="{{ $i }}" data-type="{{ $offer->discount_type }}" onclick="goToSlide({{ $i }})">
                    <span class="osl-thumb-val">{{ $offer->display['num'] }}@if($offer->display['money'])<x-riyal />@else%@endif</span>
                    <span class="osl-thumb-title">{{ Str::limit($offer->title_ar, 28) }}</span>
                </button>
                @endforeach
            </div>
        </div>
    </div>
    @endif
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
(function () {
    const slider = document.getElementById('offersSlider');
    if (!slider) return;

    const isRtl  = document.documentElement.dir === 'rtl';
    const slides = [...slider.querySelectorAll('.os-slide')];
    const thumbs = [...slider.querySelectorAll('.osl-thumb')];
    let visible  = slides.slice();   // slides allowed by the current filter
    let pos      = 0;                // position inside `visible`
    let current  = null;
    let touched  = false;            // only write the URL hash after the visitor navigates

    function stopMedia(slide) {
        if (!slide) return;
        slide.querySelectorAll('iframe[src]').forEach(f => { f.src = f.src; }); // reload = stop playback
        slide.querySelectorAll('video').forEach(v => v.pause());
    }

    function loadMedia(slide) {
        slide.querySelectorAll('[data-src]').forEach(el => {
            if (!el.getAttribute('src')) el.setAttribute('src', el.dataset.src);
        });
    }

    function render(fromPrev, initial) {
        if (!initial) touched = true;
        const empty = document.getElementById('filterEmpty');
        slides.forEach(s => s.classList.remove('is-active', 'from-prev'));
        thumbs.forEach(t => t.classList.remove('is-active'));
        stopMedia(current);

        empty.style.display = visible.length ? 'none' : 'block';
        slider.querySelectorAll('.osl-arrow').forEach(a => a.disabled = visible.length < 2);
        document.getElementById('slideTotal').textContent = visible.length;
        document.getElementById('slideNow').textContent = visible.length ? pos + 1 : 0;
        if (!visible.length) { current = null; return; }

        current = visible[pos];
        current.classList.add('is-active');
        if (fromPrev) current.classList.add('from-prev');
        loadMedia(current);

        const thumb = thumbs.find(t => t.dataset.index === current.dataset.index);
        if (thumb) {
            thumb.classList.add('is-active');
            if (touched) thumb.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
        }
        if (touched) history.replaceState(null, '', '#' + current.id);
    }

    window.slideBy = function (dir) {
        if (visible.length < 2) return;
        pos = (pos + dir + visible.length) % visible.length;
        render(dir < 0);
    };

    window.goToSlide = function (index) {
        const target = visible.findIndex(s => s.dataset.index === String(index));
        if (target < 0 || target === pos) return;
        const fromPrev = target < pos;
        pos = target;
        render(fromPrev);
    };

    window.filterOffers = function (type, btn) {
        document.querySelectorAll('.offer-flt').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        visible = slides.filter(s => type === 'all' || s.dataset.type === type);
        thumbs.forEach(t => t.style.display = (type === 'all' || t.dataset.type === type) ? '' : 'none');
        document.getElementById('visibleCount').innerHTML = `<i class="bi bi-card-list"></i> ${visible.length} عرض`;
        pos = 0;
        render(false);
    };

    // Keyboard: the "next" arrow sits on the left in RTL
    document.addEventListener('keydown', e => {
        if (/INPUT|TEXTAREA|SELECT/.test(e.target.tagName)) return;
        if (e.key === 'ArrowLeft')  slideBy(isRtl ? 1 : -1);
        if (e.key === 'ArrowRight') slideBy(isRtl ? -1 : 1);
    });

    // Swipe
    let startX = null;
    const track = document.getElementById('offersTrack');
    track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
    track.addEventListener('touchend', e => {
        if (startX === null) return;
        const dx = e.changedTouches[0].clientX - startX;
        startX = null;
        if (Math.abs(dx) < 50) return;
        slideBy((dx > 0) === isRtl ? 1 : -1);
    });

    // Open the offer from the URL hash (#offer-12) when present
    const fromHash = slides.findIndex(s => '#' + s.id === location.hash);
    pos = fromHash > 0 ? fromHash : 0;
    render(false, true);
})();
</script>
@endsection
