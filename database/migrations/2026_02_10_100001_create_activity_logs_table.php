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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 100);           // 'login', 'logout', 'view_session', 'submit_quiz', etc.
            $table->string('action_category', 50);   // 'auth', 'content', 'assessment', 'attendance', 'admin', 'communication'
            $table->string('loggable_type')->nullable(); // Polymorphic: App\Models\Session, App\Models\Quiz, etc.
            $table->unsignedBigInteger('loggable_id')->nullable();
            $table->json('properties')->nullable();   // Extra metadata (old/new values, scores, durations, etc.)
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id', 100)->nullable(); // Browser session ID for grouping
            $table->boolean('xapi_sent')->default(false);  // Flag: has this been sent as xAPI statement?
            $table->timestamp('xapi_sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'action']);
            $table->index(['loggable_type', 'loggable_id']);
            $table->index(['action_category', 'created_at']);
            $table->index('xapi_sent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
