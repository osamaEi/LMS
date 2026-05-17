<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramMKTSeeder extends Seeder
{
    public function run(): void
    {
        $program = Program::where('code', 'DIP-MKT-002')->first();
        if (!$program) {
            $this->command->error('Program DIP-MKT-002 not found.');
            return;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $termIds = Term::where('program_id', $program->id)->pluck('id');
        DB::table('term_subject')->whereIn('term_id', $termIds)->delete();
        Subject::where('program_id', $program->id)->forceDelete();
        Term::where('program_id', $program->id)->forceDelete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $semesters = [
            1 => ['ar' => 'الفصل التدريبي الأول',  'en' => '1st Semester'],
            2 => ['ar' => 'الفصل التدريبي الثاني',  'en' => '2nd Semester'],
            3 => ['ar' => 'الفصل التدريبي الثالث',  'en' => '3rd Semester'],
            4 => ['ar' => 'الفصل التدريبي الرابع',  'en' => '4th Semester'],
            5 => ['ar' => 'الفصل التدريبي الخامس',  'en' => '5th Semester'],
        ];

        $terms = [];
        foreach ($semesters as $num => $names) {
            $terms[$num] = Term::create([
                'program_id'  => $program->id,
                'term_number' => $num,
                'name_ar'     => $names['ar'],
                'name_en'     => $names['en'],
                'status'      => 'upcoming',
            ]);
        }

        // [code, name_ar, name_en, credits, semester]
        $subjects = [
            // ── Semester 1 ──
            ['ARAB 101',  'الكتابة الفنية',                    'Arabic Language',                        2, 1],
            ['ISLM 101',  'الدراسات الإسلامية',                'Islamic Studies',                        2, 1],
            ['VOCA 101',  'التوجيه المهني والتميز',             'Vocational Guidance & Excellence',       2, 1],
            ['KABB 101',  'التعرف على عالم الأعمال (١)',         'Know About Business -1',                 4, 1],
            ['MATH 101',  'الرياضيات',                         'Mathematics',                            3, 1],
            ['ICMT 101',  'مقدمة تطبيقات الحاسب',              'Introduction to Computer Applications',   2, 1],
            ['ENGL 111',  'لغة إنجليزية (١)',                   'English Language -1',                    3, 1],
            ['UMAN 101',  'مبادئ إدارة الأعمال',               'Principles of Business Administration',   4, 1],
            ['USEL 111',  'مبادئ التسويق',                     'Principles of Marketing',                 4, 1],

            // ── Semester 2 ──
            ['LEAS 101',  'مهارات التعلم',                     'Learning Skills',                        2, 2],
            ['ENGL 112',  'لغة إنجليزية (٢)',                   'English Language -2',                    3, 2],
            ['ECON 101',  'مقدمة في الاقتصاد',                 'Principles of Economics',                3, 2],
            ['USEL 121',  'قنوات التوزيع',                     'Channels of Distribution',               3, 2],
            ['USEL 122',  'ترويج المنتجات',                    'Product Promotion',                      2, 2],
            ['ICMT 102',  'تطبيقات الحاسب المتقدمة',           'Advanced Computer Applications',          2, 2],
            ['UACC 101',  'مبادئ محاسبة',                      'Accounting Principles',                  3, 2],

            // ── Semester 3 ──
            ['STAT 101',  'مقدمة في الإحصاء',                  'Introduction to Statistics',             3, 3],
            ['ENGL 113',  'لغة إنجليزية (٣)',                   'English Language -3',                    3, 3],
            ['USEL 241',  'سلوك المستهلك',                     'Consumer Behavior',                      3, 3],
            ['USEL 242',  'خدمة العملاء',                      'Customer Services',                      3, 3],
            ['USEL 131',  'مهارات البيع (١)',                   'Selling Skills (1)',                      4, 3],
            ['UMAN 271',  'إدارة الجودة الشاملة',              'Total Quality Management',                6, 3],

            // ── Semester 4 ──
            ['ETHS 101',  'السلوك الوظيفي ومهارات الاتصال',    'Professional Ethics & Comm. Skills',     2, 4],
            ['KABB 102',  'التعرف على عالم الأعمال (٢)',         'Know About Business -2',                 2, 4],
            ['USEL 271',  'بحوث التسويق',                      'Marketing Research',                     3, 4],
            ['USEL 261',  'التسويق الإلكتروني',                'Electronic Marketing',                   4, 4],
            ['USEL 232',  'مهارات البيع (٢)',                   'Selling Skills (2)',                      3, 4],
            ['USEL 285',  'موضوعات مختارة',                    'Selected Topics',                        3, 4],
            ['USEL 251',  'تطبيقات تسويقية على الحاسب',        'Marketing Applications on Computer',     3, 4],

            // ── Semester 5 ──
            ['USEL 298',  'التدريب التعاوني -١',               'Co-operative Training -1',               2, 5],
            ['USEL 299',  'التدريب التعاوني -٢',               'Co-operative Training -2',               2, 5],
        ];

        foreach ($subjects as [$code, $nameAr, $nameEn, $credits, $termNum]) {
            $subject = Subject::create([
                'program_id' => $program->id,
                'term_id'    => $terms[$termNum]->id,
                'code'       => $code,
                'name_ar'    => $nameAr,
                'name_en'    => $nameEn,
                'credits'    => $credits,
                'status'     => 'active',
            ]);
            $terms[$termNum]->subjects()->syncWithoutDetaching([$subject->id]);
        }

        $this->command->info('✓ MKT Program seeded: 5 terms, ' . count($subjects) . ' subjects');
    }
}
