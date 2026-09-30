{{-- Offer card — expects $offer --}}
@php
    $d          = $offer->display;
    $isActive   = $offer->is_active;
    $isExpired  = $offer->is_expired;
    $isUpcoming = $offer->is_upcoming;
    $price      = $offer->price_info;
    $origPrice  = $price['orig'] ?? null;
    $newPrice   = $price['new'] ?? null;
    $showUrl    = route('offers.show', $offer);
@endphp
<article class="oc oc--{{ $d['type'] }} {{ $isExpired ? 'is-expired' : '' }}" data-type="{{ $offer->discount_type }}">

    {{-- Media --}}
    {{-- Cards show the image; the video plays on the offer's page --}}
    @if($offer->image_url)
        <a href="{{ $showUrl }}" class="oc-media">
            <img src="{{ $offer->image_url }}" alt="{{ $offer->title_ar }}" loading="lazy">
            @if($offer->has_video)<span class="oc-play" title="يحتوي على فيديو"><i class="bi bi-play-fill"></i></span>@endif
        </a>
    @else
        <a href="{{ $showUrl }}" class="oc-media oc-media--hero">
            <span class="oc-hero-num">{{ $d['num'] }}<small>@if($d['money'])<x-riyal />@else%@endif</small></span>
            <span class="oc-hero-lbl">{{ $d['label'] }}</span>
            @if($offer->has_video)<span class="oc-play" title="يحتوي على فيديو"><i class="bi bi-play-fill"></i></span>@endif
        </a>
    @endif

    {{-- Discount + status strip --}}
    <div class="oc-strip">
        <span class="oc-chip">
            <i class="bi bi-lightning-charge-fill"></i>
            {{ $d['label'] }}: <strong>{{ $d['num'] }} @if($d['money'])<x-riyal />@else%@endif</strong>
        </span>
        @if($isExpired)
            <span class="oc-status st-expired">منتهي</span>
        @elseif($isUpcoming)
            <span class="oc-status st-upcoming">قريباً</span>
        @else
            <span class="oc-status st-active"><i class="oc-dot"></i> نشط الآن</span>
        @endif
    </div>

    {{-- Body --}}
    <div class="oc-body">
        <h3 class="oc-title"><a href="{{ $showUrl }}">{{ $offer->title_ar }}</a></h3>

        <div class="oc-programs">
            @forelse($offer->programs->take(3) as $prog)
                <a href="{{ $prog->public_url }}" class="oc-prog" title="{{ $prog->name_ar }}">
                    <i class="bi bi-mortarboard-fill"></i> {{ Str::limit($prog->name_ar, 38) }}
                </a>
            @empty
                <a href="{{ route('training-paths') }}" class="oc-prog oc-prog--all">
                    <i class="bi bi-globe2"></i> جميع البرامج والدورات
                </a>
            @endforelse
            @if($offer->programs->count() > 3)
                <a href="{{ $showUrl }}#programs" class="oc-prog oc-prog--more">+{{ $offer->programs->count() - 3 }} أخرى</a>
            @endif
        </div>

        @if($offer->description_ar)
            <p class="oc-desc">{{ Str::limit(strip_tags($offer->description_ar), 140) }}</p>
        @endif

        @if($origPrice !== null)
        <div class="oc-price">
            @if($price['from'])<span class="oc-price-from">يبدأ من</span>@endif
            <span class="oc-price-new">{{ number_format($newPrice, 0) }} <x-riyal /></span>
            @if($newPrice < $origPrice)
            <span class="oc-price-old">{{ number_format($origPrice, 0) }} <x-riyal /></span>
            <span class="oc-price-save">وفّر {{ number_format($origPrice - $newPrice, 0) }} <x-riyal /></span>
            @endif
        </div>
        @endif

        @if($isActive && $offer->is_open_ended)
        <div class="oc-countdown oc-countdown--open">
            <i class="bi bi-infinity"></i>
            <span>عرض مستمر — لفترة غير محددة</span>
        </div>
        @elseif($isActive)
        <div class="oc-countdown" data-end="{{ $offer->end_date->copy()->endOfDay()->toISOString() }}">
            <i class="bi bi-hourglass-split"></i>
            <span>ينتهي خلال</span>
            <b class="cd-days">--</b><small>يوم</small>
            <b class="cd-hours">--</b><small>:</small>
            <b class="cd-mins">--</b><small>:</small>
            <b class="cd-secs">--</b>
        </div>
        @elseif($isUpcoming)
        <div class="oc-countdown oc-countdown--soon">
            <i class="bi bi-calendar-event"></i>
            <span>يبدأ {{ $offer->start_date->format('d/m/Y') }}</span>
        </div>
        @endif

        @if($offer->code && $offer->discount_type !== 'override' && !$isExpired)
        <div class="oc-code">
            <span class="oc-code-lbl">كود الخصم</span>
            <code>{{ $offer->code }}</code>
            <button type="button" class="oc-copy" onclick="copyOfferCode(this, @js($offer->code))" title="نسخ">
                <i class="bi bi-clipboard"></i>
            </button>
        </div>
        @endif

        <div class="oc-meta">
            @if($offer->end_date)
            <span><i class="bi bi-calendar3"></i> حتى {{ $offer->end_date->format('d/m/Y') }}</span>
            @else
            <span><i class="bi bi-infinity"></i> بدون تاريخ انتهاء</span>
            @endif
            @if($offer->max_uses && $offer->uses_left !== null)
            <span><i class="bi bi-people-fill"></i> {{ $offer->uses_left }} مقعد متبقٍ</span>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div class="oc-actions">
        <a href="{{ $showUrl }}" class="oc-btn oc-btn--ghost">التفاصيل</a>
        @if($isExpired)
            <span class="oc-btn oc-btn--disabled">انتهى العرض</span>
        @else
            <a href="{{ route('register') }}" class="oc-btn oc-btn--primary">
                {{ $isUpcoming ? 'سجّل اهتمامك' : 'سجّل الآن' }}
            </a>
        @endif
    </div>
</article>
