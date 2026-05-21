@extends('layouts.dashboard')

@section('title', 'دوراتي')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">دوراتي</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">الدورات والبرامج المسندة إليك</p>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900 dark:text-green-200">
    {{ session('success') }}
</div>
@endif

<!-- Statistics -->
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
    @php
        $typeLabels = ['training' => 'تدريبي', 'english' => 'إنجليزي', 'course' => 'دورة'];
        $typeColors = [
            'training' => ['from' => '#0071AA', 'to' => '#005a88'],
            'english'  => ['from' => '#d97706', 'to' => '#b45309'],
            'course'   => ['from' => '#10b981', 'to' => '#059669'],
        ];
    @endphp
    <div class="rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <div>
                <p class="text-sm" style="color: rgba(255,255,255,0.8);">إجمالي الدورات</p>
                <p class="text-2xl font-bold text-white">{{ $programs->count() }}</p>
            </div>
        </div>
    </div>
    @foreach(['training','english','course'] as $t)
    @php $cnt = $programs->where('type', $t)->count(); @endphp
    @if($cnt)
    <div class="rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, {{ $typeColors[$t]['from'] }} 0%, {{ $typeColors[$t]['to'] }} 100%);">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm" style="color: rgba(255,255,255,0.8);">{{ $typeLabels[$t] }}</p>
                <p class="text-2xl font-bold text-white">{{ $cnt }}</p>
            </div>
        </div>
    </div>
    @endif
    @endforeach
</div>

<!-- Programs Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($programs as $program)
    @php
        $type = $program->type;
        $cfg  = [
            'training' => ['label' => 'تدريبي',  'bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'from' => '#0071AA', 'to' => '#005a88'],
            'english'  => ['label' => 'إنجليزي', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'from' => '#d97706', 'to' => '#b45309'],
            'course'   => ['label' => 'دورة',    'bg' => 'bg-green-100',  'text' => 'text-green-700',  'from' => '#10b981', 'to' => '#059669'],
        ][$type] ?? ['label' => $type, 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'from' => '#6b7280', 'to' => '#4b5563'];
    @endphp
    <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden hover:shadow-lg transition-shadow">
        <!-- Banner -->
        <div class="h-36 flex items-center justify-center" style="background: linear-gradient(135deg, {{ $cfg['from'] }}, {{ $cfg['to'] }});">
            <svg class="h-14 w-14" style="color: rgba(255,255,255,0.45);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </div>

        <div class="p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="rounded-full px-3 py-1 text-xs font-medium {{ $cfg['bg'] }} {{ $cfg['text'] }}">
                    {{ $cfg['label'] }}
                </span>
                @if($program->status === 'active')
                    <span class="rounded-full bg-success-50 px-3 py-1 text-xs font-medium text-success-600 dark:bg-success-900 dark:text-success-200">نشط</span>
                @else
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ $program->status ?? 'غير محدد' }}</span>
                @endif
            </div>

            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ $program->name_ar }}</h3>
            @if($program->name_en)
            <p class="text-sm text-gray-400 dark:text-gray-500 mb-3">{{ $program->name_en }}</p>
            @endif

            <div class="flex items-center gap-4 mb-4 text-sm text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    {{ $program->sessions_count }} محاضرة
                </span>
                <span class="flex items-center gap-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{ $program->enrolled_students_count }} متدرب
                </span>
            </div>
            <a href="{{ route('teacher.my-courses.show', $program->id) }}"
               class="flex items-center justify-center gap-2 w-full rounded-lg px-4 py-2.5 text-sm font-medium text-white transition-colors"
               style="background: linear-gradient(135deg, {{ $cfg['from'] }}, {{ $cfg['to'] }});">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                عرض الدورة والمحاضرات
            </a>
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="rounded-xl border border-gray-200 bg-white p-12 dark:border-gray-800 dark:bg-gray-900 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
            <p class="mt-4 text-sm font-medium text-gray-900 dark:text-white">لا توجد دورات مسندة إليك</p>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">تواصل مع الإدارة لإسناد دورات</p>
        </div>
    </div>
    @endforelse
</div>
@endsection
