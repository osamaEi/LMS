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
        // Add title_en column
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->string('title_en')->after('title');
        });

        // Rename title to title_ar
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->renameColumn('title', 'title_ar');
        });

        // Add description_en column
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->text('description_en')->nullable()->after('description');
        });

        // Rename description to description_ar
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->renameColumn('description', 'description_ar');
        });

        // Drop status and is_mandatory columns
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropColumn(['status', 'is_mandatory']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back status and is_mandatory
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->string('status')->default('scheduled');
            $table->boolean('is_mandatory')->default(false);
        });

        // Rename description_ar back to description
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->renameColumn('description_ar', 'description');
        });

        // Drop description_en
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropColumn('description_en');
        });

        // Rename title_ar back to title
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->renameColumn('title_ar', 'title');
        });

        // Drop title_en
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropColumn('title_en');
        });
    }
};
