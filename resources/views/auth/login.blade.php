<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول - ALERTIQA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-Regular.ttf') format('truetype');
            font-weight: 400;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-Medium.ttf') format('truetype');
            font-weight: 500;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-SemiBold.ttf') format('truetype');
            font-weight: 600;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('/font/static/Cairo-Bold.ttf') format('truetype');
            font-weight: 700;
            font-style: normal;
        }

        * {
            font-family: 'Cairo', sans-serif;
        }

        h1, h2, h3, .font-bold {
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50">

    <!-- ============================================ -->
    <!-- PART 1: LEFT SIDE - BLUE BACKGROUND WITH LOGO -->
    <!-- ============================================ -->

    <div class="min-h-screen flex">

        <!-- Left Side: Image Background -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden"
             style="background-image: url('{{ asset('images/logo/right.png') }}');
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;">

            <!-- Dark overlay for better text visibility -->
            <div class="absolute inset-0 bg-black bg-opacity-30"></div>

            <!-- Content: Logo Only -->
            <div class="relative z-10 flex flex-col justify-center items-center text-white p-12 w-full">
                <div class="text-center">

                  

                </div>
            </div>
        </div>


        <!-- ============================================ -->
        <!-- PART 2: RIGHT SIDE - LOGIN FORM -->
        <!-- ============================================ -->

        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white">
            <div class="w-full max-w-md">

                <!-- Logo at top -->
                <div class="text-center mb-8">
                    <img src="{{ asset('images/logo/logo.png') }}"
                         alt="ALERTIQA"
                         class="mx-auto mb-8"
                         style="width: 80px; height: auto;">

                    <h2 class="text-3xl font-bold text-gray-900 mb-3">تسجيل الدخول</h2>
                    <p class="text-gray-600 text-sm">ادخل إلى حسابك باستخدام بيانات المعهد لمتابعة دوراتك وجدولاتك الدراسية</p>
                </div>

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email/ID Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                            </svg>
                            رقم الهوية
                        </label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            required
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                            placeholder=""
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                            </svg>
                            كلمة المرور
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('password') border-red-500 @enderror"
                                placeholder=""
                            >
                            <button type="button" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" onclick="togglePassword()">
                                <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <a href="#" class="inline-block mt-2 text-sm text-blue-600 hover:text-blue-700">نسيت كلمة المرور؟</a>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full py-3.5 px-4 text-white font-bold rounded-lg shadow-md transition-all duration-200"
                        style="background-color: #0D6FA6;"
                        onmouseover="this.style.backgroundColor='#0A5A86'"
                        onmouseout="this.style.backgroundColor='#0D6FA6'"
                    >
                        إرسال رمز التحقق
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white text-gray-500">أو</span>
                    </div>
                </div>

                <!-- Bottom Buttons -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Nafath Button -->
                    <button type="button" class="flex flex-col items-center justify-center gap-2 px-4 py-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="10" fill="#1F2937"/>
                            <path d="M12 8v8M8 12h8" stroke="white" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                        <span class="text-xs text-gray-600">أجابات إستفسارات ؟</span>
                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">تواصل معنا</a>
                    </button>

                    <!-- Register Button -->
                    <button type="button" class="flex flex-col items-center justify-center gap-2 px-4 py-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-xs text-gray-600">ليس لديك حساب ؟</span>
                        <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700">سجل حساب جديد</a>
                    </button>
                </div>

            </div>
        </div>

    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }
    </script>

</body>
</html>
