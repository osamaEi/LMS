<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Drop global unique if it still exists (legacy)
            try { $table->dropUnique(['code']); } catch (\Throwable $e) {}
            $table->unique(['program_id', 'code'], 'subjects_program_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropUnique('subjects_program_code_unique');
        });
    }
};
