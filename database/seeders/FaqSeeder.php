<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        Faq::truncate();

        $faqs = [

            // ── التسجيل والدفع ──────────────────────────────────────────
            [
                'category'    => 'registration',
                'sort_order'  => 1,
                'question_ar' => 'كيف أتسجل في المعهد؟',
                'question_en' => 'How do I register at the institute?',
                'answer_ar'   => 'يمكنك التسجيل بسهولة عبر حساب نفاذ أو إنشاء حساب داخلي. بعد التسجيل، يمكنك اختيار الدبلومة الأكاديمي أو الدورات القصيرة التي تناسبك. ستتلقى رسالة تأكيد على بريدك الإلكتروني بعد إتمام التسجيل.',
                'answer_en'   => 'You can register easily via your Nafath account or by creating an internal account. After registering, you can choose the academic diploma or short courses that suit you. You will receive a confirmation email once registration is complete.',
            ],
            [
                'category'    => 'registration',
                'sort_order'  => 2,
                'question_ar' => 'ما طرق الدفع المتاحة؟',
                'question_en' => 'What payment methods are available?',
                'answer_ar'   => 'نوفر عدة طرق للدفع تشمل: بطاقات الائتمان، مدى، التحويل البنكي، والدفع عند التسجيل. كما نوفر خيارات التقسيط للدبلومات الطويلة.',
                'answer_en'   => 'We provide several payment methods including credit cards, Mada, bank transfer, and payment upon registration. Installment options are also available for long diploma programs.',
            ],
            [
                'category'    => 'registration',
                'sort_order'  => 3,
                'question_ar' => 'هل يمكنني الاسترداد إذا انسحبت من البرنامج؟',
                'question_en' => 'Can I get a refund if I withdraw from the program?',
                'answer_ar'   => 'نعم، يمكن طلب استرداد الرسوم وفقاً لسياسة الاسترداد المعتمدة. الاسترداد الكامل متاح خلال 7 أيام من تاريخ التسجيل، والاسترداد الجزئي (50%) متاح خلال 14 يوماً، وبعد ذلك لا يُقبل طلب الاسترداد.',
                'answer_en'   => 'Yes, you may request a fee refund according to the approved refund policy. A full refund is available within 7 days of registration, a partial refund (50%) within 14 days, and no refund is accepted after that.',
            ],
            [
                'category'    => 'registration',
                'sort_order'  => 4,
                'question_ar' => 'ما الوثائق المطلوبة للتسجيل؟',
                'question_en' => 'What documents are required for registration?',
                'answer_ar'   => 'تحتاج إلى: صورة من الهوية الوطنية أو الإقامة، صورة شخصية حديثة، شهادة المؤهل الأكاديمي الأخير، وأي شهادات مهنية ذات صلة إن وُجدت.',
                'answer_en'   => 'You need: a copy of your national ID or residency permit, a recent personal photo, your latest academic qualification certificate, and any relevant professional certificates if available.',
            ],
            [
                'category'    => 'registration',
                'sort_order'  => 5,
                'question_ar' => 'كيف أعرف أن طلبي قُبل؟',
                'question_en' => 'How do I know my application was accepted?',
                'answer_ar'   => 'ستصلك رسالة بريد إلكتروني تأكيدية فور قبول طلبك، إضافةً إلى إشعار داخل المنصة. يمكنك أيضاً متابعة حالة طلبك من لوحة التحكم الخاصة بك.',
                'answer_en'   => 'You will receive a confirmation email as soon as your application is accepted, along with an in-platform notification. You can also track your application status from your dashboard.',
            ],

            // ── الدورات والدبلومات ──────────────────────────────────────
            [
                'category'    => 'courses',
                'sort_order'  => 1,
                'question_ar' => 'ما هو نظام الفصل الدراسي؟',
                'question_en' => 'What is the term system?',
                'answer_ar'   => 'نظام الفصل الدراسي هو نظام تقسيم الدبلومة التدريبي إلى فترات زمنية محددة (ترم)، مدة كل منها فترة معينة، مما يساعد في تنظيم العملية التدريبية وتقييم تقدم المتدرب بشكل دوري.',
                'answer_en'   => 'The term system divides the training diploma into specific time periods (terms), each of a certain duration, helping to organize the training process and periodically assess trainee progress.',
            ],
            [
                'category'    => 'courses',
                'sort_order'  => 2,
                'question_ar' => 'هل يمكنني التحويل من دبلومة لآخر؟',
                'question_en' => 'Can I transfer from one diploma to another?',
                'answer_ar'   => 'نعم، يمكنك التحويل بين الدبلومات وفق شروط أكاديمية محددة وبعد مراجعة الساعات التدريبية المنجزة والتنسيق مع الإدارة الأكاديمية.',
                'answer_en'   => 'Yes, you can transfer between diplomas according to specific academic conditions and after reviewing completed training hours and coordinating with the academic administration.',
            ],
            [
                'category'    => 'courses',
                'sort_order'  => 3,
                'question_ar' => 'هل يوجد تدريب عن بعد؟',
                'question_en' => 'Is remote training available?',
                'answer_ar'   => 'نعم، نوفر خيارات التدريب عن بعد لمعظم دوراتنا ودبلوماتنا التدريبية، مع توفر جميع المواد التعليمية والموارد إلكترونياً.',
                'answer_en'   => 'Yes, we provide remote training options for most of our courses and diploma programs, with all educational materials and resources available electronically.',
            ],
            [
                'category'    => 'courses',
                'sort_order'  => 4,
                'question_ar' => 'ما مدة الدورة القصيرة؟',
                'question_en' => 'How long is a short course?',
                'answer_ar'   => 'تتراوح الدورات القصيرة بين أسبوعين إلى 8 أسابيع تبعاً لطبيعة الدورة ومحتواها.',
                'answer_en'   => 'Short courses range from two weeks to 8 weeks depending on the nature and content of the course.',
            ],
            [
                'category'    => 'courses',
                'sort_order'  => 5,
                'question_ar' => 'كم عدد المواد في كل برنامج؟',
                'question_en' => 'How many subjects are in each program?',
                'answer_ar'   => 'يتفاوت عدد المواد حسب البرنامج. يتكون كل فصل دراسي عادةً من 4 إلى 8 مواد دراسية، ويمكنك الاطلاع على الخطة الدراسية التفصيلية لكل برنامج من صفحة البرامج.',
                'answer_en'   => 'The number of subjects varies by program. Each term typically consists of 4 to 8 subjects. You can view the detailed study plan for each program on the programs page.',
            ],
            [
                'category'    => 'courses',
                'sort_order'  => 6,
                'question_ar' => 'هل يمكنني الدراسة في أكثر من دبلومة في نفس الوقت؟',
                'question_en' => 'Can I study more than one diploma at the same time?',
                'answer_ar'   => 'يسمح بالتسجيل في دبلومة واحد فقط في نفس الوقت للطلاب النظاميين، غير أنه يمكن الالتحاق بدورات قصيرة إضافية بجانب الدبلومة الرئيسي وفق ضوابط محددة.',
                'answer_en'   => 'Regular students may only enroll in one diploma at a time; however, additional short courses may be taken alongside the main diploma subject to specific conditions.',
            ],

            // ── الشهادات والاعتماد ──────────────────────────────────────
            [
                'category'    => 'certificates',
                'sort_order'  => 1,
                'question_ar' => 'هل الشهادات معتمدة؟',
                'question_en' => 'Are the certificates accredited?',
                'answer_ar'   => 'نعم، جميع شهاداتنا معتمدة من المؤسسة العامة للتدريب التقني والمهني ومعترف بها في سوق العمل السعودي.',
                'answer_en'   => 'Yes, all our certificates are accredited by the Technical and Vocational Training Corporation (TVTC) and recognized in the Saudi labor market.',
            ],
            [
                'category'    => 'certificates',
                'sort_order'  => 2,
                'question_ar' => 'متى أحصل على شهادتي؟',
                'question_en' => 'When will I receive my certificate?',
                'answer_ar'   => 'تُصدر الشهادة خلال أسبوع إلى أسبوعين من إتمام متطلبات البرنامج بنجاح واجتياز جميع التقييمات.',
                'answer_en'   => 'The certificate is issued within one to two weeks of successfully completing all program requirements and passing all assessments.',
            ],
            [
                'category'    => 'certificates',
                'sort_order'  => 3,
                'question_ar' => 'هل يمكنني الحصول على شهادة رقمية؟',
                'question_en' => 'Can I get a digital certificate?',
                'answer_ar'   => 'نعم، تُوفَّر الشهادة الرقمية (Digital Badge) إضافةً إلى النسخة الورقية. يمكن مشاركتها على LinkedIn أو أي منصة مهنية.',
                'answer_en'   => 'Yes, a digital badge is provided in addition to the printed certificate. It can be shared on LinkedIn or any professional platform.',
            ],
            [
                'category'    => 'certificates',
                'sort_order'  => 4,
                'question_ar' => 'ما الحد الأدنى لاجتياز البرنامج؟',
                'question_en' => 'What is the minimum passing score?',
                'answer_ar'   => 'الحد الأدنى للاجتياز هو 60% في التقييمات الإجمالية مع حضور لا يقل عن 75% من إجمالي ساعات البرنامج.',
                'answer_en'   => 'The minimum passing score is 60% in overall assessments with an attendance rate of no less than 75% of total program hours.',
            ],
            [
                'category'    => 'certificates',
                'sort_order'  => 5,
                'question_ar' => 'هل الشهادة معترف بها دولياً؟',
                'question_en' => 'Is the certificate internationally recognized?',
                'answer_ar'   => 'تحمل بعض برامجنا اعتمادات دولية من جهات متخصصة. يُذكر نطاق الاعتماد بوضوح في وصف كل برنامج.',
                'answer_en'   => 'Some of our programs carry international accreditation from specialized bodies. The accreditation scope is clearly mentioned in each program description.',
            ],

            // ── منصة الطالب ─────────────────────────────────────────────
            [
                'category'    => 'platform',
                'sort_order'  => 1,
                'question_ar' => 'كيف أصل إلى المواد الدراسية؟',
                'question_en' => 'How do I access study materials?',
                'answer_ar'   => 'بعد التسجيل، يمكنك الوصول إلى جميع المواد الدراسية من قائمة "ملفاتي" في القائمة الجانبية. تشمل المواد: ملفات PDF، فيديوهات المحاضرات، وروابط جلسات Zoom.',
                'answer_en'   => 'After registration, you can access all study materials from the "My Files" section in the sidebar. Materials include PDF files, lecture videos, and Zoom session links.',
            ],
            [
                'category'    => 'platform',
                'sort_order'  => 2,
                'question_ar' => 'كيف أتابع حضوري؟',
                'question_en' => 'How do I track my attendance?',
                'answer_ar'   => 'يمكنك مراجعة سجل حضورك الكامل من قسم "سجل الحضور" في لوحة التحكم. يُظهر السجل تاريخ كل جلسة وحالة الحضور.',
                'answer_en'   => 'You can review your full attendance record from the "Attendance Record" section in your dashboard. The record shows the date of each session and attendance status.',
            ],
            [
                'category'    => 'platform',
                'sort_order'  => 3,
                'question_ar' => 'كيف أطلع على درجاتي ونتائجي؟',
                'question_en' => 'How do I view my grades and results?',
                'answer_ar'   => 'تظهر درجات التقييمات والاختبارات في قسم "نتائجي" بلوحة تحكم الطالب فور صدورها من قِبل المعلم.',
                'answer_en'   => 'Assessment and exam grades appear in the "My Results" section of the student dashboard as soon as they are released by the teacher.',
            ],
            [
                'category'    => 'platform',
                'sort_order'  => 4,
                'question_ar' => 'كيف أستخدم نظام التذاكر للدعم؟',
                'question_en' => 'How do I use the support ticket system?',
                'answer_ar'   => 'من قائمة "تذاكر الدعم" في لوحة تحكمك، انقر على "إنشاء تذكرة جديدة"، اختر الفئة والأولوية، واكتب وصفاً لمشكلتك. سيرد فريق الدعم خلال 24 ساعة.',
                'answer_en'   => 'From the "Support Tickets" menu in your dashboard, click "Create New Ticket", select the category and priority, and write a description of your issue. The support team will respond within 24 hours.',
            ],
            [
                'category'    => 'platform',
                'sort_order'  => 5,
                'question_ar' => 'كيف أحدّث بياناتي الشخصية؟',
                'question_en' => 'How do I update my personal information?',
                'answer_ar'   => 'انتقل إلى "الملف الشخصي" من القائمة العلوية، ثم انقر على "تعديل الملف الشخصي". يمكنك تحديث الاسم ورقم الجوال والصورة الشخصية.',
                'answer_en'   => 'Go to "Profile" from the top menu, then click "Edit Profile". You can update your name, mobile number, and profile picture.',
            ],
            [
                'category'    => 'platform',
                'sort_order'  => 6,
                'question_ar' => 'كيف يعمل نظام المدفوعات في المنصة؟',
                'question_en' => 'How does the payment system work on the platform?',
                'answer_ar'   => 'عند التسجيل في برنامج مدفوع، يتم توجيهك تلقائياً لإتمام الدفع عبر بوابة الدفع. يمكنك مراجعة جميع فواتيرك وتاريخ مدفوعاتك من قسم "المدفوعات" في لوحة تحكمك.',
                'answer_en'   => 'When enrolling in a paid program, you are automatically directed to complete payment via the payment gateway. You can review all your invoices and payment history from the "Payments" section in your dashboard.',
            ],

            // ── الدعم التقني ─────────────────────────────────────────────
            [
                'category'    => 'support',
                'sort_order'  => 1,
                'question_ar' => 'كيف أحصل على الدعم التقني؟',
                'question_en' => 'How do I get technical support?',
                'answer_ar'   => 'فريق الدعم التقني متاح على مدار الساعة للرد على استفساراتك وحل أي مشاكل تقنية. يمكنك التواصل عبر الهاتف أو البريد الإلكتروني أو نظام التذاكر.',
                'answer_en'   => 'The technical support team is available around the clock to respond to your inquiries and resolve any technical issues. You can contact us by phone, email, or the ticket system.',
            ],
            [
                'category'    => 'support',
                'sort_order'  => 2,
                'question_ar' => 'ماذا أفعل إذا لم أتمكن من الوصول لحسابي؟',
                'question_en' => 'What should I do if I cannot access my account?',
                'answer_ar'   => 'انقر على "نسيت كلمة المرور" في صفحة تسجيل الدخول وأدخل بريدك الإلكتروني. ستصلك رابط إعادة التعيين خلال دقائق. إذا استمرت المشكلة، تواصل مع الدعم التقني.',
                'answer_en'   => 'Click "Forgot Password" on the login page and enter your email. You will receive a reset link within minutes. If the issue persists, contact technical support.',
            ],
            [
                'category'    => 'support',
                'sort_order'  => 3,
                'question_ar' => 'ما المتصفحات المدعومة؟',
                'question_en' => 'Which browsers are supported?',
                'answer_ar'   => 'المنصة تعمل على أحدث إصدارات: Chrome، Firefox، Edge، وSafari. يُنصح باستخدام Chrome أو Edge للحصول على أفضل أداء.',
                'answer_en'   => 'The platform works on the latest versions of Chrome, Firefox, Edge, and Safari. Chrome or Edge is recommended for the best performance.',
            ],
            [
                'category'    => 'support',
                'sort_order'  => 4,
                'question_ar' => 'ماذا أفعل إذا كانت الفيديوهات لا تعمل؟',
                'question_en' => 'What should I do if videos are not working?',
                'answer_ar'   => 'تأكد من جودة الاتصال بالإنترنت، جرّب تحديث الصفحة، وتأكد من عدم تفعيل مانع الإعلانات. إذا استمرت المشكلة، يمكنك رفع تذكرة دعم تقني مع وصف المشكلة.',
                'answer_en'   => 'Check your internet connection quality, try refreshing the page, and make sure your ad blocker is not active. If the issue persists, you can submit a technical support ticket describing the problem.',
            ],
            [
                'category'    => 'support',
                'sort_order'  => 5,
                'question_ar' => 'كيف أبلّغ عن مشكلة في المنصة؟',
                'question_en' => 'How do I report a platform issue?',
                'answer_ar'   => 'استخدم نظام التذاكر من لوحة تحكمك واختر الفئة "دعم تقني" وأولوية "عالية" لضمان أسرع استجابة. يمكنك أيضاً التواصل عبر واتساب للمشاكل العاجلة.',
                'answer_en'   => 'Use the ticket system from your dashboard and select the "Technical Support" category with "High" priority to ensure the fastest response. You can also contact us via WhatsApp for urgent issues.',
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create(array_merge($faq, ['status' => 'active']));
        }

        $this->command->info('✅  FAQ seeder: ' . count($faqs) . ' questions seeded across 5 categories.');
    }
}
