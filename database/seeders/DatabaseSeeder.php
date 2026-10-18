<?php

namespace Database\Seeders;

use Database\Seeders\Student115Quiz62AccessSeeder;
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

           Student115Quiz62AccessSeeder::class,
        ]);
    }
}
