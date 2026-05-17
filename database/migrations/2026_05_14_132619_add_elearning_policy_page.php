<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $contentAr = '
<h2>سياسات وضوابط التدريب الإلكتروني</h2>

<h3>أولاً: الالتزام بتحقيق النزاهة في بيئة التدريب الإلكتروني</h3>
<p>تحقق الجهة النزاهة في بيئة التدريب الإلكتروني من خلال توفر سياسة تتضمن مفهوم النزاهة، وأشكال انتهاكها، وإجراءات مخالفتها. وتُحدد الأساليب المتبعة لمنع انتحال الهوية في بيئة التدريب الإلكتروني مع توفر آلية لفحص أعمال المتدربين ومنع الغش بهدف الحفاظ على سلوكيات النزاهة لدى جميع أطراف العملية التدريبي ة.</p>
<p>تشمل سياسة النزاهة ما يأتي:</p>
<ul>
    <li>مفهوم النزاهة.</li>
    <li>أشكال انتهاك النزاهة: الغش، انتحال الشخصية، استغلال التعاون، التزوير، وغيرها.</li>
    <li>الإجراءات والعقوبات التي تتخذها الجهة عند انتهاك النزاهة في بيئة التعليم أو التدريب الإلكتروني.</li>
    <li>الآلية المتبعة لمنع انتحال الهوية وفحص أعمال المتدربين ومنع الغش للبرامج التي تزيد مدتها عن شهر.</li>
</ul>

<h3>ثانياً: الالتزام بضمان خصوصية بيانات المستفيدين وحمايتها</h3>
<p>توفر الجهة سياسات وإجراءات واضحة لجمع البيانات وتخزينها ومعالجتها والتخلص منها وطريقة إتلافها، وضمان خصوصيتها وحمايتها والوصول إليها، وعدم الاطلاع عليها أو مشاركتها إلا من قبل المختصين وذوي الصلاحية. كما تلتزم الجهة بأي سياسات أخرى تصدر من الجهات التنظيمية ذات العلاقة في مجال إدارة البيانات وحوكمتها.</p>
<p>تشمل سياسة الخصوصية كحد أدنى:</p>
<ul>
    <li>نوعية البيانات التي تجمعها الجهة.</li>
    <li>طريقة جمع البيانات والغرض منها.</li>
    <li>مدة تخزينها واحتفاظ الجهة بها.</li>
    <li>من يملك صلاحية الاطلاع على هذه البيانات ومشاركتها.</li>
    <li>التزام الجهة بنظام حماية البيانات الشخصية.</li>
</ul>

<h3>ثالثاً: الهيكل التنظيمي وأدوار ومسؤوليات القائمين على التدريب الإلكتروني</h3>
<p>تحدد الجهة أدوار ومسؤوليات الكادر الإشرافي والتعليمي/التدريبي  والفني والإداري والتقني المساند لعملية التعليم/التدريب الإلكتروني مع التزام الجهة بتبليغها لهم للقيام بأدوارهم.</p>
<p>يشمل الهيكل التنظيمي:</p>
<ul>
    <li>البيانات الشخصية (الاسم والمسمى الوظيفي).</li>
    <li>الأدوار والمسؤوليات.</li>
    <li>عدد أفراد الفريق.</li>
</ul>

<h3>رابعاً: سياسة الحضور الإلكتروني</h3>
<p>وجود سياسة للحضور تضمن احتساب الحضور الإلكتروني معادلاً للحضور الاعتيادي، مع توضيح الحد الأدنى لساعات حضور البرنامج والإجراءات المتخذة في حق المتدرب في حال عدم الالتزام بذلك. كما تلتزم الجهة بمتابعة حضور المتدربين في التدريب المتزامن وغير المتزامن.</p>
<p>تشمل سياسة الحضور:</p>
<ul>
    <li>الإشارة إلى أن الحضور الإلكتروني يعادل الحضور الاعتيادي.</li>
    <li>الحد الأدنى لساعات حضور البرنامج.</li>
    <li>الإجراءات المتخذة في حق المتعلم أو المتدرب في حال عدم الالتزام بالحد الأدنى لساعات حضور البرنامج.</li>
    <li>نسبة ساعات الفصول الافتراضية المتزامنة (25% كحد أدنى للبرامج التي تزيد مدتها عن شهر).</li>
</ul>

<h3>خامساً: سياسة التواصل في بيئة التدريب الإلكتروني</h3>
<p>توضح الجهة سياسة التواصل بين المستفيدين في بيئة التعليم/التدريب الإلكتروني بما فيها التواصل بين المدرب والمتدرب وبين المتدربين وبعضهم، وتتضمن أدوات التواصل المتاحة مع توضيح آداب التواصل.</p>
<p>تشمل سياسة التواصل:</p>
<ul>
    <li>أدوات وقنوات التواصل: المناقشات، البريد الإلكتروني، المنتديات.</li>
    <li>آداب التواصل: الاحترام، وعدم الإساءة، وعدم الخوض في مناقشات سياسية أو دينية.</li>
    <li>الإجراء المتخذ من قِبل الجهة في حال مخالفة آداب التواصل.</li>
