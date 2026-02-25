@extends('layouts.dashboard')

@section('title', $subject->name)

@section('content')
<div class="mb-6">
    <!-- Breadcrumb -->
    <nav class="mb-4 text-sm">
        <ol class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('teacher.my-subjects.index') }}" class="hover:text-brand-500">موادي</a></li>
            <li>/</li>
            <li class="text-gray-900 dark:text-white">{{ $subject->name }}</li>
        </ol>
    </nav>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $subject->name }}</h1>
                @if($subject->status === 'active')
                    <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                {{ $subject->term->name ?? '-' }} - {{ $subject->term->program->name ?? '-' }}
                @if($subject->code)
                    <span class="mx-2">|</span>
                    <span class="font-mono">{{ $subject->code }}</span>
                @endif
            </p>
        </div>
     
    </div>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900 dark:text-green-200">
    {{ session('success') }}
</div>
@endif

<!-- Statistics Cards -->



{{-- ════════════════════════════════════
     Files Section
════════════════════════════════════ --}}
<div class="mb-6 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden">

    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 px-6 py-4">
        <div class="flex items-center gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-xl" style="background: linear-gradient(135deg,#0071AA,#005a88);">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">ملفات المادة</h2>
                <p class="text-xs text-gray-500">جميع الملفات المرفقة بالحصص</p>
            </div>
            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                {{ $allFiles->count() }}
            </span>
        </div>
    </div>

    @if($allFiles->isEmpty())
        <div class="flex flex-col items-center justify-center py-14 text-center">
            <div class="mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="font-medium text-gray-500">لا توجد ملفات مرفقة</p>
            <p class="mt-1 text-sm text-gray-400">يمكنك إرفاق ملفات عند إنشاء أو تعديل الحصص</p>
        </div>
    @else
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($allFiles as $item)
                @php
                    $file = $item['file'];
                    $session = $item['session'];
                    $ext = $file->file_path ? strtolower(pathinfo($file->file_path, PATHINFO_EXTENSION)) : '';
                    $isPdf  = $ext === 'pdf';
                    $isImg  = in_array($ext, ['jpg','jpeg','png','gif','webp','svg']);
                    $isWord = in_array($ext, ['doc','docx']);
                    $isExcel= in_array($ext, ['xls','xlsx','csv']);
                    $isPpt  = in_array($ext, ['ppt','pptx']);

                    // Icon Tailwind class & label
                    if ($isPdf)      { $iconClass = 'bg-red-500';    $iconLabel = 'PDF'; }
                    elseif ($isImg)  { $iconClass = 'bg-purple-500'; $iconLabel = strtoupper($ext); }
                    elseif ($isWord) { $iconClass = 'bg-blue-600';   $iconLabel = 'DOC'; }
                    elseif ($isExcel){ $iconClass = 'bg-green-600';  $iconLabel = 'XLS'; }
                    elseif ($isPpt)  { $iconClass = 'bg-orange-600'; $iconLabel = 'PPT'; }
                    else             { $iconClass = 'bg-gray-500';   $iconLabel = strtoupper($ext) ?: 'FILE'; }
                @endphp
                <div class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">

                    {{-- Type icon --}}
                    <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl text-white text-xs font-bold shadow-sm {{ $iconClass }}">
                        {{ $iconLabel }}
                    </div>

                    {{-- Info --}}
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
                                <span>{{ $file->getFormattedSize() }}</span>
                            @endif
                            @if($file->description)
                                <span class="truncate max-w-xs">{{ $file->description }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-shrink-0 items-center gap-2">
                        @if($file->file_path)
                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-100 dark:bg-blue-900 dark:text-blue-300 transition">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                عرض
                            </a>
                            <a href="{{ asset('storage/' . $file->file_path) }}" download
                               class="inline-flex items-center gap-1.5 rounded-lg bg-green-50 px-3 py-1.5 text-xs font-medium text-green-700 hover:bg-green-100 dark:bg-green-900 dark:text-green-300 transition">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
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


@endsection
