@extends('layouts.front')

@section('title', $page->title . ' - ' . __('Al-Ertiqaa High Institute for Training'))

@section('styles')
<style>
.page-hero { background: linear-gradient(135deg, #00304d 0%, #0071aa 100%); padding: 3rem clamp(1rem,4vw,4rem); color: #fff; }
.page-hero .breadcrumb-nav { margin-bottom: 1rem; }
.page-hero .breadcrumb-nav a { color: rgba(255,255,255,.75); }
.page-hero .breadcrumb-nav span { color: rgba(255,255,255,.5); margin: 0 .4rem; }
.page-hero h1 { font-size: clamp(1.6rem,3vw,2.4rem); font-weight: 800; margin: 0; }
.page-body-content {
    max-width: 880px; margin: 3rem auto; padding: 0 clamp(1rem,4vw,4rem);
    line-height: 1.9; color: #374151; font-size: 1rem;
}
.page-body-content h2, .page-body-content h3 { font-weight: 700; color: #111827; margin: 2rem 0 .75rem; }
.page-body-content p { margin-bottom: 1.25rem; }
.page-body-content ul, .page-body-content ol { margin: 1rem 0 1.25rem 1.5rem; }
.page-body-content li { margin-bottom: .4rem; }
.page-meta { display: flex; gap: 1rem; align-items: center; font-size: .82rem; color: rgba(255,255,255,.7); margin-top: .75rem; flex-wrap: wrap; }
</style>
@endsection

@section('content')
<div class="page-hero">
    <div class="breadcrumb-nav">
        <a href="{{ route('home') }}">{{ __('Home') }}</a>
        <span>›</span>
        <span>{{ $page->title }}</span>
    </div>
    <h1>{{ $page->title }}</h1>
    <div class="page-meta">
        <span><i class="bi bi-calendar3"></i> {{ $page->published_at?->translatedFormat('d M Y') ?? $page->created_at->translatedFormat('d M Y') }}</span>
        <span><i class="bi bi-arrow-repeat"></i> {{ __('Version') }} {{ $page->version }}</span>
    </div>
</div>

<div class="page-body-content">
    {!! $page->content !!}
</div>
@endsection
