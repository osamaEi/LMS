@extends('layouts.dashboard')

@section('title', 'إدارة المواد الدراسية')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">المواد الدراسية</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة جميع المواد الدراسية في النظام</p>
    </div>
    <a href="{{ route('admin.subjects.create') }}"
       class="flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        إضافة مادة جديدة
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
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">اسم المادة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">المعلم</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الفصل/المسار</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الدروس</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الحالة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subjects as $subject)
                <tr class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        <span class="rounded-lg bg-gray-100 px-2 py-1 text-xs font-medium dark:bg-gray-800">
                            {{ $subject->code }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $subject->name }}</div>
                        @if($subject->credits)
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $subject->credits }} ساعة معتمدة</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $subject->teacher->name ?? '-' }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $subject->term->name ?? '-' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $subject->term->program->name ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium dark:bg-gray-800">
                            {{ $subject->sessions_count }} درس
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($subject->status === 'active')
                            <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
                        @elseif($subject->status === 'completed')
                            <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">مكتمل</span>
                        @else
                            <span class="rounded-full bg-yellow-50 px-3 py-1 text-xs font-medium text-yellow-600 dark:bg-yellow-900 dark:text-yellow-200">غير نشط</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.subjects.show', $subject) }}"
                               class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-100 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800 transition-colors">
                                عرض
                            </a>
                            <a href="{{ route('admin.subjects.edit', $subject) }}"
                               class="rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-100 dark:bg-brand-900 dark:text-brand-200 dark:hover:bg-brand-800 transition-colors">
                                تعديل
                            </a>
                            <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه المادة؟')">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">لا توجد مواد دراسية</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ابدأ بإضافة مادة دراسية جديدة</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($subjects->hasPages())
    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        {{ $subjects->links() }}
    </div>
    @endif
</div>
@endsection
