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
                    @if(auth()->user()->role === 'admin')
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
        <div class="flex items-center gap-4">
            <!-- Dark Mode Toggle -->
            <button
                @click="darkMode = !darkMode"
                class="relative flex h-10 w-10 items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                title="تبديل الوضع الليلي"
            >
                <!-- Sun Icon (shown in dark mode) -->
                <svg x-show="darkMode" class="fill-current text-yellow-400" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 2.5C10 1.83696 10.5178 1.30435 11.1792 1.30435C11.8406 1.30435 12.3584 1.83696 12.3584 2.5V3.04348C12.3584 3.70652 11.8406 4.23913 11.1792 4.23913C10.5178 4.23913 10 3.70652 10 3.04348V2.5Z" fill="currentColor"/>
                    <path d="M10 15.7609C10 15.0978 10.5178 14.5652 11.1792 14.5652C11.8406 14.5652 12.3584 15.0978 12.3584 15.7609V16.3043C12.3584 16.9674 11.8406 17.5 11.1792 17.5C10.5178 17.5 10 16.9674 10 16.3043V15.7609Z" fill="currentColor"/>
                    <path d="M3.66919 5.49783C3.20818 5.03246 3.20818 4.27841 3.66919 3.81304C4.1302 3.34767 4.87587 3.34767 5.33687 3.81304L5.72134 4.20109C6.18235 4.66645 6.18235 5.42051 5.72134 5.88587C5.26033 6.35124 4.51467 6.35124 4.05366 5.88587L3.66919 5.49783Z" fill="currentColor"/>
                    <path d="M16.638 14.1139C16.177 13.6486 16.177 12.8945 16.638 12.4292C17.099 11.9638 17.8447 11.9638 18.3057 12.4292L18.6902 12.8172C19.1512 13.2826 19.1512 14.0366 18.6902 14.502C18.2292 14.9674 17.4835 14.9674 17.0225 14.502L16.638 14.1139Z" fill="currentColor"/>
                    <path d="M2.5 10C1.83696 10 1.30435 10.5178 1.30435 11.1792C1.30435 11.8406 1.83696 12.3584 2.5 12.3584H3.04348C3.70652 12.3584 4.23913 11.8406 4.23913 11.1792C4.23913 10.5178 3.70652 10 3.04348 10H2.5Z" fill="currentColor"/>
                    <path d="M15.7609 10C15.0978 10 14.5652 10.5178 14.5652 11.1792C14.5652 11.8406 15.0978 12.3584 15.7609 12.3584H16.3043C16.9674 12.3584 17.5 11.8406 17.5 11.1792C17.5 10.5178 16.9674 10 16.3043 10H15.7609Z" fill="currentColor"/>
                    <path d="M5.49783 16.638C5.03246 16.177 4.27841 16.177 3.81304 16.638C3.34767 17.099 3.34767 17.8447 3.81304 18.3057L4.20109 18.6902C4.66645 19.1512 5.42051 19.1512 5.88587 18.6902C6.35124 18.2292 6.35124 17.4835 5.88587 17.0225L5.49783 16.638Z" fill="currentColor"/>
                    <path d="M14.1139 3.66919C13.6486 3.20818 12.8945 3.20818 12.4292 3.66919C11.9638 4.1302 11.9638 4.87587 12.4292 5.33687L12.8172 5.72134C13.2826 6.18235 14.0366 6.18235 14.502 5.72134C14.9674 5.26033 14.9674 4.51467 14.502 4.05366L14.1139 3.66919Z" fill="currentColor"/>
                    <path d="M11.1792 6.95652C8.85649 6.95652 6.97388 8.85607 6.97388 11.1989C6.97388 13.5418 8.85649 15.4413 11.1792 15.4413C13.5019 15.4413 15.3845 13.5418 15.3845 11.1989C15.3845 8.85607 13.5019 6.95652 11.1792 6.95652Z" fill="currentColor"/>
                </svg>

                <!-- Moon Icon (shown in light mode) -->
                <svg x-show="!darkMode" class="fill-current text-gray-500" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 0C4.477 0 0 4.477 0 10C0 15.523 4.477 20 10 20C11.395 20 12.725 19.7 13.929 19.153C14.345 18.977 14.404 18.402 14.061 18.128C11.672 16.317 10 13.396 10 10C10 6.604 11.672 3.683 14.061 1.872C14.404 1.598 14.345 1.023 13.929 0.847C12.725 0.3 11.395 0 10 0Z" fill="currentColor"/>
                </svg>
            </button>

            <!-- Notification Bell -->
            <button class="relative flex h-10 w-10 items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <span class="absolute -top-0.5 -left-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500">
                    <span class="text-[10px] font-bold text-white">3</span>
                </span>
                <svg class="fill-current text-gray-500 dark:text-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 18.3333C11.1506 18.3333 12.0833 17.4006 12.0833 16.25H7.91667C7.91667 17.4006 8.84937 18.3333 10 18.3333ZM16.25 12.0833V7.91667C16.25 5.15417 14.7656 2.84271 12.1875 2.25521V1.66667C12.1875 0.953125 11.7135 0.416667 10 0.416667C8.28646 0.416667 7.8125 0.953125 7.8125 1.66667V2.25521C5.24479 2.84271 3.75 5.14479 3.75 7.91667V12.0833L1.66667 14.1667V15.2083H18.3333V14.1667L16.25 12.0833Z" fill=""/>
                </svg>
            </button>

            <!-- User Profile Dropdown -->
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <!-- Trigger Button -->
                <button
                    @click="open = !open"
                    class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                >
                    <div class="relative">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D6FA6&color=fff"
                             alt="User"
                             class="h-10 w-10 rounded-full border-2 border-blue-500" />
                        <span class="absolute -bottom-0.5 -left-0.5 h-3 w-3 rounded-full border-2 border-white bg-green-500 dark:border-gray-900"></span>
                    </div>
                    <div class="hidden md:block text-right">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ auth()->user()->getRoleDisplayName() }}</p>
                    </div>
                    <svg
                        class="fill-current text-gray-500 dark:text-gray-400 transition-transform duration-200"
                        :class="{'rotate-180': open}"
                        width="16"
                        height="16"
                        viewBox="0 0 16 16"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path d="M8 10.6667L4 6.66667L4.93333 5.73333L8 8.8L11.0667 5.73333L12 6.66667L8 10.6667Z" fill=""/>
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    class="absolute left-0 mt-2 w-64 origin-top-left rounded-lg bg-white dark:bg-gray-800 shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                    style="display: none;"
                >
                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ auth()->user()->email }}</p>
                        <span class="inline-flex mt-2 px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                            {{ auth()->user()->getRoleDisplayName() }}
                        </span>
                    </div>

                    <!-- Menu Items -->
                    <div class="py-2">
                        <!-- Profile Link -->
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>الملف الشخصي</span>
                        </a>

                        <!-- Settings Link -->
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>الإعدادات</span>
                        </a>

                        <!-- Help Link -->
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>المساعدة والدعم</span>
                        </a>
                    </div>

                    <!-- Logout -->
                    <div class="border-t border-gray-100 dark:border-gray-700 py-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 w-full px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span class="font-medium">تسجيل الخروج</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
