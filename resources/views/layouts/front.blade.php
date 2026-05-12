<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('Al-Ertiqaa High Institute for Training'))</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Vector.png') }}" />

    <!-- Bootstrap CSS -->
    @if(app()->getLocale() == 'ar')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <!-- Cairo Font - Preload critical weights -->
    <link rel="preload" href="/font/static/Cairo-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/font/static/Cairo-Bold.woff2" as="font" type="font/woff2" crossorigin>

    <!-- Cairo Font - Local WOFF2 -->
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-Regular.woff2') format('woff2'),
                 url('/font/static/Cairo-Regular.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-Medium.woff2') format('woff2'),
                 url('/font/static/Cairo-Medium.ttf') format('truetype');
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-SemiBold.woff2') format('woff2'),
                 url('/font/static/Cairo-SemiBold.ttf') format('truetype');
            font-weight: 600;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-Bold.woff2') format('woff2'),
                 url('/font/static/Cairo-Bold.ttf') format('truetype');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-ExtraBold.woff2') format('woff2'),
                 url('/font/static/Cairo-ExtraBold.ttf') format('truetype');
            font-weight: 800;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-Black.woff2') format('woff2'),
                 url('/font/static/Cairo-Black.ttf') format('truetype');
            font-weight: 900;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-Light.woff2') format('woff2'),
                 url('/font/static/Cairo-Light.ttf') format('truetype');
            font-weight: 300;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-ExtraLight.woff2') format('woff2'),
                 url('/font/static/Cairo-ExtraLight.ttf') format('truetype');
            font-weight: 200;
            font-style: normal;
            font-display: swap;
        }
    </style>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />

    <style>
        /* Colors */
        :root {
            --main-color: rgba(0, 113, 170, 1);
            --second-color: rgba(243, 244, 246, 1);
        }

        /* Global */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: 1.5;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .page-body {
            flex: 1;
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
            position: relative;
            z-index: 500;
        }

        .bg-gray {
            background-color: rgba(243, 244, 246, 1);
        }

        .top-bar {
            border-bottom: 1px solid #d1d5db;
        }

        .middle-bar {
            border-bottom: none;
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

        [dir="ltr"] .mobile-menu {
            right: auto;
            left: 0;
            box-shadow: 5px 0 15px rgba(0, 0, 0, 0.3);
        }

        .mobile-menu.active {
            display: flex;
            padding-right: 20px;
        }

        [dir="ltr"] .mobile-menu.active {
            padding-right: 0;
            padding-left: 20px;
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

        [dir="ltr"] .mobile-nav-list li a:hover,
        [dir="ltr"] .mobile-nav-list li a.active {
            padding-right: 0;
            padding-left: 25px;
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

        /* Main Content Wrapper */
        .main-content {
            padding-top: 2rem;
        }

        /* Breadcrumb */
        .breadcrumb-section {
            background: #eaf5fb;
            padding: 2rem clamp(2rem, 5vw, 5rem);
            margin: 0 clamp(1rem, 3vw, 3rem);
            border-radius: 12px;
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

        /* Hero Section for inner pages */
        .hero-section {
            background: #eaf5fb;
            padding: 3rem clamp(2rem, 5vw, 5rem);
            margin: 0;
            margin-bottom: 4rem;
            border-radius: 0;
        }

        .hero-section h2 {
            margin-bottom: 1rem;
            font-weight: bold;
        }

        .hero-section p {
            line-height: 1.8;
            color: rgba(56, 66, 80, 1);
        }

        .hero-section .breadcrumb-nav {
            margin-bottom: 1.5rem;
        }

        /* Content Section */
        .content-section {
            padding: 2rem clamp(1rem, 3vw, 3rem);
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

        /* Search Modal */
        .search-modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.6);
            z-index: 10000;
            justify-content: center;
            align-items: flex-start;
            padding-top: 100px;
        }

        .search-modal-overlay.active {
            display: flex;
        }

        .search-modal {
            background: white;
            border-radius: 15px;
            padding: 25px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-modal-header h4 {
            margin: 0;
            color: var(--main-color);
        }

        .close-search-btn {
            width: 35px;
            height: 35px;
            border: none;
            background: #f0f0f0;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .close-search-btn:hover {
            background: var(--main-color);
            color: white;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-input-wrapper input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s;
        }

        .search-input-wrapper input:focus {
            border-color: var(--main-color);
        }

        .search-input-wrapper .search-submit-btn {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--main-color);
            color: white;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        [dir="ltr"] .search-input-wrapper .search-submit-btn {
            left: auto;
            right: 10px;
        }

        .search-input-wrapper .search-submit-btn:hover {
            background: #005a87;
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

        /* ── Nav Dropdown — click-only, no hover trap ── */
        .nav-dropdown-wrap { position: relative; }
        .nav-dropdown-menu {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            right: 0;
            min-width: 230px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 10px 36px rgba(0,0,0,.13);
            padding: .45rem 0;
            z-index: 9999;
            list-style: none;
            margin: 0;
            pointer-events: none;
            opacity: 0;
            transform: translateY(-8px);
            transition: opacity .18s ease, transform .18s ease;
        }
        [dir="ltr"] .nav-dropdown-menu { right: auto; left: 0; }
        .nav-dropdown-menu.open {
            display: block;
            pointer-events: auto;
            opacity: 1;
            transform: translateY(0);
        }
        .dd-caret { opacity:.55; transition: transform .2s; }
        .nav-dropdown-wrap.open .dd-caret { transform: rotate(180deg); opacity:.8; }
        .nav-dropdown-menu li a {
            display: flex; align-items: center; gap: .6rem;
            padding: .65rem 1.15rem;
            font-size: .875rem; color: #374151 !important;
            text-decoration: none; transition: background .12s, color .12s;
        }
        .nav-dropdown-menu li a i { font-size: .95rem; color: var(--main-color); flex-shrink:0; }
        .nav-dropdown-menu li a:hover { background: #eaf5fb; color: var(--main-color) !important; }
        .nav-dropdown-menu li a.active { color: var(--main-color) !important; font-weight: 700; background: #eaf5fb; }
        .dropdown-divider-item { height: 1px; background: #f3f4f6; margin: .35rem 0; }
        .dd-badge {
            margin-right: auto; font-size: .68rem; font-weight: 700;
            background: #dbeafe; color: #1d4ed8;
            border-radius: 20px; padding: .1rem .55rem; white-space: nowrap;
        }
        [dir="ltr"] .dd-badge { margin-right: 0; margin-left: auto; }

        /* ── Offers badge ── */
        .nav-offers-badge {
            display:inline-block; background:#ef4444; color:#fff;
            font-size:.58rem; font-weight:900; padding:.05rem .38rem;
            border-radius:20px; line-height:1.5; vertical-align:middle;
            margin-right:.15rem;
        }

        /* ── Mobile sub-items ── */
        .mobile-sub-item a {
            padding-right: 2.75rem !important;
            font-size: .875rem !important;
            color: #6b7280 !important;
            background: #fafafa !important;
        }
        [dir="ltr"] .mobile-sub-item a { padding-right: 1rem !important; padding-left: 2.75rem !important; }
        .mobile-sub-item a::before { content: '—'; color: #d1d5db; margin-left: .4rem; }
        [dir="ltr"] .mobile-sub-item a::before { margin-left: 0; margin-right: .4rem; }
        .mobile-sub-item a:hover, .mobile-sub-item a.active { color: var(--main-color) !important; background: #eaf5fb !important; }

        /* Language Switcher */
        .lang-switcher {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .lang-switcher a {
            color: inherit;
            text-decoration: none;
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

            /* Hero section mobile */
            .hero-section {
                margin: 0;
                margin-bottom: 2rem;
                padding: 2rem 1.5rem;
            }

            .footer-bottom {
                flex-direction: column;
                gap: .5rem;
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

        /* ── Saudi Top Bar ── */
        .saudi-top-bar {
            background: #fff;
            color: #1a1a1a;
            font-size: 0.78rem;
            padding: 0.35rem clamp(1rem, 3vw, 3rem);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            border-bottom: 2px solid #006c35;
        }

        .saudi-top-bar .saudi-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            flex-wrap: wrap;
        }

        .saudi-top-bar .saudi-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .saudi-emblem {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 0.82rem;
            letter-spacing: 0.01em;
        }

        .saudi-emblem img {
            width: 28px;
            height: 28px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .saudi-divider {
            width: 1px;
            height: 18px;
            background: rgba(0,0,0,0.15);
        }

        .saudi-badge {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(0,108,53,0.07);
            border: 1px solid rgba(0,108,53,0.25);
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 0.72rem;
        }

        .saudi-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #4ade80;
            flex-shrink: 0;
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .vision-badge {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            font-weight: 700;
            font-size: 0.75rem;
            opacity: 0.92;
        }

        .vision-badge span.v-year {
            background: #006c35;
            color: #fff;
            border-radius: 3px;
            padding: 1px 6px;
            font-weight: 800;
        }

        .saudi-date-time {
            font-size: 0.72rem;
            opacity: 0.85;
            white-space: nowrap;
        }

        .accessibility-btns {
            display: flex;
            gap: 0.4rem;
        }

        .accessibility-btns button {
            background: rgba(0,0,0,0.06);
            border: 1px solid rgba(0,0,0,0.12);
            color: #333;
            width: 24px;
            height: 24px;
            border-radius: 4px;
            font-size: 0.7rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            padding: 0;
        }

        .accessibility-btns button:hover {
            background: rgba(0,108,53,0.12);
            color: #006c35;
        }

        @media (max-width: 768px) {
            .saudi-top-bar { font-size: 0.7rem; padding: 0.3rem 0.75rem; }
            .vision-badge, .saudi-date-time, .accessibility-btns { display: none; }
        }

        @yield('styles')
    </style>
</head>
<body>

    <!-- Saudi National Identity Top Bar -->
    <div class="saudi-top-bar" role="banner" aria-label="Saudi national identity bar">
        <div class="saudi-left">
            <!-- National Emblem + Kingdom name -->
            <div class="saudi-emblem">
                <!-- Saudi Arabia Flag SVG -->
                <svg width="38" height="26" viewBox="0 0 900 600" xmlns="http://www.w3.org/2000/svg" style="border-radius:3px;flex-shrink:0;box-shadow:0 1px 4px rgba(0,0,0,.4)">
                    <rect width="900" height="600" fill="#006c35"/>
                    <!-- Shahada text (La ilaha illallah Muhammadun rasulullah) -->
                    <text x="450" y="230" text-anchor="middle" fill="white" font-size="110" font-family="'Traditional Arabic','Scheherazade New','Arial',serif" font-weight="bold">لا إله إلا الله</text>
                    <text x="450" y="360" text-anchor="middle" fill="white" font-size="110" font-family="'Traditional Arabic','Scheherazade New','Arial',serif" font-weight="bold">محمد رسول الله</text>
                    <!-- Sword -->
                    <g transform="translate(450,460)">
                        <!-- Blade -->
                        <rect x="-180" y="-8" width="340" height="16" rx="4" fill="white"/>
                        <!-- Tip -->
                        <polygon points="160,-8 220,0 160,8" fill="white"/>
                        <!-- Guard -->
                        <rect x="-195" y="-28" width="20" height="56" rx="4" fill="white"/>
                        <!-- Handle -->
                        <rect x="-240" y="-10" width="50" height="20" rx="5" fill="white"/>
                        <!-- Pommel -->
                        <circle cx="-240" cy="0" r="14" fill="white"/>
                    </g>
                </svg>
                <span>المملكة العربية السعودية</span>
            </div>

            <div class="saudi-divider"></div>

            <!-- Official site badge -->
            <div class="saudi-badge">
                <span class="dot"></span>
                <span>موقع رسمي معتمد</span>
            </div>

            <div class="saudi-divider d-none d-md-block"></div>

            <!-- Vision 2030 -->
            <div class="vision-badge">
                <span>رؤية</span>
                <span class="v-year">2030</span>
            </div>
        </div>

        <div class="saudi-right">
            <!-- Live date/time -->
            <div class="saudi-date-time" id="saudiDateTime"></div>

            <div class="saudi-divider"></div>

            <!-- Accessibility: font size -->
            <div class="accessibility-btns" title="حجم الخط">
                <button onclick="changeFontSize(1)" title="تكبير الخط" aria-label="تكبير الخط">A+</button>
                <button onclick="changeFontSize(-1)" title="تصغير الخط" aria-label="تصغير الخط">A-</button>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg d-flex flex-column shadow-sm">
        {{-- Containers 1 & 2 removed --}}

        <!-- Container 3 -->
        <div class="container-fluid bottom-bar bg-white d-flex align-items-center justify-content-between">
            <a class="navbar-brand" href="/">
                <img src="{{ asset('images/nav.png') }}" alt="Logo" />
            </a>

            <button class="navbar-toggler d-lg-none" type="button" onclick="toggleMobileMenu()">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Desktop Menu -->
            @php $activeOffersCount = \App\Models\Offer::active()->count(); @endphp
            <div class="desktop-menu d-none d-lg-flex align-items-center justify-content-between flex-grow-1">
                <ul class="navbar-nav d-flex flex-row mb-0">

                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">{{ __('Home') }}</a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('training-paths') }}" class="{{ request()->routeIs('training-paths') ? 'active' : '' }}">{{ __('Training Paths') }}</a>
                    </li>

                    {{-- Courses dropdown --}}
                    <li class="nav-item nav-dropdown-wrap" id="coursesWrap">
                        <a href="#" id="coursesToggle"
                           class="{{ request()->routeIs('short-courses') || request()->routeIs('english-courses') ? 'active' : '' }}"
                           style="display:inline-flex;align-items:center;gap:.3rem;">
                            {{ __('Short Courses') }}
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="dd-caret"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                        </a>
                        <ul class="nav-dropdown-menu" id="coursesMenu">
                            <li>
                                <a href="{{ route('short-courses') }}" class="{{ request()->routeIs('short-courses') ? 'active' : '' }}">
                                    <i class="bi bi-grid-3x3-gap"></i> الدورات القصيرة
                                </a>
                            </li>
                            <li class="dropdown-divider-item"></li>
                            <li>
                                <a href="{{ route('english-courses') }}" class="{{ request()->routeIs('english-courses') ? 'active' : '' }}">
                                    <i class="bi bi-translate"></i> اللغة الإنجليزية
                                    <span class="dd-badge">15 مستوى</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('english-courses', ['tab' => 'foundation']) }}">
                                    <i class="bi bi-arrow-return-left" style="opacity:.5"></i> مستويات التمهيد (0–2)
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('english-courses', ['tab' => 'core-a']) }}">
                                    <i class="bi bi-arrow-return-left" style="opacity:.5"></i> المستويات 1–6
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('english-courses', ['tab' => 'core-b']) }}">
                                    <i class="bi bi-arrow-return-left" style="opacity:.5"></i> المستويات 7–12
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('offers') }}" class="{{ request()->routeIs('offers') ? 'active' : '' }}">
                            {{ __('Offers') }}@if($activeOffersCount > 0)<span class="nav-offers-badge">{{ $activeOffersCount }}</span>@endif
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'active' : '' }}">{{ __('About Us') }}</a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('news') }}" class="{{ request()->routeIs('news*') ? 'active' : '' }}">{{ __('News') }}</a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">{{ __('Contact Us') }}</a>
                    </li>

                </ul>

                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline" type="button" onclick="openSearchModal()" title="{{ __('Search') }}">
                        <i class="bi bi-search"></i>
                    </button>
                    <div class="btn btn-outline lang-switcher">
                        <i class="bi bi-translate"></i>
                        @if(app()->getLocale() == 'ar')
                            <a href="{{ route('lang.switch', 'en') }}">EN</a>
                        @else
                            <a href="{{ route('lang.switch', 'ar') }}">ع</a>
                        @endif
                    </div>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-outline">
                            <i class="bi bi-person-circle"></i> {{ __('Dashboard') }}
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline" style="background:var(--main-color);color:#fff;border-color:var(--main-color);">
                            <i class="bi bi-person"></i> {{ __('Login') }}
                        </a>
                    @endauth
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
            <li><a href="{{ route('home') }}"           class="{{ request()->routeIs('home')          ? 'active' : '' }}"><i class="bi bi-house-door" style="margin-left:.4rem;opacity:.6"></i>{{ __('Home') }}</a></li>
            <li><a href="{{ route('training-paths') }}" class="{{ request()->routeIs('training-paths') ? 'active' : '' }}"><i class="bi bi-signpost-split" style="margin-left:.4rem;opacity:.6"></i>{{ __('Training Paths') }}</a></li>
            <li><a href="{{ route('short-courses') }}"  class="{{ request()->routeIs('short-courses')  ? 'active' : '' }}"><i class="bi bi-grid-3x3-gap" style="margin-left:.4rem;opacity:.6"></i>{{ __('Short Courses') }}</a></li>
            <li class="mobile-sub-item"><a href="{{ route('english-courses') }}" class="{{ request()->routeIs('english-courses') ? 'active' : '' }}"><i class="bi bi-translate"></i> اللغة الإنجليزية</a></li>
            <li class="mobile-sub-item"><a href="{{ route('english-courses', ['tab' => 'foundation']) }}">مستويات التمهيد (0–2)</a></li>
            <li class="mobile-sub-item"><a href="{{ route('english-courses', ['tab' => 'core-a']) }}">المستويات 1–6</a></li>
            <li class="mobile-sub-item"><a href="{{ route('english-courses', ['tab' => 'core-b']) }}">المستويات 7–12</a></li>
            <li>
                <a href="{{ route('offers') }}" class="{{ request()->routeIs('offers') ? 'active' : '' }}">
                    <i class="bi bi-tags" style="margin-left:.4rem;opacity:.6"></i>{{ __('Offers') }}
                    @if($activeOffersCount > 0)<span class="nav-offers-badge" style="margin-right:.25rem;">{{ $activeOffersCount }}</span>@endif
                </a>
            </li>
            <li><a href="{{ route('about') }}"   class="{{ request()->routeIs('about')   ? 'active' : '' }}"><i class="bi bi-info-circle" style="margin-left:.4rem;opacity:.6"></i>{{ __('About Us') }}</a></li>
            <li><a href="{{ route('news') }}"    class="{{ request()->routeIs('news*')    ? 'active' : '' }}"><i class="bi bi-newspaper" style="margin-left:.4rem;opacity:.6"></i>{{ __('News') }}</a></li>
            <li><a href="{{ route('faq') }}"     class="{{ request()->routeIs('faq')      ? 'active' : '' }}"><i class="bi bi-question-circle" style="margin-left:.4rem;opacity:.6"></i>{{ __('FAQ') }}</a></li>
            <li><a href="{{ route('contact') }}" class="{{ request()->routeIs('contact')  ? 'active' : '' }}"><i class="bi bi-envelope" style="margin-left:.4rem;opacity:.6"></i>{{ __('Contact Us') }}</a></li>
        </ul>
        <div class="mobile-menu-buttons">
            <button class="btn btn-outline w-100" type="button" onclick="openSearchModal(); closeMobileMenu();">
                <i class="bi bi-search"></i> {{ __('Search') }}
            </button>
            @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-outline w-100">
                    <i class="bi bi-person-circle"></i> {{ __('Dashboard') }}
                </a>
            @else
                <a href="{{ route('login') }}" class="btn w-100" style="background:var(--main-color);color:#fff;border-color:var(--main-color);">
                    <i class="bi bi-person"></i> {{ __('Login') }}
                </a>
            @endauth
            <div class="btn btn-outline w-100 lang-switcher justify-content-center">
                <i class="bi bi-translate"></i>
                @if(app()->getLocale() == 'ar')
                    <a href="{{ route('lang.switch', 'en') }}">English</a>
                @else
                    <a href="{{ route('lang.switch', 'ar') }}">العربية</a>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="page-body">
        @yield('content')
    </div>

    <!-- Search Modal -->
    <div class="search-modal-overlay" id="searchModalOverlay" onclick="closeSearchModal(event)">
        <div class="search-modal" onclick="event.stopPropagation()">
            <div class="search-modal-header">
                <h4>{{ __('Search') }}</h4>
                <button class="close-search-btn" type="button" onclick="closeSearchModal()">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form action="#" method="GET" class="search-form">
                <div class="search-input-wrapper">
                    <input type="text" name="q" id="searchInput" placeholder="{{ __('Search for courses, paths, news...') }}" autocomplete="off" />
                    <button type="submit" class="search-submit-btn">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('layouts.partials.footer')

    @include('layouts.partials.toaster')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Current locale
        const currentLocale = '{{ app()->getLocale() }}';

        // Saudi Top Bar — live date/time
        (function updateSaudiTime() {
            const el = document.getElementById('saudiDateTime');
            if (!el) return;
            const now = new Date();
            const days = ['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
            const months = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
            let h = now.getHours(), m = now.getMinutes().toString().padStart(2,'0');
            const period = h >= 12 ? 'م' : 'ص';
            h = h % 12 || 12;
            el.textContent = `${days[now.getDay()]} ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} — ${h}:${m} ${period}`;
        })();
        setInterval(function() {
            const el = document.getElementById('saudiDateTime');
            if (!el) return;
            const now = new Date();
            const days = ['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];
            const months = ['يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
            let h = now.getHours(), m = now.getMinutes().toString().padStart(2,'0');
            const period = h >= 12 ? 'م' : 'ص';
            h = h % 12 || 12;
            el.textContent = `${days[now.getDay()]} ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} — ${h}:${m} ${period}`;
        }, 30000);

        // Font size accessibility
        let currentFontSize = 100;
        function changeFontSize(dir) {
            currentFontSize = Math.min(130, Math.max(85, currentFontSize + dir * 5));
            document.documentElement.style.fontSize = currentFontSize + '%';
        }

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
                closeSearchModal();
            }
        });

        // Nav Dropdown — click-only toggle
        (function () {
            const wrap   = document.getElementById('coursesWrap');
            const toggle = document.getElementById('coursesToggle');
            const menu   = document.getElementById('coursesMenu');
            if (!toggle || !menu || !wrap) return;

            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                const isOpen = wrap.classList.contains('open');
                // close all first
                document.querySelectorAll('.nav-dropdown-wrap.open').forEach(function(el) {
                    el.classList.remove('open');
                    el.querySelector('.nav-dropdown-menu') && el.querySelector('.nav-dropdown-menu').classList.remove('open');
                });
                if (!isOpen) {
                    wrap.classList.add('open');
                    menu.classList.add('open');
                }
            });

            document.addEventListener('click', function (e) {
                if (!wrap.contains(e.target)) {
                    wrap.classList.remove('open');
                    menu.classList.remove('open');
                }
            });
        })();

        // Search Modal Functions
        function openSearchModal() {
            const searchModal = document.getElementById('searchModalOverlay');
            searchModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            setTimeout(() => {
                document.getElementById('searchInput').focus();
            }, 100);
        }

        function closeSearchModal(event) {
            if (event && event.target !== event.currentTarget) return;
            const searchModal = document.getElementById('searchModalOverlay');
            searchModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    </script>

    @yield('scripts')
</body>
</html>
