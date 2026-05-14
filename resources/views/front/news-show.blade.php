@extends('layouts.front')

@section('title', $news->title . ' - ' . __('Al-Ertiqaa High Institute for Training'))

@section('styles')
<style>
.news-show-hero {
    background: linear-gradient(135deg, #0071AA 0%, #005a88 55%, #003a6b 100%);
    padding: clamp(3rem,6vw,5rem) clamp(2rem,6vw,6rem);
    position: relative; overflow: hidden;
}
.news-show-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 80% 60% at 110% 50%, rgba(255,255,255,.06) 0%, transparent 70%);
    pointer-events: none;
}
.news-show-hero .inner { position: relative; z-index: 1; max-width: 820px; }
.news-show-hero .navi {
    display: flex; align-items: center; gap: .5rem;
    font-size: .85rem; color: rgba(255,255,255,.6); margin-bottom: 1.2rem;
}
.news-show-hero .navi a { color: rgba(255,255,255,.65); text-decoration: none; }
.news-show-hero .navi a:hover { color: #fff; }
.news-show-hero h1 {
    color: #fff; font-size: clamp(1.6rem,4vw,2.6rem);
    font-weight: 900; margin: 0 0 1rem; line-height: 1.35;
}
.news-meta {
    display: flex; gap: 1.25rem; flex-wrap: wrap;
    font-size: .84rem; color: rgba(255,255,255,.7);
}
.news-meta i { margin-left: .3rem; }

.news-show-body {
    max-width: 820px;
    margin: 0 auto;
    padding: 3rem clamp(1rem,4vw,3rem);
}

.news-cover {
    width: 100%; border-radius: 16px;
    overflow: hidden; margin-bottom: 2.5rem;
    box-shadow: 0 4px 24px rgba(0,0,0,.1);
    max-height: 480px;
}
.news-cover img {
    width: 100%; height: 100%;
    object-fit: cover; display: block;
}

.news-content {
    line-height: 1.9; color: #374151; font-size: 1.05rem;
}
.news-content h2, .news-content h3 { font-weight: 700; color: #111827; margin: 2rem 0 .75rem; }
.news-content p { margin-bottom: 1.25rem; }
.news-content ul, .news-content ol { margin: 1rem 0 1.25rem 1.5rem; }
.news-content li { margin-bottom: .4rem; }
.news-content img { max-width: 100%; border-radius: 10px; margin: 1rem 0; }

.news-share {
    display: flex; align-items: center; gap: .75rem;
    padding: 1.5rem 0;
    border-top: 1px solid #e5e7eb;
    margin-top: 2rem;
    flex-wrap: wrap;
}
.news-share span { font-weight: 700; color: #374151; font-size: .9rem; }
.share-btn {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .45rem 1rem; border-radius: 8px;
    font-size: .82rem; font-weight: 600; text-decoration: none;
    transition: opacity .2s;
}
.share-btn:hover { opacity: .85; }
.share-btn.tw  { background: #000;   color: #fff; }
.share-btn.wa  { background: #25d366; color: #fff; }
.share-btn.cp  { background: #e5e7eb; color: #374151; cursor: pointer; border: none; }

/* Related */
.related-section {
    background: #f8fafc;
    padding: 3rem clamp(2rem,6vw,6rem);
    border-top: 1px solid #e5e7eb;
}
.related-section h3 {
    font-size: 1.3rem; font-weight: 800; color: #111827; margin-bottom: 1.5rem;
}
.related-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1.25rem;
}
.related-card {
    background: #fff; border-radius: 14px;
    border: 1px solid #e5e7eb;
    overflow: hidden; text-decoration: none; color: inherit;
    transition: transform .25s, box-shadow .25s;
    display: flex; flex-direction: column;
}
.related-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,113,170,.1); }
.related-card img {
    width: 100%; height: 160px; object-fit: cover; display: block;
}
.related-card .rc-body { padding: 1rem; flex: 1; }
.related-card .rc-body h4 { font-size: .95rem; font-weight: 700; color: #111827; margin-bottom: .4rem; line-height: 1.4; }
.related-card .rc-body span { font-size: .78rem; color: #9ca3af; }

@media(max-width:640px) {
    .news-show-body { padding: 2rem 1rem; }
    .related-section { padding: 2rem 1rem; }
}
</style>
@endsection

@section('content')

@php
$isEn  = app()->isLocale('en');
$title = $isEn ? ($news->title_en ?: $news->title_ar) : $news->title_ar;
$body  = $isEn ? ($news->body_en  ?: $news->body_ar)  : $news->body_ar;
$date  = $news->published_at ?? $news->created_at;
$pageUrl = url()->current();
@endphp

{{-- Hero --}}
<section class="news-show-hero">
    <div class="inner">
        <div class="navi">
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
            <span>/</span>
            <a href="{{ route('news') }}">{{ __('News') }}</a>
            <span>/</span>
            <span style="color:rgba(255,255,255,.9);">{{ Str::limit($title, 50) }}</span>
        </div>
        <h1>{{ $title }}</h1>
        <div class="news-meta">
            <span><i class="bi bi-calendar3"></i>{{ $date->translatedFormat('d M Y') }}</span>
            <span><i class="bi bi-clock"></i>{{ $date->diffForHumans() }}</span>
        </div>
    </div>
</section>

{{-- Body --}}
<div class="news-show-body">

    @if($news->image)
    <div class="news-cover">
        <img src="{{ asset('storage/' . $news->image) }}" alt="{{ $title }}" loading="lazy">
    </div>
    @endif

    <div class="news-content">
        {!! $body !!}
    </div>

    {{-- Share --}}
    <div class="news-share">
        <span>{{ __('Share') }}:</span>
        <a href="https://twitter.com/intent/tweet?url={{ urlencode($pageUrl) }}&text={{ urlencode($title) }}"
           target="_blank" rel="noopener" class="share-btn tw">
            <i class="bi bi-twitter-x"></i> X
        </a>
        <a href="https://wa.me/?text={{ urlencode($title . ' ' . $pageUrl) }}"
           target="_blank" rel="noopener" class="share-btn wa">
            <i class="bi bi-whatsapp"></i> واتساب
        </a>
        <button class="share-btn cp" onclick="navigator.clipboard.writeText('{{ $pageUrl }}'); this.textContent='تم النسخ ✓'">
            <i class="bi bi-link-45deg"></i> نسخ الرابط
        </button>
    </div>

</div>

{{-- Related --}}
@if($related->count())
<section class="related-section">
    <div style="max-width:820px;margin:0 auto;">
        <h3>{{ __('Related News') }}</h3>
        <div class="related-grid">
            @foreach($related as $item)
            @php
                $rTitle = $isEn ? ($item->title_en ?: $item->title_ar) : $item->title_ar;
                $rDate  = $item->published_at ?? $item->created_at;
            @endphp
            <a href="{{ route('news.show', $item) }}" class="related-card">
                @if($item->image)
                <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $rTitle }}" loading="lazy">
                @else
                <div style="height:160px;background:linear-gradient(135deg,#0071aa,#005a88);"></div>
                @endif
                <div class="rc-body">
                    <h4>{{ $rTitle }}</h4>
                    <span><i class="bi bi-calendar3"></i> {{ $rDate->translatedFormat('d M Y') }}</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
