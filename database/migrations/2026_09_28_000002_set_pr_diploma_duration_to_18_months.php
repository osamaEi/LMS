<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Public Relations diploma runs a year and a half (18 months), not 10.
    public function up(): void
    {
        DB::table('programs')
            ->where('code', 'PR-DIPLOMA')
            ->update(['duration_months' => 18, 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('programs')
            ->where('code', 'PR-DIPLOMA')
            ->update(['duration_months' => 10, 'updated_at' => now()]);
    }
};