</ul>

<h3>سادساً: نظام التحقق من هوية المستفيد</h3>
<p>توفر الجهة نظاماً أو أداة للتحقق من هوية المستفيد عند كل مرة يتم فيها الدخول على نظام إدارة التعلم/المنصة التدريبي ة، مثل: ربط الدخول بالهوية الوطنية، أو التحقق الثنائي.</p>

<h3>سابعاً: تعليمات استخدام بيئة التدريب الإلكتروني</h3>
<p>توفر الجهة أدلة إرشادية إلكترونية مصورة وأدلة تفاعلية ومقاطع مرئية للمدرب والمتدرب لشرح نظام إدارة التعلم/المنصة التدريبي ة في كافة مراحل التدريب.</p>
<p>تشمل التعليمات المتاحة للمتدرب:</p>
<ul>
    <li>كيفية تسجيل الدخول واختيار الدورات التدريبي ة والبدء بعملية التدريب.</li>
    <li>طريقة التعامل مع الفصول الافتراضية والتفاعل مع المحتوى.</li>
    <li>كيفية إنجاز المهام ومتابعة التقدم في البرنامج.</li>
    <li>طريقة إتمام الاختبارات ورفع الواجبات.</li>
</ul>

<h3>ثامناً: الدعم الفني للمستفيدين</h3>
<p>توفر الجهة تعليمات واضحة عن الدعم الفني للمستفيدين بمختلف فئاتهم، وتضمن تقديم الدعم الفني عند وجود استفسارات أو شكاوى أو حدوث مشكلات تقنية تعيق استمرار العملية التدريبي ة.</p>
<p>تشمل صفحة الدعم الفني:</p>
<ul>
    <li>قنوات الدعم الفني (قناتين للدعم كحد أدنى).</li>
    <li>الخدمات المشمولة بالدعم الفني.</li>
    <li>طريقة استقبال الشكاوى والمقترحات وكيفية التعامل معها.</li>
    <li>أوقات عمل فريق الدعم الفني والوقت المتوقع للرد.</li>
    <li>إجراءات التصعيد في حال عدم الرد في المدة المحددة.</li>
</ul>

<h3>تاسعاً: المحتوى الرقمي للبرامج التدريبي ة</h3>
<p>توفر الجهة المحتوى الرقمي بشكل كامل على نظام إدارة التعلم/المنصة التدريبي ة، على أن يكون المحتوى متوافقاً مع الأهداف والموضوعات المعتمدة. وتتاح خاصية الوصول للفصل الافتراضي من خلال صفحة المقرر للموضوعات المقدمة بالأسلوب المتزامن.</p>

<h3>عاشراً: آلية الإجابة على استفسارات المتدربين</h3>
<p>توفير آلية للإجابة على استفسارات المتدرب من قِبل المدرب خلال الفترة الزمنية للبرنامج/المقرر، تتضمن وسائل للإجابة على الاستفسارات مع توضيح إجراءات التصعيد في حال عدم الإجابة في المدة المحددة.</p>
<p>تشمل الآلية:</p>
<ul>
    <li>وسائل التواصل المتاحة للرد على الاستفسارات.</li>
    <li>المدة الزمنية للإجابة على استفسارات المتدربين.</li>
    <li>إجراءات التصعيد في حال عدم الإجابة في المدة المحددة.</li>
</ul>

<h3>حادي عشر: سياسة تقييم الأنشطة والمهام</h3>
<p>توفر الجهة سياسة تقييم الأنشطة والمهام في البرنامج/المقرر تتضمن كحد أدنى طريقة توزيع درجات تقييم أنشطة ومهام المتدرب، وتنشرها داخل صفحة المقرر، وذلك للبرنامج الذي تزيد مدته عن شهر.</p>
';

        $contentEn = '
<h2>E-Learning Policies and Standards</h2>

<h3>1. Commitment to Integrity in the E-Training Environment</h3>
<p>The institution ensures integrity in the e-training environment by maintaining a policy that covers the concept of integrity, forms of violations, and disciplinary procedures. Methods to prevent identity impersonation are defined, along with mechanisms for reviewing trainees\' work and preventing cheating.</p>
<ul>
    <li>Definition of academic integrity.</li>
    <li>Forms of integrity violations: cheating, impersonation, misuse of collaboration, forgery, etc.</li>
    <li>Procedures and penalties for violations of integrity in e-learning/training.</li>
    <li>Mechanisms to prevent identity impersonation and detect cheating for programs exceeding one month.</li>
</ul>

