@extends('layouts.dashboard')

@section('title', __('الملف الشخصي'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-2xl shadow-2xl p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white opacity-5 rounded-full -ml-24 -mb-24"></div>

                <div class="relative z-10 flex items-center justify-between">
                    <div class="flex items-center space-x-4 rtl:space-x-reverse">
                        <div class="w-20 h-20 rounded-full bg-white bg-opacity-20 flex items-center justify-center backdrop-blur-sm">
                            @if(auth()->user()->avatar)
                                <img src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover">
                            @else
                                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">{{ __('الملف الشخصي') }}</h1>
                            <p class="text-blue-100 mt-1">{{ __('تحديث معلوماتك الشخصية') }}</p>
                        </div>
                    </div>
                    <div class="hidden md:flex items-center space-x-2 rtl:space-x-reverse">
                        <span class="px-4 py-2 bg-white bg-opacity-20 rounded-lg backdrop-blur-sm">
                            {{ auth()->user()->role }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4 rounded-xl shadow-lg flex items-center space-x-3 rtl:space-x-reverse animate-fade-in">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4 rounded-xl shadow-lg flex items-center space-x-3 rtl:space-x-reverse animate-fade-in">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Avatar Upload Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                        <h2 class="text-xl font-bold flex items-center">
                            <svg class="w-6 h-6 mr-2 rtl:ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('الصورة الشخصية') }}
                        </h2>
                    </div>

                    <div class="p-6">
                        <form action="{{ route('profile.update-avatar') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                            @csrf

                            <div class="flex flex-col items-center">
                                <div class="w-40 h-40 rounded-full bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center mb-4 shadow-lg overflow-hidden" id="avatar-preview-container">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}" alt="Avatar" class="w-40 h-40 rounded-full object-cover" id="avatar-preview">
                                    @else
                                        <svg class="w-20 h-20 text-gray-400" fill="currentColor" viewBox="0 0 20 20" id="avatar-placeholder">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                        </svg>
                                    @endif
                                </div>

                                <label class="w-full">
                                    <div class="flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-sky-400 to-sky-600 rounded-xl cursor-pointer hover:from-sky-500 hover:to-sky-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                                        <svg class="w-5 h-5 mr-2 rtl:ml-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5.5 13a3.5 3.5 0 01-.369-6.98 4 4 0 117.753-1.977A4.5 4.5 0 1113.5 13H11V9.413l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13H5.5z"></path>
                                        </svg>
                                        {{ __('اختر صورة') }}
                                    </div>
                                    <input type="file" name="avatar" accept="image/*" class="hidden" id="avatar-input" onchange="previewAvatar(event)">
                                </label>
                                @error('avatar')
                                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-sky-400 to-sky-600 rounded-xl font-semibold hover:from-sky-500 hover:to-sky-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                                {{ __('حفظ الصورة') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- User Info Card -->
                <div class="mt-8 bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl shadow-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">{{ __('معلومات الحساب') }}</h3>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm">
                            <span class="text-gray-600 w-24">{{ __('الدور:') }}</span>
                            <span class="font-semibold text-gray-800">{{ auth()->user()->role }}</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <span class="text-gray-600 w-24">{{ __('انضم في:') }}</span>
                            <span class="font-semibold text-gray-800">{{ auth()->user()->created_at->format('Y-m-d') }}</span>
                        </div>
                        @if(auth()->user()->sso_provider)
                        <div class="flex items-center text-sm">
                            <span class="text-gray-600 w-24">{{ __('SSO:') }}</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-lg text-xs font-semibold">{{ auth()->user()->sso_provider }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Personal Information -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-blue-500 to-cyan-500 px-6 py-4">
                        <h2 class="text-xl font-bold flex items-center">
                            <svg class="w-6 h-6 mr-2 rtl:ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('المعلومات الشخصية') }}
                        </h2>
                    </div>

                    <div class="p-6">
                        <form action="{{ route('profile.update') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('الاسم') }}</label>
                                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-300 @error('name') border-red-500 @enderror">
                                    @error('name')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('البريد الإلكتروني') }}</label>
                                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-300 @error('email') border-red-500 @enderror">
                                    @error('email')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('رقم الهاتف') }}</label>
                                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-300 @error('phone') border-red-500 @enderror">
                                    @error('phone')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('الرقم الوطني') }}</label>
                                    <input type="text" name="national_id" value="{{ old('national_id', auth()->user()->national_id) }}"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring focus:ring-blue-200 transition-all duration-300 @error('national_id') border-red-500 @enderror">
                                    @error('national_id')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-sky-400 to-sky-600 rounded-xl font-semibold hover:from-sky-500 hover:to-sky-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                                    {{ __('حفظ التغييرات') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Password Change -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-4">
                        <h2 class="text-xl font-bold flex items-center">
                            <svg class="w-6 h-6 mr-2 rtl:ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            {{ __('تغيير كلمة المرور') }}
                        </h2>
                    </div>

                    <div class="p-6">
                        <form action="{{ route('profile.update-password') }}" method="POST" class="space-y-6">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('كلمة المرور الحالية') }}</label>
                                <input type="password" name="current_password"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all duration-300 @error('current_password') border-red-500 @enderror">
                                @error('current_password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('كلمة المرور الجديدة') }}</label>
                                    <input type="password" name="password"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all duration-300 @error('password') border-red-500 @enderror">
                                    @error('password')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('تأكيد كلمة المرور') }}</label>
                                    <input type="password" name="password_confirmation"
                                        class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-orange-500 focus:ring focus:ring-orange-200 transition-all duration-300">
                                </div>
                            </div>

                            <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-l-4 border-orange-500 p-4 rounded-lg">
                                <div class="flex">
                                    <svg class="w-5 h-5 text-orange-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="mr-3 rtl:ml-3">
                                        <p class="text-sm text-orange-800 font-semibold">{{ __('متطلبات كلمة المرور:') }}</p>
                                        <ul class="text-sm text-orange-700 mt-1 list-disc list-inside">
                                            <li>{{ __('على الأقل 8 أحرف') }}</li>
                                            <li>{{ __('يفضل استخدام حروف كبيرة وصغيرة وأرقام') }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end pt-4">
                                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-sky-400 to-sky-600 rounded-xl font-semibold hover:from-sky-500 hover:to-sky-700 transition-all duration-300 shadow-lg hover:shadow-xl">
                                    {{ __('تحديث كلمة المرور') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('avatar-preview-container');
            const placeholder = document.getElementById('avatar-placeholder');

            if (placeholder) {
                placeholder.remove();
            }

            let preview = document.getElementById('avatar-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'avatar-preview';
                preview.className = 'w-40 h-40 rounded-full object-cover';
                container.appendChild(preview);
            }

            preview.src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
}
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}
</style>
@endsection
