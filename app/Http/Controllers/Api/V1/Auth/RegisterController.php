<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\StudentDocument;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __construct(protected OtpService $otpService)
    {
    }

    /**
     * POST /api/v1/auth/register/otp/send
     * Step 1 — verify the phone/national id are free, then SMS a 6-digit OTP.
     *
     * Fields: phone, national_id
     */
    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'phone'       => ['required', 'regex:/^(05|5)\d{8}$/', 'max:12'],
            'national_id' => ['required', 'digits:10'],
        ], [
            'phone.required'       => 'رقم الجوال مطلوب',
            'phone.regex'          => 'رقم الجوال غير صالح (مثال: 0512345678)',
            'national_id.required' => 'رقم الهوية مطلوب',
            'national_id.digits'   => 'رقم الهوية يجب أن يكون 10 أرقام',
        ]);

        $phone = $this->normalisePhone($data['phone']);

        if ($this->phoneTaken($phone)) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الجوال مسجل مسبقاً',
            ], 422);
        }

        if (User::where('national_id', $data['national_id'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الهوية مسجل مسبقاً',
            ], 422);
        }

        try {
            $otp = $this->otpService->send($phone, 'registration');
        } catch (\Throwable $e) {
            Log::error('Registration SMS error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'تعذر إرسال رمز التحقق. حاول مرة أخرى.',
            ], 502);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال رمز التحقق إلى جوالك',
            'data'    => [
                'phone'      => $phone,
                'expires_at' => $otp->expires_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * POST /api/v1/auth/register/otp/verify
     * Step 2 — confirm the SMS code.
     *
     * Fields: phone, otp
     */
    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'phone' => ['required', 'regex:/^(05|5)\d{8}$/', 'max:12'],
            'otp'   => ['required', 'digits:6'],
        ], [
            'phone.required' => 'رقم الجوال مطلوب',
            'phone.regex'    => 'رقم الجوال غير صالح (مثال: 0512345678)',
            'otp.required'   => 'رمز التحقق مطلوب',
            'otp.digits'     => 'رمز التحقق يجب أن يكون 6 أرقام',
        ]);

        $phone = $this->normalisePhone($data['phone']);

        if (!$this->otpService->verify($phone, $data['otp'], 'registration')) {
            return response()->json([
                'success' => false,
                'message' => 'رمز التحقق غير صحيح أو منتهي الصلاحية',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم التحقق من رقم الجوال بنجاح',
            'data'    => [
                'phone'    => $phone,
                'verified' => true,
            ],
        ]);
    }

    /**
     * POST /api/v1/auth/register (multipart/form-data)
     * Step 3 — create the account. Requires a verified registration OTP for the
     * same phone number (valid for one hour after verification).
     *
     * Required fields:
     *   phone, national_id, name, email, password, password_confirmation,
     *   date_of_birth, gender, nationality,
     *   specialization, specialization_type, date_of_graduation,
     *   national_id_front (file), national_id_back (file), certificate (file),
     *   is_confirm_user (1), is_terms (1)
     */
    public function register(Request $request)
    {
        $request->validate([
            'phone'                 => ['required', 'regex:/^(05|5)\d{8}$/', 'max:12'],
            'national_id'           => 'required|digits:10|unique:users,national_id',
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255|unique:users,email',
            'password'              => ['required', 'confirmed', Password::min(8)],
            'date_of_birth'         => 'required|date|before:today',
            'gender'                => 'required|in:male,female',
            'nationality'           => 'required|string|max:100',
            'specialization'        => 'required|string|max:255',
            'specialization_type'   => 'required|string|max:255',
            'date_of_graduation'    => 'required|date',
            'national_id_front'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'national_id_back'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'certificate'           => 'required|file|mimes:pdf|max:10240',
            'is_confirm_user'       => 'required|accepted',
            'is_terms'              => 'required|accepted',
        ], [
            'phone.required'              => 'رقم الجوال مطلوب',
            'phone.regex'                 => 'رقم الجوال غير صالح (مثال: 0512345678)',
            'national_id.required'        => 'رقم الهوية مطلوب',
            'national_id.digits'          => 'رقم الهوية يجب أن يكون 10 أرقام',
            'national_id.unique'          => 'رقم الهوية مسجل مسبقاً',
            'name.required'               => 'الاسم مطلوب',
            'email.required'              => 'البريد الإلكتروني مطلوب',
            'email.email'                 => 'البريد الإلكتروني غير صالح',
            'email.unique'                => 'البريد الإلكتروني مسجل مسبقاً',
            'password.required'           => 'كلمة المرور مطلوبة',
            'password.confirmed'          => 'تأكيد كلمة المرور غير متطابق',
            'date_of_birth.required'      => 'تاريخ الميلاد مطلوب',
            'date_of_birth.before'        => 'تاريخ الميلاد غير صحيح',
            'gender.required'             => 'الجنس مطلوب',
            'gender.in'                   => 'قيمة الجنس يجب أن تكون male أو female',
            'nationality.required'        => 'الجنسية مطلوبة',
            'specialization.required'     => 'نوع المؤهل مطلوب',
            'specialization_type.required'=> 'المؤهل التعليمي  مطلوب',
            'date_of_graduation.required' => 'تاريخ التخرج مطلوب',
            'national_id_front.required'  => 'صورة الهوية الأمامية مطلوبة',
            'national_id_front.mimes'     => 'يجب أن تكون الصورة بصيغة JPG أو PNG أو PDF',
            'national_id_front.max'       => 'حجم الملف لا يتجاوز 5 ميجابايت',
            'national_id_back.required'   => 'صورة الهوية الخلفية مطلوبة',
            'national_id_back.mimes'      => 'يجب أن تكون الصورة بصيغة JPG أو PNG أو PDF',
            'national_id_back.max'        => 'حجم الملف لا يتجاوز 5 ميجابايت',
            'certificate.required'        => 'الشهادة مطلوبة',
            'certificate.mimes'           => 'يجب أن تكون الشهادة بصيغة PDF',
            'certificate.max'             => 'حجم الشهادة يجب ألا يتجاوز 10 ميجابايت',
            'is_confirm_user.accepted'    => 'يجب الإقرار بصحة البيانات المدخلة',
            'is_terms.accepted'           => 'يجب الموافقة على الشروط والأحكام',
        ]);

        $phone = $this->normalisePhone($request->input('phone'));

        // Gate on step 2: the phone must carry a verified registration OTP.
        if (!$this->otpService->isVerified($phone, 'registration')) {
            return response()->json([
                'success' => false,
                'message' => 'يجب التحقق من رقم الجوال أولاً',
            ], 403);
        }

        if ($this->phoneTaken($phone)) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الجوال مسجل مسبقاً',
            ], 422);
        }

        try {
            $user = User::create([
                'name'                => $request->name,
                'email'               => $request->email,
                'phone'               => $phone,
                'national_id'         => $request->national_id,
                'password'            => Hash::make($request->password),
                'date_of_birth'       => $request->date_of_birth,
                'gender'              => $request->gender,
                'nationality'         => $request->nationality,
                'specialization'      => $request->specialization,
                'specialization_type' => $request->specialization_type,
                'date_of_graduation'  => $request->date_of_graduation,
                'is_confirm_user'     => true,
                'is_terms'            => true,
                'role'                => 'student',
                'status'              => 'pending',
                'date_of_register'    => now()->toDateString(),
                'phone_verified_at'   => now(),
            ]);

            // Store national ID images and certificate in student_documents
            foreach (['national_id_front', 'national_id_back', 'certificate'] as $field) {
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
        } catch (\Throwable $e) {
            Log::error('Registration error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الحساب.',
            ], 500);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء حسابك بنجاح! سيتم مراجعة بياناتك والتواصل معك قريباً.',
            'data' => [
                'user' => [
                    'id'                  => $user->id,
                    'name'                => $user->name,
                    'national_id'         => $user->national_id,
                    'date_of_birth'       => $user->date_of_birth?->format('Y-m-d'),
                    'gender'              => $user->gender,
                    'nationality'         => $user->nationality,
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

    /**
     * The phone may already be stored with or without its leading zero.
     */
    protected function phoneTaken(string $phone): bool
    {
        return User::where('phone', $phone)
            ->orWhere('phone', ltrim($phone, '0'))
            ->exists();
    }

    /**
     * Ensure the Saudi mobile number carries its leading zero.
     */
    protected function normalisePhone(string $phone): string
    {
        return str_starts_with($phone, '0') ? $phone : '0' . $phone;
    }
}
