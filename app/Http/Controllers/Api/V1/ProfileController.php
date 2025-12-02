<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompleteProfileRequest;
use App\Http\Resources\UserResource;
use App\Services\DocumentUploadService;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    protected DocumentUploadService $documentUploadService;

    public function __construct(DocumentUploadService $documentUploadService)
    {
        $this->documentUploadService = $documentUploadService;
    }

    /**
     * Complete or update user profile
     */
    public function completeProfile(CompleteProfileRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = $request->user();

            // Update user profile data
            $user->update([
                'name' => $request->name,
                'national_id' => $request->national_id,
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'email' => $request->email,
                'phone' => $request->phone,
                'type' => $request->type,
                'program_id' => $request->program_id,
                'date_of_register' => $request->date_of_register ?? now()->toDateString(),
                'is_terms' => $request->is_terms,
                'is_confirm_user' => $request->is_confirm_user,
                'profile_completed_at' => now(),
            ]);

            // Upload national ID document
            if ($request->hasFile('national_id_file')) {
                $this->documentUploadService->uploadDocument(
                    $user,
                    $request->file('national_id_file'),
                    'national_id'
                );
            }

            // Upload certificate document
            if ($request->hasFile('certificate_file')) {
                $this->documentUploadService->uploadDocument(
                    $user,
                    $request->file('certificate_file'),
                    'certificate'
                );
            }

            // Upload payment billing document
            if ($request->hasFile('payment_billing_file')) {
                $this->documentUploadService->uploadDocument(
                    $user,
                    $request->file('payment_billing_file'),
                    'payment_billing'
                );
            }

            DB::commit();

            return new UserResource($user->fresh(['program', 'documents']));

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to complete profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
