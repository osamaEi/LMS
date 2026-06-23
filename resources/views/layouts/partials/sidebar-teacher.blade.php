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

{{-- ═══ التدريس ═══ --}}
<li style="padding:8px 16px 4px">
    <span style="font-size:0.68rem;font-weight:700;letter-spacing:0.1em;color:rgba(255,255,255,0.35);display:block;text-transform:uppercase">{{ __('Teaching') }}</span>
</li>

<!-- مقرراتي -->
<li>
    <a href="{{ route('teacher.my-subjects.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.my-subjects.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill:none;stroke:currentColor;stroke-width:2;" width="20" height="20" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 7v-6m0 0l-9-5m9 5l9-5"/>
        </svg>
        <span>مقرراتي</span>
    </a>
</li>

<!-- الدورات -->
<li>
    <a href="{{ route('teacher.my-courses.index') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.my-courses.*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill:none;stroke:currentColor;stroke-width:2;" width="20" height="20" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        <span>الدورات</span>
    </a>
</li>


{{-- ═══ فاصل ═══ --}}
<li style="margin:6px 16px;height:1px;background:rgba(255,255,255,0.1)"></li>

<!-- الاختبارات والحلول -->
<li>
    <a href="{{ route('teacher.quizzes.overview') }}"
       class="menu-item group relative flex items-center gap-3 rounded-lg px-4 py-3 font-medium {{ request()->routeIs('teacher.quizzes.overview*') ? 'menu-item-active' : 'menu-item-inactive' }}">
        <svg class="fill-current" style="fill:none;stroke:currentColor;stroke-width:2;" width="20" height="20" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>الاختبارات والحلول</span>
    </a>
</li>

{{-- ═══ فاصل ═══ --}}
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
