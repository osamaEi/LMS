<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل حساب جديد - ALERTIQA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @font-face { font-family: 'Cairo'; src: url('/font/static/Cairo-Regular.ttf') format('truetype'); font-weight: 400; }
        @font-face { font-family: 'Cairo'; src: url('/font/static/Cairo-Medium.ttf') format('truetype'); font-weight: 500; }
        @font-face { font-family: 'Cairo'; src: url('/font/static/Cairo-SemiBold.ttf') format('truetype'); font-weight: 600; }
        @font-face { font-family: 'Cairo'; src: url('/font/static/Cairo-Bold.ttf') format('truetype'); font-weight: 700; }
        * { font-family: 'Cairo', sans-serif; }
        h1, h2, h3, .font-bold { font-family: 'Cairo', sans-serif; font-weight: 700; }

        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(1.4); opacity: 0; }
        }
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .pulse-ring { animation: pulse-ring 1.5s ease-out infinite; }
        .spin-slow { animation: spin-slow 3s linear infinite; }

        .step { display: none; }
        .step.active { display: block; }
    </style>
</head>
<body class="min-h-screen" style="background: #f8fafc;">

<div class="min-h-screen flex">
    <!-- Left Side: Image -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden"
         style="background-image: url('{{ asset('images/logo/right.png') }}');
                background-size: cover; background-position: center; background-repeat: no-repeat;">
        <div class="absolute inset-0 bg-black bg-opacity-30"></div>
    </div>

    <!-- Right Side: Registration Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
        <div class="w-full max-w-md">

            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="{{ asset('images/logo/logo.png') }}" alt="ALERTIQA" class="mx-auto mb-6" style="width: 80px; height: auto;">
            </div>

            <!-- Progress Steps -->
            <div class="flex items-center justify-center gap-2 mb-8">
                <div id="step-dot-1" class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm shadow-lg" style="background: #3b82f6;">1</div>
                <div class="w-12 h-1 rounded-full" style="background: #e2e8f0;" id="step-line-1"></div>
                <div id="step-dot-2" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-lg" style="background: #e2e8f0; color: #94a3b8;">2</div>
                <div class="w-12 h-1 rounded-full" style="background: #e2e8f0;" id="step-line-2"></div>
                <div id="step-dot-3" class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm shadow-lg" style="background: #e2e8f0; color: #94a3b8;">3</div>
            </div>

            <!-- ==================== STEP 1: Mobile + National ID ==================== -->
            <div id="step1" class="step active">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold mb-2" style="color: #1e3a5f;">تسجيل حساب جديد</h2>
                    <p class="text-sm" style="color: #64748b;">أدخل رقم الجوال ورقم الهوية للتحقق عبر نفاذ</p>
                </div>

                <form id="step1-form" class="space-y-5">
                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #1e3a5f;">
                            <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            رقم الجوال
                        </label>
                        <input type="text" id="phone" name="phone" placeholder="05XXXXXXXX" maxlength="10" required
                               class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none transition"
                               style="border-color: #93c5fd; background: #f0f9ff;"
                               onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'"
                               onblur="this.style.borderColor='#93c5fd'; this.style.boxShadow='none'">
                        <p id="phone-error" class="mt-1 text-sm hidden" style="color: #f97316;"></p>
                    </div>

                    <!-- National ID -->
                    <div>
                        <label class="block text-sm font-medium mb-2" style="color: #1e3a5f;">
                            <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                            رقم الهوية الوطنية
                        </label>
                        <input type="text" id="national_id" name="national_id" placeholder="1XXXXXXXXX" maxlength="10" required
                               class="w-full px-4 py-3 border-2 rounded-xl focus:outline-none transition"
                               style="border-color: #93c5fd; background: #f0f9ff;"
                               onfocus="this.style.borderColor='#3b82f6'; this.style.boxShadow='0 0 0 3px rgba(59,130,246,0.1)'"
                               onblur="this.style.borderColor='#93c5fd'; this.style.boxShadow='none'">
                        <p id="national_id-error" class="mt-1 text-sm hidden" style="color: #f97316;"></p>
                    </div>

                    <!-- Error Message -->
                    <div id="step1-error" class="hidden p-4 rounded-xl text-sm font-medium" style="background: #fff7ed; color: #9a3412; border: 1px solid #fdba74;"></div>

                    <!-- Submit -->
                    <button type="submit" id="step1-btn"
                            class="w-full py-3.5 px-4 text-white font-bold rounded-xl shadow-lg transition-all hover:shadow-xl transform hover:scale-105"
                            style="background: #3b82f6;">
                        <span id="step1-btn-text">التحقق عبر نفاذ</span>
                        <svg id="step1-spinner" class="hidden w-5 h-5 inline spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </form>

                <!-- Login link -->
                <div class="text-center mt-6">
                    <p class="text-sm" style="color: #64748b;">لديك حساب بالفعل؟
                        <a href="{{ route('login') }}" class="font-bold" style="color: #3b82f6;">تسجيل الدخول</a>
                    </p>
                </div>
            </div>

            <!-- ==================== STEP 2: Nafath Verification ==================== -->
            <div id="step2" class="step">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold mb-2" style="color: #1e3a5f;">التحقق عبر نفاذ</h2>
                    <p class="text-sm" style="color: #64748b;">افتح تطبيق نفاذ واختر الرقم المطابق</p>
                </div>

                <!-- Random Number Display -->
                <div class="flex justify-center mb-8">
                    <div class="relative">
                        <div class="absolute inset-0 rounded-full pulse-ring" style="background: rgba(59,130,246,0.2);"></div>
                        <div class="relative w-32 h-32 rounded-full flex items-center justify-center shadow-2xl" style="background: #3b82f6;">
                            <span id="nafath-random" class="text-5xl font-bold text-white">--</span>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-6">
                    <p class="text-sm font-medium" style="color: #1e3a5f;">اختر هذا الرقم في تطبيق نفاذ</p>
                </div>

                <!-- Status -->
                <div id="nafath-status" class="p-4 rounded-xl text-center mb-6" style="background: #dbeafe;">
                    <div class="flex items-center justify-center gap-3">
                        <svg class="w-5 h-5 spin-slow" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="font-medium text-sm" style="color: #1e40af;">في انتظار التحقق...</span>
                    </div>
                </div>

                <!-- Timer -->
                <div class="text-center mb-6">
                    <p class="text-xs" style="color: #94a3b8;">المهلة المتبقية: <span id="nafath-timer" class="font-bold" style="color: #f97316;">5:00</span></p>
                </div>

                <!-- Error (hidden by default) -->
                <div id="step2-error" class="hidden p-4 rounded-xl text-center" style="background: #fff7ed; border: 1px solid #fdba74;">
                    <p class="font-medium text-sm" style="color: #9a3412;"></p>
                    <button onclick="goToStep(1)" class="mt-3 px-6 py-2 text-white font-bold rounded-xl text-sm" style="background: #f97316;">
                        حاول مرة أخرى
                    </button>
                </div>
            </div>

            <!-- ==================== STEP 3: Complete Registration ==================== -->
            <div id="step3" class="step" style="max-height:80vh; overflow-y:auto; padding-right:4px;">
                <div class="text-center mb-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-2 shadow-lg" style="background: #22c55e;">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold mb-1" style="color: #1e3a5f;">تم التحقق بنجاح!</h2>
                    <p class="text-xs" style="color: #64748b;">أكمل بياناتك الشخصية والأكاديمية</p>
                </div>

                <form id="step3-form" class="space-y-4">

                    {{-- Verified Data (phone + national_id from Step 1) --}}
                    <div style="background:#f0fdf4; border-radius:14px; padding:.75rem 1rem; border:1px solid #bbf7d0;">
                        <div class="flex items-center gap-2 mb-2.5">
                            <svg class="w-4 h-4" style="color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-xs font-bold" style="color:#15803d;">البيانات المتحقق منها عبر نفاذ</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-[0.68rem] font-medium mb-1" style="color:#64748b;">رقم الجوال</p>
                                <p id="display-phone" class="text-sm font-bold" style="color:#1e3a5f;" dir="ltr">—</p>
                            </div>
                            <div>
                                <p class="text-[0.68rem] font-medium mb-1" style="color:#64748b;">رقم الهوية الوطنية</p>
                                <p id="display-national-id" class="text-sm font-bold" style="color:#1e3a5f;" dir="ltr">—</p>
                            </div>
                        </div>
                    </div>

                    {{-- Personal Info Section --}}
                    <div style="background:#f0fdf4; border-radius:14px; padding:.875rem 1rem; border:1px solid #bbf7d0;">
                        <p class="text-xs font-bold mb-3" style="color:#15803d; text-transform:uppercase; letter-spacing:.05em;">البيانات الشخصية</p>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold mb-1" style="color:#1e3a5f;">الاسم الكامل <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="name" name="name" required placeholder="الاسم الرباعي"
                                       class="w-full px-3 py-2.5 border-2 rounded-xl focus:outline-none text-sm"
                                       style="border-color:#86efac; background:#fff;"
                                       onfocus="this.style.borderColor='#22c55e'" onblur="this.style.borderColor='#86efac'">
                                <p id="name-error" class="mt-1 text-xs hidden" style="color:#ef4444;"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold mb-1" style="color:#1e3a5f;">تاريخ الميلاد <span style="color:#ef4444;">*</span></label>
                                    <input type="date" id="date_of_birth" name="date_of_birth" required
                                           class="w-full px-3 py-2.5 border-2 rounded-xl focus:outline-none text-sm"
                                           style="border-color:#86efac; background:#fff;"
                                           onfocus="this.style.borderColor='#22c55e'" onblur="this.style.borderColor='#86efac'">
                                    <p id="dob-error" class="mt-1 text-xs hidden" style="color:#ef4444;"></p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold mb-1" style="color:#1e3a5f;">الجنس <span style="color:#ef4444;">*</span></label>
                                    <select id="gender" name="gender" required
                                            class="w-full px-3 py-2.5 border-2 rounded-xl focus:outline-none text-sm"
                                            style="border-color:#86efac; background:#fff;"
                                            onfocus="this.style.borderColor='#22c55e'" onblur="this.style.borderColor='#86efac'">
                                        <option value="">اختر</option>
                                        <option value="male">ذكر</option>
                                        <option value="female">أنثى</option>
                                    </select>
                                    <p id="gender-error" class="mt-1 text-xs hidden" style="color:#ef4444;"></p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold mb-1" style="color:#1e3a5f;">البريد الإلكتروني <span style="color:#ef4444;">*</span></label>
                                <input type="email" id="email" name="email" required placeholder="example@email.com"
                                       class="w-full px-3 py-2.5 border-2 rounded-xl focus:outline-none text-sm"
                                       style="border-color:#86efac; background:#fff;"
                                       onfocus="this.style.borderColor='#22c55e'" onblur="this.style.borderColor='#86efac'">
                                <p id="email-error" class="mt-1 text-xs hidden" style="color:#ef4444;"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold mb-1" style="color:#1e3a5f;">كلمة المرور <span style="color:#ef4444;">*</span></label>
                                    <input type="password" id="password" name="password" required minlength="8" placeholder="8 أحرف على الأقل"
                                           class="w-full px-3 py-2.5 border-2 rounded-xl focus:outline-none text-sm"
                                           style="border-color:#86efac; background:#fff;"
                                           onfocus="this.style.borderColor='#22c55e'" onblur="this.style.borderColor='#86efac'">
                                    <p id="password-error" class="mt-1 text-xs hidden" style="color:#ef4444;"></p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold mb-1" style="color:#1e3a5f;">تأكيد كلمة المرور <span style="color:#ef4444;">*</span></label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                                           class="w-full px-3 py-2.5 border-2 rounded-xl focus:outline-none text-sm"
                                           style="border-color:#86efac; background:#fff;"
                                           onfocus="this.style.borderColor='#22c55e'" onblur="this.style.borderColor='#86efac'">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Academic Info Section --}}
                    <div style="background:#eff6ff; border-radius:14px; padding:.875rem 1rem; border:1px solid #bfdbfe;">
                        <p class="text-xs font-bold mb-3" style="color:#1d4ed8; text-transform:uppercase; letter-spacing:.05em;">البيانات الأكاديمية</p>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs font-semibold mb-1" style="color:#1e3a5f;">التخصص <span style="color:#ef4444;">*</span></label>
                                <input type="text" id="specialization" name="specialization" required placeholder="مثال: علوم الحاسب، إدارة الأعمال"
                                       class="w-full px-3 py-2.5 border-2 rounded-xl focus:outline-none text-sm"
                                       style="border-color:#bfdbfe; background:#fff;"
                                       onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#bfdbfe'">
                                <p id="spec-error" class="mt-1 text-xs hidden" style="color:#ef4444;"></p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold mb-1" style="color:#1e3a5f;">نوع التخصص <span style="color:#ef4444;">*</span></label>
                                    <select id="specialization_type" name="specialization_type" required
                                            class="w-full px-3 py-2.5 border-2 rounded-xl focus:outline-none text-sm"
                                            style="border-color:#bfdbfe; background:#fff;"
                                            onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#bfdbfe'">
                                        <option value="">اختر</option>
                                        <option value="diploma">دبلوم</option>
                                        <option value="bachelor">بكالوريوس</option>
                                        <option value="master">ماجستير</option>
                                        <option value="phd">دكتوراه</option>
                                        <option value="training">تدريب مهني</option>
                                    </select>
                                    <p id="spec-type-error" class="mt-1 text-xs hidden" style="color:#ef4444;"></p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold mb-1" style="color:#1e3a5f;">تاريخ التخرج <span style="color:#ef4444;">*</span></label>
                                    <input type="date" id="date_of_graduation" name="date_of_graduation" required
                                           class="w-full px-3 py-2.5 border-2 rounded-xl focus:outline-none text-sm"
                                           style="border-color:#bfdbfe; background:#fff;"
                                           onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#bfdbfe'">
                                    <p id="grad-error" class="mt-1 text-xs hidden" style="color:#ef4444;"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- National ID Images Section --}}
                    <div style="background:#fdf4ff; border-radius:14px; padding:.875rem 1rem; border:1px solid #e9d5ff;">
                        <p class="text-xs font-bold mb-3" style="color:#7c3aed; text-transform:uppercase; letter-spacing:.05em;">صور الهوية الوطنية</p>
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Front -->
                            <div>
                                <label class="block text-xs font-semibold mb-1" style="color:#1e3a5f;">الوجه الأمامي <span style="color:#ef4444;">*</span></label>
                                <label id="front-label" for="national_id_front"
                                       class="flex flex-col items-center justify-center w-full cursor-pointer rounded-xl border-2 border-dashed"
                                       style="border-color:#c4b5fd; background:#fff; min-height:90px; padding:.625rem; transition:border-color .2s;"
                                       ondragover="event.preventDefault();this.style.borderColor='#7c3aed'"
                                       ondragleave="this.style.borderColor='#c4b5fd'"
                                       ondrop="handleDrop(event,'national_id_front','front-preview','front-label')">
                                    <div id="front-preview" style="text-align:center; width:100%;">
                                        <svg class="w-7 h-7 mx-auto mb-1" style="color:#a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-xs" style="color:#7c3aed;">اضغط أو اسحب</p>
                                        <p class="text-xs" style="color:#a78bfa; margin-top:2px;">JPG, PNG, PDF</p>
                                    </div>
                                    <input type="file" id="national_id_front" name="national_id_front"
                                           accept=".jpg,.jpeg,.png,.pdf" required class="hidden"
                                           onchange="previewFile(this,'front-preview','front-label')">
                                </label>
                                <p id="front-error" class="mt-1 text-xs hidden" style="color:#ef4444;"></p>
                            </div>
                            <!-- Back -->
                            <div>
                                <label class="block text-xs font-semibold mb-1" style="color:#1e3a5f;">الوجه الخلفي <span style="color:#ef4444;">*</span></label>
                                <label id="back-label" for="national_id_back"
                                       class="flex flex-col items-center justify-center w-full cursor-pointer rounded-xl border-2 border-dashed"
                                       style="border-color:#c4b5fd; background:#fff; min-height:90px; padding:.625rem; transition:border-color .2s;"
                                       ondragover="event.preventDefault();this.style.borderColor='#7c3aed'"
                                       ondragleave="this.style.borderColor='#c4b5fd'"
                                       ondrop="handleDrop(event,'national_id_back','back-preview','back-label')">
                                    <div id="back-preview" style="text-align:center; width:100%;">
                                        <svg class="w-7 h-7 mx-auto mb-1" style="color:#a78bfa;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-xs" style="color:#7c3aed;">اضغط أو اسحب</p>
                                        <p class="text-xs" style="color:#a78bfa; margin-top:2px;">JPG, PNG, PDF</p>
                                    </div>
                                    <input type="file" id="national_id_back" name="national_id_back"
                                           accept=".jpg,.jpeg,.png,.pdf" required class="hidden"
                                           onchange="previewFile(this,'back-preview','back-label')">
                                </label>
                                <p id="back-error" class="mt-1 text-xs hidden" style="color:#ef4444;"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Checkboxes --}}
                    <div class="space-y-2">
                        <label id="confirm-box" class="flex items-start gap-3 cursor-pointer p-3 rounded-xl border-2"
                               style="border-color:#e2e8f0; background:#f8fafc; transition:border-color .2s;">
                            <input type="checkbox" id="is_confirm_user" name="is_confirm_user" value="1"
                                   style="width:18px;height:18px;flex-shrink:0;margin-top:2px;accent-color:#3b82f6;cursor:pointer;"
                                   onchange="toggleCheckbox(this,'confirm-box')">
                            <span class="text-xs font-medium leading-relaxed" style="color:#374151;">
                                أقر بأن جميع البيانات المدخلة صحيحة ومطابقة للهوية الرسمية.
                            </span>
                        </label>
                        <p id="confirm-error" class="text-xs hidden" style="color:#ef4444; padding-right:.5rem;"></p>

                        <label id="terms-box" class="flex items-start gap-3 cursor-pointer p-3 rounded-xl border-2"
                               style="border-color:#e2e8f0; background:#f8fafc; transition:border-color .2s;">
                            <input type="checkbox" id="is_terms" name="is_terms" value="1"
                                   style="width:18px;height:18px;flex-shrink:0;margin-top:2px;accent-color:#3b82f6;cursor:pointer;"
                                   onchange="toggleCheckbox(this,'terms-box')">
                            <span class="text-xs font-medium leading-relaxed" style="color:#374151;">
                                أوافق على الشروط والأحكام وسياسات الخصوصية الخاصة بالمعهد.
                            </span>
                        </label>
                        <p id="terms-error" class="text-xs hidden" style="color:#ef4444; padding-right:.5rem;"></p>
                    </div>

                    <!-- Error -->
                    <div id="step3-error" class="hidden p-3 rounded-xl text-sm font-medium" style="background:#fff7ed; color:#9a3412; border:1px solid #fdba74;"></div>

                    <!-- Submit -->
                    <button type="submit" id="step3-btn"
                            class="w-full py-3.5 px-4 text-white font-bold rounded-xl shadow-lg transition-all hover:shadow-xl"
                            style="background: #22c55e;">
                        <span id="step3-btn-text">إنشاء الحساب</span>
                        <svg id="step3-spinner" class="hidden w-5 h-5 inline spin-slow mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </form>
            </div>

            <!-- ==================== SUCCESS ==================== -->
            <div id="step-success" class="step">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl" style="background: #22c55e;">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold mb-3" style="color: #1e3a5f;">تم إنشاء حسابك بنجاح!</h2>
                    <p class="text-sm mb-8" style="color: #64748b;">يمكنك الآن تسجيل الدخول باستخدام بريدك الإلكتروني وكلمة المرور</p>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 px-8 py-3.5 text-white font-bold rounded-xl shadow-lg transition-all hover:shadow-xl transform hover:scale-105"
                       style="background: #3b82f6;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        تسجيل الدخول
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let pollInterval = null;
    let timerInterval = null;
    let currentTransactionId = null;

    // ========== Step Navigation ==========
    function goToStep(step) {
        document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
        document.getElementById(step === 'success' ? 'step-success' : 'step' + step).classList.add('active');

        // Populate verified data display when entering Step 3
        if (step === 3) {
            const phone = document.getElementById('phone').value.trim();
            const natId = document.getElementById('national_id').value.trim();
            const dPhone = document.getElementById('display-phone');
            const dNatId = document.getElementById('display-national-id');
            if (dPhone) dPhone.textContent = phone || '—';
            if (dNatId) dNatId.textContent = natId || '—';
        }

        // Update step dots
        for (let i = 1; i <= 3; i++) {
            const dot = document.getElementById('step-dot-' + i);
            if (i < step) {
                dot.style.background = '#22c55e';
                dot.style.color = 'white';
            } else if (i == step) {
                dot.style.background = '#3b82f6';
                dot.style.color = 'white';
            } else {
                dot.style.background = '#e2e8f0';
                dot.style.color = '#94a3b8';
            }
        }
        if (step >= 2) document.getElementById('step-line-1').style.background = '#22c55e';
        else document.getElementById('step-line-1').style.background = '#e2e8f0';
        if (step >= 3 || step === 'success') document.getElementById('step-line-2').style.background = '#22c55e';
        else document.getElementById('step-line-2').style.background = '#e2e8f0';
    }

    // ========== STEP 1: Submit ==========
    document.getElementById('step1-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Clear errors
        document.getElementById('phone-error').classList.add('hidden');
        document.getElementById('national_id-error').classList.add('hidden');
        document.getElementById('step1-error').classList.add('hidden');

        const phone = document.getElementById('phone').value.trim();
        const nationalId = document.getElementById('national_id').value.trim();

        // Validate
        if (!/^(05|5)\d{8}$/.test(phone)) {
            document.getElementById('phone-error').textContent = 'رقم الجوال غير صالح (مثال: 05XXXXXXXX)';
            document.getElementById('phone-error').classList.remove('hidden');
            return;
        }
        if (!/^\d{10}$/.test(nationalId)) {
            document.getElementById('national_id-error').textContent = 'رقم الهوية يجب أن يكون 10 أرقام';
            document.getElementById('national_id-error').classList.remove('hidden');
            return;
        }

        // Show loading
        document.getElementById('step1-btn').disabled = true;
        document.getElementById('step1-btn-text').textContent = 'جاري الاتصال بنفاذ...';
        document.getElementById('step1-spinner').classList.remove('hidden');

        try {
            const res = await fetch('/register/nafath', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ phone, national_id: nationalId }),
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(', ') : 'حدث خطأ');
                document.getElementById('step1-error').textContent = msg;
                document.getElementById('step1-error').classList.remove('hidden');
                resetStep1Btn();
                return;
            }

            // Success - go to step 2
            currentTransactionId = data.transaction_id;
            document.getElementById('nafath-random').textContent = data.random || '--';
            goToStep(2);

            // BYPASS MODE: Show step 2 briefly, then auto-proceed to step 3
            if (data.bypass) {
                // Update status message
                document.getElementById('nafath-status').innerHTML = `
                    <div class="flex items-center justify-center gap-3">
                        <svg class="w-5 h-5 spin-slow" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span class="font-medium text-sm" style="color: #1e40af;">محاكاة التحقق (وضع التطوير)...</span>
                    </div>
                `;

                // Auto-proceed to step 3 after 2 seconds
                setTimeout(() => {
                    goToStep(3);
                }, 2000);
                return;
            }

            // Normal flow - start polling
            startPolling();
            startTimer();

        } catch (err) {
            document.getElementById('step1-error').textContent = 'حدث خطأ في الاتصال. حاول مرة أخرى.';
            document.getElementById('step1-error').classList.remove('hidden');
            resetStep1Btn();
        }
    });

    function resetStep1Btn() {
        document.getElementById('step1-btn').disabled = false;
        document.getElementById('step1-btn-text').textContent = 'التحقق عبر نفاذ';
        document.getElementById('step1-spinner').classList.add('hidden');
    }

    // ========== STEP 2: Polling ==========
    function startPolling() {
        if (pollInterval) clearInterval(pollInterval);

        pollInterval = setInterval(async () => {
            try {
                const res = await fetch('/register/nafath/poll/' + currentTransactionId, {
                    headers: { 'Accept': 'application/json' },
                });
                const data = await res.json();

                if (data.status === 'approved') {
                    clearInterval(pollInterval);
                    clearInterval(timerInterval);
                    goToStep(3);
                } else if (data.status === 'rejected') {
                    clearInterval(pollInterval);
                    clearInterval(timerInterval);
                    showStep2Error('تم رفض طلب التحقق. حاول مرة أخرى.');
                } else if (data.status === 'expired') {
                    clearInterval(pollInterval);
                    clearInterval(timerInterval);
                    showStep2Error('انتهت صلاحية الطلب. حاول مرة أخرى.');
                }
            } catch (err) {
                // Silent fail, will retry
            }
        }, 3000);
    }

    function startTimer() {
        let seconds = 300; // 5 minutes
        if (timerInterval) clearInterval(timerInterval);

        timerInterval = setInterval(() => {
            seconds--;
            const m = Math.floor(seconds / 60);
            const s = seconds % 60;
            document.getElementById('nafath-timer').textContent = m + ':' + (s < 10 ? '0' : '') + s;

            if (seconds <= 0) {
                clearInterval(timerInterval);
                clearInterval(pollInterval);
                showStep2Error('انتهت المهلة. حاول مرة أخرى.');
            }
        }, 1000);
    }

    function showStep2Error(msg) {
        document.getElementById('nafath-status').classList.add('hidden');
        const errDiv = document.getElementById('step2-error');
        errDiv.querySelector('p').textContent = msg;
        errDiv.classList.remove('hidden');
    }

    // ========== STEP 3: Helper – file preview ==========
    function previewFile(input, previewId, labelId) {
        const preview = document.getElementById(previewId);
        const label   = document.getElementById(labelId);
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const name = file.name.length > 22 ? file.name.substring(0, 22) + '…' : file.name;
        const isImage = file.type.startsWith('image/');
        if (isImage) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="preview"
                         style="width:100%;height:64px;object-fit:cover;border-radius:8px;margin-bottom:4px;">
                    <p style="font-size:.65rem;color:#7c3aed;font-weight:600;">${name}</p>`;
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = `
                <svg class="w-7 h-7 mx-auto mb-1" style="color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <p style="font-size:.65rem;color:#7c3aed;font-weight:600;">${name}</p>`;
        }
        label.style.borderColor = '#7c3aed';
        label.style.borderStyle = 'solid';
    }

    function handleDrop(event, inputId, previewId, labelId) {
        event.preventDefault();
        const input = document.getElementById(inputId);
        const dt    = event.dataTransfer;
        if (dt.files.length) {
            const transfer = new DataTransfer();
            transfer.items.add(dt.files[0]);
            input.files = transfer.files;
            previewFile(input, previewId, labelId);
        }
        document.getElementById(labelId).style.borderColor = '#7c3aed';
    }

    function toggleCheckbox(checkbox, boxId) {
        const box = document.getElementById(boxId);
        box.style.borderColor = checkbox.checked ? '#3b82f6' : '#e2e8f0';
        box.style.background  = checkbox.checked ? '#eff6ff' : '#f8fafc';
    }

    // ========== STEP 3: Complete Registration ==========
    document.getElementById('step3-form').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Clear all errors
        ['name-error','dob-error','gender-error','email-error','password-error',
         'spec-error','spec-type-error','grad-error','front-error','back-error',
         'confirm-error','terms-error','step3-error'
        ].forEach(id => { const el = document.getElementById(id); if(el){ el.textContent=''; el.classList.add('hidden'); }});

        // Client-side validation
        let valid = true;
        const v = (id, errId, msg) => {
            const el = document.getElementById(id);
            if (!el) return;
            const val = el.type === 'checkbox' ? el.checked : el.value.trim();
            if (!val) { showFieldError(errId, msg); valid = false; }
        };

        v('name',                'name-error',      'الاسم الكامل مطلوب');
        v('date_of_birth',       'dob-error',       'تاريخ الميلاد مطلوب');
        v('gender',              'gender-error',    'الجنس مطلوب');
        v('email',               'email-error',     'البريد الإلكتروني مطلوب');
        v('specialization',      'spec-error',      'التخصص مطلوب');
        v('specialization_type', 'spec-type-error', 'نوع التخصص مطلوب');
        v('date_of_graduation',  'grad-error',      'تاريخ التخرج مطلوب');

        const pw  = document.getElementById('password').value;
        const pwc = document.getElementById('password_confirmation').value;
        if (pw.length < 8) { showFieldError('password-error', 'كلمة المرور 8 أحرف على الأقل'); valid = false; }
        else if (pw !== pwc) { showFieldError('password-error', 'تأكيد كلمة المرور غير متطابق'); valid = false; }

        if (!document.getElementById('national_id_front').files.length) {
            showFieldError('front-error', 'صورة الوجه الأمامي للهوية مطلوبة'); valid = false;
        }
        if (!document.getElementById('national_id_back').files.length) {
            showFieldError('back-error', 'صورة الوجه الخلفي للهوية مطلوبة'); valid = false;
        }
        if (!document.getElementById('is_confirm_user').checked) {
            showFieldError('confirm-error', 'يجب الإقرار بصحة البيانات المدخلة'); valid = false;
        }
        if (!document.getElementById('is_terms').checked) {
            showFieldError('terms-error', 'يجب الموافقة على الشروط والأحكام'); valid = false;
        }

        if (!valid) return;

        // Build FormData (supports file uploads)
        const fd = new FormData(document.getElementById('step3-form'));

        // Show loading
        document.getElementById('step3-btn').disabled = true;
        document.getElementById('step3-btn-text').textContent = 'جاري إنشاء الحساب...';
        document.getElementById('step3-spinner').classList.remove('hidden');

        try {
            const res = await fetch('/register/complete', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    // No Content-Type — browser sets it with multipart boundary automatically
                },
                body: fd,
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                let msg = data.message || 'حدث خطأ';
                if (data.errors) {
                    const allErrors = Object.values(data.errors).flat();
                    msg = allErrors.join('<br>');
                }
                const errEl = document.getElementById('step3-error');
                errEl.innerHTML = msg;
                errEl.classList.remove('hidden');
                resetStep3Btn();
                return;
            }

            goToStep('success');

        } catch (err) {
            document.getElementById('step3-error').textContent = 'حدث خطأ في الاتصال. حاول مرة أخرى.';
            document.getElementById('step3-error').classList.remove('hidden');
            resetStep3Btn();
        }
    });

    function resetStep3Btn() {
        document.getElementById('step3-btn').disabled = false;
        document.getElementById('step3-btn-text').textContent = 'إنشاء الحساب';
        document.getElementById('step3-spinner').classList.add('hidden');
    }

    function showFieldError(id, msg) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = msg;
        el.classList.remove('hidden');
    }
</script>

</body>
</html>
