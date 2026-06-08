<?php

namespace Database\Seeders;

use Database\Seeders\PermissionSeeder;
use Database\Seeders\Student29DemoSeeder;
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

           PermissionSeeder::class,
           Student29DemoSeeder::class,
        ]);
    }
}
