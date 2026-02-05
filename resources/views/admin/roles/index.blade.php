@extends('layouts.dashboard')

@section('title', 'إدارة الأدوار')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Modern Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2.5 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 1L3 5V9C3 13.55 5.99 17.74 10 19C14.01 17.74 17 13.55 17 9V5L10 1ZM10 9.99H15C14.47 13.11 12.54 15.87 10 16.9V10H5V6.3L10 3.69V9.99Z"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">إدارة الأدوار والصلاحيات</h1>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mr-14">تحكم في أدوار المستخدمين وصلاحياتهم بسهولة</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.permissions.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:border-indigo-500 dark:hover:border-indigo-500 hover:text-indigo-600 dark:hover:text-indigo-400 transition-all shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 1L3 5V9C3 13.55 5.99 17.74 10 19C14.01 17.74 17 13.55 17 9V5L10 1ZM10 9.99H15C14.47 13.11 12.54 15.87 10 16.9V10H5V6.3L10 3.69V9.99Z"/>
                        </svg>
                        الصلاحيات
                    </a>
                    <a href="{{ route('admin.roles.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        دور جديد
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 dark:bg-green-900/20 border-r-4 border-green-500 text-green-800 dark:text-green-400 px-6 py-4 rounded-lg shadow-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border-r-4 border-red-500 text-red-800 dark:text-red-400 px-6 py-4 rounded-lg shadow-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
        @endif

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Total Roles -->
            <div class="relative overflow-hidden bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-indigo-100 mb-1">إجمالي الأدوار</p>
                    <p class="text-4xl font-bold text-white">{{ $totalRoles }}</p>
                </div>
            </div>

            <!-- Total Permissions -->
            <div class="relative overflow-hidden bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 1L3 5V9C3 13.55 5.99 17.74 10 19C14.01 17.74 17 13.55 17 9V5L10 1ZM10 9.99H15C14.47 13.11 12.54 15.87 10 16.9V10H5V6.3L10 3.69V9.99Z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-purple-100 mb-1">إجمالي الصلاحيات</p>
                    <p class="text-4xl font-bold text-white">{{ $totalPermissions }}</p>
                </div>
            </div>

            <!-- Active Roles -->
            <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-emerald-100 mb-1">أدوار مستخدمة</p>
                    <p class="text-4xl font-bold text-white">{{ $rolesWithUsers }}</p>
                </div>
            </div>

            <!-- Average Permissions -->
            <div class="relative overflow-hidden bg-gradient-to-br from-orange-500 to-amber-600 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                <div class="relative p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm font-medium text-orange-100 mb-1">متوسط الصلاحيات</p>
                    <p class="text-4xl font-bold text-white">{{ number_format($avgPermissionsPerRole ?? 0, 1) }}</p>
                </div>
            </div>
        </div>

        <!-- Roles Grid/Cards View -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($roles as $role)
            @php
                // Assign unique colors to each role
                $gradients = [
                    'from-indigo-500 to-purple-600',
                    'from-purple-500 to-pink-600',
                    'from-blue-500 to-cyan-600',
                    'from-emerald-500 to-teal-600',
                    'from-orange-500 to-red-600',
                    'from-pink-500 to-rose-600',
                    'from-cyan-500 to-blue-600',
                    'from-amber-500 to-orange-600',
                ];
                $colorIndex = $loop->index % count($gradients);
                $gradientClass = $gradients[$colorIndex];
            @endphp

            <div class="group bg-gradient-to-br {{ $gradientClass }} rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden transform hover:scale-105">
                <!-- Card Header with Gradient -->
                <div class="relative h-32 p-6">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                    <div class="relative flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 bg-white/30 backdrop-blur-md rounded-xl flex items-center justify-center ring-2 ring-white/50 shadow-xl">
                                <span class="text-2xl font-bold text-white">{{ mb_substr($role->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-white drop-shadow-md line-clamp-1">{{ $role->name }}</h3>
                                <p class="text-xs text-white/90 mt-1 font-medium">{{ $role->guard_name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-sm p-6">
                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div class="text-center p-4 bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-xl border-2 border-indigo-200 dark:border-indigo-700">
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $role->permissions->count() }}</p>
                            <p class="text-xs text-indigo-700 dark:text-indigo-300 mt-1 font-semibold">صلاحية</p>
                        </div>
                        <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/30 dark:to-pink-900/30 rounded-xl border-2 border-purple-200 dark:border-purple-700">
                            @php $userCount = $role->users()->count(); @endphp
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $userCount }}</p>
                            <p class="text-xs text-purple-700 dark:text-purple-300 mt-1 font-semibold">مستخدم</p>
                        </div>
                    </div>

                    <!-- Permissions Preview -->
                    <div class="mb-5">
                        <p class="text-xs font-bold text-indigo-600 dark:text-indigo-400 uppercase mb-3 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 1L3 5V9C3 13.55 5.99 17.74 10 19C14.01 17.74 17 13.55 17 9V5L10 1Z"/>
                            </svg>
                            الصلاحيات
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @forelse($role->permissions->take(4) as $permission)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm {{ \App\Helpers\PermissionHelper::getPermissionColor($permission->name) }}">
                                {{ \App\Helpers\PermissionHelper::translatePermission($permission->name) }}
                            </span>
                            @empty
                            <span class="text-xs text-gray-400 dark:text-gray-500 italic">لا توجد صلاحيات</span>
                            @endforelse
                            @if($role->permissions->count() > 4)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-gray-200 to-gray-300 text-gray-700 dark:from-gray-700 dark:to-gray-600 dark:text-gray-300 shadow-sm">
                                +{{ $role->permissions->count() - 4 }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Meta Info -->
                    <div class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 mb-5 pb-5 border-b-2 border-indigo-100 dark:border-indigo-900">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ $role->created_at->diffForHumans() }}</span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.roles.show', $role) }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            عرض
                        </a>
                        <a href="{{ route('admin.roles.edit', $role) }}"
                           class="p-3 bg-gradient-to-br from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 text-white rounded-xl transition-all shadow-md hover:shadow-lg"
                           title="تعديل">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="p-3 bg-gradient-to-br from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white rounded-xl transition-all shadow-md hover:shadow-lg"
                                    title="حذف">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 dark:from-indigo-900/20 dark:via-purple-900/20 dark:to-pink-900/20 rounded-3xl shadow-lg border-2 border-indigo-200 dark:border-indigo-700 p-12">
                    <div class="text-center max-w-md mx-auto">
                        <div class="w-24 h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">لا توجد أدوار حتى الآن</h3>
                        <p class="text-gray-700 dark:text-gray-300 mb-8 text-lg">ابدأ بإنشاء دور جديد لإدارة صلاحيات المستخدمين</p>
                        <a href="{{ route('admin.roles.create') }}"
                           class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold rounded-xl transition-all shadow-xl hover:shadow-2xl transform hover:scale-105">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            إنشاء دور جديد
                        </a>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($roles->hasPages())
        <div class="mt-8">
            {{ $roles->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
