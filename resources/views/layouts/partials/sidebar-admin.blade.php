<!-- لوحة التحكم -->
<li>
    <a href="{{ route('admin.dashboard') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.99999 1.66666C5.39999 1.66666 1.66666 5.39999 1.66666 9.99999C1.66666 14.6 5.39999 18.3333 9.99999 18.3333C14.6 18.3333 18.3333 14.6 18.3333 9.99999C18.3333 5.39999 14.6 1.66666 9.99999 1.66666ZM9.99999 16.6667C6.31666 16.6667 3.33332 13.6833 3.33332 9.99999C3.33332 6.31666 6.31666 3.33332 9.99999 3.33332C13.6833 3.33332 16.6667 6.31666 16.6667 9.99999C16.6667 13.6833 13.6833 16.6667 9.99999 16.6667Z" fill=""/>
        </svg>
        <span>لوحة التحكم</span>
    </a>
</li>

<!-- إدارة المعلمين -->
<li>
    <a href="{{ route('admin.teachers.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.teachers.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13.3333 10.8333C14.7167 10.8333 15.8333 9.71667 15.8333 8.33334C15.8333 6.95 14.7167 5.83334 13.3333 5.83334C11.95 5.83334 10.8333 6.95 10.8333 8.33334C10.8333 9.71667 11.95 10.8333 13.3333 10.8333ZM6.66667 10.8333C8.05 10.8333 9.16667 9.71667 9.16667 8.33334C9.16667 6.95 8.05 5.83334 6.66667 5.83334C5.28333 5.83334 4.16667 6.95 4.16667 8.33334C4.16667 9.71667 5.28333 10.8333 6.66667 10.8333ZM6.66667 12.5C4.72222 12.5 0.833333 13.4778 0.833333 15.4167V17.5H12.5V15.4167C12.5 13.4778 8.61111 12.5 6.66667 12.5ZM13.3333 12.5C13.0889 12.5 12.8111 12.5111 12.5167 12.5333C13.5278 13.2333 14.1667 14.1833 14.1667 15.4167V17.5H19.1667V15.4167C19.1667 13.4778 15.2778 12.5 13.3333 12.5Z" fill=""/>
        </svg>
        <span>إدارة المعلمين</span>
    </a>
</li>

<!-- إدارة الطلاب -->
<li>
    <a href="{{ route('admin.students.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.students.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13.3333 10.8333C14.7167 10.8333 15.8333 9.71667 15.8333 8.33334C15.8333 6.95 14.7167 5.83334 13.3333 5.83334C11.95 5.83334 10.8333 6.95 10.8333 8.33334C10.8333 9.71667 11.95 10.8333 13.3333 10.8333ZM6.66667 10.8333C8.05 10.8333 9.16667 9.71667 9.16667 8.33334C9.16667 6.95 8.05 5.83334 6.66667 5.83334C5.28333 5.83334 4.16667 6.95 4.16667 8.33334C4.16667 9.71667 5.28333 10.8333 6.66667 10.8333ZM6.66667 12.5C4.72222 12.5 0.833333 13.4778 0.833333 15.4167V17.5H12.5V15.4167C12.5 13.4778 8.61111 12.5 6.66667 12.5ZM13.3333 12.5C13.0889 12.5 12.8111 12.5111 12.5167 12.5333C13.5278 13.2333 14.1667 14.1833 14.1667 15.4167V17.5H19.1667V15.4167C19.1667 13.4778 15.2778 12.5 13.3333 12.5Z" fill=""/>
        </svg>
        <span>إدارة الطلاب</span>
    </a>
</li>

<!-- إدارة الدورات -->
<li>
    <a href="{{ route('admin.courses.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.courses.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 3.33334H2.5C1.58333 3.33334 0.833333 4.08334 0.833333 5V15C0.833333 15.9167 1.58333 16.6667 2.5 16.6667H17.5C18.4167 16.6667 19.1667 15.9167 19.1667 15V5C19.1667 4.08334 18.4167 3.33334 17.5 3.33334ZM17.5 15H2.5V5H17.5V15Z" fill=""/>
        </svg>
        <span>إدارة الدورات</span>
    </a>
</li>

<!-- إدارة المسارات التعليمية -->
<li>
    <a href="{{ route('admin.programs.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.programs.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 1.66666L1.66667 5.83333V8.33333C1.66667 12.4833 4.65 16.3583 8.75 17.4583C9.575 17.6833 10.425 17.6833 11.25 17.4583C15.35 16.3583 18.3333 12.4833 18.3333 8.33333V5.83333L10 1.66666ZM10 3.33333L16.6667 6.66666V8.33333C16.6667 11.6667 14.2667 14.7417 11.0833 15.6417C10.3833 15.8167 9.61667 15.8167 8.91667 15.6417C5.73333 14.7417 3.33333 11.6667 3.33333 8.33333V6.66666L10 3.33333ZM9.16667 10.8333L7.08333 8.74999L6.08333 9.74999L9.16667 12.8333L14.1667 7.83333L13.1667 6.83333L9.16667 10.8333Z" fill=""/>
        </svg>
        <span>المسارات التعليمية</span>
    </a>
