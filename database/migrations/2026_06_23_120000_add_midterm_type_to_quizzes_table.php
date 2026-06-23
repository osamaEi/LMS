<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE quizzes MODIFY COLUMN type ENUM('quiz','midterm','exam','homework','paper') NOT NULL DEFAULT 'quiz'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE quizzes MODIFY COLUMN type ENUM('quiz','exam','homework','paper') NOT NULL DEFAULT 'quiz'");
    }
};
