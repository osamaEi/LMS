@extends('layouts.dashboard')

@section('title', 'لوحة تحكم المعلم')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6">
    <!-- Card 1: عدد الدورات الحالية -->
    <div class="rounded-xl bg-brand-600 p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90 mb-1">عدد الدورات الحالية</div>
                <div class="text-4xl font-bold">{{ $stats['my_courses_count'] ?? 0 }}</div>
            </div>
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20">
                <svg class="fill-current" width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 5H3C1.9 5 1 5.9 1 7V17C1 18.1 1.9 19 3 19H21C22.1 19 23 18.1 23 17V7C23 5.9 22.1 5 21 5ZM21 17H3V7H21V17Z" fill="white"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 2: عدد الطلاب -->
    <div class="rounded-xl bg-success-600 p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90 mb-1">عدد الطلاب</div>
                <div class="text-4xl font-bold">{{ $stats['total_students'] ?? 0 }}</div>
            </div>
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20">
                <svg class="fill-current" width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 11C17.66 11 18.99 9.66 18.99 8C18.99 6.34 17.66 5 16 5C14.34 5 13 6.34 13 8C13 9.66 14.34 11 16 11ZM8 11C9.66 11 10.99 9.66 10.99 8C10.99 6.34 9.66 5 8 5C6.34 5 5 6.34 5 8C5 9.66 6.34 11 8 11ZM8 13C5.67 13 1 14.17 1 16.5V19H15V16.5C15 14.17 10.33 13 8 13ZM16 13C15.71 13 15.38 13.02 15.03 13.05C16.19 13.89 17 15.02 17 16.5V19H23V16.5C23 14.17 18.33 13 16 13Z" fill="white"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 3: محاضرات محجوزة اليوم -->
    <div class="rounded-xl bg-warning-600 p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm opacity-90 mb-1">محاضرات محجوزة اليوم</div>
                <div class="text-4xl font-bold">{{ $stats['today_classes'] ?? 0 }}</div>
            </div>
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/20">
                <svg class="fill-current" width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 4H18V2H16V4H8V2H6V4H5C3.89 4 3.01 4.9 3.01 6L3 20C3 21.1 3.89 22 5 22H19C20.1 22 21 21.1 21 20V6C21 4.9 20.1 4 19 4ZM19 20H5V9H19V20Z" fill="white"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-3 mt-6">
    <!-- Left Column - User Profile Card -->
    <div class="lg:col-span-1">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex flex-col items-center text-center">
                <div class="relative mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=10b981&color=fff&size=120"
                         alt="Profile"
                         class="h-30 w-30 rounded-full border-4 border-success-500" />
                    <span class="absolute bottom-2 right-2 h-4 w-4 rounded-full border-2 border-white bg-success-500"></span>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ auth()->user()->name }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ auth()->user()->national_id }}</p>

                <div class="w-full space-y-3 text-right">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                        <span class="text-sm text-gray-500 dark:text-gray-400">البريد الإلكتروني</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->email }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 dark:border-gray-800">
                        <span class="text-sm text-gray-500 dark:text-gray-400">رقم الهاتف</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->phone ?? 'غير محدد' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500 dark:text-gray-400">تاريخ التسجيل</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->created_at->format('Y/m/d') }}</span>
                    </div>
                </div>

                <a href="{{ route('teacher.profile') }}"
                   class="mt-6 w-full rounded-lg border border-brand-500 bg-white px-4 py-2.5 text-sm font-medium text-brand-500 hover:bg-brand-50 dark:bg-gray-900 dark:hover:bg-brand-500/10">
                    عرض الملف الكامل
                </a>
            </div>
        </div>

        <!-- Today's Classes -->
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h3 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">محاضرات اليوم</h3>
            <div class="space-y-4">
                @forelse($todayClasses ?? [] as $class)
                <div class="rounded-lg border border-gray-100 p-4 dark:border-gray-800">
                    <div class="mb-2 flex items-start justify-between">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $class->course->title }}</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ $class->title }}</p>
                        </div>
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>{{ $class->start_time }} - {{ $class->end_time }}</span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">لا توجد محاضرات اليوم</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column - Course Cards -->
    <div class="lg:col-span-2">
        <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">دوراتك النشطة حالياً</h2>
        <p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
            تابع جلسات التدريب المحجوزة اليوم، وتابع أنشطتك التعليمية بسهولة.
        </p>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            @forelse($myCourses ?? [] as $course)
            <!-- Course Card -->
            <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-4">
                    <div class="mb-4 h-40 w-full overflow-hidden rounded-lg bg-gray-200">
                        @if($course->image)
                            <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="h-full w-full object-cover" />
                        @else
                            <div class="flex h-full items-center justify-center bg-brand-100">
                                <svg class="h-16 w-16 text-brand-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <h3 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">{{ $course->title }}</h3>
                    <p class="mb-3 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                        {{ $course->description }}
                    </p>
                    <div class="mb-3 flex items-center gap-2">
                        <span class="inline-flex items-center rounded-full bg-success-50 px-2.5 py-0.5 text-xs font-medium text-success-700 dark:bg-success-500/20 dark:text-success-400">
                            نشطة
                        </span>
                    </div>
                    <div class="mb-2 text-xs text-gray-500 dark:text-gray-400">
                        {{ $course->students_count ?? 0 }} طالب | {{ $course->lessons_count ?? 0 }} درس
                    </div>
                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="mb-1 flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">{{ $course->progress ?? 50 }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                            <div class="h-full rounded-full bg-brand-500" style="width: {{ $course->progress ?? 50 }}%"></div>
                        </div>
                    </div>
                </div>
                <a href="#"
                   class="w-full block text-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
                   onclick="event.preventDefault(); alert('صفحة تفاصيل الدورة قيد التطوير');">
                    عرض التفاصيل
                </a>
            </div>
            @empty
            <div class="col-span-2 text-center py-8">
                <p class="text-gray-500 dark:text-gray-400 mb-4">لا توجد دورات نشطة</p>
                <a href="#"
                   class="inline-block rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600"
                   onclick="event.preventDefault(); alert('صفحة إنشاء الدورة قيد التطوير');">
                    إنشاء دورة جديدة
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
