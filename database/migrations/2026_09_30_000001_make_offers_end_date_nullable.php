<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->date('end_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        // open-ended offers need a date before the column can be NOT NULL again
        DB::table('offers')->whereNull('end_date')->update(['end_date' => now()->addYear()->toDateString()]);

        Schema::table('offers', function (Blueprint $table) {
            $table->date('end_date')->nullable(false)->change();
        });
    }
};
