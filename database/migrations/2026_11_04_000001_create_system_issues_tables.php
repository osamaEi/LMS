<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Internal tracker for system problems, used by admins only.
        Schema::create('system_issues', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('voice_path')->nullable();
            $table->string('status', 30)->default('open')->index();
            $table->string('priority', 20)->default('medium')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Links from an issue to something in the system (student, teacher,
        // session, ...) or to any URL. The label is a snapshot so a tag still
        // reads correctly after the linked record is renamed or deleted.
        Schema::create('system_issue_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_issue_id')->constrained('system_issues')->cascadeOnDelete();
            $table->string('type', 30);
            $table->unsignedBigInteger('taggable_id')->nullable();
            $table->string('label');
            $table->string('url', 2048)->nullable();
            $table->timestamps();

            $table->index(['type', 'taggable_id']);
        });

        Schema::create('system_issue_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_issue_id')->constrained('system_issues')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('system_issue_comments')->cascadeOnDelete();
            // 'comment' = written by a user, 'status' = automatic status-change entry.
            $table->string('type', 20)->default('comment');
            $table->text('body')->nullable();
            $table->string('voice_path')->nullable();
            $table->timestamp('edited_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_issue_comments');
        Schema::dropIfExists('system_issue_tags');
        Schema::dropIfExists('system_issues');
    }
};
