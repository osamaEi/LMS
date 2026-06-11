@php use Illuminate\Support\Facades\Storage; @endphp
@extends('layouts.front')

@section('title', __('Al-Ertiqaa High Institute for Training'))

@section('styles')
/* ── Hero Slider ── */
.hero-section { position: relative; height: 65vh; min-height: 480px; overflow: hidden; }
.hero-slides { position: absolute; inset: 0; }
.hero-slide {
    position: absolute; inset: 0;
    background-size: cover; background-position: center 20%;
    opacity: 0;
    transition: opacity 1.3s ease-in-out, transform 8s ease;
    transform: scale(1.02);
}
.hero-slide.active { opacity: 1; transform: scale(1); }
.hero-slide::after {
    content: ''; position: absolute; inset: 0;
    background: linear-gradient(90deg, rgba(0,0,0,.38) 0%, rgba(0,0,0,.15) 40%, transparent 70%);
}
[dir="rtl"] .hero-slide::after {
    background: linear-gradient(270deg, rgba(0,0,0,.38) 0%, rgba(0,0,0,.15) 40%, transparent 70%);
}
.hero-vline {
    position: absolute; top: 15%; bottom: 15%;
    right: 42%; width: 1px;
    background: linear-gradient(to bottom, transparent, rgba(255,255,255,.12) 30%, rgba(255,255,255,.12) 70%, transparent);
    z-index: 1;
}
[dir="rtl"] .hero-vline { right: auto; left: 42%; }
@media(max-width:991px){ .hero-vline{display:none;} }

