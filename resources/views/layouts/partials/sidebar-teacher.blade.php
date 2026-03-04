{{-- ═══ الرئيسية ═══ --}}
<li style="padding:16px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">الرئيسية</span>
</li>

<!-- لوحة التحكم -->
<li>
    <a href="{{ route('teacher.dashboard') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.99999 1.66666C5.39999 1.66666 1.66666 5.39999 1.66666 9.99999C1.66666 14.6 5.39999 18.3333 9.99999 18.3333C14.6 18.3333 18.3333 14.6 18.3333 9.99999C18.3333 5.39999 14.6 1.66666 9.99999 1.66666ZM9.99999 16.6667C6.31666 16.6667 3.33332 13.6833 3.33332 9.99999C3.33332 6.31666 6.31666 3.33332 9.99999 3.33332C13.6833 3.33332 16.6667 6.31666 16.6667 9.99999C16.6667 13.6833 13.6833 16.6667 9.99999 16.6667Z"/>
        </svg>
        <span>لوحة التحكم</span>
    </a>
</li>

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

{{-- ═══ التدريس ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">التدريس</span>
</li>

<!-- موادي -->
<li>
    <a href="{{ route('teacher.my-subjects.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.my-subjects.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 3.33334H2.5C1.58333 3.33334 0.833333 4.08334 0.833333 5V15C0.833333 15.9167 1.58333 16.6667 2.5 16.6667H17.5C18.4167 16.6667 19.1667 15.9167 19.1667 15V5C19.1667 4.08334 18.4167 3.33334 17.5 3.33334ZM17.5 15H2.5V5H17.5V15Z"/>
        </svg>
        <span>موادي</span>
    </a>
</li>

<!-- الجدول الدراسي -->
<li>
    <a href="{{ route('teacher.schedule') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.schedule') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.8333 2.5H15V0.833336H13.3333V2.5H6.66667V0.833336H5V2.5H4.16667C3.24167 2.5 2.5 3.25 2.5 4.16667V15.8333C2.5 16.75 3.24167 17.5 4.16667 17.5H15.8333C16.75 17.5 17.5 16.75 17.5 15.8333V4.16667C17.5 3.25 16.75 2.5 15.8333 2.5ZM15.8333 15.8333H4.16667V7.5H15.8333V15.8333ZM15.8333 5.83334H4.16667V4.16667H15.8333V5.83334ZM6.66667 10H9.16667V12.5H6.66667V10Z"/>
        </svg>
        <span>الجدول الدراسي</span>
    </a>
</li>

<!-- الاختبارات - Dropdown -->
<li x-data="{ open: {{ request()->routeIs('teacher.quizzes.*') ? 'true' : 'false' }} }">
    <button @click="open = !open"
            class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium w-full {{ request()->routeIs('teacher.quizzes.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.8333 2.5H4.16667C3.24167 2.5 2.5 3.24167 2.5 4.16667V15.8333C2.5 16.7583 3.24167 17.5 4.16667 17.5H15.8333C16.7583 17.5 17.5 16.7583 17.5 15.8333V4.16667C17.5 3.24167 16.7583 2.5 15.8333 2.5ZM15.8333 15.8333H4.16667V4.16667H15.8333V15.8333ZM13.3333 6.66667H6.66667V8.33333H13.3333V6.66667ZM10 10H6.66667V11.6667H10V10ZM10 13.3333H6.66667V15H10V13.3333Z"/>
        </svg>
        <span>الاختبارات</span>
        <svg class="fill-current ms-auto transition-transform duration-200" :class="{ 'rotate-180': open }" width="16" height="16" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
        </svg>
    </button>
    <!-- Dropdown Menu -->
    <ul x-show="open" x-collapse class="mt-2 mr-4 space-y-1 border-r-2 border-white/20 pr-3">
        @php
            $teacherSubjects = \App\Models\Subject::where('teacher_id', auth()->id())->get();
        @endphp
        @forelse($teacherSubjects as $subject)
        <li>
            <a href="{{ route('teacher.quizzes.index', $subject->id) }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ request()->is('teacher/subjects/'.$subject->id.'/quizzes*') ? 'bg-white/20 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                <span class="w-2 h-2 rounded-full {{ request()->is('teacher/subjects/'.$subject->id.'/quizzes*') ? 'bg-white' : 'bg-white/50' }}"></span>
                <span class="truncate">{{ $subject->name }}</span>
            </a>
        </li>
        @empty
        <li class="px-3 py-2 text-sm text-white/50">
            لا توجد مواد مسندة إليك
        </li>
        @endforelse
    </ul>
</li>

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

<!-- الملفات والموارد -->
<li>
    <a href="{{ route('teacher.files.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.files.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 2l4.5 4.5H13V4zM8 17h8v-1.5H8V17zm0-3h8v-1.5H8V14zm0-3h5v-1.5H8V11z"/>
        </svg>
        <span>الملفات والموارد</span>
    </a>
</li>

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

{{-- ═══ إدارة الطلاب ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">إدارة الطلاب</span>
</li>

<!-- الطلاب -->
<li>
    <a href="{{ route('teacher.students.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.students.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M13.3333 10.8333C14.7167 10.8333 15.8333 9.71667 15.8333 8.33334C15.8333 6.95 14.7167 5.83334 13.3333 5.83334C11.95 5.83334 10.8333 6.95 10.8333 8.33334C10.8333 9.71667 11.95 10.8333 13.3333 10.8333ZM6.66667 10.8333C8.05 10.8333 9.16667 9.71667 9.16667 8.33334C9.16667 6.95 8.05 5.83334 6.66667 5.83334C5.28333 5.83334 4.16667 6.95 4.16667 8.33334C4.16667 9.71667 5.28333 10.8333 6.66667 10.8333ZM6.66667 12.5C4.72222 12.5 0.833333 13.4778 0.833333 15.4167V17.5H12.5V15.4167C12.5 13.4778 8.61111 12.5 6.66667 12.5ZM13.3333 12.5C13.0889 12.5 12.8111 12.5111 12.5167 12.5333C13.5278 13.2333 14.1667 14.1833 14.1667 15.4167V17.5H19.1667V15.4167C19.1667 13.4778 15.2778 12.5 13.3333 12.5Z"/>
        </svg>
        <span>الطلاب</span>
    </a>
</li>

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

{{-- ═══ الدعم ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">الدعم</span>
</li>

<!-- تذاكر الدعم -->
<li>
    <a href="{{ route('teacher.tickets.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.tickets.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 4.16667H2.5C1.58333 4.16667 0.833333 4.91667 0.833333 5.83333V14.1667C0.833333 15.0833 1.58333 15.8333 2.5 15.8333H17.5C18.4167 15.8333 19.1667 15.0833 19.1667 14.1667V5.83333C19.1667 4.91667 18.4167 4.16667 17.5 4.16667ZM17.5 14.1667H2.5V7.5L10 11.6667L17.5 7.5V14.1667ZM10 10L2.5 5.83333H17.5L10 10Z"/>
        </svg>
        <span>تذاكر الدعم</span>
    </a>
</li>

<!-- الحضور والغياب -->
<li>
    <a href="{{ route('teacher.attendance.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.attendance.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.16667 14.1667H10.8333V9.16667H9.16667V14.1667ZM10 1.66667C5.4 1.66667 1.66667 5.4 1.66667 10C1.66667 14.6 5.4 18.3333 10 18.3333C14.6 18.3333 18.3333 14.6 18.3333 10C18.3333 5.4 14.6 1.66667 10 1.66667ZM10 16.6667C6.325 16.6667 3.33333 13.675 3.33333 10C3.33333 6.325 6.325 3.33333 10 3.33333C13.675 3.33333 16.6667 6.325 16.6667 10C16.6667 13.675 13.675 16.6667 10 16.6667ZM9.16667 7.5H10.8333V5.83333H9.16667V7.5Z"/>
        </svg>
        <span>الحضور والغياب</span>
    </a>
</li>

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

{{-- ═══ المتابعة ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">المتابعة</span>
</li>

<!-- التقييمات -->
<li>
    <a href="{{ route('teacher.grades.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.grades.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.8333 2.5H4.16667C3.24167 2.5 2.5 3.24167 2.5 4.16667V15.8333C2.5 16.7583 3.24167 17.5 4.16667 17.5H15.8333C16.7583 17.5 17.5 16.7583 17.5 15.8333V4.16667C17.5 3.24167 16.7583 2.5 15.8333 2.5ZM15.8333 15.8333H4.16667V4.16667H15.8333V15.8333ZM13.3333 6.66667H6.66667V8.33333H13.3333V6.66667ZM10 10H6.66667V11.6667H10V10ZM10 13.3333H6.66667V15H10V13.3333Z"/>
        </svg>
        <span>التقييمات</span>
    </a>
</li>

<!-- الاستبيانات -->
<li>
    <a href="{{ route('teacher.surveys.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.surveys.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 1.66667H2.5C1.58333 1.66667 0.833333 2.41667 0.833333 3.33333V13.3333C0.833333 14.25 1.58333 15 2.5 15H5.83333V18.3333L10.8333 15H17.5C18.4167 15 19.1667 14.25 19.1667 13.3333V3.33333C19.1667 2.41667 18.4167 1.66667 17.5 1.66667ZM17.5 13.3333H10.1667L7.5 15V13.3333H2.5V3.33333H17.5V13.3333ZM9.16667 10.8333H10.8333V12.5H9.16667V10.8333ZM9.16667 4.16667H10.8333V9.16667H9.16667V4.16667Z"/>
        </svg>
        <span>الاستبيانات</span>
    </a>
</li>

{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

{{-- ═══ الحساب ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">الحساب</span>
</li>

<!-- الملف الشخصي -->
<li>
    <a href="{{ route('teacher.profile') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.profile') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill: currentColor;" width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 1.66667C5.4 1.66667 1.66667 5.4 1.66667 10C1.66667 14.6 5.4 18.3333 10 18.3333C14.6 18.3333 18.3333 14.6 18.3333 10C18.3333 5.4 14.6 1.66667 10 1.66667ZM10 5C11.8417 5 13.3333 6.49167 13.3333 8.33333C13.3333 10.175 11.8417 11.6667 10 11.6667C8.15833 11.6667 6.66667 10.175 6.66667 8.33333C6.66667 6.49167 8.15833 5 10 5ZM10 16.6667C7.79167 16.6667 5.81667 15.6417 4.51667 14.05C5.85 12.9083 7.34167 12.5 10 12.5C12.6583 12.5 14.15 12.9083 15.4833 14.05C14.1833 15.6417 12.2083 16.6667 10 16.6667Z"/>
        </svg>
        <span>الملف الشخصي</span>
    </a>
</li>
