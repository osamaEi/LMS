<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('teacher.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone'       => 'nullable|string|max:20',
            'nationality' => 'nullable|string|max:100',
            'bio'         => 'nullable|string|max:500',
        ], [
            'name.required'  => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique'   => 'البريد الإلكتروني مسجل مسبقاً',
        ]);

        $user->update($validated);

        return response()->json(['success' => true, 'message' => 'تم تحديث البيانات بنجاح']);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة',
            'password.required'                 => 'كلمة المرور الجديدة مطلوبة',
            'password.confirmed'                => 'تأكيد كلمة المرور غير متطابق',
        ]);

        auth()->user()->update(['password' => Hash::make($request->password)]);

        return response()->json(['success' => true, 'message' => 'تم تغيير كلمة المرور بنجاح']);
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'photo.required' => 'يرجى اختيار صورة',
            'photo.mimes'    => 'يجب أن تكون الصورة بصيغة JPG أو PNG',
            'photo.max'      => 'حجم الصورة لا يتجاوز 2 ميجابايت',
        ]);

        $user = auth()->user();
        $path = $request->file('photo')->store('uploads/images', 'public');
        $user->update(['profile_photo' => $path]);

        return response()->json([
            'success'   => true,
            'message'   => 'تم تحديث الصورة الشخصية بنجاح',
            'photo_url' => asset('storage/' . $path),
        ]);
    }
}
