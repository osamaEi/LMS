<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>معهد الإرتقاء العالي للتدريب</title>

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

        /* Prevent body scroll when menu is open */
        body.menu-open {
            overflow: hidden;
        }

        /* Hide mobile menu on desktop */
        @media (min-width: 992px) {
            .mobile-menu,
            .mobile-menu-overlay,
            .navbar-toggler {
                display: none !important;
            }
        }

        /* Landing Section */
        .section-wrapper {
            background: linear-gradient(271deg, var(--Neutral-surface-container, #fff) 0.63%, var(--Accent-Gradient-gradient-lightest, #f5f6ff) 99.37%);
            padding: 0 clamp(1rem, 3vw, 3rem);
        }

        .section-content {
            padding: clamp(2rem, 4vw, 4rem) 0;
            margin: 0 auto;
        }

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

        .left-sec {
            position: relative;
            display: flex;
            justify-content: end;
            align-items: center;
            min-height: 400px;
            margin-top: 30px;
        }

        .main-img {
            max-width: 100%;
            height: auto;
        }

        .abs-btn {
            position: absolute;
            top: clamp(40px, 15%, 80px);
            right: 15%;
            background-color: var(--main-color);
            color: white;
            padding: 7px 15px;
            border: none;
            border-radius: 5px;
            white-space: nowrap;
        }

        .img-abs {
            position: absolute;
            top: 23%;
            right: 40%;
            max-width: clamp(30px, 5vw, 50px);
            height: auto;
        }

        /* Section Container */
        .section-container {
            padding: clamp(2rem, 5vw, 5rem) clamp(1rem, 3vw, 3rem);
            margin: 0 auto;
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

        .card h5 {
            margin-top: 10px;
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

        .course-card p {
            flex-grow: 1;
        }

        .marks {
            margin-bottom: 1rem;
        }

        .marks div {
            border: 1px solid black;
            padding: 1px 7px;
            border-radius: 7px;
            white-space: nowrap;
            font-size: 12px;
        }

        .marks .nd {
            color: var(--main-color);
            background-color: rgba(239, 248, 255, 1);
            border: 1px solid rgba(178, 221, 255, 1) !important;
        }

        .marks .th {
            color: rgba(8, 93, 58, 1);
            background-color: rgba(236, 253, 243, 1);
            border: 1px solid rgba(171, 239, 198, 1) !important;
        }

        /* How It Works Section */
        .content-wrapper {
            flex-direction: row-reverse;
            align-items: flex-start;
            gap: 20px;
        }

        .right-sec-steps {
            flex: 1;
            position: relative;
            margin: 20px 20px 0;
            min-width: 0;
        }

        .right-sec-steps::before {
            content: "";
            position: absolute;
            top: 0;
            right: -22px;
            width: 4px;
            height: 100%;
            background: linear-gradient(to bottom, #e5e7eb 0%, #e5e7eb 1%, var(--main-color) 1%, var(--main-color) 25%, #e5e7eb 25%, #e5e7eb 100%);
        }

        .right-sec-steps h4 {
            margin-bottom: 10px;
        }

        .section-item {
            margin-bottom: 30px;
        }

        .image-container {
            flex: 1;
            min-width: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .image-container img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        /* App Section */
        .app-section {
            background-color: rgba(234, 245, 251, 1);
        }

        .store-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .store-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #000;
            color: #fff;
            padding: 10px 15px;
            border-radius: 10px;
            text-decoration: none;
            width: 160px;
            height: 60px;
            transition: all 0.3s;
        }

        .store-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.4);
            color: #fff;
        }

        .store-btn .text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .store-btn .text .small {
            font-size: 10px;
            opacity: 0.9;
        }

        .store-btn .text .big {
            font-size: 14px;
            font-weight: 600;
        }

        /* Trainers Section */
        .trainers-card {
            max-width: 450px;
            border: none;
            border-radius: clamp(12px, 1.5vw, 20px);
            background: linear-gradient(180deg, rgba(255, 255, 255, 1) 0%, rgba(82, 154, 255, 0.2) 75%);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            text-align: center;
            padding: 20px;
        }

        .trainers-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .trainers-card .top-img img {
            width: clamp(50px, 6vw, 90px);
            height: clamp(50px, 6vw, 90px);
            border-radius: 50%;
            object-fit: cover;
        }

        .trainers-card .bottom-img img {
            width: clamp(35px, 4.5vw, 70px);
            height: clamp(35px, 4.5vw, 70px);
            border-radius: 50%;
            object-fit: cover;
        }

        .center-card {
            border: none;
            height: 100%;
        }

        .center-card img {
            width: 100%;
            height: auto;
            border-radius: clamp(12px, 1.5vw, 24px);
            object-fit: cover;
            max-height: clamp(300px, 40vw, 600px);
        }

        /* Feedback Section */
        .feedback-section {
            background-color: rgba(243, 244, 246, 1);
        }

        .feedback {
            margin-top: clamp(2rem, 4vw, 5rem);
            flex-wrap: wrap;
            gap: clamp(1.5rem, 3vw, 4rem);
            max-width: clamp(1200px, 90vw, 1800px);
            margin-left: auto;
            margin-right: auto;
        }

        .feedback .img img {
            width: clamp(200px, 20vw, 300px);
            height: auto;
            border-radius: 50%;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        }

        .feedback .text {
            position: relative;
            flex: 1.5;
        }

        .feedback .text .bi-quote {
            position: absolute;
            top: -110px;
            right: -130px;
            color: rgba(189, 188, 188, 0.6);
            transform: rotate(180deg);
            font-size: 160px;
        }

        .feedback .text h3 {
            line-height: 1.7;
            margin-bottom: clamp(1rem, 2vw, 2rem);
        }

        .imgs-av img {
            width: clamp(45px, 5vw, 70px);
            height: clamp(45px, 5vw, 70px);
            border-radius: 50%;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.3s;
        }

        .imgs-av img:hover,
        .imgs-av img.active {
            border-color: var(--main-color);
        }

        /* FAQ Section */
        .accordion {
            max-width: 1000px;
            margin: 0 auto;
        }

        .accordion-button {
            background-color: #fff;
            color: #000;
            box-shadow: none !important;
            justify-content: space-between;
        }

        .accordion-button::after {
            margin-left: 0;
            margin-right: auto;
        }

        .accordion-button:not(.collapsed) {
            background-color: rgba(249, 250, 251, 1);
            color: #000;
        }

        .accordion-button:focus {
            box-shadow: none !important;
        }

        .accordion-body {
            background-color: rgba(249, 250, 251, 1);
            line-height: 1.6;
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
            max-width: 100%;
            margin-left: auto;
            margin-right: auto;
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
            width: 100%;
            max-width: 100%;
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
            max-width: 100%;
        }

        .footer-store-btn {
            display: flex;
            flex-direction: row;
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

        /* Medium screens - Tablets */
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
            /* Top bar adjustments */
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

            /* Middle bar adjustments */
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

            /* Bottom bar adjustments */
            .bottom-bar {
                padding: 0.5rem;
            }

            .navbar-brand img {
                max-width: 100px;
            }

            .left-sec {
                margin-top: 40px;
                min-height: 300px;
            }

            .abs-btn {
                position: static;
                transform: none;
                margin-top: 15px;
            }

            .img-abs {
                display: none;
            }

            .content-wrapper {
                flex-direction: column;
            }

            .right-sec-steps {
                margin: 20px 0 0;
            }

            .right-sec-steps::before {
                right: -15px;
            }

            .image-container {
                width: 100%;
                margin-top: 30px;
            }

            .feedback {
                flex-direction: column;
            }

            .feedback .img {
                display: flex;
                justify-content: center;
            }

            .feedback .text .bi-quote {
                right: -20px;
                top: -30px;
                font-size: 40px;
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
            /* Top bar - hide on very small screens or simplify */
            .top-bar {
                padding: 0.2rem 0.5rem;
            }

            .top-bar img {
                width: 20px;
            }

            .top-text {
                font-size: 0.65rem;
            }

            /* Middle bar - show only essential info */
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

            /* Bottom bar */
            .navbar-brand img {
                max-width: 80px;
            }

            .navbar-toggler {
                padding: 0.35rem 0.5rem;
                font-size: 0.85rem;
            }

            .nav-item a {
                font-size: 0.9rem;
                padding: 0.6rem 0.75rem;
            }

            .navbar-collapse form .btn-outline {
                font-size: 0.85rem;
                padding: 0.5rem;
            }

            .courses-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 400px) {
            .top-bar {
                justify-content: center;
            }

            .middle-bar .info-section > div:nth-child(n+2) {
                display: none;
            }

            .navbar-brand img {
                max-width: 70px;
            }
        }

        @media (min-width: 1920px) {
            .abs-btn {
                border-radius: 20px;
            }

            .img-abs {
                top: 130px;
                right: 36%;
                width: 40px;
            }
        }
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
                        <a href="/" class="active">الرئيسية</a>
                    </li>
                    <li class="nav-item">
                        <a href="#">مسارات التدريب</a>
                    </li>
                    <li class="nav-item">
                        <a href="#">الدورات القصيرة</a>
                    </li>
                    <li class="nav-item">
                        <a href="#">عن المعهد</a>
                    </li>
                    <li class="nav-item">
                        <a href="#">الأخبار</a>
                    </li>
                    <li class="nav-item">
                        <a href="#">الأسئلة الشائعة</a>
                    </li>
                    <li class="nav-item">
                        <a href="#">تواصل معنا</a>
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
            <li><a href="/" class="active">الرئيسية</a></li>
            <li><a href="#">مسارات التدريب</a></li>
            <li><a href="#">الدورات القصيرة</a></li>
            <li><a href="#">عن المعهد</a></li>
            <li><a href="#">الأخبار</a></li>
            <li><a href="#">الأسئلة الشائعة</a></li>
            <li><a href="#">تواصل معنا</a></li>
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

    <!-- Landing Section -->
    <section class="container-fluid section-wrapper">
        <div class="row section-content">
            <div class="col-sm-12 col-md-6 d-flex flex-column justify-content-center">
                <p class="st-p">التدريب الذي يلبي احتياجاتك</p>
                <h1>
                    تدريب متميّز يفتح لك أبواب
                    <span style="color: var(--main-color);">الغد</span>
                </h1>
                <p class="nd-p">
                    بخبرة تمتد لأكثر من 10 أعوام، نصنع فرقًا حقيقيًا في حياة الأفراد والمؤسسات. نرشدك ببوصلة التدريب نحو تخصصك ومهنتك بثقة، لنكون بوابتك الأولى نحو مستقبل يواكب مستهدفات 2030.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn full-btn">ابدأ رحلتك التجريبية</button>
                    <button class="btn notfull-btn">استكشف برامجنا</button>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 left-sec">
                <img src="{{ asset('images/person.png') }}" alt="" class="main-img" />
                <button class="abs-btn">ابدأ التعلم الآن</button>
                <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs" />
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="row container-fluid section-container align-items-center">
        <div class="col-12 col-md-5">
            <p class="st-p">التدريب الذي يلبي احتياجاتك</p>
            <h1>من نحن</h1>
            <p class="nd-p">
                معهد الارتقاء العالي التدريبي هو جهة تدريبية معتمدة في المملكة العربية السعودية، نسعى لتقديم برامج تعليمية وتدريبية عالية الجودة تخدم احتياجات سوق العمل وتواكب التطور المهني والمعرفي.
            </p>
        </div>
        <div class="col-12 col-md-7 left-sec">
            <img src="{{ asset('images/Media.png') }}" class="main-img" />
            <button class="abs-btn">ابدأ التعلم الآن</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs" />
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="section-container">
        <div class="head text-center d-flex justify-content-center align-items-center flex-column position-relative">
            <p class="st-p">التدريب الذي يلبي احتياجاتك</p>
            <h1>لماذا تختارنا</h1>
            <div>
                <p class="nd-p">
                    نقدّم منظومة تدريبية متكاملة تجمع بين الجودة، المرونة، والتقنيات الحديثة لضمان أفضل تجربة تعليمية.
                </p>
                <button class="abs-btn d-none d-md-block">ابدأ التعلم الآن</button>
                <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs d-none d-md-block" />
            </div>
        </div>
        <div class="row text-center mt-4 container-fluid gx-4 gy-4 mx-auto">
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>دعم مستمر</h5>
                    <p>خدمة دعم فني على مدار الساعة تساعدك في تجاوز أي مشكلة تقنية.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>مدربون متخصصون</h5>
                    <p>يتولى التدريب نخبة من المدربين المعتمدين وأصحاب الخبرة الأكاديمية والمهنية.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>تعليم رقمي</h5>
                    <p>تجربة تعليمية سلسة، آمنة، ومتوافقة مع احتياجات المتدربين.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>تدريب معتمد</h5>
                    <p>معتمدة من الجهات الرسمية داخل المملكة، تضمن مسارًا موثوقًا لتطوير مهاراتك المهنية.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>شهادات رسمية</h5>
                    <p>يحصل المتدرب بعد إتمام البرامج على شهادات معتمدة رسميًا، تعزّز فرصه المهنية.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>طرق دفع متعددة</h5>
                    <p>نوفر منظومة دفع مرنة تناسب جميع احتياجات المتدربين.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>محتوى تفاعلي</h5>
                    <p>دروس فيديو، ملفات، تقييمات، واختبارات تعزز الفهم وتدعم مبدأ التعلّم بالممارسة.</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>مسارات واضحة</h5>
                    <p>خطط تعليمية مبنية على مسارات واضحة تمتد حتى 10 أرباع تدريبية.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Training Paths Section -->
    <section class="container-fluid py-5" style="background: #f3f4f6">
        <div class="head d-flex justify-content-center align-items-center flex-column py-5 position-relative text-center">
            <p class="st-p">التدريب الذي يلبي احتياجاتك</p>
            <h1>مسارات تدريبية شاملة لبناء مستقبلك</h1>
            <p class="phead">نوفر مسارات تدريبية تمتد لعامين ونصف عبر 10 أرباع تدريبية، إضافة إلى دورات قصيرة ومتخصصة تناسب مختلف الأهداف المهنية.</p>
            <button class="abs-btn d-none d-md-block">ابدأ التعلم الآن</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs d-none d-md-block" />
        </div>
        <div class="courses-container">
            @for ($i = 0; $i < 3; $i++)
            <div class="course-card card p-2">
                <img src="{{ asset('images/course.jpg') }}" class="rounded" />
                <h4 class="mt-2">تطوير المهارات الإدارية</h4>
                <p>أساسيات الإدارة الحديثة: قيادة، تخطيط، واتخاذ القرار العملي.</p>
                <div class="marks my-1 d-flex gap-2 flex-wrap">
                    <div class="st">
                        <i class="bi bi-exclamation-triangle"></i>
                        وسم
                    </div>
                    <div class="nd">
                        <i class="bi bi-exclamation-triangle"></i>
                        وسم
                    </div>
                    <div class="th">
                        <i class="bi bi-exclamation-triangle"></i>
                        وسم
                    </div>
                </div>
                <div class="d-flex gap-2 w-100 flex-wrap flex-sm-nowrap mt-2">
                    <button class="notfull-btn w-100">اطّلع على التفاصيل</button>
                    <button class="full-btn w-100">سجّل الآن</button>
                </div>
            </div>
            @endfor
        </div>
    </section>

    <!-- Upcoming Courses Section -->
    <section class="container-fluid py-5">
        <div class="head d-flex justify-content-center align-items-center flex-column py-5 position-relative text-center">
            <p class="st-p">التدريب الذي يلبي احتياجاتك</p>
            <h2>دورات تبدأ قريبًا — احجز مقعدك</h2>
            <p class="phead">اختر من بين مجموعة من الدورات المتخصصة التي تبدأ خلال الأسابيع القادمة.</p>
            <button class="notfull-btn mt-3">عرض جميع الدورات</button>
            <button class="abs-btn d-none d-md-block">ابدأ التعلم الآن</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs d-none d-md-block" />
        </div>
        <div class="courses-container">
            @for ($i = 0; $i < 3; $i++)
            <div class="course-card card p-2">
                <img src="{{ asset('images/course.jpg') }}" class="rounded" />
                <h4 class="mt-2">تطوير المهارات الإدارية</h4>
                <p>أساسيات الإدارة الحديثة: قيادة، تخطيط، واتخاذ القرار العملي.</p>
                <div class="marks my-1 d-flex gap-2 flex-wrap">
                    <div class="st">
                        <i class="bi bi-exclamation-triangle"></i>
                        وسم
                    </div>
                    <div class="nd">
                        <i class="bi bi-exclamation-triangle"></i>
                        وسم
                    </div>
                    <div class="th">
                        <i class="bi bi-exclamation-triangle"></i>
                        وسم
                    </div>
                </div>
                <div class="mt-2 d-flex gap-2 w-100 flex-wrap flex-sm-nowrap">
                    <button class="notfull-btn w-100">اطّلع على التفاصيل</button>
                    <button class="full-btn w-100">سجّل الآن</button>
                </div>
            </div>
            @endfor
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="container-fluid py-5">
        <div class="head d-flex justify-content-center align-items-center flex-column py-5 position-relative text-center">
            <p class="st-p">التدريب الذي يلبي احتياجاتك</p>
            <h2>كيف تعمل منظومتنا التدريبية؟</h2>
            <p>
                نظام تدريبي متكامل يضمن رحلة تعليمية واضحة، منظمة، وذات نتائج قابلة للقياس. من التسجيل وحتى الحصول على الشهادة، صمّمنا منظومتنا لتكون بسيطة، فعّالة، ومتوافقة مع معايير التدريب الأهلي في المملكة.
            </p>
            <button class="notfull-btn mt-3">عرض جميع الدورات</button>
            <button class="abs-btn d-none d-md-block">ابدأ التعلم الآن</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs d-none d-md-block" />
        </div>
        <div class="d-flex container-fluid content-wrapper">
            <div class="image-container">
                <img src="{{ asset('images/sixthMedia.png') }}" alt="" />
            </div>
            <div class="right-sec-steps">
                <div class="section-item">
                    <h4>التسجيل والبدء</h4>
                    <p class="nd-p">
                        ابدأ رحلتك التعليمية بسهولة عبر
                        <a href="#" style="color: var(--main-color);">إنشاء حساب</a>
                        أو
                        <a href="#" style="color: var(--main-color);">تسجيل الدخول</a>
                        من خلال
                        <a href="#" style="color: var(--main-color);">نفاذ</a>
                        ، ثم اكتشف البرامج والمسارات التي صُمّمت لتناسب أهدافك وطموحاتك.
                    </p>
                </div>

                <div class="section-item">
                    <h4>اختيار البرنامج المناسب لك</h4>
                    <p class="nd-p">
                        سواء كنت تبحث عن مسار أكاديمي يمتد لعامين ونصف (10 أرباع)، أو دورة قصيرة تمتد لأسابيع أو شهور… ستجد ما يناسب أهدافك وطموحاتك المهنية.
                    </p>
                </div>

                <div class="section-item">
                    <h4>التعلّم والمتابعة</h4>
                    <p class="nd-p">
                        ادرس عبر محتوى مرئي ومنظم، مع نظام حضور وغياب، وتقدم تدريبي واضح، وتواصل مباشر مع المدربين، لضمان تجربة تعلم متكاملة وسلسة.
                    </p>
                </div>

                <div class="section-item">
                    <h4>التقييم والحصول على الشهادة</h4>
                    <p class="nd-p">
                        بعد إكمال متطلباتك التدريبية يتم تقييمك واعتماد إنجازك، ثم إصدار شهادتك الرقمية المعتمدة لتبدأ خطوتك المهنية بثقة.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- App Section -->
    <section class="container-fluid pt-5 pb-5 app-section">
        <div class="d-flex justify-content-between main-wrapper flex-wrap" style="max-width: 1400px; margin: 0 auto; gap: 2rem;">
            <div class="right-sec pe-3" style="flex: 1; min-width: 300px; padding: 3rem 0;">
                <p class="st-p" style="background: white;">التدريب الذي يلبي احتياجاتك</p>
                <h1>التطبيق الذي يرافقك في كل خطوة من رحلتك التدريبية</h1>
                <p class="nd-p">
                    يقدّم تطبيقنا تجربة تعليمية متكاملة تتيح لك متابعة دوراتك، حضور المحاضرات، معرفة تقدمك، والتواصل مباشرة مع المدربين — كل ذلك من مكان واحد وبواجهة سهلة الاستخدام.
                </p>
                <div class="store-buttons" dir="ltr">
                    <!-- Huawei AppGallery -->
                    <a href="#" class="store-btn">
                        <img src="{{ asset('images/huawei-appgallery-thumb.png') }}" style="width: 40px; border-radius: 10px" />
                        <div class="text">
                            <span class="small">EXPLORE IT ON</span>
                            <span class="big">AppGallery</span>
                        </div>
                    </a>

                    <!-- Apple App Store -->
                    <a href="#" class="store-btn">
                        <i class="bi bi-apple" style="font-size: 24px;"></i>
                        <div class="text">
                            <span class="small">Download on the</span>
                            <span class="big">App Store</span>
                        </div>
                    </a>

                    <!-- Google Play -->
                    <a href="#" class="store-btn">
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
            <div class="d-flex left-sec" style="flex: 1; min-width: 300px; position: relative;">
                <div class="st-mobile">
                    <img src="{{ asset('images/phone1.png') }}" alt="" style="width: 250px; height: 500px; position: relative; z-index: 50; left: -140px;" />
                </div>
                <div class="nd-mobile">
                    <img src="{{ asset('images/phone2.png') }}" alt="" style="width: 250px; height: 400px; position: relative; top: 100px; z-index: 30;" />
                </div>
            </div>
        </div>
    </section>

    <!-- Trainers Section -->
    <section class="container-fluid section-container p-5">
        <div class="head text-center d-flex justify-content-center align-items-center flex-column position-relative">
            <p class="st-p">التدريب الذي يلبي احتياجاتك</p>
            <h1>ارتقِ بمهاراتك مع أفضل المدربين المعتمدين</h1>
            <p class="head-desc">
                نقدّم لك منظومة تدريبية يشرف عليها نخبة من المدربين السعوديين أصحاب الخبرات الأكاديمية والمهنية، لتضمن تعلّمًا فعّالًا يُمكّنك من تحقيق أهدافك بثقة ومنهجية.
            </p>
            <button class="abs-btn d-none d-md-block">ابدأ التعلم الآن</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs d-none d-md-block" />
        </div>

        <div class="row mt-4">
            <!-- Right Column -->
            <div class="col-12 col-md-3 col-lg-3 d-flex flex-column gap-3">
                <div class="trainers-card">
                    <div class="imgs d-flex flex-column align-items-center">
                        <div class="top-img">
                            <img src="{{ asset('images/top.png') }}" alt="" />
                        </div>
                        <div class="bottom-img d-flex gap-3 mt-2">
                            <img src="{{ asset('images/left.png') }}" alt="" />
                            <img src="{{ asset('images/right.png') }}" alt="" />
                        </div>
                    </div>
                    <div class="text mt-3">
                        <h4>خبرة تقودك للنجاح</h4>
                        <p>كل مدرب يمتلك خبرة أكاديمية ومهنية في مجاله، لضمان جودة التدريب وعمق الفائدة.</p>
                    </div>
                </div>
                <div class="trainers-card">
                    <div class="imgs d-flex flex-column align-items-center">
                        <div class="top-img">
                            <img src="{{ asset('images/top.png') }}" alt="" />
                        </div>
                        <div class="bottom-img d-flex gap-3 mt-2">
                            <img src="{{ asset('images/left.png') }}" alt="" />
                            <img src="{{ asset('images/right.png') }}" alt="" />
                        </div>
                    </div>
                    <div class="text mt-3">
                        <h4>محتوى تدريبي معتمد</h4>
                        <p>برامج ومسارات مقدمة من مدربين معتمدين، مبنية على معايير التدريب الأهلي داخل المملكة.</p>
                    </div>
                </div>
            </div>

            <!-- Center Column -->
            <div class="col-12 col-md-6 col-lg-6">
                <div class="center-card h-100">
                    <div class="img">
                        <img src="{{ asset('images/center.jpg') }}" alt="" />
                    </div>
                    <div class="btns d-flex gap-4 justify-content-center mt-4">
                        <button class="full-btn">انضم كمدرب معنا</button>
                        <button class="notfull-btn">شروط الانضمام</button>
                    </div>
                </div>
            </div>

            <!-- Left Column -->
            <div class="col-12 col-md-3 col-lg-3 d-flex flex-column gap-3">
                <div class="trainers-card">
                    <div class="imgs d-flex flex-column align-items-center">
                        <div class="top-img">
                            <img src="{{ asset('images/top.png') }}" alt="" />
                        </div>
                        <div class="bottom-img d-flex gap-3 mt-2">
                            <img src="{{ asset('images/left.png') }}" alt="" />
                            <img src="{{ asset('images/right.png') }}" alt="" />
                        </div>
                    </div>
                    <div class="text mt-3">
                        <h4>تعلم مرن ومتعدد الأساليب</h4>
                        <p>محتوى مباشر ومسجّل، تم تصميمه ليواكب أنماط التعلم المختلفة ويمنحك مرونة كاملة.</p>
                    </div>
                </div>
                <div class="trainers-card">
                    <div class="imgs d-flex flex-column align-items-center">
                        <div class="top-img">
                            <img src="{{ asset('images/top.png') }}" alt="" />
                        </div>
                        <div class="bottom-img d-flex gap-3 mt-2">
                            <img src="{{ asset('images/left.png') }}" alt="" />
                            <img src="{{ asset('images/right.png') }}" alt="" />
                        </div>
                    </div>
                    <div class="text mt-3">
                        <h4>تفاعل مباشر مع المدربين</h4>
                        <p>إمكانية التواصل، طرح الأسئلة، واستلام التغذية الراجعة لمساعدتك على التقدم بثقة.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories Section -->
    <section class="container-fluid section-container p-4 feedback-section">
        <div class="head text-center d-flex justify-content-center align-items-center flex-column position-relative">
            <p class="st-p">التدريب الذي يلبي احتياجاتك</p>
            <h1>نجاحات نفتخر بها</h1>
            <p class="head-desc">
                مئات المتدربين طوروا مسيرتهم المهنية وانطلقوا نحو فرص جديدة بفضل برامجنا المعتمدة.
            </p>
            <button class="notfull-btn">عرض جميع القصص</button>
            <button class="abs-btn d-none d-md-block">ابدأ التعلم الآن</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs d-none d-md-block" />
        </div>

        <div class="feedback d-flex mt-5">
            <div class="img">
                <img src="{{ asset('images/avatar.png') }}" />
            </div>
            <div class="text pt-5 me-5">
                <i class="bi bi-quote"></i>
                <h3 class="fw-bold">
                    رحلة تدريبية مميزة وواضحة من البداية للنهاية، متابعة التيرم والدروس كانت سهلة جدًا، والتطبيق ساعدني أتابع تقدمي بشكل يومي.
                </h3>
                <p class="nd-p">
                    سلمان .م - مسار الحاسب وتقنية المعلومات
                </p>
                <div class="imgs-av d-flex gap-3">
                    <img src="{{ asset('images/avatar.png') }}" class="active" />
                    <img src="{{ asset('images/person.png') }}" />
                    <img src="{{ asset('images/right.png') }}" />
                    <img src="{{ asset('images/Media.png') }}" />
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="container-fluid section-container p-5">
        <div class="head text-center d-flex justify-content-center align-items-center flex-column position-relative">
            <p class="st-p">التدريب الذي يلبي احتياجاتك</p>
            <h1>الأسئلة الأكثر شيوعًا حول برامجنا ومنصتنا</h1>
            <p class="head-desc">
                نقدّم لك إجابات شاملة لأكثر الأسئلة التي قد تخطر على بالك حول التسجيل، البرامج، التيرمات، الدورات، والدعم الفني، لتسهيل تجربتك التعليمية معنا.
            </p>
            <button class="notfull-btn">عرض جميع الأسئلة</button>
            <button class="abs-btn d-none d-md-block">ابدأ التعلم الآن</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs d-none d-md-block" />
        </div>
        <div class="accordion mt-5" id="accordionExample">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        كيف أسجل في المعهد؟
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        يمكنك التسجيل بسهولة عبر حساب نفاذ أو إنشاء حساب داخلي. بعد التسجيل، يمكنك اختيار المسار الأكاديمي أو الدورات القصيرة المناسبة لك.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        كيف أسجل في المعهد؟
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        يمكنك التسجيل بسهولة عبر حساب نفاذ أو إنشاء حساب داخلي. بعد التسجيل، يمكنك اختيار المسار الأكاديمي أو الدورات القصيرة المناسبة لك.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        كيف أسجل في المعهد؟
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        يمكنك التسجيل بسهولة عبر حساب نفاذ أو إنشاء حساب داخلي. بعد التسجيل، يمكنك اختيار المسار الأكاديمي أو الدورات القصيرة المناسبة لك.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        كيف أسجل في المعهد؟
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        يمكنك التسجيل بسهولة عبر حساب نفاذ أو إنشاء حساب داخلي. بعد التسجيل، يمكنك اختيار المسار الأكاديمي أو الدورات القصيرة المناسبة لك.
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                    <!-- Huawei AppGallery -->
                    <a href="#" class="footer-store-btn">
                        <img src="{{ asset('images/huawei-appgallery-thumb.png') }}" style="width: 40px; border-radius: 10px" />
                        <div class="text">
                            <span class="small">EXPLORE IT ON</span>
                            <span class="big">AppGallery</span>
                        </div>
                    </a>

                    <!-- Apple App Store -->
                    <a href="#" class="footer-store-btn">
                        <i class="bi bi-apple" style="font-size: 24px;"></i>
                        <div class="text">
                            <span class="small">Download on the</span>
                            <span class="big">App Store</span>
                        </div>
                    </a>

                    <!-- Google Play -->
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
                    <a href="/">الرئيسية</a>
                    <a href="#">عن المعهد</a>
                    <a href="#">مسارات التدريب</a>
                    <a href="#">الدورات القادمة</a>
                    <a href="#">المدربين</a>
                    <a href="#">الأخبار</a>
                    <a href="#">تواصل معنا</a>
                </div>
                <div class="col-lg-4 col-md-6 col-12 d-flex flex-column gap-3 footer-section">
                    <h5>خدمات المعهد</h5>
                    <hr />
                    <a href="#">نظام التيرمات والبرامج المهنية</a>
                    <a href="#">الدورات القصيرة</a>
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
                        <div>
                            <i class="bi bi-telephone"></i>
                            رقم الجوال
                        </div>
                        <div>
                            9200343222
                            <i class="bi bi-copy"></i>
                        </div>
                    </a>
                    <a href="#">
                        <div>
                            <i class="bi bi-envelope"></i>
                            البريد الإلكتروني
                        </div>
                        <div>
                            help@company.sa
                            <i class="bi bi-copy"></i>
                        </div>
                    </a>
                    <a href="#">
                        <div>
                            <i class="bi bi-geo-alt-fill"></i>
                            الموقع
                        </div>
                        <div>
                            الرياض
                            <i class="bi bi-link-45deg"></i>
                        </div>
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
                <p>معهد الارتقاء العالي للتدريب. جميع الحقوق محفوظة &copy; 2024.</p>
            </div>
            <div class="footer-meta">
                <p>تاريخ آخر تعديل: 04/12/2020</p>
                <p>تم تطويره وصيانته بواسطة [أدخل اسم الجهة]</p>
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

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeMobileMenu();
            }
        });
    </script>
</body>
</html>
