@extends('layouts.front')

@section('title', 'سياسة الحضور')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<style>
    .policy-content h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
    .policy-content h3 {
        font-size: 1.15rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.75rem;
    }
    .policy-content p {
        color: #4b5563;
        line-height: 1.9;
        margin-bottom: 1rem;
    }
    .policy-content ul {
        list-style: none;
        padding: 0;
        margin-bottom: 1rem;
    }
    .policy-content ul li {
        position: relative;
        padding-right: 1.5rem;
        padding-top: 0.35rem;
        padding-bottom: 0.35rem;
        color: #4b5563;
        line-height: 1.8;
    }
    .policy-content ul li::before {
        content: "\25CF";
        color: #059669;
        position: absolute;
        right: 0;
        top: 0.35rem;
        font-size: 0.6rem;
    }
    .policy-content .info-box {
        background: #ecfdf5;
        border-right: 4px solid #059669;
        padding: 1rem 1.25rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .policy-content .warning-box {
        background: #fef2f2;
        border-right: 4px solid #dc2626;
        padding: 1rem 1.25rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .policy-content .highlight-box {
        background: #eff6ff;
        border-right: 4px solid #2563eb;
        padding: 1rem 1.25rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }
    .policy-content .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    .policy-content .stat-card {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 0.75rem;
        padding: 1.25rem;
        text-align: center;
    }
    .policy-content .stat-card .stat-number {
        font-size: 2rem;
        font-weight: 800;
        color: #059669;
        display: block;
    }
    .policy-content .stat-card .stat-label {
        color: #6b7280;
        font-size: 0.9rem;
        margin-top: 0.25rem;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-12" style="direction: rtl;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Card -->
        <div class="rounded-t-2xl p-8 text-white" style="background: linear-gradient(135deg, #059669, #047857);">
            <h1 class="text-3xl font-bold">سياسة الحضور</h1>
            <p class="mt-2" style="color: #a7f3d0;">تنظيم الحضور الإلكتروني وضمان الالتزام بمتطلبات التعلم</p>
            <div class="mt-3 flex items-center gap-2 text-sm" style="color: #d1fae5;">
                <span>معيار NELC: 1.1.9</span>
                <span>|</span>
                <span>آخر تحديث: {{ date('Y-m-d') }}</span>
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-b-2xl shadow-xl p-8 space-y-8 policy-content">

            <!-- المقدمة -->
            <section>
                <h2>1. المقدمة</h2>
                <p>
                    يعتمد معهد الارتقاء العالي للتدريب نظام حضور إلكتروني متكامل يضمن متابعة انتظام المتدربين في برامجهم التدريبية. تم إعداد هذه السياسة بما يتوافق مع معايير المركز الوطني للتعليم الإلكتروني (NELC) ومتطلبات المؤسسة العامة للتدريب التقني والمهني لضمان جودة العملية التعليمية.
                </p>
                <div class="info-box">
                    <p class="mb-0"><strong>مبدأ أساسي:</strong> يعادل الحضور الإلكتروني عبر منصات التعلم الإلكتروني الحضور الفعلي التقليدي من حيث القيمة والاعتماد، ويخضع لنفس الضوابط والمتطلبات المنصوص عليها في أنظمة التدريب المعتمدة.</p>
                </div>
            </section>

            <!-- أنواع الحضور -->
            <section>
                <h2>2. أنواع الحضور الإلكتروني</h2>

                <h3>2.1 الحضور التزامني (Synchronous)</h3>
                <p>الحضور المباشر في الفصول الافتراضية والمحاضرات المتزامنة، ويتم تسجيله من خلال:</p>
                <ul>
                    <li>تسجيل الدخول إلى الفصل الافتراضي في الوقت المحدد</li>
                    <li>التواجد الفعلي طوال مدة الجلسة (لا يقل عن 75% من وقت الجلسة)</li>
                    <li>التفاعل والمشاركة الفعالة أثناء الجلسة</li>
                    <li>إتمام الأنشطة والمهام المطلوبة خلال الجلسة</li>
                </ul>

                <h3>2.2 الحضور غير التزامني (Asynchronous)</h3>
                <p>التفاعل مع المحتوى التعليمي في أوقات مرنة، ويتم قياسه من خلال:</p>
                <ul>
                    <li>مشاهدة المحتوى التعليمي المسجل (فيديو، صوتي، تفاعلي)</li>
                    <li>إكمال الأنشطة والتمارين التفاعلية في المقرر</li>
                    <li>المشاركة في منتديات النقاش وإنجاز الواجبات المطلوبة</li>
                    <li>إتمام التقييمات الذاتية والاختبارات القصيرة</li>
                </ul>
            </section>

            <!-- الحد الأدنى للحضور -->
            <section>
                <h2>3. الحد الأدنى لساعات الحضور</h2>

                <div class="stat-grid">
                    <div class="stat-card">
                        <span class="stat-number">75%</span>
                        <span class="stat-label">الحد الأدنى للحضور الكلي</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">25%</span>
                        <span class="stat-label">الحد الأدنى عبر الفصول الافتراضية</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-number">100%</span>
                        <span class="stat-label">حضور الاختبارات إلزامي</span>
                    </div>
                </div>

                <h3>3.1 متطلبات الحضور التزامني</h3>
                <div class="highlight-box">
                    <p class="mb-0"><strong>الفصول الافتراضية:</strong> يجب ألا يقل حضور المتدرب عبر الفصول الافتراضية (التعلم التزامني) عن <strong>25%</strong> من إجمالي ساعات البرنامج التدريبي، وذلك تماشياً مع معيار NELC 1.1.9. ويتم تسجيل الحضور آلياً من خلال نظام إدارة التعلم.</p>
                </div>

                <h3>3.2 متطلبات الحضور الكلي</h3>
                <ul>
                    <li>يجب ألا يقل إجمالي ساعات الحضور (تزامني + غير تزامني) عن <strong>75%</strong> من إجمالي ساعات البرنامج</li>
                    <li>يتم احتساب ساعات الحضور غير التزامني بناءً على التفاعل الفعلي مع المحتوى والأنشطة</li>
                    <li>حضور جلسات الاختبارات إلزامي بنسبة <strong>100%</strong> ولا يقبل أي عذر للغياب إلا بموافقة رسمية</li>
                    <li>يجب ألا يتجاوز الغياب بدون عذر مقبول <strong>10%</strong> من إجمالي ساعات البرنامج</li>
                </ul>
            </section>

            <!-- آلية تسجيل الحضور -->
            <section>
                <h2>4. آلية تسجيل الحضور الإلكتروني</h2>
                <p>يتم تسجيل ومتابعة الحضور الإلكتروني من خلال الآليات التالية:</p>
                <ul>
                    <li><strong>نظام إدارة التعلم (LMS):</strong> يسجل تلقائياً أوقات الدخول والخروج ومدة التواجد ومستوى التفاعل مع المحتوى</li>
                    <li><strong>تقارير الفصول الافتراضية:</strong> يتم استخراج تقارير مفصلة من نظام الفصول الافتراضية تتضمن وقت الدخول والمغادرة ومدة الحضور</li>
                    <li><strong>تتبع xAPI/SCORM:</strong> تتبع تقدم المتدرب في المحتوى التعليمي ونسبة إنجاز الأنشطة</li>
                    <li><strong>سجلات التقييم:</strong> تسجيل حضور الاختبارات والتقييمات المحددة بمواعيد</li>
                </ul>
                <div class="info-box">
                    <p class="mb-0"><strong>التحقق من الهوية:</strong> قد يتم استخدام آليات التحقق من الهوية مثل التعرف على الوجه أو رمز التحقق العشوائي أثناء الفصول الافتراضية لضمان الحضور الفعلي للمتدرب.</p>
                </div>
            </section>

            <!-- الغياب والأعذار -->
            <section>
                <h2>5. الغياب والأعذار المقبولة</h2>

                <h3>5.1 الأعذار المقبولة</h3>
                <ul>
                    <li>المرض بموجب تقرير طبي معتمد من جهة صحية رسمية</li>
                    <li>الوفاة لأحد الأقارب من الدرجة الأولى (بحد أقصى ثلاثة أيام)</li>
                    <li>المشاكل التقنية الموثقة التي تمنع الوصول للمنصة (بشرط إبلاغ الدعم الفني فوراً)</li>
                    <li>الظروف القاهرة والكوارث الطبيعية المثبتة</li>
                    <li>التكليف الرسمي من جهة العمل (للموظفين) بموجب خطاب رسمي</li>
                </ul>

                <h3>5.2 إجراءات تقديم العذر</h3>
                <ul>
                    <li>يجب تقديم العذر خلال ثلاثة (3) أيام عمل من تاريخ الغياب</li>
                    <li>يتم تقديم العذر عبر المنصة الإلكترونية مع إرفاق المستندات الداعمة</li>
                    <li>يتم مراجعة الأعذار من قبل الإدارة الأكاديمية خلال يومي (2) عمل</li>
                    <li>يُبلغ المتدرب بالقرار عبر البريد الإلكتروني وإشعار على المنصة</li>
                </ul>
            </section>

            <!-- إجراءات عدم الالتزام -->
            <section>
                <h2>6. إجراءات عدم الالتزام بالحضور</h2>
                <p>يتم اتخاذ الإجراءات التالية في حال عدم التزام المتدرب بمتطلبات الحضور:</p>

                <h3>6.1 التنبيهات المبكرة</h3>
                <ul>
                    <li><strong>التنبيه الأول:</strong> إشعار تلقائي عند وصول نسبة الغياب إلى <strong>10%</strong> من إجمالي ساعات البرنامج</li>
                    <li><strong>التنبيه الثاني:</strong> إشعار رسمي من المشرف الأكاديمي عند وصول نسبة الغياب إلى <strong>15%</strong></li>
                    <li><strong>الإنذار:</strong> إنذار كتابي رسمي عند وصول نسبة الغياب إلى <strong>20%</strong> مع إتاحة فرصة لتعويض الغياب</li>
                </ul>

                <h3>6.2 العقوبات</h3>
                <ul>
                    <li><strong>تجاوز 25% غياب:</strong> الحرمان من دخول الاختبار النهائي والرسوب في المقرر</li>
                    <li><strong>تجاوز 25% غياب بعذر مقبول:</strong> اعتبار المتدرب منسحباً ومنحه فرصة إعادة التسجيل في الدورة القادمة</li>
                    <li><strong>الغياب المتعمد والمتكرر:</strong> إحالة الموضوع للجنة التأديبية واتخاذ الإجراءات المناسبة</li>
                    <li><strong>التلاعب بسجلات الحضور:</strong> تطبيق عقوبات النزاهة الأكاديمية بالإضافة إلى عقوبات الغياب</li>
                </ul>
            </section>

            <!-- تعويض الغياب -->
            <section>
                <h2>7. آلية تعويض الغياب</h2>
                <p>يمكن للمتدرب تعويض جزء من ساعات الغياب من خلال:</p>
                <ul>
                    <li>حضور جلسات تعويضية يحددها المدرب خلال أوقات بديلة</li>
                    <li>مشاهدة تسجيلات الفصول الافتراضية وإتمام المهام المرتبطة بها (للجلسات غير التزامنية فقط)</li>
                    <li>إنجاز أنشطة ومهام إضافية يحددها المدرب كبديل للحضور الفائت</li>
                    <li>يجب إتمام التعويض خلال أسبوعين من تاريخ الغياب</li>
                </ul>
                <div class="warning-box">
                    <p class="mb-0"><strong>ملاحظة:</strong> لا يمكن تعويض ساعات الحضور التزامني (الفصول الافتراضية المباشرة) بالكامل عبر أنشطة غير تزامنية. يجب أن يحقق المتدرب الحد الأدنى المطلوب (25%) من ساعات الحضور التزامني.</p>
                </div>
            </section>

            <!-- مسؤوليات -->
            <section>
                <h2>8. المسؤوليات</h2>
                <ul>
                    <li><strong>المتدرب:</strong> الالتزام بمتطلبات الحضور، والتحقق المستمر من سجل الحضور، وتقديم الأعذار في المواعيد المحددة</li>
                    <li><strong>المدرب:</strong> تسجيل الحضور بدقة، ومتابعة نسب الغياب، والتواصل مع المتدربين المتغيبين</li>
                    <li><strong>الإدارة الأكاديمية:</strong> مراجعة سجلات الحضور، والبت في الأعذار، وتطبيق الإجراءات التأديبية</li>
                    <li><strong>الدعم الفني:</strong> ضمان عمل أنظمة تسجيل الحضور بشكل صحيح ومعالجة المشاكل التقنية فوراً</li>
                </ul>
            </section>

        </div>
    </div>
</div>
@endsection
