<?php

namespace Database\Seeders;

use Database\Seeders\Student421TeacherReportDiagnosticSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
           //  ProgramMKTSeeder::class,
           //  ProgramHRSeeder::class,
           //  PublicRelationsDiplomaSeeder::class,
           //  PublicRelationsSubjectsSeeder::class,

           Student421TeacherReportDiagnosticSeeder::class,
        ]);
    }
}
