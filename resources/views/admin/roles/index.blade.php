@extends('layouts.dashboard')

@section('title', 'إدارة الأدوار')

@section('content')
<div class="min-h-screen py-6" style="background: linear-gradient(135deg, #f0f9ff, #ede9fe);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2.5 rounded-xl shadow-lg" style="background: #3b82f6;">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 1L3 5V9C3 13.55 5.99 17.74 10 19C14.01 17.74 17 13.55 17 9V5L10 1ZM10 9.99H15C14.47 13.11 12.54 15.87 10 16.9V10H5V6.3L10 3.69V9.99Z"/>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold" style="color: #1e3a5f;">إدارة الأدوار والصلاحيات</h1>
                    </div>
                    <p class="mr-14 font-medium" style="color: #64748b;">تحكم في أدوار المستخدمين وصلاحياتهم بسهولة</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.permissions.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-white font-semibold rounded-xl transition-all shadow-sm hover:shadow-md border-2" style="color: #3b82f6; border-color: #93c5fd;">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 1L3 5V9C3 13.55 5.99 17.74 10 19C14.01 17.74 17 13.55 17 9V5L10 1ZM10 9.99H15C14.47 13.11 12.54 15.87 10 16.9V10H5V6.3L10 3.69V9.99Z"/>
                        </svg>
                        الصلاحيات
                    </a>
                    <a href="{{ route('admin.roles.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-105" style="background: #3b82f6;">
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
        <div class="mb-6 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-4" style="background: #22c55e;">
            <div class="p-2 rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="font-semibold">{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 text-white px-6 py-4 rounded-2xl shadow-lg flex items-center gap-4" style="background: #f97316;">
            <div class="p-2 rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <p class="font-semibold">{{ session('error') }}</p>
        </div>
        @endif

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all" style="background: #3b82f6;">
                <div class="absolute top-0 right-0 w-32 h-32 rounded-full -mr-16 -mt-16" style="background: rgba(255,255,255,0.1);"></div>
                <div class="relative p-6">
                    <div class="p-3 rounded-xl mb-4 inline-block" style="background: rgba(255,255,255,0.2);">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium mb-1" style="color: #dbeafe;">إجمالي الأدوار</p>
                    <p class="text-4xl font-bold text-white">{{ $totalRoles }}</p>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all" style="background: #22c55e;">
                <div class="absolute top-0 right-0 w-32 h-32 rounded-full -mr-16 -mt-16" style="background: rgba(255,255,255,0.1);"></div>
                <div class="relative p-6">
                    <div class="p-3 rounded-xl mb-4 inline-block" style="background: rgba(255,255,255,0.2);">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 1L3 5V9C3 13.55 5.99 17.74 10 19C14.01 17.74 17 13.55 17 9V5L10 1ZM10 9.99H15C14.47 13.11 12.54 15.87 10 16.9V10H5V6.3L10 3.69V9.99Z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium mb-1" style="color: #dcfce7;">إجمالي الصلاحيات</p>
                    <p class="text-4xl font-bold text-white">{{ $totalPermissions }}</p>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all" style="background: #f97316;">
                <div class="absolute top-0 right-0 w-32 h-32 rounded-full -mr-16 -mt-16" style="background: rgba(255,255,255,0.1);"></div>
                <div class="relative p-6">
                    <div class="p-3 rounded-xl mb-4 inline-block" style="background: rgba(255,255,255,0.2);">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium mb-1" style="color: #ffedd5;">أدوار مستخدمة</p>
                    <p class="text-4xl font-bold text-white">{{ $rolesWithUsers }}</p>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl shadow-lg hover:shadow-xl transition-all" style="background: #3b82f6;">
                <div class="absolute top-0 right-0 w-32 h-32 rounded-full -mr-16 -mt-16" style="background: rgba(255,255,255,0.1);"></div>
                <div class="relative p-6">
                    <div class="p-3 rounded-xl mb-4 inline-block" style="background: rgba(255,255,255,0.2);">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium mb-1" style="color: #dbeafe;">متوسط الصلاحيات</p>
                    <p class="text-4xl font-bold text-white">{{ number_format($avgPermissionsPerRole ?? 0, 1) }}</p>
                </div>
            </div>
        </div>

        <!-- Roles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($roles as $role)
            @php
                $colors = [
                    ['bg' => '#3b82f6', 'light' => '#dbeafe'],
                    ['bg' => '#22c55e', 'light' => '#dcfce7'],
                    ['bg' => '#f97316', 'light' => '#ffedd5'],
                ];
                $color = $colors[$loop->index % count($colors)];
            @endphp

            <div class="rounded-2xl shadow-lg hover:shadow-2xl transition-all overflow-hidden transform hover:scale-105 bg-white">
                <!-- Card Header -->
                <div class="relative p-6" style="background: {{ $color['bg'] }};">
                    <div class="absolute top-0 left-0 w-24 h-24 rounded-full -ml-12 -mt-12" style="background: rgba(255,255,255,0.1);"></div>
                    <div class="relative flex items-center gap-3">
                        <div class="w-14 h-14 rounded-xl flex items-center justify-center shadow-xl" style="background: rgba(255,255,255,0.25);">
                            <span class="text-2xl font-bold text-white">{{ mb_substr($role->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white line-clamp-1">{{ $role->name }}</h3>
                            <p class="text-xs font-medium mt-1" style="color: rgba(255,255,255,0.8);">{{ $role->guard_name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-6">
                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div class="text-center p-4 rounded-xl border-2" style="background: {{ $color['light'] }}; border-color: {{ $color['bg'] }}30;">
                            <p class="text-2xl font-bold" style="color: {{ $color['bg'] }};">{{ $role->permissions->count() }}</p>
                            <p class="text-xs mt-1 font-semibold" style="color: #64748b;">صلاحية</p>
                        </div>
                        <div class="text-center p-4 rounded-xl border-2" style="background: {{ $color['light'] }}; border-color: {{ $color['bg'] }}30;">
                            @php $userCount = $role->users()->count(); @endphp
                            <p class="text-2xl font-bold" style="color: {{ $color['bg'] }};">{{ $userCount }}</p>
                            <p class="text-xs mt-1 font-semibold" style="color: #64748b;">مستخدم</p>
                        </div>
                    </div>

                    <!-- Permissions Preview -->
                    <div class="mb-5">
                        <p class="text-xs font-bold uppercase mb-3 flex items-center gap-1" style="color: {{ $color['bg'] }};">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 1L3 5V9C3 13.55 5.99 17.74 10 19C14.01 17.74 17 13.55 17 9V5L10 1Z"/>
                            </svg>
                            الصلاحيات
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @forelse($role->permissions->take(4) as $permission)
                            @php
                                $permColors = ['#3b82f6', '#22c55e', '#f97316'];
                                $permColor = $permColors[$loop->index % 3];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-white shadow-sm" style="background: {{ $permColor }};">
                                {{ \App\Helpers\PermissionHelper::translatePermission($permission->name) }}
                            </span>
                            @empty
                            <span class="text-xs italic" style="color: #94a3b8;">لا توجد صلاحيات</span>
                            @endforelse
                            @if($role->permissions->count() > 4)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm" style="background: #e2e8f0; color: #475569;">
                                +{{ $role->permissions->count() - 4 }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Meta -->
                    <div class="flex items-center gap-2 text-xs mb-5 pb-5" style="color: {{ $color['bg'] }}; border-bottom: 2px solid {{ $color['light'] }};">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="font-medium">{{ $role->created_at->diffForHumans() }}</span>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.roles.show', $role) }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 text-white font-semibold rounded-xl transition-all shadow-md hover:shadow-lg" style="background: #3b82f6;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            عرض
                        </a>
                        <a href="{{ route('admin.roles.edit', $role) }}"
                           class="p-3 text-white rounded-xl transition-all shadow-md hover:shadow-lg" style="background: #22c55e;"
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
                                    class="p-3 text-white rounded-xl transition-all shadow-md hover:shadow-lg" style="background: #f97316;"
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
                <div class="bg-white rounded-3xl shadow-lg border-2 p-12" style="border-color: #93c5fd;">
                    <div class="text-center max-w-md mx-auto">
                        <div class="w-24 h-24 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl" style="background: #3b82f6;">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-3" style="color: #1e3a5f;">لا توجد أدوار حتى الآن</h3>
                        <p class="mb-8 text-lg" style="color: #64748b;">ابدأ بإنشاء دور جديد لإدارة صلاحيات المستخدمين</p>
                        <a href="{{ route('admin.roles.create') }}"
                           class="inline-flex items-center gap-3 px-8 py-4 text-white font-bold rounded-xl transition-all shadow-xl hover:shadow-2xl transform hover:scale-105" style="background: #3b82f6;">
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