</li>

<!-- إدارة الفصول الدراسية -->
<li>
    <a href="{{ route('admin.terms.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.terms.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16.6667 2.5H15V0.833333H13.3333V2.5H6.66667V0.833333H5V2.5H3.33333C2.41667 2.5 1.66667 3.25 1.66667 4.16667V17.5C1.66667 18.4167 2.41667 19.1667 3.33333 19.1667H16.6667C17.5833 19.1667 18.3333 18.4167 18.3333 17.5V4.16667C18.3333 3.25 17.5833 2.5 16.6667 2.5ZM16.6667 17.5H3.33333V7.5H16.6667V17.5ZM16.6667 5.83333H3.33333V4.16667H16.6667V5.83333Z" fill=""/>
        </svg>
        <span>الفصول الدراسية</span>
    </a>
</li>

<!-- إدارة المواد -->
<li>
    <a href="{{ route('admin.subjects.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.subjects.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.8333 3.33333H4.16667C3.24167 3.33333 2.5 4.08333 2.5 5V15C2.5 15.9167 3.24167 16.6667 4.16667 16.6667H15.8333C16.7583 16.6667 17.5 15.9167 17.5 15V5C17.5 4.08333 16.7583 3.33333 15.8333 3.33333ZM15.8333 15H4.16667V5H15.8333V15ZM6.66667 10H13.3333V11.6667H6.66667V10ZM6.66667 7.5H13.3333V9.16667H6.66667V7.5ZM6.66667 12.5H10.8333V14.1667H6.66667V12.5Z" fill=""/>
        </svg>
        <span>المواد الدراسية</span>
    </a>
</li>

<!-- إدارة الدروس -->
<li>
    <a href="{{ route('admin.sessions.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.sessions.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 0.833333C4.94167 0.833333 0.833333 4.94167 0.833333 10C0.833333 15.0583 4.94167 19.1667 10 19.1667C15.0583 19.1667 19.1667 15.0583 19.1667 10C19.1667 4.94167 15.0583 0.833333 10 0.833333ZM14.5833 10.8333H9.16667V5.41667H10.8333V9.16667H14.5833V10.8333Z" fill=""/>
        </svg>
        <span>الدروس والمحاضرات</span>
    </a>
</li>

<!-- استبيانات الرضا (NELC 1.2.11) -->
<li>
    <a href="{{ route('admin.surveys.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.surveys.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15.8333 2.5H4.16667C3.25 2.5 2.5 3.25 2.5 4.16667V15.8333C2.5 16.75 3.25 17.5 4.16667 17.5H15.8333C16.75 17.5 17.5 16.75 17.5 15.8333V4.16667C17.5 3.25 16.75 2.5 15.8333 2.5ZM8.33333 13.3333L5 10L6.175 8.825L8.33333 10.975L13.825 5.48333L15 6.66667L8.33333 13.3333Z" fill=""/>
        </svg>
        <span>استبيانات الرضا</span>
    </a>
</li>

<!-- تذاكر الدعم (NELC 1.3.3) -->
<li>
    <a href="{{ route('admin.tickets.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.tickets.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 5H16.6667V3.33333C16.6667 2.41667 15.9167 1.66667 15 1.66667H5C4.08333 1.66667 3.33333 2.41667 3.33333 3.33333V5H2.5C1.58333 5 0.833333 5.75 0.833333 6.66667V15C0.833333 15.9167 1.58333 16.6667 2.5 16.6667H4.16667V18.3333H15.8333V16.6667H17.5C18.4167 16.6667 19.1667 15.9167 19.1667 15V6.66667C19.1667 5.75 18.4167 5 17.5 5ZM5 3.33333H15V5H5V3.33333ZM14.1667 15H5.83333V11.6667H14.1667V15ZM17.5 13.3333H15.8333V10H4.16667V13.3333H2.5V6.66667H17.5V13.3333Z" fill=""/>
        </svg>
        <span>الدعم الفني</span>
    </a>
</li>

