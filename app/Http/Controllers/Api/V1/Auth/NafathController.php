<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\InitiateNafathRequest;
use App\Http\Resources\NafathTransactionResource;
use App\Models\User;
use App\Services\NafathService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NafathController extends Controller
{
    protected NafathService $nafathService;

    public function __construct(NafathService $nafathService)
    {
        $this->nafathService = $nafathService;
    }

    /**
     * Initiate Nafath OpenAccount verification
     */
    public function initiate(InitiateNafathRequest $request): JsonResponse
    {
        try {
            $nationalId = $request->national_id;

            // Find user by national ID
            $user = User::where('national_id', $nationalId)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User with this National ID not found. Please register first.',
                ], 404);
            }

            // Check if phone is verified
            if (!$user->phone_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please verify your phone number first.',
                ], 400);
            }

            // Check if already verified
            if ($user->nafath_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'National ID already verified via Nafath.',
                ], 400);
            }

            // Initiate Nafath verification
            $transaction = $this->nafathService->initiateOpenAccount($nationalId, $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Nafath verification initiated. Please approve on your Nafath app.',
                'data' => [
                    'transaction' => new NafathTransactionResource($transaction),
                    'instructions' => 'Open your Nafath mobile app and approve the verification request. Then poll the status using the transaction_id.',
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate Nafath verification. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Poll Nafath transaction status
     */
    public function poll(Request $request, string $transactionId): JsonResponse
    {
        try {
            // Get transaction
            $transaction = $this->nafathService->getTransaction($transactionId);

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found.',
                ], 404);
            }

            // Poll status
            $transaction = $this->nafathService->pollStatus($transactionId);

            $message = match ($transaction->status) {
                'pending' => 'Verification pending. Please approve on your Nafath app.',
                'approved' => 'National ID verified successfully via Nafath.',
                'rejected' => 'Nafath verification was rejected.',
                'expired' => 'Nafath verification request has expired. Please try again.',
                default => 'Unknown status.',
            };

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'transaction' => new NafathTransactionResource($transaction),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to poll transaction status. Please try again.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
}
