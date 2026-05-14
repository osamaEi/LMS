@extends('layouts.front')

@section('title', $page->title . ' - ' . __('Al-Ertiqaa High Institute for Training'))

@section('styles')
<style>
/* ── Hero ── */
.page-hero {
    background: linear-gradient(135deg, #00304d 0%, #0071aa 100%);
    padding: 3rem clamp(1rem,4vw,4rem);
    color: #fff;
}
.page-hero .breadcrumb-nav { margin-bottom: 1rem; }
.page-hero .breadcrumb-nav a { color: rgba(255,255,255,.7); text-decoration: none; }
.page-hero .breadcrumb-nav a:hover { color: #fff; }
.page-hero .breadcrumb-nav span { color: rgba(255,255,255,.4); margin: 0 .4rem; }
.page-hero h1 { font-size: clamp(1.6rem,3vw,2.4rem); font-weight: 800; margin: 0 0 .75rem; }
.page-meta {
    display: flex; gap: 1.25rem; flex-wrap: wrap;
    font-size: .82rem; color: rgba(255,255,255,.65);
}
.page-meta i { margin-left: .3rem; }

/* ── Layout ── */
.page-wrap {
    max-width: 1100px;
    margin: 0 auto;
    padding: 3rem clamp(1rem,4vw,3rem) 4rem;
    display: grid;
    grid-template-columns: 1fr 260px;
    gap: 3rem;
    align-items: start;
}

/* ── Content ── */
.page-content {
    min-width: 0;
    line-height: 1.9;
    color: #374151;
    font-size: 1rem;
}
.page-content h2 {
    font-size: 1.5rem; font-weight: 800; color: #111827;
    margin: 0 0 1.5rem; padding-bottom: .75rem;
    border-bottom: 3px solid #0071aa;
}
.page-content h3 {
    font-size: 1.05rem; font-weight: 700; color: #0071aa;
    margin: 2.5rem 0 .75rem;
    display: flex; align-items: center; gap: .6rem;
}
.page-content h3::before {
    content: '';
    display: inline-block;
    width: 4px; height: 1.1em;
    background: #0071aa;
    border-radius: 2px;
    flex-shrink: 0;
}
.page-content p { margin-bottom: 1.1rem; }
.page-content ul {
    margin: .75rem 0 1.25rem;
    padding: 0;
    list-style: none;
}
.page-content ul li {
    position: relative;
    padding-right: 1.25rem;
    margin-bottom: .55rem;
    color: #4b5563;
}
[dir="ltr"] .page-content ul li { padding-right: 0; padding-left: 1.25rem; }
.page-content ul li::before {
    content: '';
    position: absolute;
    right: 0; top: .6em;
    width: 6px; height: 6px;
    border-radius: 50%;
    background: #0071aa;
}
[dir="ltr"] .page-content ul li::before { right: auto; left: 0; }

/* ── TOC Sidebar ── */
.page-toc {
    position: sticky;
    top: 80px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 1.25rem;
    font-size: .85rem;
}
.page-toc .toc-title {
    font-weight: 700; color: #111827;
    margin-bottom: 1rem;
    font-size: .9rem;
    display: flex; align-items: center; gap: .5rem;
}
.page-toc .toc-title i { color: #0071aa; }
.page-toc a {
    display: block;
    color: #6b7280;
    text-decoration: none;
    padding: .35rem .6rem;
    border-radius: 7px;
    margin-bottom: .2rem;
    line-height: 1.45;
    transition: all .15s;
    border-right: 2px solid transparent;
}
[dir="ltr"] .page-toc a { border-right: none; border-left: 2px solid transparent; }
.page-toc a:hover, .page-toc a.active {
    color: #0071aa;
    background: #e0f2fe;
    border-right-color: #0071aa;
}
[dir="ltr"] .page-toc a:hover, [dir="ltr"] .page-toc a.active {
    border-left-color: #0071aa;
    border-right-color: transparent;
}

@media (max-width: 768px) {
    .page-wrap { grid-template-columns: 1fr; }
    .page-toc { position: static; order: -1; }
}
</style>
@endsection

@section('content')

{{-- Hero --}}
<div class="page-hero">
    <div class="breadcrumb-nav">
        <a href="{{ route('home') }}">{{ __('Home') }}</a>
        <span>›</span>
        <span>{{ $page->title }}</span>
    </div>
    <h1>{{ $page->title }}</h1>
    <div class="page-meta">
        <span><i class="bi bi-calendar3"></i>{{ $page->published_at?->translatedFormat('d M Y') ?? $page->created_at->translatedFormat('d M Y') }}</span>
        <span><i class="bi bi-arrow-repeat"></i>{{ __('Version') }} {{ $page->version }}</span>
    </div>
</div>

{{-- Body --}}
<div class="page-wrap">

    {{-- Main content --}}
    <article class="page-content" id="page-content">
        {!! $page->content !!}
    </article>

    {{-- TOC sidebar (auto-built from h3 headings) --}}
    <aside class="page-toc" id="page-toc">
        <div class="toc-title"><i class="bi bi-list-ul"></i> المحتويات</div>
        <nav id="toc-nav"></nav>
    </aside>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const headings = document.querySelectorAll('#page-content h3');
    const nav      = document.getElementById('toc-nav');
    if (!headings.length || !nav) return;

    headings.forEach(function (h, i) {
        const id = 'section-' + i;
        h.id = id;
        const a = document.createElement('a');
        a.href = '#' + id;
        a.textContent = h.textContent.trim();
        a.addEventListener('click', function (e) {
            e.preventDefault();
            h.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
        nav.appendChild(a);
    });

    // Highlight active section on scroll
    const links = nav.querySelectorAll('a');
    const obs = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                links.forEach(function (l) { l.classList.remove('active'); });
                const active = nav.querySelector('a[href="#' + entry.target.id + '"]');
                if (active) active.classList.add('active');
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    headings.forEach(function (h) { obs.observe(h); });
});
</script>

@endsection