.hero-content {
    position: relative; z-index: 2; height: 100%;
    display: flex; flex-direction: column; justify-content: center;
    padding: 0 clamp(1.5rem, 6vw, 8rem);
    max-width: 760px;
    animation: heroFadeUp 1s ease .15s both;
}
@keyframes heroFadeUp {
    from { opacity:0; transform:translateY(35px); }
    to   { opacity:1; transform:translateY(0); }
}
.hero-tag {
    display: inline-flex; align-items: center; gap: .5rem;
    background: rgba(255,255,255,.13); backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,.22); border-radius: 50px;
    padding: .38rem 1.1rem; color: rgba(255,255,255,.95); font-size: .8rem;
    margin-bottom: 1rem; width: fit-content;
}
.hero-tag .tag-dot { width:7px; height:7px; border-radius:50%; background:#4ade80; flex-shrink:0; animation:pulse-green 2s infinite; }
@keyframes pulse-green { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.45;transform:scale(.8)} }
.hero-accent-line {
    width: 52px; height: 3px;
    background: linear-gradient(to right, #7dd3fc, rgba(125,211,252,.25));
    border-radius: 3px; margin-bottom: 1.2rem;
}
[dir="rtl"] .hero-accent-line { background: linear-gradient(to left, #7dd3fc, rgba(125,211,252,.25)); }
.hero-content h1 { color: #fff; font-size: clamp(2rem, 4.5vw, 3.6rem); font-weight: 800; line-height: 1.3; margin-bottom: 1.2rem; }
[dir="ltr"] .hero-content h1 { font-size: clamp(1.7rem, 3.5vw, 2.8rem); line-height: 1.25; letter-spacing: -.5px; }
.hero-content h1 span { color: #7dd3fc; }
.hero-content p { color: rgba(255,255,255,.86); font-size: clamp(.9rem, 1.7vw, 1.1rem); margin-bottom: 2rem; line-height: 1.9; max-width: 580px; }
[dir="ltr"] .hero-content p { font-size: clamp(.85rem, 1.5vw, 1rem); max-width: 520px; }
.hero-btns { display: flex; gap: 1rem; flex-wrap: wrap; }
.hero-full-btn {
    background: var(--main-color); color: #fff; padding: .65rem 1.75rem;
    border-radius: 8px; border: none; font-size: 1rem; transition: all .3s; cursor: pointer;
}
.hero-full-btn:hover { background: #005a8a; transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,113,170,.4); color: #fff; }
.hero-notfull-btn {
    background: transparent; color: #fff; padding: .65rem 1.75rem;
    border-radius: 8px; border: 2px solid rgba(255,255,255,.65);
    font-size: 1rem; transition: all .3s; cursor: pointer;
}
.hero-notfull-btn:hover { background: rgba(255,255,255,.13); border-color: #fff; color:#fff; }

.hero-dots {
    position: absolute; bottom: 2rem; z-index: 3;
    left: 50%; transform: translateX(-50%);
    display: flex; gap: .5rem;
}
[dir="rtl"] .hero-dots { left: auto; right: 50%; transform: translateX(50%); }
.hero-dot { width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,.4); border: none; cursor: pointer; transition: all .3s; }
.hero-dot.active { background: #fff; width: 32px; border-radius: 5px; }
.hero-scroll-hint {
    position: absolute; bottom: 2rem; right: 2.5rem; z-index: 3;
    display: flex; flex-direction: column; align-items: center; gap: .4rem;
    color: rgba(255,255,255,.5); font-size: .72rem;
    animation: bounce 2s infinite;
}
[dir="rtl"] .hero-scroll-hint { right: auto; left: 2.5rem; }
@keyframes bounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(7px); } }

/* ── Stats Bar ── */
.stats-bar { background: var(--main-color); padding: 2.5rem clamp(1rem,4vw,4rem); }
.stats-bar .stats-inner { display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap; gap: 1.5rem 0; }
.stats-bar .stat-item { text-align: center; color: #fff; flex: 1 1 0; min-width: 140px; position: relative; }
.stats-bar .stat-item:not(:last-child)::after { content: ''; position: absolute; top: 50%; right: 0; transform: translateY(-50%); width: 1px; height: 50px; background: rgba(255,255,255,.3); }
[dir="rtl"] .stats-bar .stat-item:not(:last-child)::after { right: auto; left: 0; }
.stats-bar .stat-number { font-size: clamp(2rem,3.5vw,3rem); font-weight: 800; display: block; line-height: 1.1; }
.stats-bar .stat-label { font-size: .9rem; opacity: .85; margin-top: .3rem; display: block; }
@media (max-width: 576px) { .stats-bar .stat-item { min-width: 45%; } .stats-bar .stat-item:not(:last-child)::after { display: none; } }

/* ── Section Heads ── */
.section-head { text-align: center; margin-bottom: 3rem; }
.section-head h2 { font-size: clamp(1.6rem,3vw,2.4rem); font-weight: 800; margin-bottom: .75rem; }
.section-head p { color: #555; max-width: 650px; margin: 0 auto; line-height: 1.8; }

/* ── Why Cards ── */
.why-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); }
.why-card { border: none; border-radius: 16px; background: linear-gradient(160deg, #fff 0%, rgba(82,154,255,.12) 100%); padding: 1.75rem 1.25rem; height: 100%; transition: transform .3s, box-shadow .3s; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
.why-card:hover { transform: translateY(-6px); box-shadow: 0 12px 28px rgba(0,113,170,.15); }
.why-icon { width: 64px; height: 64px; border-radius: 14px; background: linear-gradient(135deg, var(--main-color), #0099d6); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; }
.why-icon i { color: #fff; font-size: 1.6rem; }
.why-card h5 { font-weight: 700; margin-bottom: .5rem; }
.why-card p { color: #666; font-size: .9rem; margin: 0; }

/* ── Programs ── */
.programs-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); background: var(--second-color); }
.courses-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap: 1.5rem; max-width: 1300px; margin: 0 auto; }
.course-card { background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,.07); transition: all .3s; }
.course-card:hover { transform: translateY(-5px); box-shadow: 0 12px 28px rgba(0,0,0,.12); }
.course-card img { width: 100%; height: 200px; object-fit: cover; object-position: center 20%; }
.course-card-body { padding: 1.25rem; }
.course-card h4 { font-weight: 700; margin-bottom: .5rem; font-size: 1.1rem; }
.course-card p { color: #666; font-size: .9rem; flex-grow: 1; }
.marks { display: flex; gap: .5rem; flex-wrap: wrap; margin: .75rem 0; }
.marks span { padding: .2rem .6rem; border-radius: 6px; font-size: .78rem; font-weight: 600; white-space: nowrap; }
.marks .st { background: #f3f4f6; border: 1px solid #d1d5db; color: #374151; }
.marks .nd { background: #eff8ff; border: 1px solid #b2ddff; color: var(--main-color); }
.marks .th { background: #ecfdf3; border: 1px solid #abefb6; color: #085d3a; }
.course-btns { display: flex; gap: .75rem; margin-top: 1rem; }
.course-btns a { flex: 1; text-align: center; padding: .5rem; border-radius: 8px; font-size: .9rem; font-weight: 600; transition: all .3s; }
.course-btns .btn-primary-course { background: var(--main-color); color: #fff; }
.course-btns .btn-primary-course:hover { background: #005a8a; }
.course-btns .btn-outline-course { border: 1.5px solid var(--main-color); color: var(--main-color); }
.course-btns .btn-outline-course:hover { background: #eaf5fb; }

/* ── Gallery ── */
.gallery-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); }
.gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; max-width: 1300px; margin: 0 auto; }
.gallery-item { border-radius: 12px; overflow: hidden; position: relative; cursor: pointer; }
.gallery-item img { width: 100%; height: 100%; object-fit: cover; object-position: center 20%; transition: transform .4s ease; display: block; }
.gallery-item:hover img { transform: scale(1.05); }
.gallery-item .overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,60,100,.6) 0%, transparent 60%); opacity: 0; transition: .3s; }
.gallery-item:hover .overlay { opacity: 1; }
.gallery-item-1 { grid-column: span 2; grid-row: span 2; height: 420px; }
.gallery-item-2, .gallery-item-3, .gallery-item-4, .gallery-item-5 { height: 204px; }

/* ── How It Works ── */
.how-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); background: var(--second-color); }
.how-image { border-radius: 20px; overflow: hidden; box-shadow: 0 12px 40px rgba(0,0,0,.12); }
.how-image img { width: 100%; height: 100%; object-fit: cover; object-position: center 20%; }
.how-steps { display: flex; flex-direction: column; gap: 1.75rem; }
.how-step { display: flex; gap: 1.25rem; align-items: flex-start; }
.how-step-number { flex-shrink: 0; width: 48px; height: 48px; border-radius: 12px; background: var(--main-color); color: #fff; font-size: 1.2rem; font-weight: 800; display: flex; align-items: center; justify-content: center; }
.how-step-text h5 { font-weight: 700; margin-bottom: .3rem; }
.how-step-text p { color: #666; font-size: .9rem; margin: 0; line-height: 1.7; }

/* ── Testimonials ── */
.testimonials-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); }
.testimonial-card { background: #fff; border-radius: 16px; padding: 1.75rem; box-shadow: 0 4px 20px rgba(0,0,0,.08); height: 100%; border-top: 4px solid var(--main-color); display: flex; flex-direction: column; gap: 1rem; }
.testimonial-stars { color: #f59e0b; }
.testimonial-text { color: #444; line-height: 1.8; font-size: .95rem; flex: 1; }
.testimonial-author { display: flex; align-items: center; gap: .75rem; border-top: 1px solid #f0f0f0; padding-top: 1rem; }
.testimonial-author .name { font-weight: 700; font-size: .9rem; }
.testimonial-author .role { font-size: .8rem; color: #888; }

/* ── Partners ── */
.partners-section { padding: clamp(4rem,7vw,6rem) 0; background: #fff; position: relative; overflow: hidden; }
.partners-section::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, var(--main-color), #38bdf8, var(--main-color), transparent); }
.partners-head { text-align: center; padding: 0 1rem; margin-bottom: 3rem; }
.partners-head h2 { font-size: clamp(1.6rem,3vw,2.3rem); font-weight: 800; color: #0f172a; margin-bottom: .5rem; }
.partners-head p { color: #64748b; font-size: .92rem; margin: 0; }
.partners-count-row { display: flex; align-items: center; justify-content: center; gap: 1.25rem; margin-top: 1.5rem; flex-wrap: wrap; }
.p-count-chip { display: inline-flex; align-items: center; gap: .45rem; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; padding: .4rem 1rem; color: #64748b; font-size: .78rem; }
.p-count-chip strong { color: var(--main-color); font-size: .92rem; font-weight: 800; }
.partners-grid-wrap { overflow: hidden; padding: 1rem 0; position: relative; }
.partners-grid-wrap::before,
.partners-grid-wrap::after { content:''; position:absolute; top:0; bottom:0; width:100px; z-index:2; pointer-events:none; }
.partners-grid-wrap::before { right:0; background:linear-gradient(to left, #fff 40%, transparent); }
.partners-grid-wrap::after  { left:0;  background:linear-gradient(to right, #fff 40%, transparent); }
.partners-track { display:flex; gap:1.75rem; width:max-content; animation:marquee-ltr 20s linear infinite; }
.partners-track:hover { animation-play-state:paused; }
@keyframes marquee-ltr { 0%{transform:translateX(-50%)} 100%{transform:translateX(0)} }
.p-logo-card { display:flex; flex-direction:column; align-items:center; justify-content:center; width:220px; min-height:130px; flex-shrink:0; background:transparent; border-radius:0; border:none; box-shadow:none; padding:16px 24px; gap:10px; transition:all .3s ease; cursor:default; }
.p-logo-card:hover { transform:translateY(-4px); }
.p-logo-card img { max-width:190px; max-height:100px; object-fit:contain; filter:grayscale(15%) opacity(.8); transition:filter .3s; }
.p-logo-card:hover img { filter:grayscale(0%) opacity(1); }
.p-logo-card .p-name { font-size:.82rem; font-weight:700; color:#475569; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:190px; text-align:center; display:block; }
.p-logo-card:hover .p-name { color:var(--main-color); }

/* ── App Section ── */
.app-section { background: linear-gradient(135deg, #004e7e 0%, var(--main-color) 100%); padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); color: #fff; }
.app-section h2 { font-size: clamp(1.6rem,3vw,2.4rem); font-weight: 800; margin-bottom: 1rem; }
.app-section p { opacity: .88; line-height: 1.8; max-width: 500px; }
.store-buttons { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1.75rem; }
.store-btn { display: flex; align-items: center; gap: .75rem; background: rgba(255,255,255,.12); backdrop-filter: blur(10px); color: #fff; padding: .8rem 1.25rem; border-radius: 12px; border: 1.5px solid rgba(255,255,255,.25); transition: all .3s; min-width: 160px; }
.store-btn:hover { background: rgba(255,255,255,.22); transform: translateY(-3px); color: #fff; }
.store-btn .text { display: flex; flex-direction: column; line-height: 1.2; }
.store-btn .text small { font-size: .7rem; opacity: .8; }
.store-btn .text span { font-size: 1rem; font-weight: 700; }

/* ── Responsive ── */
@media (max-width: 991px) {
    .gallery-grid { grid-template-columns: repeat(2, 1fr); }
    .gallery-item-1 { grid-column: span 2; height: 280px; }
}
@media (max-width: 768px) {
    .hero-section { height: 55vh; min-height: 400px; }
    .hero-content { max-width: 100%; padding: 0 1.5rem; }
    .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
    .gallery-item-1 { height: 220px; }
    .gallery-item-2, .gallery-item-3, .gallery-item-4, .gallery-item-5 { height: 160px; }
    .how-image { height: 260px; }
}
@media (max-width: 480px) { .hero-content h1 { font-size: 1.75rem; } }
@endsection

@section('content')
@php
    $lms3s = fn(string $n) => asset('lms3/' . rawurlencode('حين يلتقي التدريب مع الإبداع 3') . '/' . $n);
    $lms3f = fn(string $n) => asset('lms3/' . rawurlencode($n));
@endphp

@include('front.partials.home.hero')
@include('front.partials.home.stats')
@include('front.partials.home.why')
@include('front.partials.home.programs')
@include('front.partials.home.gallery')
@include('front.partials.home.how')
@include('front.partials.home.testimonials')
@include('front.partials.home.app-download')
@include('front.partials.home.partners')

@endsection

@section('scripts')
<script>
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slide');
const dots   = document.querySelectorAll('.hero-dot');

function goToSlide(n) {
    slides[currentSlide].classList.remove('active');
    dots[currentSlide].classList.remove('active');
    currentSlide = (n + slides.length) % slides.length;
    slides[currentSlide].classList.add('active');
    dots[currentSlide].classList.add('active');
}

setInterval(() => goToSlide(currentSlide + 1), 5000);
</script>
@endsection
