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
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('session_number');
            $table->enum('type', ['live_zoom', 'recorded_video']);
            $table->datetime('scheduled_at')->nullable();
            $table->integer('duration_minutes')->nullable();

            // For Zoom sessions
            $table->string('zoom_meeting_id')->nullable();
            $table->text('zoom_start_url')->nullable();
            $table->text('zoom_join_url')->nullable();
            $table->string('zoom_password')->nullable();
            $table->datetime('started_at')->nullable();
            $table->datetime('ended_at')->nullable();

            // For recorded videos
            $table->text('video_path')->nullable();
            $table->text('video_url')->nullable();
            $table->enum('video_platform', ['local', 'youtube', 'vimeo'])->default('local');
            $table->integer('video_duration')->nullable();
            $table->bigInteger('video_size')->nullable();

            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');
            $table->boolean('is_mandatory')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('subject_id');
            $table->index('type');
            $table->index('status');
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
