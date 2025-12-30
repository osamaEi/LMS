@extends('layouts.dashboard')

@section('title', 'لوحة تحكم المدير')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">لوحة التحكم</h1>
    <p class="text-gray-600 dark:text-gray-400 mt-1">نظرة عامة على النظام</p>
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

    <!-- Subjects -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0 bg-orange-100 dark:bg-orange-900 rounded-lg p-3">
                <svg class="w-6 h-6 text-orange-600 dark:text-orange-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z"></path>
                </svg>
            </div>
            <div class="mr-4">
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">المواد الدراسية</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['subjects_count'] ?? 0 }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">نشطة: {{ $stats['active_subjects'] ?? 0 }}</p>
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

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Users Growth Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">نمو المستخدمين</h3>
        <div style="height: 300px;">
            <canvas id="usersGrowthChart"></canvas>
        </div>
    </div>

    <!-- Enrollments Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">التسجيلات الشهرية</h3>
        <div style="height: 300px;">
            <canvas id="enrollmentsChart"></canvas>
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
                <span class="text-sm text-gray-600 dark:text-gray-300">البرامج التعليمية</span>
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $stats['programs_count'] ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Subjects -->
<div class="mt-6">
    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">أحدث المواد الدراسية</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($recentSubjects ?? [] as $subject)
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
            <div class="h-32 bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center">
                <svg class="w-12 h-12 text-white/80" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                </svg>
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $subject->name }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $subject->code }}</p>
                <div class="flex items-center justify-between">
                    <span class="text-xs px-2 py-1 rounded
                        {{ $subject->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ $subject->status === 'active' ? 'نشطة' : 'غير نشطة' }}
                    </span>
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $subject->term->name ?? '' }}</span>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ $subject->teacher->name ?? 'غير محدد' }}</p>
            </div>
        </div>
        @empty
        <div class="col-span-4 text-center py-8 text-gray-500 dark:text-gray-400">لا توجد مواد دراسية</div>
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

    // Enrollments Chart
    const enrollmentsCtx = document.getElementById('enrollmentsChart').getContext('2d');

    new Chart(enrollmentsCtx, {
        type: 'bar',
        data: {
            labels: allMonths,
            datasets: [{
                label: 'التسجيلات',
                data: allMonths.map(month => (studentsData[month] || 0) + (teachersData[month] || 0)),
                backgroundColor: 'rgba(0, 113, 170, 0.8)',
                borderColor: '#0071AA',
                borderWidth: 1,
                borderRadius: 5
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
});
</script>
@endsection
