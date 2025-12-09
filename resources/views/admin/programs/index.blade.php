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

<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800">
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">#</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الكود</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">اسم المسار</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">النوع</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">المدة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الفصول الدراسية</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الحالة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($programs as $program)
                <tr class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        <span class="rounded-lg bg-gray-100 px-2 py-1 text-xs font-medium dark:bg-gray-800">
                            {{ $program->code }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $program->name }}</div>
                                @if($program->description)
                                <div class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">{{ $program->description }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        @if($program->type === 'diploma')
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600 dark:bg-blue-900 dark:text-blue-200">دبلوم</span>
                        @else
                            <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-medium text-purple-600 dark:bg-purple-900 dark:text-purple-200">تدريبي</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        @if($program->duration_months)
                            {{ $program->duration_months }} شهر
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium dark:bg-gray-800">
                            {{ $program->terms_count }} فصل
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($program->status === 'active')
                            <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
                        @else
                            <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">غير نشط</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.programs.show', $program) }}"
                               class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-100 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800 transition-colors">
                                عرض
                            </a>
                            <a href="{{ route('admin.programs.edit', $program) }}"
                               class="rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-100 dark:bg-brand-900 dark:text-brand-200 dark:hover:bg-brand-800 transition-colors">
                                تعديل
                            </a>
                            <form action="{{ route('admin.programs.destroy', $program) }}" method="POST" class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المسار؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="rounded-lg bg-error-50 px-3 py-1.5 text-xs font-medium text-error-600 hover:bg-error-100 dark:bg-error-900 dark:text-error-200 dark:hover:bg-error-800 transition-colors">
                                    حذف
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">لا توجد مسارات تعليمية</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ابدأ بإضافة مسار تعليمي جديد</p>
                            <a href="{{ route('admin.programs.create') }}"
                               class="mt-4 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                                إضافة مسار جديد
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($programs->hasPages())
    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        {{ $programs->links() }}
    </div>
    @endif
</div>
@endsection
