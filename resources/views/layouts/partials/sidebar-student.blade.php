<!-- لوحة التحكم -->
<li>
    <a href="{{ route('student.dashboard') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M3.33333 3.33334H8.33333V10H3.33333V3.33334ZM11.6667 3.33334H16.6667V6.66667H11.6667V3.33334ZM11.6667 10H16.6667V16.6667H11.6667V10ZM3.33333 13.3333H8.33333V16.6667H3.33333V13.3333Z" fill=""/>
        </svg>
        <span>لوحة التحكم</span>
    </a>
</li>

<!-- دوراتي -->
<li>
    <a href="{{ route('student.schedule') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.subjects.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M16.6667 2.5H3.33333C2.41667 2.5 1.66667 3.25 1.66667 4.16667V15.8333C1.66667 16.75 2.41667 17.5 3.33333 17.5H16.6667C17.5833 17.5 18.3333 16.75 18.3333 15.8333V4.16667C18.3333 3.25 17.5833 2.5 16.6667 2.5ZM16.6667 15.8333H3.33333V4.16667H16.6667V15.8333ZM5 6.66667H10V11.6667H5V6.66667ZM11.6667 6.66667H15V8.33333H11.6667V6.66667ZM11.6667 9.16667H15V10.8333H11.6667V9.16667ZM5 13.3333H15V15H5V13.3333Z" fill=""/>
        </svg>
        <span>دوراتي</span>
    </a>
</li>

<!-- المدفوعات -->
<li>
    <a href="#"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.payments.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 4.16667H2.5C1.57953 4.16667 0.833334 4.91286 0.833334 5.83333V14.1667C0.833334 15.0871 1.57953 15.8333 2.5 15.8333H17.5C18.4205 15.8333 19.1667 15.0871 19.1667 14.1667V5.83333C19.1667 4.91286 18.4205 4.16667 17.5 4.16667ZM17.5 14.1667H2.5V10H17.5V14.1667ZM17.5 7.5H2.5V5.83333H17.5V7.5Z" fill=""/>
        </svg>
        <span>المدفوعات</span>
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

<!-- الإعلانات -->
<li>
    <a href="#"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.announcements.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.5 2.5H2.5C1.57953 2.5 0.833334 3.24619 0.833334 4.16667V15.8333C0.833334 16.7538 1.57953 17.5 2.5 17.5H17.5C18.4205 17.5 19.1667 16.7538 19.1667 15.8333V4.16667C19.1667 3.24619 18.4205 2.5 17.5 2.5ZM17.5 15.8333H2.5V6.66667L10 10.8333L17.5 6.66667V15.8333ZM10 9.16667L2.5 5V4.16667H17.5V5L10 9.16667Z" fill=""/>
        </svg>
        <span>الإعلانات</span>
    </a>
</li>

<!-- التقييم والشهادات -->
<li>
    <a href="{{ route('student.grades.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.grades.*') || request()->routeIs('student.certificates.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 1.66667L2.5 5.83333V9.16667C2.5 13.7917 5.7 18.1167 10 19.1667C14.3 18.1167 17.5 13.7917 17.5 9.16667V5.83333L10 1.66667ZM15.8333 9.16667C15.8333 12.9 13.3333 16.3333 10 17.4167C6.66667 16.3333 4.16667 12.9 4.16667 9.16667V6.75L10 3.58333L15.8333 6.75V9.16667ZM6.175 10L5 11.175L8.33333 14.5L15 7.83333L13.825 6.65833L8.33333 12.15L6.175 10Z" fill=""/>
        </svg>
        <span>التقييم والشهادات</span>
    </a>
</li>

<!-- الدعم والمساعدة -->
<li>
    <a href=""
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('tickets.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 0.833336C4.94167 0.833336 0.833336 4.94167 0.833336 10C0.833336 15.0583 4.94167 19.1667 10 19.1667C15.0583 19.1667 19.1667 15.0583 19.1667 10C19.1667 4.94167 15.0583 0.833336 10 0.833336ZM10.8333 15.8333H9.16667V14.1667H10.8333V15.8333ZM10.8333 12.5H9.16667C9.16667 9.375 12.5 9.58333 12.5 7.5C12.5 6.11667 11.3833 5 10 5C8.61667 5 7.5 6.11667 7.5 7.5H5.83333C5.83333 5.19167 7.69167 3.33333 10 3.33333C12.3083 3.33333 14.1667 5.19167 14.1667 7.5C14.1667 10.4167 10.8333 10.625 10.8333 12.5Z" fill=""/>
        </svg>
        <span>الدعم والمساعدة</span>
    </a>
</li>

<!-- الإعدادات -->
<li>
    <a href="{{ route('student.profile') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('student.profile') || request()->routeIs('student.settings.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.1417 10.9417C17.175 10.6333 17.2 10.325 17.2 10C17.2 9.675 17.175 9.36667 17.1417 9.05833L19.15 7.48333C19.3333 7.34167 19.3833 7.075 19.2667 6.86667L17.3667 3.63333C17.2417 3.40833 16.9833 3.325 16.7583 3.40833L14.4 4.36667C13.8917 3.98333 13.35 3.65833 12.7583 3.40833L12.4 0.891667C12.3667 0.65 12.1583 0.5 11.9167 0.5H8.08333C7.84167 0.5 7.63333 0.65 7.6 0.891667L7.24167 3.40833C6.65 3.65833 6.10833 3.99167 5.6 4.36667L3.24167 3.40833C3.01667 3.31667 2.75833 3.40833 2.63333 3.63333L0.733333 6.86667C0.608333 7.08333 0.666667 7.34167 0.85 7.48333L2.85833 9.05833C2.825 9.36667 2.8 9.68333 2.8 10C2.8 10.3167 2.825 10.6333 2.85833 10.9417L0.85 12.5167C0.666667 12.6583 0.616667 12.925 0.733333 13.1333L2.63333 16.3667C2.75833 16.5917 3.01667 16.675 3.24167 16.5917L5.6 15.6333C6.10833 16.0167 6.65 16.3417 7.24167 16.5917L7.6 19.1083C7.63333 19.35 7.84167 19.5 8.08333 19.5H11.9167C12.1583 19.5 12.3667 19.35 12.4 19.1083L12.7583 16.5917C13.35 16.3417 13.8917 16.0083 14.4 15.6333L16.7583 16.5917C16.9833 16.6833 17.2417 16.5917 17.3667 16.3667L19.2667 13.1333C19.3917 12.9083 19.3333 12.6583 19.15 12.5167L17.1417 10.9417ZM10 13.5C8.06667 13.5 6.5 11.9333 6.5 10C6.5 8.06667 8.06667 6.5 10 6.5C11.9333 6.5 13.5 8.06667 13.5 10C13.5 11.9333 11.9333 13.5 10 13.5Z" fill=""/>
        </svg>
        <span>الإعدادات</span>
    </a>
</li>
