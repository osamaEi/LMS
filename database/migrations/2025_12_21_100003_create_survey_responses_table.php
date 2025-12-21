<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_id')->constrained('satisfaction_surveys')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('question_id')->constrained('survey_questions')->onDelete('cascade');
            $table->tinyInteger('rating')->nullable(); // 1-5 scale
            $table->text('answer')->nullable(); // For text answers or comments
            $table->string('selected_option')->nullable(); // For multiple choice
            $table->timestamps();

            $table->index(['survey_id', 'user_id']);
            $table->index(['question_id', 'rating']);
            $table->unique(['survey_id', 'user_id', 'question_id'], 'unique_response');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
