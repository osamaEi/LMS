<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Manual participation items a teacher records for a student inside one
     * subject (class discussion, presentation, …). These sit alongside the
     * quiz/homework buckets that make up the المشاركة column.
     */
    public function up(): void
    {
        Schema::create('participation_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');                                   // the "key"
            $table->decimal('grade', 6, 2)->default(0);
            $table->unsignedSmallInteger('max_grade')->default(10);
            $table->timestamps();

            $table->index(['student_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participation_marks');
    }
};
