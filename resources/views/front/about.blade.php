@extends('layouts.front')

@section('title', __('About Us') . ' - ' . __('Al-Ertiqaa High Institute for Training'))

@section('styles')
<style>
    /* Stats Section */
    .stats-section {
        background: var(--second-color);
        padding: 2rem clamp(1rem, 3vw, 3rem);
        margin: 0 clamp(1rem, 3vw, 3rem);
        border-radius: 12px;
    }

    .stat-item {
        text-align: center;
        padding: 1.5rem;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--main-color);
    }

    .stat-label {
        color: rgba(56, 66, 80, 1);
        margin-top: 0.5rem;
    }

    /* Vision Section */
    .vision-section {
        padding: 2rem clamp(1rem, 3vw, 3rem);
    }

    .vision-section .center {
        position: relative;
        height: 700px;
        overflow: hidden;
    }

    .vision-section .main-img {
        width: 100%;
        height: 100%;
        border-radius: 30px;
        object-fit: cover;
    }

    .vision-card {
        background-color: rgba(229, 231, 235, 1);
        width: 45%;
        border-radius: 20px;
        padding: 15px 20px;
        position: absolute;
        display: flex;
        gap: 15px;
        align-items: flex-start;
    }

    .vision-card.one {
        top: 24px;
        right: 20px;
    }

    [dir="ltr"] .vision-card.one {
        right: auto;
        left: 20px;
    }

    .vision-card.two {
        bottom: 24px;
        top: auto;
        left: 20px;
    }

    [dir="ltr"] .vision-card.two {
        left: auto;
        right: 20px;
    }

    .vision-card .icon {
        width: 55px;
        height: 55px;
        background-color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        padding: 9px;
        flex-shrink: 0;
    }

    .vision-card .icon i {
        font-size: 24px;
        color: var(--main-color);
    }

    .vision-card h3 {
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
    }

    .vision-card p {
        margin: 0;
        font-size: 0.9rem;
        line-height: 1.6;
        color: rgba(56, 66, 80, 1);
    }

    /* Values Section */
    .values-section {
        padding: 2rem clamp(1rem, 3vw, 3rem);
        background: #f9fafb;
    }

    .values-section .head {
        text-align: center;
        margin-bottom: 2rem;
    }

    .values-section h2 {
        margin: 1rem 0;
    }

    .value-card {
        border: none;
        border-radius: 16px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 1) 0%, rgba(82, 154, 255, 0.2) 75%);
        padding: 20px;
        height: 100%;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .value-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .value-card i {
        font-size: 50px;
        color: var(--main-color);
        margin-bottom: 1rem;
    }

    .value-card h5 {
        margin-bottom: 0.5rem;
        font-weight: bold;
    }

    .value-card p {
        font-size: 0.9rem;
        color: rgba(56, 66, 80, 1);
        line-height: 1.6;
    }

    /* Gallery Section */
    .gallery-section {
        padding: 2rem clamp(1rem, 3vw, 3rem);
    }

    .gallery-section .head {
        text-align: center;
        margin-bottom: 2rem;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(2, 220px);
        gap: 12px;
        border-radius: 20px;
        overflow: hidden;
    }

    .gallery-grid .g-item {
        overflow: hidden;
        border-radius: 12px;
    }

    .gallery-grid .g-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .gallery-grid .g-item:hover img {
        transform: scale(1.06);
    }

    .gallery-grid .g-item.wide {
        grid-column: span 2;
    }

    .gallery-grid .g-item.tall {
        grid-row: span 2;
    }

    @media (max-width: 768px) {
        .gallery-grid {
            grid-template-columns: repeat(2, 1fr);
            grid-template-rows: auto;
        }
        .gallery-grid .g-item.wide,
        .gallery-grid .g-item.tall {
            grid-column: span 1;
            grid-row: span 1;
        }
        .gallery-grid .g-item {
            height: 160px;
        }
    }

    /* Contact Section */
    .contact-section {
        padding: 2rem clamp(1rem, 3vw, 3rem);
    }

    .contact-section .row {
        align-items: center;
    }

    .contact-content h2 {
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .contact-content p {
        line-height: 1.8;
        color: rgba(56, 66, 80, 1);
        margin-bottom: 1.5rem;
    }

    /* Formal image frame */
    .formal-img-wrap {
        position: relative;
        padding: 20px 20px 20px 0;
    }
    [dir="ltr"] .formal-img-wrap { padding: 20px 0 20px 20px; }

    .formal-img-wrap::before {
        content: '';
        position: absolute;
        top: 0; right: 0;
        width: 75%; height: 80%;
        background: linear-gradient(135deg, var(--main-color, #0071AA) 0%, #004d77 100%);
        border-radius: 24px;
        z-index: 0;
        opacity: .12;
    }
    .formal-img-wrap::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0;
        width: 50%; height: 55%;
        border: 3px solid var(--main-color, #0071AA);
        border-radius: 20px;
        z-index: 0;
        opacity: .25;
    }

    .formal-img-inner {
        position: relative;
        z-index: 1;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    }

    .formal-img-inner img {
        width: 100%;
        height: 380px;
        object-fit: cover;
        display: block;
        filter: brightness(.96) contrast(1.04) saturate(1.05);
    }

    .formal-img-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,30,60,.55) 0%, transparent 55%);
        border-radius: 20px;
    }

    .formal-img-badge {
        position: absolute;
        bottom: 20px;
        right: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255,255,255,.96);
        backdrop-filter: blur(8px);
        border-radius: 14px;
        padding: 10px 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    [dir="ltr"] .formal-img-badge { right: auto; left: 20px; }

    .formal-img-badge-icon {
        width: 38px; height: 38px;
        background: linear-gradient(135deg, var(--main-color, #0071AA), #004d77);
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .formal-img-badge-text strong {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        line-height: 1.2;
    }
    .formal-img-badge-text span {
        font-size: 11px;
        color: #6b7280;
    }

    .formal-img-corner {
        position: absolute;
        top: -12px; left: -12px;
        width: 56px; height: 56px;
        background: linear-gradient(135deg, var(--main-color, #0071AA), #005a88);
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 6px 20px rgba(0,113,170,.35);
        z-index: 2;
    }
    [dir="ltr"] .formal-img-corner { left: auto; right: -12px; }

    @media (max-width: 768px) {
        .formal-img-inner img { height: 260px; }
        .contact-image { margin-top: 2rem; }
    }

    @media (max-width: 991px) {
        .vision-section .center {
            height: auto;
            min-height: 500px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .vision-section .main-img {
            position: relative !important;
            height: 300px;
            margin-bottom: 15px;
        }

        .vision-card {
            position: relative !important;
            width: 100%;
            top: auto !important;
            right: auto !important;
            left: auto !important;
        }
    }

    @media (max-width: 768px) {
        .stats-section,
        .vision-section,
        .values-section,
        .contact-section {
            padding: 1.5rem 1rem;
            margin: 0 1rem;
        }

        .contact-image {
            margin-top: 2rem;
        }
    }
</style>
@endsection

@section('content')
@php
    $lms3s = fn(string $n) => asset('lms3/' . rawurlencode('حين يلتقي التدريب مع الإبداع 3') . '/' . $n);
@endphp
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="breadcrumb-nav">
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
            <span>></span>
            <span>{{ __('About Us') }}</span>
        </div>
        <h2>{{ __('Al-Ertiqaa High Institute... Where Your Professional Future Begins') }}</h2>
        <p>
            {{ __('An accredited training institution offering professional development programs and specialized paths spanning two and a half years, according to the highest training quality standards within the Kingdom.') }}
        </p>
    </section>

    <!-- Stats Section -->
  

    <!-- Vision & Mission Section -->
    <section class="vision-section">
        <div class="center">
            <img class="main-img" src="{{ $lms3s('1.png') }}" alt="Vision" />

            <div class="vision-card one">
                <div class="icon">
                    <i class="bi bi-eye"></i>
                </div>
                <div class="text">
                    <h3>{{ __('Vision') }}</h3>
                    <p>
                        {{ __('To be one of the leading training institutes in the Kingdom, and a model in the quality of education and professional training.') }}
                    </p>
                </div>
            </div>

            <div class="vision-card two">
                <div class="icon">
                    <i class="bi bi-bullseye"></i>
                </div>
                <div class="text">
                    <h3>{{ __('Mission') }}</h3>
                    <p>
                        {{ __('Providing accredited training programs and effective educational paths that contribute to developing individuals\' skills and meeting the needs of the Saudi labor market.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <div class="head">
            <p class="st-p mx-auto">{{ __('Training That Meets Your Needs') }}</p>
            <h2>{{ __('Our Core Values') }}</h2>
        </div>

        <div class="row g-4">
            @php
            $values = [
                ['icon' => 'bi-shield-fill-check', 'title' => __('Quality'), 'desc' => __('We commit to high quality standards in all training programs.')],
                ['icon' => 'bi-award', 'title' => __('Reliability'), 'desc' => __('We are keen on building trust with trainees and partners.')],
                ['icon' => 'bi-arrow-repeat', 'title' => __('Continuous Update'), 'desc' => __('We continuously develop our content to keep pace with the labor market.')],
                ['icon' => 'bi-person-check', 'title' => __('Learner Empowerment'), 'desc' => __('We focus on developing the trainee\'s comprehensive skills.')],
                ['icon' => 'bi-lightbulb', 'title' => __('Innovation'), 'desc' => __('We are always looking for new ways to improve the educational experience.')],
                ['icon' => 'bi-people', 'title' => __('Collaboration'), 'desc' => __('We believe in the importance of teamwork and effective partnerships.')],
                ['icon' => 'bi-graph-up-arrow', 'title' => __('Excellence'), 'desc' => __('We strive to achieve excellence in everything we offer.')],
                ['icon' => 'bi-heart', 'title' => __('Dedication'), 'desc' => __('We work with dedication to achieve the trainees\' goals.')],
            ];
            @endphp

            @foreach($values as $value)
            <div class="col-lg-3 col-md-4 col-sm-6">
                <div class="value-card">
                    <i class="bi {{ $value['icon'] }}"></i>
                    <h5>{{ $value['title'] }}</h5>
                    <p>{{ $value['desc'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="head">
            <h2>{{ __('From Our Institute') }}</h2>
        </div>
        <div class="gallery-grid">
            <div class="g-item tall">
                <img loading="lazy" src="{{ $lms3s('2.png') }}" alt="معهد الارتقاء" />
            </div>
            <div class="g-item">
                <img loading="lazy" src="{{ $lms3s('4.png') }}" alt="قاعة التدريب" />
            </div>
            <div class="g-item">
                <img loading="lazy" src="{{ $lms3s('5.png') }}" alt="المتدربون" />
            </div>
            <div class="g-item">
                <img loading="lazy" src="{{ $lms3s('9.png') }}" alt="الحفل" />
            </div>
            <div class="g-item wide">
                <img loading="lazy" src="{{ $lms3s('13.png') }}" alt="مختبر الحاسب" />
            </div>
            <div class="g-item">
                <img loading="lazy" src="{{ $lms3s('11.png') }}" alt="التدريب" />
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="row">
            <div class="col-lg-6">
                <div class="contact-content">
                    <h2>{{ __('We\'re Happy to Hear From You... We Value Every Question and Inquiry') }}</h2>
                    <p>
                        {{ __('Our support and guidance team is here to serve you and answer all your questions related to training programs, career paths, registration procedures, term system, course schedules, or any details you need to know before starting your journey with us.') }}
                    </p>
                    <a href="{{ route('contact') }}" class="full-btn">{{ __('Contact Us Now') }}</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="formal-img-wrap">
                    <div class="formal-img-inner">
                        <img loading="lazy" src="{{ $lms3s('6.png') }}" alt="Contact Us" />
                        <div class="formal-img-overlay"></div>
                        <div class="formal-img-badge">
                            <div class="formal-img-badge-icon">
                                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div class="formal-img-badge-text">
                                <strong>{{ __('We\'re here for you') }}</strong>
                                <span>{{ __('24/7 Support') }}</span>
                            </div>
                        </div>
                        <div class="formal-img-corner">
                            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
