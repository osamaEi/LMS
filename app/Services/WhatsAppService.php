<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private string $phoneNumberId;
    private string $accessToken;
    private string $apiUrl;

    public function __construct()
    {
        $this->phoneNumberId = config('services.whatsapp.phone_number_id', '');
        $this->accessToken   = config('services.whatsapp.access_token', '');
        $this->apiUrl        = config('services.whatsapp.api_url', 'https://graph.facebook.com/v19.0');
    }

    /**
     * Send a text message to a WhatsApp number.
     *
     * @param  string  $to  Phone number in international format e.g. 966501234567
     * @param  string  $message  The message text
     * @return array Response from Meta API
     * @throws Exception
     */
    public function sendTextMessage(string $to, string $message): array
    {
        $response = Http::withToken($this->accessToken)
            ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'recipient_type'    => 'individual',
                'to'                => $to,
                'type'              => 'text',
                'text'              => ['body' => $message],
            ]);

        if (! $response->successful()) {
            Log::error('WhatsApp send message failed', [
                'to'     => $to,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            throw new Exception('WhatsApp API error: ' . $response->status() . ' - ' . $response->body());
        }

        Log::info('WhatsApp message sent', [
            'to'         => $to,
            'message_id' => $response->json('messages.0.id'),
        ]);

        return $response->json();
    }

    /**
     * Mark an incoming message as read (shows double blue ticks to the customer).
     *
     * @param  string  $waMessageId  WhatsApp message ID
     */
    public function markMessageRead(string $waMessageId): void
    {
        try {
            Http::withToken($this->accessToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'status'            => 'read',
                    'message_id'        => $waMessageId,
                ]);
        } catch (Exception $e) {
            Log::warning('WhatsApp mark read failed', [
                'message_id' => $waMessageId,
                'error'      => $e->getMessage(),
            ]);
        }
    }
}
