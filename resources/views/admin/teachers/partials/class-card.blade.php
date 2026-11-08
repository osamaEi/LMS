{{-- One class card on the teacher profile. Expects $class decorated by TeacherController@show. --}}
@php
    $past     = $class->is_past;
    $capacity = $class->max_students;
    $fill     = $capacity ? min(100, (int) round($class->students_total / $capacity * 100)) : null;
    $period   = collect([$class->start_date?->format('Y-m-d'), $class->end_date?->format('Y-m-d')])->filter()->implode(' → ');
    $tone     = $past
        ? ['bar' => 'from-rose-500 to-red-500', 'ring' => 'ring-red-100 dark:ring-red-900/40', 'chip' => 'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300', 'dot' => 'bg-red-500', 'progress' => 'bg-red-400']
        : ['bar' => 'from-emerald-400 to-teal-500', 'ring' => 'ring-emerald-100 dark:ring-emerald-900/40', 'chip' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300', 'dot' => 'bg-emerald-500 animate-pulse', 'progress' => 'bg-gradient-to-l from-emerald-400 to-teal-500'];
@endphp
<a href="{{ route('admin.classes.show', $class) }}"
   x-show="classVisible({{ Js::from(['direct' => $class->is_direct, 'past' => $past]) }})"
   class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 transition duration-200 hover:-translate-y-1 hover:shadow-xl dark:bg-gray-800 {{ $tone['ring'] }}">
    <span class="h-1.5 w-full bg-gradient-to-l {{ $tone['bar'] }}" aria-hidden="true"></span>

    <div class="flex flex-1 flex-col p-5">
        {{-- Title row --}}
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <h3 class="truncate text-base font-bold text-gray-900 group-hover:text-brand-600 dark:text-white">{{ $class->name }}</h3>
                <p class="mt-0.5 truncate text-xs text-gray-500 dark:text-gray-400">{{ $class->program->name_ar ?? 'بدون برنامج' }}</p>
            </div>
            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $tone['chip'] }}">
                <span class="h-1.5 w-1.5 rounded-full {{ $tone['dot'] }}"></span>
                {{ $past ? 'سابقة' : 'حالية' }}
            </span>
        </div>

        {{-- Relationship --}}
        <div class="mt-3 flex flex-wrap gap-1.5 text-[11px] font-semibold">
            @if($class->is_direct)
                <span class="rounded-md bg-amber-50 px-2 py-0.5 text-amber-700 ring-1 ring-inset ring-amber-600/20 dark:bg-amber-900/30 dark:text-amber-300">مدرب المجموعة</span>
            @else
                <span class="rounded-md bg-violet-50 px-2 py-0.5 text-violet-700 ring-1 ring-inset ring-violet-600/20 dark:bg-violet-900/30 dark:text-violet-300">يدرّس فيها</span>
                @if($class->teacher)
                <span class="rounded-md bg-gray-100 px-2 py-0.5 text-gray-600 dark:bg-gray-700 dark:text-gray-300">المسؤول: {{ $class->teacher->name }}</span>
                @endif
            @endif
            @foreach($class->my_subjects->take(2) as $subj)
                <span class="max-w-[10rem] truncate rounded-md bg-sky-50 px-2 py-0.5 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">{{ $subj->name_ar }}</span>
            @endforeach
            @if($class->my_subjects->count() > 2)
                <span class="rounded-md bg-sky-50 px-2 py-0.5 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">+{{ $class->my_subjects->count() - 2 }}</span>
            @endif
        </div>

        {{-- Stats --}}
        <dl class="mt-4 grid grid-cols-3 gap-2 text-center">
            <div class="rounded-xl bg-gray-50 px-2 py-2.5 dark:bg-gray-900/50">
                <dt class="text-[11px] text-gray-500 dark:text-gray-400">الطلاب</dt>
                <dd class="mt-0.5 text-lg font-bold text-gray-900 dark:text-white">
                    {{ $class->students_total }}@if($capacity)<span class="text-xs font-medium text-gray-400">/{{ $capacity }}</span>@endif
                </dd>
            </div>
            <div class="rounded-xl bg-gray-50 px-2 py-2.5 dark:bg-gray-900/50">
                <dt class="text-[11px] text-gray-500 dark:text-gray-400">جلساته</dt>
                <dd class="mt-0.5 text-lg font-bold text-gray-900 dark:text-white">
                    {{ $class->my_sessions }}@if($class->all_sessions > $class->my_sessions)<span class="text-xs font-medium text-gray-400">/{{ $class->all_sessions }}</span>@endif
                </dd>
            </div>
            <div class="rounded-xl bg-gray-50 px-2 py-2.5 dark:bg-gray-900/50">
                <dt class="text-[11px] text-gray-500 dark:text-gray-400">منجزة</dt>
                <dd class="mt-0.5 text-lg font-bold text-gray-900 dark:text-white">{{ $class->my_done }}</dd>
            </div>
        </dl>

        {{-- Progress --}}
        <div class="mt-4">
            <div class="mb-1 flex items-center justify-between text-[11px] font-medium text-gray-500 dark:text-gray-400">
                <span>تقدّم الجلسات</span>
                <span>{{ $class->progress }}%</span>
            </div>
            <div class="h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                <div class="h-full rounded-full {{ $tone['progress'] }}" style="width: {{ $class->progress }}%"></div>
            </div>
            @if($fill !== null)
            <div class="mt-2 mb-1 flex items-center justify-between text-[11px] font-medium text-gray-500 dark:text-gray-400">
                <span>الامتلاء</span>
                <span>{{ $fill }}%</span>
            </div>
            <div class="h-1.5 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                <div class="h-full rounded-full {{ $fill >= 90 ? 'bg-amber-500' : 'bg-sky-400' }}" style="width: {{ $fill }}%"></div>
            </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="flex-1 min-h-[1rem]" aria-hidden="true"></div>
        <div class="flex items-center justify-between gap-2 border-t border-gray-100 pt-3 text-xs text-gray-500 dark:border-gray-700 dark:text-gray-400">
            @if($class->my_next_at)
                <span class="inline-flex items-center gap-1 font-semibold text-violet-700 dark:text-violet-300">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span dir="ltr">{{ $class->my_next_at->format('Y-m-d H:i') }}</span>
                </span>
            @elseif($period)
                <span dir="ltr">{{ $period }}</span>
            @else
                <span>لا جلسات قادمة</span>
            @endif
            <svg class="h-4 w-4 rotate-180 text-gray-300 transition group-hover:-translate-x-1 group-hover:text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        </div>
    </div>
</a>
