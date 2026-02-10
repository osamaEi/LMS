@extends('layouts.dashboard')

@section('title', __('Reports'))

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Header -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-3">
                <span class="flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-blue-600 shadow-lg">
                    <svg class="fill-white" width="28" height="28" viewBox="0 0 24 24">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                    </svg>
                </span>
                <div>
                    <span class="block">{{ __('NELC Reports') }}</span>
                    <span class="block text-sm font-normal text-gray-500">Comprehensive Analytics & Compliance Dashboard</span>
                </div>
            </h2>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.activity-logs.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg border border-stroke py-2 px-4 text-center font-medium hover:bg-gray-100 dark:border-strokedark dark:hover:bg-meta-4 transition">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                </svg>
                Activity Logs
            </a>
            <a href="{{ route('admin.xapi.index') }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg border border-stroke py-2 px-4 text-center font-medium hover:bg-gray-100 dark:border-strokedark dark:hover:bg-meta-4 transition">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" fill="currentColor"/>
                </svg>
                xAPI Dashboard
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <!-- Total Students -->
        <div class="group relative overflow-hidden rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 h-24 w-24 -mr-6 -mt-6 rounded-full bg-primary opacity-5 group-hover:opacity-10 transition"></div>
            <div class="p-5">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary bg-opacity-10">
                        <svg class="fill-primary" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                        </svg>
                    </div>
                </div>
                <h4 class="text-2xl font-bold text-black dark:text-white">{{ number_format($stats['total_students']) }}</h4>
                <p class="text-xs text-gray-500 mt-1">Students</p>
            </div>
        </div>

        <!-- Total Programs -->
        <div class="group relative overflow-hidden rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 h-24 w-24 -mr-6 -mt-6 rounded-full bg-success opacity-5 group-hover:opacity-10 transition"></div>
            <div class="p-5">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-success bg-opacity-10">
                        <svg class="fill-success" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82zM12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                        </svg>
                    </div>
                </div>
                <h4 class="text-2xl font-bold text-black dark:text-white">{{ number_format($stats['total_programs']) }}</h4>
                <p class="text-xs text-gray-500 mt-1">Programs</p>
            </div>
        </div>

        <!-- Total Sessions -->
        <div class="group relative overflow-hidden rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 h-24 w-24 -mr-6 -mt-6 rounded-full bg-warning opacity-5 group-hover:opacity-10 transition"></div>
            <div class="p-5">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-warning bg-opacity-10">
                        <svg class="fill-warning" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    </div>
                </div>
                <h4 class="text-2xl font-bold text-black dark:text-white">{{ number_format($stats['total_sessions']) }}</h4>
                <p class="text-xs text-gray-500 mt-1">Sessions</p>
            </div>
        </div>

        <!-- Total Activities -->
        <div class="group relative overflow-hidden rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 h-24 w-24 -mr-6 -mt-6 rounded-full bg-meta-3 opacity-5 group-hover:opacity-10 transition"></div>
            <div class="p-5">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-meta-3 bg-opacity-10">
                        <svg class="fill-meta-3" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                </div>
                <h4 class="text-2xl font-bold text-black dark:text-white">{{ number_format($stats['total_activities']) }}</h4>
                <p class="text-xs text-gray-500 mt-1">Activities Logged</p>
            </div>
        </div>

        <!-- Active Enrollments -->
        <div class="group relative overflow-hidden rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 h-24 w-24 -mr-6 -mt-6 rounded-full bg-danger opacity-5 group-hover:opacity-10 transition"></div>
            <div class="p-5">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-danger bg-opacity-10">
                        <svg class="fill-danger" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                </div>
                <h4 class="text-2xl font-bold text-black dark:text-white">{{ number_format($stats['active_enrollments']) }}</h4>
                <p class="text-xs text-gray-500 mt-1">Enrollments</p>
            </div>
        </div>

        <!-- Pending xAPI -->
        <div class="group relative overflow-hidden rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-xl transition-all duration-300">
            <div class="absolute top-0 right-0 h-24 w-24 -mr-6 -mt-6 rounded-full bg-meta-5 opacity-5 group-hover:opacity-10 transition"></div>
            <div class="p-5">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-meta-5 bg-opacity-10">
                        <svg class="fill-meta-5" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                    </div>
                </div>
                <h4 class="text-2xl font-bold text-black dark:text-white">{{ number_format($stats['pending_xapi']) }}</h4>
                <p class="text-xs text-gray-500 mt-1">Pending xAPI</p>
            </div>
        </div>
    </div>

    <!-- Main Reports Grid -->
    <div class="mb-6">
        <h3 class="mb-4 text-lg font-semibold text-black dark:text-white flex items-center gap-2">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                <path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z" fill="currentColor"/>
            </svg>
            Available Reports
        </h3>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <!-- NELC Compliance Report -->
            <div class="group relative overflow-hidden rounded-xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 bg-gradient-to-br from-primary to-purple-600 opacity-10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="p-6">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-purple-600 shadow-lg">
                        <svg class="fill-white" width="28" height="28" viewBox="0 0 24 24">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-4.41 0-8-3.59-8-8V8.3l8-4 8 4V12c0 4.41-3.59 8-8 8z"/>
                            <path d="M10.5 13.5l-2.15-2.15L7 12.7l3.5 3.5 7-7-1.35-1.35z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-black dark:text-white">NELC Compliance</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Comprehensive compliance report for NELC accreditation including activity tracking, xAPI, accessibility, and all required metrics.
                    </p>
                    <div class="mb-4 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full bg-primary bg-opacity-10 px-2 py-1 text-xs font-medium text-primary">
                            🔐 Security
                        </span>
                        <span class="inline-flex items-center rounded-full bg-success bg-opacity-10 px-2 py-1 text-xs font-medium text-success">
                            📊 Analytics
                        </span>
                        <span class="inline-flex items-center rounded-full bg-warning bg-opacity-10 px-2 py-1 text-xs font-medium text-warning">
                            ♿ Accessibility
                        </span>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('admin.reports.nelc-compliance') }}"
                           class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-primary to-purple-600 py-3 px-4 text-center font-medium text-white hover:shadow-lg transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>
                            </svg>
                            View Full Report
                        </a>
                        <a href="{{ route('admin.reports.export', ['type' => 'nelc', 'format' => 'pdf']) }}"
                           class="flex w-full items-center justify-center gap-2 rounded-lg border border-primary py-3 px-4 text-center font-medium text-primary hover:bg-primary hover:text-white transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z" fill="currentColor"/>
                            </svg>
                            Export PDF
                        </a>
                    </div>
                </div>
            </div>

            <!-- Student Progress Report -->
            <div class="group relative overflow-hidden rounded-xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 bg-gradient-to-br from-success to-emerald-600 opacity-10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="p-6">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-success to-emerald-600 shadow-lg">
                        <svg class="fill-white" width="28" height="28" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-black dark:text-white">Student Progress</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Track enrollment, attendance, assessment scores, and overall student progress across all programs and subjects.
                    </p>
                    <div class="mb-4 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full bg-success bg-opacity-10 px-2 py-1 text-xs font-medium text-success">
                            📈 Progress Tracking
                        </span>
                        <span class="inline-flex items-center rounded-full bg-meta-3 bg-opacity-10 px-2 py-1 text-xs font-medium text-meta-3">
                            📊 Performance
                        </span>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('admin.reports.student-progress') }}"
                           class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-success to-emerald-600 py-3 px-4 text-center font-medium text-white hover:shadow-lg transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>
                            </svg>
                            View Report
                        </a>
                        <a href="{{ route('admin.reports.export', ['type' => 'student-progress', 'format' => 'csv']) }}"
                           class="flex w-full items-center justify-center gap-2 rounded-lg border border-success py-3 px-4 text-center font-medium text-success hover:bg-success hover:text-white transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z" fill="currentColor"/>
                            </svg>
                            Export CSV
                        </a>
                    </div>
                </div>
            </div>

            <!-- Attendance Report -->
            <div class="group relative overflow-hidden rounded-xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 bg-gradient-to-br from-warning to-orange-600 opacity-10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="p-6">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-warning to-orange-600 shadow-lg">
                        <svg class="fill-white" width="28" height="28" viewBox="0 0 24 24">
                            <path d="M9 11H7v2h2v-2zm4 0h-2v2h2v-2zm4 0h-2v2h2v-2zm2-7h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-black dark:text-white">Attendance Report</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Detailed attendance records for live sessions and recorded video views across all programs with timestamps.
                    </p>
                    <div class="mb-4 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full bg-warning bg-opacity-10 px-2 py-1 text-xs font-medium text-warning">
                            ✅ Attendance
                        </span>
                        <span class="inline-flex items-center rounded-full bg-danger bg-opacity-10 px-2 py-1 text-xs font-medium text-danger">
                            📹 Live & Recorded
                        </span>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('admin.reports.attendance') }}"
                           class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-warning to-orange-600 py-3 px-4 text-center font-medium text-white hover:shadow-lg transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>
                            </svg>
                            View Report
                        </a>
                        <a href="{{ route('admin.reports.export', ['type' => 'attendance', 'format' => 'csv']) }}"
                           class="flex w-full items-center justify-center gap-2 rounded-lg border border-warning py-3 px-4 text-center font-medium text-warning hover:bg-warning hover:text-white transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z" fill="currentColor"/>
                            </svg>
                            Export CSV
                        </a>
                    </div>
                </div>
            </div>

            <!-- Grades Report -->
            <div class="group relative overflow-hidden rounded-xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 bg-gradient-to-br from-meta-3 to-blue-600 opacity-10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="p-6">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-meta-3 to-blue-600 shadow-lg">
                        <svg class="fill-white" width="28" height="28" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-black dark:text-white">Grades & Assessment</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Comprehensive overview of student grades, quiz scores, assessment performance, and grading analytics.
                    </p>
                    <div class="mb-4 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full bg-meta-3 bg-opacity-10 px-2 py-1 text-xs font-medium text-meta-3">
                            📝 Assessments
                        </span>
                        <span class="inline-flex items-center rounded-full bg-success bg-opacity-10 px-2 py-1 text-xs font-medium text-success">
                            🏆 Grades
                        </span>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('admin.reports.grades') }}"
                           class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-meta-3 to-blue-600 py-3 px-4 text-center font-medium text-white hover:shadow-lg transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>
                            </svg>
                            View Report
                        </a>
                        <a href="{{ route('admin.reports.export', ['type' => 'grades', 'format' => 'csv']) }}"
                           class="flex w-full items-center justify-center gap-2 rounded-lg border border-meta-3 py-3 px-4 text-center font-medium text-meta-3 hover:bg-meta-3 hover:text-white transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z" fill="currentColor"/>
                            </svg>
                            Export CSV
                        </a>
                    </div>
                </div>
            </div>

            <!-- Teacher Performance -->
            <div class="group relative overflow-hidden rounded-xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 bg-gradient-to-br from-danger to-pink-600 opacity-10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="p-6">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-danger to-pink-600 shadow-lg">
                        <svg class="fill-white" width="28" height="28" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-black dark:text-white">Teacher Performance</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Evaluate teacher ratings, student feedback, teaching effectiveness metrics, and performance analytics.
                    </p>
                    <div class="mb-4 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full bg-danger bg-opacity-10 px-2 py-1 text-xs font-medium text-danger">
                            ⭐ Ratings
                        </span>
                        <span class="inline-flex items-center rounded-full bg-warning bg-opacity-10 px-2 py-1 text-xs font-medium text-warning">
                            💬 Feedback
                        </span>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('admin.reports.teacher-performance') }}"
                           class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-danger to-pink-600 py-3 px-4 text-center font-medium text-white hover:shadow-lg transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>
                            </svg>
                            View Report
                        </a>
                        <a href="{{ route('admin.reports.export', ['type' => 'teacher-performance', 'format' => 'csv']) }}"
                           class="flex w-full items-center justify-center gap-2 rounded-lg border border-danger py-3 px-4 text-center font-medium text-danger hover:bg-danger hover:text-white transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z" fill="currentColor"/>
                            </svg>
                            Export CSV
                        </a>
                    </div>
                </div>
            </div>

            <!-- Activity Logs -->
            <div class="group relative overflow-hidden rounded-xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                <div class="absolute top-0 right-0 w-32 h-32 -mr-8 -mt-8 bg-gradient-to-br from-meta-5 to-indigo-600 opacity-10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
                <div class="p-6">
                    <div class="mb-4 flex h-14 w-14 items-center justify-center rounded-xl bg-gradient-to-br from-meta-5 to-indigo-600 shadow-lg">
                        <svg class="fill-white" width="28" height="28" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-black dark:text-white">Activity Logs</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        Comprehensive activity tracking including logins, content access, assessments, and xAPI statements.
                    </p>
                    <div class="mb-4 flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full bg-meta-5 bg-opacity-10 px-2 py-1 text-xs font-medium text-meta-5">
                            📝 Tracking
                        </span>
                        <span class="inline-flex items-center rounded-full bg-primary bg-opacity-10 px-2 py-1 text-xs font-medium text-primary">
                            🔒 Audit Trail
                        </span>
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('admin.activity-logs.index') }}"
                           class="flex w-full items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-meta-5 to-indigo-600 py-3 px-4 text-center font-medium text-white hover:shadow-lg transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>
                            </svg>
                            View Logs
                        </a>
                        <a href="{{ route('admin.activity-logs.stats') }}"
                           class="flex w-full items-center justify-center gap-2 rounded-lg border border-meta-5 py-3 px-4 text-center font-medium text-meta-5 hover:bg-meta-5 hover:text-white transition-all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" fill="currentColor"/>
                            </svg>
                            View Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Export Section -->
    <div class="rounded-xl border border-stroke bg-gradient-to-br from-primary/5 to-purple-600/5 shadow-default dark:border-strokedark overflow-hidden">
        <div class="border-b border-stroke bg-white px-6 py-4 dark:border-strokedark dark:bg-boxdark">
            <h3 class="font-semibold text-black dark:text-white flex items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z" fill="currentColor"/>
                </svg>
                {{ __('Quick Export') }} / تصدير سريع
            </h3>
        </div>
        <div class="p-6 bg-white dark:bg-boxdark">
            <p class="text-sm mb-4 text-gray-600 dark:text-gray-400">
                Generate comprehensive reports in various formats for NELC submission and internal analysis.
            </p>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <a href="{{ route('admin.reports.export', ['type' => 'all', 'format' => 'pdf']) }}"
                   class="group flex items-center justify-center gap-2 rounded-lg border-2 border-danger bg-white py-4 px-6 text-center font-medium text-danger hover:bg-danger hover:text-white transition-all hover:shadow-lg">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" class="group-hover:scale-110 transition-transform">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zM6 20V4h7v5h5v11H6z" fill="currentColor"/>
                    </svg>
                    <span>📄 Export All (PDF)</span>
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'all', 'format' => 'csv']) }}"
                   class="group flex items-center justify-center gap-2 rounded-lg border-2 border-success bg-white py-4 px-6 text-center font-medium text-success hover:bg-success hover:text-white transition-all hover:shadow-lg">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" class="group-hover:scale-110 transition-transform">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" fill="currentColor"/>
                    </svg>
                    <span>📊 Export All (CSV)</span>
                </a>
                <a href="{{ route('admin.reports.export', ['type' => 'all', 'format' => 'json']) }}"
                   class="group flex items-center justify-center gap-2 rounded-lg border-2 border-meta-3 bg-white py-4 px-6 text-center font-medium text-meta-3 hover:bg-meta-3 hover:text-white transition-all hover:shadow-lg">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" class="group-hover:scale-110 transition-transform">
                        <path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V6h5.17l2 2H20v10z" fill="currentColor"/>
                    </svg>
                    <span>📋 Export All (JSON)</span>
                </a>
            </div>
        </div>
    </div>

    <!-- NELC Info Card -->
    <div class="mt-6 rounded-xl border-l-4 border-primary bg-gradient-to-r from-primary/10 to-purple-600/10 p-6">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl bg-primary shadow-lg">
                <svg class="fill-white" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M11 17h2v-6h-2v6zm1-15C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zM11 9h2V7h-2v2z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h4 class="mb-2 text-lg font-semibold text-black dark:text-white">
                    📌 About NELC Reports & Compliance
                </h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    These reports are designed to meet <strong>NELC (National eLearning Center)</strong> compliance standards for official accreditation in Saudi Arabia.
                    All data includes bilingual support (Arabic/English), comprehensive activity tracking, xAPI integration, and accessibility metrics
                    required for <strong>FutureX platform</strong> integration.
                </p>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-primary border border-primary/20">
                        ✓ xAPI Compliance
                    </span>
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-success border border-success/20">
                        ✓ Activity Tracking
                    </span>
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-warning border border-warning/20">
                        ✓ Accessibility Standards
                    </span>
                    <span class="inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-danger border border-danger/20">
                        ✓ Bilingual Support
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.group:hover .animate-float {
    animation: float 3s ease-in-out infinite;
}
</style>
@endsection
