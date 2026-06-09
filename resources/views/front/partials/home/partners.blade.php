@php $partners = \App\Models\Partner::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get(); @endphp
@if($partners->isNotEmpty())
<section class="partners-section">
    <div class="partners-head">
        <h2>{{ app()->getLocale() === 'ar' ? 'شركائنا' : 'Partners & Affiliates' }}</h2>
        <p>{{ app()->getLocale() === 'ar' ? 'نفخر بشراكتنا مع عدد من الجهات والمنظمات الرائدة' : 'Proud to work alongside leading organizations and institutions' }}</p>
        <div class="partners-count-row">
            <div class="p-count-chip">
                <i class="bi bi-buildings"></i>
                <strong>{{ $partners->count() }}+</strong>
                <span>{{ app()->getLocale() === 'ar' ? 'جهة شريكة' : 'Partner Organizations' }}</span>
            </div>
            <div class="p-count-chip">
                <i class="bi bi-patch-check-fill" style="color:#60a5fa"></i>
                <span>{{ app()->getLocale() === 'ar' ? 'شراكات موثوقة ومعتمدة' : 'Verified & Accredited' }}</span>
            </div>
        </div>
    </div>
    <div class="partners-grid-wrap">
        <div class="partners-track">
            @foreach($partners as $p)
            <div class="p-logo-card">
                @if($p->url)
                    <a href="{{ $p->url }}" target="_blank" rel="noopener" style="display:contents">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($p->logo) }}" alt="{{ $p->name }}">
                    </a>
                @else
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($p->logo) }}" alt="{{ $p->name }}">
                @endif
                <span class="p-name">{{ $p->name }}</span>
            </div>
            @endforeach
            {{-- duplicate for seamless loop --}}
            @foreach($partners as $p)
            <div class="p-logo-card" aria-hidden="true">
                @if($p->url)
                    <a href="{{ $p->url }}" target="_blank" rel="noopener" style="display:contents">
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($p->logo) }}" alt="{{ $p->name }}">
                    </a>
                @else
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($p->logo) }}" alt="{{ $p->name }}">
                @endif
                <span class="p-name">{{ $p->name }}</span>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
