@extends('layouts.dashboard')

@section('title', 'العروض والخصومات')

@push('styles')
<style>
    .offers-page { max-width:1100px; margin:0 auto; }

    .offers-hero {
        background: linear-gradient(135deg,#0071AA 0%,#003d5c 100%);
        border-radius: 24px; padding: 2.5rem 2rem;
        text-align: center; position: relative; overflow: hidden;
        margin-bottom: 2rem;
    }
    .offers-hero::before {
        content:''; position:absolute; inset:0;
        background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        animation: slidePattern 25s linear infinite;
    }
    @keyframes slidePattern { 0%{transform:translateX(0)} 100%{transform:translateX(-60px)} }

    .offer-card {
        background: #fff;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        transition: all .3s cubic-bezier(.4,0,.2,1);
        display: flex;
        flex-direction: column;
    }
    .dark .offer-card { background: #1f2937; }
    .offer-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0,0,0,.12);
    }

    .offer-img { width:100%; height:160px; object-fit:cover; display:block; }
    .offer-img-placeholder {
        width:100%; height:160px; display:flex; align-items:center; justify-content:center;
        font-size:4rem;
    }

    .offer-body { padding:1.5rem; flex:1; display:flex; flex-direction:column; }

    .discount-pill {
        display: inline-flex; align-items:center; gap:.35rem;
        padding:.4rem 1.1rem; border-radius:999px; font-size:1rem; font-weight:900;
        margin-bottom: .85rem;
    }
    .pill-pct   { background:linear-gradient(135deg,#0071AA,#003d5c); color:#fff; }
    .pill-fixed { background:linear-gradient(135deg,#10b981,#059669); color:#fff; }

    .code-box {
        display: flex; align-items:center; gap:.5rem; justify-content:center;
        background: #f8fafc; border: 2px dashed #e2e8f0;
        border-radius: 12px; padding: .6rem 1rem; margin: .85rem 0;
        cursor: pointer; transition: all .2s;
    }
    .code-box:hover { border-color:#0071AA; background:#eff6ff; }
    .dark .code-box { background:#374151; border-color:#4b5563; }
    .code-text { font-family:monospace; font-size:1.05rem; font-weight:800; color:#0071AA; letter-spacing:2px; }

    .timer-wrap { display:flex; gap:.5rem; justify-content:center; margin:.75rem 0; }
    .timer-box { background:linear-gradient(135deg,#1e3a5f,#0071AA); border-radius:10px; padding:.4rem .6rem; min-width:48px; text-align:center; }
    .timer-num { font-size:1.3rem; font-weight:900; color:#fff; line-height:1; display:block; }
    .timer-lbl { font-size:.6rem; color:rgba(255,255,255,.7); margin-top:.1rem; display:block; }

    .offer-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #f1f5f9;
        display: flex; align-items:center; justify-content:space-between; gap:.75rem; flex-wrap:wrap;
    }
    .dark .offer-footer { border-color:#374151; }

    .contact-btn {
        display:inline-flex; align-items:center; gap:.4rem; padding:.6rem 1.25rem;
        border-radius:12px; background:linear-gradient(135deg,#0071AA,#005a88);
        color:#fff; font-weight:700; font-size:.85rem; text-decoration:none;
        transition:all .2s;
    }
    .contact-btn:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(0,113,170,.3); color:#fff; }

    .days-badge {
        display:inline-flex; align-items:center; gap:.3rem;
        padding:.35rem .8rem; border-radius:20px; font-size:.75rem; font-weight:700;
    }
    .badge-hot    { background:rgba(239,68,68,.1); color:#dc2626; }
    .badge-normal { background:rgba(16,185,129,.1); color:#059669; }
    .badge-soon   { background:rgba(245,158,11,.1); color:#d97706; }

    .offers-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:1.5rem; }

    .section-title {
        font-size:1.15rem; font-weight:900; color:#111827;
        display:flex; align-items:center; gap:.75rem; margin-bottom:1.25rem;
    }
    .dark .section-title { color:#f9fafb; }
    .section-title::after { content:''; flex:1; height:2px; background:linear-gradient(to left,transparent,#e5e7eb); border-radius:2px; }

    .no-offers { text-align:center; padding:4rem 2rem; background:#fff; border-radius:22px; }
    .dark .no-offers { background:#1f2937; }

    .copied-toast {
        position:fixed; bottom:1.5rem; left:50%; transform:translateX(-50%) translateY(100px);
        background:#10b981; color:#fff; font-weight:700; font-size:.88rem;
        padding:.65rem 1.5rem; border-radius:999px; box-shadow:0 8px 24px rgba(16,185,129,.35);
        transition:transform .3s cubic-bezier(.4,0,.2,1);
        z-index:9999; white-space:nowrap;
    }
    .copied-toast.show { transform:translateX(-50%) translateY(0); }
</style>
@endpush

@section('content')
<div class="offers-page" dir="rtl">

    {{-- Hero --}}
    <div class="offers-hero">
        <div style="position:relative;z-index:5;">
            <div style="font-size:3.5rem;margin-bottom:.75rem;">🎁</div>
            <h1 style="font-size:2rem;font-weight:900;color:#fff;margin:0 0 .5rem;">العروض والخصومات</h1>
            <p style="font-size:1rem;color:rgba(255,255,255,.75);margin:0 0 1.5rem;">لا تفوّت أفضل عروض البرامج التدريبية  — كودات خصم حصرية لفترة محدودة</p>
            @if($activeOffers->count() > 0)
            <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.15);border-radius:999px;padding:.5rem 1.25rem;font-size:.88rem;font-weight:700;color:#fff;backdrop-filter:blur(4px);">
                <span style="width:8px;height:8px;border-radius:50%;background:#4ade80;animation:blink 1.5s infinite;display:inline-block;"></span>
                {{ $activeOffers->count() }} عرض نشط الآن
            </div>
            @endif
        </div>
    </div>

    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.4} }

    @if($activeOffers->count())
    {{-- Active Offers --}}
    <div class="section-title">
        <svg style="width:22px;height:22px;color:#0071AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        العروض النشطة
    </div>

    <div class="offers-grid" style="margin-bottom:2.5rem;">
    @foreach($activeOffers as $offer)
    @php
        $daysLeft = $offer->days_left;
        $badgeClass = $daysLeft <= 3 ? 'badge-hot' : ($daysLeft <= 14 ? 'badge-soon' : 'badge-normal');
    @endphp
    <div class="offer-card">
        @if($offer->image)
            <img src="{{ asset('storage/'.$offer->image) }}" class="offer-img" alt="{{ $offer->title_ar }}">
        @else
            <div class="offer-img-placeholder" style="background:linear-gradient(135deg,{{ $offer->discount_type=='percentage'?'#dbeafe,#bfdbfe':'#dcfce7,#bbf7d0' }});">
                {{ $offer->discount_type=='percentage'?'💸':'🎫' }}
            </div>
        @endif

        <div class="offer-body">
            {{-- Discount pill --}}
            <div>
                <span class="discount-pill {{ $offer->discount_type=='percentage'?'pill-pct':'pill-fixed' }}">
                    🏷 {{ $offer->discount_label }} {{ $offer->discount_type=='percentage'?'خصم':'خصم مباشر' }}
                </span>
            </div>

            <h2 style="font-size:1.05rem;font-weight:900;color:#111827;margin:.4rem 0;line-height:1.4;">
                {{ $offer->title_ar }}
            </h2>

            @if($offer->description_ar)
            <p style="font-size:.82rem;color:#6b7280;margin:0 0 .75rem;line-height:1.6;">{{ $offer->description_ar }}</p>
            @endif

            @if($offer->program)
            <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(139,92,246,.08);border-radius:8px;padding:.3rem .75rem;font-size:.75rem;font-weight:700;color:#7c3aed;margin-bottom:.75rem;">
                🎓 {{ $offer->program->name_ar }}
            </div>
            @else
            <div style="display:inline-flex;align-items:center;gap:.4rem;background:rgba(59,130,246,.08);border-radius:8px;padding:.3rem .75rem;font-size:.75rem;font-weight:700;color:#2563eb;margin-bottom:.75rem;">
                🌐 جميع البرامج
            </div>
            @endif

            {{-- Promo code --}}
            @if($offer->code)
            <div class="code-box" onclick="copyCode('{{ $offer->code }}', this)">
                <svg style="width:15px;height:15px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                <span class="code-text">{{ $offer->code }}</span>
                <span style="font-size:.7rem;color:#9ca3af;margin-right:auto;">انقر للنسخ</span>
            </div>
            @endif

            {{-- Countdown timer --}}
            <div class="timer-wrap" data-end="{{ $offer->end_date->endOfDay()->toIso8601String() }}">
                <div class="timer-box"><span class="timer-num" data-days>{{ $daysLeft }}</span><span class="timer-lbl">يوم</span></div>
                <div class="timer-box"><span class="timer-num" data-hours>00</span><span class="timer-lbl">ساعة</span></div>
                <div class="timer-box"><span class="timer-num" data-mins>00</span><span class="timer-lbl">دقيقة</span></div>
                <div class="timer-box"><span class="timer-num" data-secs>00</span><span class="timer-lbl">ثانية</span></div>
            </div>

            @if($offer->max_uses)
            @php $usePct = min(100, ($offer->uses_count / $offer->max_uses) * 100); @endphp
            <div style="margin-top:.5rem;">
                <div style="display:flex;justify-content:space-between;font-size:.72rem;color:#9ca3af;margin-bottom:.3rem;">
                    <span>الاستخدامات</span>
                    <span>{{ $offer->uses_count }}/{{ $offer->max_uses }}</span>
                </div>
                <div style="height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
                    <div style="height:100%;border-radius:3px;background:{{ $usePct>=90?'#ef4444':($usePct>=60?'#f59e0b':'#10b981') }};width:{{ $usePct }}%;"></div>
                </div>
            </div>
            @endif
        </div>

        <div class="offer-footer">
            <div>
                <span class="days-badge {{ $badgeClass }}">
                    ⏰ {{ $daysLeft == 0 ? 'ينتهي اليوم!' : 'متبقي '.$daysLeft.' يوم' }}
                </span>
                <div style="font-size:.7rem;color:#9ca3af;margin-top:.25rem;">
                    ينتهي {{ $offer->end_date->format('Y/m/d') }}
                </div>
            </div>
            <a href="{{ route('student.payments.index') }}" class="contact-btn">
                <svg style="width:15px;height:15px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                استفد من العرض
            </a>
        </div>
    </div>
    @endforeach
    </div>
    @endif

    @if($upcomingOffers->count())
    {{-- Upcoming Offers --}}
    <div class="section-title">
        <svg style="width:22px;height:22px;color:#f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        عروض قادمة قريباً
    </div>

    <div class="offers-grid" style="margin-bottom:2rem;">
    @foreach($upcomingOffers as $offer)
    <div class="offer-card" style="opacity:.85;">
        @if($offer->image)
            <img src="{{ asset('storage/'.$offer->image) }}" class="offer-img" alt="{{ $offer->title_ar }}" style="filter:grayscale(30%);">
        @else
            <div class="offer-img-placeholder" style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);">🔜</div>
        @endif
        <div class="offer-body">
            <span class="discount-pill" style="background:linear-gradient(135deg,#6b7280,#4b5563);">
                🕐 قادم — {{ $offer->discount_label }} خصم
            </span>
            <h2 style="font-size:1rem;font-weight:900;color:#111827;margin:.4rem 0 .3rem;">{{ $offer->title_ar }}</h2>
            @if($offer->program)
            <div style="font-size:.75rem;color:#7c3aed;font-weight:700;margin-bottom:.5rem;">🎓 {{ $offer->program->name_ar }}</div>
            @endif
            <div style="font-size:.8rem;color:#6b7280;">
                📅 يبدأ في {{ $offer->start_date->format('Y/m/d') }}
            </div>
        </div>
        <div class="offer-footer">
            <span style="font-size:.78rem;color:#9ca3af;font-weight:600;">ينتهي {{ $offer->end_date->format('Y/m/d') }}</span>
        </div>
    </div>
    @endforeach
    </div>
    @endif

    @if($activeOffers->isEmpty() && $upcomingOffers->isEmpty())
    <div class="no-offers">
        <div style="font-size:4rem;margin-bottom:1rem;">🎁</div>
        <h3 style="font-size:1.15rem;font-weight:800;color:#374151;margin:0 0 .5rem;">لا توجد عروض متاحة حالياً</h3>
        <p style="font-size:.88rem;color:#9ca3af;margin:0;">تابعنا لتكون أول من يعرف بالعروض الجديدة</p>
    </div>
    @endif

</div>

<div class="copied-toast" id="copiedToast">✅ تم نسخ الكود!</div>

@push('scripts')
<script>
function copyCode(code, el) {
    navigator.clipboard.writeText(code).then(function() {
        var toast = document.getElementById('copiedToast');
        toast.classList.add('show');
        setTimeout(function() { toast.classList.remove('show'); }, 2200);
    });
}

// Countdown timers
document.querySelectorAll('.timer-wrap[data-end]').forEach(function(wrap) {
    function tick() {
        var end = new Date(wrap.dataset.end).getTime();
        var now = Date.now();
        var diff = Math.max(0, end - now);
        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);
        var pad = function(n) { return n < 10 ? '0'+n : n; };
        var dEl = wrap.querySelector('[data-days]');
        var hEl = wrap.querySelector('[data-hours]');
        var mEl = wrap.querySelector('[data-mins]');
        var sEl = wrap.querySelector('[data-secs]');
        if (dEl) dEl.textContent = d;
        if (hEl) hEl.textContent = pad(h);
        if (mEl) mEl.textContent = pad(m);
        if (sEl) sEl.textContent = pad(s);
    }
    tick();
    setInterval(tick, 1000);
});
</script>
@endpush
@endsection
