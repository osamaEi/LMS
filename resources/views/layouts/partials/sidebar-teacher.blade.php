{{-- ═══ الرئيسية ═══ --}}
<li style="padding:16px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">{{ __('Main') }}</span>
</li>

<!-- لوحة التحكم -->
<li>
    <a href="{{ route('teacher.dashboard') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.99999 1.66666C5.39999 1.66666 1.66666 5.39999 1.66666 9.99999C1.66666 14.6 5.39999 18.3333 9.99999 18.3333C14.6 18.3333 18.3333 14.6 18.3333 9.99999C18.3333 5.39999 14.6 1.66666 9.99999 1.66666ZM9.99999 16.6667C6.31666 16.6667 3.33332 13.6833 3.33332 9.99999C3.33332 6.31666 6.31666 3.33332 9.99999 3.33332C13.6833 3.33332 16.6667 6.31666 16.6667 9.99999C16.6667 13.6833 13.6833 16.6667 9.99999 16.6667Z"/>
        </svg>
        <span>{{ __('Dashboard') }}</span>
    </a>
</li>

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

@php
$_sessionSubjectIds = \App\Models\Session::where('teacher_id', auth()->id())->whereNotNull('subject_id')->distinct()->pluck('subject_id');
$_teacherSubjects  = \App\Models\Subject::where(function ($q) use ($_sessionSubjectIds) {
                            $q->assignedToTeacher(auth()->id())->orWhereIn('id', $_sessionSubjectIds);
                        })
                        ->where(function ($q) {
                            $q->whereNotNull('class_id')->orWhereHas('term', fn($tq) => $tq->whereNotNull('class_id'));
                        })
                        ->with(['program:id,type,name_ar', 'term.program:id,type,name_ar'])->get();
$_teachingPrograms = auth()->user()->teachingPrograms()->get(['id', 'type', 'name_ar']);

// Resolve program types using PHP arrays to avoid Eloquent Collection pitfalls
$_subjectTypeArr  = $_teacherSubjects->map(fn($s) => $s->program?->type ?? $s->term?->program?->type)
                                     ->filter()->values()->toArray();
$_programTypeArr  = $_teachingPrograms->pluck('type')->toArray();
$_byType          = collect(array_values(array_unique(array_merge($_subjectTypeArr, $_programTypeArr))));

$_hasAny     = $_byType->isNotEmpty();
$_hasDiploma = $_byType->contains('diploma');
$_hasCourses = $_byType->contains(fn($t) => in_array($t, ['training', 'english', 'course']));

