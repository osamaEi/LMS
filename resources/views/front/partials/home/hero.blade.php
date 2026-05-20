<section class="hero-section">
    <div class="hero-slides">
        <div class="hero-slide active" style="background-image:url('{{ $lms3s('1.png') }}')"></div>
    </div>
    <div class="hero-vline"></div>
    <div class="hero-content">
        <div class="hero-tag">
            <span class="tag-dot"></span>
            {{ app()->getLocale()=='ar' ? 'معهد الارتقاء العالي للتدريب — معتمد من المؤسسة العامةً  للتدريب التقني والمهني' : 'Al-Ertiqaa Institute — Officially Accredited' }}
        </div>
        <div class="hero-accent-line"></div>
        <h1>
            {{ __('Distinguished training opens doors to') }}
            <span>{{ __('tomorrow') }}</span>
        </h1>
        <p>{{ __('With over 10 years of experience, we make a real difference in the lives of individuals and organizations. We guide you with the training compass towards your specialization and profession with confidence, to be your first gateway to a future that keeps pace with Vision 2030 targets.') }}</p>
        <div class="hero-btns">
            <a href="{{ route('login') }}" class="hero-full-btn">{{ __('Start Your Trial Journey') }}</a>
            <a href="{{ route('training-paths') }}" class="hero-notfull-btn">{{ __('Explore Our Programs') }}</a>
        </div>
    </div>

    <div class="hero-dots" id="heroDots">
        <button class="hero-dot active" onclick="goToSlide(0)"></button>
        <button class="hero-dot"        onclick="goToSlide(1)"></button>
        <button class="hero-dot"        onclick="goToSlide(2)"></button>
    </div>
    <div class="hero-scroll-hint">
        <span>{{ app()->getLocale()=='ar' ? 'اكتشف' : 'Scroll' }}</span>
        <i class="bi bi-chevron-double-down"></i>
    </div>
</section>
