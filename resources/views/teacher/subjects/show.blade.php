@extends('layouts.dashboard')

@section('title', $subject->name)

@section('content')
<div x-data="{ uploadModal: false }">

{{-- ─── Header ─── --}}
<div class="mb-6">
    <nav class="mb-4 text-sm">
        <ol class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('teacher.my-subjects.index') }}" class="hover:text-brand-500">مقرراتي</a></li>
            <li>/</li>
            <li class="text-gray-900 dark:text-white">{{ $subject->name }}</li>
        </ol>
    </nav>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subject->name }}</h1>
                @if($subject->status === 'active')
                    <span class="rounded-full px-3 py-1 text-xs font-medium" style="background:#dcfce7;color:#16a34a">نشط</span>
                @elseif($subject->status === 'inactive')
                    <span class="rounded-full px-3 py-1 text-xs font-medium" style="background:#f3f4f6;color:#6b7280">غير نشط</span>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $subject->term->name ?? '' }}
                @if($subject->term?->program?->name)
                    <span class="mx-1">-</span>{{ $subject->term->program->name }}
                @endif
                @if($subject->code)
                    <span class="mx-2">|</span>
                    <span class="font-mono" dir="ltr">{{ $subject->code }}</span>
                @endif
            </p>
        </div>
        <div class="flex gap-2 flex-wrap">
            <button @click="uploadModal = true"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 transition">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                رفع ملف
            </button>
            <a href="{{ route('teacher.my-subjects.sessions.create', $subject->id) }}"
               class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white transition"
               style="background: linear-gradient(135deg,#0071AA,#005a88)">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إنشاء حصة
            </a>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900 dark:text-green-200">
    {{ session('success') }}
</div>
@endif
@if($errors->any())
<div class="mb-4 rounded-lg bg-red-50 p-4 text-sm text-red-600 dark:bg-red-900 dark:text-red-200">
    <ul class="list-disc list-inside space-y-1">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- ─── Stats ─── --}}
<div class="mb-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-4 text-center">
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subject->enrollments_count }}</p>
        <p class="text-xs text-gray-500 mt-1">طالب مسجل</p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-4 text-center">
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $sessions->count() }}</p>
        <p class="text-xs text-gray-500 mt-1">حصة</p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-4 text-center">
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subject->files->count() }}</p>
        <p class="text-xs text-gray-500 mt-1">ملف للمقرر</p>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 p-4 text-center">
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $allFiles->count() }}</p>
        <p class="text-xs text-gray-500 mt-1">ملف للحصص</p>
    </div>
</div>

{{-- ─── Sessions Section ─── --}}
<div class="mb-6 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background: linear-gradient(135deg,#0071AA,#005a88);">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">الحصص</h2>
                <p class="text-xs text-gray-500">قائمة الحصص والمحاضرات</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                {{ $sessions->count() }}
            </span>
        </div>
        <a href="{{ route('teacher.my-subjects.sessions.create', $subject->id) }}"
           class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-white transition"
           style="background: linear-gradient(135deg,#0071AA,#005a88)">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            إضافة حصة
        </a>
    </div>

    @if($sessions->isEmpty())
        <div class="flex flex-col items-center justify-center py-14 text-center">
            <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="font-medium text-gray-500">لا توجد حصص بعد</p>
            <a href="{{ route('teacher.my-subjects.sessions.create', $subject->id) }}"
               class="mt-3 inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium text-white"
               style="background: linear-gradient(135deg,#0071AA,#005a88)">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إنشاء أول حصة
            </a>
        </div>
    @else
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($sessions as $session)
                @php
                    $typeBadge = $session->type === 'live_zoom'
                        ? ['label' => 'زووم مباشر', 'bg' => '#eff6ff', 'color' => '#1d4ed8']
                        : ['label' => 'مسجل',       'bg' => '#f5f3ff', 'color' => '#6d28d9'];
                    $statusBadge = match($session->status ?? 'pending') {
                        'completed' => ['label' => 'مكتملة',     'bg' => '#dcfce7', 'color' => '#16a34a'],
                        'live'      => ['label' => 'مباشر الآن', 'bg' => '#fef2f2', 'color' => '#dc2626'],
                        default     => ['label' => 'قادمة',      'bg' => '#fefce8', 'color' => '#ca8a04'],
                    };
                @endphp
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">

                    {{-- Session number --}}
                    <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl text-white text-sm font-bold shadow-sm"
                         style="background: linear-gradient(135deg,#0071AA,#005a88)">
                        {{ $session->session_number }}
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-medium text-gray-900 dark:text-white text-sm">{{ $session->title }}</p>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium"
                                  style="background:{{ $typeBadge['bg'] }};color:{{ $typeBadge['color'] }}">{{ $typeBadge['label'] }}</span>
                            <span class="rounded-full px-2 py-0.5 text-xs font-medium"
                                  style="background:{{ $statusBadge['bg'] }};color:{{ $statusBadge['color'] }}">{{ $statusBadge['label'] }}</span>
                        </div>
                        <div class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-gray-500">
                            @if($session->scheduled_at)
                                <span class="flex items-center gap-1">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d H:i') }}
                                </span>
                            @endif
                            @if($session->duration_minutes)
                                <span>{{ $session->duration_minutes }} دقيقة</span>
                            @endif
                            @if($session->files->count())
                                <span class="flex items-center gap-1">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    {{ $session->files->count() }} ملف
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-shrink-0 items-center gap-2">
                        @if($session->type === 'live_zoom' && $session->zoom_join_url)
                            <a href="{{ route('teacher.my-subjects.sessions.zoom-embedded', [$subject->id, $session->id]) }}"
                               class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-white transition"
                               style="background:#0071AA">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                دخول
                            </a>
                        @endif
                        <a href="{{ route('teacher.my-subjects.sessions.attendance', [$subject->id, $session->id]) }}"
                           class="inline-flex items-center rounded-lg bg-purple-50 px-3 py-1.5 text-xs font-medium text-purple-700 hover:bg-purple-100 dark:bg-purple-900 dark:text-purple-300 transition">
                            الحضور
                        </a>
                        <a href="{{ route('teacher.my-subjects.sessions.edit', [$subject->id, $session->id]) }}"
                           class="inline-flex items-center rounded-lg bg-gray-50 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 transition">
                            تعديل
                        </a>
                        <form action="{{ route('teacher.my-subjects.sessions.destroy', [$subject->id, $session->id]) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الحصة؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-red-50 p-1.5 text-red-500 hover:bg-red-100 dark:bg-red-900 dark:text-red-300 transition">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ─── Subject Files Section ─── --}}
