<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();
            $table->string('label_ar');
            $table->string('label_en')->nullable();
            $table->string('url');
            $table->enum('section', ['quick', 'services'])->default('quick');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default settings
        $now = now();
        DB::table('site_settings')->insert([
            ['key' => 'footer_description_ar', 'value' => 'معهد تدريبي معتمد يقدم برامج التطوير المهني والمسارات التعليمية منذ أكثر من 10 سنوات، بما يتوافق مع رؤية السعودية 2030.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'footer_description_en', 'value' => 'An accredited training institute offering professional development programs and educational paths for over 10 years, aligned with Saudi Vision 2030.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'phone',                 'value' => '9200343222',              'created_at' => $now, 'updated_at' => $now],
            ['key' => 'email',                 'value' => 'help@alertiqa.edu.sa',    'created_at' => $now, 'updated_at' => $now],
            ['key' => 'address_ar',            'value' => 'الرياض، المملكة العربية السعودية', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'address_en',            'value' => 'Riyadh, Saudi Arabia',    'created_at' => $now, 'updated_at' => $now],
            ['key' => 'working_hours_ar',      'value' => 'الأحد – الخميس: 8:00 ص – 5:00 م', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'working_hours_en',      'value' => 'Sun – Thu: 8:00 AM – 5:00 PM', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'copyright_ar',          'value' => 'معهد الارتقاء العالي للتدريب. جميع الحقوق محفوظة.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'copyright_en',          'value' => 'Al-Ertiqaa High Institute for Training. All rights reserved.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'social_twitter',        'value' => '',                        'created_at' => $now, 'updated_at' => $now],
            ['key' => 'social_instagram',      'value' => '',                        'created_at' => $now, 'updated_at' => $now],
            ['key' => 'social_linkedin',       'value' => '',                        'created_at' => $now, 'updated_at' => $now],
            ['key' => 'social_youtube',        'value' => '',                        'created_at' => $now, 'updated_at' => $now],
            ['key' => 'social_facebook',       'value' => '',                        'created_at' => $now, 'updated_at' => $now],
            ['key' => 'social_snapchat',       'value' => '',                        'created_at' => $now, 'updated_at' => $now],
            ['key' => 'social_whatsapp',       'value' => '',                        'created_at' => $now, 'updated_at' => $now],
        ]);

        // Seed default footer links
        $links = [
            ['label_ar' => 'الرئيسية',       'label_en' => 'Home',             'url' => '/',                 'section' => 'quick',    'sort_order' => 1],
            ['label_ar' => 'من نحن',          'label_en' => 'About Us',         'url' => '/about',            'section' => 'quick',    'sort_order' => 2],
            ['label_ar' => 'الدبلومات  ',       'label_en' => 'Training Paths',   'url' => '/training-paths',   'section' => 'quick',    'sort_order' => 3],
            ['label_ar' => 'الدورات',         'label_en' => 'Courses',          'url' => '/short-courses',    'section' => 'quick',    'sort_order' => 4],
            ['label_ar' => 'الأخبار',         'label_en' => 'News',             'url' => '/news',             'section' => 'quick',    'sort_order' => 5],
            ['label_ar' => 'تواصل معنا',      'label_en' => 'Contact Us',       'url' => '/contact',          'section' => 'quick',    'sort_order' => 6],
            ['label_ar' => 'برامج الترم',     'label_en' => 'Term Programs',    'url' => '/training-paths',   'section' => 'services', 'sort_order' => 1],
            ['label_ar' => 'الدورات', 'label_en' => 'Short Courses',    'url' => '/short-courses',    'section' => 'services', 'sort_order' => 2],
            ['label_ar' => 'التدريب عن بُعد', 'label_en' => 'Remote Training',  'url' => '/short-courses',    'section' => 'services', 'sort_order' => 3],
            ['label_ar' => 'الدعم الفني',     'label_en' => 'Technical Support','url' => '/faq',              'section' => 'services', 'sort_order' => 4],
            ['label_ar' => 'سياسة الخصوصية', 'label_en' => 'Privacy Policy',   'url' => '/page/privacy-policy','section'=>'services', 'sort_order' => 5],
            ['label_ar' => 'الشروط والأحكام', 'label_en' => 'Terms & Conditions','url' => '/page/terms',      'section' => 'services', 'sort_order' => 6],
        ];

        foreach ($links as $link) {
            DB::table('footer_links')->insert(array_merge($link, ['is_active' => true, 'created_at' => $now, 'updated_at' => $now]));
        }

        // Seed default pages
        DB::table('pages')->insertOrIgnore([
            [
                'slug' => 'privacy-policy',
                'title_ar' => 'سياسة الخصوصية',
                'title_en' => 'Privacy Policy',
                'content_ar' => '<h2>سياسة الخصوصية</h2><p>يلتزم معهد الارتقاء العالي للتدريب بحماية خصوصية مستخدميه. تصف هذه السياسة كيفية جمع معلوماتك الشخصية واستخدامها وحمايتها.</p><p>نحن نجمع المعلومات التي تقدمها طوعاً عند التسجيل أو التواصل معنا. لن نشارك بياناتك مع أطراف ثالثة دون موافقتك.</p>',
                'content_en' => '<h2>Privacy Policy</h2><p>Al-Ertiqaa High Institute for Training is committed to protecting the privacy of its users. This policy describes how we collect, use, and protect your personal information.</p><p>We collect information you voluntarily provide when registering or contacting us. We will not share your data with third parties without your consent.</p>',
                'category' => 'legal',
                'is_published' => true,
                'version' => 1,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'terms',
                'title_ar' => 'الشروط والأحكام',
                'title_en' => 'Terms & Conditions',
                'content_ar' => '<h2>الشروط والأحكام</h2><p>باستخدامك لموقع معهد الارتقاء العالي للتدريب، فإنك توافق على الشروط والأحكام التالية. يُرجى قراءتها بعناية قبل استخدام الخدمات.</p><p>يحتفظ المعهد بحق تعديل هذه الشروط في أي وقت. سيتم إخطارك بأي تغييرات جوهرية.</p>',
                'content_en' => '<h2>Terms & Conditions</h2><p>By using Al-Ertiqaa High Institute for Training website, you agree to the following terms and conditions. Please read them carefully before using our services.</p><p>The Institute reserves the right to modify these terms at any time. You will be notified of any material changes.</p>',
                'category' => 'legal',
                'is_published' => true,
                'version' => 1,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'refund-policy',
                'title_ar' => 'سياسة الاسترداد',
                'title_en' => 'Refund Policy',
                'content_ar' => '<h2>سياسة الاسترداد</h2><p>يمكن استرداد رسوم التسجيل خلال 7 أيام من تاريخ الدفع وقبل بدء البرنامج التدريبي. لا يحق الاسترداد بعد بدء البرنامج.</p>',
                'content_en' => '<h2>Refund Policy</h2><p>Registration fees may be refunded within 7 days of payment and before the training program begins. No refunds are available after the program has started.</p>',
                'category' => 'legal',
                'is_published' => true,
                'version' => 1,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('footer_links');
        Schema::dropIfExists('site_settings');
    }
};
