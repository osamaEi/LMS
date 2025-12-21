<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('satisfaction_surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['student', 'teacher', 'general'])->default('student');
            $table->enum('status', ['draft', 'active', 'closed'])->default('draft');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_mandatory')->default(true);
            $table->boolean('is_anonymous')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index(['subject_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satisfaction_surveys');
    }
};
