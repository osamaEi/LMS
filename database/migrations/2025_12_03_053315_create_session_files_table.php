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
        Schema::create('session_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('class_sessions')->cascadeOnDelete();
            $table->enum('type', ['video', 'pdf', 'zoom']); // نوع المحتوى
            $table->string('title'); // عنوان الملف/الفيديو
            $table->text('description')->nullable(); // وصف
            $table->integer('order')->default(0); // ترتيب العرض

            // For videos
            $table->text('video_path')->nullable();
            $table->text('video_url')->nullable();
            $table->enum('video_platform', ['local', 'youtube', 'vimeo'])->nullable();
            $table->integer('video_duration')->nullable()->comment('بالدقائق');
            $table->bigInteger('video_size')->nullable()->comment('بالبايت');

            // For PDFs
            $table->text('file_path')->nullable();
            $table->text('file_url')->nullable();
            $table->bigInteger('file_size')->nullable()->comment('بالبايت');

            // For Zoom
            $table->string('zoom_meeting_id')->nullable();
            $table->text('zoom_start_url')->nullable();
            $table->text('zoom_join_url')->nullable();
            $table->string('zoom_password')->nullable();
            $table->datetime('zoom_scheduled_at')->nullable();
            $table->integer('zoom_duration')->nullable()->comment('بالدقائق');

            $table->boolean('is_mandatory')->default(false); // إلزامي؟
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('session_id');
            $table->index('type');
            $table->index(['session_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_files');
    }
};
