@extends('layouts.admin')

@section('title', 'تفاصيل الطلب - ' . $user->name)

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.pending-users.index') }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 text-sm font-medium mb-2 inline-block">
                ← العودة للطلبات المعلقة
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تفاصيل طلب التسجيل</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">مراجعة بيانات المستخدم قبل القبول أو الرفض</p>
        </div>
        <div class="flex gap-3">
            <form action="{{ route('admin.pending-users.approve', $user) }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                        onclick="return confirm('هل أنت متأكد من قبول هذا المستخدم؟')"
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-colors shadow-lg">
                    قبول الطلب
                </button>
            </form>
            <form action="{{ route('admin.pending-users.reject', $user) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit"
                        onclick="return confirm('هل أنت متأكد من رفض وحذف هذا المستخدم؟')"
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-colors shadow-lg">
                    رفض الطلب
                </button>
            </form>
        </div>
    </div>

    <!-- User Information Cards -->
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
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">نوع المستخدم</label>
                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 rounded-lg text-sm">
                            {{ $user->role === 'student' ? 'طالب' : $user->role }}
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

        <!-- Registration Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                معلومات التسجيل
            </h2>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">تاريخ التسجيل</label>
                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">{{ $user->created_at->format('Y-m-d H:i') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">الحالة</label>
                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 rounded-lg text-sm">
                            قيد الانتظار
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
