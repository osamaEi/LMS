@extends('layouts.front')

@section('title', __('Courses') . ' - ' . __('Al-Ertiqaa High Institute for Training'))

@section('styles')
<style>
    /* Courses Section */
    .courses-section {
        padding: 2rem clamp(1rem, 3vw, 3rem);
    }

    .courses-section .head {
        text-align: center;
        margin-bottom: 2rem;
    }

    .courses-section .head h2 {
        margin: 1rem 0;
    }

    .courses-section .head p {
        max-width: 800px;
        margin: 0 auto;
        line-height: 1.8;
        color: rgba(56, 66, 80, 1);
    }

    .course-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
    }

    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .course-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .course-card .card-body {
        padding: 1.5rem;
    }

    .course-card .card-title {
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .course-card .card-text {
        color: rgba(56, 66, 80, 1);
        font-size: 0.9rem;
        line-height: 1.6;
    }

    .course-card .course-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }

    .course-card .course-price {
        color: var(--main-color);
        font-weight: bold;
    }

    .course-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--main-color);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
    }

    [dir="ltr"] .course-badge {
        right: auto;
        left: 15px;
    }

    .course-card-wrapper {
        position: relative;
    }

    /* Featured Banner */
    .featured-banner {
        margin: 0 clamp(1rem, 3vw, 3rem) 2rem;
        border-radius: 20px;
        overflow: hidden;
        position: relative;
        height: 300px;
    }

    .featured-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center 30%;
    }

    .featured-banner .overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to left, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.1) 60%);
        display: flex;
        align-items: center;
        justify-content: flex-start;
        padding: 2rem clamp(1rem, 4vw, 4rem);
    }

    [dir="rtl"] .featured-banner .overlay {
        background: linear-gradient(to right, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.1) 60%);
    }

    .featured-banner .overlay-text h3 {
        color: white;
        font-size: clamp(1.2rem, 3vw, 1.8rem);
        margin-bottom: 0.5rem;
    }

    .featured-banner .overlay-text p {
        color: rgba(255,255,255,0.85);
        font-size: 0.95rem;
    }

    @media (max-width: 768px) {
        .featured-banner { height: 200px; margin: 0 1rem 1.5rem; }
    }

    /* Mockup Section */
    .mockup-section {
        background: linear-gradient(135deg, #1d6b8f 0%, #0071aa 100%);
        padding: 3rem clamp(1rem, 3vw, 3rem);
        color: white;
    }

    .mockup-section .content {
        max-width: 600px;
    }

    .mockup-section h2 {
        margin-bottom: 1.5rem;
    }

    .mockup-section p {
        line-height: 1.8;
        opacity: 0.95;
        margin-bottom: 2rem;
    }

    .store-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .store-btn {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #000;
        color: white;
        padding: 12px 20px;
        border-radius: 10px;
        text-decoration: none;
        transition: transform 0.3s;
    }

    .store-btn:hover {
        transform: translateY(-3px);
        color: white;
    }

    /* FAQ Section */
    .faq-section {
        padding: 2rem clamp(1rem, 3vw, 3rem);
        background: #f9fafb;
    }

    .faq-section .head {
        margin-bottom: 2rem;
    }

    .faq-section h2 {
        margin: 0.5rem 0;
    }

    .accordion-item {
        border: 1px solid #e5e7eb;
        margin-bottom: 1rem;
        border-radius: 10px !important;
        overflow: hidden;
    }

    .accordion-button {
        background: white;
        font-weight: 500;
        padding: 1.25rem;
    }

    .accordion-button:not(.collapsed) {
        background: #f9fafb;
        color: #000;
        box-shadow: none;
    }

    .accordion-button:focus {
        box-shadow: none;
    }

    .accordion-body {
        background: #f9fafb;
        line-height: 1.8;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 0.5rem;
        margin-top: 3rem;
        flex-wrap: wrap;
    }

    .pagination-btn {
        min-width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #e0e0e0;
        background-color: white;
        color: #666;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        padding: 0 12px;
    }

    .pagination-btn:hover {
        border-color: var(--main-color);
        transform: translateY(-2px);
    }

    .pagination-btn.active {
        background-color: var(--main-color);
        color: white;
        border-color: var(--main-color);
    }

    /* Course type filter buttons */
    .course-flt {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: .42rem 1rem;
        border-radius: 8px;
        font-size: .8rem;
        font-weight: 700;
        border: 1.5px solid #e5e7eb;
        background: #f9fafb;
        color: #6b7280;
        cursor: pointer;
        transition: all .17s;
        white-space: nowrap;
        flex-shrink: 0;
        font-family: inherit;
    }
    .course-flt:hover               { background: #eaf5fb; color: #0071AA; border-color: #0071AA; }
    .course-flt.active              { background: #0071AA; color: #fff; border-color: #0071AA; }
    .course-flt.developmental.active{ background: #2563eb; border-color: #2563eb; }
    .course-flt.qualifying.active   { background: #7c3aed; border-color: #7c3aed; }

    @media (max-width: 768px) {
        .courses-section,
        .faq-section {
            padding: 1.5rem 1rem;
        }
    }
</style>
@endsection

@section('content')
@php $lms3s = fn(string $n) => asset('lms3/' . rawurlencode('حين يلتقي التدريب مع الإبداع 3') . '/' . $n); @endphp
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="breadcrumb-nav">
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
            <span>></span>
            <span>{{ __('Courses') }}</span>
        </div>
        <h2>{{ __('Courses') }}</h2>
    </section>

    <!-- Featured Banner -->
    <div class="featured-banner">
        <img loading="lazy" src="{{ asset('lms-photos/3.png') }}" alt="All Courses" onerror="this.src='{{ asset('lms-photos/1.png') }}'" />
        <div class="overlay">
            <div class="overlay-text">
                <h3>{{ __('All Our Professional Courses') }}</h3>
                <p>{{ __('Flexible, accredited, and designed for your success') }}</p>
            </div>
        </div>
    </div>

    <!-- Course Type Filter -->
    @if(!$programs->isEmpty())
    <div style="background:#fff;border-bottom:1px solid #e5e7eb;position:sticky;top:0;z-index:200;box-shadow:0 2px 12px rgba(0,0,0,.06);">
        <div class="container" style="max-width:1400px;">
            <div style="display:flex;align-items:center;gap:.5rem;padding:.75rem 1rem;overflow-x:auto;scrollbar-width:none;">
                <button class="course-flt active" onclick="filterCourses('all',this)">
                    <i class="bi bi-grid-3x3-gap-fill"></i> الكل
                </button>
                <button class="course-flt developmental" onclick="filterCourses('developmental',this)">
                    <i class="bi bi-graph-up-arrow"></i> تطويري
                </button>
                <button class="course-flt qualifying" onclick="filterCourses('qualifying',this)">
                    <i class="bi bi-mortarboard-fill"></i> تأهيلي
                </button>
                <span id="courseCount" style="margin-right:auto;font-size:.78rem;font-weight:700;color:#6b7280;white-space:nowrap;display:inline-flex;align-items:center;gap:5px;">
                    <i class="bi bi-card-list" style="color:#0071AA;"></i>
                    {{ $programs->count() }} دورة
                </span>
            </div>
        </div>
    </div>
    @endif

    <!-- Courses Section -->
    <section class="courses-section mb-5">
        <div class="head">
            <h2>اختر الدورة المناسبة لك</h2>
            <p>
                دوراتنا مصممة لتطوير مهاراتك بسرعة وفعالية في مجالات متعددة،
                معتمدة رسمياً وتناسب  المتدربون  والمهنيين الساعين لتعزيز خبراتهم.
            </p>
        </div>

        @if($programs->isEmpty())
            <p class="text-center text-muted py-5">لا توجد دورات متاحة حالياً.</p>
        @else
        @php
            $courseImages = [
                $lms3s('3.png'), $lms3s('5.png'), $lms3s('7.png'),
                $lms3s('9.png'), $lms3s('11.png'), $lms3s('13.png'),
            ];
        @endphp
        <div class="courses-container" id="coursesGrid">
            @foreach($programs as $program)
            <div class="course-card-wrapper" data-course-type="{{ $program->course_type }}">
                <div class="course-card">
                    <img src="{{ $program->image ? asset('storage/' . $program->image) : $courseImages[$loop->index % count($courseImages)] }}" alt="{{ $program->name_ar }}" />
                    {{-- Type badge --}}
                    @if($program->course_type === 'developmental')
                        <span class="course-badge" style="background:#2563eb;">تطويري</span>
                    @elseif($program->course_type === 'qualifying')
                        <span class="course-badge" style="background:#7c3aed;">تأهيلي</span>
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ app()->getLocale() === 'en' ? ($program->name_en ?: $program->name_ar) : $program->name_ar }}</h5>
                        <p class="card-text">{{ Str::limit(app()->getLocale() === 'en' ? ($program->description_en ?: $program->description_ar) : $program->description_ar, 100) }}</p>
                        <div class="course-meta">
                            @if($program->duration_months)
                                <span><i class="bi bi-clock"></i> {{ $program->duration_months }} {{ __('Months') }}</span>
                            @endif
                            @if($program->price)
                                <span class="course-price">{{ number_format($program->price, 0) }} {{ __('SAR') }}</span>
                            @endif
                        </div>
                        <a href="{{ route('register') }}" class="full-btn mt-3 w-100 d-block text-center">{{ __('Register Now') }}</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </section>

    <!-- Mockup Section -->
    <section class="mockup-section">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="content">

                    <h2>{{ __('Download Our App Now and Start Your Learning Journey') }}</h2>
                    <p>
                        {{ __('Our institute app provides you with quick and direct access to all training paths and courses. Track your progress, receive lecture notifications, and communicate with trainers easily from anywhere.') }}
                    </p>
                    <div class="store-buttons" dir="ltr">
                        <a href="#" class="store-btn">
                            <i class="bi bi-apple" style="font-size: 24px;"></i>
                            <div>
                                <small>Download on the</small>
                                <div>App Store</div>
                            </div>
                        </a>
                        <a href="#" class="store-btn">
                            <i class="bi bi-google-play" style="font-size: 24px;"></i>
                            <div>
                                <small>GET IT ON</small>
                                <div>Google Play</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img loading="lazy" src="{{ $lms3s('8.png') }}" alt="App Mockup" style="max-width: 450px; border-radius: 20px; object-fit: cover;" onerror="this.style.display='none'" />
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="head">
            <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
            <h2>{{ __('Frequently Asked Questions About Courses') }}</h2>
        </div>

        <div class="accordion" id="faqAccordion" style="max-width: 900px; margin: 0 auto;">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        {{ __('Can I switch from one path to another?') }}
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ __('Yes, you can switch between paths according to specific academic conditions and after reviewing the completed training hours and coordinating with the academic administration.') }}
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        {{ __('How do I register at the institute?') }}
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ __('You can easily register via Nafath account or create an internal account. After registration, you can choose the academic path or short courses that suit you.') }}
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        {{ __('What is the duration of a course?') }}
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ __('Courses range from two weeks to 8 weeks depending on the nature and content of the course.') }}
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        {{ __('Do I get a certificate after completing the course?') }}
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        {{ __('Yes, the trainee receives an accredited certificate after successfully completing the course and passing the required assessments.') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
function filterCourses(type, btn) {
    document.querySelectorAll('.course-flt').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    let n = 0;
    document.querySelectorAll('#coursesGrid .course-card-wrapper').forEach(card => {
        const cardType = card.dataset.courseType || '';
        const show = type === 'all' || cardType === type;
        card.style.display = show ? '' : 'none';
        if (show) n++;
    });
    document.getElementById('courseCount').innerHTML =
        `<i class="bi bi-card-list" style="color:#0071AA;"></i> ${n} دورة`;
}
</script>
@endsection
