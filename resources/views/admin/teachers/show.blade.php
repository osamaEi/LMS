@extends('layouts.dashboard')

@section('title', 'ملف المدرب — ' . $teacher->name)

@php
    $isActive = $teacher->status === 'active';
    $initials = collect(preg_split('/\s+/u', trim($teacher->name)))->filter()->take(2)
        ->map(fn ($w) => mb_substr($w, 0, 1))->join('');

    $statusStyles = [
        'active'    => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/30 dark:text-emerald-300',
        'pending'   => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300',
        'suspended' => 'bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/30 dark:text-red-300',
    ];
    $statusClass = $statusStyles[$teacher->status] ?? 'bg-gray-100 text-gray-600 ring-gray-500/20 dark:bg-gray-700 dark:text-gray-300';

    $programTypeLabels = [
        'training' => 'البرامج التدريبية',
        'english'  => 'دورات اللغة الإنجليزية',
        'course'   => 'الدورات التأهيلية',
        'diploma'  => 'الدبلومات',
    ];
    $programTypeShort = ['training' => 'تدريبي', 'english' => 'إنجليزي', 'course' => 'تأهيلي', 'diploma' => 'دبلوم'];

    $kpis = [
        ['label' => 'المقررات',        'value' => $subjects->count(),          'tab' => 'subjects', 'tone' => 'text-sky-600 bg-sky-50 dark:bg-sky-900/30 dark:text-sky-300'],
        ['label' => 'الدورات',         'value' => $courses->count(),           'tab' => 'programs', 'tone' => 'text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-300'],
        ['label' => 'المجموعات',       'value' => $classes->count(),           'tab' => 'classes',  'tone' => 'text-amber-600 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-300'],
        ['label' => 'الجلسات القادمة', 'value' => $sessionStats['upcoming'],   'tab' => null,       'tone' => 'text-violet-600 bg-violet-50 dark:bg-violet-900/30 dark:text-violet-300',
         'hint' => 'من أصل ' . $sessionStats['total'] . ' جلسة'],
    ];
@endphp

