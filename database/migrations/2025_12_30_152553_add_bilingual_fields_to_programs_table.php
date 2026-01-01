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
        // First add new columns
        Schema::table('programs', function (Blueprint $table) {
            $table->string('name_en')->after('name');
            $table->text('description_en')->nullable()->after('description');
        });

        // Then rename existing columns
        Schema::table('programs', function (Blueprint $table) {
            $table->renameColumn('name', 'name_ar');
            $table->renameColumn('description', 'description_ar');
        });

        // Drop type column and index
        Schema::table('programs', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropColumn('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First restore type column
        Schema::table('programs', function (Blueprint $table) {
            $table->enum('type', ['diploma', 'training'])->default('diploma')->after('code');
            $table->index('type');
        });

        // Rename columns back
        Schema::table('programs', function (Blueprint $table) {
            $table->renameColumn('name_ar', 'name');
            $table->renameColumn('description_ar', 'description');
        });

        // Drop added columns
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'description_en']);
        });
    }
};