$_courseTypeConfig = [
    'training' => ['label' => 'البرامج التدريبية',      'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
    'english'  => ['label' => 'دورات اللغة الإنجليزية', 'icon' => 'M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129'],
    'course'   => ['label' => 'الدورات التأهيلية',       'icon' => 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z'],
];
@endphp

{{-- ═══ التدريس ═══ --}}
@if($_hasAny)
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">{{ __('Teaching') }}</span>
</li>

{{-- مقررات الدبلومات --}}
@if($_hasDiploma)
<li>
    <a href="{{ route('teacher.my-subjects.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.my-subjects.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill:none;stroke:currentColor;stroke-width:2;" width="20" height="20" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 7v-6m0 0l-9-5m9 5l9-5"/>
        </svg>
        <span>مقرراتي</span>
    </a>
</li>
@endif

{{-- الدورات التأهيلية / الإنجليزية / التدريبية --}}

<li>
    <a href="{{ route('teacher.my-courses.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.my-courses.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
      
        <span>الدورات</span>
    </a>
</li>

@endif

<!-- الجدول التدريبي -->
<li>
    <a href="{{ route('teacher.schedule') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.schedule') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.8333 2.5H15V0.833336H13.3333V2.5H6.66667V0.833336H5V2.5H4.16667C3.24167 2.5 2.5 3.25 2.5 4.16667V15.8333C2.5 16.75 3.24167 17.5 4.16667 17.5H15.8333C16.75 17.5 17.5 16.75 17.5 15.8333V4.16667C17.5 3.25 16.75 2.5 15.8333 2.5ZM15.8333 15.8333H4.16667V7.5H15.8333V15.8333ZM15.8333 5.83334H4.16667V4.16667H15.8333V5.83334ZM6.66667 10H9.16667V12.5H6.66667V10Z"/>
        </svg>
        <span>{{ __('Academic Schedule') }}</span>
    </a>
</li>

<!-- الاختبارات - Dropdown -->
@if($_hasAny)
<li x-data="{ open: {{ request()->routeIs('teacher.quizzes.*') ? 'true' : 'false' }} }">
    <button @click="open = !open"
            class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium w-full {{ request()->routeIs('teacher.quizzes.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.8333 2.5H4.16667C3.24167 2.5 2.5 3.24167 2.5 4.16667V15.8333C2.5 16.7583 3.24167 17.5 4.16667 17.5H15.8333C16.7583 17.5 17.5 16.7583 17.5 15.8333V4.16667C17.5 3.24167 16.7583 2.5 15.8333 2.5ZM15.8333 15.8333H4.16667V4.16667H15.8333V15.8333ZM13.3333 6.66667H6.66667V8.33333H13.3333V6.66667ZM10 10H6.66667V11.6667H10V10ZM10 13.3333H6.66667V15H10V13.3333Z"/>
        </svg>
        <span>{{ __('Quizzes') }}</span>
        <svg class="fill-current ms-auto transition-transform duration-200" :class="{ 'rotate-180': open }" width="16" height="16" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>
    <!-- Dropdown Menu -->
    <ul x-show="open" x-collapse class="mt-2 mr-4 space-y-1 border-r-2 border-white/20 pr-3">
        @foreach($_teacherSubjects->sortBy('name') as $subject)
        <li>
            <a href="{{ route('teacher.quizzes.index', $subject->id) }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-white transition-colors {{ request()->is('teacher/subjects/'.$subject->id.'/quizzes*') ? 'bg-white/20' : 'hover:bg-white/10' }}">
                <span class="w-2 h-2 rounded-full {{ request()->is('teacher/subjects/'.$subject->id.'/quizzes*') ? 'bg-white' : 'bg-white/50' }}"></span>
                <span class="truncate">{{ $subject->name }}@if($subject->code) <span class="text-white/60">({{ $subject->code }})</span>@endif</span>
            </a>
        </li>
        @endforeach
    </ul>
</li>
@endif

{{-- ═══ فاصل ═══ --}}
@if($_hasAny)
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

<!-- الواجبات المنزلية -->
<li>
    <a href="{{ route('teacher.homework.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.homework.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1s-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
        </svg>
        <span>{{ __('Homework') }}</span>
    </a>
</li>
@endif

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>



{{-- ═══ الدعم ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">{{ __('Support') }}</span>
</li>

<!-- تذاكر الدعم -->
<li>
    <a href="{{ route('teacher.tickets.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.tickets.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 4.16667H2.5C1.58333 4.16667 0.833333 4.91667 0.833333 5.83333V14.1667C0.833333 15.0833 1.58333 15.8333 2.5 15.8333H17.5C18.4167 15.8333 19.1667 15.0833 19.1667 14.1667V5.83333C19.1667 4.91667 18.4167 4.16667 17.5 4.16667ZM17.5 14.1667H2.5V7.5L10 11.6667L17.5 7.5V14.1667ZM10 10L2.5 5.83333H17.5L10 10Z"/>
        </svg>
        <span>{{ __('Support Tickets') }}</span>
    </a>
</li>

<!-- الحضور والغياب -->
<!-- <li>
    <a href="{{ route('teacher.attendance.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.attendance.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.16667 14.1667H10.8333V9.16667H9.16667V14.1667ZM10 1.66667C5.4 1.66667 1.66667 5.4 1.66667 10C1.66667 14.6 5.4 18.3333 10 18.3333C14.6 18.3333 18.3333 14.6 18.3333 10C18.3333 5.4 14.6 1.66667 10 1.66667ZM10 16.6667C6.325 16.6667 3.33333 13.675 3.33333 10C3.33333 6.325 6.325 3.33333 10 3.33333C13.675 3.33333 16.6667 6.325 16.6667 10C16.6667 13.675 13.675 16.6667 10 16.6667ZM9.16667 7.5H10.8333V5.83333H9.16667V7.5Z"/>
        </svg>
        <span>{{ __('Attendance') }}</span>
    </a>
</li> -->

{{-- ═══ فاصل ═══ --}}

<!-- التقييمات -->
<!-- <li>
    <a href="{{ route('teacher.grades.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.grades.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.8333 2.5H4.16667C3.24167 2.5 2.5 3.24167 2.5 4.16667V15.8333C2.5 16.7583 3.24167 17.5 4.16667 17.5H15.8333C16.7583 17.5 17.5 16.7583 17.5 15.8333V4.16667C17.5 3.24167 16.7583 2.5 15.8333 2.5ZM15.8333 15.8333H4.16667V4.16667H15.8333V15.8333ZM13.3333 6.66667H6.66667V8.33333H13.3333V6.66667ZM10 10H6.66667V11.6667H10V10ZM10 13.3333H6.66667V15H10V13.3333Z"/>
        </svg>
        <span>{{ __('Grades') }}</span>
    </a>
</li>

<li>
    <a href="{{ route('teacher.surveys.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.surveys.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 1.66667H2.5C1.58333 1.66667 0.833333 2.41667 0.833333 3.33333V13.3333C0.833333 14.25 1.58333 15 2.5 15H5.83333V18.3333L10.8333 15H17.5C18.4167 15 19.1667 14.25 19.1667 13.3333V3.33333C19.1667 2.41667 18.4167 1.66667 17.5 1.66667ZM17.5 13.3333H10.1667L7.5 15V13.3333H2.5V3.33333H17.5V13.3333ZM9.16667 10.8333H10.8333V12.5H9.16667V10.8333ZM9.16667 4.16667H10.8333V9.16667H9.16667V4.16667Z"/>
        </svg>
        <span>{{ __('Surveys') }}</span>
    </a>
</li> -->

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

{{-- ═══ الحساب ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">{{ __('Account') }}</span>
</li>

<!-- الملف الشخصي -->
<li>
    <a href="{{ route('teacher.profile') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.profile') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 1.66667C5.4 1.66667 1.66667 5.4 1.66667 10C1.66667 14.6 5.4 18.3333 10 18.3333C14.6 18.3333 18.3333 14.6 18.3333 10C18.3333 5.4 14.6 1.66667 10 1.66667ZM10 5C11.8417 5 13.3333 6.49167 13.3333 8.33333C13.3333 10.175 11.8417 11.6667 10 11.6667C8.15833 11.6667 6.66667 10.175 6.66667 8.33333C6.66667 6.49167 8.15833 5 10 5ZM10 16.6667C7.79167 16.6667 5.81667 15.6417 4.51667 14.05C5.85 12.9083 7.34167 12.5 10 12.5C12.6583 12.5 14.15 12.9083 15.4833 14.05C14.1833 15.6417 12.2083 16.6667 10 16.6667Z"/>
        </svg>
        <span>{{ __('Profile') }}</span>
    </a>
</li>
