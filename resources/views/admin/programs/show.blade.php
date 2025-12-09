@extends('layouts.dashboard')

@section('title', 'عرض المسار التعليمي')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.programs.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $program->name }}</h1>
        @if($program->status === 'active')
            <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
        @else
            <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">غير نشط</span>
        @endif
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.programs.edit', $program) }}"
           class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            تعديل المسار
        </a>
        <a href="{{ route('admin.terms.create', ['program_id' => $program->id]) }}"
           class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
            إضافة فصل دراسي
        </a>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Program Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Info -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">معلومات المسار</h2>
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">كود المسار</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $program->code }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">النوع</dt>
                    <dd class="mt-1">
                        @if($program->type === 'diploma')
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600 dark:bg-blue-900 dark:text-blue-200">دبلوم</span>
                        @else
                            <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-medium text-purple-600 dark:bg-purple-900 dark:text-purple-200">تدريبي</span>
                        @endif
                    </dd>
                </div>
                @if($program->duration_months)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">المدة</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $program->duration_months }} شهر</dd>
                </div>
                @endif
                @if($program->price)
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">السعر</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ number_format($program->price, 2) }} ريال</dd>
                </div>
                @endif
            </dl>

            @if($program->description)
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">الوصف</h3>
                <p class="text-sm text-gray-900 dark:text-white">{{ $program->description }}</p>
            </div>
            @endif
        </div>

        <!-- Terms List -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">الفصول الدراسية ({{ $program->terms->count() }})</h2>
            </div>

            @forelse($program->terms as $term)
            <div class="border-b border-gray-200 dark:border-gray-800 py-4 last:border-0">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $term->name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            الفصل {{ $term->term_number }} • {{ $term->subjects_count }} مادة دراسية
                        </p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                من {{ $term->start_date->format('Y/m/d') }} إلى {{ $term->end_date->format('Y/m/d') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($term->status === 'active')
                            <span class="rounded-full bg-success-50 px-2 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
                        @elseif($term->status === 'upcoming')
                            <span class="rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-600 dark:bg-blue-900 dark:text-blue-200">قادم</span>
                        @else
                            <span class="rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">مكتمل</span>
                        @endif
                        <a href="{{ route('admin.terms.show', $term) }}"
                           class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300">
                            عرض →
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <svg class="h-12 w-12 text-gray-400 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">لا توجد فصول دراسية</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">ابدأ بإضافة فصل دراسي جديد لهذا المسار</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Sidebar Stats -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Quick Stats -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">إحصائيات سريعة</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">عدد الفصول</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $program->terms->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">إجمالي المواد</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $program->terms->sum('subjects_count') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
