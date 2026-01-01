<!-- لوحة التحكم -->
<li>
    <a href="{{ route('teacher.dashboard') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.99999 1.66666C5.39999 1.66666 1.66666 5.39999 1.66666 9.99999C1.66666 14.6 5.39999 18.3333 9.99999 18.3333C14.6 18.3333 18.3333 14.6 18.3333 9.99999C18.3333 5.39999 14.6 1.66666 9.99999 1.66666ZM9.99999 16.6667C6.31666 16.6667 3.33332 13.6833 3.33332 9.99999C3.33332 6.31666 6.31666 3.33332 9.99999 3.33332C13.6833 3.33332 16.6667 6.31666 16.6667 9.99999C16.6667 13.6833 13.6833 16.6667 9.99999 16.6667Z" fill=""/>
        </svg>
        <span>لوحة التحكم</span>
    </a>
</li>

<!-- موادي -->
<li>
    <a href="{{ route('teacher.my-subjects.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.my-subjects.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 3.33334H2.5C1.58333 3.33334 0.833333 4.08334 0.833333 5V15C0.833333 15.9167 1.58333 16.6667 2.5 16.6667H17.5C18.4167 16.6667 19.1667 15.9167 19.1667 15V5C19.1667 4.08334 18.4167 3.33334 17.5 3.33334ZM17.5 15H2.5V5H17.5V15Z" fill=""/>
        </svg>
        <span>موادي</span>
    </a>
</li>

<!-- الجدول الدراسي -->
<li>
    <a href="{{ route('teacher.schedule') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.schedule') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.8333 2.5H14.1667V1.66667C14.1667 1.20834 13.7917 0.833336 13.3333 0.833336C12.875 0.833336 12.5 1.20834 12.5 1.66667V2.5H7.5V1.66667C7.5 1.20834 7.125 0.833336 6.66667 0.833336C6.20833 0.833336 5.83333 1.20834 5.83333 1.66667V2.5H4.16667C2.78333 2.5 1.66667 3.61667 1.66667 5V16.6667C1.66667 18.05 2.78333 19.1667 4.16667 19.1667H15.8333C17.2167 19.1667 18.3333 18.05 18.3333 16.6667V5C18.3333 3.61667 17.2167 2.5 15.8333 2.5ZM16.6667 16.6667C16.6667 17.1333 16.3 17.5 15.8333 17.5H4.16667C3.7 17.5 3.33333 17.1333 3.33333 16.6667V7.5H16.6667V16.6667Z" fill=""/>
        </svg>
        <span>الجدول الدراسي</span>
    </a>
</li>

<!-- الطلاب -->
<li>
    <a href="{{ route('teacher.students.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.students.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13.3333 10.8333C14.7167 10.8333 15.8333 9.71667 15.8333 8.33334C15.8333 6.95 14.7167 5.83334 13.3333 5.83334C11.95 5.83334 10.8333 6.95 10.8333 8.33334C10.8333 9.71667 11.95 10.8333 13.3333 10.8333ZM6.66667 10.8333C8.05 10.8333 9.16667 9.71667 9.16667 8.33334C9.16667 6.95 8.05 5.83334 6.66667 5.83334C5.28333 5.83334 4.16667 6.95 4.16667 8.33334C4.16667 9.71667 5.28333 10.8333 6.66667 10.8333ZM6.66667 12.5C4.72222 12.5 0.833333 13.4778 0.833333 15.4167V17.5H12.5V15.4167C12.5 13.4778 8.61111 12.5 6.66667 12.5ZM13.3333 12.5C13.0889 12.5 12.8111 12.5111 12.5167 12.5333C13.5278 13.2333 14.1667 14.1833 14.1667 15.4167V17.5H19.1667V15.4167C19.1667 13.4778 15.2778 12.5 13.3333 12.5Z" fill=""/>
        </svg>
        <span>الطلاب</span>
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
