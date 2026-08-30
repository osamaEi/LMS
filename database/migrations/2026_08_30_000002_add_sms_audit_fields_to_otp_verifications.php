<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('otp_verifications', function (Blueprint $table) {
            // Existing rows represent codes that reached the former send path.
            $table->string('status', 20)->default('sent')->after('type');
            $table->timestamp('sent_at')->nullable()->after('status');
            $table->string('provider_reference')->nullable()->after('sent_at');
            $table->text('failure_reason')->nullable()->after('provider_reference');
            $table->index(['phone', 'type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('otp_verifications', function (Blueprint $table) {
            $table->dropIndex(['phone', 'type', 'status']);
            $table->dropColumn(['status', 'sent_at', 'provider_reference', 'failure_reason']);
        });
    }
};
