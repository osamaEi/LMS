@extends('layouts.dashboard')

@section('title', 'عرض الدبلوم التعليمي')

@section('content')
<div x-data="{
    openTermModal() { window.dispatchEvent(new CustomEvent('open-term-modal')); },
    openSubjectModal(termId, termName) { window.dispatchEvent(new CustomEvent('open-subject-modal', { detail: { termId, termName } })); }
}">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('admin.programs.index') }}"
               class="flex items-center justify-center h-10 w-10 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
                <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $program->name }}</h1>
                    @if($program->status === 'active')
                        <span style="background:#dcfce7;color:#16a34a;border-radius:9999px;padding:.25rem .75rem;font-size:.75rem;font-weight:600;">نشط</span>
                    @else
                        <span style="background:#f3f4f6;color:#6b7280;border-radius:9999px;padding:.25rem .75rem;font-size:.75rem;font-weight:600;">غير نشط</span>
                    @endif
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $program->code }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.programs.edit', $program) }}"
                   class="flex items-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    تعديل
                </a>
                <button @click="openTermModal()"
                        class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة ربع دراسي
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 dark:bg-green-900/20 dark:border-green-800">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Stats Cards -->
    <div class="flex flex-wrap gap-4 mb-6">
        <div class="flex-1 min-w-[160px] bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $program->terms->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">الأرباع التدريبية </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="flex-1 min-w-[160px] bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-purple-500">{{ $program->terms->sum('subjects_count') }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">المقررات  التدريبية </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="flex-1 min-w-[160px] bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-green-500">{{ $program->terms->where('status', 'active')->count() }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">أرباع نشطة</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        @if($program->duration_months)
        <div class="flex-1 min-w-[160px] bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-orange-500">{{ $program->duration_months }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">شهر (المدة)</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="space-y-6">
        <!-- Program Details & Terms -->
        <div>
            <!-- Program Info Card -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">معلومات الدبلوم</h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14" />
                                </svg>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">كود الدبلوم</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $program->code }}</dd>
                            </div>
                        </div>
                        @if($program->price)
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">السعر</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($program->price, 2) }} ريال</dd>
                            </div>
                        </div>
                        @endif
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">الحالة</dt>
                                <dd class="mt-0.5">
                                    @if($program->status === 'active')
                                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-600 dark:bg-green-900/30 dark:text-green-400">نشط</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">غير نشط</span>
                                    @endif
                                </dd>
                            </div>
                        </div>
                    </dl>

                    @if($program->description)
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">الوصف</h3>
                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $program->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Terms List -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="p-6 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">الأرباع التدريبية  ({{ $program->terms->count() }})</h2>
                    <button @click="openTermModal()"
                            class="flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        إضافة ربع
                    </button>
                </div>
                <div class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse($program->terms as $term)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center">
                                        <span class="text-sm font-bold text-brand-600 dark:text-brand-400">{{ $term->term_number }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $term->name }}</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $term->subjects_count }} مادة دراسية
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 mt-3 mr-13">
                                    @if($term->start_date && $term->end_date)
                                    <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $term->start_date->format('Y/m/d') }} - {{ $term->end_date->format('Y/m/d') }}
                                    </div>
                                    @endif
                                </div>
                                {{-- Subjects datatable --}}
                                @if($term->subjects->isNotEmpty())
                                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
                                    <div class="overflow-x-auto rounded-lg border border-gray-100 dark:border-gray-800">
                                        <table class="w-full text-sm">
                                            <thead>
                                                <tr class="bg-gray-50 dark:bg-gray-800/60 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                    <th class="px-4 py-2.5 text-right w-8">#</th>
                                                    <th class="px-4 py-2.5 text-right">الكود</th>
                                                    <th class="px-4 py-2.5 text-right">اسم المقرر</th>
                                                    <th class="px-4 py-2.5 text-right hidden md:table-cell">الاسم الإنجليزي</th>
                                                    <th class="px-4 py-2.5 text-center w-16">س.م</th>
                                                    <th class="px-4 py-2.5 text-right hidden lg:table-cell">المدرب</th>
                                                    <th class="px-4 py-2.5 text-center w-16">الحالة</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                                @foreach($term->subjects as $idx => $subject)
                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors group">
                                                    <td class="px-4 py-2.5 text-xs text-gray-400 dark:text-gray-500">{{ $idx + 1 }}</td>
                                                    <td class="px-4 py-2.5" dir="ltr">
                                                        <span class="font-mono text-xs font-semibold text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/20 px-2 py-0.5 rounded">
                                                            {{ $subject->code }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white">
                                                        {{ $subject->name_ar ?: $subject->name_en }}
                                                    </td>
                                                    <td class="px-4 py-2.5 text-gray-500 dark:text-gray-400 text-xs hidden md:table-cell" dir="ltr">
                                                        {{ $subject->name_en }}
                                                    </td>
                                                    <td class="px-4 py-2.5 text-center">
                                                        @if($subject->credits)
                                                        <span class="text-xs font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-full px-2 py-0.5">
                                                            {{ $subject->credits }}
                                                        </span>
                                                        @else
                                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-2.5 hidden lg:table-cell">
                                                        <form method="POST" action="{{ route('admin.subjects.assign-teacher', $subject) }}" class="flex items-center gap-1">
                                                            @csrf @method('PATCH')
                                                            <select name="teacher_id" onchange="this.form.submit()"
                                                                    class="text-xs border-0 bg-transparent text-gray-600 dark:text-gray-300 cursor-pointer rounded px-1 py-0.5 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-1 focus:ring-brand-400 max-w-[140px]">
                                                                <option value="">— بدون مدرب —</option>
                                                                @foreach($teachers as $teacher)
                                                                    <option value="{{ $teacher->id }}" {{ $subject->teacher_id == $teacher->id ? 'selected' : '' }}>
                                                                        {{ $teacher->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </form>
                                                    </td>
                                                    <td class="px-4 py-2.5 text-center">
                                                        @php $sbtnStyle = $subject->status === 'active' ? 'background:#dcfce7;color:#16a34a;border-color:#bbf7d0;' : 'background:#f3f4f6;color:#6b7280;border-color:#e5e7eb;' @endphp
                                                        <div x-data="{ open: false }" class="relative inline-block">
                                                            <button @click="open = !open" @click.outside="open = false"
                                                                    class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold border transition-colors"
                                                                    style="{{ $sbtnStyle }}">
                                                                {{ $subject->status === 'active' ? 'نشط' : 'مقفل' }}
                                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                            </button>
                                                            <div x-show="open" x-cloak
                                                                 class="absolute left-0 mt-1 w-28 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50 overflow-hidden">
                                                                @if($subject->status !== 'active')
                                                                <form method="POST" action="{{ route('admin.subjects.toggle-status', $subject) }}">
                                                                    @csrf @method('PATCH')
                                                                    <input type="hidden" name="status" value="active">
                                                                    <button type="submit" class="w-full text-right px-3 py-2 text-xs font-medium text-green-700 hover:bg-green-50 dark:hover:bg-green-900/20 flex items-center gap-2">
                                                                        <span class="w-2 h-2 rounded-full bg-green-400 inline-block"></span> تنشيط
                                                                    </button>
                                                                </form>
                                                                @endif
                                                                @if($subject->status !== 'inactive')
                                                                <form method="POST" action="{{ route('admin.subjects.toggle-status', $subject) }}">
                                                                    @csrf @method('PATCH')
                                                                    <input type="hidden" name="status" value="inactive">
                                                                    <button type="submit" class="w-full text-right px-3 py-2 text-xs font-medium text-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
                                                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                                        قفل
                                                                    </button>
                                                                </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="bg-gray-50 dark:bg-gray-800/60 border-t border-gray-200 dark:border-gray-700">
                                                    <td colspan="4" class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400">
                                                        المجموع — {{ $term->subjects->count() }} مقرر
                                                    </td>
                                                    <td class="px-4 py-2 text-center">
                                                        <span class="text-xs font-bold text-brand-600 dark:text-brand-400">
                                                            {{ $term->subjects->sum('credits') }}
                                                        </span>
                                                    </td>
                                                    <td colspan="2"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="flex items-center gap-3">
                                @if($term->status === 'active')
                                    <span style="background:#dcfce7;color:#16a34a;border-radius:9999px;padding:.2rem .65rem;font-size:.72rem;font-weight:600;">نشط</span>
                                @elseif($term->status === 'upcoming')
                                    <span style="background:#dbeafe;color:#2563eb;border-radius:9999px;padding:.2rem .65rem;font-size:.72rem;font-weight:600;">قادم</span>
                                @else
                                    <span style="background:#f3f4f6;color:#6b7280;border-radius:9999px;padding:.2rem .65rem;font-size:.72rem;font-weight:600;">مكتمل</span>
                                @endif
                                <div class="flex items-center gap-1">
                                    <button @click="openSubjectModal({{ $term->id }}, '{{ addslashes($term->name) }}')"
                                            class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                            title="إضافة مادة">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                    <a href="{{ route('admin.terms.show', $term) }}"
                                       class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                       title="عرض التفاصيل">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.terms.edit', $term) }}"
                                       class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                       title="تعديل">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">لا توجد أرباع دراسية</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ابدأ بإضافة ربع تدريبي جديد لهذا الدبلوم</p>
                        <button @click="openTermModal()"
                                class="mt-4 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            إضافة ربع دراسي
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

</div>

<!-- Term Modal -->
<template x-teleport="body">
    <div x-data="{ open: false }"
         @open-term-modal.window="open = true"
         x-show="open"
         x-cloak
         style="position:fixed;inset:0;z-index:99999;">
        <div @click="open = false" style="position:fixed;inset:0;background:rgba(0,0,0,0.55);"></div>
        <div style="position:fixed;inset:0;display:flex;align-items:center;justify-content:center;padding:1rem;">
            <div @click.stop style="background:white;border-radius:14px;width:100%;max-width:440px;box-shadow:0 25px 60px rgba(0,0,0,0.2);" class="dark:bg-gray-800">
                <div style="padding:1.1rem 1.25rem;border-bottom:1px solid #e5e7eb;" class="dark:border-gray-700 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">إضافة ربع دراسي</h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('admin.terms.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="program_id" value="{{ $program->id }}">
                    <div style="padding:1.1rem 1.25rem;" class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">اسم الربع *</label>
                                <input type="text" name="name_ar" required placeholder="الفصل الأول"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">رقم الربع *</label>
                                <input type="number" name="term_number" required min="1" value="{{ $program->terms->count() + 1 }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">تاريخ البداية</label>
                                <input type="date" name="start_date"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">تاريخ النهاية</label>
                                <input type="date" name="end_date"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">الحالة *</label>
                            <select name="status" required class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="upcoming">قادم</option>
                                <option value="active">نشط</option>
                                <option value="completed">مكتمل</option>
                            </select>
                        </div>
                    </div>
                    <div style="padding:1rem 1.25rem;border-top:1px solid #e5e7eb;" class="dark:border-gray-700 flex justify-end gap-2">
                        <button type="button" @click="open = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">إلغاء</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors font-medium">إضافة الربع</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<!-- Subject Modal (create new subject for this program) -->
<template x-teleport="body">
    <div x-data="{ open: false, termId: null, termName: '' }"
         @open-subject-modal.window="open = true; termId = $event.detail.termId; termName = $event.detail.termName"
         x-show="open"
         x-cloak
         style="position:fixed;inset:0;z-index:99999;">
        <div @click="open = false" style="position:fixed;inset:0;background:rgba(0,0,0,0.55);"></div>
        <div style="position:fixed;inset:0;display:flex;align-items:center;justify-content:center;padding:1rem;">
            <div @click.stop style="background:white;border-radius:14px;width:100%;max-width:500px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,0.2);" class="dark:bg-gray-800">
                <div style="padding:1.1rem 1.25rem;border-bottom:1px solid #e5e7eb;flex-shrink:0;" class="dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">إضافة مادة دراسية</h3>
                        <p class="text-xs text-gray-400 mt-0.5" x-text="termName"></p>
                    </div>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('admin.subjects.store') }}" method="POST" style="display:flex;flex-direction:column;overflow:hidden;flex:1;">
                    @csrf
                    <input type="hidden" name="program_id" value="{{ $program->id }}">
                    <input type="hidden" name="term_id" :value="termId">
                    <div style="padding:1.1rem 1.25rem;overflow-y:auto;flex:1;" class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">كود المادة *</label>
                                <input type="text" name="code" required dir="ltr" placeholder="MATH 101"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white font-mono focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">الساعات المعتمدة</label>
                                <input type="number" name="credits" min="1" max="10" value="3"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">اسم المادة (عربي) *</label>
                            <input type="text" name="name_ar" required placeholder="مبادئ الرياضيات"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">اسم المادة (إنجليزي)</label>
                            <input type="text" name="name_en" dir="ltr" placeholder="Mathematics"
                                   class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">الحالة</label>
                            <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                            </select>
                        </div>
                    </div>
                    <div style="padding:1rem 1.25rem;border-top:1px solid #e5e7eb;flex-shrink:0;" class="dark:border-gray-700 flex justify-end gap-2">
                        <button type="button" @click="open = false" class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">إلغاء</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white rounded-lg font-medium transition-colors" style="background:linear-gradient(135deg,#7c3aed,#5b21b6);">إضافة المادة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

@endsection
