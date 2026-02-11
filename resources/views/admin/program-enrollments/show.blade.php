@extends('layouts.dashboard')

@section('title', 'تفاصيل طلب التسجيل - ' . $user->name)

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.program-enrollments.index') }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm font-medium mb-2 inline-block">
                ← العودة للطلبات المعلقة
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تفاصيل طلب التسجيل في البرنامج</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">مراجعة بيانات الطالب وطلب التسجيل في البرنامج</p>
        </div>
        <div class="flex gap-3">
            <form action="{{ route('admin.program-enrollments.approve', $user) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        onclick="return confirm('هل أنت متأكد من قبول طلب التسجيل لهذا الطالب؟')"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-colors shadow-lg">
                    قبول الطلب
                </button>
            </form>
            <form action="{{ route('admin.program-enrollments.reject', $user) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        onclick="return confirm('هل أنت متأكد من رفض طلب التسجيل؟ سيتم إزالة البرنامج من حساب الطالب.')"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-colors shadow-lg">
                    رفض الطلب
                </button>
            </form>
        </div>
    </div>

    <!-- Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                المعلومات الشخصية
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">الاسم الكامل</label>
                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">رقم الهوية الوطنية</label>
                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $user->national_id ?? 'غير متوفر' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">حالة الحساب</label>
                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                        <span class="px-3 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 rounded-lg text-sm">
                            نشط
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                معلومات الاتصال
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">البريد الإلكتروني</label>
                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">رقم الجوال</label>
                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $user->phone ?? 'غير متوفر' }}</p>
                </div>
            </div>
        </div>

        <!-- Program Request Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                البرنامج المطلوب
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">اسم البرنامج</label>
                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $user->program->name ?? 'غير محدد' }}</p>
                </div>
                @if($user->program && $user->program->code)
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">رمز البرنامج</label>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $user->program->code }}</p>
                    </div>
                @endif
                @if($user->program && $user->program->duration_months)
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">مدة البرنامج</label>
                        <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $user->program->duration_months }} شهر</p>
                    </div>
                @endif
                <div>
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">حالة الطلب</label>
                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 rounded-lg text-sm">
                            قيد الانتظار
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Request Date Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                معلومات الطلب
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">تاريخ الطلب</label>
                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $user->updated_at->format('Y-m-d H:i') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $user->updated_at->diffForHumans() }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">تاريخ إنشاء الحساب</label>
                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $user->created_at->format('Y-m-d H:i') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <!-- Nafath Verification -->
        @if($user->nafath_verified_at)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    التحقق عبر نفاذ
                </h2>
                <div class="flex items-center gap-2 text-green-600 dark:text-green-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="font-medium">تم التحقق من الهوية عبر نفاذ</span>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-400">تاريخ التحقق</label>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $user->nafath_verified_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @if($user->nafath_transaction_id)
                        <div>
                            <label class="text-sm font-medium text-gray-600 dark:text-gray-400">معرف المعاملة</label>
                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 font-mono">{{ $user->nafath_transaction_id }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