<h3>2. Commitment to Protecting Beneficiary Data Privacy</h3>
<p>The institution provides clear policies and procedures for collecting, storing, processing, and disposing of data, ensuring privacy, protection, and access control — only authorized personnel may view or share data. The institution also complies with data governance regulations from relevant regulatory bodies.</p>
<ul>
    <li>Types of data collected by the institution.</li>
    <li>Methods and purposes of data collection.</li>
    <li>Storage duration and retention policy.</li>
    <li>Who has access to and may share the data.</li>
    <li>Compliance with personal data protection regulations.</li>
</ul>

<h3>3. Organizational Structure and Roles in E-Training</h3>
<p>The institution clearly defines the roles and responsibilities of supervisory, educational/training, technical, administrative, and support staff involved in e-learning/training operations.</p>
<ul>
    <li>Personal information (name and job title).</li>
    <li>Roles and responsibilities.</li>
    <li>Team size.</li>
</ul>

<h3>4. E-Attendance Policy</h3>
<p>The institution maintains an attendance policy that recognizes electronic attendance as equivalent to in-person attendance, specifying the minimum attendance hours and procedures for non-compliance. The institution monitors trainee attendance in both synchronous and asynchronous modes.</p>
<ul>
    <li>Electronic attendance is equivalent to in-person attendance.</li>
    <li>Minimum required attendance hours per program.</li>
    <li>Procedures for trainees who fail to meet minimum attendance requirements.</li>
    <li>At least 25% of program hours must be delivered via virtual classrooms (for programs exceeding one month).</li>
</ul>

<h3>5. Communication Policy in the E-Training Environment</h3>
<p>The institution clarifies the communication policy between all parties in the e-learning/training environment, including instructor-to-trainee and peer-to-peer communication, covering available tools and communication etiquette.</p>
<ul>
    <li>Communication tools and channels: discussions, email, forums.</li>
    <li>Communication etiquette: respect, no offensive language, no political or religious discussions.</li>
    <li>Actions taken for violations of communication etiquette.</li>
</ul>

<h3>6. Identity Verification System</h3>
<p>The institution provides a system or tool to verify the identity of beneficiaries each time they log into the LMS/training platform, such as national ID authentication or two-factor authentication.</p>

<h3>7. Instructions for Using the E-Training Environment</h3>
<p>The institution provides illustrated electronic guides, interactive manuals, and video tutorials for both instructors and trainees explaining the LMS/training platform at all stages of the training process.</p>
<ul>
    <li>How to log in, select courses, and begin training.</li>
    <li>How to use virtual classrooms and interact with content.</li>
    <li>How to complete assignments and track program progress.</li>
    <li>How to complete assessments and submit assignments.</li>
</ul>

<h3>8. Technical Support for Beneficiaries</h3>
<p>The institution provides clear technical support instructions for all categories of beneficiaries, ensuring support is available for inquiries, complaints, and technical issues that may hinder the training process.</p>
<ul>
    <li>Support channels (minimum two channels).</li>
    <li>Services covered by technical support.</li>
    <li>Procedures for receiving and handling complaints and suggestions.</li>
    <li>Support team working hours and expected response time.</li>
    <li>Escalation procedures if response deadlines are not met.</li>
</ul>

<h3>9. Digital Content of Training Programs</h3>
<p>The institution provides complete digital content on the LMS/training platform, aligned with approved objectives and topics. Access to virtual classroom sessions is available through the course page for synchronous delivery topics.</p>

<h3>10. Mechanism for Responding to Trainee Inquiries</h3>
<p>The institution provides a mechanism for responding to trainee inquiries by instructors during the program/course period, including available communication means and escalation procedures if responses are not provided within the specified timeframe.</p>
<ul>
    <li>Available communication means for responding to inquiries.</li>
    <li>Expected response time for trainee inquiries.</li>
    <li>Escalation procedures for unanswered inquiries.</li>
</ul>

<h3>11. Activity and Assignment Assessment Policy</h3>
<p>The institution provides an assessment policy for activities and assignments in the program/course, including at a minimum the distribution of assessment grades for trainee activities and tasks, published within the course page for programs exceeding one month.</p>
';

        DB::table('pages')->insertOrIgnore([
            'slug'         => 'elearning-policy',
            'title_ar'     => 'سياسات التدريب الإلكتروني',
            'title_en'     => 'E-Learning Policies',
            'content_ar'   => $contentAr,
            'content_en'   => $contentEn,
            'category'     => 'legal',
            'is_published' => true,
            'version'      => 1,
            'published_at' => $now,
            'created_at'   => $now,
            'updated_at'   => $now,
        ]);

        // Add footer link for the policy page
        DB::table('footer_links')->insertOrIgnore([
            'label_ar'    => 'سياسات التدريب الإلكتروني',
            'label_en'    => 'E-Learning Policies',
            'url'         => '/page/elearning-policy',
            'section'     => 'services',
            'sort_order'  => 7,
            'is_active'   => true,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('pages')->where('slug', 'elearning-policy')->delete();
        DB::table('footer_links')->where('url', '/page/elearning-policy')->delete();
    }
};
