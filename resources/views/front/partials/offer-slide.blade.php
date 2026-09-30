{{-- One offer shown in full inside the offers slider — expects $offer, $index --}}
@php
    $d          = $offer->display;
    $isActive   = $offer->is_active;
    $isExpired  = $offer->is_expired;
    $isUpcoming = $offer->is_upcoming;
    $price      = $offer->price_info;
    $showUrl    = route('offers.show', $offer);
    $hasCode    = $offer->code && $offer->discount_type !== 'override';
@endphp
<article class="os-slide oc--{{ $d['type'] }} {{ $isExpired ? 'is-expired' : '' }}"
         data-type="{{ $offer->discount_type }}" data-index="{{ $index }}" id="offer-{{ $offer->id }}"
         aria-roledescription="slide" aria-label="{{ $offer->title_ar }}">

    {{-- Media --}}
    <div class="sl-media {{ !$offer->has_video && !$offer->image_url ? 'sl-media--hero' : '' }}">
        @if($offer->has_video && $offer->video_embed_url)
            {{-- src is set when the slide is first shown --}}
            <iframe data-src="{{ $offer->video_embed_url }}" title="{{ $offer->title_ar }}"
                    allow="encrypted-media; picture-in-picture; fullscreen" allowfullscreen></iframe>
        @elseif($offer->has_video)
            <video data-src="{{ $offer->video_file_url }}" controls preload="none" playsinline
                   @if($offer->image_url) poster="{{ $offer->image_url }}" @endif></video>
        @elseif($offer->image_url)
            {{-- whole image, never cropped; a blurred copy fills the empty space --}}
            <span class="img-blur-bg" style="background-image:url('{{ $offer->image_url }}')"></span>
            <a href="{{ $offer->image_url }}" target="_blank" rel="noopener" class="img-full" title="عرض الصورة بالحجم الكامل">
                <img src="{{ $offer->image_url }}" alt="{{ $offer->title_ar }}" {{ $index > 0 ? 'loading=lazy' : '' }}>
                <span class="img-zoom"><i class="bi bi-arrows-fullscreen"></i></span>
            </a>
        @else
            <span class="sl-hero-num">{{ $d['num'] }}<small>@if($d['money'])<x-riyal />@else%@endif</small></span>
            <span class="sl-hero-lbl">{{ $d['label'] }}</span>
        @endif
    </div>

    {{-- Info --}}
    <div class="sl-info">
        <div class="sl-top">
            <span class="sl-type">{{ $d['label'] }}: <b>{{ $d['num'] }} @if($d['money'])<x-riyal />@else%@endif</b></span>
            @if($isExpired)
                <span class="oc-status st-expired">منتهي</span>
            @elseif($isUpcoming)
                <span class="oc-status st-upcoming">يبدأ {{ $offer->start_date->format('d/m/Y') }}</span>
            @else
                <span class="oc-status st-active"><i class="oc-dot"></i> نشط الآن</span>
            @endif
        </div>

        <h3 class="sl-title"><a href="{{ $showUrl }}">{{ $offer->title_ar }}</a></h3>

        @if($offer->description_ar)
            <p class="sl-desc">{{ Str::limit(strip_tags($offer->description_ar), 220) }}</p>
        @endif

        <div class="oc-programs">
            @forelse($offer->programs->take(4) as $prog)
                <a href="{{ $prog->public_url }}" class="oc-prog" title="{{ $prog->name_ar }}">
                    <i class="bi bi-mortarboard-fill"></i> {{ Str::limit($prog->name_ar, 40) }}
                </a>
            @empty
                <a href="{{ route('training-paths') }}" class="oc-prog oc-prog--all">
                    <i class="bi bi-globe2"></i> جميع البرامج والدورات
                </a>
            @endforelse
            @if($offer->programs->count() > 4)
                <a href="{{ $showUrl }}#programs" class="oc-prog oc-prog--more">+{{ $offer->programs->count() - 4 }} أخرى</a>
            @endif
        </div>

        @if($price)
        <div class="sl-price">
            <span class="sl-price-lbl">{{ $price['from'] ? 'السعر بعد العرض يبدأ من' : 'السعر بعد العرض' }}</span>
            <div class="sl-price-row">
                <span class="sl-price-new">{{ number_format($price['new'], 0) }} <x-riyal /></span>
                @if($price['new'] < $price['orig'])
                    <span class="oc-price-old">{{ number_format($price['orig'], 0) }} <x-riyal /></span>
                    <span class="oc-price-save">وفّر {{ number_format($price['orig'] - $price['new'], 0) }} <x-riyal /></span>
                @endif
            </div>
        </div>
        @endif

        @if($isActive && $offer->is_open_ended)
            <div class="oc-countdown oc-countdown--open"><i class="bi bi-infinity"></i> <span>عرض مستمر — لفترة غير محددة</span></div>
        @elseif($isActive)
            <div class="sl-cd" data-end="{{ $offer->end_date->copy()->endOfDay()->toISOString() }}">
                <div><b class="cd-days">--</b><span>يوم</span></div>
                <div><b class="cd-hours">--</b><span>ساعة</span></div>
                <div><b class="cd-mins">--</b><span>دقيقة</span></div>
                <div><b class="cd-secs">--</b><span>ثانية</span></div>
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

        <div class="sl-actions">
            @if($isExpired)
                <span class="oc-btn oc-btn--disabled">انتهى العرض</span>
            @else
                <a href="{{ route('register') }}" class="oc-btn oc-btn--primary">
                    <i class="bi bi-mortarboard-fill"></i> {{ $isUpcoming ? 'سجّل اهتمامك' : 'سجّل الآن واستفد' }}
                </a>
            @endif
            <a href="{{ $showUrl }}" class="oc-btn oc-btn--ghost">تفاصيل العرض <i class="bi bi-arrow-left"></i></a>
        </div>
    </div>
</article>
