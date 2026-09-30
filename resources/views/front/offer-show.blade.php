@extends('layouts.front')

@php
    $d          = $offer->display;
    $isActive   = $offer->is_active;
    $isExpired  = $offer->is_expired;
    $isUpcoming = $offer->is_upcoming;
    $price      = $offer->price_info;
    $origPrice  = $price['orig'] ?? null;
    $newPrice   = $price['new'] ?? null;
    $progCount  = $offer->programs->count();
    $progText   = match (true) {
        $progCount === 0 => 'جميع البرامج والدورات',
        $progCount === 1 => $offer->programs->first()->name_ar,
        default          => $progCount . ' برامج ودورات',
    };
    $pageUrl    = route('offers.show', $offer);
    $hasCode    = $offer->code && $offer->discount_type !== 'override';
@endphp

@section('title', $offer->title_ar . ' — العروض والخصومات')

@section('styles')
    .os-wrap { padding: 2rem clamp(1rem, 3vw, 3rem) 3rem; background: #f8fafc; }
    .os-grid { display: grid; grid-template-columns: minmax(0, 1.65fr) minmax(0, 1fr); gap: 1.75rem; align-items: start; max-width: 1280px; margin: 0 auto; }
    @media (max-width: 992px) { .os-grid { grid-template-columns: 1fr; } }

    .os-card { background: #fff; border: 1px solid #eef2f7; border-radius: 20px; box-shadow: 0 4px 18px rgba(15,23,42,.05); overflow: hidden; }
    .os-card + .os-card { margin-top: 1.5rem; }
    .os-card-body { padding: 1.5rem 1.75rem; }
    .os-h { display: flex; align-items: center; gap: .55rem; font-size: 1.1rem; font-weight: 800; color: #0f172a; margin: 0 0 1rem; }
    .os-h i { color: var(--oc-c1); }

    /* Media */
    .os-media { position: relative; aspect-ratio: 16 / 9; background: #0f172a; }
    .os-media iframe, .os-media video, .os-media img { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; }
    .os-media--img { aspect-ratio: auto; overflow: hidden; display: flex; justify-content: center; }
    .os-media--img .img-full { position: relative; z-index: 1; display: block; cursor: zoom-in; }
    .os-media--img img { position: static; width: auto; height: auto; max-width: 100%; max-height: 75vh; display: block; }
    .os-media video { object-fit: contain; background: #000; }
    .os-media--hero {
        background: linear-gradient(135deg, var(--oc-c1), var(--oc-c2));
        display: flex; flex-direction: column; align-items: center; justify-content: center; color: #fff; overflow: hidden;
    }
    .os-media--hero::before, .os-media--hero::after { content: ''; position: absolute; border-radius: 50%; background: rgba(255,255,255,.08); }
    .os-media--hero::before { width: 340px; height: 340px; top: -120px; right: -80px; }
    .os-media--hero::after  { width: 220px; height: 220px; bottom: -90px; left: -40px; }
    .os-hero-num { font-size: clamp(4rem, 12vw, 7rem); font-weight: 900; line-height: 1; text-shadow: 0 6px 24px rgba(0,0,0,.18); position: relative; z-index: 1; }
    .os-hero-num small { font-size: .35em; margin-inline-start: .3rem; }
    .os-hero-lbl { font-size: 1.05rem; font-weight: 700; opacity: .9; margin-top: .5rem; position: relative; z-index: 1; }

    .os-desc { font-size: .98rem; line-height: 2; color: #334155; margin: 0; }
    .os-desc-empty { color: #94a3b8; }

    /* Included programs */
    .os-progs { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: .75rem; }
    .os-prog {
        display: flex; align-items: center; gap: .8rem; padding: .7rem; border: 1.5px solid #eef2f7; border-radius: 14px;
        text-decoration: none; color: inherit; transition: all .18s; background: #fff;
    }
    .os-prog:hover { border-color: var(--oc-c1); background: var(--oc-soft); transform: translateY(-2px); }
    .os-prog img, .os-prog-ico { width: 54px; height: 54px; border-radius: 12px; object-fit: cover; flex-shrink: 0; }
    .os-prog-ico { background: var(--oc-soft); color: var(--oc-c1); display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    .os-prog-info { flex: 1; min-width: 0; }
    .os-prog-info b { display: block; font-size: .9rem; font-weight: 800; color: #0f172a; line-height: 1.5; }
    .os-prog-price { display: flex; gap: .5rem; align-items: baseline; font-size: .8rem; margin-top: .15rem; }
    .os-prog-price strong { color: var(--oc-c1); font-weight: 900; }
    .os-prog-price del { color: #94a3b8; }
    .os-prog-go { color: #cbd5e1; transition: color .18s; }
    [dir="ltr"] .os-prog-go { transform: scaleX(-1); }
    .os-prog:hover .os-prog-go { color: var(--oc-c1); }
    .os-cats { display: flex; flex-wrap: wrap; gap: .6rem; }
    .os-cats a {
        display: inline-flex; align-items: center; gap: .45rem; padding: .6rem 1rem; border-radius: 12px;
        background: var(--oc-soft); color: var(--oc-c1); font-weight: 800; font-size: .88rem; text-decoration: none; border: 1.5px solid transparent; transition: all .18s;
    }
    .os-cats a:hover { border-color: var(--oc-c1); }

    /* Steps */
    .os-steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
    @media (max-width: 640px) { .os-steps { grid-template-columns: 1fr; } }
    .os-step { background: var(--oc-soft); border-radius: 14px; padding: 1rem; }
    .os-step-n {
        width: 30px; height: 30px; border-radius: 50%; background: var(--oc-c1); color: #fff;
        display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: .85rem; margin-bottom: .6rem;
    }
    .os-step b { display: block; font-size: .92rem; color: #0f172a; margin-bottom: .2rem; }
    .os-step p { font-size: .8rem; color: #64748b; margin: 0; line-height: 1.7; }

    /* Side panel */
    .os-panel { position: sticky; top: 90px; }
    .os-panel .os-card-body { padding: 1.5rem; }
    .os-status-row { display: flex; align-items: center; justify-content: space-between; gap: .5rem; margin-bottom: .9rem; }
    .os-type { font-size: .78rem; font-weight: 800; color: var(--oc-c1); background: var(--oc-soft); padding: .25rem .7rem; border-radius: 999px; }
    .os-title { font-size: 1.45rem; font-weight: 900; color: #0f172a; line-height: 1.5; margin: 0 0 .4rem; }
    .os-program { display: flex; align-items: center; gap: .4rem; font-size: .9rem; color: #64748b; font-weight: 600; margin: 0 0 1.1rem; }
    .os-program i { color: var(--oc-c1); }

    .os-price-box { background: var(--oc-soft); border-radius: 16px; padding: 1rem 1.1rem; margin-bottom: 1rem; }
    .os-price-lbl { font-size: .75rem; font-weight: 700; color: #64748b; }
    .os-price-row { display: flex; align-items: baseline; flex-wrap: wrap; gap: .6rem; margin-top: .15rem; }
    .os-price-new { font-size: 2rem; font-weight: 900; color: var(--oc-c1); line-height: 1.2; }
    .os-price-old { font-size: 1rem; color: #94a3b8; text-decoration: line-through; }
    .os-price-save { font-size: .75rem; font-weight: 800; color: #b45309; background: #fef3c7; padding: .2rem .6rem; border-radius: 6px; }

    .os-cd { display: grid; grid-template-columns: repeat(4, 1fr); gap: .5rem; margin-bottom: 1rem; }
    .os-cd-box { background: #0f172a; border-radius: 12px; padding: .6rem .25rem; text-align: center; color: #fff; }
    .os-cd-box b { display: block; font-size: 1.4rem; font-weight: 900; line-height: 1.1; font-variant-numeric: tabular-nums; }
    .os-cd-box span { font-size: .68rem; opacity: .6; font-weight: 700; }
    .os-cd-title { font-size: .78rem; font-weight: 800; color: #9a3412; margin-bottom: .45rem; display: flex; align-items: center; gap: .35rem; }

    .os-panel .oc-code { margin-bottom: 1rem; padding-top: .5rem; padding-bottom: .5rem; }
    .os-panel .oc-code code { font-size: 1.05rem; }

    .os-info { list-style: none; padding: 0; margin: 0 0 1.1rem; border-top: 1px solid #f1f5f9; }
    .os-info li { display: flex; justify-content: space-between; gap: 1rem; padding: .6rem 0; border-bottom: 1px solid #f1f5f9; font-size: .86rem; }
    .os-info li span { color: #64748b; display: flex; align-items: center; gap: .4rem; }
    .os-info li b { color: #0f172a; font-weight: 800; text-align: end; }
    .os-uses-bar { height: 6px; background: #f1f5f9; border-radius: 99px; overflow: hidden; margin-top: .4rem; }
    .os-uses-bar i { display: block; height: 100%; background: linear-gradient(90deg, #f59e0b, #ef4444); border-radius: 99px; }

    .os-cta { display: flex; width: 100%; padding: .85rem 1rem; font-size: 1rem; border-radius: 13px; }
    .os-share { display: flex; align-items: center; justify-content: center; gap: .5rem; margin-top: 1rem; font-size: .8rem; color: #64748b; font-weight: 700; }
    .os-share a, .os-share button {
        width: 36px; height: 36px; border-radius: 10px; border: 1px solid #e2e8f0; background: #fff; color: #475569;
        display: inline-flex; align-items: center; justify-content: center; text-decoration: none; transition: all .18s; cursor: pointer;
    }
    .os-share a:hover, .os-share button:hover { background: var(--oc-c1); border-color: var(--oc-c1); color: #fff; }
    .os-share .copied { background: #10b981 !important; border-color: #10b981 !important; color: #fff !important; }

    .os-more { max-width: 1280px; margin: 3rem auto 0; }
    .os-more-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; }
    .os-more-head h2 { font-size: 1.35rem; font-weight: 800; margin: 0; }
    .os-more-head a { font-size: .88rem; font-weight: 700; color: #0071AA; text-decoration: none; }
@endsection

@section('content')
@include('front.partials.offer-card-assets')

{{-- Hero --}}
<section class="hero-section">
    <div class="breadcrumb-nav">
        <a href="{{ route('home') }}">الرئيسية</a>
        <span>></span>
        <a href="{{ route('offers') }}">العروض والخصومات</a>
        <span>></span>
        <span>{{ Str::limit($offer->title_ar, 40) }}</span>
    </div>
    <h2>{{ $offer->title_ar }}</h2>
    @if($offer->title_en)<p dir="ltr">{{ $offer->title_en }}</p>@endif
</section>

<div class="os-wrap">
    <div class="os-grid oc--{{ $d['type'] }}">

        {{-- Main column --}}
        <div>
            <div class="os-card">
                @if($offer->has_video)
                    <div class="os-media">
                        @if($offer->video_embed_url)
                            <iframe src="{{ $offer->video_embed_url }}" title="{{ $offer->title_ar }}"
                                    allow="encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>
                        @else
                            <video src="{{ $offer->video_file_url }}" controls preload="metadata" playsinline></video>
                        @endif
                    </div>
                @elseif($offer->image_url)
                    <div class="os-media os-media--img">
                        <span class="img-blur-bg" style="background-image:url('{{ $offer->image_url }}')"></span>
                        <a href="{{ $offer->image_url }}" target="_blank" rel="noopener" class="img-full" title="عرض الصورة بالحجم الكامل">
                            <img src="{{ $offer->image_url }}" alt="{{ $offer->title_ar }}">
                            <span class="img-zoom"><i class="bi bi-arrows-fullscreen"></i></span>
                        </a>
                    </div>
                @else
                    <div class="os-media os-media--hero">
                        <span class="os-hero-num">{{ $d['num'] }}<small>@if($d['money'])<x-riyal />@else%@endif</small></span>
                        <span class="os-hero-lbl">{{ $d['label'] }}</span>
                    </div>
                @endif
            </div>

            <div class="os-card">
                <div class="os-card-body">
                    <h3 class="os-h"><i class="bi bi-info-circle-fill"></i> تفاصيل العرض</h3>
                    @if($offer->description_ar)
                        <p class="os-desc">{!! nl2br(e($offer->description_ar)) !!}</p>
                    @else
                        <p class="os-desc os-desc-empty">
                            احصل على {{ $d['label'] }} بقيمة {{ $d['num'] }}@if($d['money']) <x-riyal />@else%@endif
                            على {{ $progText }}
                            {{ $offer->end_date ? 'حتى ' . $offer->end_date->format('d/m/Y') : '— عرض مستمر لفترة غير محددة' }}.
                        </p>
                    @endif
                </div>
            </div>

            <div class="os-card" id="programs">
                <div class="os-card-body">
                    <h3 class="os-h"><i class="bi bi-mortarboard-fill"></i> البرامج والدورات المشمولة بالعرض</h3>
                    @if($progCount)
                        <div class="os-progs">
                            @foreach($offer->programs as $prog)
                            @php $pOrig = (float) $prog->price; $pNew = $pOrig > 0 ? $offer->getEffectivePriceForProgram($prog) : null; @endphp
                            <a href="{{ $prog->public_url }}" class="os-prog">
                                @if($prog->image)
                                    <img src="{{ asset('storage/' . $prog->image) }}" alt="" loading="lazy">
                                @else
                                    <span class="os-prog-ico"><i class="bi bi-mortarboard-fill"></i></span>
                                @endif
                                <span class="os-prog-info">
                                    <b>{{ $prog->name_ar }}</b>
                                    @if($pNew !== null)
                                    <span class="os-prog-price">
                                        <strong>{{ number_format($pNew, 0) }} <x-riyal /></strong>
                                        @if($pNew < $pOrig)<del>{{ number_format($pOrig, 0) }} <x-riyal /></del>@endif
                                    </span>
                                    @endif
                                </span>
                                <i class="bi bi-chevron-left os-prog-go"></i>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <p class="os-desc" style="margin-bottom:1rem;">هذا العرض يشمل جميع البرامج والدورات — تصفّح الأقسام واختر ما يناسبك:</p>
                        <div class="os-cats">
                            <a href="{{ route('training-paths') }}"><i class="bi bi-signpost-2-fill"></i> المسارات التدريبية</a>
                            <a href="{{ route('training-programs') }}"><i class="bi bi-tools"></i> البرامج التأهيلية</a>
                            <a href="{{ route('courses.developmental') }}"><i class="bi bi-graph-up-arrow"></i> الدورات التطويرية</a>
                            <a href="{{ route('courses.qualifying') }}"><i class="bi bi-award-fill"></i> الدورات التأهيلية</a>
                            <a href="{{ route('english-courses') }}"><i class="bi bi-translate"></i> اللغة الإنجليزية</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="os-card">
                <div class="os-card-body">
                    <h3 class="os-h"><i class="bi bi-signpost-split-fill"></i> كيف تستفيد من العرض؟</h3>
                    <div class="os-steps">
                        <div class="os-step">
                            <div class="os-step-n">1</div>
                            <b>أنشئ حسابك</b>
                            <p>سجّل في المنصة ببياناتك خلال دقيقة واحدة.</p>
                        </div>
                        <div class="os-step">
                            <div class="os-step-n">2</div>
                            <b>اختر البرنامج</b>
                            <p>{{ $progCount ? 'اختر من البرامج المشمولة بالعرض' : 'اختر أي برنامج أو دورة تناسبك' }}.</p>
                        </div>
                        <div class="os-step">
                            <div class="os-step-n">3</div>
                            <b>{{ $hasCode ? 'أدخل كود الخصم' : 'يُطبَّق تلقائياً' }}</b>
                            <p>{{ $hasCode ? 'استخدم الكود ' . $offer->code . ' عند الدفع.' : 'سعر العرض يظهر لك مباشرة عند الدفع.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Side panel --}}
        <aside class="os-panel">
            <div class="os-card">
                <div class="os-card-body">
                    <div class="os-status-row">
                        <span class="os-type">{{ $d['label'] }}</span>
                        @if($isExpired)
                            <span class="oc-status st-expired">منتهي</span>
                        @elseif($isUpcoming)
                            <span class="oc-status st-upcoming">قريباً</span>
                        @else
                            <span class="oc-status st-active"><i class="oc-dot"></i> نشط الآن</span>
                        @endif
                    </div>

                    <h1 class="os-title">{{ $offer->title_ar }}</h1>
                    <p class="os-program">
                        <i class="bi bi-mortarboard-fill"></i>
                        <a href="#programs" style="color:inherit;text-decoration:none;">{{ $progText }}</a>
                    </p>

                    <div class="os-price-box">
                        @if($origPrice !== null)
                            <div class="os-price-lbl">{{ $price['from'] ? 'السعر بعد العرض يبدأ من' : 'السعر بعد العرض' }}</div>
                            <div class="os-price-row">
                                <span class="os-price-new">{{ number_format($newPrice, 0) }} <x-riyal /></span>
                                @if($newPrice < $origPrice)
                                    <span class="os-price-old">{{ number_format($origPrice, 0) }} <x-riyal /></span>
                                @endif
                            </div>
                            @if($newPrice < $origPrice)
                                <span class="os-price-save">وفّر {{ number_format($origPrice - $newPrice, 0) }} <x-riyal /></span>
                            @endif
                        @else
                            <div class="os-price-lbl">{{ $d['label'] }}</div>
                            <div class="os-price-row">
                                <span class="os-price-new">{{ $d['num'] }} @if($d['money'])<x-riyal />@else%@endif</span>
                            </div>
                        @endif
                    </div>

                    @if($isActive && $offer->is_open_ended)
                        <div class="oc-countdown oc-countdown--open" style="margin-bottom:1rem;">
                            <i class="bi bi-infinity"></i> <span>عرض مستمر — لفترة غير محددة</span>
                        </div>
                    @elseif($isActive)
                        <div class="os-cd-title"><i class="bi bi-hourglass-split"></i> ينتهي العرض خلال</div>
                        <div class="os-cd" data-end="{{ $offer->end_date->copy()->endOfDay()->toISOString() }}">
                            <div class="os-cd-box"><b class="cd-days">--</b><span>يوم</span></div>
                            <div class="os-cd-box"><b class="cd-hours">--</b><span>ساعة</span></div>
                            <div class="os-cd-box"><b class="cd-mins">--</b><span>دقيقة</span></div>
                            <div class="os-cd-box"><b class="cd-secs">--</b><span>ثانية</span></div>
                        </div>
                    @endif

                    @if($hasCode && !$isExpired)
                        <div class="oc-code">
                            <span class="oc-code-lbl">كود الخصم</span>
                            <code>{{ $offer->code }}</code>
                            <button type="button" class="oc-copy" onclick="copyOfferCode(this, @js($offer->code))" title="نسخ">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    @endif

                    <ul class="os-info">
                        <li><span><i class="bi bi-calendar-check"></i> يبدأ</span><b>{{ $offer->start_date->format('d/m/Y') }}</b></li>
                        <li><span><i class="bi bi-calendar-x"></i> ينتهي</span><b>{{ $offer->end_date?->format('d/m/Y') ?? '♾️ غير محدد' }}</b></li>
                        @if($offer->max_uses)
                        <li style="display:block;">
                            <div style="display:flex;justify-content:space-between;">
                                <span><i class="bi bi-people-fill"></i> المقاعد المتبقية</span>
                                <b>{{ $offer->uses_left }} / {{ $offer->max_uses }}</b>
                            </div>
                            <div class="os-uses-bar"><i style="width:{{ min(100, round($offer->uses_count / $offer->max_uses * 100)) }}%"></i></div>
                        </li>
                        @endif
                    </ul>

                    @if($isExpired)
                        <span class="oc-btn oc-btn--disabled os-cta">انتهى هذا العرض</span>
                    @else
                        <a href="{{ route('register') }}" class="oc-btn oc-btn--primary os-cta">
                            <i class="bi bi-mortarboard-fill"></i>
                            {{ $isUpcoming ? 'سجّل اهتمامك الآن' : 'سجّل الآن واستفد من العرض' }}
                        </a>
                    @endif

                    <div class="os-share">
                        <span>شارك العرض:</span>
                        <a href="https://wa.me/?text={{ urlencode($offer->title_ar . ' ' . $pageUrl) }}" target="_blank" rel="noopener" title="واتساب"><i class="bi bi-whatsapp"></i></a>
                        <a href="https://twitter.com/intent/tweet?text={{ urlencode($offer->title_ar) }}&url={{ urlencode($pageUrl) }}" target="_blank" rel="noopener" title="X"><i class="bi bi-twitter-x"></i></a>
                        <button type="button" onclick="copyOfferLink(this)" title="نسخ الرابط"><i class="bi bi-link-45deg"></i></button>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    @if($otherOffers->isNotEmpty())
    <div class="os-more">
        <div class="os-more-head">
            <h2>عروض أخرى قد تهمك</h2>
            <a href="{{ route('offers') }}">كل العروض <i class="bi bi-arrow-left"></i></a>
        </div>
        <div class="oc-grid">
            @foreach($otherOffers as $other)
                @include('front.partials.offer-card', ['offer' => $other])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function copyOfferLink(btn) {
    navigator.clipboard.writeText(@js($pageUrl)).then(() => {
        btn.classList.add('copied');
        setTimeout(() => btn.classList.remove('copied'), 2000);
    });
}
</script>
@endsection
