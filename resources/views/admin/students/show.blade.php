@extends('layouts.dashboard')

@section('title', 'الملف الشخصي للطالب')

@section('content')
<!-- Profile Header Card -->
<div class="mb-6 overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
    <div class="relative h-35 bg-gradient-to-r from-success-500 to-success-600">
        <div class="absolute -bottom-16 right-8">
            <div class="h-32 w-32 overflow-hidden rounded-full border-4 border-white bg-white">
                @if($student->profile_photo)
                    <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->name }}" class="h-full w-full object-cover">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&size=200&background=10b981&color=fff" alt="{{ $student->name }}" class="h-full w-full object-cover">
                @endif
            </div>
        </div>
    </div>
    <div class="px-8 pb-6 pt-20">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="mb-1.5 text-2xl font-bold text-black dark:text-white">{{ $student->name }}</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    {{ $student->getRoleDisplayName() }}
                    @if($student->program)
                        - {{ $student->program->name }}
                    @endif
                    @if($student->track)
                        - {{ $student->track->name }}
                    @endif
                </p>
                <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $student->email }}
                    </span>
                    @if($student->phone)
                    <span class="flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $student->phone }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.students.edit', $student) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-6 py-2.5 text-center font-medium text-white hover:bg-brand-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    تعديل الملف الشخصي
                </a>
                <a href="{{ route('admin.students.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-100 px-6 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <!-- Left Column - Personal & Academic Info -->
    <div class="lg:col-span-2">
        <!-- Personal Information -->
        <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h4 class="mb-6 text-xl font-bold text-black dark:text-white">المعلومات الشخصية</h4>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">الاسم الكامل</label>
                    <p class="text-black dark:text-white">{{ $student->name }}</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">رقم الهوية الوطنية</label>
                    <p class="text-black dark:text-white">{{ $student->national_id ?? '-' }}</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">البريد الإلكتروني</label>
                    <p class="text-black dark:text-white">{{ $student->email }}</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">رقم الهاتف</label>
                    <p class="text-black dark:text-white">{{ $student->phone ?? '-' }}</p>
                </div>
                @if($student->date_of_birth)
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">تاريخ الميلاد</label>
                    <p class="text-black dark:text-white">{{ $student->date_of_birth->format('Y-m-d') }}</p>
                </div>
                @endif
                @if($student->gender)
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">الجنس</label>
                    <p class="text-black dark:text-white">{{ $student->gender === 'male' ? 'ذكر' : 'أنثى' }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Academic Information -->
        <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h4 class="mb-6 text-xl font-bold text-black dark:text-white">المعلومات الأكاديمية</h4>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">نوع التسجيل</label>
                    <p class="text-black dark:text-white">
                        @if($student->type === 'diploma')
                            دبلوم
                        @elseif($student->type === 'training')
                            تدريب
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">الحالة</label>
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium
                        @if($student->status === 'active') bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-200
                        @elseif($student->status === 'pending') bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-200
                        @elseif($student->status === 'suspended') bg-error-100 text-error-700 dark:bg-error-900 dark:text-error-200
                        @else bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-200
                        @endif">
                        {{ $student->getStatusDisplayName() }}
                    </span>
                </div>
                @if($student->program)
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">البرنامج</label>
                    <p class="text-black dark:text-white">{{ $student->program->name }}</p>
                    @if($student->program->code)
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $student->program->code }}</p>
                    @endif
                </div>
                @endif
                @if($student->track)
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">المسار</label>
                    <p class="text-black dark:text-white">{{ $student->track->name }}</p>
                    @if($student->track->code)
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $student->track->code }}</p>
                    @endif
                </div>
                @endif
                @if($student->current_term_number)
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">الفصل الدراسي الحالي</label>
                    <p class="text-black dark:text-white">الفصل {{ $student->current_term_number }}</p>
                </div>
                @endif
                @if($student->date_of_register)
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">تاريخ التسجيل</label>
                    <p class="text-black dark:text-white">{{ $student->date_of_register->format('Y-m-d') }}</p>
                </div>
                @endif
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">الانضمام منذ</label>
                    <p class="text-black dark:text-white">{{ $student->created_at->diffForHumans() }}</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">نظام الفصول</label>
                    <span class="inline-flex items-center gap-1">
                        @if($student->is_terms)
                            <svg class="h-5 w-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-black dark:text-white">مفعل</span>
                        @else
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-black dark:text-white">غير مفعل</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Enrolled Subjects/Courses -->
        @if($student->enrollments->count() > 0)
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h4 class="mb-6 text-xl font-bold text-black dark:text-white">المقررات المسجلة</h4>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-700">
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">المقرر</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">الحالة</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">الدرجة</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">التقدير</th>
                            <th class="px-4 py-3 text-right text-sm font-medium text-gray-900 dark:text-white">التقدم</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($student->enrollments as $enrollment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm text-black dark:text-white">
                                @if($enrollment->subject)
                                    {{ $enrollment->subject->name }}
                                @elseif($enrollment->course)
                                    {{ $enrollment->course->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium
                                    @if($enrollment->status === 'active') bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($enrollment->status === 'completed') bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-200
                                    @elseif($enrollment->status === 'withdrawn') bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-200
                                    @elseif($enrollment->status === 'failed') bg-error-100 text-error-700 dark:bg-error-900 dark:text-error-200
                                    @else bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-200
                                    @endif">
                                    @if($enrollment->status === 'active') نشط
                                    @elseif($enrollment->status === 'completed') مكتمل
                                    @elseif($enrollment->status === 'withdrawn') منسحب
                                    @elseif($enrollment->status === 'failed') راسب
                                    @else غير محدد
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-black dark:text-white">
                                {{ $enrollment->final_grade ?? '-' }}
                            </td>
                            <td class="px-4 py-3">
                                @if($enrollment->grade_letter)
                                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-bold
                                        @if(in_array($enrollment->grade_letter, ['A+', 'A'])) bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-200
                                        @elseif(in_array($enrollment->grade_letter, ['B+', 'B'])) bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200
                                        @elseif(in_array($enrollment->grade_letter, ['C+', 'C'])) bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-200
                                        @elseif(in_array($enrollment->grade_letter, ['D+', 'D'])) bg-orange-100 text-orange-700 dark:bg-orange-900 dark:text-orange-200
                                        @else bg-error-100 text-error-700 dark:bg-error-900 dark:text-error-200
                                        @endif">
                                        {{ $enrollment->grade_letter }}
                                    </span>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if($enrollment->progress)
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 flex-1 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                            <div class="h-full rounded-full bg-brand-500" style="width: {{ $enrollment->progress }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600 dark:text-gray-400">{{ $enrollment->progress }}%</span>
                                    </div>
                                @else
                                    <span class="text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- About Me -->
        @if($student->bio)
        <div class="mt-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h4 class="mb-4 text-xl font-bold text-black dark:text-white">نبذة عني</h4>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $student->bio }}</p>
        </div>
        @endif
    </div>

    <!-- Right Column - Stats & Quick Info -->
    <div class="lg:col-span-1">
        <!-- Stats -->
        <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h4 class="mb-6 text-xl font-bold text-black dark:text-white">الإحصائيات</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="text-gray-700 dark:text-gray-300">عدد المقررات</span>
                    <span class="text-xl font-bold text-brand-600 dark:text-brand-400">{{ $student->enrollments->count() }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="text-gray-700 dark:text-gray-300">المقررات النشطة</span>
                    <span class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $student->enrollments->where('status', 'active')->count() }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="text-gray-700 dark:text-gray-300">المقررات المكتملة</span>
                    <span class="text-xl font-bold text-success-600 dark:text-success-400">{{ $student->enrollments->where('status', 'completed')->count() }}</span>
                </div>
                @php
                    $completedEnrollments = $student->enrollments->where('status', 'completed')->where('final_grade', '!=', null);
                    $averageGrade = $completedEnrollments->count() > 0 ? $completedEnrollments->avg('final_grade') : 0;
                @endphp
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="text-gray-700 dark:text-gray-300">المعدل التراكمي</span>
                    <span class="text-xl font-bold text-brand-600 dark:text-brand-400">{{ $averageGrade > 0 ? number_format($averageGrade, 2) : '-' }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="text-gray-700 dark:text-gray-300">حالة التحقق من البريد</span>
                    @if($student->email_verified_at)
                        <span class="inline-flex rounded-full bg-success-100 px-2 py-1 text-xs font-medium text-success-700 dark:bg-success-900 dark:text-success-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-warning-100 px-2 py-1 text-xs font-medium text-warning-700 dark:bg-warning-900 dark:text-warning-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    @endif
                </div>
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="text-gray-700 dark:text-gray-300">حالة التحقق من الهاتف</span>
                    @if($student->phone_verified_at)
                        <span class="inline-flex rounded-full bg-success-100 px-2 py-1 text-xs font-medium text-success-700 dark:bg-success-900 dark:text-success-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-warning-100 px-2 py-1 text-xs font-medium text-warning-700 dark:bg-warning-900 dark:text-warning-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    @endif
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-700 dark:text-gray-300">حالة التحقق من نفاذ</span>
                    @if($student->nafath_verified_at)
                        <span class="inline-flex rounded-full bg-success-100 px-2 py-1 text-xs font-medium text-success-700 dark:bg-success-900 dark:text-success-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-warning-100 px-2 py-1 text-xs font-medium text-warning-700 dark:bg-warning-900 dark:text-warning-200">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Account Status -->
        <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h4 class="mb-6 text-xl font-bold text-black dark:text-white">حالة الحساب</h4>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700 dark:text-gray-300">تأكيد المستخدم</span>
                    @if($student->is_confirm_user)
                        <svg class="h-5 w-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @else
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700 dark:text-gray-300">اكتمال الملف الشخصي</span>
                    @if($student->profile_completed_at)
                        <svg class="h-5 w-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @else
                        <svg class="h-5 w-5 text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h4 class="mb-4 text-xl font-bold text-black dark:text-white">إجراءات سريعة</h4>
            <div class="space-y-2">
                <a href="{{ route('admin.students.edit', $student) }}" class="flex w-full items-center gap-2 rounded-lg bg-brand-50 px-4 py-3 text-sm font-medium text-brand-600 hover:bg-brand-100 dark:bg-brand-900 dark:text-brand-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    تعديل البيانات
                </a>
                <button class="flex w-full items-center gap-2 rounded-lg bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    إرسال رسالة
                </button>
                <button class="flex w-full items-center gap-2 rounded-lg bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    عرض السجل الأكاديمي
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
