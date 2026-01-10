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

<!-- Disabled sections until routes are created -->
{{--
<li>
    <a href="{{ route('teacher.attendance.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium">
        <span>الحضور والغياب</span>
    </a>
</li>

<li>
    <a href="{{ route('teacher.assignments.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium">
        <span>الواجبات</span>
    </a>
</li>

<li>
    <a href="{{ route('teacher.grades.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium">
        <span>التقييمات</span>
    </a>
</li>

<li>
    <a href="{{ route('teacher.profile') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium">
        <span>الملف الشخصي</span>
    </a>
</li>
--}}
