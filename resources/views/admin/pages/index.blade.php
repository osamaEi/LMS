@extends('layouts.dashboard')
@section('title', 'الصفحات')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">الصفحات</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة صفحات الموقع (الشروط، الخصوصية، وغيرها)</p>
    </div>
    <a href="{{ route('admin.pages.create') }}"
       class="flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-bold text-white"
       style="background:linear-gradient(135deg,#0071aa,#005a8a);">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة صفحة
    </a>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-700">{{ session('success') }}</div>
@endif

<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">العنوان</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">الرابط (Slug)</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">التصنيف</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">الحالة</th>
                <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-400">إجراءات</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($pages as $page)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $page->title_ar }}</td>
                <td class="px-4 py-3 text-gray-500 font-mono text-xs" dir="ltr">{{ $page->slug }}</td>
                <td class="px-4 py-3 text-gray-500">{{ $page->category }}</td>
                <td class="px-4 py-3">
                    <span class="inline-block px-2 py-0.5 text-xs rounded-full {{ $page->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $page->is_published ? 'منشورة' : 'مسودة' }}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-3">
                        <a href="{{ url('/page/' . $page->slug) }}" target="_blank" class="text-xs text-blue-500 hover:text-blue-700">عرض</a>
                        <a href="{{ route('admin.pages.edit', $page) }}" class="text-xs text-indigo-500 hover:text-indigo-700">تعديل</a>
                        <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline"
                              onsubmit="return confirm('حذف هذه الصفحة نهائياً؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-red-500 hover:text-red-700">حذف</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-4 py-10 text-center text-gray-400">لا توجد صفحات بعد</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
