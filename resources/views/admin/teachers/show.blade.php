@extends('layouts.dashboard')

@section('title', 'الملف الشخصي لل مدرب ')

@section('content')
<!-- Profile Header Card -->
<div class="mb-6 overflow-hidden rounded-lg bg-white shadow dark:bg-gray-800">
    <div class="relative h-35 bg-gradient-to-r from-brand-500 to-brand-600">
        <div class="absolute -bottom-16 right-8">
            <div class="h-32 w-32 overflow-hidden rounded-full border-4 border-white bg-white">
                @if($teacher->profile_photo)
                    <img src="{{ asset('storage/' . $teacher->profile_photo) }}" alt="{{ $teacher->name }}" class="h-full w-full object-cover">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&size=200&background=6366f1&color=fff" alt="{{ $teacher->name }}" class="h-full w-full object-cover">
                @endif
            </div>
        </div>
    </div>
    <div class="px-8 pb-6 pt-20">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h3 class="mb-1.5 text-2xl font-bold text-black dark:text-white">{{ $teacher->name }}</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $teacher->getRoleDisplayName() }} - {{ $teacher->specialization ?? 'غير محدد' }}</p>
                <div class="mt-2 flex flex-wrap items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $teacher->email }}
                    </span>
                    @if($teacher->phone)
                    <span class="flex items-center gap-1">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $teacher->phone }}
                    </span>
                    @endif
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.teachers.edit', $teacher) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-6 py-2.5 text-center font-medium text-white hover:bg-brand-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    تعديل الملف الشخصي
                </a>
                <a href="{{ route('admin.teachers.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-gray-100 px-6 py-2.5 text-center font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
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
    <!-- Left Column - Personal Info -->
    <div class="lg:col-span-2">
        <!-- Personal Information -->
        <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h4 class="mb-6 text-xl font-bold text-black dark:text-white">المعلومات الشخصية</h4>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">الاسم الكامل</label>
                    <p class="text-black dark:text-white">{{ $teacher->name }}</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">رقم الهوية الوطنية</label>
                    <p class="text-black dark:text-white">{{ $teacher->national_id ?? '-' }}</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">البريد الإلكتروني</label>
                    <p class="text-black dark:text-white">{{ $teacher->email }}</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">رقم الهاتف</label>
                    <p class="text-black dark:text-white">{{ $teacher->phone ?? '-' }}</p>
                </div>
                @if($teacher->date_of_birth)
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">تاريخ الميلاد</label>
                    <p class="text-black dark:text-white">{{ \Carbon\Carbon::parse($teacher->date_of_birth)->format('Y-m-d') }}</p>
                </div>
                @endif
                @if($teacher->gender)
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">الجنس</label>
                    <p class="text-black dark:text-white">{{ $teacher->gender === 'male' ? 'ذكر' : 'أنثى' }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Professional Info -->
        <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h4 class="mb-6 text-xl font-bold text-black dark:text-white">المعلومات المهنية</h4>
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">الوظيفة</label>
                    <p class="text-black dark:text-white">{{ $teacher->getRoleDisplayName() }}</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">الحالة</label>
                    <span class="inline-flex rounded-full px-3 py-1 text-sm font-medium
                        @if($teacher->status === 'active') bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-200
                        @elseif($teacher->status === 'pending') bg-warning-100 text-warning-700 dark:bg-warning-900 dark:text-warning-200
                        @elseif($teacher->status === 'suspended') bg-error-100 text-error-700 dark:bg-error-900 dark:text-error-200
                        @else bg-gray-100 text-gray-700 dark:bg-gray-900 dark:text-gray-200
                        @endif">
                        {{ $teacher->getStatusDisplayName() }}
                    </span>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">تاريخ التسجيل</label>
                    <p class="text-black dark:text-white">{{ $teacher->created_at->format('Y-m-d') }}</p>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">الانضمام منذ</label>
                    <p class="text-black dark:text-white">{{ $teacher->created_at->diffForHumans() }}</p>
                </div>
                @if($teacher->specialization)
                <div class="sm:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">التخصص</label>
                    <div class="flex flex-wrap gap-2">
                        <span class="rounded-full bg-brand-100 px-3 py-1 text-sm text-brand-700 dark:bg-brand-900 dark:text-brand-200">{{ $teacher->specialization }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- About Me -->
        @if($teacher->bio)
        <div class="rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h4 class="mb-4 text-xl font-bold text-black dark:text-white">نبذة عني</h4>
            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $teacher->bio }}</p>
        </div>
        @endif
    </div>

    <!-- Right Column - Stats & Activity -->
    <div class="lg:col-span-1">
        <!-- Stats -->
        <div class="mb-6 rounded-lg bg-white p-6 shadow dark:bg-gray-800">
            <h4 class="mb-6 text-xl font-bold text-black dark:text-white">الإحصائيات</h4>
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="text-gray-700 dark:text-gray-300">عدد المقررات </span>
                    <span class="text-xl font-bold text-brand-600 dark:text-brand-400">{{ $teacher->subjects->count() }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="text-gray-700 dark:text-gray-300">حالة التحقق من البريد</span>
                    @if($teacher->email_verified_at)
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
                    @if($teacher->phone_verified_at)
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
                    @if($teacher->nafath_verified_at)
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
                    <span class="text-gray-700 dark:text-gray-300">عدد الدورات المعيّنة</span>
                    <span class="text-xl font-bold text-green-600 dark:text-green-400">{{ $courses->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-700 dark:text-gray-300">مقررات الدبلومات</span>
                    <span class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ $diplomaSubjects->count() }}</span>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Courses & Diploma Subjects Tabs -->
<div class="mt-6 rounded-lg bg-white shadow dark:bg-gray-800" x-data="{ tab: 'courses' }">
    <!-- Tab Headers -->
    <div class="flex border-b border-gray-200 dark:border-gray-700">
        <button @click="tab = 'courses'"
                :class="tab === 'courses' ? 'border-b-2 border-brand-500 text-brand-600 dark:text-brand-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                class="flex items-center gap-2 px-6 py-4 text-sm font-medium transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            الدورات
            <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700 dark:bg-green-900 dark:text-green-200">
                {{ $courses->count() }}
            </span>
        </button>
        <button @click="tab = 'diploma'"
                :class="tab === 'diploma' ? 'border-b-2 border-brand-500 text-brand-600 dark:text-brand-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                class="flex items-center gap-2 px-6 py-4 text-sm font-medium transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            مقررات الدبلومات
            <span class="rounded-full bg-purple-100 px-2 py-0.5 text-xs font-semibold text-purple-700 dark:bg-purple-900 dark:text-purple-200">
                {{ $diplomaSubjects->count() }}
            </span>
        </button>
    </div>

    <!-- Tab: Courses -->
    <div x-show="tab === 'courses'" class="p-6">
        @if($courses->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-gray-400 dark:text-gray-500">
                <svg class="mb-3 h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <p class="text-sm">لا توجد دورات معيّنة لهذا المدرب</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($courses as $program)
                    @php
                        $typeConfig = match($program->type) {
                            'training' => ['label' => 'تدريبي',  'bg' => 'bg-blue-100',   'text' => 'text-blue-700',   'dark' => 'dark:bg-blue-900 dark:text-blue-200'],
                            'english'  => ['label' => 'إنجليزي', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-700', 'dark' => 'dark:bg-yellow-900 dark:text-yellow-200'],
                            'course'   => ['label' => 'دورة',    'bg' => 'bg-green-100',  'text' => 'text-green-700',  'dark' => 'dark:bg-green-900 dark:text-green-200'],
                            default    => ['label' => $program->type, 'bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'dark' => 'dark:bg-gray-700 dark:text-gray-200'],
                        };
                    @endphp
                    <div class="flex items-start gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900">
                            <svg class="h-4 w-4 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-black dark:text-white">{{ $program->name_ar }}</p>
                            <span class="mt-1 inline-block rounded-full px-2 py-0.5 text-xs font-medium {{ $typeConfig['bg'] }} {{ $typeConfig['text'] }} {{ $typeConfig['dark'] }}">
                                {{ $typeConfig['label'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Tab: Diploma Subjects -->
    <div x-show="tab === 'diploma'" class="p-6">
        @if($diplomaSubjects->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-gray-400 dark:text-gray-500">
                <svg class="mb-3 h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <p class="text-sm">لا توجد مقررات دبلومات معيّنة لهذا المدرب</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($diplomaSubjects as $subject)
                    @php $prog = $subject->program ?? $subject->term?->program; @endphp
                    <div class="flex items-start gap-3 rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-100 dark:bg-purple-900">
                            <svg class="h-4 w-4 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-medium text-black dark:text-white">{{ $subject->name_ar ?? $subject->name ?? '-' }}</p>
                            @if($subject->code)
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $subject->code }}</p>
                            @endif
                            @if($prog)
                                <p class="mt-1 truncate text-xs text-purple-600 dark:text-purple-400">{{ $prog->name_ar }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
