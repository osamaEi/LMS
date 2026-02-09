<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('session_files', function (Blueprint $table) {
            $table->string('alt_text_ar')->nullable()->after('description');
            $table->string('alt_text_en')->nullable()->after('alt_text_ar');
            $table->text('caption_ar')->nullable();
            $table->text('caption_en')->nullable();
            $table->string('accessibility_status', 20)->default('unchecked');
        });

        Schema::table('class_sessions', function (Blueprint $table) {
            $table->text('video_caption_url')->nullable()->after('video_size');
            $table->text('video_transcript_ar')->nullable();
            $table->text('video_transcript_en')->nullable();
            $table->string('accessibility_status', 20)->default('unchecked');
        });
    }

    public function down(): void
    {
        Schema::table('session_files', function (Blueprint $table) {
            $table->dropColumn(['alt_text_ar', 'alt_text_en', 'caption_ar', 'caption_en', 'accessibility_status']);
        });

        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropColumn(['video_caption_url', 'video_transcript_ar', 'video_transcript_en', 'accessibility_status']);
        });
    }
};
