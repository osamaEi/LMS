<aside
    :class="sidebarToggle ? 'translate-x-0' : 'translate-x-full'"
    class="sidebar fixed right-0 top-0 z-9999 flex h-screen w-[280px] flex-col overflow-y-hidden bg-white px-6 dark:bg-gray-800 lg:static lg:translate-x-0"
>
    <!-- SIDEBAR HEADER -->
    <div class="flex items-center justify-between pt-8 pb-7">
        <a href="{{ route(auth()->user()->role . '.dashboard') }}">
            <img src="{{ asset('images/logo/logo.png') }}" alt="Logo" class="h-12" />
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

        <!-- تسجيل الخروج -->
        <div class="mt-auto pt-8 pb-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-3 rounded-lg bg-error-50 px-4 py-3 font-medium text-error-500 hover:bg-error-100">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.3333 14.1667L17.5 10L13.3333 5.83334V8.33334H7.5V11.6667H13.3333V14.1667ZM15.8333 0.833336H4.16667C2.78333 0.833336 1.66667 1.95 1.66667 3.33334V7.5H4.16667V3.33334H15.8333V16.6667H4.16667V12.5H1.66667V16.6667C1.66667 18.05 2.78333 19.1667 4.16667 19.1667H15.8333C17.2167 19.1667 18.3333 18.05 18.3333 16.6667V3.33334C18.3333 1.95 17.2167 0.833336 15.8333 0.833336Z" fill=""/>
                    </svg>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
    </div>
</aside>
