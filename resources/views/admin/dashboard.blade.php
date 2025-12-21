@extends('layouts.dashboard')

@section('title', 'لوحة تحكم المدير')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">لوحة التحكم</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-1">نظرة عامة على النظام ومعايير NELC</p>
</div>

<!-- NELC Compliance Section -->
<div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg shadow-lg p-6 mb-6 text-white">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-bold">التوافق مع معايير NELC</h2>
            <p class="text-blue-100 text-sm">المركز الوطني للتعليم الإلكتروني</p>
        </div>
        <div class="text-center">
            <div class="text-4xl font-bold">{{ $nelcStats['satisfaction_rate'] ?? 0 }}%</div>
            <div class="text-sm text-blue-100">نسبة الرضا</div>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
        <div class="bg-white/10 rounded-lg p-3 text-center">
            <div class="text-2xl font-bold">{{ $nelcStats['avg_teacher_rating'] ?? 0 }}/5</div>
            <div class="text-xs text-blue-100">تقييم المدربين</div>
        </div>
        <div class="bg-white/10 rounded-lg p-3 text-center">
            <div class="text-2xl font-bold">{{ $nelcStats['attendance_rate'] ?? 0 }}%</div>
            <div class="text-xs text-blue-100">معدل الحضور</div>
        </div>
        <div class="bg-white/10 rounded-lg p-3 text-center">
            <div class="text-2xl font-bold">{{ $nelcStats['open_tickets'] ?? 0 }}</div>
            <div class="text-xs text-blue-100">تذاكر مفتوحة</div>
        </div>
        <div class="bg-white/10 rounded-lg p-3 text-center">
            <div class="text-2xl font-bold">{{ $nelcStats['active_surveys'] ?? 0 }}</div>
            <div class="text-xs text-blue-100">استبيانات نشطة</div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Teachers -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-blue-100 dark:bg-blue-900 rounded-lg p-3">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                </svg>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">المعلمين</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['teachers_count'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Students -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-green-100 dark:bg-green-900 rounded-lg p-3">
                <svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"></path>
                </svg>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">الطلاب</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['students_count'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Courses -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-orange-100 dark:bg-orange-900 rounded-lg p-3">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"></path>
                </svg>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">الدورات</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['courses_count'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">نشطة: {{ $stats['active_courses'] ?? 0 }}</p>
            </div>
        </div>
    </div>

    <!-- Terms -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-purple-100 dark:bg-purple-900 rounded-lg p-3">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-300" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">الفصول الدراسية</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['terms_count'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">نشطة: {{ $stats['active_terms'] ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- NELC Metrics Row -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Satisfaction Rate -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">معدل الرضا</h4>
            <span class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 rounded">NELC 1.2.11</span>
        </div>
        <div class="flex items-end gap-2">
            <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $nelcStats['satisfaction_rate'] ?? 0 }}%</span>
        </div>
        <div class="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-400">
            <span>{{ $nelcStats['active_surveys'] ?? 0 }} استبيان نشط</span>
        </div>
    </div>

    <!-- Support Response Time -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">متوسط الرد</h4>
            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 rounded">NELC 1.3.3</span>
        </div>
        <div class="flex items-end gap-2">
            <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $nelcStats['avg_response_time'] ?? 0 }}</span>
            <span class="text-gray-500 dark:text-gray-400 mb-1">دقيقة</span>
        </div>
        <div class="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-400">
            <span>{{ $nelcStats['open_tickets'] ?? 0 }} تذكرة مفتوحة</span>
        </div>
    </div>

    <!-- Teacher Rating -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">تقييم المدربين</h4>
            <span class="text-xs px-2 py-1 bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300 rounded">NELC 2.4.9</span>
        </div>
        <div class="flex items-end gap-2">
            <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $nelcStats['avg_teacher_rating'] ?? 0 }}</span>
            <span class="text-gray-500 dark:text-gray-400 mb-1">/5</span>
        </div>
        <div class="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-400">
            <span>{{ $pendingRatingsCount ?? 0 }} تقييم بانتظار الموافقة</span>
        </div>
    </div>

    <!-- Attendance Rate -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-2">
            <h4 class="text-sm font-medium text-gray-600 dark:text-gray-400">معدل الحضور</h4>
            <span class="text-xs px-2 py-1 bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300 rounded">NELC 1.2.5</span>
        </div>
        <div class="flex items-end gap-2">
            <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $nelcStats['attendance_rate'] ?? 0 }}%</span>
        </div>
        <div class="mt-2 flex items-center text-xs text-gray-500 dark:text-gray-400">
            <span>إجمالي الحضور</span>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Users Growth Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">نمو المستخدمين</h3>
        <div style="height: 300px;">
            <canvas id="usersGrowthChart"></canvas>
        </div>
    </div>

    <!-- Satisfaction Trend Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">اتجاه رضا المستفيدين</h3>
        <div style="height: 300px;">
            <canvas id="satisfactionChart"></canvas>
        </div>
    </div>
</div>

