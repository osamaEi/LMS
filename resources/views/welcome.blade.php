<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Al-Ertiqaa High Institute for Training') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/Vector.png') }}" />

    <!-- Bootstrap CSS -->
    @if(app()->getLocale() == 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

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
            @if(app()->getLocale() == 'ar')
            font-family: 'Cairo', sans-serif !important;
            @else
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
            @endif
            box-sizing: border-box;
        }

        body {
            @if(app()->getLocale() == 'ar')
            font-family: 'Cairo', sans-serif !important;
            @else
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            @endif
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
            {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 0;
            width: 383px;
            height: 100vh;
            background: #fff;
            z-index: 9999;
            overflow-y: auto;
            box-shadow: {{ app()->getLocale() == 'ar' ? '-5px' : '5px' }} 0 15px rgba(0, 0, 0, 0.3);
            flex-direction: column;
        }

        .mobile-menu.active {
            display: flex;
            {{ app()->getLocale() == 'ar' ? 'padding-right' : 'padding-left' }}: 117px;
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
            {{ app()->getLocale() == 'ar' ? 'padding-right' : 'padding-left' }}: 25px;
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
            justify-content: {{ app()->getLocale() == 'ar' ? 'end' : 'start' }};
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
            {{ app()->getLocale() == 'ar' ? 'right: 15%' : 'right: 15%' }};
            background-color: var(--main-color);
            color: white;
            padding: 7px 15px;
            border: none;
            border-radius: 5px;
            white-space: nowrap;
        }

        .img-abs {
            position: absolute;
            top: 37%;

            {{ app()->getLocale() == 'ar' ? 'right: 26%' : 'right: 30%' }};
            max-width: clamp(30px, 5vw, 50px);
            height: auto;
        }
        .coming-abs {
                top: {{ app()->getLocale() == 'ar' ? '37%' : '27%' }};
               right: {{ app()->getLocale() == 'ar' ? '26%' : '28%' }};

        }
        .land-abs {
            top: 30%;
            right: 40%;
        }
        .train-abs{
            top: 30%;

        }
        .How-abs{
            top: {{ app()->getLocale() == 'ar' ? '20%' : '20%' }} ;
            right:{{ app()->getLocale() == 'ar' ? '20%' : '15%' }} ;
        }
        .How-abs-btn{
            top: {{ app()->getLocale() == 'ar' ? '5%' : '15%' }} ;
            right: {{ app()->getLocale() == 'ar' ? '5%' : '2%' }};
        }
        .trainers-abs{
            top: {{ app()->getLocale() == 'ar' ? '20%' : '23%' }};

            right:{{ app()->getLocale() == 'ar' ? '17%' : '16%' }};
        }
        .trainers-abs-btn{
            top: 2%;
            right: {{ app()->getLocale() == 'ar' ? '5%' : '2%' }};
        }
        .faq-abs{
            top: {{ app()->getLocale() == 'ar' ? '25%' : '17%' }};
            right: {{ app()->getLocale() == 'ar' ? '19%' : '14%' }};
        }
        .faq-abs-btn{
            top: {{ app()->getLocale() == 'ar' ? '5%' : '0%' }};
            right: {{ app()->getLocale() == 'ar' ? '5%' : '0%' }};
        }
        .fedback-abs{
            top: {{ app()->getLocale() == 'ar' ? '20%' : '15%' }};
            right: {{ app()->getLocale() == 'ar' ? '22%' : '18%' }};
        }
        .fedback-abs-btn{
            top: {{ app()->getLocale() == 'ar' ? '10%' : '5%' }};
            right: {{ app()->getLocale() == 'ar' ? '7%' : '3%' }};
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
            flex-direction: {{ app()->getLocale() == 'ar' ? 'row-reverse' : 'row' }};
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
            {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: -22px;
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
            {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: {{ app()->getLocale() == 'ar' ? '-130px' : '-130px' }};
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
            {{ app()->getLocale() == 'ar' ? 'margin-left: 0; margin-right: auto;' : 'margin-right: 0; margin-left: auto;' }}
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
            transform: translateX({{ app()->getLocale() == 'ar' ? '-5px' : '5px' }});
            {{ app()->getLocale() == 'ar' ? 'padding-right' : 'padding-left' }}: 5px;
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
                {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: -15px;
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
                {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: -20px;
                top: -30px;
                font-size: 40px;
            }

            .footer-store-buttons {
                grid-template-columns: 1fr;
            }

            .foot-foot {
                flex-direction: column;
                align-items: flex-start;
                text-align: {{ app()->getLocale() == 'ar' ? 'right' : 'left' }};
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
                {{ app()->getLocale() == 'ar' ? 'right' : 'left' }}: 36%;
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
            <p class="top-text mb-0">{{ __('Official government site registered with the Digital Government Authority') }}</p>
        </div>

        <!-- Container 2 -->
        <div class="container-fluid middle-bar bg-gray d-flex justify-content-between align-items-center">
            <div class="d-flex gap-4 info-section">
                <div class="d-flex gap-1 align-items-center">
                    <i class="bi bi-cloud"></i>
                    <p class="mb-0">{{ __('Cloudy') }}</p>
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
                    <p class="mb-0">{{ __('Riyadh') }}</p>
                </div>
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
                        <a href="{{ route('home') }}" class="active">{{ __('Home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('training-paths') }}">{{ __('Training Paths') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('short-courses') }}">{{ __('Short Courses') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('about') }}">{{ __('About Us') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('news') }}">{{ __('News') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('faq') }}">{{ __('FAQ') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('contact') }}">{{ __('Contact Us') }}</a>
                    </li>
                </ul>

                <div class="d-flex gap-2">
                    <button class="btn btn-outline" type="button">
                        <i class="bi bi-search"></i> {{ __('Search') }}
                    </button>
                    @if(app()->getLocale() == 'ar')
                        <a href="{{ route('lang.switch', 'en') }}" class="btn btn-outline">
                            <i class="bi bi-translate"></i> English
                        </a>
                    @else
                        <a href="{{ route('lang.switch', 'ar') }}" class="btn btn-outline">
                            <i class="bi bi-translate"></i> العربية
                        </a>
                    @endif
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline">
                                <i class="bi bi-person"></i> {{ __('Dashboard') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline">
                                <i class="bi bi-person"></i> {{ __('Login') }}
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
            <li><a href="{{ route('home') }}" class="active">{{ __('Home') }}</a></li>
            <li><a href="{{ route('training-paths') }}">{{ __('Training Paths') }}</a></li>
            <li><a href="{{ route('short-courses') }}">{{ __('Short Courses') }}</a></li>
            <li><a href="{{ route('about') }}">{{ __('About Us') }}</a></li>
            <li><a href="{{ route('news') }}">{{ __('News') }}</a></li>
            <li><a href="{{ route('faq') }}">{{ __('FAQ') }}</a></li>
            <li><a href="{{ route('contact') }}">{{ __('Contact Us') }}</a></li>
        </ul>
        <div class="mobile-menu-buttons">
            <button class="btn btn-outline" type="button">
                <i class="bi bi-search"></i> {{ __('Search') }}
            </button>
            @if(app()->getLocale() == 'ar')
                <a href="{{ route('lang.switch', 'en') }}" class="btn btn-outline">
                    <i class="bi bi-translate"></i> English
                </a>
            @else
                <a href="{{ route('lang.switch', 'ar') }}" class="btn btn-outline">
                    <i class="bi bi-translate"></i> العربية
                </a>
            @endif
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline">
                        <i class="bi bi-person"></i> {{ __('Dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">
                        <i class="bi bi-person"></i> {{ __('Login') }}
                    </a>
                @endauth
            @endif
        </div>
    </div>

    <!-- Landing Section -->
    <section class="container-fluid section-wrapper">
        <div class="row section-content">
            <div class="col-sm-12 col-md-6 d-flex flex-column justify-content-center">
                <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
                <h1>
                    {{ __('Distinguished training opens doors to') }}
                    <span style="color: var(--main-color);">{{ __('tomorrow') }}</span>
                </h1>
                <p class="nd-p">
                    {{ __('With over 10 years of experience, we make a real difference in the lives of individuals and organizations. We guide you with the training compass towards your specialization and profession with confidence, to be your first gateway to a future that keeps pace with Vision 2030 targets.') }}
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn full-btn">{{ __('Start Your Trial Journey') }}</button>
                    <button class="btn notfull-btn">{{ __('Explore Our Programs') }}</button>
                </div>
            </div>
            <div class="col-sm-12 col-md-6 left-sec">
                <img src="{{ asset('images/person.png') }}" alt="" class="main-img" />
                <button class="abs-btn">{{ __('Start Learning Now') }}</button>
                <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs land-abs" />
            </div>
        </div>
    </section>

    <!-- About Section -->


    <!-- Why Choose Us Section -->
    <section class="section-container">
        <div class="head text-center d-flex justify-content-center align-items-center flex-column position-relative">
            <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
            <h1>{{ __('Why Choose Us') }}</h1>
            <div>
                <p class="nd-p">
                    {{ __('We offer an integrated training system that combines quality, flexibility, and modern technologies to ensure the best educational experience.') }}
                </p>
                <button class="abs-btn d-none d-md-block">{{ __('Start Learning Now') }}</button>
                <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs d-none d-md-block" />
            </div>
        </div>
        <div class="row text-center mt-4 container-fluid gx-4 gy-4 mx-auto">
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>{{ __('Continuous Support') }}</h5>
                    <p>{{ __('24/7 technical support service helps you overcome any technical problem.') }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>{{ __('Specialized Trainers') }}</h5>
                    <p>{{ __('Training is conducted by elite certified trainers with academic and professional experience.') }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>{{ __('Digital Education') }}</h5>
                    <p>{{ __('A smooth, secure educational experience compatible with trainees needs.') }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>{{ __('Accredited Training') }}</h5>
                    <p>{{ __('Accredited by official authorities within the Kingdom, ensuring a reliable path for developing your professional skills.') }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>{{ __('Official Certificates') }}</h5>
                    <p>{{ __('After completing programs, trainees receive officially accredited certificates that enhance their career opportunities.') }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>{{ __('Multiple Payment Methods') }}</h5>
                    <p>{{ __('We provide a flexible payment system that suits all trainees needs.') }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>{{ __('Interactive Content') }}</h5>
                    <p>{{ __('Video lessons, files, assessments, and tests that enhance understanding and support learning by practice.') }}</p>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card">
                    <i class="bi bi-shield-fill-check"></i>
                    <h5>{{ __('Clear Paths') }}</h5>
                    <p>{{ __('Educational plans built on clear paths extending up to 10 training quarters.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Training Paths Section -->
    <section class="container-fluid py-5" style="background: #f3f4f6">
        <div class="head d-flex justify-content-center align-items-center flex-column py-5 position-relative text-center">
            <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
            <h1>{{ __('Comprehensive training paths to build your future') }}</h1>
            <p class="phead">{{ __('We provide training paths spanning two and a half years through 10 training quarters, plus short and specialized courses for various professional goals.') }}</p>
            <button class="abs-btn d-none d-md-block">{{ __('Start Learning Now') }}</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs train-abs d-none d-md-block" />
        </div>
        <div class="courses-container">
            @for ($i = 0; $i < 3; $i++)
            <div class="course-card card p-2">
                <img src="{{ asset('images/course.jpg') }}" class="rounded" />
                <h4 class="mt-2">{{ __('Management Skills Development') }}</h4>
                <p>{{ __('Modern management fundamentals: leadership, planning, and practical decision-making.') }}</p>
                <div class="marks my-1 d-flex gap-2 flex-wrap">
                    <div class="st">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ __('Tag') }}
                    </div>
                    <div class="nd">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ __('Tag') }}
                    </div>
                    <div class="th">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ __('Tag') }}
                    </div>
                </div>
                <div class="d-flex gap-2 w-100 flex-wrap flex-sm-nowrap mt-2">
                    <button class="notfull-btn w-100">{{ __('View Details') }}</button>
                    <button class="full-btn w-100">{{ __('Register Now') }}</button>
                </div>
            </div>
            @endfor
        </div>
    </section>

    <!-- Upcoming Courses Section -->
    <section class="container-fluid py-5">
        <div class="head d-flex justify-content-center align-items-center flex-column py-5 position-relative text-center">
            <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
            <h2>{{ __('Courses starting soon — Reserve your seat') }}</h2>
            <p class="phead">{{ __('Choose from a variety of specialized courses starting in the coming weeks.') }}</p>
            <button class="notfull-btn mt-3">{{ __('View All Courses') }}</button>
            <button class="abs-btn d-none d-md-block">{{ __('Start Learning Now') }}</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs coming-abs d-none d-md-block" />
        </div>
        <div class="courses-container">
            @for ($i = 0; $i < 3; $i++)
            <div class="course-card card p-2">
                <img src="{{ asset('images/course.jpg') }}" class="rounded" />
                <h4 class="mt-2">{{ __('Management Skills Development') }}</h4>
                <p>{{ __('Modern management fundamentals: leadership, planning, and practical decision-making.') }}</p>
                <div class="marks my-1 d-flex gap-2 flex-wrap">
                    <div class="st">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ __('Tag') }}
                    </div>
                    <div class="nd">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ __('Tag') }}
                    </div>
                    <div class="th">
                        <i class="bi bi-exclamation-triangle"></i>
                        {{ __('Tag') }}
                    </div>
                </div>
                <div class="mt-2 d-flex gap-2 w-100 flex-wrap flex-sm-nowrap">
                    <button class="notfull-btn w-100">{{ __('View Details') }}</button>
                    <button class="full-btn w-100">{{ __('Register Now') }}</button>
                </div>
            </div>
            @endfor
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="container-fluid py-5 " >
        <div class="head d-flex justify-content-center align-items-center flex-column py-5 position-relative text-center">
            <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
            <h2>{{ __('How does our training system work?') }}</h2>
            <p>
                {{ __('An integrated training system that ensures a clear, organized educational journey with measurable results. From registration to certification, we designed our system to be simple, effective, and compliant with private training standards in the Kingdom.') }}
            </p>
            <button class="notfull-btn mt-3">{{ __('View All Courses') }}</button>
            <button class="abs-btn d-none d-md-block How-abs-btn">{{ __('Start Learning Now') }}</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs How-abs d-none d-md-block" />
        </div>
        <div class="row container-fluid content-wrapper How-sec {{ app()->getLocale() == 'ar' ?  '': 'flex-row-reverse' }}">

            <div class="col-sm-12 col-md-6 image-container">
                    <img src="{{ asset('images/sixthMedia.png') }}" alt="" />
            </div>
            <div class="col-sm-12 col-md-6 right-sec-steps">
                    <div class="section-item">
                        <h4>{{ __('Registration and Getting Started') }}</h4>
                        <p class="nd-p">
                            {{ __('Start your educational journey easily by creating an account or logging in through Nafath, then discover programs and paths designed to suit your goals and aspirations.') }}
                        </p>
                    </div>

                    <div class="section-item">
                        <h4>{{ __('Choosing the Right Program for You') }}</h4>
                        <p class="nd-p">
                            {{ __('Whether youre looking for an academic path spanning two and a half years (10 quarters), or a short course lasting weeks or months... you will find what suits your goals and professional aspirations.') }}
                        </p>
                    </div>

                    <div class="section-item">
                        <h4>{{ __('Learning and Follow-up') }}</h4>
                        <p class="nd-p">
                            {{ __('Study through visual and organized content, with an attendance system, clear training progress, and direct communication with trainers, ensuring an integrated and smooth learning experience.') }}
                        </p>
                    </div>

                    <div class="section-item">
                        <h4>{{ __('Assessment and Certification') }}</h4>
                        <p class="nd-p">
                            {{ __('After completing your training requirements, you will be evaluated and your achievement accredited, then your accredited digital certificate will be issued to start your professional step with confidence.') }}
                        </p>
                    </div>
            </div>


        </div>

    </section>

    <!-- App Section -->
    <section class="container-fluid pt-5 pb-5 app-section">
        <div class="d-flex justify-content-between main-wrapper flex-wrap" style="max-width: 1400px; margin: 0 auto; gap: 2rem;">
            <div class="right-sec pe-3" style="flex: 1; min-width: 300px; padding: 3rem 0;">
                <p class="st-p" style="background: white;">{{ __('Training That Meets Your Needs') }}</p>
                <h1>{{ __('The app that accompanies you at every step of your training journey') }}</h1>
                <p class="nd-p">
                    {{ __('Our app provides a comprehensive educational experience that allows you to follow your courses, attend lectures, track your progress, and communicate directly with trainers — all from one place with an easy-to-use interface.') }}
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
            <div class="d-flex left-sec align-items-end" style="flex: 1; min-width: 300px; position: relative;">
                <div class="st-mobile">
                    <img src="{{ asset('images/phone1.png') }}" alt="" style="width: 250px; height: 500px; position: relative; z-index: 50; {{ app()->getLocale() == 'ar' ? 'left: -140px;' : 'right: -140px;' }}" />
                </div>
                <div class="nd-mobile">
                    <img src="{{ asset('images/phone2.png') }}" alt="" style="width: 250px; height: 400px; position: relative; z-index: 30;" />
                </div>
            </div>
        </div>
    </section>

    <!-- Trainers Section -->
    <section class="container-fluid section-container p-5">
        <div class="head text-center d-flex justify-content-center align-items-center flex-column position-relative">
            <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
            <h1>{{ __('Elevate your skills with the best certified trainers') }}</h1>
            <p class="head-desc">
                {{ __('We offer you a training system supervised by elite Saudi trainers with academic and professional expertise, to ensure effective learning that enables you to achieve your goals with confidence and methodology.') }}
            </p>
            <button class="abs-btn d-none d-md-block trainers-abs-btn">{{ __('Start Learning Now') }}</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs trainers-abs d-none d-md-block" />
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
                        <h4>{{ __('Experience leads you to success') }}</h4>
                        <p>{{ __('Each trainer has academic and professional experience in their field, ensuring training quality and depth of benefit.') }}</p>
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
                        <h4>{{ __('Accredited training content') }}</h4>
                        <p>{{ __('Programs and paths provided by certified trainers, built on private training standards within the Kingdom.') }}</p>
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
                        <button class="full-btn">{{ __('Join us as a trainer') }}</button>
                        <button class="notfull-btn">{{ __('Joining requirements') }}</button>
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
                        <h4>{{ __('Flexible and diverse learning') }}</h4>
                        <p>{{ __('Live and recorded content, designed to keep pace with different learning styles and give you complete flexibility.') }}</p>
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
                        <h4>{{ __('Direct interaction with trainers') }}</h4>
                        <p>{{ __('Ability to communicate, ask questions, and receive feedback to help you progress confidently.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Stories Section -->
    <section class="container-fluid section-container p-4 feedback-section">
        <div class="head text-center d-flex justify-content-center align-items-center flex-column position-relative">
            <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
            <h1>{{ __('Success Stories We Are Proud Of') }}</h1>
            <p class="head-desc">
                {{ __('Hundreds of trainees have developed their careers and launched into new opportunities thanks to our accredited programs.') }}
            </p>
            <button class="notfull-btn">{{ __('View All Stories') }}</button>
            <button class="abs-btn d-none d-md-block fedback-abs-btn">{{ __('Start Learning Now') }}</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs fedback-abs d-none d-md-block" />
        </div>

        <div class="feedback d-flex mt-5">
            <div class="img">
                <img id="mainTestimonialImg" src="{{ asset('images/avatar.png') }}" />
            </div>
            <div class="text pt-5 {{ app()->getLocale() == 'ar' ? 'me-5' : 'ms-5' }}">
                <i class="bi bi-quote"></i>
                <h3 id="testimonialText" class="fw-bold">
                    {{ __('A distinctive and clear training journey from beginning to end, following the term and lessons was very easy, and the app helped me track my progress daily.') }}
                </h3>
                <p id="testimonialAuthor" class="nd-p">
                    {{ __('Salman M. - Computer and IT Path') }}
                </p>
                <div class="imgs-av d-flex gap-3">
                    <img src="{{ asset('images/avatar.png') }}" class="active" data-index="0" onclick="changeTestimonial(0)" />
                    <img src="{{ asset('images/person.png') }}" data-index="1" onclick="changeTestimonial(1)" />
                    <img src="{{ asset('images/right.png') }}" data-index="2" onclick="changeTestimonial(2)" />
                    <img src="{{ asset('images/Media.png') }}" data-index="3" onclick="changeTestimonial(3)" />
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="container-fluid section-container p-5">
        <div class="head text-center d-flex justify-content-center align-items-center flex-column position-relative">
            <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
            <h1>{{ __('Frequently Asked Questions About Our Programs and Platform') }}</h1>
            <p class="head-desc">
                {{ __('We provide comprehensive answers to the most common questions about registration, programs, terms, courses, and technical support, to facilitate your educational experience with us.') }}
            </p>
            <button class="notfull-btn">{{ __('View All Questions') }}</button>
            <button class="abs-btn d-none d-md-block faq-abs-btn">{{ __('Start Learning Now') }}</button>
            <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs faq-abs d-none d-md-block" />
        </div>
        <div class="accordion mt-5" id="accordionExample">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        {{ __('How do I register at the institute?') }}
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        {{ __('You can easily register via Nafath account or create an internal account. After registration, you can choose the academic path or short courses that suit you.') }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        {{ __('What payment methods are available?') }}
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        {{ __('We provide several payment methods including: credit cards, Mada, bank transfer, and payment upon registration. We also offer installment options for long paths.') }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        {{ __('Are the certificates accredited?') }}
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        {{ __('Yes, all our certificates are accredited by the Technical and Vocational Training Corporation and recognized in the Saudi labor market.') }}
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        {{ __('Is there remote training?') }}
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        {{ __('Yes, we provide remote training options for most of our courses and training paths, with all educational materials and resources available electronically.') }}
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
                <h2>{{ __('Al-Ertiqaa High Institute for Training') }}</h2>
                <p class="intro-text">
                    {{ __('An accredited training institute offering professional development programs and educational paths for over 10 years. We help students and trainees acquire future skills, providing accredited programs aligned with Saudi Vision 2030, supervised by elite trainers and experts to ensure training quality and outcomes.') }}
                </p>
            </div>
            <div class="mobile-apps">
                <p class="apps-title">{{ __('Mobile Apps') }}</p>
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
                    <h5>{{ __('Important Links') }}</h5>
                    <hr />
                    <a href="{{ route('home') }}">{{ __('Home') }}</a>
                    <a href="{{ route('about') }}">{{ __('About Us') }}</a>
                    <a href="{{ route('training-paths') }}">{{ __('Training Paths') }}</a>
                    <a href="{{ route('short-courses') }}">{{ __('Upcoming Courses') }}</a>
                    <a href="#">{{ __('Trainers') }}</a>
                    <a href="{{ route('news') }}">{{ __('News') }}</a>
                    <a href="{{ route('contact') }}">{{ __('Contact Us') }}</a>
                </div>
                <div class="col-lg-4 col-md-6 col-12 d-flex flex-column gap-3 footer-section">
                    <h5>{{ __('Institute Services') }}</h5>
                    <hr />
                    <a href="#">{{ __('Term System and Professional Programs') }}</a>
                    <a href="{{ route('short-courses') }}">{{ __('Short Courses') }}</a>
                    <a href="#">{{ __('Remote Training') }}</a>
                    <a href="#">{{ __('Certificate Accreditation') }}</a>
                    <a href="#">{{ __('Student Registration') }}</a>
                    <a href="#">{{ __('Trainer Enrollment') }}</a>
                    <a href="{{ route('faq') }}">{{ __('Technical Support and Follow-up') }}</a>
                </div>
                <div class="col-lg-4 col-md-12 col-12 d-flex flex-column gap-3 footer-section">
                    <h5>{{ __('Contact Information') }}</h5>
                    <hr />
                    <a href="#">
                        <div>
                            <i class="bi bi-telephone"></i>
                            {{ __('Phone Number') }}
                        </div>
                        <div>
                            9200343222
                            <i class="bi bi-copy"></i>
                        </div>
                    </a>
                    <a href="#">
                        <div>
                            <i class="bi bi-envelope"></i>
                            {{ __('Email') }}
                        </div>
                        <div>
                            help@company.sa
                            <i class="bi bi-copy"></i>
                        </div>
                    </a>
                    <a href="#">
                        <div>
                            <i class="bi bi-geo-alt-fill"></i>
                            {{ __('Location') }}
                        </div>
                        <div>
                            {{ __('Riyadh') }}
                            <i class="bi bi-link-45deg"></i>
                        </div>
                    </a>
                    <a href="#">
                        <div>{{ __('Follow Us') }}</div>
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
                <p>{{ __('Al-Ertiqaa High Institute for Training') }}. {{ __('All rights reserved') }} &copy; 2024.</p>
            </div>
            <div class="footer-meta">
                <p>{{ __('Last modified') }}: 04/12/2020</p>
                <p>{{ __('Developed and maintained by Al-Ertiqaa') }}</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Get current locale
        const currentLocale = '{{ app()->getLocale() }}';

        // Update date and time
        function updateDateTime() {
            const now = new Date();

            if (currentLocale === 'ar') {
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
            } else {
                const englishMonths = [
                    "January", "February", "March", "April", "May", "June",
                    "July", "August", "September", "October", "November", "December"
                ];

                const day = now.getDate();
                const month = englishMonths[now.getMonth()];
                const year = now.getFullYear();
                document.getElementById('currentDate').textContent = `${day}-${month}-${year}`;

                let hours = now.getHours();
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const period = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12 || 12;
                document.getElementById('currentTime').textContent = `${hours}:${minutes} ${period}`;
            }
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

        // Testimonial Data
        const testimonials = currentLocale === 'ar' ? [
            {
                img: "{{ asset('images/avatar.png') }}",
                text: "رحلة تدريبية مميزة وواضحة من البداية للنهاية، متابعة التيرم والدروس كانت سهلة جدًا، والتطبيق ساعدني أتابع تقدمي بشكل يومي.",
                author: "سلمان .م - مسار الحاسب وتقنية المعلومات"
            },
            {
                img: "{{ asset('images/person.png') }}",
                text: "تجربتي مع المعهد كانت رائعة، المدربون محترفون والمحتوى التعليمي ممتاز. أنصح الجميع بالتسجيل في برامجهم التدريبية.",
                author: "أحمد .ع - مسار إدارة الأعمال"
            },
            {
                img: "{{ asset('images/right.png') }}",
                text: "الدعم الفني متميز والاستجابة سريعة. البرنامج التدريبي ساعدني على تطوير مهاراتي المهنية بشكل ملحوظ.",
                author: "محمد .ك - مسار التسويق الرقمي"
            },
            {
                img: "{{ asset('images/Media.png') }}",
                text: "شكراً لمعهد الارتقاء على هذه التجربة التعليمية المتميزة. الشهادة المعتمدة ساعدتني في الحصول على فرصة عمل جديدة.",
                author: "خالد .س - مسار الأمن السيبراني"
            }
        ] : [
            {
                img: "{{ asset('images/avatar.png') }}",
                text: "A distinctive and clear training journey from beginning to end, following the term and lessons was very easy, and the app helped me track my progress daily.",
                author: "Salman M. - Computer and IT Path"
            },
            {
                img: "{{ asset('images/person.png') }}",
                text: "My experience with the institute was wonderful, the trainers are professional and the educational content is excellent. I recommend everyone to register in their training programs.",
                author: "Ahmed A. - Business Administration Path"
            },
            {
                img: "{{ asset('images/right.png') }}",
                text: "The technical support is excellent and the response is fast. The training program helped me develop my professional skills significantly.",
                author: "Mohammed K. - Digital Marketing Path"
            },
            {
                img: "{{ asset('images/Media.png') }}",
                text: "Thanks to Al-Ertiqaa Institute for this distinguished educational experience. The accredited certificate helped me get a new job opportunity.",
                author: "Khalid S. - Cybersecurity Path"
            }
        ];

        // Change Testimonial Function
        function changeTestimonial(index) {
            const testimonial = testimonials[index];

            // Update main image with fade effect
            const mainImg = document.getElementById('mainTestimonialImg');
            mainImg.style.opacity = '0';
            setTimeout(() => {
                mainImg.src = testimonial.img;
                mainImg.style.opacity = '1';
            }, 200);

            // Update text with fade effect
            const textEl = document.getElementById('testimonialText');
            const authorEl = document.getElementById('testimonialAuthor');

            textEl.style.opacity = '0';
            authorEl.style.opacity = '0';

            setTimeout(() => {
                textEl.textContent = testimonial.text;
                authorEl.textContent = testimonial.author;
                textEl.style.opacity = '1';
                authorEl.style.opacity = '1';
            }, 200);

            // Update active state on thumbnails
            const thumbnails = document.querySelectorAll('.imgs-av img');
            thumbnails.forEach((thumb, i) => {
                if (i === index) {
                    thumb.classList.add('active');
                } else {
                    thumb.classList.remove('active');
                }
            });
        }

        // Add transition styles for smooth fade
        document.addEventListener('DOMContentLoaded', function() {
            const mainImg = document.getElementById('mainTestimonialImg');
            const textEl = document.getElementById('testimonialText');
            const authorEl = document.getElementById('testimonialAuthor');

            if (mainImg) mainImg.style.transition = 'opacity 0.3s ease';
            if (textEl) textEl.style.transition = 'opacity 0.3s ease';
            if (authorEl) authorEl.style.transition = 'opacity 0.3s ease';
        });
    </script>
</body>
</html>
