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
        // Add name_en column
        Schema::table('terms', function (Blueprint $table) {
            $table->string('name_en')->after('name');
        });

        // Rename name to name_ar
        Schema::table('terms', function (Blueprint $table) {
            $table->renameColumn('name', 'name_ar');
        });

        // Drop track_id foreign key and column
        Schema::table('terms', function (Blueprint $table) {
            $table->dropForeign(['track_id']);
            $table->dropColumn('track_id');
        });

        // Make registration dates nullable (they already might be)
        Schema::table('terms', function (Blueprint $table) {
            $table->date('registration_start_date')->nullable()->change();
            $table->date('registration_end_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add track_id back
        Schema::table('terms', function (Blueprint $table) {
            $table->foreignId('track_id')->nullable()->after('program_id')->constrained('tracks')->nullOnDelete();
        });

        // Rename name_ar back to name
        Schema::table('terms', function (Blueprint $table) {
            $table->renameColumn('name_ar', 'name');
        });

        // Drop name_en
        Schema::table('terms', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });
    }
};
