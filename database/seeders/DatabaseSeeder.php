<?php

namespace Database\Seeders;

use Database\Seeders\ProgramCSSeeder;
use Database\Seeders\ProgramHRSeeder;
use Database\Seeders\ProgramMKTSeeder;
use Database\Seeders\PublicRelationsDiplomaSeeder;
use Database\Seeders\PublicRelationsSubjectsSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProgramMKTSeeder::class,
            ProgramHRSeeder::class,
            PublicRelationsDiplomaSeeder::class,
            PublicRelationsSubjectsSeeder::class,
        ]);
    }
}
