@extends('layouts.dashboard')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">التقارير والإحصائيات</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">عرض التقارير التفصيلية والإحصائيات الشاملة للنظام</p>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <!-- Reports Section -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">التقارير المتاحة</h2>
        <div class="space-y-3">
            <a href="#" class="flex items-center justify-between rounded-lg border border-gray-200 p-4 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">تقرير المعلمين</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">إحصائيات شاملة عن المعلمين</p>
                    </div>
                </div>
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="#" class="flex items-center justify-between rounded-lg border border-gray-200 p-4 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-success-100 dark:bg-success-900">
                        <svg class="h-5 w-5 text-success-600 dark:text-success-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">تقرير الطلاب</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">إحصائيات شاملة عن الطلاب</p>
                    </div>
                </div>
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>

            <a href="#" class="flex items-center justify-between rounded-lg border border-gray-200 p-4 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-warning-100 dark:bg-warning-900">
                        <svg class="h-5 w-5 text-warning-600 dark:text-warning-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">تقرير الدورات</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">إحصائيات شاملة عن الدورات</p>
                    </div>
                </div>
                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">إحصائيات سريعة</h2>
        <div class="space-y-4">
            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                <p class="text-sm text-gray-500 dark:text-gray-400">معدل الحضور</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">95%</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                <p class="text-sm text-gray-500 dark:text-gray-400">معدل إتمام الدورات</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">87%</p>
            </div>
            <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-800">
                <p class="text-sm text-gray-500 dark:text-gray-400">رضا الطلاب</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">4.8/5</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
    <div class="text-center py-12">
        <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <p class="mt-4 text-gray-500 dark:text-gray-400">التقارير التفصيلية قيد التطوير</p>
    </div>
</div>
@endsection
