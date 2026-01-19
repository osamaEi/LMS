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
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->string('zoom_recording_id')->nullable()->after('zoom_password');
            $table->text('recording_download_url')->nullable()->after('zoom_recording_id');
            $table->string('recording_file_path')->nullable()->after('recording_download_url');
            $table->string('recording_thumbnail')->nullable()->after('recording_file_path');
            $table->enum('recording_status', ['pending', 'processing', 'completed', 'failed'])->default('pending')->after('recording_thumbnail');
            $table->unsignedBigInteger('recording_size')->nullable()->comment('Size in bytes')->after('recording_status');
            $table->integer('recording_duration')->nullable()->comment('Duration in seconds')->after('recording_size');
            $table->timestamp('recording_synced_at')->nullable()->after('recording_duration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_sessions', function (Blueprint $table) {
            $table->dropColumn([
                'zoom_recording_id',
                'recording_download_url',
                'recording_file_path',
                'recording_thumbnail',
                'recording_status',
                'recording_size',
                'recording_duration',
                'recording_synced_at'
            ]);
        });
    }
};
