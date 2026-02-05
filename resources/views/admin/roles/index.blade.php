@extends('layouts.dashboard')

@section('title', 'إدارة الأدوار')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-indigo-50/30 to-purple-50/30 dark:from-gray-900 dark:via-gray-900 dark:to-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Modern Header with Gradient -->
        <div class="relative overflow-hidden bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 rounded-2xl shadow-2xl mb-8 animate-gradient">
            <!-- Decorative circles with animation -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-10 rounded-full -translate-x-1/2 -translate-y-1/2 animate-pulse"></div>
            <div class="absolute bottom-0 right-0 w-96 h-96 bg-white opacity-10 rounded-full translate-x-1/3 translate-y-1/3 animate-pulse"></div>
            <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-yellow-300 opacity-10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>

            <div class="relative px-8 py-12">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="mb-6 sm:mb-0">
                        <h1 class="text-3xl font-bold text-white mb-2 flex items-center gap-3">
                            <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm shadow-xl ring-2 ring-white/30">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 1L3 5V9C3 13.55 5.99 17.74 10 19C14.01 17.74 17 13.55 17 9V5L10 1ZM10 9.99H15C14.47 13.11 12.54 15.87 10 16.9V10H5V6.3L10 3.69V9.99Z"/>
                                </svg>
                            </div>
                            إدارة الأدوار والصلاحيات
                        </h1>
                        <p class="text-indigo-100 text-sm mr-16">إنشاء وتعديل أدوار المستخدمين وتخصيص صلاحياتهم</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.roles.create') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-white text-indigo-600 font-semibold rounded-xl hover:bg-indigo-50 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1 hover:scale-105">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            إضافة دور جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-r-4 border-green-500 text-green-700 dark:text-green-400 px-6 py-4 rounded-lg shadow-md flex items-center gap-3 animate-slide-in">
            <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border-r-4 border-red-500 text-red-700 dark:text-red-400 px-6 py-4 rounded-lg shadow-md flex items-center gap-3 animate-slide-in">
            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Roles -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-t-4 border-indigo-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wide">إجمالي الأدوار</p>
                            <p class="text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent mt-2">{{ $totalRoles }}</p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-indigo-500 via-indigo-600 to-purple-600 rounded-2xl shadow-xl transform hover:rotate-12 transition-transform">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Permissions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-t-4 border-purple-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-purple-600 dark:text-purple-400 uppercase tracking-wide">إجمالي الصلاحيات</p>
                            <p class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent mt-2">{{ $totalPermissions }}</p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-purple-500 via-purple-600 to-pink-600 rounded-2xl shadow-xl transform hover:rotate-12 transition-transform">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 1L3 5V9C3 13.55 5.99 17.74 10 19C14.01 17.74 17 13.55 17 9V5L10 1ZM10 9.99H15C14.47 13.11 12.54 15.87 10 16.9V10H5V6.3L10 3.69V9.99Z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles with Users -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-t-4 border-green-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-green-600 dark:text-green-400 uppercase tracking-wide">أدوار مستخدمة</p>
                            <p class="text-4xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent mt-2">{{ $rolesWithUsers }}</p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-green-500 via-green-600 to-emerald-600 rounded-2xl shadow-xl transform hover:rotate-12 transition-transform">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Permissions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border-t-4 border-orange-500">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-orange-600 dark:text-orange-400 uppercase tracking-wide">متوسط الصلاحيات</p>
                            <p class="text-4xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mt-2">{{ number_format($avgPermissionsPerRole ?? 0, 1) }}</p>
                        </div>
                        <div class="p-4 bg-gradient-to-br from-orange-500 via-orange-600 to-red-600 rounded-2xl shadow-xl transform hover:rotate-12 transition-transform">
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roles Table -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
            <!-- Table Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-indigo-50 via-purple-50 to-pink-50 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800 border-b-2 border-indigo-200 dark:border-gray-700">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <div class="p-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">قائمة الأدوار</span>
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-indigo-700 dark:text-indigo-300 uppercase tracking-wider">
                                اسم الدور
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-purple-700 dark:text-purple-300 uppercase tracking-wider">
                                الصلاحيات
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-green-700 dark:text-green-300 uppercase tracking-wider">
                                المستخدمين
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-orange-700 dark:text-orange-300 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($roles as $role)
                        @php
                            // Color gradients for role avatars
                            $gradients = [
                                'from-indigo-500 to-purple-600',
                                'from-purple-500 to-pink-600',
                                'from-blue-500 to-cyan-600',
                                'from-green-500 to-emerald-600',
                                'from-orange-500 to-red-600',
                                'from-pink-500 to-rose-600',
                                'from-cyan-500 to-blue-600',
                                'from-yellow-500 to-orange-600',
                            ];
                            $gradientClass = $gradients[($loop->index) % count($gradients)];
                        @endphp
                        <tr class="hover:bg-gradient-to-r hover:from-indigo-50/50 hover:to-purple-50/50 dark:hover:from-gray-700/50 dark:hover:to-gray-700/50 transition-all duration-200 border-l-4 border-transparent hover:border-l-indigo-500">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-14 h-14 bg-gradient-to-br {{ $gradientClass }} rounded-2xl flex items-center justify-center shadow-xl transform hover:scale-110 transition-transform ring-4 ring-white/50 dark:ring-gray-700/50">
                                        <span class="text-white font-bold text-xl drop-shadow-lg">{{ mb_substr($role->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $role->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1 mt-1">
                                            <svg class="w-3 h-3 text-indigo-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="font-medium">منذ {{ $role->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-wrap gap-2">
                                    @forelse($role->permissions->take(3) as $permission)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold {{ \App\Helpers\PermissionHelper::getPermissionColor($permission->name) }} shadow-md transform hover:scale-105 transition-transform">
                                        {{ \App\Helpers\PermissionHelper::translatePermission($permission->name) }}
                                    </span>
                                    @empty
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic">لا توجد صلاحيات</span>
                                    @endforelse
                                    @if($role->permissions->count() > 3)
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-700 dark:from-indigo-900/50 dark:to-purple-900/50 dark:text-indigo-300 shadow-md">
                                        +{{ $role->permissions->count() - 3 }}
                                    </span>
                                    @endif
                                </div>
                                <div class="mt-2 text-xs">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-indigo-900/30 dark:to-purple-900/30">
                                        <span class="text-gray-600 dark:text-gray-400">المجموع:</span>
                                        <span class="font-bold text-indigo-600 dark:text-indigo-400 mr-1">{{ $role->permissions->count() }}</span>
                                        <span class="text-gray-600 dark:text-gray-400 mr-1">صلاحية</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                @php
                                    $userCount = $role->users()->count();
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-bold {{ $userCount > 0 ? 'bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 dark:from-purple-900/50 dark:to-pink-900/50 dark:text-purple-300 shadow-lg' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }} transform hover:scale-105 transition-transform">
                                        <svg class="w-4 h-4 ml-1.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                        </svg>
                                        {{ $userCount }} مستخدم
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.roles.show', $role) }}"
                                       class="group p-3 text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 rounded-xl transition-all shadow-md hover:shadow-lg transform hover:scale-110"
                                       title="عرض التفاصيل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.roles.edit', $role) }}"
                                       class="group p-3 text-green-600 hover:text-green-700 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 dark:hover:bg-green-900/40 rounded-xl transition-all shadow-md hover:shadow-lg transform hover:scale-110"
                                       title="تعديل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟ سيتم إزالة جميع الصلاحيات المرتبطة به.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="group p-3 text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 rounded-xl transition-all shadow-md hover:shadow-lg transform hover:scale-110"
                                                title="حذف">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-200 dark:from-indigo-900/30 dark:to-purple-900/30 rounded-full flex items-center justify-center mb-6 shadow-xl">
                                        <svg class="w-12 h-12 text-indigo-400 dark:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد أدوار</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">ابدأ بإنشاء دور جديد لتنظيم صلاحيات المستخدمين</p>
                                    <a href="{{ route('admin.roles.create') }}"
                                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        إنشاء دور جديد
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($roles->hasPages())
            <div class="px-6 py-4 border-t-2 border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50">
                {{ $roles->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
@keyframes gradient {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 15s ease infinite;
}

@keyframes slide-in {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slide-in {
    animation: slide-in 0.5s ease-out;
}
</style>
@endsection
