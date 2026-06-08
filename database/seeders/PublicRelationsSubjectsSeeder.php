<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Subject;

class PublicRelationsSubjectsSeeder extends Seeder
{
    public function run(): void
    {
        $program = Program::where('code', 'PR-DIPLOMA')->firstOrFail();
        $terms   = $program->terms()->orderBy('term_number')->get()->keyBy('term_number');

        // helper
        $make = fn(int $term, string $code, string $name, int $credits) => [
            'program_id'    => $program->id,
            'term_id'       => $terms[$term]->id,
            'code'          => $code,
            'name_ar'       => $name,
            'name_en'       => '',
            'credits'       => $credits,
            'status'        => 'active',
            'language'      => 'ar',
            'delivery_mode' => 'in_person',
        ];

        $subjects = [
            // ── الربع الأول ────────────────────────────────────────────────
            $make(1, '001 سلم', 'ثقافة إسلامية (1)',                                              1),
            $make(1, '001 حال', 'مقدمة الحاسب وتطبيقاته',                                        1),
            $make(1, '101 علق', 'مدخل إلى العلاقات العامة',                                      3),
            $make(1, '102 علق', 'الأسس العامة للعلاقات العامة',                                  2),
            $make(1, '103 علق', 'الدعاية والإعلان والعلاقات العامة في المدونات الإلكترونية',     3),
            $make(1, '104 علق', 'العلاقات العامة والصورة الذهنية',                               2),

            // ── الربع الثاني ───────────────────────────────────────────────
            $make(2, '002 سلم', 'ثقافة إسلامية (2)',                                              1),
            $make(2, '002 حال', 'معالجة النصوص',                                                  1),
            $make(2, '201 علق', 'العلاقات العامة والعلاقات الانسانية',                            3),
            $make(2, '202 علق', 'إدارة العلاقات العامة والرأي العام',                             3),
            $make(2, '104 دار', 'مبادئ إدارة الأعمال',                                            2),

            // ── الربع الثالث ───────────────────────────────────────────────
            $make(3, '003 سلم', 'ثقافة إسلامية (3)',                                              1),
            $make(3, '003 حال', 'استخدام الإنترنت',                                               1),
            $make(3, '106 نجل', 'لغة انجليزية عامة',                                              3),
            $make(3, '225 دار', 'إدارة الموارد البشرية',                                          2),
            $make(3, '301 علق', 'العلاقات العامة في الأزمات',                                     2),
            $make(3, '302 علق', 'خدمة العملاء',                                                   2),

            // ── الربع الرابع ───────────────────────────────────────────────
            $make(4, '004 سلم', 'ثقافة إسلامية (4)',                                              1),
            $make(4, '001 سلك', 'سلوك مهني',                                                      2),
            $make(4, '102 سوق', 'مبادئ التسويق',                                                  2),
            $make(4, '401 علق', 'مهارات الاتصال',                                                 3),
            $make(4, '402 علق', 'مشروع التخرج (بحث)',                                              3),

            // ── الربع الخامس: التدريب التعاوني ────────────────────────────
            $make(5, '501 علق', 'تدريب تعاوني (1)',                                               2),
            $make(5, '502 علق', 'تدريب تعاوني (2)',                                               2),
        ];

        foreach ($subjects as $data) {
            $subject = Subject::updateOrCreate(
                ['program_id' => $data['program_id'], 'code' => $data['code']],
                $data
            );

            // Sync to term_subject pivot so the admin UI shows them
            \Illuminate\Support\Facades\DB::table('term_subject')->updateOrInsert(
                ['term_id' => $subject->term_id, 'subject_id' => $subject->id]
            );
        }

        $count = Subject::where('program_id', $program->id)->count();
        $this->command->info("✓ {$count} subjects seeded for {$program->name_ar}");
    }
}
