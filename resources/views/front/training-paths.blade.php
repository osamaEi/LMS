@extends('layouts.front')

@section('title', 'المسارات التدريبي ة - معهد الارتقاء العالي للتدريب')

@section('styles')
<style>
    /* Courses Section */
    .courses-section {
        padding: 2rem clamp(3rem, 7vw, 7rem);
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

    /* Mockup Section */
    .mockup-section {
        background: linear-gradient(135deg, #1d6b8f 0%, #0071aa 100%);
        padding: 3rem clamp(3rem, 7vw, 7rem);
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
        padding: 2rem clamp(3rem, 7vw, 7rem);
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

    @media (max-width: 991px) {
        .left-sec {
            padding: 20px;
            text-align: center;
            margin-top: 2rem;
        }

        .left-sec img {
            width: 100%;
        }

        .abs-btn, .img-abs {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .courses-section,
        .faq-section {
            padding: 1.5rem 1rem;
        }
    }
</style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="breadcrumb-nav">
            <a href="{{ route('home') }}">الرئيسية</a>
            <span>></span>
            <span>المسارات التدريبي ة</span>
        </div>
        <h2>مسارات تدريبية تبني مستقبلك المهني بثقة</h2>
        <p>
            نوفر في معهدنا منظومة تدريبية متكاملة مبنية على احتياجات سوق العمل السعودي وفق رؤية 2030،
            من خلال مسارات تدريبية تمتد لمدة سنتين ونصف (10 فصول تدريبية)،
            تُعدّ المتدربين بخبرة عملية وعلمية تجعل مسيرتهم التعليمية واضحة وقوية وموجّهة نحو مهن المستقبل.
        </p>
    </section>

    <!-- Training Programs Section -->
    <section class="courses-section">
        <div class="head">
            <h2>برامجنا التدريبي ة والمسارات المتخصصة</h2>
            <p>
                مجموعة متنوعة من البرامج التدريبي ة المعتمدة المصممة لتلبية احتياجات سوق العمل.
                اختر مسارك التدريبي  وابدأ مسيرة مهنية بدعم من خبراء معتمدين ومنهجيات حديثة.
            </p>
        </div>

        @if($programs->isEmpty())
            <p class="text-center text-muted py-5">لا توجد مسارات تدريبية متاحة حالياً.</p>
        @else
        <div class="courses-container">
            @foreach($programs as $program)
            <div class="course-card">
                <img src="{{ $program->image ? asset('storage/' . $program->image) : asset('images/course.jpg') }}" alt="{{ $program->name_ar }}" />
                <div class="card-body">
                    <h5 class="card-title">{{ $program->name_ar }}</h5>
                    <p class="card-text">{{ Str::limit($program->description_ar, 100) }}</p>
                    <div class="course-meta">
                        @if($program->duration_months)
                            <span><i class="bi bi-clock"></i> {{ $program->duration_months }} شهر</span>
                        @endif
                        @if($program->price)
                            <span class="course-price">{{ number_format($program->price, 0) }} <x-riyal /></span>
                        @endif
                    </div>
                    <a href="{{ route('register') }}" class="full-btn mt-3 w-100 d-block text-center">سجّل الآن</a>
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
        
                    <h2>حمّل تطبيقنا الآن وابدأ رحلتك التعليمية</h2>
                    <p>
                        تطبيق معهدنا يمنحك وصولاً سريعاً ومباشراً لجميع المسارات التدريبي ة والدورات.
                        تابع تقدمك، استقبل إشعارات المحاضرات، وتواصل مع المدربين بسهولة من أي مكان.
                    </p>
                    <div class="store-buttons" dir="ltr">
                        <a href="#" class="store-btn">
                            <i class="bi bi-apple" style="font-size: 24px;"></i>
                            <div>
                                <small>متاح على</small>
                                <div>App Store</div>
                            </div>
                        </a>
                        <a href="#" class="store-btn">
                            <i class="bi bi-google-play" style="font-size: 24px;"></i>
                            <div>
                                <small>حمّله من</small>
                                <div>Google Play</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="{{ asset('images/mockup.png') }}" alt="App Mockup" style="max-width: 400px;" onerror="this.style.display='none'" />
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="head">

            <h2>الأسئلة الشائعة حول المسارات</h2>
        </div>

        <div class="accordion" id="faqAccordion" style="max-width: 900px; margin: 0 auto;">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        هل يمكنني الانتقال من مسار إلى آخر؟
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        نعم، يمكنك الانتقال بين المسارات وفق شروط أكاديمية محددة وبعد مراجعة الساعات التدريبي ة المكتملة والتنسيق مع الإدارة الأكاديمية.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        ما هو نظام الفصول التدريبي ة؟
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        نظام الفصول هو تقسيم المسار التدريبي  إلى فترات زمنية محددة (فصول)، كل منها يمتد فترة معينة، مما يساعد في تنظيم العملية التدريبي ة وتقييم تقدم المتدرب بشكل دوري.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                        ما طرق الدفع المتاحة؟
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        نوفر عدة طرق للدفع تشمل: البطاقات الائتمانية، مدى، التحويل البنكي، والدفع عند التسجيل. كما نتيح خيارات التقسيط للمسارات الطويلة.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        ماذا لو احتجت دعماً فنياً؟
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        فريق الدعم الفني متاح على مدار الساعة للإجابة على استفساراتك وحل أي مشكلة تقنية تواجهها. يمكنك التواصل معنا عبر الهاتف، البريد الإلكتروني، أو نظام التذاكر.
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
