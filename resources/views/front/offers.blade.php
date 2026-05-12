@extends('layouts.front')

@section('title', 'العروض والخصومات — معهد الارتقاء')

@section('styles')
<style>
/* ── Hero ── */
.offers-hero {
    background: linear-gradient(135deg, #92400e 0%, #d97706 40%, #f59e0b 70%, #fbbf24 100%);
    padding: 4rem clamp(1.5rem, 5vw, 5rem) 5rem;
    position: relative;
    overflow: hidden;
}
.offers-hero::before {
    content:'';
    position:absolute; inset:0;
    background: radial-gradient(ellipse at 20% 50%, rgba(255,255,255,.08) 0%, transparent 60%),
                radial-gradient(ellipse at 80% 20%, rgba(255,255,255,.06) 0%, transparent 50%);
}
.hero-blob {
    position:absolute; border-radius:50%;
    background: rgba(255,255,255,.07);
    animation: floatBlob 8s ease-in-out infinite;
}
@keyframes floatBlob {
    0%,100% { transform: translateY(0) scale(1); }
    50%      { transform: translateY(-15px) scale(1.05); }
}
.offers-hero .container { position:relative; z-index:2; }

/* ── Stat chips ── */
.stat-chips { display:flex; gap:1rem; flex-wrap:wrap; margin-top:1.5rem; }
.stat-chip {
    display:inline-flex; align-items:center; gap:.5rem;
    background: rgba(255,255,255,.18); backdrop-filter:blur(8px);
    border: 1px solid rgba(255,255,255,.25);
    border-radius: 50px; padding: .45rem 1.1rem;
    color:#fff; font-size:.82rem; font-weight:700;
}
.stat-chip i { font-size:.95rem; }

/* ── Filter bar ── */
.filter-bar {
    background:#fff; border-radius:16px;
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
    padding:1rem 1.5rem;
    display:flex; align-items:center; gap:1rem; flex-wrap:wrap;
    margin: -2.5rem auto 2.5rem;
    position:relative; z-index:10;
}
.filter-btn {
    padding:.45rem 1.1rem; border-radius:50px; font-size:.82rem; font-weight:700;
    border: 2px solid #e5e7eb; background:#fff; color:#6b7280;
    cursor:pointer; transition:all .2s; white-space:nowrap;
}
.filter-btn.active, .filter-btn:hover {
    border-color:#d97706; background:#d97706; color:#fff;
}
.filter-count {
    margin-right:auto;
    font-size:.8rem; color:#9ca3af; font-weight:600;
}
[dir="ltr"] .filter-count { margin-right:0; margin-left:auto; }

/* ── Cards Grid ── */
.offers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
}

.offer-card {
    background:#fff; border-radius:20px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    overflow:hidden;
    transition: transform .25s, box-shadow .25s;
    border: 1px solid #f3f4f6;
    position:relative;
    display:flex; flex-direction:column;
}
.offer-card:hover {
    transform:translateY(-5px);
    box-shadow: 0 12px 35px rgba(0,0,0,.12);
}

/* top color band */
.offer-card-band {
    height:6px;
}

/* discount badge */
.discount-badge {
    position:absolute; top:1rem; left:1rem;
    width:64px; height:64px; border-radius:50%;
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    font-weight:900; color:#fff;
    box-shadow: 0 4px 15px rgba(0,0,0,.2);
    line-height:1;
    z-index:5;
}
[dir="ltr"] .discount-badge { left:auto; right:1rem; }
.discount-badge .disc-val { font-size:1.2rem; }
.discount-badge .disc-unit { font-size:.6rem; opacity:.85; margin-top:.1rem; }

/* offer image */
.offer-img {
    width:100%; height:160px; object-fit:cover;
}
.offer-img-placeholder {
    width:100%; height:160px;
    display:flex; align-items:center; justify-content:center;
    font-size:3.5rem;
}

.offer-body { padding:1.25rem; flex:1; display:flex; flex-direction:column; gap:.75rem; }

