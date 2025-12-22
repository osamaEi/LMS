<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'معهد الإرتقاء العالي للتدريب')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Vector.png') }}" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <style>
        /* Cairo Local Font */
        @font-face {
            font-family: 'Cairo';
            src: url('/fonts/Cairo-VariableFont_slnt,wght.ttf') format('truetype');
            font-weight: 100 900;
            font-style: normal;
            font-display: swap;
        }

        /* Colors */
        :root {
            --main-color: rgba(0, 113, 170, 1);
            --second-color: rgba(243, 244, 246, 1);
        }

        /* Global */
        * {
            font-family: 'Cairo', sans-serif !important;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.5;
            overflow-x: hidden;
        }

        /* Font Awesome fix */
        .fa, .fas, .far, .fab {
            font-family: "Font Awesome 6 Free" !important;
            font-weight: 900;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* Navbar Styles */
        nav.navbar {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }

        .bg-gray {
            background-color: rgba(243, 244, 246, 1);
        }

        .top-bar {
            padding: 0.25rem 1rem;
            border-bottom: 1px solid #d1d5db;
        }

        .middle-bar {
            padding: 0.35rem 1rem;
            border-bottom: 1px solid #d1d5db;
        }

        .middle-bar .info-section {
            flex-wrap: wrap;
            gap: 1rem;
        }

        .middle-bar .info-section > div {
            font-size: 0.9rem;
        }

        .middle-bar .icons-section {
            flex-shrink: 0;
            gap: 0.75rem;
        }

        .middle-bar .icons-section i {
            cursor: pointer;
            font-size: 1.1rem;
        }

        .middle-bar p,
        .middle-bar i {
            white-space: nowrap;
        }

        .bottom-bar {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .navbar-brand img {
            max-width: 120px;
            height: auto;
        }

        .nav-item a {
            color: black !important;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 20px;
            transition: all 0.3s;
        }

        .nav-item a:hover,
        .nav-item a.active {
            color: var(--main-color) !important;
            background: #eaf5fb;
        }

        .navbar-nav {
            gap: 0.5rem;
        }

        .bottom-bar .btn-outline {
            white-space: nowrap;
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
            border: 1px solid #dee2e6;
            background: transparent;
            border-radius: 5px;
            transition: all 0.3s;
        }

        .bottom-bar .btn-outline:hover {
            background: #f8f9fa;
        }

        /* Mobile Menu Styles */
        .mobile-menu-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9998;
        }

        .mobile-menu-overlay.active {
            display: block;
        }

        .mobile-menu {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            width: 383px;
            height: 100vh;
            background: #fff;
            z-index: 9999;
            overflow-y: auto;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.3);
            flex-direction: column;
        }

        .mobile-menu.active {
            display: flex;
            padding-right: 117px;
        }

        .mobile-menu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .mobile-menu-header .navbar-brand img {
            max-width: 100px;
            height: auto;
        }

        .close-menu-btn {
            width: 40px;
            height: 40px;
            background: #f0f0f0;
            border: none;
            font-size: 1.25rem;
            color: #333;
            cursor: pointer;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .close-menu-btn:hover {
            background: var(--main-color);
            color: #fff;
        }

        .mobile-nav-list {
            list-style: none;
            padding: 0;
            margin: 0;
            flex: 1;
        }

        .mobile-nav-list li {
            border-bottom: 1px solid #f0f0f0;
        }

        .mobile-nav-list li a {
            display: block;
            padding: 15px 20px;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .mobile-nav-list li a:hover,
        .mobile-nav-list li a.active {
            background: #eaf5fb;
            color: var(--main-color);
            padding-right: 25px;
        }

        .mobile-menu-buttons {
            padding: 15px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            border-top: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .mobile-menu-buttons .btn-outline {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px;
            font-size: 0.9rem;
            border: 1px solid #dee2e6;
            background: #fff;
            border-radius: 8px;
            color: #333;
            text-decoration: none;
            cursor: pointer;
        }

        .mobile-menu-buttons .btn-outline:hover {
            background: var(--main-color);
            color: #fff;
            border-color: var(--main-color);
        }

        body.menu-open {
            overflow: hidden;
        }

        @media (min-width: 992px) {
            .mobile-menu,
            .mobile-menu-overlay,
            .navbar-toggler {
                display: none !important;
            }
        }

        /* Common Styles */
        .st-p {
            background: #eaf5fb;
            color: var(--main-color);
            width: fit-content;
            padding: 5px 15px;
            border-radius: 20px;
        }

        .nd-p {
            margin: 20px 0;
            line-height: 1.6;
            color: rgba(56, 66, 80, 1);
        }

        .full-btn {
            background-color: var(--main-color);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            white-space: nowrap;
            transition: all 0.3s;
            cursor: pointer;
        }

        .notfull-btn {
            background-color: transparent;
            color: var(--main-color);
            padding: 10px 20px;
            border: 1px solid var(--main-color);
            border-radius: 5px;
            white-space: nowrap;
            transition: all 0.3s;
            cursor: pointer;
        }

        .full-btn:hover,
        .notfull-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Breadcrumb */
        .breadcrumb-section {
            background: #eaf5fb;
            padding: 2rem 3rem;
        }

        .breadcrumb-nav {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .breadcrumb-nav a {
            color: rgba(56, 66, 80, 1);
        }

        .breadcrumb-nav span {
            color: rgba(157, 164, 174, 1);
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 16px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 1) 0%, rgba(82, 154, 255, 0.2) 75%);
            padding: 15px;
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .bi-shield-fill-check {
            font-size: 75px;
            color: var(--main-color);
        }

        /* Courses Container */
        .courses-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 0 15px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .course-card {
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            transition: 0.3s;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .course-card img {
            width: 100%;
            height: auto;
            object-fit: cover;
            aspect-ratio: 16/9;
        }

        /* Section Container */
        .section-container {
            padding: clamp(2rem, 5vw, 5rem) clamp(1rem, 3vw, 3rem);
            margin: 0 auto;
        }

        /* Footer */
        .foot {
            background-color: #1d6b8f;
            color: white;
            padding: clamp(2rem, 4vw, 6rem) clamp(1rem, 3vw, 5rem);
        }

        .head-top {
            display: flex;
            gap: clamp(2rem, 4vw, 5rem);
            margin-bottom: clamp(2rem, 4vw, 5rem);
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .footer-intro {
            flex: 1;
            min-width: 320px;
            max-width: 700px;
        }

        .footer-intro img {
            max-width: clamp(200px, 25vw, 350px);
            height: auto;
        }

        .footer-intro h2 {
            margin-top: clamp(1rem, 2vw, 2rem);
            margin-bottom: clamp(0.5rem, 1vw, 1rem);
            line-height: 1.4;
        }

        .intro-text {
            line-height: 1.8;
            opacity: 0.95;
        }

        .mobile-apps {
            flex: 0 1 auto;
            min-width: 300px;
            max-width: 500px;
        }

        .apps-title {
            margin-bottom: clamp(1rem, 2vw, 2rem);
        }

        .footer-store-buttons {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: clamp(10px, 1.5vw, 18px);
        }

        .footer-store-btn {
            display: flex;
            align-items: center;
            gap: clamp(8px, 1vw, 14px);
            background-color: #000;
            color: #fff;
            padding: clamp(12px, 1.5vw, 18px) clamp(14px, 1.8vw, 22px);
            border-radius: clamp(10px, 1.2vw, 16px);
            text-decoration: none;
            transition: all 0.3s ease;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .footer-store-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
            color: #fff;
        }

        .footer-section {
            margin-bottom: clamp(2rem, 3vw, 3rem);
        }

        .footer-section h5 {
            margin: 0 !important;
            margin-bottom: 0.5rem !important;
        }

        .footer-section hr {
            margin: clamp(0.5rem, 1vw, 1rem) 0 !important;
            opacity: 0.4;
            border-color: rgba(255, 255, 255, 0.3);
        }

        .footer-section a {
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            display: block;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .footer-section a:hover {
            opacity: 1;
            transform: translateX(-5px);
            padding-right: 5px;
        }

        .foot-foot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: clamp(2rem, 4vw, 5rem);
            padding-top: clamp(1.5rem, 2.5vw, 3rem);
            border-top: 2px solid rgba(255, 255, 255, 0.25);
            flex-wrap: wrap;
            gap: clamp(1rem, 2vw, 2rem);
        }

        .foot-foot p {
            margin: 0;
            opacity: 0.9;
        }

        .footer-meta {
            display: flex;
            gap: clamp(1.5rem, 2.5vw, 3rem);
            flex-wrap: wrap;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .navbar-toggler {
                border: 1px solid #dee2e6;
                padding: 0.5rem 0.75rem;
            }

            .middle-bar .info-section {
                gap: 0.5rem;
            }
        }

        @media (max-width: 768px) {
            .top-bar {
                padding: 0.25rem 0.5rem;
                flex-wrap: wrap;
            }

            .top-bar img {
                width: 24px;
            }

            .top-text {
                font-size: 0.75rem;
            }

            .middle-bar {
                padding: 0.35rem 0.5rem;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .middle-bar .info-section {
                gap: 0.5rem;
                justify-content: flex-start;
                width: 100%;
            }

            .middle-bar .info-section > div {
                font-size: 0.7rem;
            }

            .middle-bar .icons-section {
                display: none;
            }

            .bottom-bar {
                padding: 0.5rem;
            }

            .navbar-brand img {
                max-width: 100px;
            }

            .footer-store-buttons {
                grid-template-columns: 1fr;
            }

            .foot-foot {
                flex-direction: column;
                align-items: flex-start;
                text-align: right;
            }

            .footer-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
        }

        @media (max-width: 575px) {
            .top-bar {
                padding: 0.2rem 0.5rem;
            }

            .top-bar img {
                width: 20px;
            }

            .top-text {
                font-size: 0.65rem;
            }

            .middle-bar {
                padding: 0.25rem 0.5rem;
            }

            .middle-bar .info-section {
                gap: 0.35rem;
            }

            .middle-bar .info-section > div {
                font-size: 0.6rem;
            }

            .middle-bar .info-section > div:nth-child(n+3) {
                display: none;
            }

            .navbar-brand img {
                max-width: 80px;
            }

            .navbar-toggler {
                padding: 0.35rem 0.5rem;
                font-size: 0.85rem;
            }

            .courses-container {
                grid-template-columns: 1fr;
            }
        }

        @yield('styles')
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg d-flex flex-column shadow-sm">
        <!-- Container 1 -->
        <div class="container-fluid bg-gray d-flex justify-content-start gap-3 align-items-center top-bar">
            <img src="https://flagcdn.com/sa.svg" width="30" alt="Saudi Flag" />
            <p class="top-text mb-0">موقع حكومي مسجل لدى هيئة الحكومة الرقمية</p>
        </div>

        <!-- Container 2 -->
        <div class="container-fluid middle-bar bg-gray d-flex justify-content-between align-items-center">
            <div class="d-flex gap-4 info-section">
                <div class="d-flex gap-1 align-items-center">
                    <i class="bi bi-cloud"></i>
                    <p class="mb-0">غائم</p>
                </div>
                <div class="d-flex gap-1 align-items-center">
                    <i class="bi bi-calendar"></i>
                    <p class="mb-0" id="currentDate"></p>
                </div>
                <div class="d-flex gap-1 align-items-center">
                    <i class="bi bi-clock"></i>
                    <p class="mb-0" id="currentTime"></p>
                </div>
                <div class="d-flex gap-1 align-items-center">
                    <i class="bi bi-geo-alt"></i>
                    <p class="mb-0">الرياض</p>
                </div>
            </div>

            <div class="d-flex gap-2 icons-section">
                <i class="bi bi-eye"></i>
                <i class="bi bi-zoom-in"></i>
                <i class="bi bi-zoom-out"></i>
                <i class="bi bi-mic"></i>
            </div>
        </div>

        <!-- Container 3 -->
        <div class="container-fluid bottom-bar bg-white d-flex align-items-center justify-content-between">
            <a class="navbar-brand" href="/">
                <img src="{{ asset('images/nav.png') }}" alt="Logo" />
            </a>

            <button class="navbar-toggler d-lg-none" type="button" onclick="toggleMobileMenu()">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Desktop Menu -->
            <div class="desktop-menu d-none d-lg-flex align-items-center justify-content-between flex-grow-1">
                <ul class="navbar-nav d-flex flex-row mb-0">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('training-paths') }}" class="{{ request()->routeIs('training-paths') ? 'active' : '' }}">مسارات التدريب</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('short-courses') }}" class="{{ request()->routeIs('short-courses') ? 'active' : '' }}">الدورات القصيرة</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">عن المعهد</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('news') }}" class="{{ request()->routeIs('news') ? 'active' : '' }}">الأخبار</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('faq') }}" class="{{ request()->routeIs('faq') ? 'active' : '' }}">الأسئلة الشائعة</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">تواصل معنا</a>
                    </li>
                </ul>

                <div class="d-flex gap-2">
                    <button class="btn btn-outline" type="button">
                        <i class="bi bi-search"></i> Search
                    </button>
                    <button type="button" class="btn btn-outline">
                        <i class="bi bi-translate"></i> English
                    </button>
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline">
                                <i class="bi bi-person"></i> لوحة التحكم
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline">
                                <i class="bi bi-person"></i> تسجيل الدخول
                            </a>
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="mobile-menu-header">
            <a class="navbar-brand" href="/">
                <img src="{{ asset('images/nav.png') }}" alt="Logo" />
            </a>
            <button class="close-menu-btn" type="button" onclick="closeMobileMenu()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <ul class="mobile-nav-list">
            <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">الرئيسية</a></li>
            <li><a href="{{ route('training-paths') }}" class="{{ request()->routeIs('training-paths') ? 'active' : '' }}">مسارات التدريب</a></li>
            <li><a href="{{ route('short-courses') }}" class="{{ request()->routeIs('short-courses') ? 'active' : '' }}">الدورات القصيرة</a></li>
            <li><a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">عن المعهد</a></li>
            <li><a href="{{ route('news') }}" class="{{ request()->routeIs('news') ? 'active' : '' }}">الأخبار</a></li>
            <li><a href="{{ route('faq') }}" class="{{ request()->routeIs('faq') ? 'active' : '' }}">الأسئلة الشائعة</a></li>
            <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">تواصل معنا</a></li>
        </ul>
        <div class="mobile-menu-buttons">
            <button class="btn btn-outline" type="button">
                <i class="bi bi-search"></i> Search
            </button>
            <button type="button" class="btn btn-outline">
                <i class="bi bi-translate"></i> English
            </button>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline">
                        <i class="bi bi-person"></i> لوحة التحكم
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">
                        <i class="bi bi-person"></i> تسجيل الدخول
                    </a>
                @endauth
            @endif
        </div>
    </div>

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="p-5 foot container-fluid">
        <div class="head-top">
            <div class="footer-intro">
                <img src="{{ asset('images/footlogooo.png') }}" alt="" />
                <h2>معهد الارتقاء العالي للتدريب</h2>
                <p class="intro-text">
                    معهد تدريبي معتمد يقدّم برامج تطوير مهني ومسارات تعليمية تمتدّ لأكثر من 10 سنوات، نساعد الطلاب والمتدربين على اكتساب مهارات المستقبل، وتقديم برامج معتمدة تتماشى مع رؤية المملكة 2030، بإشراف نخبة من المدربين والخبراء لضمان جودة التدريب ومخرجاته.
                </p>
            </div>
            <div class="mobile-apps">
                <p class="apps-title">تطبيقات الجوال</p>
                <div class="footer-store-buttons" dir="ltr">
                    <a href="#" class="footer-store-btn">
                        <img src="{{ asset('images/huawei-appgallery-thumb.png') }}" style="width: 40px; border-radius: 10px" />
                        <div class="text">
                            <span class="small">EXPLORE IT ON</span>
                            <span class="big">AppGallery</span>
                        </div>
                    </a>
                    <a href="#" class="footer-store-btn">
                        <i class="bi bi-apple" style="font-size: 24px;"></i>
                        <div class="text">
                            <span class="small">Download on the</span>
                            <span class="big">App Store</span>
                        </div>
                    </a>
                    <a href="#" class="footer-store-btn pt-3 pb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="24" viewBox="0 0 21 24" fill="none">
                            <path d="M9.80482 11.4617L0.0895996 22.0059C0.0905121 22.0078 0.090512 22.0106 0.0914244 22.0125C0.389807 23.1574 1.41179 24 2.62539 24C3.11083 24 3.56616 23.8656 3.95671 23.6305L3.98773 23.6118L14.9229 17.1593L9.80482 11.4617Z" fill="#EA4335"/>
                            <path d="M19.6332 9.66424L19.624 9.6577L14.9029 6.85928L9.58398 11.6994L14.922 17.1562L19.6177 14.3858C20.4407 13.9305 21.0001 13.0431 21.0001 12.0204C21.0001 11.0033 20.4489 10.1205 19.6332 9.66424Z" fill="#FBBC04"/>
                            <path d="M0.0894234 1.9952C0.0310244 2.21542 0 2.44683 0 2.68571V21.3182C0 21.5571 0.0310245 21.7885 0.0903359 22.0078L10.1386 11.7332L0.0894234 1.9952Z" fill="#4285F4"/>
                            <path d="M9.87666 12L14.9044 6.85945L3.98201 0.383598C3.58508 0.140054 3.12154 8.67844e-05 2.62606 8.67844e-05C1.41246 8.67844e-05 0.38865 0.84456 0.0902675 1.99043C0.0902675 1.99136 0.0893555 1.9923 0.0893555 1.99323L9.87666 12Z" fill="#34A853"/>
                        </svg>
                        <div class="text">
                            <span class="small">GET IT ON</span>
                            <span class="big">Google Play</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="foot-body">
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12 d-flex flex-column gap-3 footer-section">
                    <h5>روابط مهمة</h5>
                    <hr />
                    <a href="{{ route('home') }}">الرئيسية</a>
                    <a href="{{ route('about') }}">عن المعهد</a>
                    <a href="{{ route('training-paths') }}">مسارات التدريب</a>
                    <a href="{{ route('short-courses') }}">الدورات القادمة</a>
                    <a href="#">المدربين</a>
                    <a href="{{ route('news') }}">الأخبار</a>
                    <a href="{{ route('contact') }}">تواصل معنا</a>
                </div>
                <div class="col-lg-4 col-md-6 col-12 d-flex flex-column gap-3 footer-section">
                    <h5>خدمات المعهد</h5>
                    <hr />
                    <a href="#">نظام التيرمات والبرامج المهنية</a>
                    <a href="{{ route('short-courses') }}">الدورات القصيرة</a>
                    <a href="#">التدريب عن بُعد</a>
                    <a href="#">اعتماد الشهادات</a>
                    <a href="#">تسجيل الطلاب</a>
                    <a href="#">انضمام المدربين</a>
                    <a href="#">الدعم الفني والمتابعة</a>
                </div>
                <div class="col-lg-4 col-md-12 col-12 d-flex flex-column gap-3 footer-section">
                    <h5>معلومات التواصل</h5>
                    <hr />
                    <a href="#">
                        <div><i class="bi bi-telephone"></i> رقم الجوال</div>
                        <div>9200343222 <i class="bi bi-copy"></i></div>
                    </a>
                    <a href="#">
                        <div><i class="bi bi-envelope"></i> البريد الإلكتروني</div>
                        <div>help@company.sa <i class="bi bi-copy"></i></div>
                    </a>
                    <a href="#">
                        <div><i class="bi bi-geo-alt-fill"></i> الموقع</div>
                        <div>الرياض <i class="bi bi-link-45deg"></i></div>
                    </a>
                    <a href="#">
                        <div>تابعنا على</div>
                        <div class="d-flex gap-3">
                            <i class="bi bi-instagram"></i>
                            <i class="bi bi-linkedin"></i>
                            <i class="bi bi-twitter-x"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="foot-foot">
            <div class="copyright">
                <p>معهد الارتقاء العالي للتدريب. جميع الحقوق محفوظة &copy; {{ date('Y') }}.</p>
            </div>
            <div class="footer-meta">
                <p>تاريخ آخر تعديل: {{ date('d/m/Y') }}</p>
                <p>تم تطويره وصيانته بواسطة الارتقاء</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Update date and time
        function updateDateTime() {
            const now = new Date();
            const arabicMonths = [
                "يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو",
                "يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر"
            ];

            const day = now.getDate();
            const month = arabicMonths[now.getMonth()];
            const year = now.getFullYear();
            document.getElementById('currentDate').textContent = `${day}-${month}-${year}`;

            let hours = now.getHours();
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const period = hours >= 12 ? 'مساءً' : 'صباحاً';
            hours = hours % 12 || 12;
            document.getElementById('currentTime').textContent = `${hours}:${minutes} ${period}`;
        }

        updateDateTime();
        setInterval(updateDateTime, 1000);

        // Mobile Menu Functions
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

            mobileMenu.classList.add('active');
            mobileMenuOverlay.classList.add('active');
            document.body.classList.add('menu-open');
        }

        function closeMobileMenu() {
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

            mobileMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
            document.body.classList.remove('menu-open');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileMenu();
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
