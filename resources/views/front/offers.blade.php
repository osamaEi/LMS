@extends('layouts.front')

@section('title', 'العروض والخصومات — معهد الارتقاء')

@section('styles')
<style>
/* ── Hero ── */
.offers-hero {
    background: linear-gradient(135deg, #7c2d12 0%, #b45309 30%, #d97706 60%, #f59e0b 85%, #fbbf24 100%);
    padding: 4rem clamp(1.5rem, 5vw, 5rem) 6rem;
    position: relative;
    overflow: hidden;
}
.offers-hero::before {
    content:'';
    position:absolute; inset:0;
    background:
        radial-gradient(ellipse at 15% 60%, rgba(255,255,255,.12) 0%, transparent 55%),
        radial-gradient(ellipse at 85% 15%, rgba(251,191,36,.25) 0%, transparent 50%),
        radial-gradient(ellipse at 50% 100%, rgba(0,0,0,.15) 0%, transparent 60%);
}
.hero-blob {
    position:absolute; border-radius:50%;
    background: rgba(255,255,255,.08);
    animation: floatBlob 8s ease-in-out infinite;
}
.hero-blob-2 {
    position:absolute; border-radius:50%;
    background: rgba(251,191,36,.18);
    animation: floatBlob 11s ease-in-out infinite reverse;
}
@keyframes floatBlob {
    0%,100% { transform: translateY(0) scale(1); }
    50%      { transform: translateY(-18px) scale(1.06); }
}
.offers-hero .container { position:relative; z-index:2; }

/* ── Hero label badge ── */
.hero-label-badge {
    display:inline-flex; align-items:center; gap:.55rem;
    background: rgba(255,255,255,.2); backdrop-filter:blur(10px);
    border: 1.5px solid rgba(255,255,255,.4);
    border-radius: 50px; padding:.4rem 1.1rem;
    margin-bottom: 1rem;
}
.hero-label-badge .badge-dot {
    width:8px; height:8px; border-radius:50%;
    background:#fde68a; box-shadow:0 0 0 3px rgba(253,230,138,.35);
    flex-shrink:0; animation: pulseDot 2s infinite;
}
@keyframes pulseDot {
    0%,100%{ box-shadow:0 0 0 3px rgba(253,230,138,.35); }
    50%    { box-shadow:0 0 0 7px rgba(253,230,138,.12); }
}
.hero-label-badge span { color:#fef3c7; font-size:.8rem; font-weight:800; letter-spacing:.04em; }

/* ── Stat chips ── */
.stat-chips { display:flex; gap:.85rem; flex-wrap:wrap; margin-top:1.75rem; }
.stat-chip {
    display:inline-flex; align-items:center; gap:.5rem;
    background: rgba(255,255,255,.18); backdrop-filter:blur(10px);
    border: 1.5px solid rgba(255,255,255,.3);
    border-radius: 50px; padding: .5rem 1.15rem;
    color:#fff; font-size:.82rem; font-weight:700;
    box-shadow: 0 2px 12px rgba(0,0,0,.12);
}
.stat-chip i { font-size:1rem; color:#fde68a; }
.stat-chip .chip-num { font-size:1.05rem; font-weight:900; }

/* ── Filter bar ── */
.filter-bar {
    background:#fff; border-radius:20px;
    box-shadow: 0 8px 30px rgba(0,0,0,.1), 0 1px 4px rgba(0,0,0,.05);
    padding:1rem 1.5rem;
    display:flex; align-items:center; gap:.75rem; flex-wrap:wrap;
    margin: -2.5rem auto 2.5rem;
    position:relative; z-index:10;
    border: 1px solid rgba(0,0,0,.04);
}
.filter-bar-label {
    font-size:.75rem; font-weight:800; color:#9ca3af;
    text-transform:uppercase; letter-spacing:.08em;
    white-space:nowrap; margin-left:.25rem;
}
[dir="ltr"] .filter-bar-label { margin-left:0; margin-right:.25rem; }
.filter-btn {
    padding:.5rem 1.15rem; border-radius:50px; font-size:.82rem; font-weight:700;
    border: 2px solid #e5e7eb; background:#f9fafb; color:#6b7280;
    cursor:pointer; transition:all .22s; white-space:nowrap;
    display:inline-flex; align-items:center; gap:.35rem;
}
.filter-btn:hover {
    border-color:#d97706; background:#fff7ed; color:#d97706;
    transform:translateY(-1px);
}
.filter-btn.active {
    border-color:#d97706; background:linear-gradient(135deg,#d97706,#f59e0b); color:#fff;
    box-shadow: 0 3px 12px rgba(217,119,6,.35);
}
.filter-count {
    margin-right:auto;
    font-size:.8rem; color:#6b7280; font-weight:700;
    background:#f3f4f6; padding:.3rem .85rem; border-radius:20px;
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
    {{-- floating blobs --}}
    <div class="hero-blob"   style="width:360px;height:360px;top:-100px;right:-70px;animation-delay:0s;"></div>
    <div class="hero-blob"   style="width:200px;height:200px;bottom:-50px;left:8%;animation-delay:3s;"></div>
    <div class="hero-blob-2" style="width:140px;height:140px;top:30%;left:3%;animation-delay:1.5s;"></div>
    <div class="hero-blob-2" style="width:90px;height:90px;bottom:15%;right:20%;animation-delay:5s;"></div>

    <div class="container">
        {{-- breadcrumb --}}
        <div style="display:flex;align-items:center;gap:.4rem;margin-bottom:1.5rem;">
            <a href="{{ route('home') }}" style="color:rgba(255,255,255,.65);font-size:.8rem;text-decoration:none;transition:color .2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,.65)'">الرئيسية</a>
            <i class="bi bi-chevron-left" style="color:rgba(255,255,255,.4);font-size:.65rem;"></i>
            <span style="color:#fde68a;font-size:.8rem;font-weight:700;">العروض والخصومات</span>
        </div>

        <div style="display:flex;align-items:center;gap:2rem;flex-wrap:wrap;">
            <div style="flex:1;min-width:280px;">

                {{-- label badge --}}
                <div class="hero-label-badge">
                    <span class="badge-dot"></span>
                    <span>🏷️ عروض حصرية ومحدودة</span>
                </div>

                {{-- main heading --}}
                <h1 style="color:#fff;font-size:clamp(1.75rem,4.5vw,2.8rem);font-weight:900;line-height:1.18;margin:0 0 1rem;text-shadow:0 2px 16px rgba(0,0,0,.25);">
                    العروض<br>
                    <span style="color:#fde68a;">والخصومات</span>
                </h1>

                {{-- sub text --}}
                <p style="color:rgba(255,255,255,.9);font-size:.95rem;line-height:1.75;max-width:500px;margin:0;text-shadow:0 1px 6px rgba(0,0,0,.2);">
                    استفد من عروضنا الحصرية على البرامج التدريبية —
                    <strong style="color:#fde68a;">وفّر على رسوم التسجيل</strong>
                    واستثمر في مستقبلك المهني.
                </p>

                {{-- stat chips --}}
                <div class="stat-chips">
                    <div class="stat-chip">
                        <i class="bi bi-tags-fill"></i>
                        <span class="chip-num">{{ $stats['total'] }}</span>
                        <span>عرض متاح</span>
                    </div>
                    <div class="stat-chip">
                        <i class="bi bi-lightning-charge-fill"></i>
                        <span class="chip-num">{{ $stats['active'] }}</span>
                        <span>نشط الآن</span>
                    </div>
                    @if($stats['upcoming'] > 0)
                    <div class="stat-chip">
                        <i class="bi bi-hourglass-split"></i>
                        <span class="chip-num">{{ $stats['upcoming'] }}</span>
                        <span>قادم قريباً</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- decorative icon box --}}
            <div style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;gap:1rem;">
                <div style="width:120px;height:120px;background:rgba(255,255,255,.18);border-radius:28px;display:flex;align-items:center;justify-content:center;font-size:4rem;backdrop-filter:blur(12px);border:2px solid rgba(255,255,255,.35);box-shadow:0 8px 32px rgba(0,0,0,.2);">
                    🏷️
                </div>
                <div style="background:rgba(253,230,138,.25);border:1.5px solid rgba(253,230,138,.5);border-radius:12px;padding:.45rem 1rem;text-align:center;">
                    <div style="color:#fde68a;font-size:1.4rem;font-weight:900;line-height:1;">{{ $stats['active'] }}</div>
                    <div style="color:rgba(255,255,255,.8);font-size:.68rem;font-weight:700;margin-top:.1rem;">عرض نشط</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Offers Container ── --}}
<div class="container" style="padding-top:0;padding-bottom:4rem;">

    {{-- Filter bar --}}
    <div class="filter-bar">
        <span class="filter-bar-label"><i class="bi bi-funnel-fill" style="color:#d97706;"></i> تصفية</span>
        <button class="filter-btn active" onclick="filterOffers('all', this)">🔍 الكل</button>
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
