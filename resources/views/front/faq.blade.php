@extends('layouts.front')

@section('title', __('FAQ') . ' - ' . __('Al-Ertiqaa High Institute for Training'))

@section('styles')
<style>
    /* FAQ Body Section */
    .faq-body-section {
        padding: 2rem clamp(1rem, 3vw, 3rem);
        max-width: 1000px;
        margin: 0 auto;
    }

    /* Search Box */
    .search-box {
        position: relative;
        margin-bottom: 2rem;
        text-align: center;
    }

    .search-box input {
        width: 50%;
        border-radius: 7px;
        border: 1px solid #ccc;
        padding: 12px 15px 12px 45px;
        outline: none;
        background-color: rgba(230, 229, 229, 0.817);
    }

    .search-box input:focus {
        border-color: var(--main-color);
    }

    .search-box .bi-search {
        position: absolute;
        {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
    }

    .search-box .filter-btn {
        background-color: var(--main-color);
        color: white;
        padding: 10px 25px;
        border: none;
        border-radius: 8px;
        {{ app()->getLocale() == 'ar' ? 'margin-right' : 'margin-left' }}: 10px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .search-box .filter-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Category Tabs */
    .category-tabs {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 2rem;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 1rem;
        justify-content: center;
    }

    .category-tab {
        padding: 10px 20px;
        color: #333;
        text-decoration: none;
        border-radius: 5px;
        transition: all 0.3s;
        cursor: pointer;
        background: transparent;
        border: none;
    }

    .category-tab:hover,
    .category-tab.active {
        color: var(--main-color);
        border-bottom: 2px solid var(--main-color);
    }

    /* FAQ Accordion */
    .faq-accordion {
        max-width: 900px;
        margin: 0 auto;
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

    /* Contact Section */
    .contact-section {
        padding: 2rem clamp(1rem, 3vw, 3rem);
        background: #f9fafb;
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

    @media (max-width: 768px) {
        .faq-body-section,
        .contact-section {
            padding: 1.5rem 1rem;
        }

        .search-box input {
            width: 100%;
            margin-bottom: 1rem;
            {{ app()->getLocale() == 'ar' ? 'padding-right' : 'padding-left' }}: 45px;
        }

        .search-box .bi-search {
            {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 15px;
        }

        .search-box .filter-btn {
            width: 100%;
            margin-left: 0;
            margin-right: 0;
        }

        .category-tabs {
            flex-direction: column;
            gap: 0;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
        }

        .category-tab {
            width: 100%;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
            border-radius: 0;
            padding: 12px;
        }

        .category-tab:last-child {
            border-bottom: none;
        }

        .category-tab.active {
            background-color: var(--main-color);
            color: white !important;
            border-bottom-color: var(--main-color);
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
            <span>{{ __('FAQ') }}</span>
        </div>
        <h2>{{ __('Frequently Asked Questions') }}</h2>
        <p>
            {{ __('Do you have questions? We are here to provide you with clear and comprehensive answers about everything related to our training programs.') }}
        </p>
    </section>

    <!-- FAQ Body Section -->
    <section class="faq-body-section">
        <!-- Search Box -->
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="{{ __('Search for your question here...') }}" />
            <button class="filter-btn">{{ __('Filter') }}</button>
        </div>

        <!-- Category Tabs -->
        <div class="category-tabs">
            <button class="category-tab active">{{ __('All') }}</button>
            <button class="category-tab">{{ __('Courses and Paths') }}</button>
            <button class="category-tab">{{ __('Registration and Payment') }}</button>
            <button class="category-tab">{{ __('Certificates and Accreditation') }}</button>
            <button class="category-tab">{{ __('Technical Support and Help') }}</button>
        </div>

        <!-- FAQ Accordion -->
        <div class="accordion faq-accordion" id="faqAccordion">
            @php
            $faqs = [
                ['q' => __('How do I register at the institute?'), 'a' => __('You can easily register via Nafath account or create an internal account. After registration, you can choose the academic path or short courses that suit you.')],
                ['q' => __('What payment methods are available?'), 'a' => __('We provide several payment methods including: credit cards, Mada, bank transfer, and payment upon registration. We also offer installment options for long paths.')],
                ['q' => __('Are the certificates accredited?'), 'a' => __('Yes, all our certificates are accredited by the Technical and Vocational Training Corporation and recognized in the Saudi labor market.')],
                ['q' => __('What is the term system?'), 'a' => __('The term system is a system of dividing the training path into specific time periods (quarters), each lasting a certain period, which helps organize the training process and periodically evaluate the trainee\'s progress.')],
                ['q' => __('Can I switch from one path to another?'), 'a' => __('Yes, you can switch between paths according to specific academic conditions and after reviewing the completed training hours and coordinating with the academic administration.')],
                ['q' => __('How do I get technical support?'), 'a' => __('The technical support team is available around the clock to answer your inquiries and solve any technical problems you may encounter. You can contact us via phone, email, or ticket system.')],
                ['q' => __('Is there remote training?'), 'a' => __('Yes, we provide remote training options for most of our courses and training paths, with all educational materials and resources available electronically.')],
                ['q' => __('What is the duration of a short course?'), 'a' => __('Short courses range from two weeks to 8 weeks depending on the nature and content of the course.')],
            ];
            @endphp

            @foreach($faqs as $index => $faq)
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $index }}">
                        {{ $faq['q'] }}
                    </button>
                </h2>
                <div id="faq{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ $faq['a'] }}
                    </div>
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
