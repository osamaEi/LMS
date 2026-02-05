@extends('layouts.dashboard')

@section('title', 'الدور: ' . $role->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">الرئيسية</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.roles.index') }}" class="hover:text-blue-600">الأدوار</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-900 dark:text-white">{{ $role->name }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $role->name }}</h1>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <a href="{{ route('admin.roles.edit', $role) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    تعديل
                </a>
                <a href="{{ route('admin.roles.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition">
                    رجوع
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Role Info -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="text-center mb-6">
                        <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $role->name }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Guard: {{ $role->guard_name }}</p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-sm text-gray-600 dark:text-gray-400">عدد الصلاحيات</span>
                            <span class="text-lg font-bold text-blue-600 dark:text-blue-400">{{ $role->permissions->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-sm text-gray-600 dark:text-gray-400">عدد المستخدمين</span>
                            <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ $role->users->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="text-sm text-gray-600 dark:text-gray-400">تاريخ الإنشاء</span>
                            <span class="text-sm text-gray-900 dark:text-white">{{ $role->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions & Users -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Permissions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 1L3 5V9C3 13.55 5.99 17.74 10 19C14.01 17.74 17 13.55 17 9V5L10 1ZM10 9.99H15C14.47 13.11 12.54 15.87 10 16.9V10H5V6.3L10 3.69V9.99Z"/>
                        </svg>
                        الصلاحيات المرتبطة ({{ $role->permissions->count() }})
                    </h3>
                    @if($role->permissions->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($role->permissions as $permission)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium {{ \App\Helpers\PermissionHelper::getPermissionColor($permission->name) }}">
                            <svg class="w-3.5 h-3.5 ml-1.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ \App\Helpers\PermissionHelper::translatePermission($permission->name) }}
                        </span>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <p class="text-gray-500 dark:text-gray-400">لا توجد صلاحيات مرتبطة بهذا الدور</p>
                    </div>
                    @endif
                </div>

                <!-- Users -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">المستخدمون بهذا الدور</h3>
                    @if($role->users->count() > 0)
                    <div class="space-y-3">
                        @foreach($role->users->take(10) as $user)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                        {{ mb_substr($user->name, 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="text-sm text-blue-600 hover:underline">عرض</a>
                        </div>
                        @endforeach
                        @if($role->users->count() > 10)
                        <p class="text-sm text-gray-500 dark:text-gray-400 text-center">
                            و {{ $role->users->count() - 10 }} مستخدم آخر...
                        </p>
                        @endif
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400">لا يوجد مستخدمون بهذا الدور</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
