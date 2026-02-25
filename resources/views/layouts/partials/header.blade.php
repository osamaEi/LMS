<header class="sticky top-0 z-999 flex w-full bg-white dark:bg-gray-900 shadow-sm">
    <div class="flex grow items-center justify-between px-4 py-4 md:px-6">
        <!-- Left Side -->
        <div class="flex items-center gap-4">
            <!-- Mobile Menu Toggle -->
            <button
                class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 text-gray-500 lg:hidden dark:border-gray-800 dark:text-gray-400"
                @click.stop="sidebarToggle = !sidebarToggle"
            >
                <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" fill=""/>
                </svg>
            </button>

            <!-- Welcome Text -->
            <div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span>👋</span>
                    مرحباً بك {{ auth()->user()->getRoleDisplayName() }} {{ auth()->user()->name }}
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'super_admin')
                        إدارة النظام والمستخدمين والدورات بكل سهولة
                    @elseif(auth()->user()->role === 'teacher')
                        إدارة دوراتك والطلاب بكل سهولة من لوحة التحكم
                    @else
                        تابع دوراتك وتقدمك التعليمي بكل سهولة
                    @endif
                </p>
            </div>
        </div>

        <!-- Right Side -->
        <div class="flex items-center gap-3">
            <!-- Notification Dropdown -->
            @include('layouts.partials.notification-dropdown')

            <!-- User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <!-- Trigger Button -->
                <button
                    @click="open = !open"
                    class="flex items-center gap-2.5 rounded-xl px-2.5 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                >
                    <div class="relative flex-shrink-0">
                        @if(auth()->user()->avatar)
                            <img src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}"
                                 alt="{{ auth()->user()->name }}"
                                 style="width:38px;height:38px;border-radius:50%;object-fit:cover;border:2px solid #0071AA" />
                        @else
                            <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center;border:2px solid rgba(0,113,170,0.3);font-size:0.95rem;font-weight:800;color:#fff;flex-shrink:0">
                                {{ mb_substr(auth()->user()->name, 0, 1) }}
                            </div>
                        @endif
                        <span style="position:absolute;bottom:-1px;left:-1px;width:11px;height:11px;background:#22c55e;border-radius:50%;border:2px solid #fff" class="dark:border-gray-900"></span>
                    </div>
                    <div class="hidden md:block text-right">
                        <p style="font-size:0.85rem;font-weight:700;color:#111827;line-height:1.2;margin:0;max-width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" class="dark:text-white">{{ auth()->user()->name }}</p>
                        <p style="font-size:0.72rem;color:#6b7280;margin:0" class="dark:text-gray-400">{{ auth()->user()->getRoleDisplayName() }}</p>
                    </div>
                    <svg class="fill-current text-gray-400 transition-transform duration-200 hidden md:block"
                         :class="{'rotate-180': open}"
                         width="14" height="14" viewBox="0 0 16 16" fill="none">
                        <path d="M8 10.6667L4 6.66667L4.93333 5.73333L8 8.8L11.0667 5.73333L12 6.66667L8 10.6667Z" fill=""/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    class="absolute left-0 mt-2 origin-top-left z-50"
                    style="display:none;width:272px"
                >
                    <div style="background:#fff;border-radius:18px;box-shadow:0 20px 60px rgba(0,0,0,0.15),0 0 0 1px rgba(0,0,0,0.06);overflow:hidden" class="dark:bg-gray-800 dark:shadow-gray-900/50">

                        {{-- User Info Header --}}
                        <div style="padding:16px 18px;background:linear-gradient(135deg,#0071AA,#005a88);position:relative;overflow:hidden">
                            <div style="position:absolute;top:-30%;right:-10%;width:60%;height:200%;background:radial-gradient(ellipse,rgba(255,255,255,0.12) 0%,transparent 70%);pointer-events:none"></div>
                            <div style="display:flex;align-items:center;gap:12px;position:relative">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/avatars/' . auth()->user()->avatar) }}"
                                         alt="{{ auth()->user()->name }}"
                                         style="width:46px;height:46px;border-radius:50%;object-fit:cover;border:2.5px solid rgba(255,255,255,0.5);flex-shrink:0" />
                                @else
                                    <div style="width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;border:2.5px solid rgba(255,255,255,0.4);font-size:1.1rem;font-weight:900;color:#fff;flex-shrink:0">
                                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                @endif
                                <div style="min-width:0">
                                    <p style="font-size:0.9rem;font-weight:800;color:#fff;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ auth()->user()->name }}</p>
                                    <p style="font-size:0.72rem;color:rgba(255,255,255,0.75);margin:2px 0 6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ auth()->user()->email }}</p>
                                    @php
                                        $roleBadge = match(auth()->user()->role) {
                                            'super_admin', 'admin' => ['label' => 'مسؤول النظام', 'bg' => 'rgba(239,68,68,0.25)', 'color' => '#fca5a5'],
                                            'teacher'             => ['label' => 'أستاذ',          'bg' => 'rgba(250,204,21,0.25)', 'color' => '#fde68a'],
                                            default               => ['label' => 'طالب',           'bg' => 'rgba(52,211,153,0.25)', 'color' => '#6ee7b7'],
                                        };
                                    @endphp
                                    <span style="display:inline-flex;align-items:center;padding:2px 10px;border-radius:999px;font-size:0.7rem;font-weight:700;background:{{ $roleBadge['bg'] }};color:{{ $roleBadge['color'] }}">
                                        {{ $roleBadge['label'] }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Navigation Links --}}
                        <div style="padding:8px">
                            @php
                                $role = auth()->user()->role;
                                $isAdmin   = in_array($role, ['admin', 'super_admin']);
                                $isTeacher = $role === 'teacher';
                                $isStudent = $role === 'student';

                                $navLinks = [];

                                // Dashboard
                                if ($isAdmin) {
                                    $navLinks[] = ['href' => route('admin.dashboard'), 'icon' => 'grid', 'label' => 'لوحة التحكم', 'color' => '#6366f1'];
                                } elseif ($isTeacher) {
                                    $navLinks[] = ['href' => route('teacher.dashboard'), 'icon' => 'grid', 'label' => 'لوحة التحكم', 'color' => '#6366f1'];
                                } else {
                                    $navLinks[] = ['href' => route('student.dashboard'), 'icon' => 'grid', 'label' => 'لوحة التحكم', 'color' => '#6366f1'];
                                }

                                // Profile
                                if ($isTeacher) {
                                    $navLinks[] = ['href' => route('teacher.profile'), 'icon' => 'user', 'label' => 'الملف الشخصي', 'color' => '#0071AA'];
                                } elseif ($isStudent) {
                                    $navLinks[] = ['href' => route('student.profile'), 'icon' => 'user', 'label' => 'الملف الشخصي', 'color' => '#0071AA'];
                                } else {
                                    $navLinks[] = ['href' => route('profile.edit'), 'icon' => 'user', 'label' => 'الملف الشخصي', 'color' => '#0071AA'];
                                }

                                // Role-specific links
                                if ($isTeacher) {
                                    $navLinks[] = ['href' => route('teacher.my-subjects.index'), 'icon' => 'book', 'label' => 'موادي', 'color' => '#8b5cf6'];
                                    $navLinks[] = ['href' => route('teacher.tickets.index'),      'icon' => 'help', 'label' => 'تذاكر الدعم', 'color' => '#10b981'];
                                } elseif ($isStudent) {
                                    $navLinks[] = ['href' => route('student.my-program'),         'icon' => 'shield', 'label' => 'برنامجي الدراسي', 'color' => '#8b5cf6'];
                                    $navLinks[] = ['href' => route('student.payments.index'),     'icon' => 'card', 'label' => 'المدفوعات', 'color' => '#f59e0b'];
                                    $navLinks[] = ['href' => route('student.tickets.index'),      'icon' => 'help', 'label' => 'تذاكر الدعم', 'color' => '#10b981'];
                                } else {
                                    $navLinks[] = ['href' => route('admin.users.index'),          'icon' => 'users', 'label' => 'المستخدمون', 'color' => '#8b5cf6'];
                                    $navLinks[] = ['href' => route('admin.programs.index'),       'icon' => 'book', 'label' => 'البرامج', 'color' => '#f59e0b'];
                                }
                            @endphp

                            @foreach($navLinks as $link)
                            <a href="{{ $link['href'] }}"
                               style="display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:12px;text-decoration:none;transition:background 0.15s"
                               onmouseover="this.style.background='#f8faff'" onmouseout="this.style.background='transparent'">
                                <div style="width:34px;height:34px;border-radius:9px;background:{{ $link['color'] }}18;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                    @if($link['icon'] === 'grid')
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="{{ $link['color'] }}">
                                        <path d="M3 3h8v8H3zm10 0h8v8h-8zM3 13h8v8H3zm10 0h8v8h-8z" opacity=".9"/>
                                    </svg>
                                    @elseif($link['icon'] === 'user')
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="{{ $link['color'] }}">
                                        <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                                    </svg>
                                    @elseif($link['icon'] === 'book')
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="{{ $link['color'] }}">
                                        <path d="M18 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V4a2 2 0 00-2-2zM6 4h5v8l-2.5-1.5L6 12V4z"/>
                                    </svg>
                                    @elseif($link['icon'] === 'help')
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="{{ $link['color'] }}">
                                        <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12zM7 9h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2z"/>
                                    </svg>
                                    @elseif($link['icon'] === 'shield')
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="{{ $link['color'] }}">
                                        <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3z"/>
                                    </svg>
                                    @elseif($link['icon'] === 'card')
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="{{ $link['color'] }}">
                                        <path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                                    </svg>
                                    @elseif($link['icon'] === 'users')
                                    <svg width="17" height="17" viewBox="0 0 24 24" fill="{{ $link['color'] }}">
                                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                    </svg>
                                    @endif
                                </div>
                                <span style="font-size:0.875rem;font-weight:600;color:#374151" class="dark:text-gray-200">{{ $link['label'] }}</span>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#d1d5db" stroke-width="2" style="margin-right:auto;flex-shrink:0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </a>
                            @endforeach
                        </div>

                        {{-- Divider --}}
                        <div style="height:1px;background:#f1f5f9;margin:4px 0" class="dark:bg-white/10"></div>

                        {{-- Logout --}}
                        <div style="padding:8px">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        style="display:flex;align-items:center;gap:12px;width:100%;padding:10px 12px;border-radius:12px;border:none;background:transparent;cursor:pointer;text-align:right;transition:background 0.15s"
                                        onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                                    <div style="width:34px;height:34px;border-radius:9px;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                    </div>
                                    <span style="font-size:0.875rem;font-weight:700;color:#dc2626">تسجيل الخروج</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
