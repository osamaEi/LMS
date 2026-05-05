@extends('layouts.front')

@section('title', __('News') . ' - ' . __('Al-Ertiqaa High Institute for Training'))

@section('styles')
<style>
    /* ── Shared ── */
    :root { --news-accent: var(--main-color, #0071AA); }

    /* ── Hero ── */
    .news-hero {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 55%, #003a6b 100%);
        padding: clamp(4rem,8vw,7rem) clamp(2rem,6vw,6rem) clamp(3rem,6vw,5rem);
        position: relative; overflow: hidden;
    }
    .news-hero::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse 80% 60% at 110% 50%, rgba(255,255,255,.06) 0%, transparent 70%);
        pointer-events: none;
    }
    .news-hero-inner { position: relative; z-index: 1; max-width: 700px; }
    .news-hero .navi {
        display: flex; align-items: center; gap: .5rem;
        font-size: .85rem; color: rgba(255,255,255,.6); margin-bottom: 1.2rem;
    }
    .news-hero .navi a { color: rgba(255,255,255,.6); text-decoration: none; }
    .news-hero .navi a:hover { color: #fff; }
    .news-hero h1 {
        color: #fff; font-size: clamp(2rem,5vw,3.2rem);
        font-weight: 900; margin: 0 0 1rem; line-height: 1.2;
    }
    .news-hero p { color: rgba(255,255,255,.75); font-size: 1.05rem; line-height: 1.8; margin: 0; }
    .news-hero-stat {
        display: inline-flex; align-items: center; gap: .5rem;
        background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
        color: #fff; border-radius: 30px; padding: .4rem 1rem;
        font-size: .82rem; font-weight: 600; margin-top: 1.5rem;
    }
    .news-hero-stat span { width: 7px; height: 7px; border-radius: 50%; background: #4ade80; display: inline-block; animation: blink 1.5s infinite; }
    @keyframes blink { 0%,100%{opacity:1} 50%{opacity:.3} }

    /* ── Page wrap ── */
    .news-wrap { padding: clamp(2.5rem,5vw,5rem) clamp(2rem,6vw,6rem); }

    /* ══════════════════════════════════════
       TOP BLOCK  — featured + secondary
    ══════════════════════════════════════ */
    .news-top {
        display: grid;
        grid-template-columns: 1.6fr 1fr;
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    /* Featured */
    .card-featured {
        position: relative; border-radius: 20px; overflow: hidden;
        box-shadow: 0 4px 30px rgba(0,0,0,.1);
        min-height: 480px; display: flex; flex-direction: column;
    }
    .card-featured img {
        position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover;
        transition: transform .6s ease;
    }
    .card-featured:hover img { transform: scale(1.04); }
    .card-featured-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(to top, rgba(0,20,50,.88) 0%, rgba(0,20,50,.3) 55%, transparent 100%);
    }
    .card-featured-body {
        position: relative; z-index: 1; margin-top: auto; padding: 2rem;
    }
    .news-tag {
        display: inline-block; padding: .3rem .85rem; border-radius: 20px;
        font-size: .72rem; font-weight: 700; letter-spacing: .5px;
        background: var(--news-accent); color: #fff; margin-bottom: .9rem;
    }
    .card-featured-body h2 {
        color: #fff; font-size: 1.55rem; font-weight: 800; line-height: 1.4;
        margin: 0 0 .8rem;
    }
    .card-featured-body .meta {
        display: flex; align-items: center; gap: 1rem;
        color: rgba(255,255,255,.65); font-size: .82rem;
    }
    .card-featured-body .meta i { margin-left: .3rem; }
    .card-featured-read {
        display: inline-flex; align-items: center; gap: .4rem;
        margin-top: 1.1rem; color: #fff; font-weight: 700; font-size: .88rem;
        text-decoration: none;
        background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.3);
        padding: .5rem 1.1rem; border-radius: 8px; backdrop-filter: blur(4px);
        transition: background .2s;
    }
    .card-featured-read:hover { background: rgba(255,255,255,.25); color: #fff; }

    /* Secondary stack */
    .news-secondary { display: flex; flex-direction: column; gap: 1.5rem; }
    .card-secondary {
        background: #fff; border-radius: 16px; overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,.06);
        display: flex; flex-direction: column; flex: 1;
        transition: transform .25s, box-shadow .25s;
    }
    .card-secondary:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,113,170,.12); }
    .card-secondary-img { height: 160px; overflow: hidden; flex-shrink: 0; }
    .card-secondary-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
    .card-secondary:hover .card-secondary-img img { transform: scale(1.06); }
    .card-secondary-body { padding: 1.1rem 1.2rem; display: flex; flex-direction: column; flex: 1; }
    .card-secondary-body .meta { color: #94a3b8; font-size: .78rem; margin-bottom: .5rem; }
    .card-secondary-body h3 {
        font-size: .95rem; font-weight: 700; color: #1e293b; line-height: 1.5;
        margin: 0 0 .5rem;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .card-secondary-link {
        color: var(--news-accent); font-size: .8rem; font-weight: 600;
        text-decoration: none; display: inline-flex; align-items: center; gap: .3rem;
        margin-top: auto;
    }
    .card-secondary-link:hover { text-decoration: underline; }

    /* ══════════════════════════════════════
       DIVIDER
    ══════════════════════════════════════ */
    .news-divider {
        display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;
    }
    .news-divider h2 { font-size: 1.25rem; font-weight: 800; color: #1e293b; margin: 0; white-space: nowrap; }
    .news-divider::after { content: ''; flex: 1; height: 2px; background: linear-gradient(to left, transparent, #e2e8f0); }
    .news-divider .dot { width: 9px; height: 9px; border-radius: 50%; background: var(--news-accent); flex-shrink: 0; }

    /* ══════════════════════════════════════
       REGULAR GRID
    ══════════════════════════════════════ */
    .news-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem; }

    .news-card {
        background: #fff; border-radius: 16px; overflow: hidden;
        box-shadow: 0 2px 14px rgba(0,0,0,.06);
        display: flex; flex-direction: column;
        transition: transform .25s, box-shadow .25s;
    }
    .news-card:hover { transform: translateY(-5px); box-shadow: 0 12px 32px rgba(0,113,170,.13); }
    .news-card-img { height: 200px; overflow: hidden; flex-shrink: 0; position: relative; }
    .news-card-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s; }
    .news-card:hover .news-card-img img { transform: scale(1.06); }
    .news-card-body { padding: 1.3rem; display: flex; flex-direction: column; flex: 1; }
    .news-card-meta {
        display: flex; align-items: center; gap: .8rem;
        color: #94a3b8; font-size: .78rem; margin-bottom: .8rem;
    }
    .news-card-meta .sep { width: 3px; height: 3px; border-radius: 50%; background: #cbd5e1; }
    .news-card-title {
        font-weight: 700; font-size: .97rem; line-height: 1.55; color: #1e293b;
        margin-bottom: .6rem;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .news-card-text {
        color: #64748b; font-size: .87rem; line-height: 1.7;
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
        flex: 1; margin-bottom: 1rem;
    }
    .news-card-footer {
        display: flex; align-items: center; justify-content: space-between;
        border-top: 1px solid #f1f5f9; padding-top: .85rem; margin-top: auto;
    }
    .news-card-link {
        color: var(--news-accent); font-size: .83rem; font-weight: 600;
        text-decoration: none; display: inline-flex; align-items: center; gap: .3rem;
        transition: gap .2s;
    }
    .news-card-link:hover { gap: .55rem; }
    .news-card-ago { font-size: .75rem; color: #b0bec5; }

    /* ── Pagination ── */
    .news-pager { display: flex; justify-content: center; gap: .4rem; margin-top: 3rem; flex-wrap: wrap; }
    .news-pager a, .news-pager span {
        min-width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center;
        border: 1.5px solid #e2e8f0; background: #fff; color: #64748b;
        border-radius: 10px; font-size: .88rem; font-weight: 600; padding: 0 .8rem;
        text-decoration: none; transition: all .2s;
    }
    .news-pager a:hover { border-color: var(--news-accent); color: var(--news-accent); }
    .news-pager .active { background: var(--news-accent); border-color: var(--news-accent); color: #fff; box-shadow: 0 4px 12px rgba(0,113,170,.25); }

    /* ── Empty ── */
    .news-empty { text-align: center; padding: 6rem 2rem; color: #94a3b8; }
    .news-empty svg { margin-bottom: 1.5rem; opacity: .4; }
    .news-empty h3 { font-size: 1.4rem; font-weight: 700; margin-bottom: .5rem; color: #64748b; }

    /* ── Responsive ── */
    @media (max-width:1100px) { .news-top { grid-template-columns: 1fr; } .card-featured { min-height: 380px; } }
    @media (max-width:991px)  { .news-grid { grid-template-columns: repeat(2,1fr); } }
    @media (max-width:600px)  {
        .news-grid { grid-template-columns: 1fr; }
        .news-wrap { padding: 1.5rem 1rem; }
        .news-hero  { padding: 3rem 1.2rem; }
        .news-secondary { flex-direction: row; overflow-x: auto; }
        .card-secondary { min-width: 260px; }
    }
</style>
@endsection

@section('content')

{{-- ── Hero ── --}}
<section class="news-hero">
    <div class="news-hero-inner">
        <div class="navi">
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
            <span>›</span>
            <span>{{ __('News') }}</span>
        </div>
        <h1>{{ __('News and Events') }}</h1>
        <p>{{ __('Follow the latest updates, official announcements, and events at the institute.') }}</p>
        @if(!isset($news) || !$news->isEmpty())
        <div class="news-hero-stat">
            <span></span>
            {{ $news->total() ?? 0 }} {{ __('news') }}
        </div>
        @endif
    </div>
</section>

<div class="news-wrap">

@if($news->isEmpty())
<div class="news-empty">
    <svg width="72" height="72" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
    <h3>{{ __('No news available at the moment.') }}</h3>
    <p>{{ __('Check back later for updates.') }}</p>
</div>

@else

@php
    $fb   = ['lms-photos/1.png','lms-photos/3.png','lms-photos/5.png','lms-photos/7.png','lms-photos/8.png','lms-photos/10.png'];
    $isEn = app()->getLocale() === 'en';
    $all  = $news->collect();
    $feat = $all->first();
    $secs = $all->slice(1, 2);
    $rest = $all->slice(3);

    function newsImg($item, $fb, $idx) {
        return $item->image ? \Illuminate\Support\Facades\Storage::url($item->image) : asset($fb[$idx % count($fb)]);
    }
@endphp

{{-- ── Top block ── --}}
@if($feat)
<div class="news-top">

    {{-- Featured --}}
    <a href="{{ route('news.show', $feat) }}" class="card-featured" style="text-decoration:none;">
        <img src="{{ newsImg($feat, $fb, 0) }}" alt="{{ $feat->title_ar }}" loading="lazy">
        <div class="card-featured-overlay"></div>
        <div class="card-featured-body">
            <span class="news-tag">{{ __('Latest') }}</span>
            <h2>{{ $isEn ? ($feat->title_en ?: $feat->title_ar) : $feat->title_ar }}</h2>
            <div class="meta">
                <span><i class="bi bi-calendar3"></i>{{ $feat->published_at ? $feat->published_at->translatedFormat('d M Y') : $feat->created_at->translatedFormat('d M Y') }}</span>
                <span><i class="bi bi-clock"></i>{{ $feat->published_at ? $feat->published_at->diffForHumans() : $feat->created_at->diffForHumans() }}</span>
            </div>
            <span class="card-featured-read">
                {{ __('Read More') }}
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            </span>
        </div>
    </a>

    {{-- Secondary --}}
    @if($secs->count())
    <div class="news-secondary">
        @foreach($secs as $si => $sec)
        <a href="{{ route('news.show', $sec) }}" class="card-secondary" style="text-decoration:none;">
            <div class="card-secondary-img">
                <img src="{{ newsImg($sec, $fb, $si+1) }}" alt="{{ $sec->title_ar }}" loading="lazy">
            </div>
            <div class="card-secondary-body">
                <div class="meta">
                    <i class="bi bi-calendar3" style="margin-left:.3rem;"></i>
                    {{ $sec->published_at ? $sec->published_at->translatedFormat('d M Y') : $sec->created_at->translatedFormat('d M Y') }}
                </div>
                <h3>{{ $isEn ? ($sec->title_en ?: $sec->title_ar) : $sec->title_ar }}</h3>
                <span class="card-secondary-link">
                    {{ __('Read More') }}
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </span>
            </div>
        </a>
        @endforeach
    </div>
    @endif

</div>
@endif

{{-- ── Regular grid ── --}}
@if($rest->count())
<div class="news-divider">
    <div class="dot"></div>
    <h2>{{ __('All News') }}</h2>
</div>

<div class="news-grid">
    @foreach($rest as $ri => $item)
    <div class="news-card">
        <div class="news-card-img">
            <img src="{{ newsImg($item, $fb, $ri+3) }}" alt="{{ $item->title_ar }}" loading="lazy">
        </div>
        <div class="news-card-body">
            <div class="news-card-meta">
                <span>{{ $item->published_at ? $item->published_at->translatedFormat('d M Y') : $item->created_at->translatedFormat('d M Y') }}</span>
            </div>
            <h3 class="news-card-title">{{ $isEn ? ($item->title_en ?: $item->title_ar) : $item->title_ar }}</h3>
            <p class="news-card-text">{{ Str::limit($isEn ? ($item->body_en ?: $item->body_ar) : $item->body_ar, 120) }}</p>
            <div class="news-card-footer">
                <a href="{{ route('news.show', $item) }}" class="news-card-link">
                    {{ __('Read More') }}
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                </a>
                <span class="news-card-ago">{{ $item->published_at ? $item->published_at->diffForHumans() : $item->created_at->diffForHumans() }}</span>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ── Pagination ── --}}
@if($news->hasPages())
<div class="news-pager">{{ $news->links() }}</div>
@endif

@endif
</div>
@endsection
