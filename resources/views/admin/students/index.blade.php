@extends('layouts.dashboard')

@section('title', 'إدارة الطلاب')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إدارة الطلاب</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">عرض وإدارة جميع الطلاب في النظام</p>
    </div>
    <a href="{{ route('admin.students.create') }}"
       class="flex items-center gap-2 rounded-lg bg-brand-500 px-6 py-3 text-sm font-medium text-white hover:bg-brand-600">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 5V15M5 10H15" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <span>إضافة طالب جديد</span>
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
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">الاسم</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">البريد الإلكتروني</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">رقم الهوية</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">رقم الهاتف</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">تاريخ التسجيل</th>
                    <th class="px-6 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($students as $student)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=10b981&color=fff"
                                 alt="{{ $student->name }}"
                                 class="h-10 w-10 rounded-full border-2 border-success-500" />
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $student->email }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $student->national_id }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $student->phone ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $student->created_at->format('Y-m-d') }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.students.show', $student) }}"
                               class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-medium text-blue-600 hover:bg-blue-100 dark:bg-blue-900 dark:text-blue-200">
                                عرض
                            </a>
                            <a href="{{ route('admin.students.edit', $student) }}"
                               class="rounded-lg bg-yellow-50 px-3 py-2 text-xs font-medium text-yellow-600 hover:bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-200">
                                تعديل
                            </a>
                            <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب؟');">
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
                            <p class="text-gray-500 dark:text-gray-400">لا يوجد طلاب مسجلين</p>
                            <a href="{{ route('admin.students.create') }}"
                               class="mt-2 text-sm text-brand-500 hover:text-brand-600">
                                إضافة أول طالب
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->hasPages())
    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        {{ $students->links() }}
    </div>
    @endif
</div>
@endsection
