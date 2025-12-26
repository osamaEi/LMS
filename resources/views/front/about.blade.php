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
        {{ app()->getLocale() == 'ar' ? 'right: 20px' : 'right: 20px' }};
    }

    .vision-card.two {
        bottom: 24px;
        top: auto;
        {{ app()->getLocale() == 'ar' ? 'left: 20px' : 'left: 20px' }};
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

    .contact-image img {
        width: 100%;
        border-radius: 20px;
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
    <section class="stats-section">
        <div class="row text-center">
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">10+</div>
                <div class="stat-label">{{ __('Years of Experience') }}</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">{{ __('Training Programs') }}</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">5000+</div>
                <div class="stat-label">{{ __('Trainees') }}</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">100+</div>
                <div class="stat-label">{{ __('Certified Trainers') }}</div>
            </div>
        </div>
    </section>

    <!-- Vision & Mission Section -->
    <section class="vision-section">
        <div class="center">
            <img class="main-img" src="{{ asset('images/nd-center.jpg') }}" alt="Vision" onerror="this.src='{{ asset('images/course.jpg') }}'" />

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

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="row">
            <div class="col-lg-6">
                <div class="contact-content">
                    <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
                    <h2>{{ __('We\'re Happy to Hear From You... We Value Every Question and Inquiry') }}</h2>
                    <p>
                        {{ __('Our support and guidance team is here to serve you and answer all your questions related to training programs, career paths, registration procedures, term system, course schedules, or any details you need to know before starting your journey with us.') }}
                    </p>
                    <a href="{{ route('contact') }}" class="full-btn">{{ __('Contact Us Now') }}</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-image">
                    <img src="{{ asset('images/contactUs.jpg') }}" alt="Contact Us" onerror="this.src='{{ asset('images/course.jpg') }}'" />
                </div>
            </div>
        </div>
    </section>
@endsection
