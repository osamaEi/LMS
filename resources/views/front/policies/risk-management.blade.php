@extends('layouts.front')

@section('title', 'خطة إدارة المخاطر واستمرارية الأعمال')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header Card -->
        <div class="bg-gradient-to-r from-red-600 to-rose-700 rounded-t-2xl p-8 text-white">
            <h1 class="text-3xl font-bold">خطة إدارة المخاطر واستمرارية الأعمال</h1>
            <p class="mt-2 text-red-100">ضمان استقرار واستمرارية العملية التعليمية</p>
            <div class="mt-3 flex items-center gap-2 text-sm text-red-200">
                <span>معيار NELC: 1.1.2</span>
                <span>|</span>
                <span>آخر تحديث: {{ date('Y-m-d') }}</span>
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-b-2xl shadow-xl p-8 space-y-8">

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-red-500">مقدمة</h2>
                <p class="text-gray-700 leading-relaxed">
                    تلتزم منصتنا التعليمية بتوفير خطة شاملة لإدارة المخاطر المالية والبشرية والتقنية، وضمان استمرارية العملية التعليمية في جميع الظروف. تتضمن هذه الخطة تقييم الوضع الراهن، وتطوير إطار العمل، وتحديد فئات المخاطر المحتملة، والأدوار والمسؤوليات اللازمة لضمان استقرار التعليم واستمراريته.
                </p>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-red-500">المرحلة الأولى: تحديد المخاطر</h2>
                <p class="text-gray-700 mb-4">تم تحديد المخاطر المحتملة التي قد تؤثر على العملية التعليمية في الفئات التالية:</p>

                <div class="space-y-4">
                    <div class="bg-red-50 rounded-lg p-5 border-r-4 border-red-500">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">1. المخاطر التقنية</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-700 mr-4">
                            <li>انقطاع الخوادم (Server Downtime)</li>
                            <li>هجمات إلكترونية وبرمجيات خبيثة</li>
                            <li>فقدان البيانات أو تلفها</li>
                            <li>مشاكل في خدمة الإنترنت</li>
                            <li>أعطال في منصة Zoom أو الفصول الافتراضية</li>
                            <li>فشل في نظام النسخ الاحتياطي</li>
                        </ul>
                    </div>

                    <div class="bg-red-50 rounded-lg p-5 border-r-4 border-red-500">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">2. المخاطر المالية</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-700 mr-4">
                            <li>عدم كفاية الموارد المالية للتشغيل</li>
                            <li>ارتفاع تكاليف الخدمات التقنية</li>
                            <li>عدم تحصيل الرسوم الدراسية</li>
                            <li>انخفاض أعداد الطلاب المسجلين</li>
                        </ul>
                    </div>

                    <div class="bg-red-50 rounded-lg p-5 border-r-4 border-red-500">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">3. المخاطر البشرية</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-700 mr-4">
                            <li>نقص الكوادر المؤهلة (معلمين، دعم فني)</li>
                            <li>استقالة أو غياب أعضاء هيئة التدريس الرئيسيين</li>
                            <li>أخطاء بشرية في إدارة النظام أو المحتوى</li>
                            <li>ضعف تدريب الكوادر على الأدوات الرقمية</li>
                        </ul>
                    </div>

                    <div class="bg-red-50 rounded-lg p-5 border-r-4 border-red-500">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">4. المخاطر التنظيمية</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-700 mr-4">
                            <li>تغييرات في الأنظمة واللوائح الحكومية</li>
                            <li>عدم الامتثال لمعايير NELC</li>
                            <li>مشاكل في الترخيص أو الاعتماد الأكاديمي</li>
                        </ul>
                    </div>

                    <div class="bg-red-50 rounded-lg p-5 border-r-4 border-red-500">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">5. المخاطر الخارجية</h3>
                        <ul class="list-disc list-inside space-y-1 text-gray-700 mr-4">
                            <li>الأوبئة والكوارث الطبيعية</li>
                            <li>انقطاع الكهرباء لفترات طويلة</li>
                            <li>أزمات اقتصادية أو سياسية</li>
                        </ul>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-red-500">المرحلة الثانية: تحليل المخاطر</h2>

                <h3 class="text-xl font-bold text-gray-800 mb-3 mt-6">أ. تقييم المخاطر</h3>
                <p class="text-gray-700 mb-4">يتم تقييم كل خطر بناءً على معيارين:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-bold text-gray-800 mb-2">1. احتمالية الحدوث</h4>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• <strong>منخفضة:</strong> 1-2</li>
                            <li>• <strong>متوسطة:</strong> 3-4</li>
                            <li>• <strong>عالية:</strong> 5</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-bold text-gray-800 mb-2">2. التأثير على العملية التعليمية</h4>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li>• <strong>منخفض:</strong> 1-2 (تأثير بسيط)</li>
                            <li>• <strong>متوسط:</strong> 3-4 (تأخير مؤقت)</li>
                            <li>• <strong>عالي:</strong> 5 (توقف كامل)</li>
                        </ul>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-3 mt-6">ب. ترتيب أولويات المخاطر</h3>
                <p class="text-gray-700 mb-4">تُرتب المخاطر حسب الأولوية باستخدام المعادلة:</p>
                <div class="bg-rose-100 rounded-lg p-4 text-center mb-4">
                    <p class="text-lg font-bold text-gray-800">مستوى الخطر = (الاحتمالية × التأثير)</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <ul class="text-gray-700 space-y-1">
                        <li>• <span class="font-bold text-red-600">حرجة (20-25):</span> تتطلب تدخلاً فورياً</li>
                        <li>• <span class="font-bold text-orange-600">عالية (15-19):</span> تتطلب خطة عاجلة</li>
                        <li>• <span class="font-bold text-yellow-600">متوسطة (10-14):</span> تُراقب بانتظام</li>
                        <li>• <span class="font-bold text-green-600">منخفضة (1-9):</span> يمكن قبولها</li>
                    </ul>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-red-500">المرحلة الثالثة: الاستجابة للمخاطر</h2>

                <div class="space-y-4">
                    <div class="bg-white border border-red-200 rounded-lg p-5">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-700 font-bold">1</span>
                            التجنب (Avoid)
                        </h3>
                        <p class="text-gray-700">
                            إلغاء النشاط أو تغيير الخطة لتفادي الخطر تماماً (مثل: عدم الاعتماد على مزود خدمة واحد).
                        </p>
                    </div>

                    <div class="bg-white border border-red-200 rounded-lg p-5">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-700 font-bold">2</span>
                            التخفيف (Mitigate)
                        </h3>
                        <p class="text-gray-700 mb-2">
                            اتخاذ إجراءات للحد من احتمالية حدوث الخطر أو تأثيره:
                        </p>
                        <ul class="list-disc list-inside text-gray-700 mr-4 space-y-1">
                            <li>نسخ احتياطي يومي للبيانات على خوادم متعددة</li>
                            <li>أنظمة حماية إلكترونية متطورة (Firewall, Anti-DDoS)</li>
                            <li>عقود صيانة مع موردين محليين ودوليين</li>
                            <li>تدريب مستمر للكوادر التقنية والأكاديمية</li>
                            <li>خطة مالية احتياطية تغطي 6 أشهر من التشغيل</li>
                        </ul>
                    </div>

                    <div class="bg-white border border-red-200 rounded-lg p-5">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-700 font-bold">3</span>
                            النقل (Transfer)
                        </h3>
                        <p class="text-gray-700">
                            نقل المسؤولية إلى طرف ثالث (مثل: التأمين على البيانات، عقود مع مزودي خدمات سحابية بضمانات SLA).
                        </p>
                    </div>

                    <div class="bg-white border border-red-200 rounded-lg p-5">
                        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
                            <span class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center text-red-700 font-bold">4</span>
                            القبول (Accept)
                        </h3>
                        <p class="text-gray-700">
                            قبول المخاطر المنخفضة مع المراقبة المستمرة ووضع خطة طوارئ للتعامل معها عند حدوثها.
                        </p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-red-500">خطة استمرارية الأعمال (BCP)</h2>

                <div class="bg-rose-50 border-r-4 border-rose-500 p-5 rounded-lg mb-4">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">السيناريو: انقطاع كامل للخوادم الرئيسية</h3>
                    <p class="text-gray-700 mb-3"><strong>الهدف:</strong> استعادة الخدمة خلال 4 ساعات كحد أقصى</p>
                    <div class="space-y-2 text-gray-700">
                        <p><strong>الساعة 0-1:</strong> تفعيل الخوادم الاحتياطية تلقائياً (Failover)</p>
                        <p><strong>الساعة 1-2:</strong> إشعار جميع المستخدمين وتفعيل وضع الطوارئ</p>
                        <p><strong>الساعة 2-4:</strong> استعادة البيانات من النسخ الاحتياطية</p>
                        <p><strong>بعد 4 ساعات:</strong> عودة الخدمة بالكامل + مراجعة شاملة</p>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mb-3">الموارد البديلة:</h3>
                <ul class="list-disc list-inside space-y-2 text-gray-700 mr-4">
                    <li>خوادم احتياطية في مركز بيانات منفصل</li>
                    <li>فريق دعم فني متاح 24/7</li>
                    <li>منصة Zoom احتياطية (حساب مؤسسي + حساب بديل)</li>
                    <li>معلمون احتياطيون لكل مقرر رئيسي</li>
                    <li>قنوات تواصل بديلة (WhatsApp, Email, SMS)</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-red-500">الأدوار والمسؤوليات</h2>

                <div class="space-y-3">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-bold text-gray-800">مدير إدارة المخاطر</h4>
                        <p class="text-sm text-gray-700">الإشراف العام على تطبيق الخطة ومراجعتها ربع سنوياً</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-bold text-gray-800">فريق الدعم الفني</h4>
                        <p class="text-sm text-gray-700">المسؤول عن التعامل الفوري مع الأعطال التقنية</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-bold text-gray-800">المدير الأكاديمي</h4>
                        <p class="text-sm text-gray-700">ضمان استمرارية التدريس وتوفير معلمين بديلين</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-bold text-gray-800">المدير المالي</h4>
                        <p class="text-sm text-gray-700">إدارة المخاطر المالية وتوفير السيولة اللازمة</p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-2xl font-bold text-gray-800 mb-4 pb-2 border-b-2 border-red-500">المراجعة والتحديث</h2>
                <p class="text-gray-700 leading-relaxed">
                    تُراجع خطة إدارة المخاطر بشكل دوري كل 3 أشهر، وتُحدّث فوراً عند:
                </p>
                <ul class="list-disc list-inside space-y-2 text-gray-700 mr-4 mt-3">
                    <li>حدوث خطر جديد لم يكن متوقعاً</li>
                    <li>فشل إجراء من إجراءات التخفيف</li>
                    <li>تغييرات جوهرية في البنية التقنية أو التنظيمية</li>
                    <li>صدور معايير أو لوائح جديدة من NELC</li>
                </ul>
            </section>

        </div>
    </div>
</div>
@endsection
