@extends('layouts.dashboard')

@section('title', 'إدارة المعلمين')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إدارة المعلمين</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">عرض وإدارة جميع المعلمين في النظام</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.teachers.export') }}"
           class="flex items-center gap-2 rounded-lg border border-green-500 bg-white px-5 py-3 text-sm font-medium text-green-600 hover:bg-green-50 dark:bg-gray-900 dark:hover:bg-gray-800">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            <span>تصدير Excel</span>
        </a>
        <a href="{{ route('admin.teachers.create') }}"
           class="flex items-center gap-2 rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600">
            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 5V15M5 10H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <span>إضافة معلم جديد</span>
        </a>
    </div>
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
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">الاسم</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">البريد الإلكتروني</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">رقم الهوية</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">رقم الهاتف</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">تاريخ التسجيل</th>
                    <th class="px-6 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($teachers as $teacher)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=6366f1&color=fff"
                                 alt="{{ $teacher->name }}"
                                 class="h-10 w-10 rounded-full border-2 border-brand-500" />
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $teacher->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $teacher->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $teacher->national_id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $teacher->phone ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $teacher->created_at->format('Y-m-d') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.teachers.show', $teacher) }}"
                               class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-medium text-blue-600 hover:bg-blue-100 dark:bg-blue-900 dark:text-blue-200">
                                عرض
                            </a>
                            <a href="{{ route('admin.teachers.edit', $teacher) }}"
                               class="rounded-lg bg-yellow-50 px-3 py-2 text-xs font-medium text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-200">
                                تعديل
                            </a>
                            <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المعلم؟');">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">لا يوجد معلمين مسجلين</p>
                            <a href="{{ route('admin.teachers.create') }}"
                               class="mt-2 text-sm text-brand-500 hover:text-brand-600">
                                إضافة أول معلم
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($teachers->hasPages())
    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        {{ $teachers->links() }}
    </div>
    @endif
</div>
@endsection
