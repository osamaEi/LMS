<?php

namespace App\Http\Controllers\Mock;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NafathMockController extends Controller
{
    /**
     * Mock MFA Request - Create a new authentication request
     */
    public function createRequest(Request $request)
    {
        // Generate a mock transaction ID
        $transId = Str::uuid()->toString();

        // Generate a random number (like Nafath does)
        $random = rand(10, 99);

        // Return success response
        return response()->json([
            'transId' => $transId,
            'random' => $random,
            'status' => 'WAITING',
            'message' => 'MFA request created successfully (MOCK)',
        ], 200);
    }

    /**
     * Mock MFA Status - Check authentication request status
     */
    public function getStatus(Request $request)
    {
        // Always return COMPLETED for immediate approval (testing mode)
        // Change this to 'WAITING', 'REJECTED', or 'EXPIRED' to test different scenarios
        $status = 'COMPLETED';

        return response()->json([
            'transId' => $request->route('transId'),
            'status' => $status,
            'message' => 'Status retrieved successfully (MOCK)',
        ], 200);
    }

    /**
     * Mock JWK Endpoint
     */
    public function getJwk()
    {
        return response()->json([
            'keys' => [
                [
                    'kty' => 'RSA',
                    'use' => 'sig',
                    'kid' => 'mock-key-id',
                    'n' => 'mock-n-value',
                    'e' => 'AQAB',
                ]
            ]
        ], 200);
    }
}
