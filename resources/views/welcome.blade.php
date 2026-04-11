<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Al-Ertiqaa High Institute for Training') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('images/Vector.png') }}" />
    <!-- Preload first hero image -->
    <link rel="preload" as="image" href="{{ asset('lms2-photo/14.webp') }}" />

    @if(app()->getLocale() == 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('/fonts/Cairo-VariableFont_slnt,wght.ttf') format('truetype');
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }
        :root {
            --main-color: rgba(0, 113, 170, 1);
            --main-dark: #005a8a;
            --second-color: rgba(243, 244, 246, 1);
        }

        /* Font families */
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important; }
        [lang="ar"] * { font-family: 'Cairo', sans-serif !important; }
        .fa, .fas, .far, .fab { font-family: "Font Awesome 6 Free" !important; font-weight: 900; }

        body { font-size: 16px; font-weight: 400; line-height: 1.5; overflow-x: hidden; }
        a { text-decoration: none; color: inherit; }

        /* ── Saudi Top Bar ── */
        .saudi-top-bar { background:#fff; color:#1a1a1a; font-size:.78rem; padding:.35rem clamp(1rem,3vw,3rem); display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; border-bottom:2px solid #006c35; }
        .saudi-top-bar .saudi-left { display:flex; align-items:center; gap:1.25rem; flex-wrap:wrap; }
        .saudi-top-bar .saudi-right { display:flex; align-items:center; gap:1rem; }
        .saudi-emblem { display:flex; align-items:center; gap:.5rem; font-weight:700; font-size:.82rem; }
        .saudi-divider { width:1px; height:18px; background:rgba(0,0,0,.15); }
        .saudi-badge { display:flex; align-items:center; gap:.4rem; background:rgba(0,108,53,.07); border:1px solid rgba(0,108,53,.25); border-radius:4px; padding:2px 8px; font-size:.72rem; }
        .saudi-badge .dot { width:6px; height:6px; border-radius:50%; background:#16a34a; flex-shrink:0; animation:pulse-green 2s infinite; }
        @keyframes pulse-green { 0%,100%{opacity:1} 50%{opacity:.4} }
        .vision-badge { display:flex; align-items:center; gap:.4rem; font-weight:700; font-size:.75rem; }
        .vision-badge .v-year { background:#006c35; color:#fff; border-radius:3px; padding:1px 6px; font-weight:800; }
        .saudi-date-time { font-size:.72rem; color:#555; white-space:nowrap; }
        .accessibility-btns { display:flex; gap:.4rem; }
        .accessibility-btns button { background:rgba(0,0,0,.06); border:1px solid rgba(0,0,0,.12); color:#333; width:24px; height:24px; border-radius:4px; font-size:.7rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .2s; padding:0; }
        .accessibility-btns button:hover { background:rgba(0,108,53,.12); color:#006c35; }
        @media(max-width:768px){ .saudi-top-bar{font-size:.7rem;padding:.3rem .75rem;} .vision-badge,.saudi-date-time,.accessibility-btns{display:none;} }

        /* ── Navbar ── */
        nav.navbar { margin-top: 0 !important; padding-top: 0 !important; }
        .bottom-bar { padding: 0.75rem 1rem; border-bottom: 1px solid #e5e7eb; }
        .navbar-brand img { max-width: 120px; height: auto; }
        .nav-item a { color: black !important; padding: 5px 10px; border-radius: 20px; transition: all .3s; }
        .nav-item a:hover, .nav-item a.active { color: var(--main-color) !important; background: #eaf5fb; }
        .navbar-nav { gap: .5rem; }
        .btn-outline {
            white-space: nowrap; padding: .375rem .75rem; font-size: .9rem;
            border: 1px solid #dee2e6; background: transparent; border-radius: 5px; transition: all .3s;
        }
        .btn-outline:hover { background: #f8f9fa; }

        /* ── Mobile Menu ── */
        .mobile-menu-overlay { display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,.5); z-index:9998; }
        .mobile-menu-overlay.active { display:block; }
        .mobile-menu {
            display: none; position: fixed; top: 0; left: 0;
            width: 320px; height: 100vh; background: #fff; z-index: 9999;
            overflow-y: auto; box-shadow: 5px 0 15px rgba(0,0,0,.3); flex-direction: column;
        }
        [dir="rtl"] .mobile-menu { left: auto; right: 0; box-shadow: -5px 0 15px rgba(0,0,0,.3); }
        .mobile-menu.active { display: flex; }
        .mobile-menu-header { display:flex; justify-content:space-between; align-items:center; padding:15px; border-bottom:1px solid #e5e7eb; background:#f9fafb; }
        .mobile-menu-header .navbar-brand img { max-width:100px; }
        .close-menu-btn { width:40px; height:40px; background:#f0f0f0; border:none; font-size:1.25rem; color:#333; cursor:pointer; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:all .3s; }
        .close-menu-btn:hover { background:var(--main-color); color:#fff; }
        .mobile-nav-list { list-style:none; padding:0; margin:0; flex:1; }
        .mobile-nav-list li { border-bottom:1px solid #f0f0f0; }
        .mobile-nav-list li a { display:block; padding:15px 20px; color:#333; transition:all .3s; }
        .mobile-nav-list li a:hover, .mobile-nav-list li a.active { color:var(--main-color); background:#eaf5fb; }
        .mobile-menu-buttons { padding:15px; display:flex; flex-direction:column; gap:10px; border-top:1px solid #e5e7eb; }
        .mobile-menu-buttons .btn-outline { width:100%; text-align:center; }
        body.menu-open { overflow:hidden; }

        /* ── Hero Slider ── */
        .hero-section { position: relative; height: 100vh; min-height: 600px; overflow: hidden; }
        .hero-slides { position: absolute; inset: 0; }
        .hero-slide {
            position: absolute; inset: 0;
            background-size: cover; background-position: center;
            opacity: 0; transition: opacity 1s ease-in-out;
        }
        .hero-slide.active { opacity: 1; }
        .hero-slide::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(90deg, rgba(0,40,70,.85) 0%, rgba(0,80,130,.6) 45%, rgba(0,0,0,.15) 100%);
        }
        [dir="rtl"] .hero-slide::after {
            background: linear-gradient(270deg, rgba(0,40,70,.85) 0%, rgba(0,80,130,.6) 45%, rgba(0,0,0,.15) 100%);
        }
        .hero-content {
            position: relative; z-index: 2; height: 100%;
            display: flex; flex-direction: column; justify-content: center;
            padding: 0 clamp(1.5rem, 6vw, 8rem);
            max-width: 700px;
        }
        .hero-content h1 { color: #fff; font-size: clamp(2rem, 4.5vw, 3.5rem); font-weight: 800; line-height: 1.25; margin-bottom: 1.25rem; }
        .hero-content h1 span { color: #7dd3fc; }
        .hero-content p { color: rgba(255,255,255,.88); font-size: clamp(.95rem, 1.8vw, 1.15rem); margin-bottom: 2rem; line-height: 1.8; }
        .hero-btns { display: flex; gap: 1rem; flex-wrap: wrap; }

        .full-btn {
            background: var(--main-color); color: #fff; padding: .65rem 1.75rem;
            border-radius: 8px; border: none; font-size: 1rem; transition: all .3s; cursor: pointer;
        }
        .full-btn:hover { background: var(--main-dark); transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,113,170,.4); color: #fff; }
        .notfull-btn {
            background: transparent; color: #fff; padding: .65rem 1.75rem;
            border-radius: 8px; border: 2px solid rgba(255,255,255,.7);
            font-size: 1rem; transition: all .3s; cursor: pointer;
        }
        .notfull-btn:hover { background: rgba(255,255,255,.15); border-color: #fff; }

        .hero-dots {
            position: absolute; bottom: 2rem; z-index: 3;
            left: 50%; transform: translateX(-50%);
            display: flex; gap: .5rem;
        }
        [dir="rtl"] .hero-dots { left: auto; right: 50%; transform: translateX(50%); }
        .hero-dot { width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,.45); border: none; cursor: pointer; transition: all .3s; }
        .hero-dot.active { background: #fff; width: 28px; border-radius: 5px; }

        .hero-scroll-hint {
            position: absolute; bottom: 2.5rem; right: 2.5rem; z-index: 3;
            display: flex; flex-direction: column; align-items: center; gap: .4rem;
            color: rgba(255,255,255,.6); font-size: .75rem;
            animation: bounce 2s infinite;
        }
        [dir="rtl"] .hero-scroll-hint { right: auto; left: 2.5rem; }
        @keyframes bounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(6px); } }

        /* ── Stats Bar ── */
        .stats-bar { background: var(--main-color); padding: 2.5rem clamp(1rem,4vw,4rem); }
        .stats-bar .stats-inner {
            display: flex;
            justify-content: space-around;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem 0;
        }
        .stats-bar .stat-item {
            text-align: center;
            color: #fff;
            flex: 1 1 0;
            min-width: 140px;
            position: relative;
        }
        .stats-bar .stat-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%; right: 0;
            transform: translateY(-50%);
            width: 1px; height: 50px;
            background: rgba(255,255,255,.3);
        }
        [dir="rtl"] .stats-bar .stat-item:not(:last-child)::after { right: auto; left: 0; }
        .stats-bar .stat-number { font-size: clamp(2rem,3.5vw,3rem); font-weight: 800; display: block; line-height: 1.1; }
        .stats-bar .stat-label { font-size: .9rem; opacity: .85; margin-top: .3rem; display: block; }
        @media (max-width: 576px) {
            .stats-bar .stat-item { min-width: 45%; }
            .stats-bar .stat-item:not(:last-child)::after { display: none; }
        }

        /* ── Section Heads ── */
        .section-head { text-align: center; margin-bottom: 3rem; }
        .section-head .badge-tag { display: inline-block; background: #eaf5fb; color: var(--main-color); padding: .3rem 1rem; border-radius: 20px; font-size: .85rem; margin-bottom: .75rem; }
        .section-head h2 { font-size: clamp(1.6rem,3vw,2.4rem); font-weight: 800; margin-bottom: .75rem; }
        .section-head p { color: #555; max-width: 650px; margin: 0 auto; line-height: 1.8; }

        /* ── Why Cards ── */
        .why-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); }
        .why-card {
            border: none; border-radius: 16px;
            background: linear-gradient(160deg, #fff 0%, rgba(82,154,255,.12) 100%);
            padding: 1.75rem 1.25rem; height: 100%;
            transition: transform .3s, box-shadow .3s;
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
        }
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
        .course-card img { width: 100%; height: 200px; object-fit: cover; }
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
        .course-btns .btn-primary-course:hover { background: var(--main-dark); }
        .course-btns .btn-outline-course { border: 1.5px solid var(--main-color); color: var(--main-color); }
        .course-btns .btn-outline-course:hover { background: #eaf5fb; }

        /* ── Gallery ── */
        .gallery-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); }
        .gallery-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; max-width: 1300px; margin: 0 auto; }
        .gallery-item { border-radius: 12px; overflow: hidden; position: relative; cursor: pointer; }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s ease; display: block; }
        .gallery-item:hover img { transform: scale(1.05); }
        .gallery-item .overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,60,100,.6) 0%, transparent 60%); opacity: 0; transition: .3s; }
        .gallery-item:hover .overlay { opacity: 1; }
        .gallery-item-1 { grid-column: span 2; grid-row: span 2; height: 420px; }
        .gallery-item-2, .gallery-item-3, .gallery-item-4, .gallery-item-5 { height: 204px; }
        .gallery-item-6 { grid-column: span 2; height: 204px; }
        .gallery-item-7, .gallery-item-8 { height: 204px; }

        /* ── How It Works ── */
        .how-section { padding: clamp(3rem,6vw,6rem) clamp(1rem,4vw,4rem); background: var(--second-color); }
        .how-image { border-radius: 20px; overflow: hidden; box-shadow: 0 12px 40px rgba(0,0,0,.12); }
        .how-image img { width: 100%; height: 100%; object-fit: cover; }
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


        /* ── Footer ── */
        .foot { background: #1d3a52; color: #fff; padding: clamp(3rem,5vw,6rem) clamp(1rem,4vw,5rem); }
        .footer-logo img { max-width: 180px; margin-bottom: 1rem; }
        .footer-desc { opacity: .8; line-height: 1.8; font-size: .9rem; margin-bottom: 1.5rem; }
        .footer-social-wrap { display: flex; gap: .75rem; }
        .footer-social-wrap a { width: 38px; height: 38px; border-radius: 50%; background: rgba(255,255,255,.1); color: #fff; display: flex; align-items: center; justify-content: center; transition: all .3s; text-decoration: none; font-size: 1rem; }
        .footer-social-wrap a:hover { background: var(--main-color); color: #fff; }
        .footer-heading { font-weight: 700; margin-bottom: 1.25rem; font-size: 1rem; border-bottom: 2px solid rgba(255,255,255,.15); padding-bottom: .75rem; }
        .footer-links { display: flex; flex-direction: column; gap: .6rem; }
        .footer-links a { color: rgba(255,255,255,.75); font-size: .9rem; transition: all .3s; text-decoration: none; }
        .footer-links a:hover { color: #fff; padding-right: 6px; }
        [dir="ltr"] .footer-links a:hover { padding-right: 0; padding-left: 6px; }
        .footer-contact-item { display: flex; align-items: center; gap: .75rem; margin-bottom: .9rem; color: rgba(255,255,255,.8); font-size: .9rem; }
        .footer-contact-item i { color: var(--main-color); font-size: 1.1rem; flex-shrink: 0; }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,.15); margin-top: 3rem; padding-top: 1.5rem; display: flex; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
        .footer-bottom p { margin: 0; font-size: .85rem; opacity: .7; }

        /* ── Responsive ── */
        @media (max-width: 991px) {
            .gallery-grid { grid-template-columns: repeat(2, 1fr); }
            .gallery-item-1 { grid-column: span 2; height: 280px; }
            .gallery-item-6 { grid-column: span 2; }
            .stats-divider { display: none; }
        }
        @media (max-width: 768px) {
            .hero-section { height: 85vh; }
            .hero-content { max-width: 100%; padding: 0 1.5rem; }
            .gallery-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
            .gallery-item-1 { height: 220px; }
            .gallery-item-2, .gallery-item-3, .gallery-item-4, .gallery-item-5,
            .gallery-item-6, .gallery-item-7, .gallery-item-8 { height: 160px; }
            .how-image { height: 260px; }
            .footer-bottom { flex-direction: column; gap: .5rem; }
        }
        @media (max-width: 480px) {
            .hero-content h1 { font-size: 1.75rem; }
        }
    </style>
</head>
<body>

    <!-- Saudi National Identity Top Bar -->
    <div class="saudi-top-bar" role="banner">
        <div class="saudi-left">
            <div class="saudi-emblem">
                <svg width="22" height="26" viewBox="0 0 100 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M50 5 L95 30 L95 80 Q95 105 50 115 Q5 105 5 80 L5 30 Z" fill="rgba(255,255,255,0.15)" stroke="rgba(255,255,255,0.6)" stroke-width="3"/>
                    <text x="50" y="72" text-anchor="middle" fill="white" font-size="38" font-family="serif">🌴</text>
                </svg>
                <span>المملكة العربية السعودية</span>
            </div>
            <div class="saudi-divider"></div>
            <div class="saudi-badge">
                <span class="dot"></span>
                <span>موقع رسمي معتمد</span>
            </div>
            <div class="saudi-divider d-none d-md-block"></div>
            <div class="vision-badge">
                <span>رؤية</span>
                <span class="v-year">2030</span>
            </div>
        </div>
        <div class="saudi-right">
            <div class="saudi-date-time" id="saudiDateTime"></div>
            <div class="saudi-divider"></div>
            <div class="accessibility-btns">
                <button onclick="changeFontSize(1)" title="تكبير الخط">A+</button>
                <button onclick="changeFontSize(-1)" title="تصغير الخط">A-</button>
            </div>
        </div>
    </div>

    <!-- ════ Navbar ════ -->
    <nav class="navbar navbar-expand-lg d-flex flex-column shadow-sm sticky-top bg-white" style="z-index:1000">
        <div class="container-fluid bottom-bar d-flex align-items-center justify-content-between">
            <a class="navbar-brand" href="/"><img src="{{ asset('images/nav.png') }}" alt="Logo" /></a>
            <button class="navbar-toggler d-lg-none border" type="button" onclick="toggleMobileMenu()">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="d-none d-lg-flex align-items-center justify-content-between flex-grow-1">
                <ul class="navbar-nav d-flex flex-row mb-0 mx-4">
                    <li class="nav-item"><a href="{{ route('home') }}" class="active">{{ __('Home') }}</a></li>
                    <li class="nav-item"><a href="{{ route('short-courses') }}">{{ __('Short Courses') }}</a></li>
                    <li class="nav-item"><a href="{{ route('about') }}">{{ __('About Us') }}</a></li>
                    <li class="nav-item"><a href="{{ route('faq') }}">{{ __('FAQ') }}</a></li>
                    <li class="nav-item"><a href="{{ route('contact') }}">{{ __('Contact Us') }}</a></li>
                </ul>
                <div class="d-flex gap-2">
                    @if(app()->getLocale() == 'ar')
                        <a href="{{ route('lang.switch', 'en') }}" class="btn btn-outline"><i class="bi bi-translate"></i> English</a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}" class="btn btn-outline"><i class="bi bi-translate"></i> العربية</a>
                    @endif
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline"><i class="bi bi-grid"></i> {{ __('Dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="full-btn" style="font-size:.9rem"><i class="bi bi-person"></i> {{ __('Login') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <a class="navbar-brand" href="/"><img src="{{ asset('images/nav.png') }}" alt="Logo" /></a>
            <button class="close-menu-btn" onclick="closeMobileMenu()"><i class="bi bi-x-lg"></i></button>
        </div>
        <ul class="mobile-nav-list">
            <li><a href="{{ route('home') }}" class="active">{{ __('Home') }}</a></li>
            <li><a href="{{ route('short-courses') }}">{{ __('Short Courses') }}</a></li>
            <li><a href="{{ route('about') }}">{{ __('About Us') }}</a></li>
            <li><a href="{{ route('faq') }}">{{ __('FAQ') }}</a></li>
            <li><a href="{{ route('contact') }}">{{ __('Contact Us') }}</a></li>
        </ul>
        <div class="mobile-menu-buttons">
            @if(app()->getLocale() == 'ar')
                <a href="{{ route('lang.switch', 'en') }}" class="btn btn-outline"><i class="bi bi-translate"></i> English</a>
            @else
                <a href="{{ route('lang.switch', 'ar') }}" class="btn btn-outline"><i class="bi bi-translate"></i> العربية</a>
            @endif
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-outline"><i class="bi bi-grid"></i> {{ __('Dashboard') }}</a>
            @else
                <a href="{{ route('login') }}" class="full-btn text-center"><i class="bi bi-person"></i> {{ __('Login') }}</a>
            @endauth
        </div>
    </div>

    <!-- ════ Hero ════ -->
    <section class="hero-section">
        <div class="hero-slides">
            <div class="hero-slide active" style="background-image:url('{{ asset('lms2-photo/14.webp') }}')"></div>
            <div class="hero-slide"        style="background-image:url('{{ asset('lms2-photo/1.webp') }}')"></div>
            <div class="hero-slide"        style="background-image:url('{{ asset('lms2-photo/4.webp') }}')"></div>
            <div class="hero-slide"        style="background-image:url('{{ asset('lms2-photo/11.webp') }}')"></div>
        </div>
        <div class="hero-content">
            <h1>
                {{ __('Distinguished training opens doors to') }}
                <span>{{ __('tomorrow') }}</span>
            </h1>
            <p>{{ __('With over 10 years of experience, we make a real difference in the lives of individuals and organizations. We guide you with the training compass towards your specialization and profession with confidence, to be your first gateway to a future that keeps pace with Vision 2030 targets.') }}</p>
            <div class="hero-btns">
                <a href="{{ route('login') }}" class="full-btn">{{ __('Start Your Trial Journey') }}</a>
                <a href="{{ route('training-paths') }}" class="notfull-btn">{{ __('Explore Our Programs') }}</a>
            </div>
        </div>
        <div class="hero-dots" id="heroDots">
            <button class="hero-dot active" onclick="goToSlide(0)"></button>
            <button class="hero-dot"        onclick="goToSlide(1)"></button>
            <button class="hero-dot"        onclick="goToSlide(2)"></button>
            <button class="hero-dot"        onclick="goToSlide(3)"></button>
        </div>
        <div class="hero-scroll-hint">
            <span>{{ app()->getLocale()=='ar' ? 'اكتشف' : 'Scroll' }}</span>
            <i class="bi bi-chevron-double-down"></i>
        </div>
    </section>

    <!-- ════ Stats Bar ════ -->
    <section class="stats-bar">
        <div class="container">
            <div class="stats-inner">
                <div class="stat-item">
                    <span class="stat-number">10+</span>
                    <span class="stat-label">{{ __('Years of Excellence') }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <span class="stat-label">{{ __('Graduates') }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">20+</span>
                    <span class="stat-label">{{ __('Training Programs') }}</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">98%</span>
                    <span class="stat-label">{{ __('Trainee Satisfaction') }}</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ════ Why Choose Us ════ -->
    <section class="why-section">
        <div class="container">
            <div class="section-head">
                <div class="badge-tag">{{ __('Why Us') }}</div>
                <h2>{{ __('Why Choose Us') }}</h2>
                <p>{{ __('We offer an integrated training system that combines quality, flexibility, and modern technologies to ensure the best educational experience.') }}</p>
            </div>
            <div class="row g-4">
                @php
                $cards = [
                    ['icon'=>'bi-headset',           'title'=>__('Continuous Support'),      'text'=>__('24/7 technical support service helps you overcome any technical problem.')],
                    ['icon'=>'bi-person-badge',       'title'=>__('Specialized Trainers'),    'text'=>__('Training is conducted by elite certified trainers with academic and professional experience.')],
                    ['icon'=>'bi-laptop',             'title'=>__('Digital Education'),       'text'=>__('A smooth, secure educational experience compatible with trainees needs.')],
                    ['icon'=>'bi-patch-check',        'title'=>__('Accredited Training'),     'text'=>__('Accredited by official authorities within the Kingdom, ensuring a reliable path for developing your professional skills.')],
                    ['icon'=>'bi-award',              'title'=>__('Official Certificates'),   'text'=>__('After completing programs, trainees receive officially accredited certificates that enhance their career opportunities.')],
                    ['icon'=>'bi-credit-card',        'title'=>__('Multiple Payment Methods'),'text'=>__('We provide a flexible payment system that suits all trainees needs.')],
                    ['icon'=>'bi-play-btn',           'title'=>__('Interactive Content'),     'text'=>__('Video lessons, files, assessments, and tests that enhance understanding and support learning by practice.')],
                    ['icon'=>'bi-map',                'title'=>__('Clear Paths'),             'text'=>__('Educational plans built on clear paths extending up to 10 training quarters.')],
                ];
                @endphp
                @foreach($cards as $card)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="why-card">
                        <div class="why-icon"><i class="bi {{ $card['icon'] }}"></i></div>
                        <h5>{{ $card['title'] }}</h5>
                        <p>{{ $card['text'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ════ Training Programs ════ -->
    <section class="programs-section">
        <div class="container">
            <div class="section-head">
                <div class="badge-tag">{{ __('Programs') }}</div>
                <h2>{{ __('Comprehensive training paths to build your future') }}</h2>
                <p>{{ __('We provide training paths spanning two and a half years through 10 training quarters, plus short and specialized courses for various professional goals.') }}</p>
            </div>
            <div class="courses-grid">
                @php $programImages = ['lms-photos/2.webp','lms-photos/8.webp','lms-photos/5.webp']; @endphp
                @forelse($featuredPrograms ?? [] as $i => $program)
                <div class="course-card">
                    <img src="{{ asset($programImages[$i % count($programImages)]) }}" alt="{{ $program->name }}" />
                    <div class="course-card-body d-flex flex-column">
                        <h4>{{ $program->name }}</h4>
                        <p>{{ Str::limit($program->description ?? __('A comprehensive training program designed to develop professional skills.'), 100) }}</p>
                        <div class="marks">
                            @if($program->duration_months)
                            <span class="st"><i class="bi bi-clock"></i> {{ $program->duration_months }} {{ __('months') }}</span>
                            @endif
                            @if($program->price && $program->price > 0)
                            <span class="nd"><i class="bi bi-tag"></i> {{ number_format($program->price,0) }} {{ __('SAR') }}</span>
                            @else
                            <span class="nd"><i class="bi bi-check-circle"></i> {{ __('Free') }}</span>
                            @endif
                            <span class="th"><i class="bi bi-mortarboard"></i> {{ $program->code ?? __('Program') }}</span>
                        </div>
                        <div class="course-btns">
                            <a href="{{ route('training-paths') }}" class="btn-outline-course">{{ __('View Details') }}</a>
                            <a href="{{ auth()->check() ? route('student.my-program') : route('login') }}" class="btn-primary-course">{{ __('Register Now') }}</a>
                        </div>
                    </div>
                </div>
                @empty
                @foreach([
                    ['lms-photos/2.webp',  __('Computer Science Diploma'),    __('Foundations of computing, programming, networks, and databases.'),           12, 5000, 'CS-101'],
                    ['lms-photos/8.webp',  __('Business Administration'),     __('Modern management fundamentals: leadership, planning, and decision-making.'), 12, null,  'BA-201'],
                    ['lms-photos/5.webp',  __('Digital Marketing Diploma'),   __('SEO, social media, paid ads, and analytics strategies.'),                     10, 4500, 'DM-301'],
                ] as [$img,$name,$desc,$months,$price,$code])
                <div class="course-card">
                    <img src="{{ asset($img) }}" alt="{{ $name }}" />
                    <div class="course-card-body d-flex flex-column">
                        <h4>{{ $name }}</h4>
                        <p>{{ $desc }}</p>
                        <div class="marks">
                            <span class="st"><i class="bi bi-clock"></i> {{ $months }} {{ __('months') }}</span>
                            @if($price)
                            <span class="nd"><i class="bi bi-tag"></i> {{ number_format($price,0) }} {{ __('SAR') }}</span>
                            @else
                            <span class="nd"><i class="bi bi-check-circle"></i> {{ __('Free') }}</span>
                            @endif
                            <span class="th"><i class="bi bi-mortarboard"></i> {{ $code }}</span>
                        </div>
                        <div class="course-btns">
                            <a href="{{ route('training-paths') }}" class="btn-outline-course">{{ __('View Details') }}</a>
                            <a href="{{ route('login') }}" class="btn-primary-course">{{ __('Register Now') }}</a>
                        </div>
                    </div>
                </div>
                @endforeach
                @endforelse
            </div>
        </div>
    </section>

    <!-- ════ Gallery ════ -->
    <section class="gallery-section">
        <div class="container-fluid px-4">
            <div class="section-head">
                <div class="badge-tag">{{ __('Gallery') }}</div>
                <h2>{{ __('Institute Life') }}</h2>
                <p>{{ __('A glimpse into our training environment, graduation ceremonies, and daily student life at Al-Ertiqaa.') }}</p>
            </div>
            <div class="gallery-grid">
                <div class="gallery-item gallery-item-1">
                    <img loading="lazy" src="{{ asset('lms2-photo/2.webp') }}" alt="Institute Building" />
                    <div class="overlay"></div>
                </div>
                <div class="gallery-item gallery-item-2">
                    <img loading="lazy" src="{{ asset('lms2-photo/14.webp') }}" alt="Computer Lab" />
                    <div class="overlay"></div>
                </div>
                <div class="gallery-item gallery-item-3">
                    <img loading="lazy" src="{{ asset('lms2-photo/1.webp') }}" alt="Student" />
                    <div class="overlay"></div>
                </div>
                <div class="gallery-item gallery-item-4">
                    <img loading="lazy" src="{{ asset('lms2-photo/3.webp') }}" alt="Consultation" />
                    <div class="overlay"></div>
                </div>
                <div class="gallery-item gallery-item-5">
                    <img loading="lazy" src="{{ asset('lms2-photo/4.webp') }}" alt="Learning" />
                    <div class="overlay"></div>
                </div>
                <div class="gallery-item gallery-item-6">
                    <img loading="lazy" src="{{ asset('lms2-photo/5.webp') }}" alt="Graduation" />
                    <div class="overlay"></div>
                </div>
                <div class="gallery-item gallery-item-7">
                    <img loading="lazy" src="{{ asset('lms2-photo/9.webp') }}" alt="Award" />
                    <div class="overlay"></div>
                </div>
                <div class="gallery-item gallery-item-8">
                    <img loading="lazy" src="{{ asset('lms2-photo/11.webp') }}" alt="Discussion" />
                    <div class="overlay"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- ════ How It Works ════ -->
    <section class="how-section">
        <div class="container">
            <div class="section-head">
                <div class="badge-tag">{{ __('Process') }}</div>
                <h2>{{ __('How does our training system work?') }}</h2>
                <p>{{ __('An integrated training system that ensures a clear, organized educational journey with measurable results.') }}</p>
            </div>
            <div class="row align-items-center g-5">
                <div class="col-lg-6 {{ app()->getLocale()=='ar' ? 'order-2' : 'order-1' }}">
                    <div class="how-image" style="height:420px">
                        <img loading="lazy" src="{{ asset('lms2-photo/4.webp') }}" alt="How It Works" />
                    </div>
                </div>
                <div class="col-lg-6 {{ app()->getLocale()=='ar' ? 'order-1' : 'order-2' }}">
                    <div class="how-steps">
                        <div class="how-step">
                            <div class="how-step-number">1</div>
                            <div class="how-step-text">
                                <h5>{{ __('Registration and Getting Started') }}</h5>
                                <p>{{ __('Start your educational journey easily by creating an account or logging in through Nafath, then discover programs and paths designed to suit your goals.') }}</p>
                            </div>
                        </div>
                        <div class="how-step">
                            <div class="how-step-number">2</div>
                            <div class="how-step-text">
                                <h5>{{ __('Choosing the Right Program for You') }}</h5>
                                <p>{{ __('Whether you\'re looking for an academic path spanning two and a half years, or a short course, you will find what suits your goals.') }}</p>
                            </div>
                        </div>
                        <div class="how-step">
                            <div class="how-step-number">3</div>
                            <div class="how-step-text">
                                <h5>{{ __('Learning and Follow-up') }}</h5>
                                <p>{{ __('Study through visual and organized content, with an attendance system, clear training progress, and direct communication with trainers.') }}</p>
                            </div>
                        </div>
                        <div class="how-step">
                            <div class="how-step-number">4</div>
                            <div class="how-step-text">
                                <h5>{{ __('Assessment and Certification') }}</h5>
                                <p>{{ __('After completing your training requirements, you will be evaluated and your accredited digital certificate will be issued.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ════ Testimonials ════ -->
    <section class="testimonials-section">
        <div class="container">
            <div class="section-head">
                <div class="badge-tag">{{ __('Reviews') }}</div>
                <h2>{{ __('What Our Trainees Say') }}</h2>
                <p>{{ __('Real experiences from our graduates who made a difference in their careers.') }}</p>
            </div>
            <div class="row g-4">
                @php
                $testimonials = app()->getLocale() === 'ar' ? [
                    ['text'=>'رحلة تدريبية مميزة وواضحة من البداية للنهاية، متابعة التيرم والدروس كانت سهلة جدًا، والتطبيق ساعدني أتابع تقدمي بشكل يومي.', 'author'=>'سلمان .م', 'role'=>'دبلومة الحاسب وتقنية المعلومات'],
                    ['text'=>'تجربتي مع المعهد كانت رائعة، المدربون محترفون والمحتوى التعليمي ممتاز. أنصح الجميع بالتسجيل في برامجهم التدريبية.', 'author'=>'أحمد .ع', 'role'=>'دبلومة إدارة الأعمال'],
                    ['text'=>'الدعم الفني متميز والاستجابة سريعة. البرنامج التدريبي ساعدني على تطوير مهاراتي المهنية بشكل ملحوظ.', 'author'=>'نورة .س', 'role'=>'دبلومة التسويق الرقمي'],
                ] : [
                    ['text'=>'A distinctive training journey from start to finish. Following the term and lessons was very easy, and the app helped me track my daily progress.', 'author'=>'Salman M.', 'role'=>'Computer & IT Diploma'],
                    ['text'=>'My experience with the institute was wonderful. The trainers are professional and the content is excellent.', 'author'=>'Ahmed A.', 'role'=>'Business Administration Diploma'],
                    ['text'=>'Outstanding technical support and fast response. The training program helped me develop my professional skills noticeably.', 'author'=>'Noura S.', 'role'=>'Digital Marketing Diploma'],
                ];
                @endphp
                @foreach($testimonials as $t)
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="testimonial-stars">
                            <i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i><i class="bi bi-star-fill"></i>
                        </div>
                        <p class="testimonial-text">"{{ $t['text'] }}"</p>
                        <div class="testimonial-author">
                            <div style="width:48px;height:48px;border-radius:50%;background:var(--main-color);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:1.2rem;flex-shrink:0">
                                {{ mb_substr($t['author'],0,1) }}
                            </div>
                            <div>
                                <div class="name">{{ $t['author'] }}</div>
                                <div class="role">{{ $t['role'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ════ App Section ════ -->
    <section class="app-section">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <h2>{{ __('Download the App & Learn Anywhere') }}</h2>
                    <p>{{ __('Follow your courses, attend live sessions, and track your progress — all from your phone.') }}</p>
                    <div class="store-buttons">
                        <a href="#" class="store-btn">
                            <i class="bi bi-apple" style="font-size:1.8rem"></i>
                            <div class="text"><small>{{ __('Download on the') }}</small><span>App Store</span></div>
                        </a>
                        <a href="#" class="store-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="28" viewBox="0 0 21 24" fill="none">
                                <path d="M9.80482 11.4617L0.0895996 22.0059C0.389807 23.1574 1.41179 24 2.62539 24C3.11083 24 3.56616 23.8656 3.95671 23.6305L14.9229 17.1593L9.80482 11.4617Z" fill="#EA4335"/>
                                <path d="M19.6332 9.66424L14.9029 6.85928L9.58398 11.6994L14.922 17.1562L19.6177 14.3858C20.4407 13.9305 21.0001 13.0431 21.0001 12.0204C21.0001 11.0033 20.4489 10.1205 19.6332 9.66424Z" fill="#FBBC04"/>
                                <path d="M0.0894234 1.9952C0.0310244 2.21542 0 2.44683 0 2.68571V21.3182C0 21.5571 0.0310245 21.7885 0.0903359 22.0078L10.1386 11.7332L0.0894234 1.9952Z" fill="#4285F4"/>
                                <path d="M9.87666 12L14.9044 6.85945L3.98201 0.383598C3.58508 0.140054 3.12154 0 2.62606 0C1.41246 0 0.38865 0.84456 0.0902675 1.99043L9.87666 12Z" fill="#34A853"/>
                            </svg>
                            <div class="text"><small>{{ __('Get it on') }}</small><span>Google Play</span></div>
                        </a>
                        <a href="#" class="store-btn">
                            <img src="{{ asset('images/huawei-appgallery-thumb.png') }}" style="width:28px;border-radius:6px" onerror="this.style.display='none'" />
                            <div class="text"><small>{{ __('Explore it on') }}</small><span>AppGallery</span></div>
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img loading="lazy" src="{{ asset('lms2-photo/4.webp') }}" alt="App" style="max-width:100%;border-radius:20px;box-shadow:0 20px 60px rgba(0,0,0,.3);max-height:360px;object-fit:cover" />
                </div>
            </div>
        </div>
    </section>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- ════ Footer ════ -->
    <footer class="foot">
        <div class="container-fluid">
            <div class="row g-5">
                <div class="col-lg-4 col-md-12">
                    <div class="footer-logo">
                        <img src="{{ asset('images/footlogooo.png') }}" alt="Logo" onerror="this.style.display='none'" />
                    </div>
                    <p class="footer-desc">{{ __('An accredited training institute offering professional development programs and educational paths for over 10 years, aligned with Saudi Vision 2030.') }}</p>
                    <div class="footer-social-wrap">
                        <a href="#"><i class="bi bi-youtube"></i></a>
                        <a href="#"><i class="bi bi-twitter-x"></i></a>
                        <a href="#"><i class="bi bi-linkedin"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="footer-heading">{{ __('Quick Links') }}</div>
                    <div class="footer-links">
                        <a href="{{ route('home') }}">{{ __('Home') }}</a>
                        <a href="{{ route('about') }}">{{ __('About Us') }}</a>
                        <a href="{{ route('training-paths') }}">{{ __('Training Paths') }}</a>
                        <a href="{{ route('short-courses') }}">{{ __('Short Courses') }}</a>
                        <a href="{{ route('news') }}">{{ __('News') }}</a>
                        <a href="{{ route('contact') }}">{{ __('Contact Us') }}</a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-4 col-6">
                    <div class="footer-heading">{{ __('Services') }}</div>
                    <div class="footer-links">
                        <a href="{{ route('training-paths') }}">{{ __('Term System Programs') }}</a>
                        <a href="{{ route('short-courses') }}">{{ __('Short Courses') }}</a>
                        <a href="{{ route('short-courses') }}">{{ __('Remote Training') }}</a>
                        <a href="{{ route('contact') }}">{{ __('Certificate Accreditation') }}</a>
                        <a href="{{ route('faq') }}">{{ __('Technical Support') }}</a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-4 col-12">
                    <div class="footer-heading">{{ __('Contact Information') }}</div>
                    <div class="footer-contact-item">
                        <i class="bi bi-telephone-fill"></i>
                        <span>9200343222</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-envelope-fill"></i>
                        <span>help@alertiqa.edu.sa</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>{{ __('Riyadh, Saudi Arabia') }}</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="bi bi-clock-fill"></i>
                        <span>{{ __('Sun – Thu: 8:00 AM – 5:00 PM') }}</span>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© {{ date('Y') }} {{ __('Al-Ertiqaa High Institute for Training') }}. {{ __('All rights reserved') }}.</p>
                <p>{{ __('Developed and maintained by Al-Ertiqaa') }}</p>
            </div>
        </div>
    </footer>

    <script>
        // ── Saudi Top Bar date/time ──
        function updateSaudiTime() {
            const el = document.getElementById('saudiDateTime');
            if (!el) return;
            const now = new Date();
            const days = ['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
            const months = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
            let h = now.getHours(), m = now.getMinutes().toString().padStart(2,'0');
            const period = h >= 12 ? 'م' : 'ص';
            h = h % 12 || 12;
            el.textContent = `${days[now.getDay()]} ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} — ${h}:${m} ${period}`;
        }
        updateSaudiTime();
        setInterval(updateSaudiTime, 30000);

        // Font size accessibility
        let currentFontSize = 100;
        function changeFontSize(dir) {
            currentFontSize = Math.min(130, Math.max(85, currentFontSize + dir * 5));
            document.documentElement.style.fontSize = currentFontSize + '%';
        }

        // ── Hero Slider ──
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

        const heroTimer = setInterval(() => goToSlide(currentSlide + 1), 5000);

        // ── Mobile Menu ──
        function toggleMobileMenu() {
            document.getElementById('mobileMenu').classList.add('active');
            document.getElementById('mobileMenuOverlay').classList.add('active');
            document.body.classList.add('menu-open');
        }
        function closeMobileMenu() {
            document.getElementById('mobileMenu').classList.remove('active');
            document.getElementById('mobileMenuOverlay').classList.remove('active');
            document.body.classList.remove('menu-open');
        }
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeMobileMenu(); });
    </script>
</body>
</html>
