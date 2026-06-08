<aside
    :class="sidebarToggle ? 'translate-x-0 shadow-2xl' : 'translate-x-full'"
    class="sidebar fixed right-0 top-0 z-9999 flex h-screen w-[280px] flex-col overflow-y-auto px-6 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
    style="background: linear-gradient(180deg, #0071AA 0%, #005a88 100%);"
>
    <!-- SIDEBAR HEADER -->
    <div class="flex items-center justify-between pt-8 pb-7">
        @php
            $userRole = auth()->user()->role;
            $dashboardRoute = in_array($userRole, ['admin', 'super_admin'])
                ? 'admin.dashboard'
                : $userRole . '.dashboard';
        @endphp
        <a href="{{ route($dashboardRoute) }}">
            <img src="{{ asset('images/logo/logo.png') }}" alt="Logo" class="h-16" style="filter: brightness(0) invert(1);" />
        </a>
        <!-- Close button — mobile only -->
        <button @click="sidebarToggle = false"
                class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg"
                style="background:rgba(255,255,255,.15);color:#fff;border:none;cursor:pointer;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
            </svg>
        </button>
    </div>

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <!-- Sidebar Menu -->
        <nav>
            <ul class="flex flex-col gap-2">
                @if(in_array(auth()->user()->role, ['admin', 'super_admin']))
                    @include('layouts.partials.sidebar-admin')
                @elseif(auth()->user()->role === 'teacher')
                    @include('layouts.partials.sidebar-teacher')
                @elseif(auth()->user()->role === 'student')
                    @include('layouts.partials.sidebar-student')
                @endif
            </ul>
        </nav>

    </div>
</aside>
