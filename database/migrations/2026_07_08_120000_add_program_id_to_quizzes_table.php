<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A quiz targets EITHER a subject (diploma programs) OR a program
        // (course / english programs) — always with a class. Make subject_id
        // nullable and add a nullable program_id alongside it.
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('subject_id')
                  ->constrained('programs')->nullOnDelete();
        });

        // subject_id was NOT NULL; relax it so program-targeted quizzes are valid.
        // Raw SQL keeps the existing FK/index intact (doctrine/dbal not required).
        DB::statement('ALTER TABLE quizzes MODIFY subject_id BIGINT UNSIGNED NULL');
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
        });

        // Restore NOT NULL. Any program-only rows must be cleared first or this
        // will fail — acceptable for a down migration.
        DB::statement('ALTER TABLE quizzes MODIFY subject_id BIGINT UNSIGNED NOT NULL');
    }
};
