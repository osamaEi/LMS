@extends('layouts.dashboard')

@section('title', 'الجلسات القادمة')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">الجلسات القادمة</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">جلسات Zoom المباشرة القادمة</p>
        </div>
    </div>

    <!-- Live Sessions -->
    @if($liveSessions->count() > 0)
        <div class="bg-gradient-to-l from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center gap-3 mb-4">
                <span class="relative flex h-3 w-3">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-white"></span>
                </span>
                <h2 class="text-xl font-bold">جلسات مباشرة الآن</h2>
            </div>
            <div class="grid gap-4">
                @foreach($liveSessions as $session)
                    <div class="bg-white/10 backdrop-blur rounded-lg p-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h3 class="font-semibold text-lg">{{ $session->title }}</h3>
                                <p class="text-white/80 text-sm">{{ $session->subject->name }}</p>
                            </div>
                            <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                               class="inline-flex items-center justify-center px-6 py-3 bg-white text-red-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                                <svg class="w-5 h-5 me-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                                </svg>
                                انضم الآن
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Upcoming Sessions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">الجلسات المجدولة</h2>
        </div>

        @if($upcomingSessions->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($upcomingSessions as $session)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <!-- Date Badge -->
                                <div class="flex-shrink-0 w-16 h-16 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl flex flex-col items-center justify-center">
                                    <span class="text-xs text-cyan-600 dark:text-cyan-400 font-medium">
                                        {{ $session->scheduled_at->format('M') }}
                                    </span>
                                    <span class="text-2xl font-bold text-cyan-600 dark:text-cyan-400">
                                        {{ $session->scheduled_at->format('d') }}
                                    </span>
                                </div>

                                <!-- Session Info -->
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $session->title }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $session->subject->name }}</p>
                                    @if($session->unit)
                                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $session->unit->title }}</p>
                                    @endif
                                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            {{ $session->scheduled_at->format('H:i') }}
                                        </span>
                                        @if($session->duration_minutes)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                </svg>
                                                {{ $session->duration_minutes }} دقيقة
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-3">
                                @if($session->scheduled_at->diffInMinutes(now()) <= 15 && $session->scheduled_at >= now())
                                    <a href="{{ route('student.sessions.join-zoom', $session->id) }}"
                                       class="inline-flex items-center px-4 py-2 bg-cyan-500 hover:bg-cyan-600 text-white rounded-lg transition">
                                        <svg class="w-4 h-4 me-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"/>
                                        </svg>
                                        انضم الآن
                                    </a>
                                @else
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        @if($session->scheduled_at->isToday())
                                            <span class="text-cyan-600 dark:text-cyan-400 font-medium">اليوم</span>
                                        @elseif($session->scheduled_at->isTomorrow())
                                            <span class="text-orange-600 dark:text-orange-400 font-medium">غداً</span>
                                        @else
                                            {{ $session->scheduled_at->diffForHumans() }}
                                        @endif
                                    </div>
                                @endif

                                @if($session->zoom_join_url)
                                    <a href="{{ $session->zoom_join_url }}" target="_blank"
                                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        رابط خارجي
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($upcomingSessions->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $upcomingSessions->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">لا توجد جلسات قادمة</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400">سيتم عرض الجلسات المجدولة هنا عند توفرها</p>
            </div>
        @endif
    </div>
</div>
@endsection
