@extends('layouts.front')

@section('title', __('Training Paths') . ' - ' . __('Al-Ertiqaa High Institute for Training'))

@section('styles')
<style>
    /* Statistics Section */
    .stats-section {
        background: var(--second-color);
        padding: 2rem clamp(3rem, 7vw, 7rem);
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

    /* Courses Section */
    .courses-section {
        padding: 2rem clamp(3rem, 7vw, 7rem);
    }

    .courses-section .head {
        text-align: center;
        margin-bottom: 2rem;
    }

    .courses-section .head h2 {
        margin: 1rem 0;
    }

    .courses-section .head p {
        max-width: 800px;
        margin: 0 auto;
        line-height: 1.8;
        color: rgba(56, 66, 80, 1);
    }

    .course-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .course-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .course-card .card-body {
        padding: 1.5rem;
    }

    .course-card .card-title {
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .course-card .card-text {
        color: rgba(56, 66, 80, 1);
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .course-card .course-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }

    .course-card .course-price {
        color: var(--main-color);
        font-weight: bold;
    }

    /* Mockup Section */
    .mockup-section {
        background: linear-gradient(135deg, #1d6b8f 0%, #0071aa 100%);
        padding: 3rem clamp(3rem, 7vw, 7rem);
        color: white;
    }

    .mockup-section .content {
        max-width: 600px;
    }

    .mockup-section h2 {
        margin-bottom: 1.5rem;
    }

    .mockup-section p {
        line-height: 1.8;
        opacity: 0.95;
        margin-bottom: 2rem;
    }

    .store-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .store-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #000;
        color: white;
        padding: 12px 20px;
        border-radius: 10px;
        text-decoration: none;
        transition: transform 0.3s;
    }

    .store-btn:hover {
        transform: translateY(-3px);
        color: white;
    }

    /* FAQ Section */
    .faq-section {
        padding: 2rem clamp(3rem, 7vw, 7rem);
    }

    .faq-section .head {
        margin-bottom: 2rem;
    }

    .faq-section h2 {
        margin: 0.5rem 0;
    }

    .accordion-item {
        border: 1px solid #e5e7eb;
        margin-bottom: 1rem;
        border-radius: 10px !important;
        overflow: hidden;
    }

    .accordion-button {
        background: white;
        font-weight: 500;
        padding: 1.25rem;
    }

    .accordion-button:not(.collapsed) {
        background: #f9fafb;
        color: #000;
        box-shadow: none;
    }

    .accordion-button:focus {
        box-shadow: none;
    }

    .accordion-body {
        background: #f9fafb;
        line-height: 1.8;
    }

    @media (max-width: 991px) {
        .left-sec {
            padding: 20px;
            text-align: center;
            margin-top: 2rem;
        }

        .left-sec img {
            width: 100%;
        }

        .abs-btn, .img-abs {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .stats-section,
        .courses-section,
        .faq-section {
            padding: 1.5rem 1rem;
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
            <span>{{ __('Training Paths') }}</span>
        </div>
        <h2>{{ __('Training Paths That Build Your Professional Future with Confidence') }}</h2>
        <p>
            {{ __('At our institute, we provide an integrated training system based on the needs of the Saudi labor market in accordance with Vision 2030, through training paths spanning two and a half years (10 training quarters), preparing trainees with practical and scientific expertise that makes their educational journey clear, strong, and directed towards future careers.') }}
        </p>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="row text-center">
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">10+</div>
                <div class="stat-label">{{ __('Training Paths') }}</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">{{ __('Training Courses') }}</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">1000+</div>
                <div class="stat-label">{{ __('Trainees') }}</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">95%</div>
                <div class="stat-label">{{ __('Satisfaction Rate') }}</div>
            </div>
        </div>
    </section>

    <!-- Training Programs Section -->
    <section class="courses-section">
        <div class="head">
            <p class="st-p mx-auto">{{ __('Training That Meets Your Needs') }}</p>
            <h2>{{ __('Our Training Programs and Specialized Paths') }}</h2>
            <p>
                {{ __('A diverse range of accredited training programs designed to meet labor market needs. Choose your training path and start a professional journey supported by certified experts and modern methodologies.') }}
            </p>
        </div>

        <div class="courses-container">
            @for($i = 0; $i < 6; $i++)
            <div class="course-card">
                <img src="{{ asset('images/course.jpg') }}" alt="Course" />
                <div class="card-body">
                    <h5 class="card-title">{{ __('Web Development Path') }}</h5>
                    <p class="card-text">{{ __('Learn the fundamentals and techniques of modern web application development using the latest technologies.') }}</p>
                    <div class="course-meta">
                        <span><i class="bi bi-clock"></i> 10 {{ __('Quarters') }}</span>
                        <span class="course-price">2500 {{ __('SAR') }}</span>
                    </div>
                    <button class="full-btn mt-3 w-100">{{ __('View Details') }}</button>
                </div>
            </div>
            @endfor
        </div>

        <div class="text-center mt-4">
            <button class="notfull-btn">{{ __('View All Paths') }}</button>
        </div>
    </section>

    <!-- Similar Paths Section -->
    <section class="courses-section" style="background: #f9fafb;">
        <div class="head">
            <p class="st-p mx-auto">{{ __('Training That Meets Your Needs') }}</p>
            <h2>{{ __('Similar Paths') }}</h2>
            <p>
                {{ __('Explore more paths that suit your interests and professional aspirations. Each path is designed to develop your skills practically and progressively, with official accreditation and recognized certificates.') }}
            </p>
        </div>

        <div class="courses-container">
            @for($i = 0; $i < 3; $i++)
            <div class="course-card">
                <img src="{{ asset('images/course.jpg') }}" alt="Course" />
                <div class="card-body">
                    <h5 class="card-title">{{ __('Cybersecurity Path') }}</h5>
                    <p class="card-text">{{ __('Learn the fundamentals and techniques of cybersecurity to protect data and digital systems.') }}</p>
                    <div class="course-meta">
                        <span><i class="bi bi-clock"></i> 10 {{ __('Quarters') }}</span>
                        <span class="course-price">3000 {{ __('SAR') }}</span>
                    </div>
                    <button class="full-btn mt-3 w-100">{{ __('View Details') }}</button>
                </div>
            </div>
            @endfor
        </div>

        <div class="text-center mt-4">
            <button class="notfull-btn">{{ __('View All Questions') }}</button>
        </div>
    </section>

    <!-- Mockup Section -->
    <section class="mockup-section">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="content">
                    <p class="st-p" style="background: rgba(255,255,255,0.2); color: white;">{{ __('Training That Meets Your Needs') }}</p>
                    <h2>{{ __('Download Our App Now and Start Your Learning Journey') }}</h2>
                    <p>
                        {{ __('Our institute app provides you with quick and direct access to all training paths and courses. Track your progress, receive lecture notifications, and communicate with trainers easily from anywhere.') }}
                    </p>
                    <div class="store-buttons" dir="ltr">
                        <a href="#" class="store-btn">
                            <i class="bi bi-apple" style="font-size: 24px;"></i>
                            <div>
                                <small>Download on the</small>
                                <div>App Store</div>
                            </div>
                        </a>
                        <a href="#" class="store-btn">
                            <i class="bi bi-google-play" style="font-size: 24px;"></i>
                            <div>
                                <small>GET IT ON</small>
                                <div>Google Play</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="{{ asset('images/mockup.png') }}" alt="App Mockup" style="max-width: 400px;" onerror="this.style.display='none'" />
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="head">
            <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
            <h2>{{ __('Frequently Asked Questions About Paths') }}</h2>
        </div>

        <div class="accordion" id="faqAccordion" style="max-width: 900px; margin: 0 auto;">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        {{ __('Can I switch from one path to another?') }}
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ __('Yes, you can switch between paths according to specific academic conditions and after reviewing the completed training hours and coordinating with the academic administration.') }}
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        {{ __('What is the term system?') }}
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ __('The term system is a system of dividing the training path into specific time periods (quarters), each lasting a certain period, which helps organize the training process and periodically evaluate the trainee\'s progress.') }}
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        {{ __('What payment methods are available?') }}
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ __('We provide several payment methods including: credit cards, Mada, bank transfer, and payment upon registration. We also offer installment options for long paths.') }}
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        {{ __('What if I need technical support?') }}
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ __('The technical support team is available around the clock to answer your inquiries and solve any technical problems you may encounter. You can contact us via phone, email, or ticket system.') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
