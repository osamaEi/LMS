@extends('layouts.dashboard')

@section('title', 'المستخدم: ' . $user->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <nav class="flex items-center text-sm text-gray-500 dark:text-gray-400 mb-2">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">الرئيسية</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.users.index') }}" class="hover:text-blue-600">المستخدمين</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-900 dark:text-white">{{ $user->name }}</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">معلومات المستخدم</h1>
            </div>
            <div class="mt-4 sm:mt-0 flex gap-2">
                <a href="{{ route('admin.users.edit', $user) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    تعديل
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition">
                    رجوع
                </a>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="text-center mb-6">
                        <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl font-bold text-white">
                                {{ mb_substr($user->name, 0, 2) }}
                            </span>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>

                        <div class="mt-4 flex justify-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $user->role == 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' : '' }}
                                {{ $user->role == 'teacher' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : '' }}
                                {{ $user->role == 'student' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                {{ $user->role == 'super_admin' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : '' }}">
                                {{ $user->getRoleDisplayName() }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $user->status == 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : '' }}
                                {{ $user->status == 'inactive' ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' : '' }}
                                {{ $user->status == 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : '' }}
                                {{ $user->status == 'suspended' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' : '' }}">
                                {{ $user->getStatusDisplayName() }}
                            </span>
                        </div>
                    </div>

                    <div class="space-y-4 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">رقم الهاتف</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->phone ?? 'غير محدد' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">تاريخ التسجيل</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">آخر تحديث</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->updated_at->format('Y-m-d') }}</span>
                        </div>
                        @if($user->email_verified_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">تأكيد البريد</span>
                            <span class="inline-flex items-center gap-1 text-sm font-medium text-green-600 dark:text-green-400">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                مؤكد
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Roles & Permissions -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Spatie Roles -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">الأدوار المعينة</h3>
                    </div>
                    @if($user->roles->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($user->roles as $role)
                        <div class="inline-flex items-center gap-2 px-4 py-2 {{ \App\Helpers\PermissionHelper::getRoleColor($role->name) }} rounded-lg">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm font-medium">{{ \App\Helpers\PermissionHelper::translateRole($role->name) }}</span>
                            <form action="{{ route('admin.users.remove-role', [$user, $role]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="hover:opacity-70 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400">لا توجد أدوار معينة لهذا المستخدم</p>
                    @endif
                </div>

                <!-- Direct Permissions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">الصلاحيات</h3>
                    @php
                        $allPermissions = $user->getAllPermissions();
                    @endphp
                    @if($allPermissions->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($allPermissions as $permission)
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium {{ \App\Helpers\PermissionHelper::getPermissionColor($permission->name) }}">
                            {{ \App\Helpers\PermissionHelper::translatePermission($permission->name) }}
                        </span>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400">لا توجد صلاحيات (مباشرة أو من خلال الأدوار)</p>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">إجراءات سريعة</h3>
                    <div class="flex flex-wrap gap-3">
                        @if($user->id !== auth()->id())
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 {{ $user->status == 'active' ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }} rounded-lg transition">
                                @if($user->status == 'active')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                تعطيل الحساب
                                @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                تفعيل الحساب
                                @endif
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-100 text-blue-800 hover:bg-blue-200 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            تعديل البيانات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
