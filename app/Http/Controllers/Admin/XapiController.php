<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Xapi\XapiClient;
use App\Services\Xapi\XapiService;

class XapiController extends Controller
{
    public function __construct(
        private XapiService $xapiService,
        private XapiClient $xapiClient
    ) {}

    public function index()
    {
        $stats = $this->xapiService->getStats();
        $enabled = config('xapi.enabled', false);
        $configured = !empty(config('xapi.lrs_endpoint'));

        return view('admin.xapi.index', compact('stats', 'enabled', 'configured'));
    }

    public function testConnection()
    {
        $result = $this->xapiClient->testConnection();
        return response()->json($result);
    }

    public function processPending()
    {
        $result = $this->xapiService->sendPending(100);
        return response()->json([
            'success' => true,
            'message' => "Processed: Sent {$result['sent']}, Failed {$result['failed']}",
        ]);
    }
}
