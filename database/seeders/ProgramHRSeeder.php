<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramHRSeeder extends Seeder
{
    public function run(): void
    {
        $program = Program::where('code', 'DIP-HR-003')->first();
        if (!$program) {
            $this->command->error('Program DIP-HR-003 not found.');
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
            ['AENG 105',  'لغة إنجليزية عامة',                        'General English Language',                          4, 1],
            ['ACOM 101',  'مقدمة تطبيقات الحاسب',                     'Introduction to Computer Applications',              2, 1],
            ['AVOC 107',  'التوجيه المهني والتميز',                    'Professional Guidance and Excellence',              2, 1],
            ['AISL 101',  'ثقافة إسلامية (١)',                         'Islamic Culture -1',                                2, 1],
            ['AMNG 104',  'مبادئ إدارة الأعمال',                      'Principles of Business Administration',              3, 1],
            ['AMAT 113',  'رياضيات عامة',                             'General Mathematics',                               3, 1],
            ['AUHR 101',  'إدارة الموارد البشرية (١)',                  'Human Resources Administration -1',                 3, 1],
            ['AMNG 229',  'إدارة الوقت',                              'Time Management',                                   1, 1],

            // ── Semester 2 ──
            ['AENG 124',  'لغة إنجليزية تخصصية (١)',                   'Specialised English Language -1',                   4, 2],
            ['AUHR 201',  'إدارة الموارد البشرية (٢)',                  'Human Resources Administration -2',                 3, 2],
            ['AMNG 227',  'مهارات التعامل مع الغير',                   'Skills of Dealing with Others',                     1, 2],
            ['AISL 102',  'ثقافة إسلامية (٢)',                         'Islamic Culture -2',                                2, 2],
            ['AUHR 202',  'احكام نظام العمل السعودي (١)',               'Provisions of Saudi Labor Law -1',                  4, 2],
            ['AMKT 102',  'مبادئ التسويق',                            'Principles of Marketing',                            4, 2],
            ['AUHR 303',  'الأنظمة الإدارية بالمملكة',                 'Administrative Systems in the Kingdom',             3, 2],

            // ── Semester 3 ──
            ['AENG 224',  'لغة إنجليزية تخصصية (٢)',                   'Specialised English Language -2',                   4, 3],
            ['AUHR 301',  'إدارة الموارد البشرية (٣)',                  'Human Resources Administration -3',                 3, 3],
            ['AUHR 302',  'احكام نظام العمل السعودي (٢)',               'Provisions of Saudi Labor Law -2',                  4, 3],
            ['ARAB 101',  'لغة عربية',                                 'Arabic Language',                                   2, 3],
            ['AUHR 501',  'إدارة شؤون الموظفين في الخدمة المدنية',      'Personnel Affairs in Civil Service',               5, 3],
            ['AUHR 402',  'فن الإقناع',                               'The Art of Persuasion',                              4, 3],

            // ── Semester 4 ──
            ['AUHR 401',  'الموارد البشرية في إدارة الجودة الشاملة',    'Human Resources in Comprehensive Quality Mgmt',    3, 4],
            ['AUHR 502',  'خدمة العملاء',                              'Customer Service',                                  3, 4],
            ['AUHR 601',  'السلوك التنظيمي في منظمات الأعمال',          'Organizational Behavior in Business Organization',  5, 4],
            ['AREL 101',  'مدخل إلى العلاقات العامة',                  'Introduction to Public Relations',                  4, 4],
            ['AETH 101',  'السلوك الوظيفي ومهارات الاتصال',            'Professional Ethics and Communication Skills',       2, 4],
            ['AUHR 602',  'مشروع التخرج (بحث)',                        'Graduation Project (Research)',                      5, 4],

            // ── Semester 5 ──
            ['AUHR 299',  'التدريب التعاوني -١',                       'Co-operative Training -1',                          2, 5],
            ['AUHR 300',  'التدريب التعاوني -٢',                       'Co-operative Training -2',                          2, 5],
        ];

        foreach ($subjects as [$code, $nameAr, $nameEn, $credits, $termNum]) {
            $subject = Subject::updateOrCreate(
                ['program_id' => $program->id, 'code' => $code],
                [
                    'term_id' => $terms[$termNum]->id,
                    'name_ar' => $nameAr,
                    'name_en' => $nameEn,
                    'credits' => $credits,
                    'status'  => 'active',
                ]
            );
            $terms[$termNum]->subjects()->syncWithoutDetaching([$subject->id]);
        }

        $this->command->info('✓ HR Program seeded: 5 terms, ' . count($subjects) . ' subjects');
    }
}
