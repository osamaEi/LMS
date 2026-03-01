<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * POST /api/v1/auth/register
     * Register a new student account (multipart/form-data)
     *
     * Required fields:
     *   name, national_id, date_of_birth, gender, email, phone,
     *   password, password_confirmation,
     *   specialization, specialization_type, date_of_graduation,
     *   national_id_front (file), national_id_back (file),
     *   is_confirm_user (1), is_terms (1)
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'national_id'           => 'required|digits:10|unique:users,national_id',
            'date_of_birth'         => 'required|date|before:today',
            'gender'                => 'required|in:male,female',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => ['required', 'regex:/^(05|5)\d{8}$/', 'max:12', 'unique:users,phone'],
            'password'              => ['required', 'confirmed', Password::min(8)],
            'specialization'        => 'required|string|max:255',
            'specialization_type'   => 'required|string|max:255',
            'date_of_graduation'    => 'required|date',
            'national_id_front'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'national_id_back'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'is_confirm_user'       => 'required|accepted',
            'is_terms'              => 'required|accepted',
        ], [
            'name.required'               => 'الاسم مطلوب',
            'national_id.required'        => 'رقم الهوية مطلوب',
            'national_id.digits'          => 'رقم الهوية يجب أن يكون 10 أرقام',
            'national_id.unique'          => 'رقم الهوية مسجل مسبقاً',
            'date_of_birth.required'      => 'تاريخ الميلاد مطلوب',
            'date_of_birth.before'        => 'تاريخ الميلاد غير صحيح',
            'gender.required'             => 'الجنس مطلوب',
            'gender.in'                   => 'قيمة الجنس يجب أن تكون male أو female',
            'email.required'              => 'البريد الإلكتروني مطلوب',
            'email.unique'                => 'البريد الإلكتروني مسجل مسبقاً',
            'phone.required'              => 'رقم الجوال مطلوب',
            'phone.regex'                 => 'رقم الجوال غير صالح (مثال: 0512345678)',
            'phone.unique'                => 'رقم الجوال مسجل مسبقاً',
            'password.required'           => 'كلمة المرور مطلوبة',
            'password.confirmed'          => 'تأكيد كلمة المرور غير متطابق',
            'specialization.required'     => 'التخصص مطلوب',
            'specialization_type.required'=> 'نوع التخصص مطلوب',
            'date_of_graduation.required' => 'تاريخ التخرج مطلوب',
            'national_id_front.required'  => 'صورة الهوية الأمامية مطلوبة',
            'national_id_front.mimes'     => 'يجب أن تكون الصورة بصيغة JPG أو PNG أو PDF',
            'national_id_front.max'       => 'حجم الملف لا يتجاوز 5 ميجابايت',
            'national_id_back.required'   => 'صورة الهوية الخلفية مطلوبة',
            'national_id_back.mimes'      => 'يجب أن تكون الصورة بصيغة JPG أو PNG أو PDF',
            'national_id_back.max'        => 'حجم الملف لا يتجاوز 5 ميجابايت',
            'is_confirm_user.accepted'    => 'يجب الإقرار بصحة البيانات المدخلة',
            'is_terms.accepted'           => 'يجب الموافقة على الشروط والأحكام',
        ]);

        // Normalise phone (ensure leading 0)
        $phone = $request->phone;
        if (!str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }

        $user = User::create([
            'name'                => $request->name,
            'national_id'         => $request->national_id,
            'date_of_birth'       => $request->date_of_birth,
            'gender'              => $request->gender,
            'email'               => $request->email,
            'phone'               => $phone,
            'password'            => Hash::make($request->password),
            'specialization'      => $request->specialization,
            'specialization_type' => $request->specialization_type,
            'date_of_graduation'  => $request->date_of_graduation,
            'is_confirm_user'     => true,
            'is_terms'            => true,
            'role'                => 'student',
            'type'                => 'student',
            'status'              => 'pending',
            'date_of_register'    => now()->toDateString(),
        ]);

        // Store national ID images
        foreach (['national_id_front', 'national_id_back'] as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $path = $file->store("student-documents/{$user->id}", 'public');
                StudentDocument::create([
                    'user_id'       => $user->id,
                    'document_type' => $field,
                    'file_path'     => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'file_size'     => $file->getSize(),
                    'mime_type'     => $file->getMimeType(),
                    'status'        => 'pending',
                ]);
            }
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء الحساب بنجاح. في انتظار مراجعة الإدارة وتفعيل حسابك.',
            'data' => [
                'user' => [
                    'id'                  => $user->id,
                    'name'                => $user->name,
                    'national_id'         => $user->national_id,
                    'date_of_birth'       => $user->date_of_birth?->format('Y-m-d'),
                    'gender'              => $user->gender,
                    'email'               => $user->email,
                    'phone'               => $user->phone,
                    'specialization'      => $user->specialization,
                    'specialization_type' => $user->specialization_type,
                    'date_of_graduation'  => $user->date_of_graduation?->format('Y-m-d'),
                    'role'                => $user->role,
                    'status'              => $user->status,
                    'program_id'          => $user->program_id,
                    'program_status'      => $user->program_status,
                ],
                'token' => $token,
            ],
        ], 201);
    }
}
