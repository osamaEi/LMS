<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessWhatsAppAiResponse;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * GET /webhooks/whatsapp
     *
     * Meta webhook verification challenge.
     * Called once when registering the webhook URL in Meta App Dashboard.
     */
    public function verify(Request $request): Response|string
    {
        $mode      = $request->get('hub_mode');
        $token     = $request->get('hub_verify_token');
        $challenge = $request->get('hub_challenge');

        if ($mode === 'subscribe' && $token === config('services.whatsapp.verify_token')) {
            Log::info('WhatsApp webhook verified successfully');
            return response($challenge, 200);
        }

        Log::warning('WhatsApp webhook verification failed', ['token' => $token]);
        return response('Forbidden', 403);
    }

    /**
     * POST /webhooks/whatsapp
     *
     * Handles incoming WhatsApp messages and status updates from Meta.
     * Must always return 200 quickly — heavy processing is handled by a queued job.
     */
    public function handle(Request $request): Response
    {
        $payload = $request->all();

        Log::debug('WhatsApp webhook received', ['payload' => $payload]);

        try {
            $entry   = $payload['entry'][0] ?? null;
            $changes = $entry['changes'][0] ?? null;
            $value   = $changes['value'] ?? null;
            $messages = $value['messages'] ?? null;

            // Ignore non-message webhooks (delivery receipts, status updates, etc.)
            if (empty($messages)) {
                return response('OK', 200);
            }

            $message      = $messages[0];
            $waMessageId  = $message['id'] ?? null;
            $waId         = $message['from'] ?? null;
            $messageType  = $message['type'] ?? 'text';

            // Only handle text messages for now
            if ($messageType !== 'text') {
                return response('OK', 200);
            }

            $text         = $message['text']['body'] ?? '';
            $customerName = $value['contacts'][0]['profile']['name'] ?? null;

            if (empty($waId) || empty($text)) {
                return response('OK', 200);
            }

            // Idempotency: skip already-processed messages (Meta may resend)
            if ($waMessageId && WhatsappMessage::where('wa_message_id', $waMessageId)->exists()) {
                return response('OK', 200);
            }

            // Format phone number for display
            $phoneNumber = '+' . ltrim($waId, '+');

            // Find or create the conversation for this WhatsApp number
            $conversation = WhatsappConversation::firstOrCreate(
                ['wa_id' => $waId],
                [
                    'customer_name' => $customerName,
                    'phone_number'  => $phoneNumber,
                    'status'        => 'open',
                ]
            );

            // Update customer name if provided and not set yet
            if ($customerName && ! $conversation->customer_name) {
                $conversation->update(['customer_name' => $customerName]);
            }

            // If conversation was closed, reopen it
            if ($conversation->isClosed()) {
                $conversation->update(['status' => 'open']);
            }

            // Save the inbound message
            WhatsappMessage::create([
                'conversation_id' => $conversation->id,
                'direction'       => 'inbound',
                'sender_type'     => 'customer',
                'content'         => $text,
                'wa_message_id'   => $waMessageId,
                'is_read'         => false,
            ]);

            // Update conversation counters
            $conversation->update([
                'last_message_at'    => now(),
                'unread_admin_count' => $conversation->unread_admin_count + 1,
                'status'             => 'open',
            ]);

            // Dispatch AI response job to queue
            ProcessWhatsAppAiResponse::dispatch($conversation, $text);

        } catch (\Exception $e) {
            Log::error('WhatsApp webhook processing error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Always return 200 to Meta, even on errors, to prevent webhook retries
        return response('OK', 200);
    }
}
