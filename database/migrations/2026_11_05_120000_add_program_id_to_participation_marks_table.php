<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Courses and English programs are graded per program, not per subject, so
     * a manual mark belongs to either a subject or a program.
     */
    public function up(): void
    {
        Schema::table('participation_marks', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->change();
            $table->foreignId('program_id')->nullable()->after('subject_id')
                ->constrained('programs')->cascadeOnDelete();
            $table->index(['student_id', 'program_id']);
        });
    }

    public function down(): void
    {
        Schema::table('participation_marks', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'program_id']);
            $table->dropConstrainedForeignId('program_id');
        });
    }
};
