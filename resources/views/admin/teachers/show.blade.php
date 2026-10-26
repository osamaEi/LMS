@extends('layouts.dashboard')

@section('title', 'الملف الشخصي لل مدرب ')

@section('content')
@if(session('success'))
<div class="mb-4 flex items-center gap-2 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-700">
    {{ session('success') }}
</div>
@endif

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
                <p class="text-gray-600 dark:text-gray-400">{{ $teacher->getRoleDisplayName() }}{{ $teacher->specialization ? ' - ' . $teacher->specialization : '' }}</p>
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
                @if($teacher->certificate_url)
                <a href="{{ $teacher->certificate_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm dark:text-white">عرض الشهادة</a>
                @endif
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
                @if($teacher->nationality)
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">الجنسية</label>
                    <p class="text-black dark:text-white">{{ $teacher->nationality }}</p>
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
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">نوع المؤهل</label>
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
                    <span class="text-xl font-bold text-brand-600 dark:text-brand-400">{{ $diplomaSubjects->count() }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="text-gray-700 dark:text-gray-300">عدد الدورات</span>
                    <span class="text-xl font-bold text-green-600 dark:text-green-400">{{ $courses->count() }}</span>
                </div>
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-700 pb-3">
                    <span class="text-gray-700 dark:text-gray-300">عدد المجموعات</span>
                    <span class="text-xl font-bold text-amber-600 dark:text-amber-400">{{ $teacher->teachingClasses->count() }}</span>
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
                </div>
        </div>

    </div>
</div>

<!-- Assignment panels: subjects, courses and classes are edited here -->
<div class="mt-6 rounded-lg bg-white shadow dark:bg-gray-800" x-data="{ panel: null }">
    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 p-6 dark:border-gray-700">
        <div>
            <h4 class="text-xl font-bold text-black dark:text-white">التعيينات</h4>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                ✓ محدد = معيّن له · إزالة العلامة = إلغاء التعيين عند الحفظ
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" @click="panel = panel === 'subjects' ? null : 'subjects'"
                    :class="panel === 'subjects' ? 'bg-sky-600 text-white' : 'bg-sky-50 text-sky-700'"
                    class="rounded-lg px-4 py-2 text-sm font-bold transition-colors">
                تعديل المقررات
            </button>
            <button type="button" @click="panel = panel === 'programs' ? null : 'programs'"
                    :class="panel === 'programs' ? 'bg-green-600 text-white' : 'bg-green-50 text-green-700'"
                    class="rounded-lg px-4 py-2 text-sm font-bold transition-colors">
                تعديل الدورات
            </button>
            <button type="button" @click="panel = panel === 'classes' ? null : 'classes'"
                    :class="panel === 'classes' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700'"
                    class="rounded-lg px-4 py-2 text-sm font-bold transition-colors">
                تعديل المجموعات
            </button>
        </div>
    </div>

    {{-- Subjects --}}
    <div x-show="panel === 'subjects'" x-cloak class="border-b border-gray-200 p-6 dark:border-gray-700">
        <form method="POST" action="{{ route('admin.teachers.assign-subjects', $teacher) }}"
              x-data="assignPanel({{ $assignedSubjectIds->toJson() }}, 'subjects[]')" @submit="return confirmRemoval($event, 'مقرر')">
            @csrf
            <div class="mb-3 flex flex-wrap items-center gap-3">
                <input type="search" placeholder="ابحث في المقررات…" x-model="query"
                       class="w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm dark:bg-gray-900 dark:text-white">
                <span class="text-sm font-semibold" :class="removedCount ? 'text-amber-600' : 'text-gray-500'" x-text="summary()"></span>
            </div>

            <div class="max-h-96 overflow-y-auto rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                @foreach($programsByType as $type => $programs)
                @foreach($programs as $prog)
                @php $subjects = $prog->terms->flatMap->subjects->merge($prog->subjects)->unique('id')->sortBy('name_ar'); @endphp
                @if($subjects->count())
                <div class="mb-3" x-show="groupVisible($el)">
                    <p class="mb-1 text-xs font-bold uppercase tracking-wide text-gray-400">{{ $prog->name_ar }}</p>
                    @foreach($subjects as $subj)
                    <label class="flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700"
                           x-show="matches('{{ \Illuminate\Support\Str::of($subj->name_ar . ' ' . $subj->code . ' ' . $subj->name_en)->replace("'", '') }}')">
                        <input type="checkbox" name="subjects[]" value="{{ $subj->id }}"
                               :checked="selected.includes({{ $subj->id }})" @change="toggle({{ $subj->id }}, $event)"
                               class="h-4 w-4 rounded border-gray-300 text-sky-600">
                        <span class="text-sm text-gray-800 dark:text-gray-100">{{ $subj->name_ar }}</span>
                        @if($subj->code)<span class="text-xs text-gray-400">{{ $subj->code }}</span>@endif
                    </label>
                    @endforeach
                </div>
                @endif
                @endforeach
                @endforeach

                @if($orphanSubjects->count())
                <div class="mb-3">
                    <p class="mb-1 text-xs font-bold uppercase tracking-wide text-gray-400">مقررات غير مرتبطة ببرنامج</p>
                    @foreach($orphanSubjects as $subj)
                    <label class="flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700"
                           x-show="matches('{{ \Illuminate\Support\Str::of($subj->name_ar . ' ' . $subj->code)->replace("'", '') }}')">
                        <input type="checkbox" name="subjects[]" value="{{ $subj->id }}"
                               :checked="selected.includes({{ $subj->id }})" @change="toggle({{ $subj->id }}, $event)"
                               class="h-4 w-4 rounded border-gray-300 text-sky-600">
                        <span class="text-sm text-gray-800 dark:text-gray-100">{{ $subj->name_ar }}</span>
                    </label>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="mt-4 flex gap-2">
                <button type="submit" class="rounded-lg bg-sky-600 px-6 py-2 text-sm font-bold text-white">حفظ المقررات</button>
                <button type="button" @click="panel = null" class="rounded-lg border border-gray-300 px-6 py-2 text-sm font-bold text-gray-700 dark:text-gray-200">إلغاء</button>
            </div>
        </form>
    </div>

    {{-- Programs / courses --}}
    <div x-show="panel === 'programs'" x-cloak class="border-b border-gray-200 p-6 dark:border-gray-700">
        <form method="POST" action="{{ route('admin.teachers.assign-programs', $teacher) }}"
              x-data="assignPanel({{ $assignedProgramIds->toJson() }}, 'program_ids[]')" @submit="return confirmRemoval($event, 'دورة')">
            @csrf
            <div class="mb-3 flex flex-wrap items-center gap-3">
                <input type="search" placeholder="ابحث في الدورات…" x-model="query"
                       class="w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm dark:bg-gray-900 dark:text-white">
                <span class="text-sm font-semibold" :class="removedCount ? 'text-amber-600' : 'text-gray-500'" x-text="summary()"></span>
            </div>

            <div class="max-h-96 overflow-y-auto rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                @forelse($allPrograms as $type => $programs)
                <div class="mb-3">
                    <p class="mb-1 text-xs font-bold uppercase tracking-wide text-gray-400">
                        {{ ['training'=>'البرامج التدريبية','english'=>'دورات اللغة الإنجليزية','course'=>'الدورات التأهيلية'][$type] ?? $type }}
                    </p>
                    @foreach($programs as $prog)
                    <label class="flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700"
                           x-show="matches('{{ \Illuminate\Support\Str::of($prog->name_ar)->replace("'", '') }}')">
                        <input type="checkbox" name="program_ids[]" value="{{ $prog->id }}"
                               :checked="selected.includes({{ $prog->id }})" @change="toggle({{ $prog->id }}, $event)"
                               class="h-4 w-4 rounded border-gray-300 text-green-600">
                        <span class="text-sm text-gray-800 dark:text-gray-100">{{ $prog->name_ar }}</span>
                    </label>
                    @endforeach
                </div>
                @empty
                <p class="py-6 text-center text-sm text-gray-400">لا توجد دورات</p>
                @endforelse
            </div>

            <div class="mt-4 flex gap-2">
                <button type="submit" class="rounded-lg bg-green-600 px-6 py-2 text-sm font-bold text-white">حفظ الدورات</button>
                <button type="button" @click="panel = null" class="rounded-lg border border-gray-300 px-6 py-2 text-sm font-bold text-gray-700 dark:text-gray-200">إلغاء</button>
            </div>
        </form>
    </div>

    {{-- Classes --}}
    <div x-show="panel === 'classes'" x-cloak class="border-b border-gray-200 p-6 dark:border-gray-700">
        <form method="POST" action="{{ route('admin.teachers.assign-classes', $teacher) }}"
              x-data="assignPanel({{ $assignedClassIds->toJson() }}, 'class_ids[]')" @submit="return confirmRemoval($event, 'مجموعة')">
            @csrf
            <div class="mb-3 flex flex-wrap items-center gap-3">
                <input type="search" placeholder="ابحث في المجموعات…" x-model="query"
                       class="w-full max-w-sm rounded-lg border border-gray-300 px-3 py-2 text-sm dark:bg-gray-900 dark:text-white">
                <span class="text-sm font-semibold" :class="removedCount ? 'text-amber-600' : 'text-gray-500'" x-text="summary()"></span>
            </div>

            <p class="mb-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-700">
                لكل مجموعة مدرب واحد — تعيين مجموعة مسندة لمدرب آخر سينقلها إلى هذا المدرب.
            </p>

            <div class="max-h-96 overflow-y-auto rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                @forelse($allClasses as $programName => $classes)
                <div class="mb-3">
                    <p class="mb-1 text-xs font-bold uppercase tracking-wide text-gray-400">{{ $programName }}</p>
                    @foreach($classes as $class)
                    @php $heldByOther = $class->teacher_id && $class->teacher_id !== $teacher->id; @endphp
                    <label class="flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700"
                           x-show="matches('{{ \Illuminate\Support\Str::of($class->name)->replace("'", '') }}')">
                        <input type="checkbox" name="class_ids[]" value="{{ $class->id }}"
                               :checked="selected.includes({{ $class->id }})" @change="toggle({{ $class->id }}, $event)"
                               class="h-4 w-4 rounded border-gray-300 text-amber-600">
                        <span class="text-sm text-gray-800 dark:text-gray-100">{{ $class->name }}</span>
                        @if($heldByOther)
                        <span class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-600">مسندة لمدرب آخر</span>
                        @endif
                    </label>
                    @endforeach
                </div>
                @empty
                <p class="py-6 text-center text-sm text-gray-400">لا توجد مجموعات</p>
                @endforelse
            </div>

            <div class="mt-4 flex gap-2">
                <button type="submit" class="rounded-lg bg-amber-600 px-6 py-2 text-sm font-bold text-white">حفظ المجموعات</button>
                <button type="button" @click="panel = null" class="rounded-lg border border-gray-300 px-6 py-2 text-sm font-bold text-gray-700 dark:text-gray-200">إلغاء</button>
            </div>
        </form>
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
            المقررات
            <span class="rounded-full bg-purple-100 px-2 py-0.5 text-xs font-semibold text-purple-700 dark:bg-purple-900 dark:text-purple-200">
                {{ $diplomaSubjects->count() }}
            </span>
        </button>
        <button @click="tab = 'classes'"
                :class="tab === 'classes' ? 'border-b-2 border-brand-500 text-brand-600 dark:text-brand-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'"
                class="flex items-center gap-2 px-6 py-4 text-sm font-medium transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            المجموعات
            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-900 dark:text-amber-200">
                {{ $teacher->teachingClasses->count() }}
            </span>
        </button>
    </div>

    <!-- Tab: Classes -->
    <div x-show="tab === 'classes'" x-cloak class="p-6">
        @if($teacher->teachingClasses->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-gray-400 dark:text-gray-500">
                <p class="text-sm">لا توجد مجموعات معيّنة لهذا المدرب</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($teacher->teachingClasses as $class)
                <a href="{{ url('/admin/classes/' . $class->id) }}"
                   class="flex items-start gap-3 rounded-lg border border-gray-200 p-4 transition-colors hover:border-amber-400 dark:border-gray-700">
                    <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900">
                        <svg class="h-4 w-4 text-amber-600 dark:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-medium text-black dark:text-white">{{ $class->name }}</p>
                        @if($class->program)
                        <p class="mt-1 truncate text-xs text-amber-600 dark:text-amber-400">{{ $class->program->name_ar }}</p>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        @endif
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
                <p class="text-sm">لا توجد مقررات معيّنة لهذا المدرب</p>
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

@push('scripts')
<script>
// Shared behaviour for the three assign panels: live search, a running
// "+added / −removed" summary, and a confirm before anything is unassigned.
function assignPanel(initialIds, fieldName) {
    return {
        initial: initialIds.map(Number),
        selected: initialIds.map(Number),
        query: '',
        fieldName,

        // أ/إ/آ ≡ ا, ة ≡ ه, ى ≡ ي so search matches how admins type.
        normalize(value) {
            return (value || '').toLowerCase()
                .replace(/[ـً-ْ]/g, '')
                .replace(/[آأإٱ]/g, 'ا')
                .replace(/ى/g, 'ي')
                .replace(/ة/g, 'ه')
                .replace(/\s+/g, ' ')
                .trim();
        },

        matches(haystack) {
            const q = this.normalize(this.query);
            if (!q) return true;
            const hay = this.normalize(haystack);
            return q.split(' ').filter(Boolean).every(term => hay.includes(term));
        },

        groupVisible(el) {
            return !this.query || el.querySelectorAll('label:not([style*="display: none"])').length > 0;
        },

        toggle(id, event) {
            if (event.target.checked) {
                if (!this.selected.includes(id)) this.selected.push(id);
            } else {
                this.selected = this.selected.filter(x => x !== id);
            }
        },

        get addedCount()   { return this.selected.filter(id => !this.initial.includes(id)).length; },
        get removedCount() { return this.initial.filter(id => !this.selected.includes(id)).length; },

        summary() {
            const parts = [this.selected.length + ' محدد'];
            if (this.addedCount)   parts.push('+' + this.addedCount + ' إضافة');
            if (this.removedCount) parts.push('−' + this.removedCount + ' إلغاء');
            return parts.join(' · ');
        },

        confirmRemoval(event, noun) {
            if (this.removedCount
                && !confirm('سيتم إلغاء تعيين ' + this.removedCount + ' ' + noun + ' من هذا المدرب. هل تريد المتابعة؟')) {
                event.preventDefault();
                return false;
            }
            return true;
        },
    };
}
</script>
@endpush
@endsection
