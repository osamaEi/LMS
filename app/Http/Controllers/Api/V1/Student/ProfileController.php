<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * GET /api/v1/student/profile
     * Get full student profile
     */
    public function show(Request $request)
    {
        $user = $request->user()->load([
            'program.terms',
            'track',
        ]);

        return response()->json([
            'success' => true,
            'data'    => new UserResource($user),
        ]);
    }

    /**
     * PUT /api/v1/student/profile
     * Update editable profile fields
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'          => 'sometimes|string|max:255',
            'phone'         => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
            'email'         => 'sometimes|email|unique:users,email,' . $user->id,
            'bio'           => 'sometimes|nullable|string|max:1000',
            'gender'        => 'sometimes|nullable|in:male,female',
            'date_of_birth' => 'sometimes|nullable|date|before:today',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'data'    => new UserResource($user->load(['program.terms', 'track'])),
        ]);
    }

    /**
     * POST /api/v1/student/profile/photo
     * Set profile photo from a URL string
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|string|max:2048',
        ]);

        $user = $request->user();

        $user->update(['profile_photo' => $request->photo]);

        return response()->json([
            'success'       => true,
            'message'       => 'تم تحديث الصورة الشخصية بنجاح',
            'profile_photo' => $request->photo,
        ]);
    }

    /**
     * POST /api/v1/student/profile/change-password
     * Change password (requires current password)
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'كلمة المرور الحالية غير صحيحة',
                'errors'  => ['current_password' => ['كلمة المرور الحالية غير صحيحة']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Revoke all other tokens for security
        $user->tokens()->where('id', '!=', $request->user()->currentAccessToken()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير كلمة المرور بنجاح',
        ]);
    }

    /**
     * DELETE /api/v1/student/profile
     * Delete the authenticated account (soft delete).
     *
     * Fields: password (required), reason (optional)
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
            'reason'   => 'nullable|string|max:1000',
        ], [
            'password.required' => 'كلمة المرور مطلوبة لتأكيد حذف الحساب',
        ]);

        $user = $request->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'كلمة المرور غير صحيحة',
                'errors'  => ['password' => ['كلمة المرور غير صحيحة']],
            ], 422);
        }

        // Release the unique identifiers so the student can register again later.
        // `deleted_at` (soft delete) is what marks the account as gone — the
        // status enum has no 'deleted' value.
        $suffix = '_del_' . time();
        $user->update([
            'email'       => $user->email . $suffix,
            'phone'       => $user->phone ? substr($user->phone, 0, 20 - strlen($suffix)) . $suffix : null,
            'national_id' => $user->national_id ? substr($user->national_id, 0, 20 - strlen($suffix)) . $suffix : null,
            'status'      => 'suspended',
        ]);

        // Revoke every token, then soft delete.
        $user->tokens()->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف حسابك بنجاح.',
        ]);
    }
}
