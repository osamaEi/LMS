@extends('layouts.dashboard')

@section('title', 'تعديل الصلاحية: ' . \App\Helpers\PermissionHelper::translatePermission($permission->name))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">الرئيسية</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.permissions.index') }}" class="hover:text-blue-600">الصلاحيات</a>
                <span class="mx-2">/</span>
                <span class="text-gray-900 dark:text-white">تعديل: {{ \App\Helpers\PermissionHelper::translatePermission($permission->name) }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل الصلاحية</h1>
        </div>

        <!-- Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('admin.permissions.update', $permission) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Permission Name -->
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        اسم الصلاحية <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $permission->name) }}"
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                           required>
                    @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.permissions.index') }}"
                       class="px-6 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
