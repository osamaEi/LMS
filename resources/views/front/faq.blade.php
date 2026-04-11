@extends('layouts.front')

@section('title', __('FAQ') . ' - ' . __('Al-Ertiqaa High Institute for Training'))

@section('styles')
<style>
    .faq-body-section {
        padding: 2.5rem clamp(1rem, 3vw, 3rem);
        max-width: 900px;
        margin: 0 auto;
    }

    /* Search Box */
    .faq-search-wrap {
        position: relative;
        margin-bottom: 2rem;
    }

    .faq-search-wrap input {
        width: 100%;
        border-radius: 12px;
        border: 1.5px solid #e5e7eb;
        padding: 14px 50px 14px 18px;
        font-size: 15px;
        outline: none;
        background: #f9fafb;
        transition: border-color .2s, box-shadow .2s;
        direction: rtl;
    }

    .faq-search-wrap input:focus {
        border-color: var(--main-color);
        box-shadow: 0 0 0 3px rgba(0, 113, 170, 0.1);
        background: white;
    }

    .faq-search-wrap .search-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }

    .faq-search-wrap .clear-btn {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        background: #e5e7eb;
        border: none;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        font-size: 12px;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        line-height: 1;
    }

    /* Category Tabs */
    .category-tabs {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 2rem;
        background: #f3f4f6;
        padding: 6px;
        border-radius: 14px;
        justify-content: center;
    }

    .category-tab {
        padding: 9px 18px;
        color: #6b7280;
        text-decoration: none;
        border-radius: 10px;
        transition: all .2s;
        cursor: pointer;
        background: transparent;
        border: none;
        font-size: 14px;
        font-weight: 500;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .category-tab .tab-count {
        background: rgba(0,0,0,0.08);
        color: inherit;
        border-radius: 20px;
        padding: 1px 8px;
        font-size: 11px;
        font-weight: 700;
        transition: all .2s;
    }

    .category-tab:hover {
        background: white;
        color: var(--main-color);
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
    }

    .category-tab.active {
        background: var(--main-color);
        color: white;
        box-shadow: 0 2px 8px rgba(0, 113, 170, 0.3);
    }

    .category-tab.active .tab-count {
        background: rgba(255,255,255,0.25);
        color: white;
    }

    /* FAQ Accordion */
    .faq-accordion {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .faq-item {
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        background: white;
        transition: border-color .2s, box-shadow .2s;
    }

    .faq-item:hover {
        border-color: var(--main-color);
        box-shadow: 0 2px 10px rgba(0, 113, 170, 0.08);
    }

    .faq-item.hidden {
        display: none;
    }

    .faq-question {
        width: 100%;
        padding: 16px 20px;
        background: white;
        border: none;
        text-align: right;
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        transition: background .15s;
    }

    .faq-question:hover {
        background: #fafafa;
    }

    .faq-question.open {
        background: #f0f9ff;
        color: var(--main-color);
        border-bottom: 1px solid #e0f2fe;
    }

    .faq-question .q-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 13px;
        font-weight: 700;
        background: #e0f2fe;
        color: var(--main-color);
        transition: all .2s;
    }

    .faq-question.open .q-icon {
        background: var(--main-color);
        color: white;
    }

    .faq-arrow {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        transition: transform .25s ease;
        color: #9ca3af;
    }

    .faq-question.open .faq-arrow {
        transform: rotate(180deg);
        color: var(--main-color);
    }

    .faq-answer {
        display: none;
        padding: 0 20px 18px;
        background: #f0f9ff;
        color: #374151;
        font-size: 14px;
        line-height: 1.8;
        border-top: none;
    }

    .faq-answer.open {
        display: block;
    }

    /* Category badge on question */
    .cat-badge {
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 20px;
        font-weight: 600;
        flex-shrink: 0;
    }

    /* Empty State */
    .faq-empty {
        text-align: center;
        padding: 60px 20px;
        display: none;
    }

    .faq-empty.show {
        display: block;
    }

    /* Results counter */
    .results-info {
        font-size: 13px;
        color: #9ca3af;
        margin-bottom: 16px;
        text-align: right;
        min-height: 20px;
    }

    /* Contact Section */
    .contact-section {
        padding: 3rem clamp(1rem, 3vw, 3rem);
        background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%);
        border-top: 1px solid #e5e7eb;
    }

    .contact-section .row {
        align-items: center;
    }

    .contact-content h2 {
        margin-bottom: 1rem;
        line-height: 1.4;
    }

    .contact-content p {
        line-height: 1.8;
        color: rgba(56, 66, 80, 1);
        margin-bottom: 1.5rem;
    }

    .contact-image img {
        width: 100%;
        border-radius: 20px;
    }

    @media (max-width: 768px) {
        .faq-body-section { padding: 1.5rem 1rem; }
        .category-tabs { gap: 5px; padding: 5px; }
        .category-tab { padding: 8px 12px; font-size: 13px; }
        .contact-image { margin-top: 2rem; }
    }
