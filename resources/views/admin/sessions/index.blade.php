@extends('layouts.dashboard')

@section('title', 'إدارة الدروس والمحاضرات')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">الدروس والمحاضرات</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة جميع الدروس والمحاضرات في النظام</p>
    </div>
    <a href="{{ route('admin.sessions.create') }}"
       class="flex items-center gap-2 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        إضافة درس جديد
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
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">عنوان الدرس</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">المادة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">النوع</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">التاريخ/الوقت</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">المدة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الحالة</th>
                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700 dark:text-gray-300">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sessions as $session)
                <tr class="border-b border-gray-200 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $session->session_number }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $session->title }}</div>
                        @if($session->is_mandatory)
                        <span class="text-xs text-error-600 dark:text-error-400">إلزامي</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-gray-100">{{ $session->subject->name ?? '-' }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $session->subject->term->name ?? '-' }} • {{ $session->subject->term->program->name ?? '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($session->type === 'live_zoom')
                            <span class="flex items-center gap-1 text-blue-600 dark:text-blue-400">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                                </svg>
                                Zoom
                            </span>
                        @else
                            <span class="flex items-center gap-1 text-purple-600 dark:text-purple-400">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                                </svg>
                                فيديو
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        @if($session->scheduled_at)
                            {{ $session->scheduled_at->format('Y/m/d') }}<br>
                            <span class="text-xs text-gray-500">{{ $session->scheduled_at->format('h:i A') }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">
                        {{ $session->duration_minutes ?? '-' }} دقيقة
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($session->status === 'live')
                            <span class="rounded-full bg-error-50 px-3 py-1 text-xs font-medium text-error-600 dark:bg-error-900 dark:text-error-200 animate-pulse">مباشر</span>
                        @elseif($session->status === 'scheduled')
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600 dark:bg-blue-900 dark:text-blue-200">مجدول</span>
                        @elseif($session->status === 'completed')
                            <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">مكتمل</span>
                        @else
                            <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">ملغي</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.sessions.show', $session) }}"
                               class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-600 hover:bg-blue-100 dark:bg-blue-900 dark:text-blue-200 dark:hover:bg-blue-800 transition-colors">
                                عرض
                            </a>
                            <a href="{{ route('admin.sessions.edit', $session) }}"
                               class="rounded-lg bg-brand-50 px-3 py-1.5 text-xs font-medium text-brand-600 hover:bg-brand-100 dark:bg-brand-900 dark:text-brand-200 dark:hover:bg-brand-800 transition-colors">
                                تعديل
                            </a>
                            <form action="{{ route('admin.sessions.destroy', $session) }}" method="POST" class="inline"
                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا الدرس؟')">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">لا توجد دروس</p>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ابدأ بإضافة درس جديد</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sessions->hasPages())
    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        {{ $sessions->links() }}
    </div>
    @endif
</div>
@endsection
