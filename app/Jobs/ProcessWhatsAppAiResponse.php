<?php

namespace App\Jobs;

use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use App\Services\AiChatService;
use App\Services\WhatsAppService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppAiResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 60;

    public function __construct(
        protected WhatsappConversation $conversation,
        protected string $customerMessage
    ) {}

    /**
     * Execute the job: generate AI reply via Claude and send it to the customer on WhatsApp.
     */
    public function handle(AiChatService $aiService, WhatsAppService $waService): void
    {
        try {
            // Mark conversation as AI responding so the admin UI shows a spinner
            $this->conversation->update(['status' => 'ai_responding']);

            // Generate response from Claude
            $aiText = $this->stripFormatting($aiService->sendMessage($this->conversation, $this->customerMessage));

            // Only send via WhatsApp API for WhatsApp conversations (not web chats)
            if ($this->conversation->isWhatsAppChat()) {
                $waService->sendTextMessage($this->conversation->wa_id, $aiText);
            }

            // Persist the outbound AI message in the database
            WhatsappMessage::create([
                'conversation_id' => $this->conversation->id,
                'direction'       => 'outbound',
                'sender_type'     => 'ai',
                'content'         => $aiText,
                'is_read'         => false,
            ]);

            // Update conversation state
            $this->conversation->update([
                'status'              => 'open',
                'last_message_at'     => now(),
                'unread_admin_count'  => $this->conversation->unread_admin_count + 1,
            ]);

            Log::info('AI WhatsApp response sent', [
                'conversation_id' => $this->conversation->id,
                'wa_id'           => $this->conversation->wa_id,
            ]);

        } catch (Exception $e) {
            Log::error('AI WhatsApp response failed', [
                'conversation_id' => $this->conversation->id,
                'error'           => $e->getMessage(),
            ]);

            // Reset status so admin UI doesn't get stuck on "ai_responding"
            $this->conversation->update(['status' => 'open']);

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Remove emoji, markdown bold/headers from AI response text.
     */
    private function stripFormatting(string $text): string
    {
        // Remove markdown bold (**text** or __text__)
        $text = preg_replace('/\*\*(.+?)\*\*/u', '$1', $text);
        $text = preg_replace('/__(.+?)__/u', '$1', $text);

        // Remove markdown headers (## Title)
        $text = preg_replace('/^#{1,6}\s*/mu', '', $text);

        // Remove markdown single stars used as bullet (*item)
        $text = preg_replace('/^\*\s+/mu', '- ', $text);

        // Remove emoji using Unicode ranges
        $text = preg_replace('/[\x{1F000}-\x{1FFFF}]/u', '', $text);
        $text = preg_replace('/[\x{2600}-\x{27BF}]/u', '', $text);
        $text = preg_replace('/[\x{FE00}-\x{FEFF}]/u', '', $text);
        $text = preg_replace('/[\x{1F900}-\x{1F9FF}]/u', '', $text);
        $text = preg_replace('/[\x{1FA00}-\x{1FA6F}]/u', '', $text);
        $text = preg_replace('/[\x{1FA70}-\x{1FAFF}]/u', '', $text);

        // Clean up extra blank lines
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

    /**
     * Handle permanent job failure after all retries exhausted.
     */
    public function failed(Exception $exception): void
    {
        Log::error('AI WhatsApp job permanently failed', [
            'conversation_id' => $this->conversation->id,
            'error'           => $exception->getMessage(),
        ]);

        $this->conversation->update(['status' => 'open']);
    }
}
