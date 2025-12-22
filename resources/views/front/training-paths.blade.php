@extends('layouts.front')

@section('title', 'مسارات التدريب - معهد الإرتقاء العالي للتدريب')

@section('styles')
<style>
    .hero-section {
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

    .hero-section h2 {
        margin-bottom: 1rem;
        font-weight: bold;
    }

    .hero-section p {
        line-height: 1.8;
        color: rgba(56, 66, 80, 1);
        max-width: 85%;
    }

    /* Choose Path Section */
    .choose-path-section {
        padding: 3rem;
    }

    .choose-path-section .row {
        align-items: center;
    }

    .left-sec {
        position: relative;
        text-align: left;
        padding: 0 80px;
    }

    .left-sec img {
        width: 75%;
        border-radius: 15px;
    }

    .abs-btn {
        position: absolute;
        top: 30px;
        right: 10%;
        transform: translateX(20%);
        background-color: var(--main-color);
        color: white;
        padding: 7px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .img-abs {
        position: absolute;
        top: 60px;
        right: 27%;
        transform: translateX(60%);
    }

    /* Third Section - Statistics */
    .stats-section {
        background: var(--second-color);
        padding: 3rem;
    }

    .stat-item {
        text-align: center;
        padding: 1.5rem;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--main-color);
    }

    .stat-label {
        color: rgba(56, 66, 80, 1);
        margin-top: 0.5rem;
    }

    /* Courses Section */
    .courses-section {
        padding: 3rem;
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
        padding: 4rem 3rem;
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
        padding: 3rem;
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
        .hero-section,
        .choose-path-section,
        .stats-section,
        .courses-section,
        .faq-section {
            padding: 2rem 1rem;
        }

        .hero-section p {
            max-width: 100%;
        }
    }
</style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="breadcrumb-nav">
            <a href="{{ route('home') }}">الرئيسية</a>
            <span><</span>
            <span>مسارات التدريب</span>
        </div>
        <h2>مسارات تدريبية تصنع مستقبلك المهني بثقة</h2>
        <p>
            نوفّر في معهدنا منظومة تدريبية متكاملة مبنية على احتياجات سوق العمل السعودي وفق رؤية 2030،
            عبر مسارات تدريبية تمتد على مدى عامين ونصف (10 أرباع تدريبية)، تهيّئ المتدرب بخبرات عملية
            وعلمية تجعل رحلته التعليمية واضحة، قوية، وموجهة نحو مهن المستقبل. سواء كنت تبحث عن تأسيس
            قوي في تخصص جديد، أو تطوير مهارة مهنية متقدمة، ستجد في مساراتنا ما يفتح لك أبواب المستقبل بثبات.
        </p>
    </section>

    <!-- Choose Path Section -->
    <section class="choose-path-section">
        <div class="row">
            <div class="col-lg-5">
                <p class="st-p">التدريب الذي يلبي احتياجاتك</p>
                <h1>اختر مسارك التدريبي نحو الاحتراف</h1>
                <p class="nd-p">
                    نقدّم لك مسارات تدريبية واضحة تمتد لعامين ونصف (10 أرباع)، مصممة لتطوير مهاراتك
                    بشكل تدريجي ومتكامل. كل مسار يحتوي على دورات قصيرة وطويلة لتتناسب مع أهدافك المهنية.
                </p>
                <div class="d-flex gap-3">
                    <button class="full-btn">استعرض المسارات</button>
                    <button class="notfull-btn">سجل الآن</button>
                </div>
            </div>
            <div class="col-lg-7 left-sec">
                <img src="{{ asset('images/media22.png') }}" alt="Training" onerror="this.src='{{ asset('images/course.jpg') }}'" />
                <button class="abs-btn">إبدأ التعلم الآن</button>
                <img src="{{ asset('images/Figma Cursor.png') }}" class="img-abs" alt="" />
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="row text-center">
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">10+</div>
                <div class="stat-label">مسارات تدريبية</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">50+</div>
                <div class="stat-label">دورة تدريبية</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">1000+</div>
                <div class="stat-label">متدرب</div>
            </div>
            <div class="col-6 col-md-3 stat-item">
                <div class="stat-number">95%</div>
                <div class="stat-label">نسبة الرضا</div>
            </div>
        </div>
    </section>

    <!-- Training Programs Section -->
    <section class="courses-section">
        <div class="head">
            <p class="st-p mx-auto">التدريب الذي يلبي احتياجاتك</p>
            <h2>برامجنا التدريبية والمسارات المتخصصة</h2>
            <p>
                مجموعة متنوِّعة من البرامج التدريبية المعتمدة المصمَّمة لتلبية احتياجات سوق العمل.
                اختر مسارك التدريبي وابدأ رحلة احترافية مدعومة بخبراء معتمدين ومنهجيات حديثة.
            </p>
        </div>

        <div class="courses-container">
            @for($i = 0; $i < 6; $i++)
            <div class="course-card">
                <img src="{{ asset('images/course.jpg') }}" alt="Course" />
                <div class="card-body">
                    <h5 class="card-title">مسار تطوير تطبيقات الويب</h5>
                    <p class="card-text">تعلم أساسيات وتقنيات تطوير تطبيقات الويب الحديثة باستخدام أحدث التقنيات.</p>
                    <div class="course-meta">
                        <span><i class="bi bi-clock"></i> 10 أرباع</span>
                        <span class="course-price">2500 ر.س</span>
                    </div>
                    <button class="full-btn mt-3 w-100">عرض التفاصيل</button>
                </div>
            </div>
            @endfor
        </div>

        <div class="text-center mt-4">
            <button class="notfull-btn">عرض جميع المسارات</button>
        </div>
    </section>

    <!-- Similar Paths Section -->
    <section class="courses-section" style="background: #f9fafb;">
        <div class="head">
            <p class="st-p mx-auto">التدريب الذي يلبي احتياجاتك</p>
            <h2>مسارات مشابهة</h2>
            <p>
                استكشف المزيد من المسارات التي تناسب اهتماماتك وتطلعاتك المهنية. كل مسار مصمم لتطوير
                مهاراتك بشكل عملي ومتدرج، مع اعتماد رسمي وشهادات معترف بها.
            </p>
        </div>

        <div class="courses-container">
            @for($i = 0; $i < 3; $i++)
            <div class="course-card">
                <img src="{{ asset('images/course.jpg') }}" alt="Course" />
                <div class="card-body">
                    <h5 class="card-title">مسار الأمن السيبراني</h5>
                    <p class="card-text">تعلم أساسيات وتقنيات الأمن السيبراني لحماية البيانات والأنظمة الرقمية.</p>
                    <div class="course-meta">
                        <span><i class="bi bi-clock"></i> 10 أرباع</span>
                        <span class="course-price">3000 ر.س</span>
                    </div>
                    <button class="full-btn mt-3 w-100">عرض التفاصيل</button>
                </div>
            </div>
            @endfor
        </div>

        <div class="text-center mt-4">
            <button class="notfull-btn">عرض جميع الأسئلة</button>
        </div>
    </section>

    <!-- Mockup Section -->
    <section class="mockup-section">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="content">
                    <p class="st-p" style="background: rgba(255,255,255,0.2); color: white;">التدريب الذي يلبي احتياجاتك</p>
                    <h2>حمّل تطبيقنا الآن وابدأ رحلتك التعليمية</h2>
                    <p>
                        تطبيق المعهد يتيح لك الوصول السريع والمباشر لجميع المسارات والدورات التدريبية.
                        تابع تقدمك، احصل على إشعارات المحاضرات، وتواصل مع المدربين بسهولة من أي مكان.
                    </p>
                    <div class="store-buttons">
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
                <img src="{{ asset('images/mockup.png') }}" alt="App Mockup" style="max-width: 400px;" onerror="this.style.display='none'" />
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section">
        <div class="head">
            <p class="st-p">التدريب الذي يلبي احتياجاتك</p>
            <h2>الأسئلة الشائعة المختصة بالمسارات</h2>
        </div>

        <div class="accordion" id="faqAccordion" style="max-width: 900px; margin: 0 auto;">
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        هل يمكنني الانتقال من مسار إلى مسار؟
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        نعم، وفق شروط أكاديمية محددة وبعد مراجعة الساعات التدريبية المنجزة.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                        ما هو نظام التيرمات؟
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        نظام التيرمات هو نظام تقسيم المسار التدريبي إلى فترات زمنية محددة (أرباع) تمتد كل منها لفترة معينة،
                        مما يساعد على تنظيم العملية التدريبية وتقييم تقدم المتدرب بشكل دوري.
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
                        نوفر عدة طرق للدفع تشمل: البطاقات الائتمانية، مدى، التحويل البنكي، والدفع عند التسجيل.
                        كما نوفر خيارات التقسيط للمسارات الطويلة.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                        ماذا لو احتجت دعمًا تقنيًا؟
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        فريق الدعم الفني متاح على مدار الساعة للرد على استفساراتك وحل أي مشكلات تقنية قد تواجهها.
                        يمكنك التواصل معنا عبر الهاتف أو البريد الإلكتروني أو نظام التذاكر.
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
