<?php

namespace Database\Seeders;

use Database\Seeders\MajedFinalQuizSeeder;

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

           MajedFinalQuizSeeder::class,
        ]);
    }
}