</style>
@endsection

@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="breadcrumb-nav">
            <a href="{{ route('home') }}">{{ __('Home') }}</a>
            <span>></span>
            <span>{{ __('FAQ') }}</span>
        </div>
        <h2>{{ __('Frequently Asked Questions') }}</h2>
        <p>{{ __('Do you have questions? We are here to provide you with clear and comprehensive answers about everything related to our training programs.') }}</p>
    </section>

    <!-- FAQ Body Section -->
    <section class="faq-body-section">

        @php
        $faqData = [
            [
                'cat'  => 'registration',
                'q'    => 'كيف أتسجل في المعهد؟',
                'a'    => 'يمكنك التسجيل بسهولة عبر حساب نفاذ أو إنشاء حساب داخلي. بعد التسجيل، يمكنك اختيار الدبلومة الأكاديمي أو الدورات القصيرة التي تناسبك. ستتلقى رسالة تأكيد على بريدك الإلكتروني بعد إتمام التسجيل.',
            ],
            [
                'cat'  => 'registration',
                'q'    => 'ما طرق الدفع المتاحة؟',
                'a'    => 'نوفر عدة طرق للدفع تشمل: بطاقات الائتمان، مدى، التحويل البنكي، والدفع عند التسجيل. كما نوفر خيارات التقسيط للدبلومات الطويلة.',
            ],
            [
                'cat'  => 'registration',
                'q'    => 'هل يمكنني الاسترداد إذا انسحبت من البرنامج؟',
                'a'    => 'نعم، يمكن طلب استرداد الرسوم وفقاً لسياسة الاسترداد المعتمدة. الاسترداد الكامل متاح خلال 7 أيام من تاريخ التسجيل، والاسترداد الجزئي (50%) متاح خلال 14 يوماً، وبعد ذلك لا يُقبل طلب الاسترداد.',
            ],
            [
                'cat'  => 'registration',
                'q'    => 'ما الوثائق المطلوبة للتسجيل؟',
                'a'    => 'تحتاج إلى: صورة من الهوية الوطنية أو الإقامة، صورة شخصية حديثة، شهادة المؤهل الأكاديمي الأخير، وأي شهادات مهنية ذات صلة إن وُجدت.',
            ],
            [
                'cat'  => 'registration',
                'q'    => 'كيف أعرف أن طلبي قُبل؟',
                'a'    => 'ستصلك رسالة بريد إلكتروني تأكيدية فور قبول طلبك، إضافةً إلى إشعار داخل المنصة. يمكنك أيضاً متابعة حالة طلبك من لوحة التحكم الخاصة بك.',
            ],
            [
                'cat'  => 'courses',
                'q'    => 'ما هو نظام الفصل الدراسي؟',
                'a'    => 'نظام الفصل الدراسي هو نظام تقسيم الدبلومة التدريبي إلى فترات زمنية محددة (ترم)، مدة كل منها فترة معينة، مما يساعد في تنظيم العملية التدريبية وتقييم تقدم المتدرب بشكل دوري.',
            ],
            [
                'cat'  => 'courses',
                'q'    => 'هل يمكنني التحويل من دبلومة لآخر؟',
                'a'    => 'نعم، يمكنك التحويل بين الدبلومات  وفق شروط أكاديمية محددة وبعد مراجعة الساعات التدريبية المنجزة والتنسيق مع الإدارة الأكاديمية.',
            ],
            [
                'cat'  => 'courses',
                'q'    => 'هل يوجد تدريب عن بعد؟',
                'a'    => 'نعم، نوفر خيارات التدريب عن بعد لمعظم دوراتنا ودبلومةاتنا التدريبية، مع توفر جميع المواد التعليمية والموارد إلكترونياً.',
            ],
            [
                'cat'  => 'courses',
                'q'    => 'ما مدة الدورة القصيرة؟',
                'a'    => 'تتراوح الدورات القصيرة بين أسبوعين إلى 8 أسابيع تبعاً لطبيعة الدورة ومحتواها.',
            ],
            [
                'cat'  => 'courses',
                'q'    => 'كم عدد المواد في كل برنامج؟',
                'a'    => 'يتفاوت عدد المواد حسب البرنامج. يتكون كل فصل دراسي عادةً من 4 إلى 8 مواد دراسية، ويمكنك الاطلاع على الخطة الدراسية التفصيلية لكل برنامج من صفحة البرامج.',
            ],
            [
                'cat'  => 'courses',
                'q'    => 'هل يمكنني الدراسة في أكثر من دبلومة في نفس الوقت؟',
                'a'    => 'يسمح بالتسجيل في دبلومة واحد فقط في نفس الوقت للطلاب النظاميين، غير أنه يمكن الالتحاق بدورات قصيرة إضافية بجانب الدبلومة الرئيسي وفق ضوابط محددة.',
            ],
            [
                'cat'  => 'certificates',
                'q'    => 'هل الشهادات معتمدة؟',
                'a'    => 'نعم، جميع شهاداتنا معتمدة من المؤسسة العامة للتدريب التقني والمهني ومعترف بها في سوق العمل السعودي.',
            ],
            [
                'cat'  => 'certificates',
                'q'    => 'متى أحصل على شهادتي؟',
                'a'    => 'تُصدر الشهادة خلال أسبوع إلى أسبوعين من إتمام متطلبات البرنامج بنجاح واجتياز جميع التقييمات.',
            ],
            [
                'cat'  => 'certificates',
                'q'    => 'هل يمكنني الحصول على شهادة رقمية؟',
                'a'    => 'نعم، تُوفَّر الشهادة الرقمية (Digital Badge) إضافةً إلى النسخة الورقية. يمكن مشاركتها على LinkedIn أو أي منصة مهنية.',
            ],
            [
                'cat'  => 'certificates',
                'q'    => 'ما الحد الأدنى لاجتياز البرنامج؟',
                'a'    => 'الحد الأدنى للاجتياز هو 60% في التقييمات الإجمالية مع حضور لا يقل عن 75% من إجمالي ساعات البرنامج.',
            ],
            [
                'cat'  => 'certificates',
                'q'    => 'هل الشهادة معترف بها دولياً؟',
                'a'    => 'تحمل بعض برامجنا اعتمادات دولية من جهات متخصصة. يُذكر نطاق الاعتماد بوضوح في وصف كل برنامج.',
            ],
            [
                'cat'  => 'platform',
                'q'    => 'كيف أصل إلى المواد الدراسية؟',
                'a'    => 'بعد التسجيل، يمكنك الوصول إلى جميع المواد الدراسية من قائمة "ملفاتي" في القائمة الجانبية. تشمل المواد: ملفات PDF، فيديوهات المحاضرات، وروابط جلسات Zoom.',
            ],
            [
                'cat'  => 'platform',
                'q'    => 'كيف أتابع حضوري؟',
                'a'    => 'يمكنك مراجعة سجل حضورك الكامل من قسم "سجل الحضور" في لوحة التحكم. يُظهر السجل تاريخ كل جلسة وحالة الحضور.',
            ],
            [
                'cat'  => 'platform',
                'q'    => 'كيف أطلع على درجاتي ونتائجي؟',
                'a'    => 'تظهر درجات التقييمات والاختبارات في قسم "نتائجي" بلوحة تحكم الطالب فور صدورها من قِبل المعلم.',
            ],
            [
                'cat'  => 'platform',
                'q'    => 'كيف أستخدم نظام التذاكر للدعم؟',
                'a'    => 'من قائمة "تذاكر الدعم" في لوحة تحكمك، انقر على "إنشاء تذكرة جديدة"، اختر الفئة والأولوية، واكتب وصفاً لمشكلتك. سيرد فريق الدعم خلال 24 ساعة.',
            ],
            [
                'cat'  => 'platform',
                'q'    => 'كيف أحدّث بياناتي الشخصية؟',
                'a'    => 'انتقل إلى "الملف الشخصي" من القائمة العلوية، ثم انقر على "تعديل الملف الشخصي". يمكنك تحديث الاسم ورقم الجوال والصورة الشخصية.',
            ],
            [
                'cat'  => 'platform',
                'q'    => 'كيف يعمل نظام المدفوعات في المنصة؟',
                'a'    => 'عند التسجيل في برنامج مدفوع، يتم توجيهك تلقائياً لإتمام الدفع عبر بوابة الدفع. يمكنك مراجعة جميع فواتيرك وتاريخ مدفوعاتك من قسم "المدفوعات" في لوحة تحكمك.',
            ],
            [
                'cat'  => 'support',
                'q'    => 'كيف أحصل على الدعم التقني؟',
                'a'    => 'فريق الدعم التقني متاح على مدار الساعة للرد على استفساراتك وحل أي مشاكل تقنية. يمكنك التواصل عبر الهاتف أو البريد الإلكتروني أو نظام التذاكر.',
            ],
            [
                'cat'  => 'support',
                'q'    => 'ماذا أفعل إذا لم أتمكن من الوصول لحسابي؟',
                'a'    => 'انقر على "نسيت كلمة المرور" في صفحة تسجيل الدخول وأدخل بريدك الإلكتروني. ستصلك رابط إعادة التعيين خلال دقائق. إذا استمرت المشكلة، تواصل مع الدعم التقني.',
            ],
            [
                'cat'  => 'support',
                'q'    => 'ما المتصفحات المدعومة؟',
                'a'    => 'المنصة تعمل على أحدث إصدارات: Chrome، Firefox، Edge، وSafari. يُنصح باستخدام Chrome أو Edge للحصول على أفضل أداء.',
            ],
            [
                'cat'  => 'support',
                'q'    => 'ماذا أفعل إذا كانت الفيديوهات لا تعمل؟',
                'a'    => 'تأكد من جودة الاتصال بالإنترنت، جرّب تحديث الصفحة، وتأكد من عدم تفعيل مانع الإعلانات. إذا استمرت المشكلة، يمكنك رفع تذكرة دعم تقني مع وصف المشكلة.',
            ],
            [
                'cat'  => 'support',
                'q'    => 'كيف أبلّغ عن مشكلة في المنصة؟',
                'a'    => 'استخدم نظام التذاكر من لوحة تحكمك واختر الفئة "دعم تقني" وأولوية "عالية" لضمان أسرع استجابة. يمكنك أيضاً التواصل عبر واتساب للمشاكل العاجلة.',
            ],
        ];

        $categories = [
            'all'          => ['label' => 'الكل', 'icon' => '🔍'],
            'registration' => ['label' => 'التسجيل والدفع', 'icon' => '💳'],
            'courses'      => ['label' => 'الدورات والدبلومات ', 'icon' => '📚'],
            'certificates' => ['label' => 'الشهادات والاعتماد', 'icon' => '🎓'],
            'platform'     => ['label' => 'منصة الطالب', 'icon' => '💻'],
            'support'      => ['label' => 'الدعم التقني', 'icon' => '🛠️'],
        ];

        $catCounts = ['all' => count($faqData)];
        foreach ($faqData as $faq) {
            $catCounts[$faq['cat']] = ($catCounts[$faq['cat']] ?? 0) + 1;
        }
        @endphp

        <!-- Search Box -->
        <div class="faq-search-wrap">
            <svg class="search-icon" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" id="faqSearch" placeholder="ابحث عن سؤالك هنا..." autocomplete="off" />
            <button class="clear-btn" id="clearSearch" onclick="clearSearch()" title="مسح البحث">✕</button>
        </div>

        <!-- Category Tabs -->
        <div class="category-tabs" id="categoryTabs">
            @foreach($categories as $key => $cat)
            <button class="category-tab {{ $key === 'all' ? 'active' : '' }}"
                    data-cat="{{ $key }}"
                    onclick="filterByCategory('{{ $key }}', this)">
                <span>{{ $cat['icon'] }} {{ $cat['label'] }}</span>
                <span class="tab-count">{{ $catCounts[$key] ?? 0 }}</span>
            </button>
            @endforeach
        </div>

        <!-- Results Info -->
        <div class="results-info" id="resultsInfo"></div>

        <!-- FAQ Accordion -->
        <div class="faq-accordion" id="faqAccordion">
            @foreach($faqData as $index => $faq)
            <div class="faq-item" data-cat="{{ $faq['cat'] }}" data-q="{{ mb_strtolower($faq['q']) }}" data-a="{{ mb_strtolower($faq['a']) }}">
                <button class="faq-question" onclick="toggleFaq(this)">
                    <div class="q-icon">س</div>
                    <span style="flex:1;text-align:right;">{{ $faq['q'] }}</span>
                    <svg class="faq-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div class="faq-answer">
                    {{ $faq['a'] }}
                </div>
            </div>
            @endforeach
        </div>

        <!-- Empty State -->
        <div class="faq-empty" id="faqEmpty">
            <div style="width:80px;height:80px;background:#f3f4f6;border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="38" height="38" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p style="font-size:17px;font-weight:700;color:#374151;margin:0 0 6px;">لا توجد نتائج</p>
            <p style="font-size:14px;color:#9ca3af;margin:0;">جرّب البحث بكلمات مختلفة أو اختر فئة أخرى</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="row">
            <div class="col-lg-6">
                <div class="contact-content">
                    <p class="st-p">{{ __('Training That Meets Your Needs') }}</p>
                    <h2>{{ __('We\'re Happy to Hear From You... We Value Every Question and Inquiry') }}</h2>
                    <p>
                        {{ __('Our support and guidance team is here to serve you and answer all your questions related to training programs, career paths, registration procedures, term system, course schedules, or any details you need to know before starting your journey with us.') }}
                    </p>
                    <a href="{{ route('contact') }}" class="full-btn">{{ __('Contact Us Now') }}</a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="contact-image">
                    <img src="{{ asset('lms2-photo/3.png') }}" alt="Contact Us" onerror="this.src='{{ asset('lms2-photo/11.png') }}'" />
                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>
