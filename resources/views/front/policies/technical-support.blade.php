@extends('layouts.front')

@section('title', 'الدعم الفني')

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
        color: #0891b2;
        position: absolute;
        right: 0;
        top: 0.35rem;
        font-size: 0.6rem;
    }
    .policy-content .info-box {
        background: #ecfeff;
        border-right: 4px solid #0891b2;
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
    .policy-content .channel-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        transition: all 0.2s;
    }
    .policy-content .channel-card:hover {
        border-color: #0891b2;
        box-shadow: 0 2px 8px rgba(8, 145, 178, 0.1);
    }
    .policy-content .channel-card .channel-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #0891b2;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .policy-content .channel-card h4 {
        color: #0891b2;
        font-weight: 700;
        font-size: 1.05rem;
        margin-bottom: 0.25rem;
    }
    .policy-content .sla-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5rem;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .policy-content .sla-table th {
        background: #0891b2;
        color: white;
        padding: 0.75rem 1rem;
        text-align: right;
        font-weight: 600;
    }
    .policy-content .sla-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        color: #4b5563;
    }
    .policy-content .sla-table tr:nth-child(even) {
        background: #f9fafb;
    }
    .policy-content .sla-table tr:hover {
        background: #ecfeff;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-12" style="direction: rtl;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Card -->
        <div class="rounded-t-2xl p-8 text-white" style="background: linear-gradient(135deg, #0891b2, #0e7490);">
            <h1 class="text-3xl font-bold">الدعم الفني</h1>
            <p class="mt-2" style="color: #a5f3fc;">خدمات دعم فني متكاملة لضمان تجربة تعليمية سلسة ومتميزة</p>
            <div class="mt-3 flex items-center gap-2 text-sm" style="color: #cffafe;">
                <span>معيار NELC: 1.3.3</span>
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
                    يوفر معهد الارتقاء العالي للتدريب منظومة دعم فني شاملة تهدف إلى تقديم المساعدة لجميع مستخدمي المنصة التعليمية وحل المشكلات التقنية بكفاءة وسرعة. نسعى لضمان تجربة تعليمية سلسة من خلال توفير قنوات دعم متعددة وفريق متخصص على مدار الساعة.
                </p>
            </section>

            <!-- قنوات الدعم -->
            <section>
                <h2>2. قنوات الدعم الفني</h2>
                <p>يوفر المعهد قنوات متعددة للدعم الفني لضمان سهولة الوصول والاستجابة السريعة:</p>

                <div class="channel-card">
                    <div class="channel-icon">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div>
                        <h4>الاتصال الهاتفي</h4>
                        <p class="mb-0">الرقم الموحد: <strong>9200343222</strong> - متاح خلال ساعات العمل الرسمية من الأحد إلى الخميس. يوفر دعماً فورياً للمشكلات العاجلة والاستفسارات التقنية.</p>
                    </div>
                </div>

                <div class="channel-card">
                    <div class="channel-icon">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div>
                        <h4>البريد الإلكتروني</h4>
                        <p class="mb-0">البريد: <strong>support@ertiqaa.edu.sa</strong> - متاح على مدار الساعة لاستقبال الطلبات. مناسب للمشكلات غير العاجلة والاستفسارات التفصيلية التي تحتاج توثيقاً.</p>
                    </div>
                </div>

                <div class="channel-card">
                    <div class="channel-icon">
                        <i class="bi bi-chat-dots-fill"></i>
                    </div>
                    <div>
                        <h4>المحادثة المباشرة (Live Chat)</h4>
                        <p class="mb-0">متاحة من خلال المنصة التعليمية مباشرة. تتيح التواصل الفوري مع فريق الدعم للحصول على مساعدة سريعة أثناء استخدام المنصة.</p>
                    </div>
                </div>

                <div class="channel-card">
                    <div class="channel-icon">
                        <i class="bi bi-ticket-detailed-fill"></i>
                    </div>
                    <div>
                        <h4>نظام تذاكر الدعم</h4>
                        <p class="mb-0">نظام تذاكر إلكتروني متكامل يتيح تتبع حالة الطلب ومعرفة مراحل المعالجة. يُولد رقم مرجعي فريد لكل طلب لسهولة المتابعة.</p>
                    </div>
                </div>

                <div class="channel-card">
                    <div class="channel-icon">
                        <i class="bi bi-whatsapp"></i>
                    </div>
                    <div>
                        <h4>واتساب الدعم</h4>
                        <p class="mb-0">رقم واتساب مخصص للدعم السريع. يُستخدم للاستفسارات البسيطة والمتابعة السريعة لحالات الدعم القائمة.</p>
                    </div>
                </div>
            </section>

            <!-- الخدمات المشمولة -->
            <section>
                <h2>3. الخدمات المشمولة بالدعم الفني</h2>
                <p>يغطي الدعم الفني الخدمات التالية:</p>

                <h3>3.1 الدعم التقني للمنصة</h3>
                <ul>
                    <li>حل مشكلات تسجيل الدخول واستعادة كلمات المرور</li>
                    <li>معالجة مشاكل الوصول إلى المقررات والمحتوى التعليمي</li>
                    <li>حل مشاكل تشغيل الفيديوهات والمحتوى التفاعلي</li>
                    <li>معالجة أعطال الفصول الافتراضية ومشاكل الصوت والصورة</li>
                    <li>حل مشاكل رفع وتحميل الملفات والواجبات</li>
                    <li>معالجة مشاكل الاختبارات الإلكترونية وتسجيل الدرجات</li>
                </ul>

                <h3>3.2 الدعم التعليمي</h3>
                <ul>
                    <li>المساعدة في التسجيل والانسحاب من المقررات والبرامج</li>
                    <li>الإرشاد حول استخدام أدوات المنصة التعليمية وميزاتها</li>
                    <li>المساعدة في الوصول إلى الموارد والمكتبة الرقمية</li>
                    <li>حل مشاكل شهادات الإتمام والتحقق من الشهادات</li>
                </ul>

                <h3>3.3 الدعم الإداري</h3>
                <ul>
                    <li>الاستفسارات المتعلقة بالرسوم والمدفوعات والفواتير</li>
                    <li>طلبات تحديث البيانات الشخصية وتغيير المعلومات</li>
                    <li>الاستفسار عن السياسات والإجراءات المعتمدة</li>
                </ul>
            </section>

            <!-- ساعات العمل -->
            <section>
                <h2>4. ساعات الدعم الفني</h2>
                <table class="sla-table">
                    <thead>
                        <tr>
                            <th>القناة</th>
                            <th>الأيام</th>
                            <th>الساعات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>الاتصال الهاتفي</strong></td>
                            <td>الأحد - الخميس</td>
                            <td>8:00 صباحاً - 10:00 مساءً</td>
                        </tr>
                        <tr>
                            <td><strong>المحادثة المباشرة</strong></td>
                            <td>الأحد - الخميس</td>
                            <td>8:00 صباحاً - 12:00 منتصف الليل</td>
                        </tr>
                        <tr>
                            <td><strong>البريد الإلكتروني</strong></td>
                            <td>جميع الأيام</td>
                            <td>24 ساعة (الاستقبال)</td>
                        </tr>
                        <tr>
                            <td><strong>نظام التذاكر</strong></td>
                            <td>جميع الأيام</td>
                            <td>24 ساعة (الاستقبال)</td>
                        </tr>
                        <tr>
                            <td><strong>واتساب</strong></td>
                            <td>الأحد - الخميس</td>
                            <td>9:00 صباحاً - 9:00 مساءً</td>
                        </tr>
                    </tbody>
                </table>
                <div class="info-box">
                    <p class="mb-0"><strong>أوقات الذروة:</strong> خلال فترات الاختبارات يتم تمديد ساعات عمل الدعم الفني لتشمل جميع أيام الأسبوع على مدار الساعة لضمان تقديم الدعم اللازم.</p>
                </div>
            </section>

            <!-- زمن الاستجابة -->
            <section>
                <h2>5. زمن الاستجابة المتوقع</h2>
                <table class="sla-table">
                    <thead>
                        <tr>
                            <th>مستوى الأولوية</th>
                            <th>نوع المشكلة</th>
                            <th>زمن الاستجابة الأولية</th>
                            <th>زمن الحل المتوقع</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong style="color: #dc2626;">حرجة</strong></td>
                            <td>عطل كامل في المنصة، مشكلة أثناء اختبار</td>
                            <td>30 دقيقة</td>
                            <td>4 ساعات</td>
                        </tr>
                        <tr>
                            <td><strong style="color: #d97706;">عالية</strong></td>
                            <td>عدم القدرة على الوصول للمقرر، مشكلة فصل افتراضي</td>
                            <td>ساعة واحدة</td>
                            <td>8 ساعات</td>
                        </tr>
                        <tr>
                            <td><strong style="color: #0284c7;">متوسطة</strong></td>
                            <td>مشاكل في الوسائط، صعوبات في رفع الملفات</td>
                            <td>4 ساعات</td>
                            <td>24 ساعة</td>
                        </tr>
                        <tr>
                            <td><strong style="color: #059669;">منخفضة</strong></td>
                            <td>استفسارات عامة، طلبات تحسين، ملاحظات</td>
                            <td>8 ساعات</td>
                            <td>48 ساعة</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- معالجة الشكاوى -->
            <section>
                <h2>6. إجراءات معالجة الشكاوى</h2>

                <h3>6.1 تقديم الشكوى</h3>
                <ul>
                    <li>يمكن تقديم الشكوى عبر أي من قنوات الدعم المتاحة أو عبر نموذج الشكاوى الإلكتروني</li>
                    <li>يتم تسجيل الشكوى ومنح صاحبها رقماً مرجعياً فريداً للمتابعة</li>
                    <li>يتم إرسال تأكيد استلام الشكوى فوراً عبر البريد الإلكتروني أو الرسائل القصيرة</li>
                </ul>

                <h3>6.2 مراحل المعالجة</h3>
                <ul>
                    <li><strong>الاستلام والتصنيف:</strong> يتم تصنيف الشكوى وتحديد مستوى أولويتها خلال ساعة واحدة</li>
                    <li><strong>التحقيق والتشخيص:</strong> يقوم الفريق المختص بدراسة المشكلة وتحديد أسبابها</li>
                    <li><strong>الحل والمعالجة:</strong> تنفيذ الحل المناسب وإبلاغ مقدم الشكوى بالنتيجة</li>
                    <li><strong>التأكيد والإغلاق:</strong> التأكد من رضا مقدم الشكوى عن الحل قبل إغلاق التذكرة</li>
                    <li><strong>المتابعة:</strong> التواصل بعد 48 ساعة من الإغلاق للتأكد من عدم تكرار المشكلة</li>
                </ul>
            </section>

            <!-- إجراءات التصعيد -->
            <section>
                <h2>7. إجراءات التصعيد</h2>
                <p>في حال عدم حل المشكلة ضمن الأطر الزمنية المحددة أو عدم الرضا عن الحل المقدم:</p>

                <h3>7.1 مستويات التصعيد</h3>
                <ul>
                    <li><strong>المستوى الأول:</strong> فريق الدعم الفني الأساسي - يتعامل مع المشكلات الشائعة والروتينية</li>
                    <li><strong>المستوى الثاني:</strong> المتخصصون الفنيون - يتعاملون مع المشكلات المعقدة التي لم تُحل في المستوى الأول (تصعيد تلقائي بعد 8 ساعات)</li>
                    <li><strong>المستوى الثالث:</strong> مدير قسم تقنية المعلومات - يتولى المشكلات الحرجة والمتكررة (تصعيد تلقائي بعد 24 ساعة)</li>
                    <li><strong>المستوى الرابع:</strong> الإدارة العليا - للحالات الاستثنائية التي تؤثر على سير العملية التعليمية بشكل كبير</li>
                </ul>

                <h3>7.2 كيفية طلب التصعيد</h3>
                <ul>
                    <li>يمكن طلب التصعيد عبر الرقم المرجعي للتذكرة من خلال أي قناة دعم</li>
                    <li>يتم تصعيد المشكلة تلقائياً عند تجاوز الأطر الزمنية المحددة لكل مستوى</li>
                    <li>يمكن التصعيد مباشرة عبر البريد الإلكتروني: <strong>escalation@ertiqaa.edu.sa</strong></li>
                </ul>
            </section>

            <!-- مؤشرات الأداء -->
            <section>
                <h2>8. مؤشرات الأداء والجودة</h2>
                <p>نلتزم بقياس وتحسين أداء الدعم الفني من خلال المؤشرات التالية:</p>
                <ul>
                    <li><strong>نسبة الحل من الاتصال الأول:</strong> الهدف 70% أو أعلى</li>
                    <li><strong>معدل رضا المستخدمين:</strong> الهدف 85% أو أعلى</li>
                    <li><strong>الالتزام بأوقات الاستجابة:</strong> الهدف 95% أو أعلى</li>
                    <li><strong>معدل إعادة فتح التذاكر:</strong> الهدف أقل من 5%</li>
                </ul>
                <p>يتم نشر تقرير أداء الدعم الفني بشكل شهري ومراجعته من قبل الإدارة لاتخاذ إجراءات التحسين المستمر.</p>
            </section>

        </div>
    </div>
</div>
@endsection
