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
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('subject_id')
                  ->constrained('programs')->nullOnDelete();
            $table->unsignedBigInteger('subject_id')->nullable()->change();
        });

        Schema::table('subject_files', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('subject_id')
                  ->constrained('programs')->nullOnDelete();
            $table->unsignedBigInteger('subject_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropColumn('program_id');
            $table->unsignedBigInteger('subject_id')->nullable(false)->change();
        });

        Schema::table('subject_files', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropColumn('program_id');
            $table->unsignedBigInteger('subject_id')->nullable(false)->change();
        });
    }
};
