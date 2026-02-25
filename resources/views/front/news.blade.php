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
        @if($news->isEmpty())
            <div class="text-center py-16 text-gray-400">
                <p style="font-size:1.1rem;">{{ __('No news available at the moment.') }}</p>
            </div>
        @else
        <div class="news-grid">
            @foreach($news as $item)
            <div class="news-card">
                @if($item->image)
                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title_ar }}" onerror="this.src='{{ asset('images/card.png') }}'" />
                @else
                    <img src="{{ asset('images/card.png') }}" alt="{{ $item->title_ar }}" onerror="this.src='{{ asset('images/course.jpg') }}'" />
                @endif
                <div class="card-body">
                    <p class="card-date">{{ $item->published_at ? $item->published_at->format('Y-m-d') : $item->created_at->format('Y-m-d') }}</p>
                    <h5 class="card-title">{{ app()->getLocale() === 'en' ? ($item->title_en ?: $item->title_ar) : $item->title_ar }}</h5>
                    <p class="card-text">{{ Str::limit(app()->getLocale() === 'en' ? ($item->body_en ?: $item->body_ar) : $item->body_ar, 120) }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Laravel Pagination --}}
        @if($news->hasPages())
        <div class="pagination-wrapper">
            {{ $news->links() }}
        </div>
        @endif
        @endif
    </section>
@endsection
