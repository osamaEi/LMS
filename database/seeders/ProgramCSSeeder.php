<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramCSSeeder extends Seeder
{
    public function run(): void
    {
        $program = Program::where('code', 'DIP-CS-001')->first();
        if (!$program) {
            $this->command->error('Program DIP-CS-001 not found. Run ProgramSeeder first.');
            return;
        }

        // Clear existing terms & subjects for this program
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        $termIds = Term::where('program_id', $program->id)->pluck('id');
        DB::table('term_subject')->whereIn('term_id', $termIds)->delete();
        Subject::where('program_id', $program->id)->forceDelete();
        Term::where('program_id', $program->id)->forceDelete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ── Semester definitions ──────────────────────────────────
        $semesters = [
            1 => ['ar' => 'الفصل التدريبي الأول',   'en' => '1st Semester'],
            2 => ['ar' => 'الفصل التدريبي الثاني',   'en' => '2nd Semester'],
            3 => ['ar' => 'الفصل التدريبي الثالث',   'en' => '3rd Semester'],
            4 => ['ar' => 'الفصل التدريبي الرابع',   'en' => '4th Semester'],
            5 => ['ar' => 'الفصل التدريبي الخامس',   'en' => '5th Semester'],
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

        // ── Subjects per semester ─────────────────────────────────
        // [code, name_ar, name_en, credits, semester_number]
        $subjects = [
            // ── Semester 1 ──
            ['ISLM 101', 'الدراسات الإسلامية',                   'Islamic Studies',                        2, 1],
            ['VOCA 101', 'التوجيه المهني والتميز',                'Vocational Guidance & Excellence',       2, 1],
            ['MATH 101', 'الرياضيات',                             'Mathematics',                            3, 1],
            ['ICMT 121', 'تجميع الحاسب وتشغيله',                  'Computer Assembly and Operating',        4, 1],
            ['PHYS 101', 'الفيزياء',                              'Physics',                                3, 1],
            ['ENGL 101', 'لغة إنجليزية (١)',                      'English Language -1',                    3, 1],
            ['IPRG 101', 'الخوارزميات والمنطق',                   'Logic and Algorithms',                   3, 1],

            // ── Semester 2 ──
            ['ENGL 102', 'لغة إنجليزية (٢)',                      'English Language -2',                    3, 2],
            ['ICMT 101', 'مقدمة تطبيقات الحاسب',                  'Introduction to Computer Applications',  2, 2],
            ['IPRG 111', 'أساسيات برمجة الحاسب',                  'Programming Fundamentals',               4, 2],
            ['ARAB 101', 'الكتابة الفنية',                        'Technical Writing',                      2, 2],
            ['IPRG 131', 'مبادئ برمجة صفحات الانترنت',            'Web Programming Fundamentals',           4, 2],
            ['IPRG 121', 'مبادئ قواعد البيانات',                  'Database Fundamentals',                  4, 2],

            // ── Semester 3 ──
            ['ENGL 103', 'لغة إنجليزية (٣)',                      'English Language -3',                    3, 3],
            ['ICMT 102', 'تطبيقات الحاسب المتقدمة',               'Advanced Computer Applications',         2, 3],
            ['IPRG 212', 'برمجة الحاسب',                          'Computer Programming',                   4, 3],
            ['IPRG 232', 'برمجة الانترنت',                        'Web Programming',                        4, 3],
            ['IPRG 222', 'برمجة قواعد البيانات',                  'Database Programming',                   4, 3],
            ['IPRG 241', 'هندسة البرمجيات',                       'Software Engineering',                   3, 3],

            // ── Semester 4 ──
            ['LEAS 101', 'مهارات التعلم',                         'Learning Skills',                        2, 4],
            ['ETHS 101', 'السلوك الوظيفي ومهارات الاتصال',         'Professional Ethics & Comm. Skills',     2, 4],
            ['ENGL 204', 'لغة إنجليزية (٤)',                      'English Language -4',                    3, 4],
            ['IPRG 234', 'تقنيات الانترنت المتقدمة',              'Advanced Internet Technologies',         4, 4],
            ['ICMT 280', 'التأهيل للشهادات الاحترافية',           'Preparation for Professional Certificates', 1, 4],
            ['IPRG 251', 'برمجة الأجهزة الذكية',                  'Smart Devices Programming',              4, 4],
            ['IPRG 295', 'مشروع',                                 'Project',                                4, 4],

            // ── Semester 5 ──
            ['IPRG 298', 'التدريب التعاوني -١',                   'Co-operative Training -1',               2, 5],
            ['IPRG 299', 'التدريب التعاوني -٢',                   'Co-operative Training -2',               2, 5],
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

            // Link to term via pivot
            $terms[$termNum]->subjects()->syncWithoutDetaching([$subject->id]);
        }

        $this->command->info('✓ CS Program seeded: 5 terms, ' . count($subjects) . ' subjects');
    }
}
