<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')
                  ->constrained('whatsapp_conversations')
                  ->onDelete('cascade');
            $table->enum('direction', ['inbound', 'outbound']);
            $table->enum('sender_type', ['customer', 'ai', 'admin']);
            $table->text('content');
            $table->string('wa_message_id')->nullable()->unique(); // WhatsApp message ID for dedup
            $table->string('media_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
            $table->index(['conversation_id', 'id']); // for after_id polling queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_messages');
    }
};
