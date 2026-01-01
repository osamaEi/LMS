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
        Schema::table('subjects', function (Blueprint $table) {
            $table->string('name_en')->after('name');
        });

        // Rename name to name_ar
        Schema::table('subjects', function (Blueprint $table) {
            $table->renameColumn('name', 'name_ar');
        });

        // Add description_en column
        Schema::table('subjects', function (Blueprint $table) {
            $table->text('description_en')->nullable()->after('description');
        });

        // Rename description to description_ar
        Schema::table('subjects', function (Blueprint $table) {
            $table->renameColumn('description', 'description_ar');
        });

        // Drop max_students and total_hours columns
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn(['max_students', 'total_hours']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back max_students and total_hours
        Schema::table('subjects', function (Blueprint $table) {
            $table->integer('max_students')->nullable();
            $table->integer('total_hours')->nullable();
        });

        // Rename description_ar back to description
        Schema::table('subjects', function (Blueprint $table) {
            $table->renameColumn('description_ar', 'description');
        });

        // Drop description_en
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('description_en');
        });

        // Rename name_ar back to name
        Schema::table('subjects', function (Blueprint $table) {
            $table->renameColumn('name_ar', 'name');
        });

        // Drop name_en
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });
    }
};
