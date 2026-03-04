@extends('layouts.dashboard')

@section('title', 'عرض الربع الدراسي')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.terms.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $term->name }}</h1>
        @if($term->status === 'active')
            <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
        @elseif($term->status === 'upcoming')
            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-600 dark:bg-blue-900 dark:text-blue-200">قادم</span>
        @else
            <span class="rounded-full bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">مكتمل</span>
        @endif
    </div>
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ route('admin.terms.edit', $term) }}"
           class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            تعديل الربع
        </a>
        <button onclick="document.getElementById('assign-subjects-modal').classList.toggle('hidden')"
                class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
            تعيين المواد الدراسية
        </button>
    </div>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-success-50 p-4 dark:bg-success-900">
    <p class="text-sm text-success-700 dark:text-success-200">{{ session('success') }}</p>
</div>
@endif

{{-- Assign Subjects Panel --}}
<div id="assign-subjects-modal" class="hidden mb-6 rounded-xl border border-blue-200 bg-blue-50 p-6 dark:border-blue-800 dark:bg-blue-950">
    <h3 class="text-base font-bold text-blue-900 dark:text-blue-100 mb-4">تعيين المواد الدراسية لهذا الربع</h3>
    @php
        $programSubjects = $term->program ? $term->program->subjects()->with('files')->get() : collect();
        $assignedIds = $term->subjects->pluck('id')->toArray();
    @endphp
    @if($programSubjects->isEmpty())
        <p class="text-sm text-blue-700 dark:text-blue-300">
            لا توجد مواد دراسية مرتبطة بالدبلوم "{{ $term->program->name_ar ?? '' }}" بعد.
            <a href="{{ route('admin.subjects.create', ['program_id' => $term->program_id]) }}" class="underline font-semibold">أضف مادة الآن</a>
        </p>
    @else
        <form action="{{ route('admin.terms.subjects.sync', $term) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3 mb-4 max-h-64 overflow-y-auto">
                @foreach($programSubjects as $subject)
                <label class="flex items-center gap-3 rounded-lg border bg-white px-4 py-3 cursor-pointer hover:border-blue-400 dark:bg-gray-900 {{ in_array($subject->id, $assignedIds) ? 'border-blue-400' : 'border-blue-200 dark:border-blue-700' }}">
                    <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                           {{ in_array($subject->id, $assignedIds) ? 'checked' : '' }}
                           class="h-4 w-4 rounded border-gray-300 text-brand-600">
                    <div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->name_ar }}</div>
                        <div class="text-xs text-gray-400">{{ $subject->code }} · {{ $subject->files->count() }} ملف</div>
                    </div>
                </label>
                @endforeach
            </div>
            <div class="flex gap-3">
                <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                    حفظ التعيينات
                </button>
                <button type="button" onclick="document.getElementById('assign-subjects-modal').classList.add('hidden')"
                        class="rounded-lg border border-gray-300 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                    إلغاء
                </button>
            </div>
        </form>
    @endif
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Term Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Basic Info -->
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">معلومات الربع الدراسي</h2>
            <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">الدبلوم</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $term->program->name_ar ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">رقم الربع</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">الربع {{ $term->term_number }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">فترة الدراسة</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                        من {{ $term->start_date->format('Y/m/d') }}<br>
                        إلى {{ $term->end_date->format('Y/m/d') }}
                    </dd>
                </div>
                @if($term->registration_start_date && $term->registration_end_date)
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">فترة التسجيل</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                        من {{ $term->registration_start_date->format('Y/m/d') }}
                        إلى {{ $term->registration_end_date->format('Y/m/d') }}
                    </dd>
                </div>
                @endif
            </dl>
        </div>

        <!-- Subjects List with Files -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="p-6 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">المواد الدراسية ({{ $term->subjects->count() }})</h2>
                <a href="{{ route('admin.subjects.create', ['program_id' => $term->program_id]) }}"
                   class="text-sm text-brand-600 hover:text-brand-700 dark:text-brand-400">+ إضافة مادة للدبلوم</a>
            </div>

            @forelse($term->subjects as $subject)
            <div class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                {{-- Subject Header (clickable to expand files) --}}
                <div class="flex items-start justify-between p-5 cursor-pointer select-none"
                     onclick="this.nextElementSibling.classList.toggle('hidden'); this.querySelector('.chevron').classList.toggle('rotate-180')">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $subject->name_ar }}</h3>
                            <span class="rounded bg-gray-100 px-2 py-0.5 text-xs font-medium dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                {{ $subject->code }}
                            </span>
                            @if($subject->status === 'active')
                                <span class="rounded-full bg-success-50 px-2 py-0.5 text-xs font-medium text-success-600">نشط</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-4 mt-2 text-xs text-gray-500 dark:text-gray-400 flex-wrap">
                            @if($subject->teacher)
                            <span class="flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $subject->teacher->name }}
                            </span>
                            @endif
                            <span class="flex items-center gap-1">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                {{ $subject->sessions_count }} درس
                            </span>
                            <span class="flex items-center gap-1 font-semibold text-blue-600">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                {{ $subject->files->count() }} ملف
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mr-4 flex-shrink-0">
                        <a href="{{ route('admin.subjects.show', $subject) }}"
                           class="text-xs text-brand-600 hover:text-brand-700 dark:text-brand-400" onclick="event.stopPropagation()">
                            عرض →
                        </a>
                        <svg class="chevron h-4 w-4 text-gray-400 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>

                {{-- Files Section (collapsed by default) --}}
                <div class="hidden border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/40 px-5 py-4">
                    @if($subject->files->isEmpty())
                        <p class="text-xs text-gray-400 text-center py-3">
                            لا توجد ملفات لهذه المادة.
                            <a href="{{ route('admin.subjects.show', $subject) }}" class="text-brand-600 underline font-medium">رفع ملفات من صفحة المادة</a>
                        </p>
                    @else
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-3">ملفات المادة:</p>
                        <div class="space-y-2">
                            @foreach($subject->files as $file)
                            <div class="flex items-center justify-between bg-white dark:bg-gray-900 rounded-lg px-4 py-3 border border-gray-200 dark:border-gray-700">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:#eff6ff;">
                                        @php
                                            $ext = strtolower($file->file_type ?? '');
                                            $c = match($ext) {
                                                'pdf' => '#dc2626',
                                                'doc','docx' => '#2563eb',
                                                'xls','xlsx' => '#16a34a',
                                                'ppt','pptx' => '#ea580c',
                                                default => '#6b7280',
                                            };
                                        @endphp
                                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="{{ $c }}" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $file->title }}</p>
                                        <p class="text-xs text-gray-400">
                                            {{ strtoupper($file->file_type ?? 'FILE') }}
                                            @if($file->file_size) · {{ $file->getFormattedSize() }} @endif
                                            @if($file->description) · {{ $file->description }} @endif
                                        </p>
                                    </div>
                                </div>
                                <a href="{{ Storage::url($file->file_path) }}" target="_blank"
                                   class="flex-shrink-0 flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 mr-4 whitespace-nowrap">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                    تحميل
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-10 px-6">
                <svg class="h-12 w-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p class="text-sm font-medium text-gray-900 dark:text-white">لا توجد مواد دراسية مرتبطة</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">اضغط "تعيين المواد الدراسية" لإضافة مواد من الدبلوم</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Sidebar Stats -->
    <div class="lg:col-span-1 space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">إحصائيات سريعة</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">عدد المواد</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $term->subjects->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">إجمالي الدروس</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $term->subjects->sum('sessions_count') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500 dark:text-gray-400">إجمالي الملفات</span>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $term->subjects->sum(fn($s) => $s->files->count()) }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">الجدول الزمني</h2>
            <div class="space-y-4">
                @if($term->registration_start_date && $term->registration_end_date)
                <div class="flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                        <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">فترة التسجيل</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $term->registration_start_date->format('Y/m/d') }} - {{ $term->registration_end_date->format('Y/m/d') }}
                        </p>
                    </div>
                </div>
                @endif
                <div class="flex items-start gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-success-100 dark:bg-success-900">
                        <svg class="h-4 w-4 text-success-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">فترة الدراسة</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $term->start_date->format('Y/m/d') }} - {{ $term->end_date->format('Y/m/d') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