@section('content')
<div x-data="teacherProfile()" x-init="init()" class="mx-auto max-w-7xl">

    {{-- Breadcrumb --}}
    <nav class="mb-4 flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400" aria-label="مسار التنقل">
        <a href="{{ route('admin.teachers.index') }}" class="hover:text-brand-600">المدربون</a>
        <svg class="h-3.5 w-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $teacher->name }}</span>
    </nav>

    {{-- Flash + validation --}}
    @if(session('success'))
    <div role="status" class="mb-4 flex items-center gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
        <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
        {{ session('success') }}
    </div>
    @endif
    @if($errors->any())
    <div role="alert" class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
        <p class="font-semibold">تعذّر حفظ التعديلات:</p>
        <ul class="mt-1 list-inside list-disc">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
    @endif

    {{-- ─── Header ─────────────────────────────────────────────── --}}
    <section class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex items-center gap-5">
                @if($teacher->profile_photo)
                    <img src="{{ asset('storage/' . $teacher->profile_photo) }}" alt=""
                         class="h-20 w-20 shrink-0 rounded-2xl object-cover ring-4 ring-gray-50 dark:ring-gray-700">
                @else
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-brand-500 to-brand-700 text-2xl font-bold text-white ring-4 ring-gray-50 dark:ring-gray-700" aria-hidden="true">
                        {{ $initials }}
                    </div>
                @endif

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $teacher->name }}</h1>
                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">
                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                            {{ $teacher->getStatusDisplayName() }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ $teacher->getRoleDisplayName() }}@if($teacher->specialization) · {{ $teacher->specialization }}@endif
                        · انضم {{ $teacher->created_at->diffForHumans() }}
                    </p>
                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm text-gray-600 dark:text-gray-300">
                        <a href="mailto:{{ $teacher->email }}" class="inline-flex items-center gap-1.5 hover:text-brand-600" dir="ltr">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            {{ $teacher->email }}
                        </a>
                        @if($teacher->phone)
                        <a href="tel:{{ $teacher->phone }}" class="inline-flex items-center gap-1.5 hover:text-brand-600" dir="ltr">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            {{ $teacher->phone }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.teachers.edit', $teacher) }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    تعديل البيانات
                </a>

                @if($isActive)
                <form action="{{ route('admin.teachers.dashboard-access', $teacher) }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
                        دخول لوحة المدرّس
                    </button>
                </form>
                @endif

                <form action="{{ route('admin.teachers.toggle-status', $teacher) }}" method="POST"
                      onsubmit="return confirm({{ Js::from($isActive ? 'تعطيل حساب ' . $teacher->name . '؟ لن يتمكن من الدخول حتى يُعاد تفعيله.' : 'تفعيل حساب ' . $teacher->name . '؟') }})">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-semibold {{ $isActive ? 'border border-amber-300 bg-amber-50 text-amber-800 hover:bg-amber-100 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-200' : 'border border-emerald-300 bg-emerald-50 text-emerald-800 hover:bg-emerald-100 dark:border-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200' }}">
                        {{ $isActive ? 'تعطيل الحساب' : 'تفعيل الحساب' }}
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- ─── KPIs ───────────────────────────────────────────────── --}}
    <section class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4" aria-label="ملخص">
        @foreach($kpis as $kpi)
            @php
                $kpiBody = '<p class="text-sm text-gray-500 dark:text-gray-400">' . e($kpi['label']) . '</p>'
                    . '<p class="mt-2 flex items-baseline gap-2"><span class="text-3xl font-bold text-gray-900 dark:text-white">' . e($kpi['value']) . '</span>'
                    . (isset($kpi['hint']) ? '<span class="text-xs text-gray-400">' . e($kpi['hint']) . '</span>' : '') . '</p>'
                    . '<span class="mt-3 inline-block h-1 w-10 rounded-full ' . $kpi['tone'] . '"></span>';
                $kpiClass = 'rounded-2xl border border-gray-200 bg-white p-5 text-right shadow-sm dark:border-gray-700 dark:bg-gray-800';
            @endphp
            @if($kpi['tab'])
            <button type="button" @click="openTab('{{ $kpi['tab'] }}')" class="{{ $kpiClass }} transition hover:border-brand-300 hover:shadow">{!! $kpiBody !!}</button>
            @else
            <div class="{{ $kpiClass }}">{!! $kpiBody !!}</div>
            @endif
        @endforeach
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ─── Assignments (main) ─────────────────────────────── --}}
        <section class="lg:col-span-2 rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800" id="assignments">
            <header class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-6 pt-5 dark:border-gray-700">
                <h2 class="pb-4 text-lg font-bold text-gray-900 dark:text-white">التعيينات</h2>
                <div class="flex gap-1" role="tablist" aria-label="أنواع التعيين">
                    @foreach([['subjects', 'المقررات', $subjects->count()], ['programs', 'الدورات', $courses->count()], ['classes', 'المجموعات', $classes->count()]] as [$key, $label, $count])
                    <button type="button" role="tab" @click="openTab('{{ $key }}')"
                            :aria-selected="tab === '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                            class="-mb-px flex items-center gap-2 border-b-2 px-4 pb-4 text-sm font-semibold transition-colors">
                        {{ $label }}
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $count }}</span>
                    </button>
                    @endforeach
                </div>
            </header>

            {{-- Subjects --}}
            <div x-show="tab === 'subjects'" role="tabpanel" class="p-6">
                <div x-show="editing !== 'subjects'">
                    <div class="mb-4 flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">المقررات التي يدرّسها المدرب</p>
                        <button type="button" @click="editing = 'subjects'" class="rounded-lg bg-sky-50 px-3 py-1.5 text-sm font-semibold text-sky-700 hover:bg-sky-100 dark:bg-sky-900/30 dark:text-sky-300">تعديل المقررات</button>
                    </div>
                    @forelse($subjects as $subject)
                        @php $prog = $subject->program ?? $subject->term?->program; @endphp
                        <div class="flex items-center justify-between gap-3 border-b border-gray-100 py-3 last:border-0 dark:border-gray-700">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900 dark:text-white">{{ $subject->name_ar ?? $subject->name ?? '—' }}</p>
                                <p class="mt-0.5 truncate text-xs text-gray-500 dark:text-gray-400">
                                    {{ $prog->name_ar ?? 'غير مرتبط ببرنامج' }}@if($subject->code) · <span dir="ltr">{{ $subject->code }}</span>@endif
                                </p>
                            </div>
                            @if($prog)
                            <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $programTypeShort[$prog->type] ?? $prog->type }}</span>
                            @endif
                        </div>
                    @empty
                        @include('admin.teachers.partials.empty-assignment', ['text' => 'لم يُعيَّن أي مقرر لهذا المدرب بعد', 'action' => 'subjects', 'cta' => 'تعيين مقررات'])
                    @endforelse
                </div>

                <form x-show="editing === 'subjects'" x-cloak method="POST" action="{{ route('admin.teachers.assign-subjects', $teacher) }}"
                      x-data="assignPanel({{ $assignedSubjectIds->toJson() }})" @submit="onSubmit($event, 'مقرر', 'subjects')">
                    @csrf
                    @include('admin.teachers.partials.assign-toolbar', ['placeholder' => 'ابحث باسم المقرر أو رمزه…'])
                    <div class="max-h-[28rem] overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        @foreach($programsByType as $type => $programs)
                            @foreach($programs as $prog)
                                @php $progSubjects = $prog->terms->flatMap->subjects->merge($prog->subjects)->unique('id')->sortBy('name_ar'); @endphp
                                @if($progSubjects->count())
                                <fieldset data-search="{{ $prog->name_ar }} {{ $progSubjects->map(fn ($s) => $s->name_ar . ' ' . $s->code . ' ' . $s->name_en)->implode(' ') }}"
                                          x-show="matches($el.dataset.search)">
                                    <legend class="sticky top-0 z-10 w-full bg-gray-50 px-4 py-2 text-xs font-bold text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                                        {{ $prog->name_ar }} <span class="font-normal">· {{ $programTypeShort[$type] ?? $type }}</span>
                                    </legend>
                                    @foreach($progSubjects as $subj)
                                        @include('admin.teachers.partials.assign-option', [
                                            'name' => 'subjects[]', 'id' => $subj->id, 'label' => $subj->name_ar,
                                            'meta' => $subj->code, 'search' => $subj->name_ar . ' ' . $subj->code . ' ' . $subj->name_en, 'accent' => 'text-sky-600',
                                        ])
                                    @endforeach
                                </fieldset>
                                @endif
                            @endforeach
                        @endforeach

                        @if($orphanSubjects->count())
                        <fieldset data-search="{{ $orphanSubjects->map(fn ($s) => $s->name_ar . ' ' . $s->code)->implode(' ') }}" x-show="matches($el.dataset.search)">
                            <legend class="sticky top-0 z-10 w-full bg-gray-50 px-4 py-2 text-xs font-bold text-gray-500 dark:bg-gray-900 dark:text-gray-400">مقررات غير مرتبطة ببرنامج</legend>
                            @foreach($orphanSubjects as $subj)
                                @include('admin.teachers.partials.assign-option', [
                                    'name' => 'subjects[]', 'id' => $subj->id, 'label' => $subj->name_ar,
                                    'meta' => $subj->code, 'search' => $subj->name_ar . ' ' . $subj->code, 'accent' => 'text-sky-600',
                                ])
                            @endforeach
                        </fieldset>
                        @endif
                    </div>
                    @include('admin.teachers.partials.assign-footer', ['save' => 'حفظ المقررات', 'tone' => 'bg-sky-600 hover:bg-sky-700'])
                </form>
            </div>

            {{-- Programs / courses --}}
            <div x-show="tab === 'programs'" x-cloak role="tabpanel" class="p-6">
                <div x-show="editing !== 'programs'">
                    <div class="mb-4 flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">الدورات والبرامج المسندة للمدرب</p>
                        <button type="button" @click="editing = 'programs'" class="rounded-lg bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300">تعديل الدورات</button>
                    </div>
                    @forelse($courses as $program)
                        <div class="flex items-center justify-between gap-3 border-b border-gray-100 py-3 last:border-0 dark:border-gray-700">
                            <p class="truncate font-medium text-gray-900 dark:text-white">{{ $program->name_ar }}</p>
                            <span class="shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-700 dark:text-gray-300">{{ $programTypeShort[$program->type] ?? $program->type }}</span>
                        </div>
                    @empty
                        @include('admin.teachers.partials.empty-assignment', ['text' => 'لا توجد دورات مسندة لهذا المدرب', 'action' => 'programs', 'cta' => 'تعيين دورات'])
                    @endforelse
                </div>

                <form x-show="editing === 'programs'" x-cloak method="POST" action="{{ route('admin.teachers.assign-programs', $teacher) }}"
                      x-data="assignPanel({{ $assignedProgramIds->toJson() }})" @submit="onSubmit($event, 'دورة', 'programs')">
                    @csrf
                    @include('admin.teachers.partials.assign-toolbar', ['placeholder' => 'ابحث باسم الدورة…'])
                    <div class="max-h-[28rem] overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        @forelse($allPrograms as $type => $programs)
                        <fieldset data-search="{{ $programs->pluck('name_ar')->implode(' ') }}" x-show="matches($el.dataset.search)">
                            <legend class="sticky top-0 z-10 w-full bg-gray-50 px-4 py-2 text-xs font-bold text-gray-500 dark:bg-gray-900 dark:text-gray-400">{{ $programTypeLabels[$type] ?? $type }}</legend>
                            @foreach($programs as $prog)
                                @include('admin.teachers.partials.assign-option', [
                                    'name' => 'program_ids[]', 'id' => $prog->id, 'label' => $prog->name_ar,
                                    'meta' => $prog->status === 'active' ? 'نشط' : null, 'search' => $prog->name_ar, 'accent' => 'text-emerald-600',
                                ])
                            @endforeach
                        </fieldset>
                        @empty
                        <p class="py-10 text-center text-sm text-gray-400">لا توجد دورات</p>
                        @endforelse
                    </div>
                    @include('admin.teachers.partials.assign-footer', ['save' => 'حفظ الدورات', 'tone' => 'bg-emerald-600 hover:bg-emerald-700'])
                </form>
            </div>

            {{-- Classes --}}
            <div x-show="tab === 'classes'" x-cloak role="tabpanel" class="p-6">
                <div x-show="editing !== 'classes'">
                    <div class="mb-4 flex items-center justify-between">
                        <p class="text-sm text-gray-500 dark:text-gray-400">المجموعات التي يتولاها المدرب</p>
                        <button type="button" @click="editing = 'classes'" class="rounded-lg bg-amber-50 px-3 py-1.5 text-sm font-semibold text-amber-700 hover:bg-amber-100 dark:bg-amber-900/30 dark:text-amber-300">تعديل المجموعات</button>
                    </div>
                    @forelse($classes as $class)
                        <a href="{{ route('admin.classes.show', $class) }}"
                           class="group flex items-center justify-between gap-3 border-b border-gray-100 py-3 last:border-0 dark:border-gray-700">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-gray-900 group-hover:text-brand-600 dark:text-white">{{ $class->name }}</p>
                                <p class="mt-0.5 truncate text-xs text-gray-500 dark:text-gray-400">{{ $class->program->name_ar ?? 'بدون برنامج' }}</p>
                            </div>
                            <svg class="h-4 w-4 shrink-0 rotate-180 text-gray-300 group-hover:text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    @empty
                        @include('admin.teachers.partials.empty-assignment', ['text' => 'لا توجد مجموعات معيّنة لهذا المدرب', 'action' => 'classes', 'cta' => 'تعيين مجموعات'])
                    @endforelse
                </div>

                <form x-show="editing === 'classes'" x-cloak method="POST" action="{{ route('admin.teachers.assign-classes', $teacher) }}"
                      x-data="assignPanel({{ $assignedClassIds->toJson() }})" @submit="onSubmit($event, 'مجموعة', 'classes')">
                    @csrf
                    <p class="mb-3 flex items-start gap-2 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
                        <svg class="mt-0.5 h-4 w-4 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/></svg>
                        لكل مجموعة مدرب واحد فقط — تعيين مجموعة مسندة لمدرب آخر سينقلها إلى هذا المدرب.
                    </p>
                    @include('admin.teachers.partials.assign-toolbar', ['placeholder' => 'ابحث باسم المجموعة…'])
                    <div class="max-h-[28rem] overflow-y-auto rounded-xl border border-gray-200 dark:border-gray-700">
                        @forelse($allClasses as $programName => $groupClasses)
                        <fieldset data-search="{{ $programName }} {{ $groupClasses->pluck('name')->implode(' ') }}" x-show="matches($el.dataset.search)">
                            <legend class="sticky top-0 z-10 w-full bg-gray-50 px-4 py-2 text-xs font-bold text-gray-500 dark:bg-gray-900 dark:text-gray-400">{{ $programName }}</legend>
                            @foreach($groupClasses as $class)
                                @php $heldBy = $class->teacher_id && $class->teacher_id !== $teacher->id ? $class->teacher : null; @endphp
                                @include('admin.teachers.partials.assign-option', [
                                    'name' => 'class_ids[]', 'id' => $class->id, 'label' => $class->name,
                                    'meta' => null, 'search' => $programName . ' ' . $class->name, 'accent' => 'text-amber-600',
                                    'warning' => $heldBy ? 'مسندة حالياً إلى ' . $heldBy->name : null,
                                ])
                            @endforeach
                        </fieldset>
                        @empty
                        <p class="py-10 text-center text-sm text-gray-400">لا توجد مجموعات</p>
                        @endforelse
                    </div>
                    @include('admin.teachers.partials.assign-footer', ['save' => 'حفظ المجموعات', 'tone' => 'bg-amber-600 hover:bg-amber-700'])
                </form>
            </div>
        </section>

        {{-- ─── Sidebar ────────────────────────────────────────── --}}
        <aside class="space-y-6">
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-base font-bold text-gray-900 dark:text-white">البيانات الشخصية</h2>
                <dl class="divide-y divide-gray-100 text-sm dark:divide-gray-700">
                    @foreach(array_filter([
                        'رقم الهوية'     => $teacher->national_id,
                        'الجنس'          => $teacher->gender ? ($teacher->gender === 'male' ? 'ذكر' : 'أنثى') : null,
                        'الجنسية'        => $teacher->nationality,
                        'تاريخ الميلاد'  => $teacher->date_of_birth?->format('Y-m-d'),
                        'التخصص'         => $teacher->specialization,
                        'تاريخ التسجيل' => $teacher->created_at->format('Y-m-d'),
                    ]) as $label => $value)
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <dt class="text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                        <dd class="text-left font-medium text-gray-900 dark:text-white" @if($label === 'رقم الهوية' || str_contains($label, 'تاريخ')) dir="ltr" @endif>{{ $value }}</dd>
                    </div>
                    @endforeach
                </dl>
            </section>

            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-4 text-base font-bold text-gray-900 dark:text-white">الحساب والروابط</h2>
                <dl class="divide-y divide-gray-100 text-sm dark:divide-gray-700">
                    @foreach(['البريد الإلكتروني' => $teacher->email_verified_at, 'رقم الجوال' => $teacher->phone_verified_at] as $label => $verifiedAt)
                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <dt class="text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                        <dd>
                            @if($verifiedAt)
                                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">موثّق</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-semibold text-gray-500 dark:bg-gray-700 dark:text-gray-400">غير موثّق</span>
                            @endif
                        </dd>
                    </div>
                    @endforeach

                    <div class="py-2.5">
                        <dt class="mb-1.5 text-gray-500 dark:text-gray-400">رابط زووم</dt>
                        <dd>
                            @if($teacher->zoom_join_url)
                            <div class="flex items-center gap-2">
                                <a href="{{ $teacher->zoom_join_url }}" target="_blank" rel="noopener noreferrer" dir="ltr"
                                   class="min-w-0 flex-1 truncate text-brand-600 hover:underline" title="{{ $teacher->zoom_join_url }}">{{ $teacher->zoom_join_url }}</a>
                                <button type="button" @click="copy({{ Js::from($teacher->zoom_join_url) }}, $event)"
                                        class="shrink-0 rounded-md bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">نسخ</button>
                            </div>
                            @else
                            <span class="text-amber-600 dark:text-amber-400">لم يُضبط — لن تظهر روابط زووم لجلسات هذا المدرب</span>
                            @endif
                        </dd>
                    </div>

                    <div class="flex items-center justify-between gap-4 py-2.5">
                        <dt class="text-gray-500 dark:text-gray-400">الشهادة</dt>
                        <dd>
                            @if($teacher->certificate_url)
                            <a href="{{ $teacher->certificate_url }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-brand-600 hover:underline">عرض الشهادة</a>
                            @else
                            <span class="text-gray-400">غير مرفقة</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            @if($teacher->bio)
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h2 class="mb-3 text-base font-bold text-gray-900 dark:text-white">نبذة</h2>
                <p class="whitespace-pre-line text-sm leading-relaxed text-gray-600 dark:text-gray-300">{{ $teacher->bio }}</p>
            </section>
            @endif
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Page state: active tab + which tab is in edit mode. The tab survives the
// redirect after saving so the admin lands back where they were.
function teacherProfile() {
    return {
        tab: 'subjects',
        editing: null,

        init() {
            try {
                const saved = sessionStorage.getItem('teacher-profile-tab');
                if (saved) { this.tab = saved; sessionStorage.removeItem('teacher-profile-tab'); }
            } catch (e) {}
            if (['#subjects', '#programs', '#classes'].includes(location.hash)) this.tab = location.hash.slice(1);
        },

        openTab(key) {
            this.tab = key;
            this.editing = null;
            document.getElementById('assignments')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },

        copy(text, event) {
            const btn = event.currentTarget, original = btn.textContent;
            const done = ok => { btn.textContent = ok ? 'تم ✓' : 'تعذّر'; setTimeout(() => btn.textContent = original, 1500); };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => done(true), () => done(false));
            } else {
                const t = Object.assign(document.createElement('textarea'), { value: text });
                t.style.cssText = 'position:fixed;opacity:0';
                document.body.appendChild(t); t.select();
                try { done(document.execCommand('copy')); } catch (e) { done(false); }
                t.remove();
            }
        },
    };
}

