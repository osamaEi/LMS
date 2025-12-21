<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('satisfaction_surveys')->onDelete('cascade');
            $table->text('question');
            $table->enum('type', ['rating', 'text', 'multiple_choice', 'yes_no'])->default('rating');
            $table->json('options')->nullable(); // For multiple choice questions
            $table->boolean('is_required')->default(true);
            $table->boolean('requires_comment_on_low_rating')->default(true); // Require comment if rating <= 2
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['survey_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_questions');
    }
};
