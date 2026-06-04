<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Program;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('programs')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $programs = [

            // ═══════════════════════════════════════════
            // الدبلومات    (type = diploma)
            // ═══════════════════════════════════════════
            [
                'name_ar'         => 'قسم الحاسب وتقنية المعلومات — تخصص برمجيات الحاسب',
                'name_en'         => 'Computer Science & IT — Computer Software',
                'code'            => 'DIP-CS-001',
                'description_ar'  => 'دبلوم متخصصة في علوم الحاسب وتقنية المعلومات بتخصص برمجيات الحاسب، معتمدة من المؤسسة العامة للتدريب التقني والمهني.',
                'description_en'  => 'Specialized diploma in Computer Science and IT with a focus on computer software, accredited by TVTC.',
                'type'            => 'diploma',
                'course_type'     => null,
                'duration_months' => 30,
                'price'           => 0,
                'status'          => 'active',
            ],
            [
                'name_ar'         => 'قسم التقنية الإدارية — تخصص التسويق ',
                'name_en'         => 'Administrative Technology — Marketing',
                'code'            => 'DIP-MKT-002',
                'description_ar'  => 'دبلوم في التقنية الإدارية بتخصص الالتسويق ، معتمدة من المؤسسة العامة للتدريب التقني والمهني.',
                'description_en'  => 'Administrative Technology diploma with a Marketing specialization, accredited by TVTC.',
                'type'            => 'diploma',
                'course_type'     => null,
                'duration_months' => 30,
                'price'           => 0,
                'status'          => 'active',
            ],
            [
                'name_ar'         => 'تخصص إدارة الموارد البشرية',
                'name_en'         => 'Human Resources Management',
                'code'            => 'DIP-HR-003',
                'description_ar'  => 'دبلوم متخصصة في إدارة الموارد البشرية، معتمدة من المؤسسة العامة للتدريب التقني والمهني.',
                'description_en'  => 'Specialized diploma in Human Resources Management, accredited by TVTC.',
                'type'            => 'diploma',
                'course_type'     => null,
                'duration_months' => 30,
                'price'           => 0,
                'status'          => 'active',
            ],

            // ═══════════════════════════════════════════
            // البرامج  (type = training)
            // ═══════════════════════════════════════════
            [
                'name_ar'         => 'قسم العلوم الإدارية — تخصص العلاقات العامة',
                'name_en'         => 'Administrative Sciences — Public Relations',
                'code'            => 'PRG-PR-001',
                'description_ar'  => 'برنامج في العلوم الإدارية بتخصص العلاقات العامة، معتمد من المؤسسة العامة للتدريب التقني والمهني.',
                'description_en'  => 'Administrative Sciences program with a Public Relations specialization, accredited by TVTC.',
                'type'            => 'training',
                'course_type'     => null,
                'duration_months' => 12,
                'price'           => 0,
                'status'          => 'active',
            ],

            // ═══════════════════════════════════════════
            // الدورات التأهيلية  (type = course, course_type = qualifying)
            // ═══════════════════════════════════════════
            [
                'name_ar'     => 'إدخال البيانات ومعالجة النصوص',
                'name_en'     => 'Data Entry and Text Processing',
                'code'        => 'QUA-001',
                'description_ar' => 'دورة تأهيلية في مهارات إدخال البيانات ومعالجة النصوص باستخدام التطبيقات المكتبية.',
                'description_en' => 'Qualifying course in data entry and text processing using office applications.',
                'type'        => 'course',
                'course_type' => 'qualifying',
                'duration_months' => 2,
                'price'       => 0,
                'status'      => 'active',
            ],
            [
                'name_ar'     => 'السكرتارية',
                'name_en'     => 'Secretarial Skills',
                'code'        => 'QUA-002',
                'description_ar' => 'دورة تأهيلية في مهارات السكرتارية وإدارة المكاتب.',
                'description_en' => 'Qualifying course in secretarial and office management skills.',
                'type'        => 'course',
                'course_type' => 'qualifying',
                'duration_months' => 2,
                'price'       => 0,
                'status'      => 'active',
            ],

            // ═══════════════════════════════════════════
            // الدورات التطويرية  (type = course, course_type = developmental)
            // ═══════════════════════════════════════════
            ['name_ar'=>'التسويق الرقمي','name_en'=>'Digital Marketing','code'=>'DEV-001',
             'description_ar'=>'دورة تطويرية شاملة في التسويق الرقمي تشمل وسائل التواصل الاجتماعي والإعلانات المدفوعة وتحليلات التسويق.',
             'description_en'=>'Comprehensive developmental course in digital marketing covering social media, paid ads, and analytics.',
             'type'=>'course','course_type'=>'developmental','category'=>'تسويق ومبيعات','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'إدارة الموارد البشرية','name_en'=>'Human Resources Management','code'=>'DEV-002',
             'description_ar'=>'دورة تطويرية في إدارة الموارد البشرية وتطوير الكوادر الوظيفية.',
             'description_en'=>'Developmental course in human resources management and workforce development.',
             'type'=>'course','course_type'=>'developmental','category'=>'إدارة وأعمال','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'إدارة الجودة','name_en'=>'Quality Management','code'=>'DEV-003',
             'description_ar'=>'دورة تطويرية في مفاهيم وأنظمة إدارة الجودة الشاملة.',
             'description_en'=>'Developmental course in total quality management concepts and systems.',
             'type'=>'course','course_type'=>'developmental','category'=>'إدارة وأعمال','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'خدمة العملاء والعلاقات العامة','name_en'=>'Customer Service and Public Relations','code'=>'DEV-004',
             'description_ar'=>'دورة تطويرية في مهارات خدمة العملاء وبناء علاقات عامة فعّالة.',
             'description_en'=>'Developmental course in customer service skills and effective public relations.',
             'type'=>'course','course_type'=>'developmental','category'=>'إدارة وأعمال','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'أسرار النجاح والتميز في العمل','name_en'=>'Secrets of Success and Excellence at Work','code'=>'DEV-005',
             'description_ar'=>'دورة تطويرية في مهارات النجاح والتميز الوظيفي وتطوير الذات.',
             'description_en'=>'Developmental course on success skills, professional excellence, and self-development.',
             'type'=>'course','course_type'=>'developmental','category'=>'تطوير الذات','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'التدريب على مهارات برنامج نور','name_en'=>'Noor Program Skills Training','code'=>'DEV-006',
             'description_ar'=>'دورة تطويرية في استخدام نظام نور التعليمي والمهارات التقنية المرتبطة به.',
             'description_en'=>'Developmental course on using the Noor educational system and related technical skills.',
             'type'=>'course','course_type'=>'developmental','category'=>'تدريب وتعليم','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'تطوير مهارات المدربين','name_en'=>'Trainer Skills Development','code'=>'DEV-007',
             'description_ar'=>'دورة تطويرية لرفع كفاءة المدربون وتطوير أساليب التدريب الفعّال.',
             'description_en'=>'Developmental course to enhance trainer efficiency and develop effective training methods.',
             'type'=>'course','course_type'=>'developmental','category'=>'تدريب وتعليم','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'فن السكرتارية وإدارة المكاتب','name_en'=>'Art of Secretarial and Office Management','code'=>'DEV-008',
             'description_ar'=>'دورة تطويرية في فنون السكرتارية الاحترافية وإدارة المكاتب الحديثة.',
             'description_en'=>'Developmental course in professional secretarial arts and modern office management.',
             'type'=>'course','course_type'=>'developmental','category'=>'إدارة وأعمال','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'أساسيات استخدام ويندوز 7 وأوفيس 2007','name_en'=>'Windows 7 and Office 2007 Fundamentals','code'=>'DEV-009',
             'description_ar'=>'دورة تطويرية في أساسيات التعامل مع نظام ويندوز 7 وحزمة أوفيس 2007.',
             'description_en'=>'Developmental course covering Windows 7 and Office 2007 basics.',
             'type'=>'course','course_type'=>'developmental','category'=>'تقنية المعلومات','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'المهارات الأساسية للحاسب وقواعد السلوك الوظيفي','name_en'=>'Basic Computer Skills and Professional Conduct','code'=>'DEV-010',
             'description_ar'=>'دورة تطويرية في المهارات الأساسية لاستخدام الحاسب وآداب وقواعد السلوك الوظيفي.',
             'description_en'=>'Developmental course in basic computer skills and professional workplace conduct.',
             'type'=>'course','course_type'=>'developmental','category'=>'تقنية المعلومات','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'فوتوشوب المستوى المبتدئ','name_en'=>'Photoshop — Beginner Level','code'=>'DEV-011',
             'description_ar'=>'دورة تطويرية في أساسيات برنامج Adobe Photoshop للمستوى المبتدئ.',
             'description_en'=>'Developmental course covering Adobe Photoshop fundamentals at beginner level.',
             'type'=>'course','course_type'=>'developmental','category'=>'تصميم وفنون','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'أسرار المقابلات الشخصية','name_en'=>'Personal Interview Secrets','code'=>'DEV-012',
             'description_ar'=>'دورة تطويرية في مهارات التحضير للمقابلات الشخصية وأسرار النجاح فيها.',
             'description_en'=>'Developmental course on preparing for job interviews and succeeding in them.',
             'type'=>'course','course_type'=>'developmental','category'=>'تطوير الذات','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'الأرشفة الإلكترونية','name_en'=>'Electronic Archiving','code'=>'DEV-013',
             'description_ar'=>'دورة تطويرية في أنظمة الأرشفة الإلكترونية وإدارة الوثائق الرقمية.',
             'description_en'=>'Developmental course in electronic archiving systems and digital document management.',
             'type'=>'course','course_type'=>'developmental','category'=>'تقنية المعلومات','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'أساسيات الرسم','name_en'=>'Drawing Fundamentals','code'=>'DEV-014',
             'description_ar'=>'دورة تطويرية في أساسيات فن الرسم والتصميم الإبداعي.',
             'description_en'=>'Developmental course in the fundamentals of drawing and creative design.',
             'type'=>'course','course_type'=>'developmental','category'=>'تصميم وفنون','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'الأمن السيبراني','name_en'=>'Cybersecurity','code'=>'DEV-015',
             'description_ar'=>'دورة تطويرية في أساسيات الأمن السيبراني وحماية المعلومات والأنظمة الرقمية.',
             'description_en'=>'Developmental course in cybersecurity fundamentals and digital systems protection.',
             'type'=>'course','course_type'=>'developmental','category'=>'تقنية المعلومات','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'المهارات المتقدمة في برنامج الاكسل','name_en'=>'Advanced Excel Skills','code'=>'DEV-016',
             'description_ar'=>'دورة تطويرية في المهارات المتقدمة لبرنامج Microsoft Excel وتحليل البيانات.',
             'description_en'=>'Developmental course in advanced Microsoft Excel skills and data analysis.',
             'type'=>'course','course_type'=>'developmental','category'=>'تقنية المعلومات','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'إدارة الوقت والتعامل مع الضغوط','name_en'=>'Time Management and Stress Handling','code'=>'DEV-017',
             'description_ar'=>'دورة تطويرية في مهارات إدارة الوقت الفعّالة والتعامل مع ضغوط العمل.',
             'description_en'=>'Developmental course in effective time management and workplace stress handling.',
             'type'=>'course','course_type'=>'developmental','category'=>'إدارة وأعمال','duration_months'=>1,'price'=>0,'status'=>'active'],

            ['name_ar'=>'أساسيات الذكاء الاصطناعي','name_en'=>'Artificial Intelligence Fundamentals','code'=>'DEV-018',
             'description_ar'=>'دورة تطويرية في أساسيات الذكاء الاصطناعي وتطبيقاته في بيئة العمل.',
             'description_en'=>'Developmental course in AI fundamentals and its workplace applications.',
             'type'=>'course','course_type'=>'developmental','category'=>'تقنية المعلومات','duration_months'=>1,'price'=>0,'status'=>'active'],
        ];

        // ═══════════════════════════════════════════
        // دورات اللغة الإنجليزية  (type = english)
        // ═══════════════════════════════════════════
        $english = [
            [
                'name_ar' => 'اللغة الإنجليزية المستوى التمهيدي',
                'name_en' => 'English Language — Introductory Level',
                'code'    => 'ENG-000',
                'description_ar' => 'دورة اللغة الإنجليزية للمستوى التمهيدي، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Introductory Level course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 0, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية المستوى التأسيسي',
                'name_en' => 'English Language — Foundation Level',
                'code'    => 'ENG-001',
                'description_ar' => 'دورة اللغة الإنجليزية للمستوى التأسيسي، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Foundation Level course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 1, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية المستوى المبتدئ',
                'name_en' => 'English Language — Beginner Level',
                'code'    => 'ENG-002',
                'description_ar' => 'دورة اللغة الإنجليزية للمستوى المبتدئ، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Beginner Level course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 2, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية من المستوى الأول',
                'name_en' => 'English Language — Level 1',
                'code'    => 'ENG-003',
                'description_ar' => 'دورة اللغة الإنجليزية المستوى الأول، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Level 1 course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 3, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية من المستوى الثاني',
                'name_en' => 'English Language — Level 2',
                'code'    => 'ENG-004',
                'description_ar' => 'دورة اللغة الإنجليزية المستوى الثاني، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Level 2 course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 4, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية من المستوى الثالث',
                'name_en' => 'English Language — Level 3',
                'code'    => 'ENG-005',
                'description_ar' => 'دورة اللغة الإنجليزية المستوى الثالث، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Level 3 course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 5, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية من المستوى الرابع',
                'name_en' => 'English Language — Level 4',
                'code'    => 'ENG-006',
                'description_ar' => 'دورة اللغة الإنجليزية المستوى الرابع، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Level 4 course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 6, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية من المستوى الخامس',
                'name_en' => 'English Language — Level 5',
                'code'    => 'ENG-007',
                'description_ar' => 'دورة اللغة الإنجليزية المستوى الخامس، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Level 5 course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 7, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية من المستوى السادس',
                'name_en' => 'English Language — Level 6',
                'code'    => 'ENG-008',
                'description_ar' => 'دورة اللغة الإنجليزية المستوى السادس، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Level 6 course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 8, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية من المستوى السابع',
                'name_en' => 'English Language — Level 7',
                'code'    => 'ENG-009',
                'description_ar' => 'دورة اللغة الإنجليزية المستوى السابع، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Level 7 course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 9, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية من المستوى الثامن',
                'name_en' => 'English Language — Level 8',
                'code'    => 'ENG-010',
                'description_ar' => 'دورة اللغة الإنجليزية المستوى الثامن، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Level 8 course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 10, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية من المستوى التاسع',
                'name_en' => 'English Language — Level 9',
                'code'    => 'ENG-011',
                'description_ar' => 'دورة اللغة الإنجليزية المستوى التاسع، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Level 9 course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 11, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية من المستوى العاشر',
                'name_en' => 'English Language — Level 10',
                'code'    => 'ENG-012',
                'description_ar' => 'دورة اللغة الإنجليزية المستوى العاشر، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Level 10 course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 12, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية من المستوى الحادي عشر',
                'name_en' => 'English Language — Level 11',
                'code'    => 'ENG-013',
                'description_ar' => 'دورة اللغة الإنجليزية المستوى الحادي عشر، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Level 11 course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 13, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
            [
                'name_ar' => 'اللغة الإنجليزية من المستوى الثاني عشر',
                'name_en' => 'English Language — Level 12',
                'code'    => 'ENG-014',
                'description_ar' => 'دورة اللغة الإنجليزية المستوى الثاني عشر، مدتها شهر واحد بعدد 40 ساعة تدريبية.',
                'description_en' => 'English Language Level 12 course, 1 month / 40 training hours.',
                'type'    => 'english', 'level' => 14, 'duration_months' => 1, 'price' => 0, 'status' => 'active',
            ],
        ];

        foreach ($programs as $data) {
            Program::create($data);
        }

        foreach ($english as $data) {
            Program::create($data);
        }

        $total = count($programs) + count($english);
        $this->command->info('✓ Programs seeded: ' . $total . ' records');
        $this->command->info('  — Diplomas:              3');
        $this->command->info('  — Training Programs:     1');
        $this->command->info('  — Qualifying Courses:    2');
        $this->command->info('  — Developmental Courses: 18');
        $this->command->info('  — English Language:      ' . count($english));
    }
}
