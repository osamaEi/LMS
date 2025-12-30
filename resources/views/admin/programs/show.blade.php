@extends('layouts.dashboard')

@section('title', 'عرض المسار التعليمي')

@section('content')
<div x-data="{}">
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
                        <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-600 dark:bg-green-900/30 dark:text-green-400">نشط</span>
                    @else
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">غير نشط</span>
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
                <button @click="$store.termModal = true"
                        class="flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إضافة فصل دراسي
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
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">الفصول الدراسية</div>
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
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">المواد الدراسية</div>
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
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">فصول نشطة</div>
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Program Details & Terms -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Program Info Card -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">معلومات المسار</h2>
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
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">كود المسار</dt>
                                <dd class="text-sm font-semibold text-gray-900 dark:text-white">{{ $program->code }}</dd>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">النوع</dt>
                                <dd class="mt-0.5">
                                    @if($program->type === 'diploma')
                                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">دبلوم</span>
                                    @else
                                        <span class="rounded-full bg-purple-100 px-3 py-1 text-xs font-medium text-purple-600 dark:bg-purple-900/30 dark:text-purple-400">تدريبي</span>
                                    @endif
                                </dd>
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
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">الفصول الدراسية ({{ $program->terms->count() }})</h2>
                    <button @click="$store.termModal = true"
                            class="flex items-center gap-2 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        إضافة فصل
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
                                    <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $term->start_date->format('Y/m/d') }} - {{ $term->end_date->format('Y/m/d') }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @if($term->status === 'active')
                                    <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-600 dark:bg-green-900/30 dark:text-green-400">نشط</span>
                                @elseif($term->status === 'upcoming')
                                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-medium text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">قادم</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">مكتمل</span>
                                @endif
                                <div class="flex items-center gap-1">
                                    <button @click="$store.subjectModal = {open: true, termId: {{ $term->id }}, termName: '{{ $term->name }}'}"
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
                        <p class="text-sm font-medium text-gray-900 dark:text-white">لا توجد فصول دراسية</p>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ابدأ بإضافة فصل دراسي جديد لهذا المسار</p>
                        <button @click="$store.termModal = true"
                                class="mt-4 inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            إضافة فصل دراسي
                        </button>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Actions -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">إجراءات سريعة</h2>
                </div>
                <div class="p-4 space-y-2">
                    <button @click="$store.termModal = true"
                            class="w-full flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        إضافة فصل دراسي جديد
                    </button>
                    <a href="{{ route('admin.programs.edit', $program) }}"
                       class="w-full flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        تعديل بيانات المسار
                    </a>
                    <a href="{{ route('admin.subjects.index') }}?program_id={{ $program->id }}"
                       class="w-full flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        عرض جميع المواد
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="p-6 border-b border-gray-200 dark:border-gray-800">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">معلومات إضافية</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">تاريخ الإنشاء</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $program->created_at->format('Y/m/d') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">آخر تحديث</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $program->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Term Modal -->
<template x-teleport="body">
    <div x-data="{ open: false }"
         x-init="$watch('$store.termModal', value => open = value)"
         x-show="open"
         x-cloak
         style="position: fixed; inset: 0; z-index: 999999;">

        <!-- Backdrop -->
        <div x-show="open" @click="open = false; $store.termModal = false"
             style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);"></div>

        <!-- Modal -->
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div x-show="open" @click.stop
                 style="background: white; border-radius: 12px; width: 100%; max-width: 420px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);"
                 class="dark:bg-gray-800">

                <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb;" class="dark:border-gray-700 flex items-center justify-between">
                    <h3 style="font-size: 1rem; font-weight: 600;" class="text-gray-900 dark:text-white">إضافة فصل دراسي</h3>
                    <button @click="open = false; $store.termModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.terms.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="program_id" value="{{ $program->id }}">
                    <div style="padding: 1rem; max-height: 50vh; overflow-y: auto;" class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">اسم الفصل *</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="الفصل الأول">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">رقم الفصل *</label>
                                <input type="number" name="term_number" required min="1" value="{{ $program->terms->count() + 1 }}" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">تاريخ البداية *</label>
                                <input type="date" name="start_date" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">تاريخ النهاية *</label>
                                <input type="date" name="end_date" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة *</label>
                            <select name="status" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="upcoming">قادم</option>
                                <option value="active">نشط</option>
                                <option value="completed">مكتمل</option>
                            </select>
                        </div>
                    </div>
                    <div style="padding: 1rem; border-top: 1px solid #e5e7eb;" class="dark:border-gray-700 flex justify-end gap-2">
                        <button type="button" @click="open = false; $store.termModal = false" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">إلغاء</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg hover:bg-blue-700">إضافة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<!-- Subject Modal -->
<template x-teleport="body">
    <div x-data="{ open: false, termId: null, termName: '' }"
         x-init="$watch('$store.subjectModal', value => { open = value.open; termId = value.termId; termName = value.termName; })"
         x-show="open"
         x-cloak
         style="position: fixed; inset: 0; z-index: 999999;">

        <!-- Backdrop -->
        <div x-show="open" @click="open = false; $store.subjectModal = {open: false, termId: null, termName: ''}"
             style="position: fixed; inset: 0; background: rgba(0,0,0,0.6);"></div>

        <!-- Modal -->
        <div style="position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; padding: 1rem;">
            <div x-show="open" @click.stop
                 style="background: white; border-radius: 12px; width: 100%; max-width: 420px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);"
                 class="dark:bg-gray-800">

                <div style="padding: 1rem; border-bottom: 1px solid #e5e7eb;" class="dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 600;" class="text-gray-900 dark:text-white">إضافة مادة دراسية</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-text="termName"></p>
                    </div>
                    <button @click="open = false; $store.subjectModal = {open: false, termId: null, termName: ''}" class="text-gray-400 hover:text-gray-600">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.subjects.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="term_id" x-bind:value="termId">
                    <div style="padding: 1rem; max-height: 50vh; overflow-y: auto;" class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">اسم المادة *</label>
                                <input type="text" name="name" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="مقدمة في البرمجة">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">كود المادة *</label>
                                <input type="text" name="code" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="CS101">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">الساعات المعتمدة</label>
                                <input type="number" name="credits" min="1" value="3" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">إجمالي الساعات</label>
                                <input type="number" name="total_hours" min="1" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="45">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة *</label>
                            <select name="status" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="active">نشطة</option>
                                <option value="inactive">غير نشطة</option>
                            </select>
                        </div>
                    </div>
                    <div style="padding: 1rem; border-top: 1px solid #e5e7eb;" class="dark:border-gray-700 flex justify-end gap-2">
                        <button type="button" @click="open = false; $store.subjectModal = {open: false, termId: null, termName: ''}" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">إلغاء</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-purple-600 rounded-lg hover:bg-purple-700">إضافة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('termModal', false);
    Alpine.store('subjectModal', { open: false, termId: null, termName: '' });
});
</script>
@endpush
