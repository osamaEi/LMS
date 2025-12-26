@extends('layouts.dashboard')

@section('title', 'لوحة تحكم الطالب')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column - Profile & Payments -->
    <div class="lg:col-span-1 space-y-6">
        <!-- Student Profile Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
            <!-- Profile Header -->
            <div class="bg-gradient-to-l from-cyan-400 to-cyan-500 p-6 text-center relative">
                <!-- Print Button -->
                <button class="absolute top-4 left-4 w-10 h-10 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                </button>

                <!-- Avatar -->
                <div class="relative inline-block">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=0D6FA6&color=fff&size=120"
                         alt="{{ auth()->user()->name }}"
                         class="w-24 h-24 rounded-full border-4 border-white shadow-lg mx-auto" />
                    <span class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></span>
                </div>

                <!-- Name -->
                <h2 class="text-xl font-bold text-white mt-4">{{ auth()->user()->name }}</h2>
            </div>

            <!-- Profile Info -->
            <div class="p-5 space-y-4">
                <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">رقم الهوية</span>
                        <p class="font-medium">{{ auth()->user()->national_id ?? '1023456789' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">تاريخ الالتحاق</span>
                        <p class="font-medium">{{ auth()->user()->created_at->format('d') }} {{ __('سبتمبر') }} {{ auth()->user()->created_at->format('Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">البريد الإلكتروني</span>
                        <p class="font-medium">{{ auth()->user()->email ?? '15 فبراير 2026' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">رقم الجوال</span>
                        <p class="font-medium">{{ auth()->user()->phone ?? 'ST-2025-145' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">الجروب المنضم له</span>
                        <p class="font-medium">{{ auth()->user()->group ?? 'التصميم الرقمي - G2' }}</p>
                    </div>
                </div>

                <!-- Quarter End Date -->
                <div class="mt-4 p-3 bg-cyan-50 dark:bg-cyan-900/20 rounded-xl border border-cyan-200 dark:border-cyan-800">
                    <p class="text-sm text-cyan-700 dark:text-cyan-300 font-medium">
                        انتهاء الربع الدراسي الحالي: <span class="font-bold">25 ديسمبر 2025</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Payments Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-5">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">المدفوعات</h3>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4 mb-4">
                <p class="text-sm text-yellow-800 dark:text-yellow-300">
                    لديك دفعة مستحقة بمبلغ <span class="font-bold">1,500 ريال</span>، آخر موعد للسداد: <span class="font-bold">20 أكتوبر 2025</span>
                </p>
            </div>

            <div class="space-y-3">
                <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <span class="text-sm text-gray-500 dark:text-gray-400">خطة الدفع</span>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white">ربع سنوية</span>
                </div>

                <div class="flex items-center gap-3 text-gray-600 dark:text-gray-300">
                    <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <span class="text-sm text-gray-500 dark:text-gray-400">طريقة السداد</span>
                    </div>
                    <span class="font-medium text-gray-900 dark:text-white">تحويل بنكي</span>
                </div>
            </div>

            <button class="w-full mt-4 bg-cyan-500 hover:bg-cyan-600 text-white font-medium py-3 px-4 rounded-xl transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
                إرفاق وصل التحويل
            </button>
        </div>
    </div>

    <!-- Right Column - Schedule & Notifications -->
    <div class="lg:col-span-2 space-y-6">
        <!-- This Week's Schedule -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">جدول هذا الأسبوع</h3>
                <a href="{{ route('student.schedule') }}" class="text-cyan-600 hover:text-cyan-700 text-sm font-medium flex items-center gap-1">
                    عرض الحضور الكامل
                    <svg class="w-4 h-4 rtl:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-5 py-3 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">اليوم</th>
                            <th class="px-5 py-3 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">المادة / الدورة</th>
                            <th class="px-5 py-3 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">نوع الجلسة</th>
                            <th class="px-5 py-3 text-right text-sm font-semibold text-gray-600 dark:text-gray-300">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @php
                        $scheduleData = [
                            ['day' => 'الأحد', 'subject' => 'إدارة الأعمال', 'type' => 'حضوري', 'status' => 'attended'],
                            ['day' => 'الاثنين', 'subject' => 'مهارات Excel للمحاسبين', 'type' => 'أونلاين', 'status' => 'pending'],
                            ['day' => 'الثلاثاء', 'subject' => 'مراجعة الفيديوهات المسجلة', 'type' => 'مسجّلة', 'status' => 'watch'],
                            ['day' => 'الأربعاء', 'subject' => 'محاسبة مالية 2', 'type' => 'مسجّلة', 'status' => 'absent'],
                            ['day' => 'الخميس', 'subject' => 'مبادئ الاقتصاد', 'type' => 'أونلاين', 'status' => 'rating'],
                        ];
                        @endphp

                        @foreach($scheduleData as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-5 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ $item['day'] }}</td>
                            <td class="px-5 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $item['subject'] }}</td>
                            <td class="px-5 py-4">
                                <span class="text-sm text-gray-600 dark:text-gray-300">{{ $item['type'] }}</span>
                            </td>
                            <td class="px-5 py-4">
                                @if($item['status'] === 'attended')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        تم الحضور
                                    </span>
                                @elseif($item['status'] === 'pending')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        لم يبدأ بعد
                                    </span>
                                @elseif($item['status'] === 'watch')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-400 cursor-pointer hover:bg-cyan-200 transition">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                        شاهد الآن
                                    </span>
                                @elseif($item['status'] === 'absent')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                        غياب
                                    </span>
                                @elseif($item['status'] === 'rating')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 cursor-pointer hover:bg-orange-200 transition">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        جاري التقييم
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Current Notifications -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">تنبيهاتك الحالية</h3>
            </div>

            <div class="p-5 space-y-4">
                @php
                $notifications = [
                    ['title' => 'تم إضافة دورة جديدة', 'desc' => 'دورة "مهارات التواصل المهني" أصبحت متاحة الآن.', 'time' => 'اليوم، الساعة 09:30 صباحاً', 'icon' => 'bell'],
                    ['title' => 'تم إضافة دورة جديدة', 'desc' => 'دورة "مهارات التواصل المهني" أصبحت متاحة الآن.', 'time' => 'اليوم، الساعة 09:30 صباحاً', 'icon' => 'bell'],
                    ['title' => 'تم إضافة دورة جديدة', 'desc' => 'دورة "مهارات التواصل المهني" أصبحت متاحة الآن.', 'time' => 'اليوم، الساعة 09:30 صباحاً', 'icon' => 'bell'],
                ];
                @endphp

                @foreach($notifications as $notification)
                <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700/50 transition cursor-pointer">
                    <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/30 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $notification['title'] }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $notification['desc'] }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $notification['time'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
