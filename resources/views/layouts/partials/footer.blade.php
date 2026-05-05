@php
use App\Models\SiteSetting;
use App\Models\FooterLink;
$fs      = SiteSetting::allKeyed();
$locale  = app()->getLocale();
$fDesc   = $locale === 'en' ? ($fs['footer_description_en'] ?? $fs['footer_description_ar'] ?? '') : ($fs['footer_description_ar'] ?? '');
$fAddr   = $locale === 'en' ? ($fs['address_en'] ?? $fs['address_ar'] ?? '') : ($fs['address_ar'] ?? '');
$fHours  = $locale === 'en' ? ($fs['working_hours_en'] ?? $fs['working_hours_ar'] ?? '') : ($fs['working_hours_ar'] ?? '');
$fCopy   = $locale === 'en' ? ($fs['copyright_en'] ?? $fs['copyright_ar'] ?? '') : ($fs['copyright_ar'] ?? '');
$fQuick  = FooterLink::where('section','quick')->where('is_active',true)->orderBy('sort_order')->get();
$fServ   = FooterLink::where('section','services')->where('is_active',true)->orderBy('sort_order')->get();
$socials = [
    'twitter'   => ['icon'=>'bi-twitter-x',  'url' => $fs['social_twitter']   ?? ''],
    'instagram' => ['icon'=>'bi-instagram',   'url' => $fs['social_instagram'] ?? ''],
    'linkedin'  => ['icon'=>'bi-linkedin',    'url' => $fs['social_linkedin']  ?? ''],
    'youtube'   => ['icon'=>'bi-youtube',     'url' => $fs['social_youtube']   ?? ''],
    'facebook'  => ['icon'=>'bi-facebook',    'url' => $fs['social_facebook']  ?? ''],
    'snapchat'  => ['icon'=>'bi-snapchat',    'url' => $fs['social_snapchat']  ?? ''],
    'whatsapp'  => ['icon'=>'bi-whatsapp',    'url' => $fs['social_whatsapp']  ?? ''],
];
@endphp
<footer class="foot">
    <div class="container-fluid">
        <div class="row g-5">
            {{-- Brand col --}}
            <div class="col-lg-4 col-md-12">
                <div class="footer-logo">
                    <img src="{{ asset('images/footlogooo.png') }}" alt="Logo" onerror="this.style.display='none'" />
                </div>
                <p class="footer-desc">{{ $fDesc }}</p>
                <div class="footer-social-wrap">
                    @foreach($socials as $social)
                        @if(!empty($social['url']))
                        <a href="{{ $social['url'] }}" target="_blank" rel="noopener">
                            <i class="bi {{ $social['icon'] }}"></i>
                        </a>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="col-lg-2 col-md-4 col-6">
                <div class="footer-heading">{{ __('Quick Links') }}</div>
                <div class="footer-links">
                    @foreach($fQuick as $link)
                    <a href="{{ $link->url }}">{{ $locale === 'en' ? ($link->label_en ?: $link->label_ar) : $link->label_ar }}</a>
                    @endforeach
                </div>
            </div>

            {{-- Services --}}
            <div class="col-lg-2 col-md-4 col-6">
                <div class="footer-heading">{{ __('Services') }}</div>
                <div class="footer-links">
                    @foreach($fServ as $link)
                    <a href="{{ $link->url }}">{{ $locale === 'en' ? ($link->label_en ?: $link->label_ar) : $link->label_ar }}</a>
                    @endforeach
                </div>
            </div>

            {{-- Contact --}}
            <div class="col-lg-4 col-md-4 col-12">
                <div class="footer-heading">{{ __('Contact Information') }}</div>
                @if(!empty($fs['phone']))
                <div class="footer-contact-item">
                    <i class="bi bi-telephone-fill"></i>
                    <span>{{ $fs['phone'] }}</span>
                </div>
                @endif
                @if(!empty($fs['email']))
                <div class="footer-contact-item">
                    <i class="bi bi-envelope-fill"></i>
                    <span>{{ $fs['email'] }}</span>
                </div>
                @endif
                @if(!empty($fAddr))
                <div class="footer-contact-item">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>{{ $fAddr }}</span>
                </div>
                @endif
                @if(!empty($fHours))
                <div class="footer-contact-item">
                    <i class="bi bi-clock-fill"></i>
                    <span>{{ $fHours }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="footer-bottom">
            <p>© {{ date('Y') }} {{ $fCopy }}</p>
        </div>
    </div>
</footer>
