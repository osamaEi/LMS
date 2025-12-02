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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('session_id')->constrained('class_sessions')->cascadeOnDelete();
            $table->boolean('attended')->default(false);
            $table->datetime('joined_at')->nullable();
            $table->datetime('left_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->decimal('watch_percentage', 5, 2)->default(0);
            $table->boolean('video_completed')->default(false);
            $table->decimal('participation_score', 5, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('student_id');
            $table->index('session_id');
            $table->index('attended');
            $table->unique(['student_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