<!-- تقييم المدربين (NELC 2.4.9) -->
<li>
    <a href="{{ route('admin.teacher-ratings.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.teacher-ratings.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 1.66667L12.575 6.88334L18.3333 7.725L14.1667 11.7833L15.15 17.5167L10 14.8083L4.85 17.5167L5.83333 11.7833L1.66667 7.725L7.425 6.88334L10 1.66667Z" fill=""/>
        </svg>
        <span>تقييم المدربين</span>
    </a>
</li>

<!-- التقارير والإحصائيات -->
<li>
    <a href="{{ route('admin.reports') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.reports') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2.5 16.25H17.5C17.9583 16.25 18.3333 16.625 18.3333 17.0833C18.3333 17.5417 17.9583 17.9167 17.5 17.9167H2.5C2.04167 17.9167 1.66667 17.5417 1.66667 17.0833C1.66667 16.625 2.04167 16.25 2.5 16.25Z" fill=""/>
            <path d="M4.16667 13.75C3.70833 13.75 3.33333 13.375 3.33333 12.9167V7.08333C3.33333 6.625 3.70833 6.25 4.16667 6.25C4.625 6.25 5 6.625 5 7.08333V12.9167C5 13.375 4.625 13.75 4.16667 13.75Z" fill=""/>
            <path d="M8.33333 13.75C7.875 13.75 7.5 13.375 7.5 12.9167V2.91667C7.5 2.45833 7.875 2.08333 8.33333 2.08333C8.79167 2.08333 9.16667 2.45833 9.16667 2.91667V12.9167C9.16667 13.375 8.79167 13.75 8.33333 13.75Z" fill=""/>
            <path d="M12.5 13.75C12.0417 13.75 11.6667 13.375 11.6667 12.9167V9.58333C11.6667 9.125 12.0417 8.75 12.5 8.75C12.9583 8.75 13.3333 9.125 13.3333 9.58333V12.9167C13.3333 13.375 12.9583 13.75 12.5 13.75Z" fill=""/>
            <path d="M16.6667 13.75C16.2083 13.75 15.8333 13.375 15.8333 12.9167V4.58333C15.8333 4.125 16.2083 3.75 16.6667 3.75C17.125 3.75 17.5 4.125 17.5 4.58333V12.9167C17.5 13.375 17.125 13.75 16.6667 13.75Z" fill=""/>
        </svg>
        <span>التقارير والإحصائيات</span>
    </a>
</li>

<!-- الإعدادات -->
<li>
    <a href="{{ route('admin.settings') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('admin.settings') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.4292 10.9417C17.4625 10.6333 17.4792 10.325 17.4792 10C17.4792 9.68334 17.4625 9.36667 17.4208 9.05834L19.5458 7.40001C19.7292 7.25834 19.7792 6.99167 19.6625 6.78334L17.6625 3.55001C17.5375 3.32501 17.2875 3.25001 17.0625 3.32501L14.5542 4.31667C14.0375 3.92501 13.4875 3.59167 12.8792 3.34167L12.4958 0.675006C12.4542 0.433339 12.2458 0.250006 11.9958 0.250006H7.99583C7.74583 0.250006 7.54583 0.433339 7.50416 0.675006L7.12083 3.34167C6.5125 3.59167 5.95416 3.93334 5.4459 4.31667L2.9375 3.32501C2.7125 3.24167 2.4625 3.32501 2.3375 3.55001L0.345828 6.78334C0.220828 6.99167 0.270829 7.25834 0.462496 7.40001L2.58749 9.05834C2.54583 9.36667 2.52083 9.69167 2.52083 10C2.52083 10.3083 2.53749 10.6333 2.57916 10.9417L0.45416 12.6C0.270826 12.7417 0.220829 13.0083 0.337496 13.2167L2.3375 16.45C2.4625 16.675 2.7125 16.75 2.9375 16.675L5.44583 15.6833C5.9625 16.075 6.5125 16.4083 7.12083 16.6583L7.50416 19.325C7.54583 19.5667 7.74583 19.75 7.99583 19.75H11.9958C12.2458 19.75 12.4542 19.5667 12.4875 19.325L12.8708 16.6583C13.4792 16.4083 14.0375 16.075 14.5458 15.6833L17.0542 16.675C17.2792 16.7583 17.5292 16.675 17.6542 16.45L19.6542 13.2167C19.7792 12.9917 19.7292 12.7417 19.5375 12.6L17.4292 10.9417ZM9.99583 13.75C8.07916 13.75 6.52083 12.1917 6.52083 10.275C6.52083 8.35834 8.07916 6.80001 9.99583 6.80001C11.9125 6.80001 13.4708 8.35834 13.4708 10.275C13.4708 12.1917 11.9125 13.75 9.99583 13.75Z" fill=""/>
        </svg>
        <span>الإعدادات</span>
    </a>
</li>
