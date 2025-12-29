@extends('layouts.front')

@section('title', __('Short Courses') . ' - ' . __('Al-Ertiqaa High Institute for Training'))

@section('styles')
<style>
    /* Courses Section */
    .courses-section {
        padding: 2rem clamp(1rem, 3vw, 3rem);
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

    .course-badge {
        position: absolute;
        top: 15px;
        {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 15px;
        background: var(--main-color);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
    }

    .course-card-wrapper {
        position: relative;
    }

    /* Mockup Section */
    .mockup-section {
        background: linear-gradient(135deg, #1d6b8f 0%, #0071aa 100%);
        padding: 3rem clamp(1rem, 3vw, 3rem);
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
        padding: 2rem clamp(1rem, 3vw, 3rem);
        background: #f9fafb;
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

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 3rem;
        flex-wrap: wrap;
    }

    .pagination-btn {
        min-width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #e0e0e0;
        background-color: white;
        color: #666;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 0 12px;
    }

    .pagination-btn:hover {
        border-color: var(--main-color);
        transform: translateY(-2px);
    }

    .pagination-btn.active {
        background-color: var(--main-color);
        color: white;
        border-color: var(--main-color);
    }

    @media (max-width: 768px) {
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
            <span>{{ __('Short Courses') }}</span>
        </div>
        <h2>{{ __('Short Courses') }}</h2>
    </section>

    <!-- Courses Section 1 -->
    <section class="courses-section mb-5">
        <div class="head">
            <p class="st-p mx-auto">{{ __('Training That Meets Your Needs') }}</p>
            <h2>{{ __('Choose the Right Short Course for You') }}</h2>
            <p>
                {{ __('Short courses are designed to develop your skills quickly and effectively in multiple fields, officially accredited and suitable for students and professionals seeking to enhance their expertise without committing to a long path.') }}
            </p>
        </div>

        <div class="courses-container">
            @php
            $courses = [
                ['title' => __('Programming Fundamentals Course'), 'duration' => '4 ' . __('Weeks'), 'price' => '500'],
                ['title' => __('Digital Marketing Course'), 'duration' => '3 ' . __('Weeks'), 'price' => '450'],
                ['title' => __('Project Management Course'), 'duration' => '5 ' . __('Weeks'), 'price' => '600'],
                ['title' => __('UI/UX Design Course'), 'duration' => '4 ' . __('Weeks'), 'price' => '550'],
                ['title' => __('Cybersecurity Course'), 'duration' => '6 ' . __('Weeks'), 'price' => '700'],
                ['title' => __('Data Analysis Course'), 'duration' => '5 ' . __('Weeks'), 'price' => '650'],
            ];
            @endphp

            @foreach($courses as $course)
            <div class="course-card-wrapper">
                <div class="course-card">
                    <img src="{{ asset('images/course.jpg') }}" alt="{{ $course['title'] }}" />
                    <div class="card-body">
                        <h5 class="card-title">{{ $course['title'] }}</h5>
                        <p class="card-text">{{ __('Learn fundamentals and advanced skills in this field with expert trainers and updated practical content.') }}</p>
                        <div class="course-meta">
                            <span><i class="bi bi-clock"></i> {{ $course['duration'] }}</span>
                            <span class="course-price">{{ $course['price'] }} {{ __('SAR') }}</span>
                        </div>
                        <button class="full-btn mt-3 w-100">{{ __('View Details') }}</button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    <!-- Featured Courses Section -->
    <section class="courses-section pt-5 pb-5" style="background: #f9fafb;">
        <div class="head">
            <p class="st-p mx-auto">{{ __('Training That Meets Your Needs') }}</p>
            <h2>{{ __('Featured Courses to Boost Your Skills Quickly') }}</h2>
            <p>
                {{ __('We offer you the best carefully selected courses to develop your skills practically and quickly.') }}
            </p>
        </div>

        <div class="courses-container">
            @for($i = 0; $i < 6; $i++)
            <div class="course-card-wrapper">
                <span class="course-badge">{{ __('Featured') }}</span>
                <div class="course-card">
                    <img src="{{ asset('images/course.jpg') }}" alt="Course" />
                    <div class="card-body">
                        <h5 class="card-title">{{ __('Artificial Intelligence Course') }}</h5>
                        <p class="card-text">{{ __('Discover the world of artificial intelligence and machine learning with real practical applications.') }}</p>
                        <div class="course-meta">
                            <span><i class="bi bi-clock"></i> 6 {{ __('Weeks') }}</span>
                            <span class="course-price">800 {{ __('SAR') }}</span>
                        </div>
                        <button class="full-btn mt-3 w-100">{{ __('View Details') }}</button>
                    </div>
                </div>
            </div>
            @endfor
        </div>

        <div class="text-center mt-4">
            <button class="notfull-btn">{{ __('View All Paths') }}</button>
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
            <h2>{{ __('Frequently Asked Questions About Courses') }}</h2>
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
                        {{ __('How do I register at the institute?') }}
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ __('You can easily register via Nafath account or create an internal account. After registration, you can choose the academic path or short courses that suit you.') }}
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        {{ __('What is the duration of a short course?') }}
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ __('Short courses range from two weeks to 8 weeks depending on the nature and content of the course.') }}
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        {{ __('Do I get a certificate after completing the course?') }}
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ __('Yes, the trainee receives an accredited certificate after successfully completing the course and passing the required assessments.') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
