<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetAlghnamn733PasswordSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'alghnamn733@gmail.com')->firstOrFail();

        $user->forceFill([
            'password' => Hash::make('alghnamn733@gmail.com'),
            'remember_token' => Str::random(60),
        ])->save();

        $this->command?->info('Password updated for alghnamn733@gmail.com.');
    }
}
