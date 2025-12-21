@extends('layouts.dashboard')

@section('title', 'تفاصيل الدورة')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تفاصيل الدورة</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">عرض معلومات الدورة والطلاب المسجلين</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.courses.edit', $course) }}"
               class="flex items-center gap-2 rounded-lg bg-yellow-500 px-6 py-3 text-sm font-medium text-white hover:bg-yellow-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>تعديل</span>
            </a>
            <a href="{{ route('admin.courses.index') }}"
               class="flex items-center gap-2 rounded-lg bg-gray-500 px-6 py-3 text-sm font-medium text-white hover:bg-gray-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>رجوع</span>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Course Information -->
    <div class="lg:col-span-2">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="mb-6 text-xl font-bold text-gray-900 dark:text-white">معلومات الدورة</h2>

            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">اسم الدورة</label>
                    <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $course->title }}</p>
                </div>

                @if($course->description)
                <div>
                    <label class="text-sm font-medium text-gray-500 dark:text-gray-400">الوصف</label>
                    <p class="mt-1 text-base text-gray-700 dark:text-gray-300">{{ $course->description }}</p>
                </div>
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">الحالة</label>
                        @php
                            $statusColors = [
                                'active' => 'bg-success-50 text-success-600 dark:bg-success-900 dark:text-success-200',
                                'draft' => 'bg-gray-50 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                'inactive' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-200',
                                'archived' => 'bg-error-50 text-error-600 dark:bg-error-900 dark:text-error-200',
                            ];
                        @endphp
                        <div class="mt-1">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium {{ $statusColors[$course->status] ?? 'bg-gray-50 text-gray-600' }}">
                                {{ $course->status_display }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">الحد الأقصى للطلاب</label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">{{ $course->max_students ?? 'غير محدد' }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">تاريخ البداية</label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">
                            {{ $course->start_date ? $course->start_date->format('Y-m-d') : '-' }}
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">تاريخ النهاية</label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">
                            {{ $course->end_date ? $course->end_date->format('Y-m-d') : '-' }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">تاريخ الإنشاء</label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">
                            {{ $course->created_at->format('Y-m-d H:i') }}
                        </p>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-500 dark:text-gray-400">آخر تحديث</label>
                        <p class="mt-1 text-base text-gray-900 dark:text-white">
                            {{ $course->updated_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrolled Students -->
        <div class="mt-6 rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="border-b border-gray-200 p-6 dark:border-gray-800">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    الطلاب المسجلين ({{ $course->students->count() }})
                </h2>
            </div>

            @if($course->students->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800">
                            <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">#</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">اسم الطالب</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">البريد الإلكتروني</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">الحالة</th>
                            <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">تاريخ التسجيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        @foreach($course->students as $student)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=6366f1&color=fff"
                                         alt="{{ $student->name }}"
                                         class="h-8 w-8 rounded-full" />
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $student->email }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $enrollmentStatusColors = [
                                        'active' => 'bg-success-50 text-success-600 dark:bg-success-900 dark:text-success-200',
                                        'completed' => 'bg-blue-50 text-blue-600 dark:bg-blue-900 dark:text-blue-200',
                                        'dropped' => 'bg-error-50 text-error-600 dark:bg-error-900 dark:text-error-200',
                                    ];
                                @endphp
                                <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium {{ $enrollmentStatusColors[$student->pivot->status] ?? 'bg-gray-50 text-gray-600' }}">
                                    {{ $student->pivot->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $student->pivot->enrolled_at ? \Carbon\Carbon::parse($student->pivot->enrolled_at)->format('Y-m-d') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <p class="mt-2 text-gray-500 dark:text-gray-400">لا يوجد طلاب مسجلين في هذه الدورة</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Teacher Information -->
    <div class="lg:col-span-1">
        <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="mb-6 text-xl font-bold text-gray-900 dark:text-white">معلومات المعلم</h2>

            @if($course->teacher)
            <div class="space-y-4">
                <div class="flex flex-col items-center">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($course->teacher->name) }}&background=6366f1&color=fff&size=128"
                         alt="{{ $course->teacher->name }}"
                         class="h-24 w-24 rounded-full" />
                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">{{ $course->teacher->name }}</h3>
                </div>

                <div class="border-t border-gray-200 pt-4 dark:border-gray-800">
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">البريد الإلكتروني</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white break-all">{{ $course->teacher->email }}</p>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-500 dark:text-gray-400">الدور</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $course->teacher->role }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="text-center text-gray-500 dark:text-gray-400">
                <p>لا يوجد معلم معين لهذه الدورة</p>
            </div>
            @endif
        </div>

        <!-- Quick Stats -->
        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <h2 class="mb-6 text-xl font-bold text-gray-900 dark:text-white">إحصائيات سريعة</h2>

            <div class="space-y-4">
                <div class="flex items-center justify-between rounded-lg bg-blue-50 p-4 dark:bg-blue-900/20">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">عدد الطلاب</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $course->students->count() }}</p>
                    </div>
                    <div class="rounded-full bg-blue-100 p-3 dark:bg-blue-900">
                        <svg class="h-8 w-8 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <div class="flex items-center justify-between rounded-lg bg-green-50 p-4 dark:bg-green-900/20">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">الطلاب النشطون</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ $course->students->where('pivot.status', 'active')->count() }}
                        </p>
                    </div>
                    <div class="rounded-full bg-green-100 p-3 dark:bg-green-900">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                @if($course->max_students)
                <div class="flex items-center justify-between rounded-lg bg-purple-50 p-4 dark:bg-purple-900/20">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">نسبة الامتلاء</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">
                            {{ round(($course->students->count() / $course->max_students) * 100) }}%
                        </p>
                    </div>
                    <div class="rounded-full bg-purple-100 p-3 dark:bg-purple-900">
                        <svg class="h-8 w-8 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
