<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Term;
use App\Models\Subject;

class PublicRelationsDiplomaSeeder extends Seeder
{
    public function run(): void
    {
        // Create the diploma program
        $program = Program::create([
            'name_ar'        => 'قسم العلوم الإدارية — تخصص العلاقات العامة',
            'name_en'        => 'Administrative Sciences - Public Relations',
            'code'           => 'PR-DIPLOMA',
            'type'           => 'diploma',
            'status'         => 'active',
            'duration_months'=> 10,
            'description_ar' => 'برنامج دبلوم تخصص العلاقات العامة — 10 أسابيع تدريب شاملة',
        ]);

        // ── الربع الأول (20 ساعة/أسبوع) ───────────────────────────────────
        $term1 = Term::create([
            'program_id'  => $program->id,
            'term_number' => 1,
            'name_ar'     => 'الربع التدريبي الأول (عشرة أسابيع) 20 ساعة في الأسبوع',
            'status'      => 'active',
        ]);

        $term1Subjects = [
            ['code' => '001 سلم', 'name_ar' => 'ثقافة إسلامية (1)',              'credits' => 1],
            ['code' => '001 حال', 'name_ar' => 'مقدمة الحاسب وتطبيقاته',        'credits' => 1],
            ['code' => '101 علق', 'name_ar' => 'مدخل إلى العلاقات العامة',       'credits' => 3],
            ['code' => '102 علق', 'name_ar' => 'الأسس العامة للعلاقات العامة',   'credits' => 2],
            ['code' => '103 علق', 'name_ar' => 'الدعاية والإعلان والعلاقات العامة في المدونات الإلكترونية', 'credits' => 3],
            ['code' => '104 علق', 'name_ar' => 'العلاقات العامة والصورة الذهنية', 'credits' => 2],
        ];

        foreach ($term1Subjects as $s) {
            Subject::create([
                'program_id' => $program->id,
                'term_id'    => $term1->id,
                'code'       => $s['code'],
                'name_ar'    => $s['name_ar'],
                'credits'    => $s['credits'],
                'name_en'    => '',
                'name_en'    => '',
                'status'     => 'active',
                'language'   => 'ar',
                'delivery_mode' => 'in_person',
            ]);
        }

        // ── الربع الثاني (21 ساعة/أسبوع) ──────────────────────────────────
        $term2 = Term::create([
            'program_id'  => $program->id,
            'term_number' => 2,
            'name_ar'     => 'الربع التدريبي الثاني (عشرة أسابيع) 21 ساعة في الأسبوع',
            'status'      => 'active',
        ]);

        $term2Subjects = [
            ['code' => '002 سلم',  'name_ar' => 'ثقافة إسلامية (2)',                       'credits' => 1],
            ['code' => '002 حال',  'name_ar' => 'معالجة النصوص',                           'credits' => 1],
            ['code' => '201 علق',  'name_ar' => 'العلاقات العامة والعلاقات الانسانية',     'credits' => 3],
            ['code' => '202 علق',  'name_ar' => 'إدارة العلاقات العامة والرأي العام',      'credits' => 3],
            ['code' => '104 دار',  'name_ar' => 'مبادئ إدارة الأعمال',                    'credits' => 2],
        ];

        foreach ($term2Subjects as $s) {
            Subject::create([
                'program_id' => $program->id,
                'term_id'    => $term2->id,
                'code'       => $s['code'],
                'name_ar'    => $s['name_ar'],
                'credits'    => $s['credits'],
                'name_en'    => '',
                'status'     => 'active',
                'language'   => 'ar',
                'delivery_mode' => 'in_person',
            ]);
        }

        // ── الربع الثالث (20 ساعة/أسبوع) ──────────────────────────────────
        $term3 = Term::create([
            'program_id'  => $program->id,
            'term_number' => 3,
            'name_ar'     => 'الربع التدريبي الثالث (عشرة أسابيع) 20 ساعة في الأسبوع',
            'status'      => 'active',
        ]);

        $term3Subjects = [
            ['code' => '003 سلم', 'name_ar' => 'ثقافة إسلامية (3)',              'credits' => 1],
            ['code' => '003 حال', 'name_ar' => 'استخدام الإنترنت',              'credits' => 1],
            ['code' => '106 نجل', 'name_ar' => 'لغة انجليزية عامة',             'credits' => 3],
            ['code' => '225 دار', 'name_ar' => 'إدارة الموارد البشرية',          'credits' => 2],
            ['code' => '301 علق', 'name_ar' => 'العلاقات العامة في الأزمات',     'credits' => 2],
            ['code' => '302 علق', 'name_ar' => 'خدمة العملاء',                   'credits' => 2],
        ];

        foreach ($term3Subjects as $s) {
            Subject::create([
                'program_id' => $program->id,
                'term_id'    => $term3->id,
                'code'       => $s['code'],
                'name_ar'    => $s['name_ar'],
                'credits'    => $s['credits'],
                'name_en'    => '',
                'status'     => 'active',
                'language'   => 'ar',
                'delivery_mode' => 'in_person',
            ]);
        }

        // ── الربع الرابع (20 ساعة/أسبوع) ──────────────────────────────────
        $term4 = Term::create([
            'program_id'  => $program->id,
            'term_number' => 4,
            'name_ar'     => 'الربع التدريبي الرابع (عشرة أسابيع) 20 ساعة في الأسبوع',
            'status'      => 'active',
        ]);

        $term4Subjects = [
            ['code' => '004 سلم', 'name_ar' => 'ثقافة إسلامية (4)',          'credits' => 1],
            ['code' => '001 سلك', 'name_ar' => 'سلوك مهني',                  'credits' => 2],
            ['code' => '102 سوق', 'name_ar' => 'مبادئ التسويق',              'credits' => 2],
            ['code' => '401 علق', 'name_ar' => 'مهارات الاتصال',             'credits' => 3],
            ['code' => '402 علق', 'name_ar' => 'مشروع التخرج (بحث)',         'credits' => 3],
        ];

        foreach ($term4Subjects as $s) {
            Subject::create([
                'program_id' => $program->id,
                'term_id'    => $term4->id,
                'code'       => $s['code'],
                'name_ar'    => $s['name_ar'],
                'credits'    => $s['credits'],
                'name_en'    => '',
                'status'     => 'active',
                'language'   => 'ar',
                'delivery_mode' => 'in_person',
            ]);
        }

        // ── الربع الخاص بالتدريب التعاوني (12 أسبوع) ──────────────────────
        $term5 = Term::create([
            'program_id'  => $program->id,
            'term_number' => 5,
            'name_ar'     => 'الربع الخاص بالتدريب التعاوني (12 أسبوع)',
            'status'      => 'active',
        ]);

        $term5Subjects = [
            ['code' => '501 علق', 'name_ar' => 'تدريب تعاوني (1)', 'credits' => 2],
            ['code' => '502 علق', 'name_ar' => 'تدريب تعاوني (2)', 'credits' => 2],
        ];

        foreach ($term5Subjects as $s) {
            Subject::create([
                'program_id' => $program->id,
                'term_id'    => $term5->id,
                'code'       => $s['code'],
                'name_ar'    => $s['name_ar'],
                'credits'    => $s['credits'],
                'name_en'    => '',
                'status'     => 'active',
                'language'   => 'ar',
                'delivery_mode' => 'in_person',
            ]);
        }

        $this->command->info('✓ Public Relations Diploma created with ' . $program->fresh()->subjects()->count() . ' subjects across 5 terms.');
    }
}

