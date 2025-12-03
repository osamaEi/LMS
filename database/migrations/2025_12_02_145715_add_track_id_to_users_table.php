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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('track_id')->nullable()->after('program_id')->constrained('tracks')->nullOnDelete();
            $table->integer('current_term_number')->nullable()->after('track_id')->comment('رقم الربع الحالي للطالب (1-10)');
            $table->index('track_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['track_id']);
            $table->dropColumn(['track_id', 'current_term_number']);
        });
    }
};
