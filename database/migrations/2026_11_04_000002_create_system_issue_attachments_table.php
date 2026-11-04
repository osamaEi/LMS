<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Files/images/videos attached to an issue or to one of its comments.
        // Stored on the private "local" disk and served through an admin-only route.
        Schema::create('system_issue_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_issue_id')->constrained('system_issues')->cascadeOnDelete();
            $table->foreignId('comment_id')->nullable()->constrained('system_issue_comments')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('path');
            $table->string('original_name');
            $table->string('mime', 150)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_issue_attachments');
    }
};
