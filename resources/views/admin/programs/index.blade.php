@extends('layouts.dashboard')

@section('title', 'إدارة المسارات التعليمية')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">المسارات التعليمية</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة جميع المسارات التعليمية في النظام</p>
    </div>
    <a href="{{ route('admin.programs.create') }}"
       class="flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
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
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6">
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
    <div class="rounded-xl bg-gradient-to-br from-blue-400 to-blue-600 p-5 text-white shadow-lg">
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
    <div class="rounded-xl bg-gradient-to-br from-blue-600 to-blue-800 p-5 text-white shadow-lg">
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
    <div class="rounded-xl bg-gradient-to-br from-sky-500 to-sky-700 p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ $stats['diploma'] }}</div>
                <div class="text-sm text-white/80 mt-1">برامج دبلوم</div>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
            </div>
        </div>
    </div>
    <div class="rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-3xl font-bold">{{ $stats['training'] }}</div>
                <div class="text-sm text-white/80 mt-1">برامج تدريبية</div>
            </div>
            <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Programs Table -->
<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800">
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">المسار</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">الكود</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">النوع</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">المدة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">السعر</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">الفصول</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">الحالة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900 dark:text-white">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($programs as $program)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center {{ $program->type === 'diploma' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-purple-100 dark:bg-purple-900/30' }}">
                                <svg class="w-5 h-5 {{ $program->type === 'diploma' ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $program->name }}</div>
                                @if($program->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ Str::limit($program->description, 50) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $program->code }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($program->type === 'diploma')
                            <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">دبلوم</span>
                        @else
                            <span class="rounded-full bg-purple-100 px-2.5 py-1 text-xs font-medium text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">تدريبي</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                        @if($program->duration_months)
                            {{ $program->duration_months }} شهر
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                        @if($program->price)
                            <span class="font-medium text-brand-600 dark:text-brand-400">{{ number_format($program->price) }}</span> ريال
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">{{ $program->terms_count }} فصل</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($program->status === 'active')
                            <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">نشط</span>
                        @else
                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">غير نشط</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.programs.show', $program) }}"
                               class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                               title="عرض">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('admin.programs.edit', $program) }}"
                               class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
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
                                        class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                        title="حذف">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">لا توجد مسارات تعليمية</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ابدأ بإضافة مسار تعليمي جديد</p>
                        <a href="{{ route('admin.programs.create') }}"
                           class="mt-4 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            إضافة مسار جديد
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($programs->hasPages())
<div class="mt-6">
    {{ $programs->links() }}
</div>
@endif
@endsection