// One assign form: live search, a running "+added / −removed" summary, and a
// confirm before anything is unassigned.
function assignPanel(initialIds) {
    return {
        initial: initialIds.map(Number),
        selected: initialIds.map(Number),
        query: '',
        submitting: false,

        // أ/إ/آ ≡ ا, ة ≡ ه, ى ≡ ي, no diacritics, Arabic-Indic digits → ASCII.
        normalize(value) {
            return (value || '').toLowerCase()
                .replace(/[ـً-ْ]/g, '')
                .replace(/[آأإٱ]/g, 'ا')
                .replace(/ى/g, 'ي')
                .replace(/ة/g, 'ه')
                .replace(/[٠-٩]/g, d => String.fromCharCode(d.charCodeAt(0) - 0x0630))
                .replace(/\s+/g, ' ')
                .trim();
        },

        matches(haystack) {
            const q = this.normalize(this.query);
            if (!q) return true;
            const hay = this.normalize(haystack);
            return q.split(' ').every(term => hay.includes(term));
        },

        isChecked(id) { return this.selected.includes(id); },

        toggle(id, checked) {
            this.selected = checked
                ? [...new Set([...this.selected, id])]
                : this.selected.filter(x => x !== id);
        },

        get added()   { return this.selected.filter(id => !this.initial.includes(id)).length; },
        get removed() { return this.initial.filter(id => !this.selected.includes(id)).length; },
        get dirty()   { return this.added > 0 || this.removed > 0; },

        reset() { this.selected = [...this.initial]; this.query = ''; },

        // Returns whether to leave edit mode; the caller clears the parent's
        // `editing` so this component never reaches into outer state.
        cancel() {
            if (this.dirty && !confirm('تجاهل التغييرات غير المحفوظة؟')) return false;
            this.reset();
            return true;
        },

        onSubmit(event, noun, tabKey) {
            if (this.removed && !confirm('سيتم إلغاء تعيين ' + this.removed + ' ' + noun + ' من هذا المدرب. متابعة؟')) {
                event.preventDefault();
                return;
            }
            try { sessionStorage.setItem('teacher-profile-tab', tabKey); } catch (e) {}
            this.submitting = true;
        },
    };
}
</script>
@endpush
