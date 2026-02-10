@extends('layouts.front')

@section('title', 'سياسة التقييم')

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
        color: #dc2626;
        position: absolute;
        right: 0;
        top: 0.35rem;
        font-size: 0.6rem;
    }
    .policy-content .info-box {
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
    .policy-content .grade-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5rem;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .policy-content .grade-table th {
        background: #dc2626;
        color: white;
        padding: 0.75rem 1rem;
        text-align: right;
        font-weight: 600;
    }
    .policy-content .grade-table td {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        color: #4b5563;
    }
    .policy-content .grade-table tr:nth-child(even) {
        background: #f9fafb;
    }
    .policy-content .grade-table tr:hover {
        background: #fef2f2;
    }
    .policy-content .weight-bar {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }
    .policy-content .weight-bar .bar-label {
        min-width: 140px;
        font-weight: 600;
        color: #374151;
    }
    .policy-content .weight-bar .bar-track {
        flex: 1;
        height: 24px;
        background: #f3f4f6;
        border-radius: 12px;
        overflow: hidden;
    }
    .policy-content .weight-bar .bar-fill {
        height: 100%;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.8rem;
        font-weight: 700;
    }
</style>
@endsection

@section('content')
<div class="min-h-screen bg-gray-50 py-12" style="direction: rtl;">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Card -->
        <div class="rounded-t-2xl p-8 text-white" style="background: linear-gradient(135deg, #dc2626, #991b1b);">
            <h1 class="text-3xl font-bold">سياسة التقييم</h1>
            <p class="mt-2" style="color: #fca5a5;">منهجية شاملة وعادلة لتقييم مخرجات التعلم وقياس الأداء الأكاديمي</p>
            <div class="mt-3 flex items-center gap-2 text-sm" style="color: #fecaca;">
                <span>معيار NELC: 2.4.2</span>
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
                    يتبنى معهد الارتقاء العالي للتدريب نظام تقييم شامل وعادل يهدف إلى قياس مدى تحقيق المتدربين لمخرجات التعلم المستهدفة. يعتمد نظام التقييم على التنويع في أساليب وأدوات التقييم لضمان شموليته ودقته، بما يتوافق مع معايير المركز الوطني للتعليم الإلكتروني ومتطلبات ضمان الجودة.
                </p>
            </section>

            <!-- أنواع التقييم -->
            <section>
                <h2>2. أنواع التقييم</h2>

                <h3>2.1 التقييم التكويني (Formative Assessment)</h3>
                <p>تقييم مستمر يهدف إلى متابعة تقدم المتدرب وتقديم التغذية الراجعة أثناء العملية التعليمية:</p>
                <ul>
                    <li><strong>الاختبارات القصيرة:</strong> اختبارات دورية قصيرة بعد كل وحدة تعليمية لقياس الاستيعاب</li>
                    <li><strong>المناقشات التفاعلية:</strong> المشاركة في منتديات النقاش والفصول الافتراضية وإثراء الحوار الأكاديمي</li>
                    <li><strong>الواجبات والأنشطة:</strong> تمارين ومهام تطبيقية مرتبطة بمحتوى المقرر تقدم بشكل دوري</li>
                    <li><strong>التقييم الذاتي:</strong> أدوات تقييم ذاتي تمكن المتدرب من قياس فهمه ومراجعة أدائه</li>
                </ul>

                <h3>2.2 التقييم الختامي (Summative Assessment)</h3>
                <p>تقييم شامل يتم في نهاية المقرر أو البرنامج لقياس مستوى التحصيل الكلي:</p>
                <ul>
                    <li><strong>الاختبار النهائي:</strong> اختبار شامل يغطي جميع محتويات المقرر ومخرجات التعلم</li>
                    <li><strong>المشاريع التطبيقية:</strong> مشاريع فردية أو جماعية تعكس تطبيق المعارف والمهارات المكتسبة</li>
                    <li><strong>الاختبارات العملية:</strong> تقييم عملي للمهارات التطبيقية في المقررات التي تتطلب ذلك</li>
                    <li><strong>حقيبة الأعمال (Portfolio):</strong> مجموعة من أعمال المتدرب التي توثق تطوره ومستوى إنجازه</li>
                </ul>
            </section>

            <!-- توزيع الدرجات -->
            <section>
                <h2>3. توزيع الدرجات</h2>
                <p>يتم توزيع الدرجات الكلية للمقرر (100 درجة) وفقاً للنموذج التالي:</p>

                <div style="margin-bottom: 1.5rem;">
                    <div class="weight-bar">
                        <span class="bar-label">أعمال الفصل</span>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: 60%; background: #dc2626;">60%</div>
                        </div>
                    </div>
                    <div class="weight-bar">
                        <span class="bar-label">الاختبار النهائي</span>
                        <div class="bar-track">
                            <div class="bar-fill" style="width: 40%; background: #991b1b;">40%</div>
                        </div>
                    </div>
                </div>

                <h3>3.1 تفصيل أعمال الفصل (60 درجة)</h3>
                <table class="grade-table">
                    <thead>
                        <tr>
                            <th>عنصر التقييم</th>
                            <th>الوزن</th>
                            <th>الوصف</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>الواجبات والأنشطة</strong></td>
                            <td>15 درجة</td>
                            <td>واجبات دورية وأنشطة تطبيقية مرتبطة بالوحدات التعليمية</td>
                        </tr>
                        <tr>
                            <td><strong>الاختبارات القصيرة</strong></td>
                            <td>15 درجة</td>
                            <td>اختبارات قصيرة بعد كل وحدة لقياس الاستيعاب والفهم</td>
                        </tr>
                        <tr>
                            <td><strong>المشاركة والتفاعل</strong></td>
                            <td>10 درجات</td>
                            <td>المشاركة في المنتديات والفصول الافتراضية والأنشطة التفاعلية</td>
                        </tr>
                        <tr>
                            <td><strong>المشروع التطبيقي</strong></td>
                            <td>15 درجة</td>
                            <td>مشروع تطبيقي فردي أو جماعي يعكس المهارات المكتسبة</td>
                        </tr>
                        <tr>
                            <td><strong>الاختبار النصفي</strong></td>
                            <td>5 درجات</td>
                            <td>اختبار في منتصف البرنامج يغطي النصف الأول من المحتوى</td>
                        </tr>
                    </tbody>
                </table>

                <h3>3.2 الاختبار النهائي (40 درجة)</h3>
                <ul>
                    <li>اختبار شامل يغطي جميع مخرجات التعلم للمقرر</li>
                    <li>يُعقد إلكترونياً عبر المنصة في الموعد المحدد</li>
                    <li>مدة الاختبار تتناسب مع عدد الأسئلة وطبيعة المقرر</li>
                    <li>قد يشمل أسئلة متنوعة: اختيار من متعدد، صح وخطأ، مقالية، تطبيقية</li>
                </ul>
            </section>

            <!-- مقياس التقديرات -->
            <section>
                <h2>4. مقياس التقديرات</h2>
                <table class="grade-table">
                    <thead>
                        <tr>
                            <th>نطاق الدرجة</th>
                            <th>التقدير</th>
                            <th>الرمز</th>
                            <th>الوصف</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>95 - 100</td>
                            <td>ممتاز مرتفع</td>
                            <td>A+</td>
                            <td>أداء متميز يفوق التوقعات</td>
                        </tr>
                        <tr>
                            <td>90 - 94</td>
                            <td>ممتاز</td>
                            <td>A</td>
                            <td>أداء متميز في جميع المجالات</td>
                        </tr>
                        <tr>
                            <td>85 - 89</td>
                            <td>جيد جداً مرتفع</td>
                            <td>B+</td>
                            <td>أداء أعلى من المتوسط بشكل ملحوظ</td>
                        </tr>
                        <tr>
                            <td>80 - 84</td>
                            <td>جيد جداً</td>
                            <td>B</td>
                            <td>أداء جيد يتجاوز المتطلبات الأساسية</td>
                        </tr>
                        <tr>
                            <td>75 - 79</td>
                            <td>جيد مرتفع</td>
                            <td>C+</td>
                            <td>أداء مقبول مع بعض نقاط القوة</td>
                        </tr>
                        <tr>
                            <td>70 - 74</td>
                            <td>جيد</td>
                            <td>C</td>
                            <td>أداء يلبي المتطلبات الأساسية</td>
                        </tr>
                        <tr>
                            <td>65 - 69</td>
                            <td>مقبول مرتفع</td>
                            <td>D+</td>
                            <td>أداء مقبول بحد أدنى</td>
                        </tr>
                        <tr>
                            <td>60 - 64</td>
                            <td>مقبول</td>
                            <td>D</td>
                            <td>الحد الأدنى لاجتياز المقرر</td>
                        </tr>
                        <tr>
                            <td>أقل من 60</td>
                            <td>راسب</td>
                            <td>F</td>
                            <td>لم يحقق الحد الأدنى المطلوب</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <!-- ضوابط التقييم -->
            <section>
                <h2>5. ضوابط التقييم الإلكتروني</h2>
                <ul>
                    <li>يتم إجراء الاختبارات عبر المنصة التعليمية باستخدام نظام اختبارات آمن ومراقب</li>
                    <li>يلتزم المتدرب بالدخول للاختبار في الوقت المحدد مع هامش لا يتجاوز خمس (5) دقائق</li>
                    <li>قد تُستخدم أنظمة مراقبة إلكترونية (Proctoring) لضمان نزاهة الاختبار</li>
                    <li>يُحظر استخدام أي مراجع أو مصادر غير مصرح بها أثناء الاختبار إلا بإذن مسبق</li>
                    <li>يتم تبديل ترتيب الأسئلة والإجابات عشوائياً لكل متدرب لمنع الغش</li>
                    <li>تُعلن النتائج خلال عشرة (10) أيام عمل من تاريخ انتهاء الاختبار</li>
                </ul>
            </section>

            <!-- التغذية الراجعة -->
            <section>
                <h2>6. التغذية الراجعة</h2>
                <p>يحرص المعهد على تقديم تغذية راجعة فعالة ومستمرة:</p>
                <ul>
                    <li>تُقدم التغذية الراجعة التفصيلية على الواجبات والأنشطة خلال سبعة (7) أيام عمل</li>
                    <li>تتضمن التغذية الراجعة نقاط القوة والضعف واقتراحات التحسين</li>
                    <li>يحق للمتدرب الاطلاع على أدائه في الاختبارات ومراجعة إجاباته</li>
                    <li>يتاح للمتدرب التقييم الذاتي بعد كل وحدة لمتابعة تقدمه</li>
                </ul>
            </section>

            <!-- التظلم وإعادة التقييم -->
            <section>
                <h2>7. التظلم وإعادة التقييم</h2>
                <ul>
                    <li>يحق للمتدرب تقديم طلب تظلم على نتيجة أي تقييم خلال خمسة (5) أيام عمل من إعلان النتيجة</li>
                    <li>يُقدم التظلم كتابياً عبر المنصة مع بيان الأسباب والمبررات</li>
                    <li>يتم مراجعة التظلم من قبل لجنة مستقلة عن المدرب المعني خلال سبعة (7) أيام عمل</li>
                    <li>يُبلغ المتدرب بنتيجة التظلم كتابياً عبر البريد الإلكتروني</li>
                    <li>قرار لجنة التظلمات نهائي وملزم لجميع الأطراف</li>
                </ul>
            </section>

            <!-- حالات خاصة -->
            <section>
                <h2>8. الحالات الخاصة</h2>
                <div class="highlight-box">
                    <p><strong>ذوو الاحتياجات الخاصة:</strong> يوفر المعهد تسهيلات خاصة للمتدربين من ذوي الإعاقة تشمل:</p>
                    <ul style="margin-bottom: 0;">
                        <li>زيادة مدة الاختبار بنسبة تصل إلى 50% حسب نوع الإعاقة</li>
                        <li>توفير اختبارات بتنسيقات بديلة (صوتية، بصرية مكبرة)</li>
                        <li>السماح باستخدام تقنيات مساعدة أثناء الاختبار</li>
                        <li>تعديل أساليب التقييم بما يتناسب مع القدرات الفردية</li>
                    </ul>
                </div>
            </section>

        </div>
    </div>
</div>
@endsection
