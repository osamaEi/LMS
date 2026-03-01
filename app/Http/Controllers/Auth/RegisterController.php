<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentDocument;
use App\Services\NafathService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    protected NafathService $nafathService;

    public function __construct(NafathService $nafathService)
    {
        $this->nafathService = $nafathService;
    }

    /**
     * Show registration form
     */
    public function showForm()
    {
        return view('auth.register');
    }

    /**
     * Initiate Nafath verification
     */
    public function initiateNafath(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^(05|5)\d{8}$/', 'max:12'],
            'national_id' => ['required', 'digits:10', 'numeric'],
        ], [
            'phone.required' => 'رقم الجوال مطلوب',
            'phone.regex' => 'رقم الجوال غير صالح',
            'national_id.required' => 'رقم الهوية مطلوب',
            'national_id.digits' => 'رقم الهوية يجب أن يكون 10 أرقام',
            'national_id.numeric' => 'رقم الهوية يجب أن يكون أرقام فقط',
        ]);

        // Check if national_id already registered
        if (User::where('national_id', $request->national_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الهوية مسجل مسبقاً',
            ], 422);
        }

        // Check if phone already registered
        $phone = $request->phone;
        if (!str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }
        if (User::where('phone', $phone)->orWhere('phone', ltrim($phone, '0'))->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الجوال مسجل مسبقاً',
            ], 422);
        }

        // BYPASS MODE: Skip Nafath verification entirely
        if (config('services.nafath.bypass', false)) {
            // Store registration data in session
            session([
                'register_phone' => $phone,
                'register_national_id' => $request->national_id,
                'register_nafath_bypass' => true,
            ]);

            // Return simulated response for step 2 display
            return response()->json([
                'success' => true,
                'bypass' => true,
                'transaction_id' => 'bypass-' . uniqid(),
                'random' => rand(10, 99),
            ]);
        }

        try {
            $transaction = $this->nafathService->initiateOpenAccount($request->national_id);

            if ($transaction->status === 'rejected') {
                $errorMsg = $transaction->response_payload['error'] ?? 'فشل الاتصال بنفاذ';
                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                ], 500);
            }

            $random = $this->nafathService->getRandomNumber($transaction);

            // Store registration data in session
            session([
                'register_phone' => $phone,
                'register_national_id' => $request->national_id,
                'register_nafath_transaction' => $transaction->transaction_id,
            ]);

            return response()->json([
                'success' => true,
                'transaction_id' => $transaction->transaction_id,
                'random' => $random,
            ]);
        } catch (\Exception $e) {
            Log::error('Registration Nafath error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الاتصال بنفاذ. حاول مرة أخرى.',
            ], 500);
        }
    }

    /**
     * Poll Nafath status
     */
    public function pollNafath($transactionId)
    {
        try {
            $transaction = $this->nafathService->pollStatus($transactionId);

            return response()->json([
                'status' => $transaction->status,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'فشل التحقق من الحالة',
            ], 500);
        }
    }

    /**
     * Complete registration after Nafath verification
     */
    public function completeRegistration(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|max:255|unique:users,email',
            'password'              => 'required|confirmed|min:8',
            'date_of_birth'         => 'required|date|before:today',
            'gender'                => 'required|in:male,female',
            'specialization'        => 'required|string|max:255',
            'specialization_type'   => 'required|string|max:255',
            'date_of_graduation'    => 'required|date',
            'national_id_front'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'national_id_back'      => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'is_confirm_user'       => 'required|accepted',
            'is_terms'              => 'required|accepted',
        ], [
            'name.required'               => 'الاسم مطلوب',
            'email.required'              => 'البريد الإلكتروني مطلوب',
            'email.email'                 => 'البريد الإلكتروني غير صالح',
            'email.unique'                => 'البريد الإلكتروني مسجل مسبقاً',
            'password.required'           => 'كلمة المرور مطلوبة',
            'password.confirmed'          => 'تأكيد كلمة المرور غير متطابق',
            'password.min'                => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'date_of_birth.required'      => 'تاريخ الميلاد مطلوب',
            'date_of_birth.before'        => 'تاريخ الميلاد غير صحيح',
            'gender.required'             => 'الجنس مطلوب',
            'gender.in'                   => 'قيمة الجنس غير صحيحة',
            'specialization.required'     => 'التخصص مطلوب',
            'specialization_type.required'=> 'نوع التخصص مطلوب',
            'date_of_graduation.required' => 'تاريخ التخرج مطلوب',
            'national_id_front.required'  => 'صورة الهوية الأمامية مطلوبة',
            'national_id_front.mimes'     => 'يجب أن تكون الصورة بصيغة JPG أو PNG أو PDF',
            'national_id_front.max'       => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت',
            'national_id_back.required'   => 'صورة الهوية الخلفية مطلوبة',
            'national_id_back.mimes'      => 'يجب أن تكون الصورة بصيغة JPG أو PNG أو PDF',
            'national_id_back.max'        => 'حجم الملف يجب ألا يتجاوز 5 ميجابايت',
            'is_confirm_user.accepted'    => 'يجب الإقرار بصحة البيانات المدخلة',
            'is_terms.accepted'           => 'يجب الموافقة على الشروط والأحكام',
        ]);

        $phone = session('register_phone');
        $nationalId = session('register_national_id');
        $isBypassed = session('register_nafath_bypass', false);

        if (!$phone || !$nationalId) {
            return response()->json([
                'success' => false,
                'message' => 'انتهت صلاحية الجلسة. يرجى البدء من جديد.',
            ], 422);
        }

        // BYPASS MODE: Skip Nafath verification check
        if (!$isBypassed) {
            $transactionId = session('register_nafath_transaction');

            if (!$transactionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'انتهت صلاحية الجلسة. يرجى البدء من جديد.',
                ], 422);
            }

            // Verify Nafath transaction is approved
            $transaction = $this->nafathService->getTransaction($transactionId);
            if (!$transaction || !$transaction->isApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم التحقق من الهوية عبر نفاذ.',
                ], 422);
            }
        }

        try {
            $userData = [
                'name'                => $request->name,
                'email'               => $request->email,
                'phone'               => $phone,
                'national_id'         => $nationalId,
                'password'            => Hash::make($request->password),
                'date_of_birth'       => $request->date_of_birth,
                'gender'              => $request->gender,
                'specialization'      => $request->specialization,
                'specialization_type' => $request->specialization_type,
                'date_of_graduation'  => $request->date_of_graduation,
                'is_confirm_user'     => true,
                'is_terms'            => true,
                'role'                => 'student',
                'status'              => 'pending',
                'date_of_register'    => now()->toDateString(),
            ];

            // Add Nafath data if not bypassed
            if (!$isBypassed && isset($transaction)) {
                $userData['nafath_verified_at'] = now();
                $userData['nafath_transaction_id'] = $transaction->transaction_id;
            }

            $user = User::create($userData);

            // Store national ID images in student_documents
            foreach (['national_id_front', 'national_id_back'] as $field) {
                if ($request->hasFile($field)) {
                    $file     = $request->file($field);
                    $path     = $file->store("student-documents/{$user->id}", 'public');
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

            // Link transaction to user if exists
            if (!$isBypassed && isset($transaction)) {
                $transaction->update(['user_id' => $user->id]);
            }

            // Clear session
            session()->forget(['register_phone', 'register_national_id', 'register_nafath_transaction', 'register_nafath_bypass']);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء حسابك بنجاح! سيتم مراجعة بياناتك والتواصل معك قريباً.',
            ]);
        } catch (\Exception $e) {
            Log::error('Registration error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الحساب.',
            ], 500);
        }
    }
}
