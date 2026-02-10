@extends('layouts.front')

@section('title', 'دليل الطالب الإرشادي')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
    .student-guide-page * {
        font-family: 'Cairo', sans-serif;
    }
    .step-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .step-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        border-color: #06b6d4;
    }
    .step-number {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        color: #fff;
        background: linear-gradient(135deg, #06b6d4, #2563eb);
    }
    .step-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: #0891b2;
        background: linear-gradient(135deg, #ecfeff, #cffafe);
    }
    .guide-header-gradient {
        background: linear-gradient(135deg, #06b6d4, #2563eb);
    }
    .sub-step {
        position: relative;
        padding-right: 1.25rem;
    }
    .sub-step::before {
        content: '';
        position: absolute;
        right: 0;
        top: 10px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #06b6d4;
    }
    .info-box {
        background: linear-gradient(135deg, #f0fdfa, #ecfeff);
        border-right: 4px solid #06b6d4;
    }
    .timeline-connector {
        position: absolute;
        right: 23px;
        top: 48px;
        bottom: -24px;
        width: 2px;
        background: linear-gradient(to bottom, #06b6d4, #2563eb);
        opacity: 0.3;
    }
</style>
@endsection

@section('content')
<div class="student-guide-page" style="min-height: 100vh; background-color: #f9fafb; padding: 3rem 0;">
    <div style="max-width: 960px; margin: 0 auto; padding: 0 1rem;">

        {{-- Header --}}
        <div class="guide-header-gradient" style="border-radius: 1rem; padding: 2.5rem; color: #fff; margin-bottom: 2rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -30px; left: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -40px; right: -20px; width: 160px; height: 160px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
            <div style="position: relative; z-index: 1;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-user-graduate" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h1 style="font-size: 1.875rem; font-weight: 700; margin: 0;">دليل الطالب الإرشادي</h1>
                        <p style="margin: 0.25rem 0 0 0; opacity: 0.9; font-size: 1rem;">دليلك الشامل لاستخدام منصة التعلم الإلكتروني بكفاءة</p>
                    </div>
                </div>
                <p style="font-size: 0.95rem; opacity: 0.85; line-height: 1.8; margin: 0;">
                    يهدف هذا الدليل إلى تعريفك بجميع الأدوات والخدمات المتاحة على المنصة التعليمية، وتمكينك من الاستفادة القصوى من تجربة التعلم الإلكتروني.
                    يرجى قراءة الخطوات التالية بعناية لضمان تجربة تعليمية سلسة وفعّالة.
                </p>
            </div>
            <div style="margin-top: 1.25rem; display: flex; gap: 1.5rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.15); border-radius: 999px; padding: 0.4rem 1rem; font-size: 0.85rem;">
                    <i class="fas fa-check-circle"></i>
                    <span>معيار المركز الوطني 1.3.2</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.15); border-radius: 999px; padding: 0.4rem 1rem; font-size: 0.85rem;">
                    <i class="fas fa-clock"></i>
                    <span>وقت القراءة: 10 دقائق</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.15); border-radius: 999px; padding: 0.4rem 1rem; font-size: 0.85rem;">
                    <i class="fas fa-list-ol"></i>
                    <span>9 خطوات أساسية</span>
                </div>
            </div>
        </div>

        {{-- Important Notice --}}
        <div class="info-box" style="border-radius: 0.75rem; padding: 1.25rem 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <i class="fas fa-info-circle" style="color: #0891b2; font-size: 1.25rem; margin-top: 3px;"></i>
                <div>
                    <p style="font-weight: 600; color: #0e7490; margin: 0 0 0.25rem 0;">ملاحظة هامة</p>
                    <p style="color: #374151; margin: 0; line-height: 1.8; font-size: 0.95rem;">
                        هذا الدليل متاح للجميع ولا يتطلب تسجيل الدخول للاطلاع عليه، وذلك وفقاً لمتطلبات المركز الوطني للتعليم الإلكتروني (NELC).
                        في حال واجهت أي صعوبة، يمكنك التواصل مع فريق الدعم الفني عبر البريد الإلكتروني أو الهاتف الموضح في صفحة "اتصل بنا".
                    </p>
                </div>
            </div>
        </div>

        {{-- Step 1: Login --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem; position: relative;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">1</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">تسجيل الدخول إلى المنصة</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">الخطوة الأولى للبدء في رحلتك التعليمية</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">للدخول إلى المنصة التعليمية، اتبع الخطوات التالية:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>افتح الموقع الإلكتروني:</strong> قم بزيارة الصفحة الرئيسية للمنصة عبر المتصفح الخاص بك (يُفضل استخدام Google Chrome أو Microsoft Edge للحصول على أفضل تجربة).
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>انقر على زر "تسجيل الدخول":</strong> ستجد الزر في الشريط العلوي من الصفحة الرئيسية. اضغط عليه للانتقال إلى صفحة تسجيل الدخول.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>أدخل بيانات الاعتماد:</strong> قم بإدخال اسم المستخدم أو البريد الإلكتروني وكلمة المرور اللذين تم تزويدك بهما عند التسجيل في المعهد.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>اضغط "دخول":</strong> بعد إدخال البيانات بشكل صحيح، سيتم توجيهك إلى لوحة التحكم الخاصة بك حيث يمكنك الوصول إلى جميع الدورات والمحتوى التعليمي.
                        </div>
                        <div style="background: #fffbeb; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-top: 0.75rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <i class="fas fa-lightbulb" style="color: #d97706; margin-top: 3px;"></i>
                            <span style="font-size: 0.875rem; color: #92400e;"><strong>نصيحة:</strong> في حال نسيت كلمة المرور، استخدم خيار "نسيت كلمة المرور" لإعادة تعيينها عبر بريدك الإلكتروني المسجل.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2: Choose Courses --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">2</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">اختيار الدورات التدريبية</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">تصفح واختر الدورات المناسبة لأهدافك التعليمية</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">توفر المنصة مجموعة متنوعة من الدورات التدريبية والمسارات التعليمية المعتمدة. لاختيار الدورة المناسبة:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>تصفح كتالوج الدورات:</strong> من لوحة التحكم الخاصة بك، انتقل إلى قسم "المسارات التدريبية" أو "الدورات القصيرة" لاستعراض جميع الدورات المتاحة.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>استخدم خاصية البحث والتصفية:</strong> يمكنك البحث عن دورة محددة باستخدام شريط البحث، أو تصفية النتائج حسب التصنيف أو المستوى أو مدة الدورة.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>اطلع على تفاصيل الدورة:</strong> قبل التسجيل، اضغط على بطاقة الدورة لمعرفة الوصف التفصيلي، والأهداف التعليمية، ومتطلبات الدورة، واسم المدرب، وعدد الساعات التدريبية.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>سجّل في الدورة:</strong> بعد اختيار الدورة المناسبة، اضغط على زر "التسجيل في الدورة" أو "الالتحاق بالدورة" لإضافتها إلى قائمة دوراتك.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3: Start Learning --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">3</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">البدء في التعلم</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">ابدأ رحلة التعلم واستكشف المحتوى التعليمي</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">بعد التسجيل في الدورة، يمكنك البدء فوراً في التعلم من خلال الخطوات التالية:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>الوصول إلى دوراتك:</strong> من لوحة التحكم، انتقل إلى قسم "دوراتي" لعرض جميع الدورات المسجل فيها. ستظهر الدورات النشطة مع شريط يوضح نسبة التقدم.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>ابدأ من الوحدة الأولى:</strong> اضغط على الدورة المطلوبة ثم ابدأ بالوحدة الأولى. يتم ترتيب المحتوى بشكل تسلسلي لضمان استيعاب المفاهيم بالتدريج.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>شاهد مقاطع الفيديو:</strong> استمع وشاهد مقاطع الفيديو التعليمية بتركيز. يمكنك إيقاف الفيديو مؤقتاً، وإعادته، وتعديل سرعة التشغيل حسب راحتك.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>اقرأ المواد المرفقة:</strong> لكل درس مواد تعليمية مرفقة (ملفات PDF أو عروض تقديمية). قم بتحميلها ومراجعتها لتعزيز فهمك للموضوع.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 4: Virtual Classrooms --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">4</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-video"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">استخدام الفصول الافتراضية</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">حضور الحصص المباشرة والتفاعل مع المدرب</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">توفر المنصة فصولاً افتراضية تفاعلية تتيح لك التعلم المباشر مع المدرب وزملائك:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>الاطلاع على جدول الحصص:</strong> من صفحة الدورة، انتقل إلى تبويب "الفصول الافتراضية" أو "الحصص المباشرة" لمعرفة مواعيد الجلسات القادمة.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>الانضمام إلى الجلسة:</strong> قبل موعد الحصة بخمس دقائق، اضغط على زر "انضمام" أو "Join" للدخول إلى الفصل الافتراضي. تأكد من عمل الكاميرا والميكروفون قبل الدخول.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>التفاعل أثناء الحصة:</strong> يمكنك رفع يدك للسؤال، واستخدام المحادثة النصية (Chat) لطرح الأسئلة، ومشاركة شاشتك عند الحاجة. كما يمكنك المشاركة في الاستطلاعات والأنشطة التفاعلية.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>مشاهدة التسجيلات:</strong> في حال عدم تمكنك من حضور الجلسة المباشرة، يمكنك مشاهدة التسجيل لاحقاً من قسم "التسجيلات" في صفحة الدورة.
                        </div>
                        <div style="background: #eff6ff; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-top: 0.75rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <i class="fas fa-laptop" style="color: #2563eb; margin-top: 3px;"></i>
                            <span style="font-size: 0.875rem; color: #1e40af;"><strong>متطلبات تقنية:</strong> تأكد من توفر اتصال إنترنت مستقر بسرعة لا تقل عن 2 ميجابت/ثانية، ومتصفح حديث، وسماعات أو مكبر صوت.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 5: Interact with Content --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">5</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">التفاعل مع المحتوى والتواصل مع الآخرين</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">المشاركة الفعالة في البيئة التعليمية</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">تتيح المنصة عدة قنوات للتفاعل والتواصل لإثراء تجربتك التعليمية:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>منتديات النقاش:</strong> لكل دورة منتدى نقاش خاص يمكنك من خلاله طرح الأسئلة ومشاركة الأفكار مع زملائك والمدرب. شارك بفاعلية لتعزيز فهمك وبناء شبكة معرفية.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>الرسائل المباشرة:</strong> يمكنك إرسال رسائل خاصة إلى المدرب أو زملائك عبر نظام المراسلة الداخلي للمنصة لطرح استفسارات خاصة أو طلب مساعدة.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>التعليقات والملاحظات:</strong> أثناء مشاهدة المحتوى التعليمي، يمكنك إضافة تعليقات أو ملاحظات على الدروس لمناقشتها مع المدرب لاحقاً.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>العمل الجماعي:</strong> في بعض الدورات، قد يُطلب منك العمل ضمن مجموعة. استخدم أدوات التعاون المتاحة مثل المجموعات المشتركة ومساحات العمل التعاونية لإنجاز المهام الجماعية.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 6: Complete Tasks --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">6</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">إنجاز المهام والأنشطة</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">تنفيذ الأنشطة التعليمية المطلوبة بإتقان</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">تتضمن كل دورة مجموعة من المهام والأنشطة التي يجب إكمالها لاجتياز الدورة بنجاح:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>استعراض المهام المطلوبة:</strong> من صفحة الدورة، انتقل إلى قسم "المهام" أو "الأنشطة" لعرض قائمة بجميع المهام مع مواعيد تسليمها والدرجة المخصصة لكل منها.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>قراءة التعليمات بدقة:</strong> قبل البدء في أي مهمة، اقرأ التعليمات والمعايير المطلوبة بعناية لضمان تلبية جميع المتطلبات.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>إنجاز المهمة:</strong> أكمل المهمة حسب التعليمات المحددة. يمكن أن تكون المهام عبارة عن أنشطة تفاعلية، أو مشاريع عملية، أو تمارين تطبيقية، أو واجبات بحثية.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>مراعاة المواعيد النهائية:</strong> تأكد من إنجاز وتسليم المهام قبل الموعد النهائي المحدد. المهام المتأخرة قد تؤثر على درجاتك النهائية.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 7: Track Progress --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">7</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">متابعة التقدم في التعلم</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">تتبع إنجازاتك ومستوى تقدمك</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">توفر المنصة أدوات متقدمة لمتابعة تقدمك التعليمي بشكل مستمر:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>لوحة المتابعة الشخصية:</strong> من لوحة التحكم الرئيسية، يمكنك الاطلاع على نسبة إكمال كل دورة من خلال أشرطة التقدم الملونة التي تعرض نسبة المحتوى الذي أنجزته.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>سجل الدرجات:</strong> يعرض قسم "الدرجات" جميع درجاتك في الاختبارات والواجبات والأنشطة. يمكنك مقارنة أدائك بالمعدل العام لمعرفة موقعك.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>تقارير الأداء:</strong> يمكنك تحميل تقارير أداء مفصلة توضح نقاط القوة والمجالات التي تحتاج إلى تحسين، مع توصيات لتطوير مستواك.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>الإشعارات والتنبيهات:</strong> فعّل الإشعارات لتصلك تنبيهات حول مواعيد التسليم القادمة، والحصص المباشرة، وأي تحديثات جديدة في الدورة.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 8: Take Exams --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">8</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">أداء الاختبارات</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">التحضير للاختبارات وأداؤها بنجاح</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">تعد الاختبارات جزءاً أساسياً من عملية التقييم في كل دورة تدريبية:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>أنواع الاختبارات:</strong> تشمل المنصة اختبارات متعددة مثل: اختبارات قصيرة بعد كل وحدة (Quiz)، واختبارات نصفية، واختبارات نهائية. لكل نوع وزن محدد في الدرجة الإجمالية.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>الاستعداد للاختبار:</strong> راجع المحتوى التعليمي والملاحظات المرفقة قبل موعد الاختبار. تأكد من استقرار اتصال الإنترنت وتوفر بيئة هادئة للتركيز.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>بدء الاختبار:</strong> انتقل إلى قسم "الاختبارات" في صفحة الدورة، واضغط على "بدء الاختبار" في الموعد المحدد. اقرأ التعليمات بعناية قبل البدء في الإجابة.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>إدارة الوقت:</strong> راقب المؤقت الزمني أعلى الشاشة وتأكد من توزيع وقتك على جميع الأسئلة. يمكنك التنقل بين الأسئلة ووضع علامة على الأسئلة التي تريد مراجعتها.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>تسليم الاختبار:</strong> بعد الانتهاء من جميع الأسئلة، راجع إجاباتك ثم اضغط "تسليم الاختبار". ستظهر نتيجتك فوراً للاختبارات الموضوعية، أو بعد تصحيح المدرب للاختبارات المقالية.
                        </div>
                        <div style="background: #fef2f2; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-top: 0.75rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <i class="fas fa-exclamation-triangle" style="color: #dc2626; margin-top: 3px;"></i>
                            <span style="font-size: 0.875rem; color: #991b1b;"><strong>تنبيه هام:</strong> لا تقم بتحديث الصفحة أو إغلاق المتصفح أثناء أداء الاختبار، حيث قد يؤدي ذلك إلى فقدان إجاباتك أو احتساب المحاولة.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 9: Submit Assignments --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">9</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">تسليم الواجبات والمشاريع</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">رفع الملفات وتسليم الأعمال المطلوبة</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">يعد تسليم الواجبات في الوقت المحدد أمراً ضرورياً لاجتياز الدورة بنجاح:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>الوصول إلى الواجب:</strong> من صفحة الدورة، انتقل إلى قسم "الواجبات" أو "المهام". ستجد قائمة بجميع الواجبات مع تواريخ الاستحقاق وتعليمات كل واجب.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>تجهيز الملفات:</strong> أعدّ ملف الواجب بالصيغة المطلوبة (PDF أو Word أو غيرها حسب تعليمات المدرب). تأكد من أن حجم الملف لا يتجاوز الحد الأقصى المسموح به.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>رفع الملف:</strong> اضغط على زر "رفع الملف" أو "إضافة تسليم"، ثم اختر الملف من جهازك. يمكنك أيضاً سحب الملف وإفلاته في منطقة الرفع مباشرة.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>إضافة تعليق (اختياري):</strong> يمكنك إضافة تعليق أو ملاحظة للمدرب مع التسليم لتوضيح أي نقطة أو طرح استفسار.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>تأكيد التسليم:</strong> بعد رفع الملف، اضغط "تسليم" لتأكيد عملية الإرسال. ستتلقى إشعاراً بنجاح التسليم، ويمكنك مراجعة حالة الواجب والدرجة بعد تصحيح المدرب.
                        </div>
                        <div style="background: #f0fdf4; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-top: 0.75rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <i class="fas fa-check-circle" style="color: #16a34a; margin-top: 3px;"></i>
                            <span style="font-size: 0.875rem; color: #166534;"><strong>نصيحة:</strong> قم بتسليم الواجب قبل الموعد النهائي بيوم على الأقل لتجنب أي مشاكل تقنية قد تحدث في اللحظات الأخيرة.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Section --}}
        <div style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem; border: 2px solid #e0f2fe;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #0e7490; margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-star" style="color: #06b6d4;"></i>
                ملخص الخطوات الأساسية
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f0f9ff; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #06b6d4, #2563eb); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">1</span>
                    <span style="font-size: 0.9rem; color: #1e3a5f;">تسجيل الدخول</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f0f9ff; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #06b6d4, #2563eb); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">2</span>
                    <span style="font-size: 0.9rem; color: #1e3a5f;">اختيار الدورات</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f0f9ff; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #06b6d4, #2563eb); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">3</span>
                    <span style="font-size: 0.9rem; color: #1e3a5f;">البدء في التعلم</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f0f9ff; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #06b6d4, #2563eb); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">4</span>
                    <span style="font-size: 0.9rem; color: #1e3a5f;">الفصول الافتراضية</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f0f9ff; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #06b6d4, #2563eb); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">5</span>
                    <span style="font-size: 0.9rem; color: #1e3a5f;">التفاعل والتواصل</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f0f9ff; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #06b6d4, #2563eb); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">6</span>
                    <span style="font-size: 0.9rem; color: #1e3a5f;">إنجاز المهام</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f0f9ff; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #06b6d4, #2563eb); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">7</span>
                    <span style="font-size: 0.9rem; color: #1e3a5f;">متابعة التقدم</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f0f9ff; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #06b6d4, #2563eb); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">8</span>
                    <span style="font-size: 0.9rem; color: #1e3a5f;">أداء الاختبارات</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #f0f9ff; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #06b6d4, #2563eb); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">9</span>
                    <span style="font-size: 0.9rem; color: #1e3a5f;">تسليم الواجبات</span>
                </div>
            </div>
        </div>

        {{-- Support Section --}}
        <div style="background: linear-gradient(135deg, #ecfeff, #eff6ff); border-radius: 1rem; padding: 2rem; text-align: center;">
            <i class="fas fa-headset" style="font-size: 2.5rem; color: #0891b2; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 0.75rem 0;">هل تحتاج إلى مساعدة؟</h3>
            <p style="color: #4b5563; line-height: 1.8; margin: 0 0 1.25rem 0; max-width: 600px; margin-left: auto; margin-right: auto;">
                فريق الدعم الفني متاح لمساعدتك في أي وقت. لا تتردد في التواصل معنا عبر أي من القنوات التالية:
            </p>
            <div style="display: flex; justify-content: center; gap: 1.5rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #0e7490;">
                    <i class="fas fa-envelope"></i>
                    <span>help@company.sa</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #0e7490;">
                    <i class="fas fa-phone"></i>
                    <span>9200343222</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #0e7490;">
                    <i class="fas fa-clock"></i>
                    <span>الأحد - الخميس: 8 صباحاً - 4 مساءً</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
