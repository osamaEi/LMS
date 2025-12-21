<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('teaching_quality')->unsigned(); // 1-5
            $table->tinyInteger('communication')->unsigned(); // 1-5
            $table->tinyInteger('punctuality')->unsigned(); // 1-5
            $table->tinyInteger('content_delivery')->unsigned(); // 1-5
            $table->tinyInteger('overall_rating')->unsigned(); // 1-5
            $table->text('comment')->nullable();
            $table->text('improvement_suggestions')->nullable();
            $table->boolean('is_anonymous')->default(true);
            $table->boolean('is_approved')->default(false); // Admin approval for public display
            $table->timestamps();

            $table->unique(['teacher_id', 'student_id', 'subject_id'], 'unique_rating');
            $table->index(['teacher_id', 'is_approved']);
            $table->index(['subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_ratings');
    }
};
