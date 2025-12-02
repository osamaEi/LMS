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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['assignment', 'quiz', 'midterm_exam', 'final_exam', 'project', 'participation']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('total_score', 5, 2);
            $table->decimal('earned_score', 5, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->comment('Weight percentage towards final grade');
            $table->datetime('due_date')->nullable();
            $table->datetime('submitted_at')->nullable();
            $table->datetime('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('feedback')->nullable();
            $table->enum('status', ['pending', 'submitted', 'graded', 'late'])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('subject_id');
            $table->index('student_id');
            $table->index('type');
            $table->index('status');
            $table->index('due_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
