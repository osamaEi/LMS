<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\TamaraPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TamaraWebhookController extends Controller
{
    protected TamaraPaymentService $tamaraService;

    public function __construct(TamaraPaymentService $tamaraService)
    {
        $this->tamaraService = $tamaraService;
    }

    /**
     * Handle Tamara webhook
     */
    public function handle(Request $request)
    {
        try {
            $payload = $request->all();

            Log::info('Tamara Webhook Received', [
                'payload' => $payload,
                'headers' => $request->headers->all(),
            ]);

            // Verify webhook signature if available
            $signature = $request->header('X-Tamara-Signature');
            if ($signature && !$this->tamaraService->verifyWebhookSignature($payload, $signature)) {
                Log::warning('Tamara Webhook: Invalid signature');
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            // Handle the webhook
            $this->tamaraService->handleWebhook($payload);

            return response()->json(['success' => true], 200);
        } catch (\Exception $e) {
            Log::error('Tamara Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
