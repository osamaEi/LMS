@extends('layouts.dashboard')

@section('title', 'لوحة المعلم')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container mx-auto px-4 py-6 max-w-7xl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">مرحباً {{ auth()->user()->name }}</h1>
    </div>







    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- My Subjects -->
        <div class="lg:col-span-2">

            <!-- All Sessions Section -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden mt-6">
                <div class="p-6 border-b dark:border-gray-700" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-white">جميع الجلسات</h2>
                                <p class="text-sm text-white/70">{{ $stats['total_sessions'] }} جلسة - {{ $stats['live_sessions'] ?? 0 }} مباشرة الآن</p>
                            </div>
                        </div>
                        <a href="{{ route('teacher.schedule') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium bg-white/15 hover:bg-white/25 text-white transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            الجدول الكامل
                        </a>
                    </div>
                </div>

                <div class="p-6">
                    @php
                        $allSessions = collect();
                        if(isset($upcomingSessions)) $allSessions = $allSessions->merge($upcomingSessions);
                        if(isset($recentSessions)) $allSessions = $allSessions->merge($recentSessions);
                        $allSessions = $allSessions->unique('id')->sortByDesc('scheduled_at')->take(8);
                    @endphp

                    @if($allSessions->count() > 0)
                        <div class="space-y-3">
                            @foreach($allSessions as $session)
                                @php
                                    $isLive = $session->started_at && !$session->ended_at;
                                    $isUpcoming = $session->scheduled_at && \Carbon\Carbon::parse($session->scheduled_at)->isFuture();
                                    $isCompleted = $session->ended_at;
                                    $isZoom = $session->type === 'live_zoom';
                                @endphp
                                <div class="flex items-center gap-4 p-4 rounded-2xl border transition-all duration-300 hover:shadow-md
                                    {{ $isLive ? 'border-red-200 bg-red-50/50 dark:bg-red-900/10 dark:border-red-800' : 'border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800' }}">

                                    {{-- Session Icon --}}
                                    <div class="flex-shrink-0">
                                        @if($isLive)
                                            <div class="w-12 h-12 rounded-xl flex items-center justify-center relative" style="background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 12px rgba(239,68,68,0.3);">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-red-500 rounded-full border-2 border-white dark:border-gray-800" style="animation: pulse 2s infinite;"></span>
                                            </div>
                                        @elseif($isUpcoming)
                                            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 12px rgba(59,130,246,0.3);">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                        @else
                                            <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16,185,129,0.3);">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Session Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h4 class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $session->title }}</h4>
                                            @if($isLive)
                                                <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold" style="background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626;">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500" style="animation: pulse 1.5s infinite;"></span>
                                                    مباشر الآن
                                                </span>
                                            @elseif($isUpcoming)
                                                <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #2563eb;">
                                                    قادمة
                                                </span>
                                            @elseif($isCompleted)
                                                <span class="flex-shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669;">
                                                    مكتملة
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $session->subject->name ?? '' }}</p>
                                        @if($session->scheduled_at)
                                            <div class="flex items-center gap-3 mt-1.5 text-xs text-gray-400">
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d') }}
                                                </span>
                                                <span class="inline-flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('h:i A') }}
                                                </span>
                                                @if($session->duration_minutes)
                                                    <span class="inline-flex items-center gap-1">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                        </svg>
                                                        {{ $session->duration_minutes }} د
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        @if($isLive && $isZoom && $session->zoom_start_url)
                                            <a href="{{ $session->zoom_start_url }}" target="_blank"
                                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-all hover:scale-105"
                                               style="background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 15px rgba(239,68,68,0.4); animation: pulse 2s infinite;">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                                انضم الآن
                                            </a>
                                        @elseif($isUpcoming && $isZoom)
                                            @if($session->zoom_start_url)
                                                <a href="{{ $session->zoom_start_url }}" target="_blank"
                                                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-all hover:scale-105"
                                                   style="background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 15px rgba(59,130,246,0.4);">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                    ابدأ Zoom
                                                </a>
                                            @else
                                                <a href="{{ route('teacher.my-subjects.sessions.zoom', [$session->subject_id, $session->id]) }}"
                                                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-white transition-all hover:scale-105"
                                                   style="background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 15px rgba(59,130,246,0.4);">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                    تجهيز Zoom
                                                </a>
                                            @endif
                                        @endif

                                        <a href="{{ route('teacher.my-subjects.show', $session->subject_id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl text-sm font-medium border-2 border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:border-blue-400 hover:text-blue-600 dark:hover:border-blue-500 dark:hover:text-blue-400 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            عرض
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- View All Link --}}
                        <div class="mt-5 pt-5 border-t border-gray-100 dark:border-gray-700 text-center">
                            <a href="{{ route('teacher.schedule') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold transition-all hover:scale-105" style="background: linear-gradient(135deg, #0071AA, #005a88); color: white; box-shadow: 0 4px 15px rgba(0,113,170,0.3);">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                عرض جميع الجلسات في التقويم
                            </a>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl flex items-center justify-center" style="background: linear-gradient(135deg, #f1f5f9, #e2e8f0);">
                                <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">لا توجد جلسات</h3>
                            <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">لم يتم إنشاء أي جلسة بعد</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Student Feedback (NELC 2.4.9) -->
        
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Mini Calendar -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm p-4 mb-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: #0071AA;">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">التقويم</h3>
                </div>

                @php
                    $today = now();
                    $currentMonth = $today->month;
                    $currentYear = $today->year;
                    $firstDayOfMonth = $today->copy()->startOfMonth();
                    $lastDayOfMonth = $today->copy()->endOfMonth();
                    $startDayOfWeek = $firstDayOfMonth->dayOfWeek;
                    $daysInMonth = $lastDayOfMonth->day;

                    // Get sessions for this month
                    $sessionDates = $upcomingSessions->pluck('scheduled_at')->map(function($date) {
                        return $date ? $date->format('Y-m-d') : null;
                    })->filter()->toArray();

                    $arabicMonths = ['', 'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                    $arabicDays = ['ح', 'ن', 'ث', 'ر', 'خ', 'ج', 'س'];
                @endphp

                <!-- Month Header -->
                <div class="text-center mb-4">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ $arabicMonths[$currentMonth] }} {{ $currentYear }}</h4>
                </div>

                <!-- Days Header -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                    @foreach($arabicDays as $day)
                        <div class="text-center text-sm font-bold text-gray-600 dark:text-gray-300 py-2">{{ $day }}</div>
                    @endforeach
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-1">
                    @for($i = 0; $i < $startDayOfWeek; $i++)
                        <div class="aspect-square"></div>
                    @endfor

                    @for($day = 1; $day <= $daysInMonth; $day++)
                        @php
                            $dateStr = $currentYear . '-' . str_pad($currentMonth, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
                            $isToday = $day === $today->day;
                            $hasSession = in_array($dateStr, $sessionDates);
                        @endphp
                        <div class="aspect-square flex items-center justify-center rounded-lg text-sm relative
                            {{ $isToday ? 'font-bold text-white' : 'text-gray-700 dark:text-gray-300' }}
                            {{ $hasSession && !$isToday ? 'font-semibold' : '' }}"
                            style="{{ $isToday ? 'background-color: #0071AA;' : '' }}">
                            {{ $day }}
                            @if($hasSession)
                                <span class="absolute bottom-1 w-1.5 h-1.5 rounded-full" style="background-color: {{ $isToday ? '#ffffff' : '#0071AA' }};"></span>
                            @endif
                        </div>
                    @endfor
                </div>

                <!-- Legend -->
                <div class="flex items-center justify-center gap-4 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full" style="background-color: #0071AA;"></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">اليوم</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: #0071AA;"></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">جلسة</span>
                    </div>
                </div>
            </div>

            <!-- Upcoming Sessions -->
    

            <!-- Support Tickets (NELC 1.3.3) -->
        

            <!-- Pending Surveys (NELC 1.2.11) -->
       
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Attendance Chart
    const attendanceEl = document.getElementById('attendanceChart');
    if (attendanceEl) {
        const attendanceCtx = attendanceEl.getContext('2d');
        let attendanceData = @json($weeklyAttendance ?? []);

        // Default data if empty
        if (Object.keys(attendanceData).length === 0) {
            attendanceData = {'الأحد': 85, 'الإثنين': 90, 'الثلاثاء': 88, 'الأربعاء': 92, 'الخميس': 87};
        }

        new Chart(attendanceCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(attendanceData),
                datasets: [{
                    label: 'نسبة الحضور',
                    data: Object.values(attendanceData),
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) { return value + '%'; }
                        }
                    }
                }
            }
        });
    }

    // Ratings Distribution Chart
    const ratingsEl = document.getElementById('ratingsChart');
    if (ratingsEl) {
        const ratingsCtx = ratingsEl.getContext('2d');
        let ratingsData = @json($ratingsDistribution ?? []);

        // Default data if empty
        if (Object.keys(ratingsData).length === 0) {
            ratingsData = {1: 2, 2: 5, 3: 15, 4: 30, 5: 48};
        }

        new Chart(ratingsCtx, {
            type: 'doughnut',
            data: {
                labels: ['1 نجمة', '2 نجمة', '3 نجوم', '4 نجوم', '5 نجوم'],
                datasets: [{
                    data: Object.values(ratingsData),
                    backgroundColor: ['#ef4444', '#f97316', '#eab308', '#84cc16', '#22c55e']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
});
</script>
@endsection
