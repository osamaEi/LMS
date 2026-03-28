<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            // Make wa_id nullable — web chats don't have a WhatsApp ID
            $table->string('wa_id')->nullable()->change();

            // Track where the conversation came from: website or WhatsApp
            $table->enum('channel', ['web', 'whatsapp'])->default('web')->after('wa_id');

            // For web chats — link to the logged-in user
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('channel');
        });
    }

    public function down(): void
    {
        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['channel', 'user_id']);
            $table->string('wa_id')->nullable(false)->change();
        });
    }
};
