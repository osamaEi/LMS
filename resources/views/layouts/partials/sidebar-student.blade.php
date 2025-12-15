<!-- لوحة التحكم -->
<li>
    <a href="{{ route('student.dashboard') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.99999 1.66666C5.39999 1.66666 1.66666 5.39999 1.66666 9.99999C1.66666 14.6 5.39999 18.3333 9.99999 18.3333C14.6 18.3333 18.3333 14.6 18.3333 9.99999C18.3333 5.39999 14.6 1.66666 9.99999 1.66666ZM9.99999 16.6667C6.31666 16.6667 3.33332 13.6833 3.33332 9.99999C3.33332 6.31666 6.31666 3.33332 9.99999 3.33332C13.6833 3.33332 16.6667 6.31666 16.6667 9.99999C16.6667 13.6833 13.6833 16.6667 9.99999 16.6667Z" fill=""/>
        </svg>
        <span>لوحة التحكم</span>
    </a>
</li>



<!-- الجدول الدراسي -->
<li>
    <a href="{{ route('student.schedule') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.schedule') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.8333 2.5H14.1667V1.66667C14.1667 1.20834 13.7917 0.833336 13.3333 0.833336C12.875 0.833336 12.5 1.20834 12.5 1.66667V2.5H7.5V1.66667C7.5 1.20834 7.125 0.833336 6.66667 0.833336C6.20833 0.833336 5.83333 1.20834 5.83333 1.66667V2.5H4.16667C2.78333 2.5 1.66667 3.61667 1.66667 5V16.6667C1.66667 18.05 2.78333 19.1667 4.16667 19.1667H15.8333C17.2167 19.1667 18.3333 18.05 18.3333 16.6667V5C18.3333 3.61667 17.2167 2.5 15.8333 2.5ZM16.6667 16.6667C16.6667 17.1333 16.3 17.5 15.8333 17.5H4.16667C3.7 17.5 3.33333 17.1333 3.33333 16.6667V7.5H16.6667V16.6667Z" fill=""/>
        </svg>
        <span>الجدول الدراسي</span>
    </a>
</li>

<!-- الواجبات -->
<li>
    <a href="{{ route('student.assignments.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.assignments.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 3.75H2.5C1.57953 3.75 0.833333 4.49619 0.833333 5.41667V14.5833C0.833333 15.5038 1.57953 16.25 2.5 16.25H17.5C18.4205 16.25 19.1667 15.5038 19.1667 14.5833V5.41667C19.1667 4.49619 18.4205 3.75 17.5 3.75Z" fill=""/>
        </svg>
        <span>الواجبات</span>
    </a>
</li>

<!-- الدرجات -->
<li>
    <a href="{{ route('student.grades.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.grades.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2.5 16.25H17.5C17.9583 16.25 18.3333 16.625 18.3333 17.0833C18.3333 17.5417 17.9583 17.9167 17.5 17.9167H2.5C2.04167 17.9167 1.66667 17.5417 1.66667 17.0833C1.66667 16.625 2.04167 16.25 2.5 16.25Z" fill=""/>
        </svg>
        <span>الدرجات</span>
    </a>
</li>

<!-- الحضور -->
<li>
    <a href="{{ route('student.attendance.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.attendance.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 0.833336C4.93333 0.833336 0.833333 4.93334 0.833333 10C0.833333 15.0667 4.93333 19.1667 10 19.1667C15.0667 19.1667 19.1667 15.0667 19.1667 10C19.1667 4.93334 15.0667 0.833336 10 0.833336ZM13.6167 12.85L9.16667 10.1667V5H10.8333V9.28334L14.5667 11.5167L13.6167 12.85Z" fill=""/>
        </svg>
        <span>الحضور</span>
    </a>
</li>

<!-- الملف الشخصي -->
<li>
    <a href="{{ route('student.profile') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.profile') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10.0002 0.625C4.8405 0.625 0.625195 4.84031 0.625195 10.0109C0.625195 15.1706 4.8405 19.3859 10.0002 19.3859C15.1599 19.3859 19.3752 15.1706 19.3752 10.0109C19.3752 4.84031 15.1599 0.625 10.0002 0.625ZM10.0002 4.375C11.6455 4.375 12.9689 5.69844 12.9689 7.34375C12.9689 8.98906 11.6455 10.3125 10.0002 10.3125C8.35488 10.3125 7.03145 8.98906 7.03145 7.34375C7.03145 5.69844 8.35488 4.375 10.0002 4.375ZM10.0002 17.1875C8.11426 17.1875 6.39551 16.4219 5.11738 15.1875C5.43488 13.8641 8.0405 13.125 10.0002 13.125C11.9599 13.125 14.5655 13.8641 14.883 15.1875C13.6049 16.4219 11.8861 17.1875 10.0002 17.1875Z" fill=""/>
        </svg>
        <span>الملف الشخصي</span>
    </a>
</li>
