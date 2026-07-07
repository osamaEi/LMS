<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            // Optional target class. When set, only students of this class see the
            // quiz. When null, the quiz stays visible to every class of the subject
            // (legacy behavior).
            $table->foreignId('class_id')->nullable()->after('subject_id')
                  ->constrained('program_classes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('class_id');
        });
    }
};
