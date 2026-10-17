<?php

namespace Database\Seeders;

use Database\Seeders\MajedGradesSeeder;
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

           MajedGradesSeeder::class,
        ]);
    }
}