<div class="mb-6 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background: linear-gradient(135deg,#16a34a,#15803d);">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">ملفات المقرر</h2>
                <p class="text-xs text-gray-500">الملفات المرتبطة بهذا المقرر</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-semibold text-green-700 dark:bg-green-900 dark:text-green-300">
                {{ $subject->files->count() }}
            </span>
        </div>
        <button @click="uploadModal = true"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-xs font-medium text-white transition"
                style="background: linear-gradient(135deg,#16a34a,#15803d)">
            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
            </svg>
            رفع ملف
        </button>
    </div>

    @if($subject->files->isEmpty())
        <div class="flex flex-col items-center justify-center py-10 text-center">
            <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                <svg class="h-7 w-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="font-medium text-gray-500">لا توجد ملفات مرفقة بهذا المقرر</p>
            <button @click="uploadModal = true"
                    class="mt-3 inline-flex items-center gap-1.5 rounded-lg px-4 py-2 text-sm font-medium text-white"
                    style="background: linear-gradient(135deg,#16a34a,#15803d)">
                رفع ملف جديد
            </button>
        </div>
    @else
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($subject->files as $sFile)
                @php
                    $ext2 = strtolower($sFile->file_type ?? pathinfo($sFile->file_path ?? '', PATHINFO_EXTENSION));
                    if ($ext2 === 'pdf')                              { $ic2 = '#ef4444'; $il2 = 'PDF'; }
                    elseif (in_array($ext2, ['doc','docx']))          { $ic2 = '#2563eb'; $il2 = 'DOC'; }
                    elseif (in_array($ext2, ['xls','xlsx','csv']))    { $ic2 = '#16a34a'; $il2 = 'XLS'; }
                    elseif (in_array($ext2, ['ppt','pptx']))          { $ic2 = '#ea580c'; $il2 = 'PPT'; }
                    elseif (in_array($ext2, ['jpg','jpeg','png','gif','webp'])) { $ic2 = '#9333ea'; $il2 = strtoupper($ext2); }
                    else                                              { $ic2 = '#6b7280'; $il2 = strtoupper($ext2) ?: 'FILE'; }
                @endphp
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl text-white text-xs font-bold shadow-sm"
                         style="background:{{ $ic2 }}">
                        {{ $il2 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="truncate font-medium text-gray-900 dark:text-white text-sm">{{ $sFile->title }}</p>
                        <div class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-gray-500">
                            @if($sFile->file_size)
                                <span>{{ number_format($sFile->file_size / 1024, 1) }} KB</span>
                            @endif
                            @if($sFile->description)
                                <span class="truncate max-w-xs">{{ $sFile->description }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-shrink-0 items-center gap-2">
                        @if($sFile->file_path)
                            <a href="{{ asset('storage/' . $sFile->file_path) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 dark:bg-blue-900 dark:text-blue-300 transition">
                                عرض
                            </a>
                            <a href="{{ asset('storage/' . $sFile->file_path) }}" download
                               class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-100 dark:bg-green-900 dark:text-green-300 transition">
                                تحميل
                            </a>
                        @endif
                        <form action="{{ route('teacher.files.destroy', $sFile) }}" method="POST" class="inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-red-50 p-1.5 text-red-500 hover:bg-red-100 dark:bg-red-900 dark:text-red-300 transition">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ─── Session Files Section ─── --}}
<div class="mb-6 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">
    <div class="flex items-center gap-3 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
        <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background: linear-gradient(135deg,#0071AA,#005a88);">
            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <h2 class="text-base font-semibold text-gray-900 dark:text-white">ملفات الحصص</h2>
            <p class="text-xs text-gray-500">جميع الملفات المرفقة بالحصص</p>
        </div>
        <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-300">
            {{ $allFiles->count() }}
        </span>
    </div>

    @if($allFiles->isEmpty())
        <div class="flex flex-col items-center justify-center py-14 text-center">
            <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="font-medium text-gray-500">لا توجد ملفات مرفقة بالحصص</p>
            <p class="mt-1 text-sm text-gray-400">يمكنك إرفاق ملفات عند إنشاء أو تعديل الحصص</p>
        </div>
    @else
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($allFiles as $item)
                @php
                    $file    = $item['file'];
                    $session = $item['session'];
                    $ext     = strtolower(pathinfo($file->file_path ?? '', PATHINFO_EXTENSION));
                    if ($ext === 'pdf')                              { $ic = '#ef4444'; $il = 'PDF'; }
                    elseif (in_array($ext, ['doc','docx']))          { $ic = '#2563eb'; $il = 'DOC'; }
                    elseif (in_array($ext, ['xls','xlsx','csv']))    { $ic = '#16a34a'; $il = 'XLS'; }
                    elseif (in_array($ext, ['ppt','pptx']))          { $ic = '#ea580c'; $il = 'PPT'; }
                    elseif (in_array($ext, ['jpg','jpeg','png','gif','webp','svg'])) { $ic = '#9333ea'; $il = strtoupper($ext); }
                    else                                             { $ic = '#6b7280'; $il = strtoupper($ext) ?: 'FILE'; }
                @endphp
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl text-white text-xs font-bold shadow-sm"
                         style="background:{{ $ic }}">
                        {{ $il }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="truncate font-medium text-gray-900 dark:text-white text-sm">
                            {{ $file->title ?: basename($file->file_path ?? 'ملف') }}
                        </p>
                        <div class="mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                الحصة {{ $session->session_number }}: {{ $session->title }}
                            </span>
                            @if($file->file_size)
                                <span>{{ number_format($file->file_size / 1024, 1) }} KB</span>
                            @endif
                            @if($file->description)
                                <span class="truncate max-w-xs">{{ $file->description }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex flex-shrink-0 items-center gap-2">
                        @if($file->file_path)
                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 dark:bg-blue-900 dark:text-blue-300 transition">
                                عرض
                            </a>
                            <a href="{{ asset('storage/' . $file->file_path) }}" download
                               class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-100 dark:bg-green-900 dark:text-green-300 transition">
                                تحميل
                            </a>
                        @endif
                        <form action="{{ route('teacher.my-subjects.sessions.files.destroy', [$subject->id, $session->id, $file->id]) }}"
                              method="POST" class="inline"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-red-50 p-1.5 text-red-500 hover:bg-red-100 dark:bg-red-900 dark:text-red-300 transition">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ─── Upload Modal ─── --}}
<div x-show="uploadModal" x-cloak
     style="position:fixed;inset:0;z-index:9999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.5);"
     @keydown.escape.window="uploadModal = false">
    <div class="rounded-2xl bg-white dark:bg-gray-900 shadow-2xl w-full max-w-md mx-4 p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">رفع ملف للمقرر</h3>
            <button @click="uploadModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('teacher.files.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">عنوان الملف <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="مثال: محاضرة الوحدة الأولى">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الملف <span class="text-red-500">*</span></label>
                    <input type="file" name="file" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm"
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.png,.jpg,.jpeg">
                    <p class="mt-1 text-xs text-gray-400">PDF, Word, Excel, PowerPoint, صور — حد أقصى 50MB</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">وصف (اختياري)</label>
                    <textarea name="description" rows="2"
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="وصف مختصر للملف"></textarea>
                </div>
            </div>
            <div class="mt-5 flex gap-3">
                <button type="submit"
                        class="flex-1 rounded-lg py-2 text-sm font-semibold text-white transition"
                        style="background: linear-gradient(135deg,#16a34a,#15803d)">
                    رفع الملف
                </button>
                <button type="button" @click="uploadModal = false"
                        class="rounded-lg border border-gray-300 dark:border-gray-700 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>

</div>{{-- end x-data --}}
@endsection
