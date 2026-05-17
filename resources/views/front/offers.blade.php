@extends('layouts.front')

@section('title', 'العروض والخصومات — معهد الارتقاء')

@section('styles')
<style>
    /* ── Offer Cards ── */
    .offer-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .offer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .offer-card.is-expired {
        opacity: 0.65;
    }

    .offer-card.is-expired .full-btn {
        background: #9ca3af !important;
        pointer-events: none;
    }

    /* Image / Discount visual area */
    .offer-img-wrap {
        position: relative;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .offer-img-wrap.type-pct  { background: linear-gradient(135deg, #0071AA 0%, #0ea5e9 100%); }
    .offer-img-wrap.type-fix  { background: linear-gradient(135deg, #059669 0%, #34d399 100%); }
    .offer-img-wrap.type-ovr  { background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%); }

    .offer-disc-display {
        text-align: center;
        color: #fff;
    }

    .offer-disc-display .disc-num {
        font-size: clamp(3rem, 8vw, 4.5rem);
        font-weight: 900;
        line-height: 1;
        letter-spacing: -0.03em;
        display: block;
        text-shadow: 0 2px 12px rgba(0,0,0,.2);
    }

    .offer-disc-display .disc-unit {
        font-size: 1.2rem;
        font-weight: 700;
        opacity: 0.85;
        display: block;
        margin-top: 0.25rem;
    }

    .offer-disc-display .disc-lbl {
        font-size: 0.75rem;
        font-weight: 700;
        opacity: 0.7;
        display: block;
        margin-top: 0.15rem;
        letter-spacing: 0.05em;
    }

    /* Badge */
    .offer-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,.22);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255,255,255,.35);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    [dir="ltr"] .offer-badge { right: auto; left: 15px; }

    /* Status pill */
    .offer-status {
        position: absolute;
        bottom: 12px;
        left: 12px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.68rem;
        font-weight: 800;
        padding: 3px 10px;
        border-radius: 6px;
    }
    [dir="ltr"] .offer-status { left: auto; right: 12px; }
    .st-active   { background: #dcfce7; color: #15803d; }
    .st-expired  { background: #fee2e2; color: #dc2626; }
    .st-upcoming { background: #dbeafe; color: #1d4ed8; }

    /* Card body */
    .offer-card .card-body {
        padding: 1.25rem 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .offer-card .card-title {
        font-weight: 800;
        font-size: 1rem;
        margin-bottom: 0.35rem;
        color: #1e293b;
        line-height: 1.4;
    }

    .offer-card .card-program {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 0.85rem;
    }
    .offer-card .card-program i { color: #0071AA; }

    .offer-card .course-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        padding-top: 0.85rem;
        border-top: 1px solid #f1f5f9;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .offer-card .meta-date {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #92400e;
        background: #fef3c7;
        padding: 3px 10px;
        border-radius: 6px;
    }

    .offer-card .meta-seats {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #166534;
        background: #dcfce7;
        padding: 3px 10px;
        border-radius: 6px;
    }

    /* Countdown */
    .offer-countdown {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        background: linear-gradient(135deg, #0071AA, #0ea5e9);
        border-radius: 10px;
        padding: 0.7rem 0.5rem;
        margin-top: 0.85rem;
    }
    .offer-countdown .cd-box { text-align: center; }
    .offer-countdown .cd-num {
        display: block;
        font-size: 1.25rem;
        font-weight: 900;
        color: #fff;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }
    .offer-countdown .cd-lbl {
        display: block;
        font-size: 0.5rem;
        font-weight: 700;
        color: rgba(255,255,255,.55);
        margin-top: 2px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .offer-countdown .cd-sep {
        color: rgba(255,255,255,.35);
        font-weight: 900;
        font-size: 1.1rem;
        align-self: flex-start;
        padding-top: 2px;
        flex-shrink: 0;
    }

    /* Code box */
    .offer-code {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1.5px dashed #cbd5e1;
        border-radius: 10px;
        padding: 0.5rem 0.75rem;
        margin-top: 0.75rem;
    }
    .offer-code .code-lbl {
        font-size: 0.62rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        white-space: nowrap;
    }
    .offer-code code {
        font-family: 'Courier New', monospace;
        font-size: 0.88rem;
        font-weight: 900;
        letter-spacing: 2.5px;
        color: #1e293b;
        flex: 1;
    }
    .offer-copy {
        width: 30px;
        height: 30px;
        border-radius: 7px;
        border: none;
        background: #fff;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.8rem;
        flex-shrink: 0;
        box-shadow: 0 1px 3px rgba(0,0,0,.1);
        transition: all 0.18s;
    }
    .offer-copy:hover  { background: #0071AA; color: #fff; }
    .offer-copy.copied { background: #10b981; color: #fff; }

    /* CTA row */
    .offer-cta {
        padding: 0 1.5rem 1.25rem;
        margin-top: auto;
    }

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
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        overflow-x: auto;
        scrollbar-width: none;
        -webkit-overflow-scrolling: touch;
    }
    .offer-filter-inner::-webkit-scrollbar { display: none; }

    .offer-flt {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 0.42rem 1rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        border: 1.5px solid #e5e7eb;
        background: #f9fafb;
        color: #6b7280;
        cursor: pointer;
        transition: all 0.17s;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .offer-flt:hover  { background: #eaf5fb; color: #0071AA; border-color: #0071AA; }
    .offer-flt.active { background: #0071AA; color: #fff; border-color: #0071AA; }
    .offer-flt i { font-size: 0.78rem; }

    .offer-count {
        margin-right: auto;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 0.78rem;
        font-weight: 700;
        color: #6b7280;
        white-space: nowrap;
    }
    [dir="ltr"] .offer-count { margin-right: 0; margin-left: auto; }
    .offer-count i { color: #0071AA; }

    /* Courses section wrapper */
    .offers-section {
        padding: 2rem clamp(1rem, 3vw, 3rem);
        background: #f9fafb;
        min-height: 50vh;
    }

    .offers-section .head {
        text-align: center;
        margin-bottom: 2rem;
    }

    .offers-section .head h2 {
        margin: 1rem 0 0.5rem;
        font-weight: bold;
    }

    .offers-section .head p {
        max-width: 700px;
        margin: 0 auto;
        line-height: 1.8;
        color: rgba(56, 66, 80, 1);
        font-size: 0.95rem;
    }

    /* Offer card wrapper (for relative badge positioning) */
    .offer-card-wrapper {
        position: relative;
    }

    /* Empty state */
    .offers-empty {
        text-align: center;
        padding: 5rem 1rem;
        color: #94a3b8;
        grid-column: 1 / -1;
    }
    .offers-empty-ico {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: #f1f5f9;
        border: 1.5px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        margin: 0 auto 1.25rem;
        color: #cbd5e1;
    }
    .offers-empty h3 { font-size: 1.05rem; font-weight: 800; color: #1e293b; margin-bottom: 0.4rem; }
    .offers-empty p  { font-size: 0.88rem; margin: 0; }

    /* Featured Banner */
    .featured-banner {
        margin: 0 clamp(1rem, 3vw, 3rem) 2rem;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        height: 280px;
    }
    .featured-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center 30%;
    }
    .featured-banner .overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to left, rgba(0,71,130,0.75) 0%, rgba(0,113,170,0.4) 60%);
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 2rem clamp(1rem, 4vw, 4rem);
    }
    [dir="rtl"] .featured-banner .overlay {
        background: linear-gradient(to right, rgba(0,71,130,0.75) 0%, rgba(0,113,170,0.4) 60%);
    }
    .featured-banner .overlay h3 { color: white; font-size: clamp(1.2rem, 3vw, 1.8rem); margin-bottom: 0.5rem; font-weight: 800; }
    .featured-banner .overlay p  { color: rgba(255,255,255,.85); font-size: 0.95rem; margin: 0; }

    @media (max-width: 768px) {
        .featured-banner { height: 180px; margin: 0 1rem 1.5rem; }
        .offers-section  { padding: 1.5rem 1rem; }
    }
</style>
@endsection

@section('content')

{{-- Hero Section --}}
<section class="hero-section">
    <div class="breadcrumb-nav">
        <a href="{{ route('home') }}">الرئيسية</a>
        <span>></span>
        <span>العروض والخصومات</span>
    </div>
    <h2>العروض والخصومات</h2>
    <p>استفد من عروضنا الحصرية على برامجنا التدريبية  المعتمدة — خصومات محدودة المدة لا تفوّتها.</p>
</section>

{{-- Featured Banner --}}
<div class="featured-banner">
    <img loading="lazy" src="{{ asset('lms-photos/3.png') }}" alt="العروض والخصومات" onerror="this.src='{{ asset('lms-photos/1.png') }}'" />
    <div class="overlay">
        <div>
            <h3>عروض حصرية على برامجنا التدريبية </h3>
            <p>سجّل الآن واستفد من أفضل الأسعار والخصومات المتاحة</p>
        </div>
    </div>
</div>

{{-- Filter Bar --}}
<div class="offer-filter-bar">
    <div class="container">
        <div class="offer-filter-inner">
            <button class="offer-flt active" onclick="filterOffers('all',this)">
                <i class="bi bi-grid-3x3-gap-fill"></i> الكل
            </button>
            <button class="offer-flt" onclick="filterOffers('percentage',this)">
                <i class="bi bi-percent"></i> نسبة مئوية
            </button>
            <button class="offer-flt" onclick="filterOffers('fixed',this)">
                <i class="bi bi-cash-coin"></i> مبلغ ثابت
            </button>
            @if($stats['has_override'])
            <button class="offer-flt" onclick="filterOffers('override',this)">
                <i class="bi bi-tag-fill"></i> سعر مباشر
            </button>
            @endif
            <span class="offer-count" id="visibleCount">
                <i class="bi bi-card-list"></i> {{ $offers->count() }} عرض
            </span>
        </div>
    </div>
</div>

{{-- Offers Grid --}}
<section class="offers-section">
    <div class="head">
        <p class="st-p">عروض وخصومات</p>
        <h2>اختر العرض المناسب لك</h2>
        <p>خصومات حصرية على برامجنا التدريبية  المعتمدة — عروض محدودة المدة تنتهي قريباً.</p>
    </div>

    <div class="courses-container" id="offersGrid">

    @forelse($offers as $offer)
    @php
        $isActive   = $offer->is_active;
        $isExpired  = $offer->is_expired;
        $isUpcoming = $offer->is_upcoming;

        if ($offer->discount_type === 'percentage') {
            $dNum   = number_format($offer->discount_value, 0);
            $dUnit  = '%';
            $dLabel = 'نسبة خصم';
            $typeClass = 'type-pct';
            $badgeText = 'خصم نسبي';
        } elseif ($offer->discount_type === 'fixed') {
            $dNum   = number_format($offer->discount_value, 0);
            $dUnit  = '<x-riyal />';
            $dLabel = 'خصم ثابت';
            $typeClass = 'type-fix';
            $badgeText = 'خصم ثابت';
        } else {
            $dNum   = number_format($offer->offer_price, 0);
            $dUnit  = '<x-riyal />';
            $dLabel = 'سعر مباشر';
            $typeClass = 'type-ovr';
            $badgeText = 'سعر مباشر';
        }
    @endphp

    <div class="offer-card-wrapper" data-type="{{ $offer->discount_type }}">
        <div class="offer-card {{ $isExpired ? 'is-expired' : '' }}">

            {{-- Discount visual top --}}
            <div class="offer-img-wrap {{ $typeClass }}">
                <div class="offer-disc-display">
                    <span class="disc-num">{{ $dNum }}</span>
                    <span class="disc-unit">{{ $dUnit }}</span>
                    <span class="disc-lbl">{{ $dLabel }}</span>
                </div>

                {{-- Type badge --}}
                <span class="offer-badge">{{ $badgeText }}</span>

                {{-- Status pill --}}
                @if($isExpired)
                    <span class="offer-status st-expired"><i class="bi bi-x-circle-fill"></i> منتهي</span>
                @elseif($isUpcoming)
                    <span class="offer-status st-upcoming"><i class="bi bi-clock-fill"></i> قادم قريباً</span>
                @else
                    <span class="offer-status st-active"><i class="bi bi-circle-fill" style="font-size:.4rem;"></i> نشط الآن</span>
                @endif
            </div>

            {{-- Card body --}}
            <div class="card-body">
                <h5 class="card-title">{{ $offer->title_ar }}</h5>

                <p class="card-program">
                    <i class="bi bi-mortarboard-fill"></i>
                    {{ $offer->program ? Str::limit($offer->program->name_ar, 35) : 'جميع البرامج التدريبية ' }}
                </p>

                {{-- Countdown --}}
                @if($isActive)
                <div class="offer-countdown" data-end="{{ $offer->end_date->endOfDay()->toISOString() }}">
                    <div class="cd-box">
                        <span class="cd-num cd-days">--</span>
                        <span class="cd-lbl">يوم</span>
                    </div>
                    <span class="cd-sep">:</span>
                    <div class="cd-box">
                        <span class="cd-num cd-hours">--</span>
                        <span class="cd-lbl">ساعة</span>
                    </div>
                    <span class="cd-sep">:</span>
                    <div class="cd-box">
                        <span class="cd-num cd-mins">--</span>
                        <span class="cd-lbl">دقيقة</span>
                    </div>
                    <span class="cd-sep">:</span>
                    <div class="cd-box">
                        <span class="cd-num cd-secs">--</span>
                        <span class="cd-lbl">ثانية</span>
                    </div>
                </div>
                @endif

                {{-- Promo code --}}
                @if($offer->code && $offer->discount_type !== 'override')
                <div class="offer-code">
                    <span class="code-lbl">كود</span>
                    <code id="code-{{ $offer->id }}">{{ $offer->code }}</code>
                    <button class="offer-copy"
                            onclick="copyCode('{{ $offer->code }}','{{ $offer->id }}')"
                            title="نسخ">
                        <i class="bi bi-clipboard" id="clip-{{ $offer->id }}"></i>
                    </button>
                </div>
                @endif

                {{-- Meta row --}}
                <div class="course-meta">
                    <span class="meta-date">
                        <i class="bi bi-calendar3"></i>
                        ينتهي {{ $offer->end_date->format('d/m/Y') }}
                    </span>
                    @if($offer->max_uses && $offer->uses_left !== null)
                    <span class="meta-seats">
                        <i class="bi bi-people-fill"></i>
                        {{ $offer->uses_left }} متبقي
                    </span>
                    @endif
                </div>
            </div>

            {{-- CTA --}}
            <div class="offer-cta">
                @if($isExpired)
                <a href="{{ route('register') }}" class="full-btn w-100 d-block text-center" style="background:#9ca3af;pointer-events:none;">
                    <i class="bi bi-clock-history"></i> انتهى هذا العرض
                </a>
                @elseif($isUpcoming)
                <a href="{{ route('register') }}" class="full-btn w-100 d-block text-center">
                    <i class="bi bi-bell-fill"></i> سجّل — العرض قادم قريباً
                </a>
                @else
                <a href="{{ route('register') }}" class="full-btn w-100 d-block text-center">
                    <i class="bi bi-mortarboard-fill"></i> سجّل الآن واستفد
                </a>
                @endif
            </div>

        </div>
    </div>
    @empty
    <div class="offers-empty">
        <div class="offers-empty-ico"><i class="bi bi-tags"></i></div>
        <h3>لا توجد عروض حالياً</h3>
        <p>تابعنا للاطلاع على أحدث العروض والخصومات</p>
    </div>
    @endforelse

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
                    برامجنا التدريبية  المعتمدة متاحة بأسعار مخفّضة لفترة محدودة.
                    سارع بالتسجيل قبل انتهاء العروض وابدأ مسيرتك التعليمية اليوم.
                </p>
                <a href="{{ route('register') }}" class="full-btn" style="display:inline-flex;align-items:center;gap:8px;font-size:1rem;padding:12px 28px;border-radius:10px;">
                    <i class="bi bi-arrow-left-circle-fill"></i>
                    سجّل الآن
                </a>
            </div>
        </div>
        <div class="col-lg-6 text-center mt-4 mt-lg-0">
            <img loading="lazy" src="{{ asset('lms2-photo/4.png') }}" alt="Training" style="max-width:420px;width:100%;border-radius:20px;" onerror="this.style.display='none'" />
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
    document.querySelectorAll('#offersGrid .offer-card-wrapper').forEach(c => {
        const show = type === 'all' || c.dataset.type === type;
        c.style.display = show ? '' : 'none';
        if (show) n++;
    });
    document.getElementById('visibleCount').innerHTML =
        `<i class="bi bi-card-list"></i> ${n} عرض`;
}

function copyCode(code, id) {
    navigator.clipboard.writeText(code).then(() => {
        const btn = document.getElementById('clip-' + id).parentElement;
        const ico = document.getElementById('clip-' + id);
        btn.classList.add('copied');
        ico.className = 'bi bi-clipboard-check';
        setTimeout(() => { btn.classList.remove('copied'); ico.className = 'bi bi-clipboard'; }, 2000);
    });
}

function updateCountdowns() {
    document.querySelectorAll('.offer-countdown[data-end]').forEach(el => {
        const diff = new Date(el.dataset.end).getTime() - Date.now();
        if (diff <= 0) {
            el.innerHTML = '<span style="color:#fff;font-weight:800;font-size:.85rem;width:100%;text-align:center;">انتهى العرض</span>';
            return;
        }
        const p = n => String(Math.floor(n)).padStart(2, '0');
        el.querySelector('.cd-days').textContent  = p(diff / 86400000);
        el.querySelector('.cd-hours').textContent = p((diff % 86400000) / 3600000);
        el.querySelector('.cd-mins').textContent  = p((diff % 3600000) / 60000);
        el.querySelector('.cd-secs').textContent  = p((diff % 60000) / 1000);
    });
}
updateCountdowns();
setInterval(updateCountdowns, 1000);
</script>
@endsection
