<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('wa_id')->unique(); // WhatsApp customer phone e.g. 966501234567
            $table->string('customer_name')->nullable();
            $table->string('phone_number');
            $table->enum('status', ['open', 'closed', 'ai_responding'])->default('open');
            $table->timestamp('last_message_at')->nullable();
            $table->unsignedInteger('unread_admin_count')->default(0);
            $table->timestamps();
            $table->index('wa_id');
            $table->index(['status', 'last_message_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
};
