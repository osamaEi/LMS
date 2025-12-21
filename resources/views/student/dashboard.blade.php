@extends('layouts.dashboard')

@section('title', 'لوحة الطالب')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="container mx-auto px-4 py-6 max-w-7xl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">مرحباً {{ auth()->user()->name }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">لوحة تحكم الطالب - معايير NELC</p>
    </div>

    <!-- Live Sessions Alert -->
    @if($liveSessions->count() > 0)
    <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-6 mb-6 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-red-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="mr-3 flex-1">
                <h3 class="text-lg font-bold text-red-800 dark:text-red-300">جلسة مباشرة الآن!</h3>
                <div class="mt-2 space-y-2">
                    @foreach($liveSessions as $session)
                    <div class="flex items-center justify-between bg-white dark:bg-gray-800 rounded p-3">
                        <div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $session->title }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $session->subject->name }}</div>
                        </div>
                        <a href="{{ route('admin.sessions.show', $session->id) }}"
                           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                            انضم الآن
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Progress Card -->
    <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-lg shadow-lg p-6 mb-6 text-white">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-bold">تقدمك الأكاديمي</h2>
                <p class="text-green-100 text-sm">معيار NELC 2.2.5 - تتبع التقدم</p>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold">{{ $stats['overall_progress'] ?? 0 }}%</div>
                <div class="text-sm text-green-100">نسبة الإكمال</div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-white/10 rounded-lg p-3 text-center">
                <div class="text-lg font-bold">{{ $stats['attendance_rate'] ?? 0 }}%</div>
                <div class="text-xs text-green-100">معدل الحضور</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 text-center">
                <div class="text-lg font-bold">{{ $stats['completed_sessions'] ?? 0 }}</div>
                <div class="text-xs text-green-100">جلسات مكتملة</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 text-center">
                <div class="text-lg font-bold">{{ $stats['average_grade'] ?? 'N/A' }}</div>
                <div class="text-xs text-green-100">متوسط الدرجات</div>
            </div>
            <div class="bg-white/10 rounded-lg p-3 text-center">
                <div class="text-lg font-bold">{{ $stats['pending_tasks'] ?? 0 }}</div>
                <div class="text-xs text-green-100">مهام معلقة</div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">موادي</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['subjects_count'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">إجمالي الجلسات</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['total_sessions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">جلسات مكتملة</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['completed_sessions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">مباشر الآن</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['live_sessions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Progress Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">تقدم المواد</h3>
            <div style="height: 250px;">
                <canvas id="progressChart"></canvas>
            </div>
        </div>

        <!-- Attendance Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">سجل الحضور (NELC 1.2.5)</h3>
            <div style="height: 250px;">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- My Subjects -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b dark:border-gray-700 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">موادي الدراسية</h2>
                    <a href="{{ route('student.schedule') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        عرض الجدول
                    </a>
                </div>
                <div class="p-6">
                    @if($subjects->count() > 0)
                        <div class="space-y-4">
                            @foreach($subjects as $subject)
                            <div class="border dark:border-gray-700 rounded-lg p-4 hover:border-blue-500 transition">
                                <div class="flex items-center justify-between flex-wrap gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h3 class="font-bold text-gray-900 dark:text-white">{{ $subject->name }}</h3>
                                            @if($subject->prerequisites_description)
                                            <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 rounded">متطلبات</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $subject->term->program->name ?? '' }} - {{ $subject->term->name ?? '' }}
                                        </p>
                                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            <span>المعلم: {{ $subject->teacher->name ?? 'غير محدد' }}</span>
                                            <span>{{ $subject->sessions_count }} جلسة</span>
                                        </div>
                                        <!-- Progress Bar -->
                                        <div class="mt-3">
                                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                <span>التقدم</span>
                                                <span>{{ $subject->pivot->progress ?? 0 }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $subject->pivot->progress ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        <a href="{{ route('student.subjects.show', $subject->id) }}"
                                           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm whitespace-nowrap">
                                            عرض التفاصيل
                                        </a>
                                        @if(isset($canRateTeacher[$subject->id]) && $canRateTeacher[$subject->id])
                                        <a href="{{ route('teacher-ratings.create', ['subject' => $subject->id]) }}"
                                           class="text-xs text-purple-600 hover:text-purple-800 dark:text-purple-400">
                                            تقييم المدرب
                                        </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p class="mt-4 text-gray-600 dark:text-gray-400">لم يتم تسجيلك في أي مواد حتى الآن</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pending Tasks & Evaluations -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-6">
                <div class="p-6 border-b dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">المهام والتقييمات المعلقة</h2>
                </div>
                <div class="p-6">
                    @if(isset($pendingEvaluations) && $pendingEvaluations->count() > 0)
                        <div class="space-y-3">
                            @foreach($pendingEvaluations as $evaluation)
                            <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $evaluation->title }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ $evaluation->subject->name ?? '' }}</div>
                                    <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                        الموعد النهائي: {{ $evaluation->due_date ? $evaluation->due_date->format('Y/m/d') : 'غير محدد' }}
                                    </div>
                                </div>
                                <span class="text-xs px-2 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 rounded">
                                    {{ $evaluation->type === 'assignment' ? 'واجب' : ($evaluation->type === 'quiz' ? 'اختبار' : 'مهمة') }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400 text-center py-4">لا توجد مهام معلقة</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Upcoming Sessions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="p-6 border-b dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">الجلسات القادمة</h2>
                </div>
                <div class="p-6">
                    @if($upcomingSessions->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingSessions as $session)
                            <div class="border-r-4 border-blue-500 pr-4 py-2">
                                <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $session->title }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $session->subject->name }}</div>
                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y-m-d H:i') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->diffForHumans() }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm">لا توجد جلسات قادمة</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Support Tickets (NELC 1.3.3) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-6">
                <div class="p-6 border-b dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">الدعم الفني</h2>
                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 rounded">NELC 1.3.3</span>
                    </div>
                </div>
                <div class="p-6">
                    <div class="text-center mb-4">
                        <a href="{{ route('tickets.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                            </svg>
                            طلب مساعدة
                        </a>
                    </div>
                    @if(isset($myTickets) && $myTickets->count() > 0)
                        <div class="space-y-3">
                            @foreach($myTickets as $ticket)
                            <a href="{{ route('tickets.show', $ticket->id) }}" class="block p-3 bg-gray-50 dark:bg-gray-700 rounded hover:bg-gray-100 dark:hover:bg-gray-600">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($ticket->subject, 20) }}</div>
                                    <span class="text-xs px-2 py-1 rounded bg-{{ $ticket->getStatusColor() }}-100 text-{{ $ticket->getStatusColor() }}-800 dark:bg-{{ $ticket->getStatusColor() }}-900 dark:text-{{ $ticket->getStatusColor() }}-300">
                                        {{ $ticket->getStatusLabel() }}
                                    </span>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $ticket->created_at->diffForHumans() }}</div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400 text-center py-4 text-sm">لا توجد تذاكر سابقة</p>
                    @endif
                </div>
            </div>

            <!-- Pending Surveys (NELC 1.2.11) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-6">
                <div class="p-6 border-b dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">استبيانات الرضا</h2>
                        <span class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 rounded">NELC 1.2.11</span>
                    </div>
                </div>
                <div class="p-6">
                    @if(isset($pendingSurveys) && $pendingSurveys->count() > 0)
                        <div class="space-y-3">
                            @foreach($pendingSurveys as $survey)
                            <a href="{{ route('surveys.show', $survey->id) }}" class="block p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded hover:bg-yellow-100 dark:hover:bg-yellow-900/30">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $survey->title }}</div>
                                <div class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                                    @if($survey->is_mandatory)
                                    <span class="text-red-600 dark:text-red-400">إلزامي</span> -
                                    @endif
                                    مطلوب إكماله
                                </div>
                                @if($survey->ends_at)
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    ينتهي: {{ $survey->ends_at->format('Y/m/d') }}
                                </div>
                                @endif
                            </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400 text-center py-4 text-sm">لا توجد استبيانات معلقة</p>
                    @endif
                </div>
            </div>

            <!-- Rate Teachers (NELC 2.4.9) -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow mt-6">
                <div class="p-6 border-b dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">تقييم المدربين</h2>
                        <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300 rounded">NELC 2.4.9</span>
                    </div>
                </div>
                <div class="p-6">
                    @if(isset($teachersToRate) && $teachersToRate->count() > 0)
                        <div class="space-y-3">
                            @foreach($teachersToRate as $item)
                            <a href="{{ route('teacher-ratings.create', ['subject' => $item['subject']->id]) }}" class="block p-3 bg-purple-50 dark:bg-purple-900/20 rounded hover:bg-purple-100 dark:hover:bg-purple-900/30">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item['teacher']->name }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">{{ $item['subject']->name }}</div>
                                <div class="text-xs text-purple-600 dark:text-purple-400 mt-1">قيّم الآن</div>
                            </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400 text-center py-4 text-sm">لا توجد تقييمات معلقة</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Progress Chart
    const progressCtx = document.getElementById('progressChart').getContext('2d');
    const progressData = @json($subjectsProgress ?? ['مادة 1' => 75, 'مادة 2' => 50, 'مادة 3' => 90]);

    new Chart(progressCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(progressData),
            datasets: [{
                label: 'نسبة الإكمال',
                data: Object.values(progressData),
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderRadius: 5
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) { return value + '%'; }
                    }
                }
            }
        }
    });

    // Attendance Chart
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    let attendanceData = @json($weeklyAttendance ?? []);

    // Default data if empty
    if (Object.keys(attendanceData).length === 0) {
        attendanceData = {'الأسبوع 1': 100, 'الأسبوع 2': 80, 'الأسبوع 3': 90, 'الأسبوع 4': 85};
    }

    new Chart(attendanceCtx, {
        type: 'line',
        data: {
            labels: Object.keys(attendanceData),
            datasets: [{
                label: 'نسبة الحضور',
                data: Object.values(attendanceData),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3,
                fill: true
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
});
</script>
@endsection
