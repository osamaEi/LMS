<aside
    :class="sidebarToggle ? 'translate-x-0' : 'translate-x-full'"
    class="sidebar fixed right-0 top-0 z-9999 flex h-screen w-[280px] flex-col overflow-y-hidden px-6 lg:static lg:translate-x-0"
    style="background: linear-gradient(180deg, #0071AA 0%, #005a88 100%);"
>
    <!-- SIDEBAR HEADER -->
    <div class="flex items-center justify-between pt-8 pb-7">
        <a href="{{ route(auth()->user()->role . '.dashboard') }}">
            <img src="{{ asset('images/logo/logo.png') }}" alt="Logo" class="h-12" style="filter: brightness(0) invert(1);" />
        </a>
    </div>

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <!-- Sidebar Menu -->
        <nav>
            <ul class="flex flex-col gap-2">
                @if(auth()->user()->role === 'admin')
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
