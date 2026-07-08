<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // When set, the teacher has released this attempt's grade/answers to
            // the student. Until then the student sees only "awaiting review".
            $table->timestamp('results_released_at')->nullable()->after('passed');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn('results_released_at');
        });
    }
};
