@extends('layouts.front')

@section('title', 'دليل المعلم الإرشادي')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
    .teacher-guide-page * {
        font-family: 'Cairo', sans-serif;
    }
    .step-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .step-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        border-color: #f59e0b;
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
        background: linear-gradient(135deg, #f59e0b, #ea580c);
    }
    .step-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: #d97706;
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
    }
    .guide-header-gradient {
        background: linear-gradient(135deg, #f59e0b, #ea580c);
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
        background: #f59e0b;
    }
    .info-box {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border-right: 4px solid #f59e0b;
    }
</style>
@endsection

@section('content')
<div class="teacher-guide-page" style="min-height: 100vh; background-color: #f9fafb; padding: 3rem 0;">
    <div style="max-width: 960px; margin: 0 auto; padding: 0 1rem;">

        {{-- Header --}}
        <div class="guide-header-gradient" style="border-radius: 1rem; padding: 2.5rem; color: #fff; margin-bottom: 2rem; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -30px; left: -30px; width: 120px; height: 120px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -40px; right: -20px; width: 160px; height: 160px; background: rgba(255,255,255,0.08); border-radius: 50%;"></div>
            <div style="position: relative; z-index: 1;">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div style="background: rgba(255,255,255,0.2); border-radius: 12px; padding: 12px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chalkboard-teacher" style="font-size: 2rem;"></i>
                    </div>
                    <div>
                        <h1 style="font-size: 1.875rem; font-weight: 700; margin: 0;">دليل المعلم الإرشادي</h1>
                        <p style="margin: 0.25rem 0 0 0; opacity: 0.9; font-size: 1rem;">دليلك الشامل لإدارة العملية التعليمية على المنصة</p>
                    </div>
                </div>
                <p style="font-size: 0.95rem; opacity: 0.85; line-height: 1.8; margin: 0;">
                    يهدف هذا الدليل إلى تمكين المدربين والمعلمين من استخدام جميع أدوات المنصة التعليمية بكفاءة عالية، بدءاً من إنشاء المحتوى التعليمي
                    وحتى تقييم المتدربين ومتابعة تقدمهم. نرجو مراجعة الخطوات التالية لضمان تقديم تجربة تعليمية متميزة.
                </p>
            </div>
            <div style="margin-top: 1.25rem; display: flex; gap: 1.5rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.15); border-radius: 999px; padding: 0.4rem 1rem; font-size: 0.85rem;">
                    <i class="fas fa-check-circle"></i>
                    <span>معيار المركز الوطني 1.3.2</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.15); border-radius: 999px; padding: 0.4rem 1rem; font-size: 0.85rem;">
                    <i class="fas fa-clock"></i>
                    <span>وقت القراءة: 8 دقائق</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; background: rgba(255,255,255,0.15); border-radius: 999px; padding: 0.4rem 1rem; font-size: 0.85rem;">
                    <i class="fas fa-list-ol"></i>
                    <span>5 خطوات أساسية</span>
                </div>
            </div>
        </div>

        {{-- Important Notice --}}
        <div class="info-box" style="border-radius: 0.75rem; padding: 1.25rem 1.5rem; margin-bottom: 2rem;">
            <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                <i class="fas fa-info-circle" style="color: #d97706; font-size: 1.25rem; margin-top: 3px;"></i>
                <div>
                    <p style="font-weight: 600; color: #92400e; margin: 0 0 0.25rem 0;">ملاحظة هامة للمعلمين</p>
                    <p style="color: #374151; margin: 0; line-height: 1.8; font-size: 0.95rem;">
                        هذا الدليل متاح للجميع ولا يتطلب تسجيل الدخول للاطلاع عليه، وذلك وفقاً لمتطلبات المركز الوطني للتعليم الإلكتروني (NELC).
                        يُرجى الالتزام بمعايير الجودة المعتمدة عند إعداد المحتوى التعليمي وتصميم الأنشطة والاختبارات لضمان تحقيق أفضل مخرجات تعليمية.
                    </p>
                </div>
            </div>
        </div>

        {{-- Step 1: Login --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
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
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">الوصول إلى لوحة تحكم المعلم</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">للدخول إلى المنصة بصفتك معلماً أو مدرباً، اتبع الخطوات التالية:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>زيارة الموقع الإلكتروني:</strong> افتح المتصفح وانتقل إلى الصفحة الرئيسية للمنصة. يُنصح باستخدام متصفح Google Chrome أو Microsoft Edge المحدّث لضمان التوافق الكامل مع جميع الأدوات.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>الضغط على "تسجيل الدخول":</strong> انقر على زر تسجيل الدخول الموجود في الشريط العلوي للصفحة الرئيسية للانتقال إلى نموذج الدخول.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>إدخال بيانات الاعتماد:</strong> أدخل البريد الإلكتروني المؤسسي وكلمة المرور المخصصة لحسابك كمعلم. هذه البيانات يتم تزويدك بها من قبل إدارة المعهد عند تعيينك.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>الوصول إلى لوحة التحكم:</strong> بعد تسجيل الدخول بنجاح، سيتم توجيهك تلقائياً إلى لوحة تحكم المعلم حيث يمكنك إدارة الدورات، ومراجعة أعمال الطلاب، والوصول إلى جميع الأدوات التعليمية.
                        </div>
                        <div style="background: #fffbeb; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-top: 0.75rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <i class="fas fa-shield-alt" style="color: #d97706; margin-top: 3px;"></i>
                            <span style="font-size: 0.875rem; color: #92400e;"><strong>أمان الحساب:</strong> احرص على تغيير كلمة المرور الافتراضية عند أول تسجيل دخول، واستخدم كلمة مرور قوية تتكون من 8 أحرف على الأقل تشمل أحرفاً كبيرة وصغيرة وأرقاماً ورموزاً.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 2: Create Courses --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">2</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-folder-plus"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">إنشاء الدورات التدريبية وإدارتها</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">بناء المحتوى التعليمي وتنظيمه</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">تتيح لك المنصة إنشاء دورات تدريبية متكاملة بمحتوى غني ومتنوع. اتبع الخطوات التالية:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>إنشاء دورة جديدة:</strong> من لوحة تحكم المعلم، انتقل إلى قسم "إدارة الدورات" واضغط على "إنشاء دورة جديدة". قم بتعبئة المعلومات الأساسية مثل: اسم الدورة، والوصف التفصيلي، والأهداف التعليمية، ومتطلبات الالتحاق، وعدد الساعات التدريبية.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>تصميم هيكل الدورة:</strong> قسّم الدورة إلى وحدات تعليمية مترابطة ومنطقية. كل وحدة يمكن أن تحتوي على عدة دروس، ولكل درس محتوى محدد. يُنصح باتباع مبدأ التدرج من البسيط إلى المعقد.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>رفع المحتوى التعليمي:</strong> أضف المحتوى المتنوع لكل درس، بما في ذلك: مقاطع الفيديو التعليمية (يُفضل ألا تتجاوز مدة الفيديو الواحد 15 دقيقة)، والعروض التقديمية، والملفات النصية (PDF)، والصور التوضيحية، والروابط المرجعية.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>تحديد إعدادات الدورة:</strong> حدد تاريخ بدء الدورة وانتهائها، ونوع التسجيل (مفتوح أو بموافقة المعلم)، والحد الأقصى لعدد المتدربين، ومتطلبات إكمال الدورة (مثل نسبة الحضور والدرجة الدنيا للنجاح).
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>نشر الدورة:</strong> بعد مراجعة المحتوى والإعدادات، اضغط "نشر الدورة" لإتاحتها للمتدربين. يمكنك تعديل المحتوى في أي وقت حتى بعد النشر.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 3: Virtual Classrooms --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">3</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-video"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">استخدام الفصول الافتراضية</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">إدارة الحصص المباشرة وجلسات التدريب</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">تمكّنك الفصول الافتراضية من تقديم حصص تدريبية مباشرة بتفاعل حقيقي مع المتدربين:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>جدولة جلسة جديدة:</strong> من صفحة إدارة الدورة، انتقل إلى تبويب "الفصول الافتراضية" واضغط على "جدولة جلسة جديدة". حدد عنوان الجلسة، والتاريخ والوقت، والمدة المتوقعة، وأضف وصفاً مختصراً لموضوع الجلسة.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>إعداد الجلسة:</strong> قبل موعد الجلسة، تأكد من تجهيز المواد التعليمية والعروض التقديمية التي ستحتاجها. اختبر الكاميرا والميكروفون واتصال الإنترنت للتأكد من جودة البث.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>بدء الجلسة وإدارتها:</strong> ادخل إلى الفصل الافتراضي قبل الموعد بعشر دقائق. خلال الجلسة يمكنك: مشاركة شاشتك لعرض المحتوى، واستخدام السبورة البيضاء التفاعلية للشرح، وإطلاق استطلاعات رأي فورية، وتقسيم المتدربين إلى مجموعات عمل (Breakout Rooms).
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>تسجيل الحضور:</strong> يتم تسجيل حضور المتدربين تلقائياً عند دخولهم الفصل الافتراضي. يمكنك مراجعة سجل الحضور بعد انتهاء الجلسة من قسم التقارير.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>تسجيل الجلسة:</strong> فعّل خاصية التسجيل في بداية الجلسة ليتمكن المتدربون الغائبون من مشاهدتها لاحقاً. سيتم حفظ التسجيل تلقائياً في صفحة الدورة.
                        </div>
                        <div style="background: #eff6ff; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-top: 0.75rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <i class="fas fa-lightbulb" style="color: #2563eb; margin-top: 3px;"></i>
                            <span style="font-size: 0.875rem; color: #1e40af;"><strong>نصيحة:</strong> حافظ على تفاعل المتدربين من خلال طرح أسئلة دورية، واستخدام الأنشطة التفاعلية كل 10-15 دقيقة لكسر رتابة المحاضرة.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 4: Create Exams and Assignments --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">4</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">إنشاء الاختبارات والواجبات</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">تصميم أدوات التقييم والقياس</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">يعد التقييم ركيزة أساسية في العملية التعليمية. توفر المنصة أدوات متنوعة لإنشاء الاختبارات والواجبات:</p>

                        {{-- Exams Section --}}
                        <div style="background: #fefce8; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.25rem;">
                            <h4 style="font-size: 1rem; font-weight: 700; color: #854d0e; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-file-alt"></i>
                                إنشاء الاختبارات
                            </h4>
                            <div class="sub-step" style="margin-bottom: 0.75rem;">
                                <strong>إنشاء اختبار جديد:</strong> من صفحة الدورة، انتقل إلى "الاختبارات" واضغط "إنشاء اختبار جديد". حدد اسم الاختبار ونوعه (قصير، نصفي، نهائي) ودرجته.
                            </div>
                            <div class="sub-step" style="margin-bottom: 0.75rem;">
                                <strong>إضافة الأسئلة:</strong> أضف الأسئلة بأنواعها المختلفة: اختيار من متعدد، صح وخطأ، إجابة قصيرة، مقالية، مطابقة، وترتيب. حدد الدرجة المخصصة لكل سؤال والإجابة الصحيحة للتصحيح الآلي.
                            </div>
                            <div class="sub-step" style="margin-bottom: 0.75rem;">
                                <strong>ضبط إعدادات الاختبار:</strong> حدد المدة الزمنية للاختبار، وعدد المحاولات المسموحة، وترتيب الأسئلة (ثابت أو عشوائي)، وخيار إظهار النتيجة فورياً أو بعد التصحيح اليدوي، وتاريخ الإتاحة وإغلاق الاختبار.
                            </div>
                        </div>

                        {{-- Assignments Section --}}
                        <div style="background: #fff7ed; border-radius: 0.75rem; padding: 1.25rem;">
                            <h4 style="font-size: 1rem; font-weight: 700; color: #9a3412; margin: 0 0 0.75rem 0; display: flex; align-items: center; gap: 0.5rem;">
                                <i class="fas fa-edit"></i>
                                إنشاء الواجبات
                            </h4>
                            <div class="sub-step" style="margin-bottom: 0.75rem;">
                                <strong>إنشاء واجب جديد:</strong> من قسم "الواجبات"، اضغط "إنشاء واجب جديد". اكتب عنوان الواجب ووصفاً تفصيلياً يوضح المطلوب بدقة، وحدد معايير التقييم والدرجة الكاملة.
                            </div>
                            <div class="sub-step" style="margin-bottom: 0.75rem;">
                                <strong>تحديد متطلبات التسليم:</strong> حدد صيغ الملفات المقبولة (PDF، Word، وغيرها)، والحد الأقصى لحجم الملف، وتاريخ التسليم النهائي، وهل يُسمح بالتسليم المتأخر أم لا.
                            </div>
                            <div class="sub-step" style="margin-bottom: 0.75rem;">
                                <strong>تصحيح الواجبات:</strong> بعد تسليم المتدربين لواجباتهم، انتقل إلى قسم "التسليمات" لمراجعة كل واجب. يمكنك تحميل الملف المرفق، ومنح الدرجة، وكتابة ملاحظات تفصيلية للمتدرب حول نقاط القوة ومجالات التحسين.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 5: Respond to Student Queries --}}
        <div class="step-card" style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem;">
            <div style="display: flex; align-items: flex-start; gap: 1.25rem;">
                <div>
                    <div class="step-number">5</div>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div class="step-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <div>
                            <h2 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0;">الرد على استفسارات المتدربين</h2>
                            <p style="color: #6b7280; font-size: 0.875rem; margin: 0.25rem 0 0 0;">التواصل الفعال وتقديم الدعم المستمر</p>
                        </div>
                    </div>
                    <div style="color: #374151; line-height: 1.9; font-size: 0.95rem;">
                        <p style="margin-bottom: 1rem;">يُعد التواصل مع المتدربين والرد على استفساراتهم من أهم عوامل نجاح العملية التعليمية. توفر المنصة قنوات متعددة لذلك:</p>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>منتديات النقاش:</strong> تابع منتدى النقاش الخاص بكل دورة بشكل دوري. قم بالرد على أسئلة المتدربين خلال 24 ساعة كحد أقصى. شجّع الحوار البنّاء بين المتدربين وأضف قيمة علمية في كل مشاركة.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>الرسائل الخاصة:</strong> تحقق بانتظام من صندوق الرسائل للرد على الاستفسارات الشخصية التي قد يحرج المتدرب من طرحها علناً. تعامل مع كل رسالة باحترافية وسرية.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>الإعلانات والتنبيهات:</strong> استخدم أداة الإعلانات لإرسال تنبيهات مهمة لجميع متدربي الدورة، مثل: تذكير بمواعيد الاختبارات، أو تغييرات في الجدول، أو مستجدات تتعلق بالمحتوى التعليمي.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>ساعات الإرشاد المكتبية:</strong> خصص ساعات محددة أسبوعياً لعقد جلسات إرشاد افتراضية فردية أو جماعية مع المتدربين الذين يحتاجون إلى دعم إضافي. أعلن عن هذه المواعيد في صفحة الدورة.
                        </div>
                        <div class="sub-step" style="margin-bottom: 0.75rem;">
                            <strong>التغذية الراجعة البنّاءة:</strong> عند تقييم أعمال المتدربين، احرص على تقديم تغذية راجعة تفصيلية وبنّاءة تساعدهم على التحسن. وضّح نقاط القوة قبل مجالات التطوير، وقدّم توجيهات عملية واضحة.
                        </div>
                        <div style="background: #f0fdf4; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-top: 0.75rem; display: flex; align-items: flex-start; gap: 0.5rem;">
                            <i class="fas fa-heart" style="color: #16a34a; margin-top: 3px;"></i>
                            <span style="font-size: 0.875rem; color: #166534;"><strong>قاعدة ذهبية:</strong> تعامل مع كل استفسار على أنه فرصة تعليمية. الرد السريع والمهني يعزز ثقة المتدرب ويزيد من تفاعله ودافعيته للتعلم.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Summary Section --}}
        <div style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem; border: 2px solid #fef3c7;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #92400e; margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-star" style="color: #f59e0b;"></i>
                ملخص خطوات المعلم
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #fffbeb; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #f59e0b, #ea580c); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">1</span>
                    <span style="font-size: 0.9rem; color: #78350f;">تسجيل الدخول</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #fffbeb; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #f59e0b, #ea580c); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">2</span>
                    <span style="font-size: 0.9rem; color: #78350f;">إنشاء الدورات</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #fffbeb; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #f59e0b, #ea580c); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">3</span>
                    <span style="font-size: 0.9rem; color: #78350f;">الفصول الافتراضية</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #fffbeb; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #f59e0b, #ea580c); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">4</span>
                    <span style="font-size: 0.9rem; color: #78350f;">الاختبارات والواجبات</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem; background: #fffbeb; border-radius: 0.5rem; padding: 0.75rem 1rem;">
                    <span style="background: linear-gradient(135deg, #f59e0b, #ea580c); color: #fff; width: 28px; height: 28px; min-width: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700;">5</span>
                    <span style="font-size: 0.9rem; color: #78350f;">الرد على الاستفسارات</span>
                </div>
            </div>
        </div>

        {{-- Best Practices Section --}}
        <div style="background: #fff; border-radius: 1rem; padding: 2rem; margin-bottom: 1.5rem; border: 1px solid #e5e7eb;">
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 1.25rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-award" style="color: #f59e0b;"></i>
                أفضل الممارسات للمعلم المتميز
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem;">
                <div style="background: #fefce8; border-radius: 0.75rem; padding: 1rem 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-clock" style="color: #d97706;"></i>
                        <strong style="color: #92400e;">الالتزام بالمواعيد</strong>
                    </div>
                    <p style="color: #4b5563; font-size: 0.875rem; line-height: 1.7; margin: 0;">ابدأ الحصص في مواعيدها وأعلن عن الاختبارات مبكراً. الالتزام يبني الثقة ويعزز احترام المتدربين.</p>
                </div>
                <div style="background: #fefce8; border-radius: 0.75rem; padding: 1rem 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-sync-alt" style="color: #d97706;"></i>
                        <strong style="color: #92400e;">تحديث المحتوى</strong>
                    </div>
                    <p style="color: #4b5563; font-size: 0.875rem; line-height: 1.7; margin: 0;">راجع محتواك التعليمي دورياً وأضف مصادر جديدة ومحدثة لضمان مواكبة أحدث المستجدات في مجال تخصصك.</p>
                </div>
                <div style="background: #fefce8; border-radius: 0.75rem; padding: 1rem 1.25rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-users" style="color: #d97706;"></i>
                        <strong style="color: #92400e;">التنوع في أساليب التدريس</strong>
                    </div>
                    <p style="color: #4b5563; font-size: 0.875rem; line-height: 1.7; margin: 0;">استخدم مزيجاً من الفيديوهات والأنشطة التفاعلية والمناقشات لتلبية أنماط التعلم المختلفة لدى المتدربين.</p>
                </div>
            </div>
        </div>

        {{-- Support Section --}}
        <div style="background: linear-gradient(135deg, #fffbeb, #fff7ed); border-radius: 1rem; padding: 2rem; text-align: center;">
            <i class="fas fa-headset" style="font-size: 2.5rem; color: #d97706; margin-bottom: 1rem;"></i>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #111827; margin: 0 0 0.75rem 0;">هل تحتاج إلى مساعدة تقنية؟</h3>
            <p style="color: #4b5563; line-height: 1.8; margin: 0 0 1.25rem 0; max-width: 600px; margin-left: auto; margin-right: auto;">
                فريق الدعم الفني للمعلمين متاح لمساعدتك في حل أي مشكلة تقنية أو الإجابة عن استفساراتك حول أدوات المنصة.
            </p>
            <div style="display: flex; justify-content: center; gap: 1.5rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #92400e;">
                    <i class="fas fa-envelope"></i>
                    <span>help@company.sa</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #92400e;">
                    <i class="fas fa-phone"></i>
                    <span>9200343222</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem; color: #92400e;">
                    <i class="fas fa-clock"></i>
                    <span>الأحد - الخميس: 8 صباحاً - 4 مساءً</span>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
