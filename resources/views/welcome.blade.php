@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.front')

@section('title', __('Al-Ertiqaa High Institute for Training'))

@section('styles')
/* ── Hero Slider ── */
.hero-section { position: relative; height: 65vh; min-height: 480px; overflow: hidden; }
.hero-slides { position: absolute; inset: 0; }
.hero-slide {
    position: absolute; inset: 0;
    background-size: cover; background-position: center 20%;
    opacity: 0;
    transition: opacity 1.3s ease-in-out, transform 8s ease;
    transform: scale(1.02);
}
.hero-slide.active { opacity: 1; transform: scale(1); }
.hero-slide::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(90deg, rgba(0,0,0,.38) 0%, rgba(0,0,0,.15) 40%, transparent 70%);
}
[dir="rtl"] .hero-slide::after {
    background: linear-gradient(270deg, rgba(0,0,0,.38) 0%, rgba(0,0,0,.15) 40%, transparent 70%);
}
.hero-vline {
    position: absolute; top: 15%; bottom: 15%;
    right: 42%; width: 1px;
    background: linear-gradient(to bottom, transparent, rgba(255,255,255,.12) 30%, rgba(255,255,255,.12) 70%, transparent);
    z-index: 1;
}
[dir="rtl"] .hero-vline { right: auto; left: 42%; }
@media(max-width:991px){ .hero-vline{display:none;} }

