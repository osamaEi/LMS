@extends('layouts.dashboard')

@section('title', 'إدارة الدورات')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إدارة الدورات</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">عرض وإدارة جميع الدورات في النظام</p>
    </div>
    <a href="{{ route('admin.courses.create') }}"
       class="flex items-center gap-2 rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 5V15M5 10H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span>إضافة دورة جديدة</span>
    </a>
</div>

@if(session('success'))
<div class="mb-6 rounded-lg bg-success-50 p-4 text-success-600 dark:bg-success-900 dark:text-success-200">
    {{ session('success') }}
</div>
@endif

<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800">
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">#</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">اسم الدورة</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">المعلم</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">الحالة</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">عدد الطلاب</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">تاريخ البداية</th>
                    <th class="px-6 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($courses as $course)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $course->title }}</span>
                            @if($course->description)
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($course->description, 50) }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($course->teacher->name ?? 'No Teacher') }}&background=6366f1&color=fff"
                                 alt="{{ $course->teacher->name ?? 'No Teacher' }}"
                                 class="h-8 w-8 rounded-full" />
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $course->teacher->name ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'active' => 'bg-success-50 text-success-600 dark:bg-success-900 dark:text-success-200',
                                'draft' => 'bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                'inactive' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-200',
                                'archived' => 'bg-error-50 text-error-600 dark:bg-error-900 dark:text-error-200',
                            ];
                        @endphp
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium {{ $statusColors[$course->status] ?? 'bg-gray-50 text-gray-600' }}">
                            {{ $course->status_display }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ $course->max_students ?? 'غير محدد' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ $course->start_date ? $course->start_date->format('Y-m-d') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.courses.show', $course) }}"
                               class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-medium text-blue-600 hover:bg-blue-100 dark:bg-blue-900 dark:text-blue-200">
                                عرض
                            </a>
                            <a href="{{ route('admin.courses.edit', $course) }}"
                               class="rounded-lg bg-yellow-50 px-3 py-2 text-xs font-medium text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-200">
                                تعديل
                            </a>
                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الدورة؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="rounded-lg bg-error-50 px-3 py-2 text-xs font-medium text-error-600 hover:bg-error-100 dark:bg-error-900 dark:text-error-200">
                                    حذف
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg class="h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">لا يوجد دورات مسجلة</p>
                            <a href="{{ route('admin.courses.create') }}"
                               class="mt-2 text-sm text-brand-500 hover:text-brand-600">
                                إضافة أول دورة
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($courses->hasPages())
    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        {{ $courses->links() }}
    </div>
    @endif
</div>
@endsection