.offer-title { font-size:1rem; font-weight:800; color:#1f2937; line-height:1.4; }
.offer-desc  { font-size:.82rem; color:#6b7280; line-height:1.6; flex:1; }

.offer-meta {
    display:flex; flex-wrap:wrap; gap:.5rem;
}
.offer-tag {
    display:inline-flex; align-items:center; gap:.35rem;
    font-size:.73rem; font-weight:700; padding:.25rem .7rem; border-radius:20px;
}
.tag-program  { background:#dbeafe; color:#1d4ed8; }
.tag-date     { background:#fef3c7; color:#92400e; }
.tag-uses     { background:#dcfce7; color:#15803d; }
.tag-override { background:#ede9fe; color:#6d28d9; }

/* code copy box */
.code-box {
    display:flex; align-items:center; gap:.5rem;
    background:#f9fafb; border:1.5px dashed #d1d5db;
    border-radius:10px; padding:.55rem .9rem;
    margin-top:.25rem;
}
.code-box code {
    font-family:monospace; font-size:.95rem; font-weight:800;
    letter-spacing:2px; color:#374151; flex:1;
}
.code-copy-btn {
    background:#f3f4f6; border:none; border-radius:7px;
    width:30px; height:30px; display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition:all .2s; color:#6b7280; flex-shrink:0;
}
.code-copy-btn:hover { background:#d97706; color:#fff; }
.code-copy-btn.copied { background:#10b981; color:#fff; }

/* countdown */
.countdown-wrap {
    display:flex; gap:.5rem; justify-content:center;
    padding:.65rem; background:#fffbeb; border-radius:10px;
    border:1px solid #fde68a;
}
.cd-unit { text-align:center; }
.cd-num { font-size:1.25rem; font-weight:900; color:#b45309; line-height:1; }
.cd-lbl { font-size:.58rem; color:#92400e; font-weight:700; margin-top:.1rem; }
.cd-sep { font-size:1.2rem; font-weight:900; color:#fbbf24; align-self:flex-start; padding-top:2px; }

/* badge — expired / upcoming */
.status-ribbon {
    position:absolute; top:1rem; right:1rem;
    font-size:.68rem; font-weight:800; padding:.3rem .75rem; border-radius:20px;
    z-index:5;
}
[dir="ltr"] .status-ribbon { right:auto; left:1rem; }
.ribbon-expired  { background:#fee2e2; color:#dc2626; }
.ribbon-upcoming { background:#dbeafe; color:#1d4ed8; }

/* register CTA */
.offer-cta {
    display:block; text-align:center;
    background:linear-gradient(135deg, #d97706, #f59e0b);
    color:#fff !important; font-weight:800; font-size:.88rem;
    padding:.7rem 1rem; border-radius:12px;
    text-decoration:none; transition:all .2s;
    margin-top:.25rem;
}
.offer-cta:hover { transform:translateY(-1px); box-shadow:0 5px 18px rgba(217,119,6,.4); }

/* no offers */
.no-offers {
    grid-column:1/-1; text-align:center; padding:4rem 1rem;
}

/* expired/inactive card overlay */
.card-expired { opacity:.6; }
.card-expired .offer-cta { background:#9ca3af; pointer-events:none; }

@media(max-width:576px) {
    .offers-grid { grid-template-columns:1fr; }
    .filter-bar { flex-wrap:wrap; }
}
</style>
@endsection

@section('content')

{{-- ── Hero ── --}}
<div class="offers-hero">
    <div class="hero-blob" style="width:320px;height:320px;top:-80px;right:-60px;animation-delay:0s;"></div>
    <div class="hero-blob" style="width:180px;height:180px;bottom:-40px;left:10%;animation-delay:3s;"></div>

    <div class="container">
        <div class="breadcrumb-nav" style="margin-bottom:1.25rem;">
            <a href="{{ route('home') }}" style="color:rgba(255,255,255,.75);font-size:.82rem;">الرئيسية</a>
            <span style="color:rgba(255,255,255,.5);margin:0 .4rem;">/</span>
            <span style="color:#fff;font-size:.82rem;font-weight:700;">العروض والخصومات</span>
        </div>

        <div style="display:flex;align-items:flex-start;gap:1.5rem;flex-wrap:wrap;">
            <div style="flex:1;min-width:260px;">
                <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);border-radius:50px;padding:.3rem .85rem;margin-bottom:.85rem;">
                    <span style="color:#fde68a;font-size:.75rem;font-weight:800;">🏷️ عروض حصرية</span>
                </div>
                <h1 style="color:#fff;font-size:clamp(1.6rem,4vw,2.5rem);font-weight:900;line-height:1.2;margin:0 0 .75rem;">
                    العروض والخصومات
                </h1>
                <p style="color:rgba(255,255,255,.85);font-size:.95rem;line-height:1.7;max-width:520px;margin:0;">
                    استفد من عروضنا الحصرية على البرامج التدريبية — وفّر على رسوم التسجيل واستثمر في مستقبلك المهني.
                </p>

                <div class="stat-chips">
                    <div class="stat-chip">
                        <i class="bi bi-tags-fill"></i>
                        <span>{{ $stats['total'] }} عرض متاح</span>
                    </div>
                    <div class="stat-chip">
                        <i class="bi bi-lightning-fill"></i>
                        <span>{{ $stats['active'] }} نشط الآن</span>
                    </div>
                    @if($stats['upcoming'] > 0)
                    <div class="stat-chip">
                        <i class="bi bi-clock"></i>
                        <span>{{ $stats['upcoming'] }} قادم قريباً</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- big discount icon --}}
            <div style="flex-shrink:0;width:110px;height:110px;background:rgba(255,255,255,.15);border-radius:24px;display:flex;align-items:center;justify-content:center;font-size:3.5rem;backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.25);">
                🏷️
            </div>
        </div>
    </div>
</div>

{{-- ── Offers Container ── --}}
<div class="container" style="padding-top:0;padding-bottom:4rem;">

    {{-- Filter bar --}}
    <div class="filter-bar">
        <button class="filter-btn active" onclick="filterOffers('all', this)">الكل</button>
        <button class="filter-btn" onclick="filterOffers('percentage', this)">📊 نسبة مئوية</button>
        <button class="filter-btn" onclick="filterOffers('fixed', this)">💰 مبلغ ثابت</button>
        @if($stats['has_override'])
        <button class="filter-btn" onclick="filterOffers('override', this)">🏷️ سعر مباشر</button>
        @endif
        <span class="filter-count" id="visibleCount">{{ $offers->count() }} عرض</span>
    </div>

    {{-- Grid --}}
    <div class="offers-grid" id="offersGrid">

        @forelse($offers as $offer)
        @php
            $isActive   = $offer->is_active;
            $isExpired  = $offer->is_expired;
            $isUpcoming = $offer->is_upcoming;

            $colors = match($offer->discount_type) {
                'percentage' => ['band'=>'linear-gradient(90deg,#0ea5e9,#3b82f6)', 'badge'=>'#2563eb', 'emoji'=>'📊'],
                'fixed'      => ['band'=>'linear-gradient(90deg,#10b981,#059669)', 'badge'=>'#059669', 'emoji'=>'💰'],
                'override'   => ['band'=>'linear-gradient(90deg,#8b5cf6,#a855f7)', 'badge'=>'#7c3aed', 'emoji'=>'🏷️'],
                default      => ['band'=>'linear-gradient(90deg,#d97706,#f59e0b)', 'badge'=>'#d97706', 'emoji'=>'🏷️'],
            };

            // build discount label
            if ($offer->discount_type === 'percentage') {
                $discVal  = number_format($offer->discount_value, 0);
                $discUnit = '%';
            } elseif ($offer->discount_type === 'fixed') {
                $discVal  = number_format($offer->discount_value, 0);
                $discUnit = 'ر.س';
            } else {
                $discVal  = number_format($offer->offer_price, 0);
                $discUnit = 'ر.س';
            }
        @endphp

        <div class="offer-card {{ $isExpired ? 'card-expired' : '' }}"
             data-type="{{ $offer->discount_type }}">

            {{-- top band --}}
            <div class="offer-card-band" style="background:{{ $colors['band'] }};"></div>

            {{-- status ribbon --}}
            @if($isExpired)
                <span class="status-ribbon ribbon-expired">منتهي</span>
            @elseif($isUpcoming)
                <span class="status-ribbon ribbon-upcoming">قادم</span>
            @endif

            {{-- discount badge --}}
            <div class="discount-badge" style="background:{{ $colors['badge'] }};">
                <span class="disc-val">{{ $discVal }}</span>
                <span class="disc-unit">{{ $discUnit }}</span>
            </div>

            {{-- image / placeholder --}}
            @if($offer->image)
                <img src="{{ asset('storage/'.$offer->image) }}" alt="{{ $offer->title_ar }}" class="offer-img">
            @else
                <div class="offer-img-placeholder" style="background:linear-gradient(135deg,#fef3c7,#fde68a);">
                    {{ $colors['emoji'] }}
                </div>
            @endif

            {{-- body --}}
            <div class="offer-body">
                <h3 class="offer-title">{{ $offer->title_ar }}</h3>

                @if($offer->description_ar)
                <p class="offer-desc">{{ Str::limit($offer->description_ar, 100) }}</p>
                @endif

                {{-- meta tags --}}
                <div class="offer-meta">
                    {{-- type --}}
                    @if($offer->discount_type === 'override')
                        <span class="offer-tag tag-override">🏷️ سعر مباشر {{ number_format($offer->offer_price,0) }} ر.س</span>
                    @elseif($offer->discount_type === 'percentage')
                        <span class="offer-tag tag-program">📊 خصم {{ number_format($offer->discount_value,0) }}%</span>
                    @else
                        <span class="offer-tag tag-program">💰 خصم {{ number_format($offer->discount_value,0) }} ر.س</span>
                    @endif

                    {{-- program --}}
                    @if($offer->program)
                        <span class="offer-tag tag-program">
                            <i class="bi bi-book"></i> {{ $offer->program->name_ar }}
                        </span>
                    @else
                        <span class="offer-tag" style="background:#f3f4f6;color:#374151;">
                            <i class="bi bi-globe"></i> جميع البرامج
                        </span>
                    @endif

                    {{-- end date --}}
                    <span class="offer-tag tag-date">
                        <i class="bi bi-calendar-x"></i>
                        ينتهي {{ $offer->end_date->format('Y/m/d') }}
                    </span>

                    {{-- uses left --}}
                    @if($offer->max_uses)
                        <span class="offer-tag tag-uses">
                            <i class="bi bi-people"></i>
                            {{ $offer->uses_left }} متبقية
                        </span>
                    @endif
                </div>

                {{-- countdown (only for active offers) --}}
                @if($isActive)
                <div class="countdown-wrap" data-end="{{ $offer->end_date->endOfDay()->toISOString() }}">
                    <div class="cd-unit"><div class="cd-num cd-days">--</div><div class="cd-lbl">يوم</div></div>
                    <div class="cd-sep">:</div>
                    <div class="cd-unit"><div class="cd-num cd-hours">--</div><div class="cd-lbl">ساعة</div></div>
                    <div class="cd-sep">:</div>
                    <div class="cd-unit"><div class="cd-num cd-mins">--</div><div class="cd-lbl">دقيقة</div></div>
                    <div class="cd-sep">:</div>
                    <div class="cd-unit"><div class="cd-num cd-secs">--</div><div class="cd-lbl">ثانية</div></div>
                </div>
                @endif

                {{-- code --}}
                @if($offer->code && $offer->discount_type !== 'override')
                <div class="code-box">
                    <code id="code-{{ $offer->id }}">{{ $offer->code }}</code>
                    <button class="code-copy-btn" onclick="copyCode('{{ $offer->code }}', '{{ $offer->id }}')" title="نسخ الكود">
                        <i class="bi bi-clipboard" id="clip-{{ $offer->id }}"></i>
                    </button>
                </div>
                @endif

                {{-- CTA --}}
                <a href="{{ route('register') }}" class="offer-cta">
                    @if($isExpired)
                        ⏱️ انتهى العرض
                    @elseif($isUpcoming)
                        🔔 سجّل — العرض قريباً
                    @else
                        🎓 سجّل الآن واستفد
                    @endif
                </a>
            </div>
        </div>
        @empty
        <div class="no-offers">
            <div style="font-size:4rem;margin-bottom:1rem;">🏷️</div>
            <h3 style="font-size:1.1rem;font-weight:800;color:#374151;margin-bottom:.5rem;">لا توجد عروض حالياً</h3>
            <p style="color:#9ca3af;font-size:.9rem;">تابعنا للاطلاع على أحدث العروض والخصومات</p>
        </div>
        @endforelse

    </div>
</div>

@endsection

@section('scripts')
<script>
// ── Filter ──────────────────────────────────────────────────────────
function filterOffers(type, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    let count = 0;
    document.querySelectorAll('.offer-card').forEach(card => {
        const match = type === 'all' || card.dataset.type === type;
        card.style.display = match ? '' : 'none';
        if (match) count++;
    });
    document.getElementById('visibleCount').textContent = count + ' عرض';
}

// ── Copy code ───────────────────────────────────────────────────────
function copyCode(code, id) {
    navigator.clipboard.writeText(code).then(() => {
        const btn  = document.querySelector(`#clip-${id}`).parentElement;
        const icon = document.getElementById(`clip-${id}`);
        btn.classList.add('copied');
        icon.className = 'bi bi-clipboard-check';
        setTimeout(() => {
            btn.classList.remove('copied');
            icon.className = 'bi bi-clipboard';
        }, 2000);
    });
}

// ── Countdown ───────────────────────────────────────────────────────
function updateCountdowns() {
    document.querySelectorAll('.countdown-wrap[data-end]').forEach(el => {
        const end  = new Date(el.dataset.end).getTime();
        const now  = Date.now();
        const diff = end - now;

        if (diff <= 0) {
            el.innerHTML = '<span style="color:#dc2626;font-weight:800;font-size:.85rem;width:100%;text-align:center;">انتهى العرض</span>';
            return;
        }

        const days  = Math.floor(diff / 86400000);
        const hours = Math.floor((diff % 86400000) / 3600000);
        const mins  = Math.floor((diff % 3600000)  / 60000);
        const secs  = Math.floor((diff % 60000)    / 1000);

        const fmt = n => String(n).padStart(2,'0');

        el.querySelector('.cd-days').textContent  = fmt(days);
        el.querySelector('.cd-hours').textContent = fmt(hours);
        el.querySelector('.cd-mins').textContent  = fmt(mins);
        el.querySelector('.cd-secs').textContent  = fmt(secs);
    });
}

updateCountdowns();
setInterval(updateCountdowns, 1000);
</script>
@endsection
