{{-- One class card on the teacher profile. Expects $class decorated by TeacherController@show; styles live in show.blade.php (.tp-*). --}}
@php
    $past     = $class->is_past;
    $capacity = $class->max_students;
    $fill     = $capacity ? min(100, (int) round($class->students_total / $capacity * 100)) : null;
    $period   = collect([$class->start_date?->format('Y-m-d'), $class->end_date?->format('Y-m-d')])->filter()->implode(' → ');
@endphp
<a href="{{ route('admin.classes.show', $class) }}" class="tp-class {{ $past ? 'past' : '' }}"
   x-show="classVisible({{ Js::from(['direct' => (bool) $class->is_direct, 'past' => (bool) $past]) }})">
    <div class="tp-class-band">
        <div class="tp-class-band-top">
            <div style="min-width:0;">
                <h3 class="tp-class-name">{{ $class->name }}</h3>
                <div class="tp-class-prog">{{ $class->program->name_ar ?? 'بدون برنامج' }}</div>
            </div>
            <span class="tp-class-state"><i></i>{{ $past ? 'سابقة' : 'حالية' }}</span>
        </div>
    </div>

    <div class="tp-class-body">
        <div class="tp-tags">
            @if($class->is_direct)
                <span class="tp-pill blue">مدرب المجموعة</span>
            @else
                <span class="tp-pill amber">يدرّس فيها</span>
                @if($class->teacher)
                <span class="tp-pill gray">المسؤول: {{ $class->teacher->name }}</span>
                @endif
            @endif
            @foreach($class->my_subjects->take(2) as $subj)
                <span class="tp-pill gray" title="{{ $subj->name_ar }}">{{ $subj->name_ar }}</span>
            @endforeach
            @if($class->my_subjects->count() > 2)
                <span class="tp-pill gray">+{{ $class->my_subjects->count() - 2 }}</span>
            @endif
        </div>

        <dl class="tp-nums">
            <div>
                <dt>الطلاب</dt>
                <dd>{{ $class->students_total }}@if($capacity)<small>/{{ $capacity }}</small>@endif</dd>
            </div>
            <div>
                <dt>جلساته</dt>
                <dd>{{ $class->my_sessions }}@if($class->all_sessions > $class->my_sessions)<small>/{{ $class->all_sessions }}</small>@endif</dd>
            </div>
            <div>
                <dt>منجزة</dt>
                <dd>{{ $class->my_done }}</dd>
            </div>
        </dl>

        <div class="tp-bar-label"><span>تقدّم الجلسات</span><span>{{ $class->progress }}%</span></div>
        <div class="tp-bar"><span style="width: {{ $class->progress }}%"></span></div>
        @if($fill !== null)
        <div class="tp-bar-label"><span>الامتلاء</span><span>{{ $fill }}%</span></div>
        <div class="tp-bar thin {{ $fill >= 90 ? 'warn' : '' }}"><span style="width: {{ $fill }}%"></span></div>
        @endif

        <div class="tp-spacer" aria-hidden="true"></div>
        <div class="tp-class-foot">
            @if($class->my_next_at)
                <span class="next">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span dir="ltr">{{ $class->my_next_at->format('Y-m-d H:i') }}</span>
                </span>
            @elseif($period)
                <span dir="ltr">{{ $period }}</span>
            @else
                <span>لا جلسات قادمة</span>
            @endif
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </div>
    </div>
</a>