let activeCategory = 'all';
let searchQuery    = '';

function toggleFaq(btn) {
    const item    = btn.closest('.faq-item');
    const answer  = item.querySelector('.faq-answer');
    const isOpen  = btn.classList.contains('open');

    // Close all
    document.querySelectorAll('.faq-question.open').forEach(q => {
        q.classList.remove('open');
        q.closest('.faq-item').querySelector('.faq-answer').classList.remove('open');
    });

    // Open clicked if it wasn't open
    if (!isOpen) {
        btn.classList.add('open');
        answer.classList.add('open');
    }
}

function filterByCategory(cat, tabEl) {
    activeCategory = cat;
    searchQuery    = '';

    // Update search input
    const searchInput = document.getElementById('faqSearch');
    searchInput.value = '';
    document.getElementById('clearSearch').style.display = 'none';

    // Update tab active state
    document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
    tabEl.classList.add('active');

    applyFilters();
}

function applyFilters() {
    const items   = document.querySelectorAll('.faq-item');
    let visible   = 0;
    const q       = searchQuery.trim().toLowerCase();

    items.forEach(item => {
        const catMatch    = activeCategory === 'all' || item.dataset.cat === activeCategory;
        const searchMatch = q === '' || item.dataset.q.includes(q) || item.dataset.a.includes(q);

        if (catMatch && searchMatch) {
            item.classList.remove('hidden');
            // Highlight search text in question
            const questionSpan = item.querySelector('.faq-question span');
            if (q) {
                const orig = item.querySelector('.faq-question span').textContent;
                const regex = new RegExp(`(${escapeRegex(q)})`, 'gi');
                questionSpan.innerHTML = orig.replace(regex, '<mark style="background:#fef3c7;border-radius:3px;padding:0 2px;">$1</mark>');
            } else {
                const orig = item.querySelector('.faq-question span').textContent;
                questionSpan.textContent = orig.replace(/<[^>]*>/g, ''); // strip any leftover tags
            }
            visible++;
        } else {
            item.classList.add('hidden');
        }
    });

    // Update results info
    const info = document.getElementById('resultsInfo');
    if (q) {
        info.textContent = `${visible} نتيجة للبحث عن "${searchQuery}"`;
    } else {
        info.textContent = '';
    }

    // Show/hide empty state
    document.getElementById('faqEmpty').classList.toggle('show', visible === 0);
}

function escapeRegex(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function clearSearch() {
    searchQuery = '';
    const searchInput = document.getElementById('faqSearch');
    searchInput.value = '';
    document.getElementById('clearSearch').style.display = 'none';
    applyFilters();
    searchInput.focus();
}

// Search input listener
document.getElementById('faqSearch').addEventListener('input', function() {
    searchQuery = this.value;
    const clearBtn = document.getElementById('clearSearch');
    clearBtn.style.display = this.value ? 'flex' : 'none';
    applyFilters();
});

// Keyboard: ESC clears search
document.getElementById('faqSearch').addEventListener('keydown', function(e) {
    if (e.key === 'Escape') clearSearch();
});

// Auto-open first item on load
document.addEventListener('DOMContentLoaded', function() {
    const firstItem = document.querySelector('.faq-item');
    if (firstItem) {
        const btn = firstItem.querySelector('.faq-question');
        btn.classList.add('open');
        firstItem.querySelector('.faq-answer').classList.add('open');
    }
});
</script>
@endsection