<!-- Support & Ratings Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Recent Tickets -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">تذاكر الدعم الأخيرة</h3>
            <a href="{{ route('admin.tickets.index') }}" class="text-sm text-blue-600 hover:underline">عرض الكل</a>
        </div>
        <div class="space-y-3">
            @forelse($recentTickets ?? [] as $ticket)
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($ticket->subject, 30) }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->user->name ?? 'غير معروف' }}</p>
                </div>
                <span class="text-xs px-2 py-1 rounded {{ $ticket->getStatusColor() }}">
                    {{ $ticket->getStatusLabel() }}
                </span>
            </div>
            @empty
            <p class="text-center text-gray-500 dark:text-gray-400 py-4">لا توجد تذاكر</p>
            @endforelse
        </div>
    </div>

    <!-- Teacher Ratings Overview -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">أفضل المدربين تقييماً</h3>
            <a href="{{ route('admin.teacher-ratings.index') }}" class="text-sm text-blue-600 hover:underline">عرض الكل</a>
        </div>
        <div class="space-y-3">
            @forelse($topTeachers ?? [] as $teacher)
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=0071AA&color=fff"
                         alt="{{ $teacher->name }}"
                         class="w-10 h-10 rounded-full">
                    <div class="mr-3">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $teacher->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $teacher->subjects_count ?? 0 }} مادة</p>
                    </div>
                </div>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span class="mr-1 text-sm font-medium text-gray-900 dark:text-white">{{ number_format($teacher->avg_rating ?? 0, 1) }}</span>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 dark:text-gray-400 py-4">لا توجد تقييمات</p>
            @endforelse
        </div>
    </div>

    <!-- Survey Completion -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">استبيانات الرضا</h3>
            <a href="{{ route('admin.surveys.index') }}" class="text-sm text-blue-600 hover:underline">عرض الكل</a>
        </div>
        <div class="space-y-4">
            @forelse($activeSurveys ?? [] as $survey)
            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($survey->title, 25) }}</p>
                    <span class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 rounded">نشط</span>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                    <span>{{ $survey->responses_count ?? 0 }} إجابة</span>
                    <span>{{ number_format($survey->getAverageRating(), 1) }}/5</span>
                </div>
                <div class="mt-2 w-full bg-gray-200 rounded-full h-2 dark:bg-gray-600">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $survey->completion_rate ?? 0 }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 dark:text-gray-400 py-4">لا توجد استبيانات نشطة</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Bottom Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Recent Teachers -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">أحدث المعلمين</h3>
        <div class="space-y-3">
            @forelse($recentTeachers ?? [] as $teacher)
            <div class="flex items-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=0071AA&color=fff"
                     alt="{{ $teacher->name }}"
                     class="w-10 h-10 rounded-full">
                <div class="mr-3">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $teacher->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $teacher->email }}</p>
                </div>
            </div>
            @empty
            <p class="text-center text-gray-500 dark:text-gray-400 py-4">لا يوجد معلمين</p>
            @endforelse
        </div>
    </div>

    <!-- Terms Status -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">الفصول الدراسية</h3>
        <div class="space-y-3">
            @foreach($termsStatus ?? [] as $status => $count)
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-300">
                    {{ $status === 'active' ? 'نشط' : ($status === 'upcoming' ? 'قادم' : 'مكتمل') }}
                </span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- System Stats -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">إحصائيات النظام</h3>
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-300">إجمالي المستخدمين</span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['total_users'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-300">تسجيل اليوم</span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['today_enrollments'] ?? 0 }}</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-sm text-gray-600 dark:text-gray-300">متوسط الطلاب</span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ number_format($stats['avg_students_per_course'] ?? 0, 1) }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Courses -->
<div class="mt-6">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">أحدث الدورات</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($recentCourses ?? [] as $course)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="h-40 bg-gray-200 dark:bg-gray-700">
                @if($course->image)
                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                @else
                    <div class="flex items-center justify-center h-full bg-brand-100 dark:bg-brand-900">
                        <svg class="w-16 h-16 text-brand-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"></path>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $course->title }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">{{ $course->description }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 rounded">
                        {{ $course->status_display }}
                    </span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $course->students_count ?? 0 }} طالب</span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $course->teacher->name ?? 'غير محدد' }}</p>
            </div>
        </div>
        @empty
        <div class="col-span-4 text-center py-8 text-gray-500 dark:text-gray-400">لا توجد دورات</div>
        @endforelse
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Users Growth Chart
    const usersGrowthCtx = document.getElementById('usersGrowthChart').getContext('2d');
    const studentsData = @json($studentsPerMonth ?? []);
    const teachersData = @json($teachersPerMonth ?? []);
    const allMonths = [...new Set([...Object.keys(studentsData), ...Object.keys(teachersData)])].sort();

    new Chart(usersGrowthCtx, {
        type: 'line',
        data: {
            labels: allMonths,
            datasets: [{
                label: 'الطلاب',
                data: allMonths.map(month => studentsData[month] || 0),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                tension: 0.3
            }, {
                label: 'المعلمين',
                data: allMonths.map(month => teachersData[month] || 0),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Satisfaction Chart
    const satisfactionCtx = document.getElementById('satisfactionChart').getContext('2d');
    const satisfactionData = @json($satisfactionTrend ?? []);

    new Chart(satisfactionCtx, {
        type: 'line',
        data: {
            labels: Object.keys(satisfactionData),
            datasets: [{
                label: 'معدل الرضا',
                data: Object.values(satisfactionData),
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5
                }
            }
        }
    });
});
</script>
@endsection
