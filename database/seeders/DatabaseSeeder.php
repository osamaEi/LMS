<?php

namespace Database\Seeders;

use Database\Seeders\Student303Quiz62DiagnosticSeeder;
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

           Student303Quiz62DiagnosticSeeder::class,
        ]);
    }
}