.hero-content {
    position: relative; z-index: 2; height: 100%;
    display: flex; flex-direction: column; justify-content: center;
    padding: 0 clamp(1.5rem, 6vw, 8rem);
    max-width: 760px;
    animation: heroFadeUp 1s ease .15s both;
}
@keyframes heroFadeUp {
    from { opacity:0; transform:translateY(35px); }
    to   { opacity:1; transform:translateY(0); }
}
.hero-tag {
    display: inline-flex; align-items: center; gap: .5rem;
    background: rgba(255,255,255,.13); backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.22); border-radius: 50px;
    padding: .38rem 1.1rem; color: rgba(255,255,255,.95); font-size: .8rem;
    margin-bottom: 1rem; width: fit-content;
}
.hero-tag .tag-dot { width:7px; height:7px; border-radius:50%; background:#4ade80; flex-shrink:0; animation:pulse-green 2s infinite; }
@keyframes pulse-green { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.45;transform:scale(.8)} }
.hero-accent-line {
    width: 52px; height: 3px;
    background: linear-gradient(to right, #7dd3fc, rgba(125,211,252,.25));
    border-radius: 3px; margin-bottom: 1.2rem;
}
[dir="rtl"] .hero-accent-line { background: linear-gradient(to left, #7dd3fc, rgba(125,211,252,.25)); }
.hero-content h1 { color: #fff; font-size: clamp(2rem, 4.5vw, 3.6rem); font-weight: 800; line-height: 1.3; margin-bottom: 1.2rem; }
.hero-content h1 span { color: #7dd3fc; }
.hero-content p { color: rgba(255,255,255,.86); font-size: clamp(.9rem, 1.7vw, 1.1rem); margin-bottom: 2rem; line-height: 1.9; max-width: 580px; }
.hero-btns { display: flex; gap: 1rem; flex-wrap: wrap; }
.hero-full-btn {
    background: var(--main-color); color: #fff; padding: .65rem 1.75rem;
    border-radius: 8px; border: none; font-size: 1rem; transition: all .3s; cursor: pointer;
}
.hero-full-btn:hover { background: #005a8a; transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,113,170,.4); color: #fff; }
.hero-notfull-btn {
    background: transparent; color: #fff; padding: .65rem 1.75rem;
    border-radius: 8px; border: 2px solid rgba(255,255,255,.65);
    font-size: 1rem; transition: all .3s; cursor: pointer;
}
.hero-notfull-btn:hover { background: rgba(255,255,255,.13); border-color: #fff; color:#fff; }

.hero-float-stats {
    position: absolute; bottom: 4.5rem;
    left: clamp(1.5rem, 6vw, 8rem); z-index: 3;
    display: flex; gap: .65rem;
    animation: heroFadeUp 1s ease .5s both;
}
[dir="rtl"] .hero-float-stats { left: auto; right: clamp(1.5rem, 6vw, 8rem); }
.hfs-pill {
    background: rgba(255,255,255,.11); backdrop-filter: blur(14px);
    border: 1px solid rgba(255,255,255,.18); border-radius: 12px;
    padding: .7rem 1.1rem; color: #fff; text-align: center; min-width: 74px;
}
.hfs-pill .hfs-n { font-size: 1.45rem; font-weight: 800; display:block; line-height:1; }
.hfs-pill .hfs-l { font-size: .67rem; opacity: .82; margin-top: .2rem; display:block; }
@media(max-width:991px){ .hero-float-stats{display:none;} }

.hero-dots {
    position: absolute; bottom: 2rem; z-index: 3;
    left: 50%; transform: translateX(-50%);
    display: flex; gap: .5rem;
}
[dir="rtl"] .hero-dots { left: auto; right: 50%; transform: translateX(50%); }
.hero-dot { width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,.4); border: none; cursor: pointer; transition: all .3s; }
.hero-dot.active { background: #fff; width: 32px; border-radius: 5px; }
.hero-scroll-hint {
    position: absolute; bottom: 2rem; right: 2.5rem; z-index: 3;
    display: flex; flex-direction: column; align-items: center; gap: .4rem;
    color: rgba(255,255,255,.5); font-size: .72rem;
    animation: bounce 2s infinite;
}
[dir="rtl"] .hero-scroll-hint { right: auto; left: 2.5rem; }
@keyframes bounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(7px); } }

/* ── Stats Bar ── */
.stats-bar { background: var(--main-color); padding: 2.5rem clamp(1rem,4vw,4rem); }
.stats-bar .stats-inner { display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap; gap: 1.5rem 0; }
.stats-bar .stat-item { text-align: center; color: #fff; flex: 1 1 0; min-width: 140px; position: relative; }
.stats-bar .stat-item:not(:last-child)::after { content: ''; position: absolute; top: 50%; right: 0; transform: translateY(-50%); width: 1px; height: 50px; background: rgba(255,255,255,.3); }
[dir="rtl"] .stats-bar .stat-item:not(:last-child)::after { right: auto; left: 0; }
.stats-bar .stat-number { font-size: clamp(2rem,3.5vw,3rem); font-weight: 800; display: block; line-height: 1.1; }
.stats-bar .stat-label { font-size: .9rem; opacity: .85; margin-top: .3rem; display: block; }
@media (max-width: 576px) { .stats-bar .stat-item { min-width: 45%; } .stats-bar .stat-item:not(:last-child)::after { display: none; } }

/* ── Section Heads ── */
.section-head { text-align: center; margin-bottom: 3rem; }
.section-head .badge-tag { display: inline-block; background: #eaf5fb; color: var(--main-color); padding: .3rem 1rem; border-radius: 20px; font-size: .85rem; margin-bottom: .75rem; }
.section-head h2 { font-size: clamp(1.6rem,3vw,2.4rem); font-weight: 800; margin-bottom: .75rem; }
.section-head p { color: #555; max-width: 650px; margin: 0 auto; line-height: 1.8; }

/* ── Why Cards ── */
.why-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); }
.why-card { border: none; border-radius: 16px; background: linear-gradient(160deg, #fff 0%, rgba(82,154,255,.12) 100%); padding: 1.75rem 1.25rem; height: 100%; transition: transform .3s, box-shadow .3s; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.why-card:hover { transform: translateY(-6px); box-shadow: 0 12px 28px rgba(0,113,170,.15); }
.why-icon { width: 64px; height: 64px; border-radius: 14px; background: linear-gradient(135deg, var(--main-color), #0099d6); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
.why-icon i { color: #fff; font-size: 1.6rem; }
.why-card h5 { font-weight: 700; margin-bottom: .5rem; }
.why-card p { color: #666; font-size: .9rem; margin: 0; }

/* ── Programs ── */
.programs-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); background: var(--second-color); }
.courses-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap: 1.5rem; max-width: 1300px; margin: 0 auto; }
.course-card { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.07); transition: all .3s; }
.course-card:hover { transform: translateY(-5px); box-shadow: 0 12px 28px rgba(0,0,0,.12); }
.course-card img { width: 100%; height: 200px; object-fit: cover; object-position: center 20%; }
.course-card-body { padding: 1.25rem; }
.course-card h4 { font-weight: 700; margin-bottom: .5rem; font-size: 1.1rem; }
.course-card p { color: #666; font-size: .9rem; flex-grow: 1; }
.marks { display: flex; gap: .5rem; flex-wrap: wrap; margin: .75rem 0; }
.marks span { padding: .2rem .6rem; border-radius: 6px; font-size: .78rem; font-weight: 600; white-space: nowrap; }
.marks .st { background: #f3f4f6; border: 1px solid #d1d5db; color: #374151; }
.marks .nd { background: #eff8ff; border: 1px solid #b2ddff; color: var(--main-color); }
.marks .th { background: #ecfdf3; border: 1px solid #abefb6; color: #085d3a; }
.course-btns { display: flex; gap: .75rem; margin-top: 1rem; }
.course-btns a { flex: 1; text-align: center; padding: .5rem; border-radius: 8px; font-size: .9rem; font-weight: 600; transition: all .3s; }
.course-btns .btn-primary-course { background: var(--main-color); color: #fff; }
.course-btns .btn-primary-course:hover { background: #005a8a; }
.course-btns .btn-outline-course { border: 1.5px solid var(--main-color); color: var(--main-color); }
.course-btns .btn-outline-course:hover { background: #eaf5fb; }

/* ── Gallery ── */
.gallery-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); }
.gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; max-width: 1300px; margin: 0 auto; }
.gallery-item { border-radius: 12px; overflow: hidden; position: relative; cursor: pointer; }
.gallery-item img { width: 100%; height: 100%; object-fit: cover; object-position: center 20%; transition: transform .4s ease; display: block; }
.gallery-item:hover img { transform: scale(1.05); }
.gallery-item .overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,60,100,.6) 0%, transparent 60%); opacity: 0; transition: .3s; }
.gallery-item:hover .overlay { opacity: 1; }
.gallery-item-1 { grid-column: span 2; grid-row: span 2; height: 420px; }
.gallery-item-2, .gallery-item-3, .gallery-item-4, .gallery-item-5 { height: 204px; }

/* ── How It Works ── */
.how-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); background: var(--second-color); }
.how-image { border-radius: 20px; overflow: hidden; box-shadow: 0 12px 40px rgba(0,0,0,.12); }
.how-image img { width: 100%; height: 100%; object-fit: cover; object-position: center 20%; }
.how-steps { display: flex; flex-direction: column; gap: 1.75rem; }
.how-step { display: flex; gap: 1.25rem; align-items: flex-start; }
.how-step-number { flex-shrink: 0; width: 48px; height: 48px; border-radius: 12px; background: var(--main-color); color: #fff; font-size: 1.2rem; font-weight: 800; display: flex; align-items: center; justify-content: center; }
.how-step-text h5 { font-weight: 700; margin-bottom: .3rem; }
.how-step-text p { color: #666; font-size: .9rem; margin: 0; line-height: 1.7; }

/* ── Testimonials ── */
.testimonials-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); }
.testimonial-card { background: #fff; border-radius: 16px; padding: 1.75rem; box-shadow: 0 4px 20px rgba(0,0,0,.08); height: 100%; border-top: 4px solid var(--main-color); display: flex; flex-direction: column; gap: 1rem; }
.testimonial-stars { color: #f59e0b; }
.testimonial-text { color: #444; line-height: 1.8; font-size: .95rem; flex: 1; }
.testimonial-author { display: flex; align-items: center; gap: .75rem; border-top: 1px solid #f0f0f0; padding-top: 1rem; }
.testimonial-author .name { font-weight: 700; font-size: .9rem; }
.testimonial-author .role { font-size: .8rem; color: #888; }

/* ── Partners ── */
.partners-section { padding: clamp(4rem,7vw,6rem) 0; background: #fff; position: relative; overflow: hidden; }
.partners-section::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, var(--main-color), #38bdf8, var(--main-color), transparent); }
.partners-head { text-align: center; padding: 0 1rem; margin-bottom: 3rem; }
.partners-badge { display: inline-flex; align-items: center; gap: .5rem; background: #eaf5fb; color: var(--main-color); padding: .38rem 1.2rem; border-radius: 50px; font-size: .82rem; font-weight: 700; margin-bottom: 1rem; border: 1px solid rgba(0,113,170,.15); }
.partners-badge::before { content: ''; width: 7px; height: 7px; border-radius: 50%; background: var(--main-color); display: inline-block; vertical-align: middle; animation: pulse-blue 2s infinite; }
@keyframes pulse-blue { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.35;transform:scale(.65)} }
.partners-head h2 { font-size: clamp(1.6rem,3vw,2.3rem); font-weight: 800; color: #0f172a; margin-bottom: .5rem; }
.partners-head p { color: #64748b; font-size: .92rem; margin: 0; }
.partners-count-row { display: flex; align-items: center; justify-content: center; gap: 1.25rem; margin-top: 1.5rem; flex-wrap: wrap; }
.p-count-chip { display: inline-flex; align-items: center; gap: .45rem; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; padding: .4rem 1rem; color: #64748b; font-size: .78rem; }
.p-count-chip strong { color: var(--main-color); font-size: .92rem; font-weight: 800; }
.partners-grid-wrap { display: flex; flex-wrap: wrap; justify-content: center; gap: 1.5rem; padding: 0 clamp(1rem,4vw,4rem) 1rem; max-width: 1200px; margin: 0 auto; }
.p-logo-card { display: flex; flex-direction: column; align-items: center; justify-content: center; width: 188px; min-height: 124px; flex-shrink: 0; background: #fff; border-radius: 16px; border: 1.5px solid #e2e8f0; box-shadow: 0 2px 12px rgba(0,0,0,.05); padding: 20px 22px 16px; gap: 10px; transition: all .3s ease; }
.p-logo-card:hover { border-color: var(--main-color); box-shadow: 0 10px 32px rgba(0,113,170,.14); transform: translateY(-5px); }
.p-logo-card img { max-width: 126px; max-height: 54px; object-fit: contain; filter: grayscale(20%) opacity(.85); transition: filter .3s; }
.p-logo-card:hover img { filter: grayscale(0%) opacity(1); }
.p-logo-card .p-name { font-size: .73rem; font-weight: 700; color: #475569; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 156px; text-align: center; display: block; }
.p-logo-card:hover .p-name { color: var(--main-color); }

/* ── App Section ── */
.app-section { background: linear-gradient(135deg, #004e7e 0%, var(--main-color) 100%); padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); color: #fff; }
.app-section h2 { font-size: clamp(1.6rem,3vw,2.4rem); font-weight: 800; margin-bottom: 1rem; }
.app-section p { opacity: .88; line-height: 1.8; max-width: 500px; }
.store-buttons { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.75rem; }
.store-btn { display: flex; align-items: center; gap: .75rem; background: rgba(255,255,255,.12); backdrop-filter: blur(10px); color: #fff; padding: .8rem 1.25rem; border-radius: 12px; border: 1.5px solid rgba(255,255,255,.25); transition: all .3s; min-width: 160px; }
.store-btn:hover { background: rgba(255,255,255,.22); transform: translateY(-3px); color: #fff; }
.store-btn .text { display: flex; flex-direction: column; line-height: 1.2; }
.store-btn .text small { font-size: .7rem; opacity: .8; }
.store-btn .text span { font-size: 1rem; font-weight: 700; }

/* ── Responsive ── */
@media (max-width: 991px) {
    .gallery-grid { grid-template-columns: repeat(2, 1fr); }
    .gallery-item-1 { grid-column: span 2; height: 280px; }
}
@media (max-width: 768px) {
    .hero-section { height: 55vh; min-height: 400px; }
    .hero-content { max-width: 100%; padding: 0 1.5rem; }
    .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
    .gallery-item-1 { height: 220px; }
    .gallery-item-2, .gallery-item-3, .gallery-item-4, .gallery-item-5 { height: 160px; }
    .how-image { height: 260px; }
}
@media (max-width: 480px) { .hero-content h1 { font-size: 1.75rem; } }
@endsection

@section('content')

<!-- ════ Hero ════ -->
<section class="hero-section">
    <div class="hero-slides">
        <div class="hero-slide active" style="background-image:url('{{ asset('lms2-photo/2.png') }}')"></div>
        <div class="hero-slide"        style="background-image:url('{{ asset('lms2-photo/14.png') }}')"></div>
        <div class="hero-slide"        style="background-image:url('{{ asset('lms2-photo/11.png') }}')"></div>
    </div>
    <div class="hero-vline"></div>
    <div class="hero-content">
        <div class="hero-tag">
            <span class="tag-dot"></span>
            {{ app()->getLocale()=='ar' ? 'معهد الارتقاء العالي للتدريب — معتمد رسمياً' : 'Al-Ertiqaa Institute — Officially Accredited' }}
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
    <div class="hero-float-stats">
        <div class="hfs-pill"><span class="hfs-n">10+</span><span class="hfs-l">{{ app()->getLocale()=='ar' ? 'سنوات خبرة' : 'Years' }}</span></div>
        <div class="hfs-pill"><span class="hfs-n">500+</span><span class="hfs-l">{{ app()->getLocale()=='ar' ? 'خريج' : 'Graduates' }}</span></div>
        <div class="hfs-pill"><span class="hfs-n">20+</span><span class="hfs-l">{{ app()->getLocale()=='ar' ? 'برنامج' : 'Programs' }}</span></div>
        <div class="hfs-pill"><span class="hfs-n">98%</span><span class="hfs-l">{{ app()->getLocale()=='ar' ? 'رضا المتدرب' : 'Satisfaction' }}</span></div>
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

<!-- ════ Stats Bar ════ -->
<section class="stats-bar">
    <div class="container">
        <div class="stats-inner">
            <div class="stat-item"><span class="stat-number">10+</span><span class="stat-label">{{ __('Years of Excellence') }}</span></div>
            <div class="stat-item"><span class="stat-number">500+</span><span class="stat-label">{{ __('Graduates') }}</span></div>
            <div class="stat-item"><span class="stat-number">20+</span><span class="stat-label">{{ __('Training Programs') }}</span></div>
            <div class="stat-item"><span class="stat-number">98%</span><span class="stat-label">{{ __('Trainee Satisfaction') }}</span></div>
        </div>
    </div>
</section>

<!-- ════ Why Choose Us ════ -->
<section class="why-section">
    <div class="container">
        <div class="section-head">
            <div class="badge-tag">{{ __('Why Us') }}</div>
            <h2>{{ __('Why Choose Us') }}</h2>
            <p>{{ __('We offer an integrated training system that combines quality, flexibility, and modern technologies to ensure the best educational experience.') }}</p>
        </div>
        <div class="row g-4">
            @php
            $cards = [
                ['icon'=>'bi-headset',      'title'=>__('Continuous Support'),       'text'=>__('24/7 technical support service helps you overcome any technical problem.')],
                ['icon'=>'bi-person-badge', 'title'=>__('Specialized Trainers'),     'text'=>__('Training is conducted by elite certified trainers with academic and professional experience.')],
                ['icon'=>'bi-laptop',       'title'=>__('Digital Education'),        'text'=>__('A smooth, secure educational experience compatible with trainees needs.')],
                ['icon'=>'bi-patch-check',  'title'=>__('Accredited Training'),      'text'=>__('Accredited by official authorities within the Kingdom, ensuring a reliable path for developing your professional skills.')],
                ['icon'=>'bi-award',        'title'=>__('Official Certificates'),    'text'=>__('After completing programs, trainees receive officially accredited certificates that enhance their career opportunities.')],
                ['icon'=>'bi-credit-card',  'title'=>__('Multiple Payment Methods'), 'text'=>__('We provide a flexible payment system that suits all trainees needs.')],
                ['icon'=>'bi-play-btn',     'title'=>__('Interactive Content'),      'text'=>__('Video lessons, files, assessments, and tests that enhance understanding and support learning by practice.')],
                ['icon'=>'bi-map',          'title'=>__('Clear Paths'),              'text'=>__('Educational plans built on clear paths extending up to 10 training quarters.')],
            ];
            @endphp
            @foreach($cards as $card)
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="why-card">
                    <div class="why-icon"><i class="bi {{ $card['icon'] }}"></i></div>
                    <h5>{{ $card['title'] }}</h5>
                    <p>{{ $card['text'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ════ Training Programs ════ -->
<section class="programs-section">
    <div class="container">
        <div class="section-head">
            <div class="badge-tag">{{ __('Programs') }}</div>
            <h2>{{ __('Comprehensive training paths to build your future') }}</h2>
            <p>{{ __('We provide training paths spanning two and a half years through 10 training quarters, plus short and specialized courses for various professional goals.') }}</p>
        </div>
        <div class="courses-grid">
            @php $programImages = ['lms2-photo/2.png','lms2-photo/8.png','lms2-photo/5.png']; @endphp
            @forelse($featuredPrograms ?? [] as $i => $program)
            <div class="course-card">
                <img src="{{ $program->image ? asset('storage/' . $program->image) : asset($programImages[$i % count($programImages)]) }}" alt="{{ $program->name }}" />
                <div class="course-card-body d-flex flex-column">
                    <h4>{{ $program->name }}</h4>
                    <p>{{ Str::limit($program->description ?? __('A comprehensive training program designed to develop professional skills.'), 100) }}</p>
                    <div class="marks">
                        @if($program->duration_months)<span class="st"><i class="bi bi-clock"></i> {{ $program->duration_months }} {{ __('months') }}</span>@endif
                        @if($program->price && $program->price > 0)
                        <span class="nd"><i class="bi bi-tag"></i> {{ number_format($program->price,0) }} {{ __('SAR') }}</span>
                        @else
                        <span class="nd"><i class="bi bi-check-circle"></i> {{ __('Free') }}</span>
                        @endif
                        <span class="th"><i class="bi bi-mortarboard"></i> {{ $program->code ?? __('Program') }}</span>
                    </div>
                    <div class="course-btns">
                        <a href="{{ route('training-paths') }}" class="btn-outline-course">{{ __('View Details') }}</a>
                        <a href="{{ auth()->check() ? route('student.my-program') : route('login') }}" class="btn-primary-course">{{ __('Register Now') }}</a>
                    </div>
                </div>
            </div>
            @empty
            @foreach([
                ['lms2-photo/2.png', __('Computer Science Diploma'),  __('Foundations of computing, programming, networks, and databases.'),           12, 5000, 'CS-101'],
                ['lms2-photo/8.png', __('Business Administration'),    __('Modern management fundamentals: leadership, planning, and decision-making.'), 12, null,  'BA-201'],
                ['lms2-photo/5.png', __('Digital Marketing Diploma'),  __('SEO, social media, paid ads, and analytics strategies.'),                     10, 4500, 'DM-301'],
            ] as [$img,$name,$desc,$months,$price,$code])
            <div class="course-card">
                <img src="{{ asset($img) }}" alt="{{ $name }}" />
                <div class="course-card-body d-flex flex-column">
                    <h4>{{ $name }}</h4>
                    <p>{{ $desc }}</p>
                    <div class="marks">
                        <span class="st"><i class="bi bi-clock"></i> {{ $months }} {{ __('months') }}</span>
                        @if($price)<span class="nd"><i class="bi bi-tag"></i> {{ number_format($price,0) }} {{ __('SAR') }}</span>@else<span class="nd"><i class="bi bi-check-circle"></i> {{ __('Free') }}</span>@endif
                        <span class="th"><i class="bi bi-mortarboard"></i> {{ $code }}</span>
                    </div>
                    <div class="course-btns">
                        <a href="{{ route('training-paths') }}" class="btn-outline-course">{{ __('View Details') }}</a>
                        <a href="{{ route('login') }}" class="btn-primary-course">{{ __('Register Now') }}</a>
                    </div>
                </div>
            </div>
            @endforeach
            @endforelse
        </div>
    </div>
</section>

<!-- ════ Gallery ════ -->
<section class="gallery-section">
    <div class="container-fluid px-4">
        <div class="section-head">
            <div class="badge-tag">{{ __('Gallery') }}</div>
            <h2>{{ __('Institute Life') }}</h2>
            <p>{{ __('A glimpse into our training environment, graduation ceremonies, and daily student life at Al-Ertiqaa.') }}</p>
        </div>
        <div class="gallery-grid">
            <div class="gallery-item gallery-item-1"><img loading="lazy" src="{{ asset('lms2-photo/2.png') }}" alt="Institute Building" /><div class="overlay"></div></div>
            <div class="gallery-item gallery-item-2"><img loading="lazy" src="{{ asset('lms2-photo/14.png') }}" alt="Computer Lab" /><div class="overlay"></div></div>
            <div class="gallery-item gallery-item-3"><img loading="lazy" src="{{ asset('lms2-photo/1.png') }}" alt="Student" /><div class="overlay"></div></div>
            <div class="gallery-item gallery-item-4"><img loading="lazy" src="{{ asset('lms2-photo/5.png') }}" alt="Graduation" /><div class="overlay"></div></div>
            <div class="gallery-item gallery-item-5"><img loading="lazy" src="{{ asset('lms2-photo/9.png') }}" alt="Award" /><div class="overlay"></div></div>
        </div>
    </div>
</section>

<!-- ════ How It Works ════ -->
<section class="how-section">
    <div class="container">
        <div class="section-head">
            <div class="badge-tag">{{ __('Process') }}</div>
            <h2>{{ __('How does our training system work?') }}</h2>
            <p>{{ __('An integrated training system that ensures a clear, organized educational journey with measurable results.') }}</p>
        </div>
        <div class="row align-items-center g-5">
            <div class="col-lg-6 {{ app()->getLocale()=='ar' ? 'order-2' : 'order-1' }}">
                <div class="how-image" style="height:420px"><img loading="lazy" src="{{ asset('lms2-photo/1.png') }}" alt="How It Works" /></div>
            </div>
            <div class="col-lg-6 {{ app()->getLocale()=='ar' ? 'order-1' : 'order-2' }}">
                <div class="how-steps">
                    <div class="how-step"><div class="how-step-number">1</div><div class="how-step-text"><h5>{{ __('Registration and Getting Started') }}</h5><p>{{ __('Start your educational journey easily by creating an account or logging in through Nafath, then discover programs and paths designed to suit your goals.') }}</p></div></div>
                    <div class="how-step"><div class="how-step-number">2</div><div class="how-step-text"><h5>{{ __('Choosing the Right Program for You') }}</h5><p>{{ __('Whether you\'re looking for an academic path spanning two and a half years, or a short course, you will find what suits your goals.') }}</p></div></div>
                    <div class="how-step"><div class="how-step-number">3</div><div class="how-step-text"><h5>{{ __('Learning and Follow-up') }}</h5><p>{{ __('Study through visual and organized content, with an attendance system, clear training progress, and direct communication with trainers.') }}</p></div></div>
                    <div class="how-step"><div class="how-step-number">4</div><div class="how-step-text"><h5>{{ __('Assessment and Certification') }}</h5><p>{{ __('After completing your training requirements, you will be evaluated and your accredited digital certificate will be issued.') }}</p></div></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ════ Testimonials ════ -->
<section class="testimonials-section">
    <div class="container">
        <div class="section-head">
            <div class="badge-tag">{{ __('Reviews') }}</div>
            <h2>{{ __('What Our Trainees Say') }}</h2>
            <p>{{ __('Real experiences from our graduates who made a difference in their careers.') }}</p>
        </div>
        <div class="row g-4">
            @php
            $testimonials = app()->getLocale() === 'ar' ? [
                ['text'=>'رحلة تدريبية مميزة وواضحة من البداية للنهاية، متابعة التيرم والدروس كانت سهلة جدًا، والتطبيق ساعدني أتابع تقدمي بشكل يومي.', 'author'=>'سلمان .م', 'role'=>'دبلومة الحاسب وتقنية المعلومات'],
                ['text'=>'تجربتي مع المعهد كانت رائعة، المدربون محترفون والمحتوى التعليمي ممتاز. أنصح الجميع بالتسجيل في برامجهم التدريبية.', 'author'=>'أحمد .ع', 'role'=>'دبلومة إدارة الأعمال'],
                ['text'=>'الدعم الفني متميز والاستجابة سريعة. البرنامج التدريبي ساعدني على تطوير مهاراتي المهنية بشكل ملحوظ.', 'author'=>'نورة .س', 'role'=>'دبلومة التسويق الرقمي'],
            ] : [
                ['text'=>'A distinctive training journey from start to finish. Following the term and lessons was very easy, and the app helped me track my daily progress.', 'author'=>'Salman M.', 'role'=>'Computer & IT Diploma'],
                ['text'=>'My experience with the institute was wonderful. The trainers are professional and the content is excellent.', 'author'=>'Ahmed A.', 'role'=>'Business Administration Diploma'],
                ['text'=>'Outstanding technical support and fast response. The training program helped me develop my professional skills noticeably.', 'author'=>'Noura S.', 'role'=>'Digital Marketing Diploma'],
            ];
            @endphp
            @foreach($testimonials as $t)
            <div class="col-md-4">
                <div class="testimonial-card">
                    <div class="testimonial-stars"><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i></div>
                    <p class="testimonial-text">"{{ $t['text'] }}"</p>
                    <div class="testimonial-author">
                        <div style="width:48px;height:48px;border-radius:50%;background:var(--main-color);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.2rem;flex-shrink:0">{{ mb_substr($t['author'],0,1) }}</div>
                        <div><div class="name">{{ $t['author'] }}</div><div class="role">{{ $t['role'] }}</div></div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ════ App Section ════ -->
<section class="app-section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2>{{ __('Download the App & Learn Anywhere') }}</h2>
                <p>{{ __('Follow your courses, attend live sessions, and track your progress — all from your phone.') }}</p>
                <div class="store-buttons">
                    <a href="#" class="store-btn"><i class="bi bi-apple" style="font-size:1.8rem"></i><div class="text"><small>{{ __('Download on the') }}</small><span>App Store</span></div></a>
                    <a href="#" class="store-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="28" viewBox="0 0 21 24" fill="none"><path d="M9.80482 11.4617L0.0895996 22.0059C0.389807 23.1574 1.41179 24 2.62539 24C3.11083 24 3.56616 23.8656 3.95671 23.6305L14.9229 17.1593L9.80482 11.4617Z" fill="#EA4335"/><path d="M19.6332 9.66424L14.9029 6.85928L9.58398 11.6994L14.922 17.1562L19.6177 14.3858C20.4407 13.9305 21.0001 13.0431 21.0001 12.0204C21.0001 11.0033 20.4489 10.1205 19.6332 9.66424Z" fill="#FBBC04"/><path d="M0.0894234 1.9952C0.0310244 2.21542 0 2.44683 0 2.68571V21.3182C0 21.5571 0.0310245 21.7885 0.0903359 22.0078L10.1386 11.7332L0.0894234 1.9952Z" fill="#4285F4"/><path d="M9.87666 12L14.9044 6.85945L3.98201 0.383598C3.58508 0.140054 3.12154 0 2.62606 0C1.41246 0 0.38865 0.84456 0.0902675 1.99043L9.87666 12Z" fill="#34A853"/></svg>
                        <div class="text"><small>{{ __('Get it on') }}</small><span>Google Play</span></div>
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img loading="lazy" src="{{ asset('lms2-photo/14.png') }}" alt="App" style="max-width:100%;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.3);max-height:360px;object-fit:cover" />
            </div>
        </div>
    </div>
</section>

<!-- ════ Partners ════ -->
@php $partners = \App\Models\Partner::where('is_active', true)->orderBy('sort_order')->orderBy('id')->get(); @endphp
@if($partners->isNotEmpty())
<section class="partners-section">
    <div class="partners-head">
        <div class="partners-badge">{{ app()->getLocale() === 'ar' ? 'شركاؤنا' : 'Our Partners' }}</div>
        <h2>{{ app()->getLocale() === 'ar' ? 'شركائنا المتعاونة' : 'Partners & Affiliates' }}</h2>
        <p>{{ app()->getLocale() === 'ar' ? 'نفخر بشراكتنا مع عدد من الجهات والمنظمات الرائدة' : 'Proud to work alongside leading organizations and institutions' }}</p>
        <div class="partners-count-row">
            <div class="p-count-chip"><i class="bi bi-buildings"></i><strong>{{ $partners->count() }}+</strong><span>{{ app()->getLocale() === 'ar' ? 'جهة شريكة' : 'Partner Organizations' }}</span></div>
            <div class="p-count-chip"><i class="bi bi-patch-check-fill" style="color:#60a5fa"></i><span>{{ app()->getLocale() === 'ar' ? 'شراكات موثوقة ومعتمدة' : 'Verified & Accredited' }}</span></div>
        </div>
    </div>
    <div class="partners-grid-wrap">
        @foreach($partners as $p)
        <div class="p-logo-card">
            @if($p->url)<a href="{{ $p->url }}" target="_blank" rel="noopener" style="display:contents"><img src="{{ Storage::url($p->logo) }}" alt="{{ $p->name }}"></a>
            @else<img src="{{ Storage::url($p->logo) }}" alt="{{ $p->name }}">@endif
            <span class="p-name">{{ $p->name }}</span>
        </div>
        @endforeach
    </div>
</section>
@endif

@endsection

@section('scripts')
<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots   = document.querySelectorAll('.hero-dot');

function goToSlide(n) {
    slides[currentSlide].classList.remove('active');
    dots[currentSlide].classList.remove('active');
    currentSlide = (n + slides.length) % slides.length;
    slides[currentSlide].classList.add('active');
    dots[currentSlide].classList.add('active');
}

setInterval(() => goToSlide(currentSlide + 1), 5000);
</script>
@endsection
