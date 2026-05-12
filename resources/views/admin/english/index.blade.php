@extends('layouts.dashboard')

@section('title', 'دورات اللغة الإنجليزية')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">دورات اللغة الإنجليزية</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة مستويات دورات اللغة الإنجليزية</p>
        </div>
        <a href="{{ route('admin.english.create') }}"
           class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-white transition-colors"
           style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            إضافة مستوى جديد
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي المستويات</p>
        </div>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">نشط</p>
        </div>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
            <svg class="w-6 h-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['inactive'] }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400">غير نشط</p>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 border border-green-200 p-4 dark:bg-green-900/20 dark:border-green-800">
    <p class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</p>
</div>
@endif

{{-- Table --}}
<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="p-5 border-b border-gray-200 dark:border-gray-800" style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:12px 12px 0 0;">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                </svg>
            </div>
            <h2 class="text-base font-bold text-white">مستويات اللغة الإنجليزية</h2>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">المستوى</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">اسم الدورة</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">الكود</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">المدة</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">السعر</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">الحالة</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($programs as $program)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-bold text-white"
                              style="background:linear-gradient(135deg,#3b82f6,#1d4ed8);">
                            {{ $program->level ?? '—' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-900 dark:text-white">{{ $program->name_ar }}</div>
                        @if($program->name_en)
                        <div class="text-xs text-gray-400 dir-ltr">{{ $program->name_en }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">{{ $program->code ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                        {{ $program->duration_months ? $program->duration_months . ' شهر' : '—' }}
                    </td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                        {{ $program->price ? number_format($program->price, 0) . ' ر.س' : 'مجاني' }}
                    </td>
                    <td class="px-4 py-3">
                        @if($program->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">نشط</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">غير نشط</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.english.edit', $program) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                تعديل
                            </a>
                            <form action="{{ route('admin.english.destroy', $program) }}" method="POST"
                                  onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    حذف
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-400 dark:text-gray-600">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                        </svg>
                        <p class="text-sm">لا توجد دورات لغة إنجليزية بعد</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($programs->hasPages())
    <div class="p-4 border-t border-gray-200 dark:border-gray-800">
        {{ $programs->links() }}
    </div>
    @endif
</div>
@endsection
