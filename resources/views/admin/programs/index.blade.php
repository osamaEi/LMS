@extends('layouts.dashboard')

@section('title', 'إدارة المسارات التعليمية')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">المسارات التعليمية</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة جميع المسارات التعليمية في النظام</p>
    </div>
    <a href="{{ route('admin.programs.create') }}"
       class="flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        إضافة مسار جديد
    </a>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900 dark:text-green-200">
    {{ session('success') }}
</div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
    <div class="rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ $stats['total'] }}</div>
                <div class="text-sm text-white/80 mt-1">إجمالي المسارات</div>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
        </div>
    </div>
    <div class="rounded-xl bg-gradient-to-br from-green-500 to-green-700 p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ $stats['active'] }}</div>
                <div class="text-sm text-white/80 mt-1">مسارات نشطة</div>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
    <div class="rounded-xl bg-gradient-to-br from-gray-500 to-gray-700 p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ $stats['inactive'] }}</div>
                <div class="text-sm text-white/80 mt-1">غير نشطة</div>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Programs Cards -->
@if($programs->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($programs as $program)
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden hover:shadow-lg transition-shadow">
        <!-- Card Header with gradient -->
        <div class="h-24 bg-gradient-to-br from-indigo-500 to-indigo-700 relative">
            <div class="absolute top-4 right-4">
                @if($program->status === 'active')
                    <span class="rounded-full bg-white/20 backdrop-blur-sm px-3 py-1 text-xs font-medium text-white">نشط</span>
                @else
                    <span class="rounded-full bg-white/20 backdrop-blur-sm px-3 py-1 text-xs font-medium text-white/70">غير نشط</span>
                @endif
            </div>
            <div class="absolute -bottom-6 right-6">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center bg-white dark:bg-gray-800 shadow-lg border-2 border-white dark:border-gray-700">
                    <svg class="w-7 h-7 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6 pt-10">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-1">{{ $program->name_ar }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $program->name_en }}</p>
                </div>
                <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $program->code }}</span>
            </div>

            @if($program->description_ar)
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">{{ $program->description_ar }}</p>
            @endif

            <!-- Stats Row -->
            <div class="flex items-center gap-4 py-4 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $program->duration_months ?? '-' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">شهر</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $program->price ? number_format($program->price) : '-' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">ريال</div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $program->terms_count }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">فصل</div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-2 pt-4 border-t border-gray-100 dark:border-gray-800">
                <a href="{{ route('admin.programs.show', $program) }}"
                   class="flex-1 flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    عرض التفاصيل
                </a>
                <a href="{{ route('admin.programs.edit', $program) }}"
                   class="p-2.5 rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors"
                   title="تعديل">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>
                <form action="{{ route('admin.programs.destroy', $program) }}" method="POST" class="inline"
                      onsubmit="return confirm('هل أنت متأكد من حذف هذا المسار؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="p-2.5 rounded-lg border border-gray-200 text-red-500 hover:bg-red-50 dark:border-gray-700 dark:hover:bg-red-900/20 transition-colors"
                            title="حذف">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-12 text-center">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">لا توجد مسارات تعليمية</p>
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ابدأ بإضافة مسار تعليمي جديد</p>
    <a href="{{ route('admin.programs.create') }}"
       class="mt-4 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        إضافة مسار جديد
    </a>
</div>
@endif

@if($programs->hasPages())
<div class="mt-6">
    {{ $programs->links() }}
</div>
@endif
@endsection
