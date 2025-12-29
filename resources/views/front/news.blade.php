@extends('layouts.front')

@section('title', __('News') . ' - ' . __('Al-Ertiqaa High Institute for Training'))

@section('styles')
<style>
    /* News Section */
    .news-section {
        padding: 2rem clamp(3rem, 7vw, 7rem);
    }

    .news-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .news-card {
        background: white;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        padding: 10px;
    }

    .news-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }

    .news-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 14px;
    }

    .news-card .card-body {
        padding: 1.25rem;
    }

    .news-card .card-date {
        color: rgba(108, 115, 127, 1);
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }

    .news-card .card-title {
        font-weight: bold;
        margin-bottom: 0.75rem;
        line-height: 1.4;
    }

    .news-card .card-text {
        color: rgba(56, 66, 80, 1);
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 1rem;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 3rem;
        padding: 1rem;
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

    .pagination-btn:hover:not(:disabled) {
        border-color: var(--main-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .pagination-btn.active {
        background-color: var(--main-color);
        color: white;
        border-color: var(--main-color);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .pagination-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    .pagination-arrow {
        background: transparent;
    }

    @media (max-width: 768px) {
        .news-section {
            padding: 1.5rem 1rem;
        }

        .news-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (min-width: 769px) and (max-width: 1199px) {
        .news-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (min-width: 1920px) {
        .news-card {
            max-width: 450px;
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
            <span>{{ __('News') }}</span>
        </div>
        <h2>{{ __('News and Events') }}</h2>
        <p>
            {{ __('Follow the latest updates, official announcements, and events at the institute.') }}
        </p>
    </section>

    <!-- News Section -->
    <section class="news-section container-fluid">
        <div class="news-grid">
            @php
            $newsItems = [
                ['date' => __('October') . ' 1, 2025', 'title' => __('Important Announcement - Registration Opens for New Training Program'), 'desc' => __('The institute announces the opening of registration for the Digital Skills Training Program for the current semester.')],
                ['date' => __('September') . ' 25, 2025', 'title' => __('Advanced Web Development Workshop'), 'desc' => __('Join us in a comprehensive workshop to learn the latest modern web application development technologies.')],
                ['date' => __('September') . ' 20, 2025', 'title' => __('Annual Technology Conference'), 'desc' => __('We are pleased to invite you to attend the Annual Technology and Innovation Conference in Digital Education.')],
                ['date' => __('September') . ' 15, 2025', 'title' => __('Cybersecurity Training Course'), 'desc' => __('Learn the fundamentals and techniques of cybersecurity to protect data and digital systems.')],
                ['date' => __('September') . ' 10, 2025', 'title' => __('Artificial Intelligence Training Program'), 'desc' => __('Discover the world of artificial intelligence and machine learning in our specialized training program.')],
                ['date' => __('September') . ' 5, 2025', 'title' => __('Mobile Application Development Seminar'), 'desc' => __('Join us in a seminar on best practices for smartphone application development.')],
                ['date' => __('September') . ' 1, 2025', 'title' => __('User Experience Design Course'), 'desc' => __('Learn the principles and foundations of user interface design and improving user experience.')],
                ['date' => __('August') . ' 28, 2025', 'title' => __('Launch of New E-Learning Platform'), 'desc' => __('We announce the launch of our new e-learning platform with advanced features.')],
                ['date' => __('August') . ' 25, 2025', 'title' => __('New Partnership with Technology Companies'), 'desc' => __('We are pleased to announce new partnerships with major technology companies to provide job opportunities for graduates.')],
            ];
            @endphp

            @foreach($newsItems as $news)
            <div class="news-card">
                <img src="{{ asset('images/card.png') }}" alt="{{ $news['title'] }}" onerror="this.src='{{ asset('images/course.jpg') }}'" />
                <div class="card-body">
                    <p class="card-date">{{ $news['date'] }}</p>
                    <h5 class="card-title">{{ $news['title'] }}</h5>
                    <p class="card-text">{{ $news['desc'] }}</p>
                    <button class="full-btn">{{ __('View Details') }}</button>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            <button class="pagination-btn pagination-arrow" disabled>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="{{ app()->getLocale() == 'ar' ? '9 18 15 12 9 6' : '15 18 9 12 15 6' }}"></polyline>
                </svg>
            </button>

            <button class="pagination-btn active">1</button>
            <button class="pagination-btn">2</button>
            <button class="pagination-btn">3</button>
            <button class="pagination-btn">...</button>
            <button class="pagination-btn">10</button>

            <button class="pagination-btn pagination-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="{{ app()->getLocale() == 'ar' ? '15 18 9 12 15 6' : '9 18 15 12 9 6' }}"></polyline>
                </svg>
            </button>
        </div>
    </section>
@endsection
