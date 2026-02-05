@extends('layouts.dashboard')

@section('title', 'إضافة معلم جديد')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.teachers.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إضافة معلم جديد</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">إضافة معلم جديد إلى النظام</p>
</div>

@if($errors->any())
<div class="mb-4 rounded-lg bg-error-50 p-4 dark:bg-error-900">
    <ul class="list-disc list-inside text-sm text-error-600 dark:text-error-200">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.teachers.store') }}" method="POST">
    @csrf

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- الاسم -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الاسم الكامل <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="الاسم الكامل للمعلم">
            </div>

            <!-- البريد الإلكتروني -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    البريد الإلكتروني <span class="text-error-500">*</span>
                </label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="example@email.com">
            </div>

            <!-- رقم الهوية -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رقم الهوية <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="national_id"
                       value="{{ old('national_id') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="رقم الهوية الوطنية">
            </div>

            <!-- رقم الهاتف -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رقم الهاتف
                </label>
                <input type="text"
                       name="phone"
                       value="{{ old('phone') }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="05xxxxxxxx">
            </div>

            <!-- كلمة المرور -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    كلمة المرور <span class="text-error-500">*</span>
                </label>
                <input type="password"
                       name="password"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="كلمة المرور (8 أحرف على الأقل)">
            </div>

            <!-- تأكيد كلمة المرور -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    تأكيد كلمة المرور <span class="text-error-500">*</span>
                </label>
                <input type="password"
                       name="password_confirmation"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="أعد كتابة كلمة المرور">
            </div>
        </div>

        <!-- الأزرار -->
        <div class="mt-6 flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-800">
            <a href="{{ route('admin.teachers.index') }}"
               class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                إلغاء
            </a>
            <button type="submit"
                    class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                إضافة المعلم
            </button>
        </div>
    </div>
</form>
@endsection
