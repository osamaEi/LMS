@extends('layouts.dashboard')

@section('title', 'لوحة المعلم')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container mx-auto px-4 py-6 max-w-7xl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">مرحباً {{ auth()->user()->name }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">لوحة تحكم المعلم - معايير NELC</p>
    </div>



    <!-- Statistics -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6 w-full">
        <div class="rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium" style="color: #bfdbfe;">المواد</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $stats['subjects_count'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium" style="color: #a7f3d0;">الطلاب</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $stats['total_students'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium" style="color: #ddd6fe;">الجلسات</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $stats['total_sessions'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="rounded-xl shadow-lg p-6 transition-all duration-300 hover:shadow-xl" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium" style="color: #fde68a;">معدل الحضور</p>
                    <p class="text-3xl font-bold text-white mt-1">{{ $stats['attendance_rate'] ?? 0 }}%</p>
                    <p class="text-xs" style="color: rgba(255,255,255,0.7);">NELC 1.2.5</p>
                </div>
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>



    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- My Subjects -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="p-6 border-b dark:border-gray-700" style="background: linear-gradient(to right, #eff6ff, #eef2ff);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: #3b82f6;">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">المواد الدراسية</h2>
                    </div>
                </div>
                <div class="p-6">
                    @if($subjects->count() > 0)
                        <div class="space-y-4">
                            @foreach($subjects as $index => $subject)
                            @php
                                $borderColors = ['#3b82f6', '#8b5cf6', '#10b981', '#f59e0b'];
                                $borderColor = $borderColors[$index % count($borderColors)];
                            @endphp
                            <div class="group rounded-xl p-5 hover:shadow-lg transition-all duration-300 bg-gray-50 dark:bg-gray-700" style="border-right: 4px solid {{ $borderColor }};">
                                <div class="flex items-center justify-between flex-wrap gap-4">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ $subject->name }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $subject->term->program->name ?? '' }} - {{ $subject->term->name ?? '' }}
                                        </p>
                                        <div class="flex items-center gap-3 mt-3 text-sm flex-wrap">
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full" style="background: #d1fae5; color: #047857;">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/></svg>
                                                {{ $subject->enrollments_count }} طالب
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full" style="background: #ede9fe; color: #6d28d9;">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/></svg>
                                                {{ $subject->sessions_count ?? 0 }} جلسة
                                            </span>
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full" style="background: #fef3c7; color: #b45309;">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                {{ number_format($subject->teacher_rating ?? 0, 1) }}
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('teacher.subjects.show', $subject->id) }}"
                                       class="px-5 py-2.5 text-white rounded-lg text-sm font-medium shadow-md hover:shadow-lg transition-all duration-300" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                                        عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">لا توجد مواد دراسية</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Student Feedback (NELC 2.4.9) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-6">
                <div class="p-6 border-b dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">آخر تقييمات الطلاب</h2>
                        <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300 rounded">NELC 2.4.9</span>
                    </div>
                </div>
                <div class="p-6">
                    @if(isset($recentRatings) && $recentRatings->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentRatings as $rating)
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        @if(!$rating->is_anonymous)
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $rating->student->name ?? 'طالب' }}</span>
                                        @else
                                        <span class="text-sm text-gray-500 dark:text-gray-400">تقييم مجهول</span>
                                        @endif
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $rating->subject->name ?? '' }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $rating->overall_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        @endfor
                                    </div>
                                </div>
                                @if($rating->comment)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $rating->comment }}</p>
                                @endif
                                @if($rating->improvement_suggestions)
                                <p class="text-sm text-blue-600 dark:text-blue-400 mt-2">
                                    <strong>اقتراح:</strong> {{ $rating->improvement_suggestions }}
                                </p>
                                @endif
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $rating->created_at->diffForHumans() }}</p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400 text-center py-4">لا توجد تقييمات بعد</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Upcoming Sessions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
                <div class="p-4 border-b dark:border-gray-700" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-white">الجلسات القادمة</h2>
                    </div>
                </div>
                <div class="p-4">
                    @if($upcomingSessions->count() > 0)
                        <div class="space-y-3">
                            @foreach($upcomingSessions as $session)
                            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700" style="border-right: 3px solid #8b5cf6;">
                                <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $session->title }}</div>
                                <div class="text-xs mt-1" style="color: #6d28d9;">{{ $session->subject->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y-m-d H:i') }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-6 text-sm">لا توجد جلسات قادمة</p>
                    @endif
                </div>
            </div>

            <!-- Support Tickets (NELC 1.3.3) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg mt-6 overflow-hidden">
                <div class="p-4 border-b dark:border-gray-700" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-white">تذاكر الدعم</h2>
                        </div>
                        <span class="text-xs px-2 py-1 rounded" style="background: rgba(255,255,255,0.2); color: white;">NELC 1.3.3</span>
                    </div>
                </div>
                <div class="p-4">
                    <div class="text-center mb-4">
                        <a href="{{ route('teacher.tickets.create') }}" class="inline-flex items-center px-4 py-2 text-white rounded-lg text-sm transition-all" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                            إنشاء تذكرة جديدة
                        </a>
                    </div>
                    @if(isset($myTickets) && $myTickets->count() > 0)
                        <div class="space-y-3">
                            @foreach($myTickets as $ticket)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($ticket->subject, 25) }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->created_at->diffForHumans() }}</div>
                                </div>
                                <span class="text-xs px-2 py-1 rounded-full" style="background: #dbeafe; color: #1e40af;">
                                    {{ $ticket->getStatusLabel() }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-4 text-sm">لا توجد تذاكر</p>
                    @endif
                </div>
            </div>

            <!-- Pending Surveys (NELC 1.2.11) -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg mt-6 overflow-hidden">
                <div class="p-4 border-b dark:border-gray-700" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-white">استبيانات الرضا</h2>
                        </div>
                        <span class="text-xs px-2 py-1 rounded" style="background: rgba(255,255,255,0.2); color: white;">NELC 1.2.11</span>
                    </div>
                </div>
                <div class="p-4">
                    @if(isset($pendingSurveys) && $pendingSurveys > 0)
                        <div class="text-center">
                            <div class="text-4xl font-bold mb-2" style="color: #f59e0b;">{{ $pendingSurveys }}</div>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">استبيان بانتظار الإكمال</p>
                            <a href="{{ route('teacher.surveys.index') }}" class="inline-block mt-3 text-sm text-brand-500 hover:underline">عرض الاستبيانات</a>
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400 text-center py-4 text-sm">لا توجد استبيانات معلقة</p>
                    @endif
                </div>
            </div>
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
