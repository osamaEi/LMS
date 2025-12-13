@extends('layouts.dashboard')

@section('title', 'إضافة درس جديد')

@push('styles')
<style>
    .session-type-card {
        transition: all 0.3s ease;
    }
    .session-type-card input:checked + label {
        border-color: rgb(99 102 241);
        background-color: rgb(238 242 255);
    }
    .dark .session-type-card input:checked + label {
        background-color: rgb(30 27 75);
    }
    .conditional-section {
        display: none;
        animation: fadeIn 0.3s ease;
    }
    .conditional-section.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.sessions.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إضافة درس جديد</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">أدخل بيانات الدرس الجديد مع محتوياته (فيديو، Zoom، ملفات)</p>
</div>

@if($errors->any())
<div class="mb-4 rounded-lg bg-error-50 p-4 dark:bg-error-900">
    <ul class="list-disc list-inside text-sm text-error-600 dark:text-error-200">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.sessions.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">المعلومات الأساسية</h2>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- المادة الدراسية -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    المادة الدراسية <span class="text-error-500">*</span>
                </label>
                <select name="subject_id"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">اختر المادة الدراسية</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id', request('subject_id')) == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }} ({{ $subject->code }}) - {{ $subject->term->name ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- عنوان الدرس -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    عنوان الدرس <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="title"
                       value="{{ old('title') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: مقدمة في البرمجة - الدرس الأول">
            </div>

            <!-- رقم الدرس -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رقم الدرس <span class="text-error-500">*</span>
                </label>
                <input type="number"
                       name="session_number"
                       value="{{ old('session_number') }}"
                       required
                       min="1"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="1">
            </div>

            <!-- نوع الدرس -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    نوع الدرس <span class="text-error-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Recorded Video -->
                    <div class="session-type-card">
                        <input type="radio" id="type_video" name="type" value="recorded_video"
                               {{ old('type', 'recorded_video') === 'recorded_video' ? 'checked' : '' }}
                               required class="sr-only peer">
                        <label for="type_video" class="flex flex-col items-center justify-center p-5 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                            <svg class="w-12 h-12 mb-2 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">فيديو مسجل</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">رفع فيديو أو رابط</span>
                        </label>
                    </div>

                    <!-- Live Zoom -->
                    <div class="session-type-card">
                        <input type="radio" id="type_zoom" name="type" value="live_zoom"
                               {{ old('type') === 'live_zoom' ? 'checked' : '' }}
                               required class="sr-only peer">
                        <label for="type_zoom" class="flex flex-col items-center justify-center p-5 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                            <svg class="w-12 h-12 mb-2 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M8 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                <path d="M12 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-8a2 2 0 0 1-2-2V4z"/>
                            </svg>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">Zoom مباشر</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">جلسة مباشرة أونلاين</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- تاريخ ووقت الدرس -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    تاريخ ووقت الدرس
                </label>
                <input type="datetime-local"
                       name="scheduled_at"
                       value="{{ old('scheduled_at') }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- المدة بالدقائق -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    المدة بالدقائق
                </label>
                <input type="number"
                       name="duration_minutes"
                       value="{{ old('duration_minutes') }}"
                       min="1"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="90">
            </div>

            <!-- الحالة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الحالة <span class="text-error-500">*</span>
                </label>
                <select name="status"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="scheduled" {{ old('status', 'scheduled') === 'scheduled' ? 'selected' : '' }}>مجدول</option>
                    <option value="live" {{ old('status') === 'live' ? 'selected' : '' }}>مباشر</option>
                    <option value="completed" {{ old('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="cancelled" {{ old('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>

            <!-- إلزامي -->
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox"
                           name="is_mandatory"
                           value="1"
                           {{ old('is_mandatory') ? 'checked' : '' }}
                           class="rounded border-gray-300 text-brand-600 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">درس إلزامي</span>
                </label>
            </div>

            <!-- الوصف -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    وصف الدرس
                </label>
                <textarea name="description"
                          rows="3"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                          placeholder="وصف تفصيلي عن محتوى الدرس...">{{ old('description') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Video Section (Conditional) -->
    <div id="video_section" class="conditional-section rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">محتوى الفيديو</h2>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- منصة الفيديو -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    مصدر الفيديو <span class="text-error-500">*</span>
                </label>
                <select id="video_platform" name="video_platform"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">اختر المصدر</option>
                    <option value="youtube" {{ old('video_platform') === 'youtube' ? 'selected' : '' }}>YouTube</option>
                    <option value="vimeo" {{ old('video_platform') === 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                    <option value="external" {{ old('video_platform') === 'external' ? 'selected' : '' }}>رابط خارجي</option>
                    <option value="local" {{ old('video_platform') === 'local' ? 'selected' : '' }}>رفع ملف محلي</option>
                </select>
            </div>

            <!-- رابط الفيديو (for YouTube/Vimeo/External) -->
            <div id="video_url_field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رابط الفيديو <span class="text-error-500">*</span>
                </label>
                <input type="url"
                       name="video_url"
                       id="video_url"
                       value="{{ old('video_url') }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="https://youtube.com/watch?v=... or https://vimeo.com/...">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">الصق رابط الفيديو هنا</p>
            </div>

            <!-- رفع ملف فيديو (for Local) -->
            <div id="video_upload_field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رفع ملف الفيديو <span class="text-error-500">*</span>
                </label>
                <div class="flex items-center justify-center w-full">
                    <label for="video_file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">انقر للرفع</span> أو اسحب وأفلت</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">MP4, AVI, MOV, MKV (MAX. 500MB)</p>
                        </div>
                        <input id="video_file" name="video_file" type="file" accept="video/*" class="hidden" />
                    </label>
                </div>
                <p class="mt-2 text-sm text-warning-600 dark:text-warning-400">⚠️ تنبيه: رفع الفيديو قد يستغرق وقتاً طويلاً حسب حجم الملف وسرعة الإنترنت</p>
            </div>
        </div>
    </div>

    <!-- Zoom Section (Conditional) -->
    <div id="zoom_section" class="conditional-section rounded-xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-6 dark:border-blue-900 dark:from-blue-950 dark:via-gray-900 dark:to-indigo-950 mb-6 shadow-lg hover:shadow-xl transition-all duration-300">
        <div class="flex items-center gap-3 mb-4">
            <!-- Zoom Official Logo -->
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                <svg class="w-8 h-8" viewBox="0 0 90 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M36.1691 17.0711C40.0314 13.1658 40.0314 6.83418 36.1691 2.92895C34.2395 0.97793 31.711 0.00161441 29.1694 0C26.6404 0.00161441 24.1119 0.97793 22.1824 2.92895C18.32 6.83418 18.32 13.1658 22.1824 17.0711C26.0447 20.9763 32.3068 20.9763 36.1691 17.0711ZM33.3717 14.2425C35.6891 11.8993 35.6891 8.10037 33.3717 5.75722C31.0543 3.41406 27.2971 3.41406 24.9797 5.75722C22.6623 8.10037 22.6623 11.8993 24.9797 14.2425C27.2971 16.5856 31.0543 16.5856 33.3717 14.2425ZM57.4327 2.92895C61.2951 6.83418 61.2951 13.1658 57.4327 17.0711C53.5704 20.9763 47.3084 20.9763 43.446 17.0711C39.5837 13.1658 39.5837 6.83418 43.446 2.92895C45.3756 0.97793 47.9041 0.00161441 50.4331 0C52.9747 0.00161441 55.5032 0.97793 57.4327 2.92895ZM54.6354 5.75722C56.9528 8.10037 56.9528 11.8993 54.6354 14.2425C52.318 16.5856 48.5607 16.5856 46.2434 14.2425C43.9259 11.8993 43.9259 8.10037 46.2434 5.75722C48.5607 3.41406 52.318 3.41406 54.6354 5.75722Z" fill="white"/>
                    <path d="M4.94506 20L3.95604 19.95C2.31347 19.8406 1.13603 18.6476 1.03846 16.9991L0.989011 16L12.8571 4H3.95604L2.96583 3.95C1.34815 3.85556 0.177592 2.62595 0.0494498 0.999056L0 0H14.8352L15.8241 0.0499992C17.4625 0.137543 18.6634 1.34167 18.7418 3.00124L18.7912 4L6.92308 16H15.8242L16.8132 16.05C18.4453 16.1531 19.5984 17.3544 19.7308 19.0009L19.7802 20H4.94506Z" fill="white"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
                    إنشاء اجتماع Zoom
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        تلقائي
                    </span>
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">سيتم الإنشاء تلقائياً عند الحفظ</p>
            </div>
        </div>

        <!-- Features List -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <!-- Auto Creation -->
            <div class="rounded-lg bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950 dark:to-indigo-950 p-4 border border-blue-200 dark:border-blue-800">
                <div class="flex items-start gap-2">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-blue-900 dark:text-blue-100 mb-1">إنشاء فوري</h3>
                        <p class="text-xs text-blue-700 dark:text-blue-300">يتم إنشاء الاجتماع تلقائياً بدون تدخل يدوي</p>
                    </div>
                </div>
            </div>

            <!-- Auto Recording -->
            <div class="rounded-lg bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-950 dark:to-pink-950 p-4 border border-red-200 dark:border-red-800">
                <div class="flex items-start gap-2">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-red-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-red-900 dark:text-red-100 mb-1">تسجيل تلقائي</h3>
                        <p class="text-xs text-red-700 dark:text-red-300">التسجيل على Cloud وتحميل تلقائي</p>
                    </div>
                </div>
            </div>

            <!-- All Features -->
            <div class="rounded-lg bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-950 dark:to-emerald-950 p-4 border border-green-200 dark:border-green-800">
                <div class="flex items-start gap-2">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-green-900 dark:text-green-100 mb-1">جميع الميزات</h3>
                        <p class="text-xs text-green-700 dark:text-green-300">Chat, Q&A, Polls, Reactions مفعّلة</p>
                    </div>
                </div>
            </div>

            <!-- Instant Join -->
            <div class="rounded-lg bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-950 dark:to-pink-950 p-4 border border-purple-200 dark:border-purple-800">
                <div class="flex items-start gap-2">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-purple-900 dark:text-purple-100 mb-1">انضمام فوري</h3>
                        <p class="text-xs text-purple-700 dark:text-purple-300">بدء الاجتماع بدون انتظار المضيف</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Files Section -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">الملفات المرفقة</h2>

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                رفع ملفات (PDF, Word, PowerPoint, إلخ)
            </label>
            <input type="file"
                   name="files[]"
                   multiple
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">يمكنك رفع ملفات متعددة (الحد الأقصى لكل ملف: 10 ميجابايت)</p>
        </div>
    </div>

    <!-- الأزرار -->
    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('admin.sessions.index') }}"
           class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
            إلغاء
        </a>
        <button type="submit"
                class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            حفظ الدرس
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeVideo = document.getElementById('type_video');
    const typeZoom = document.getElementById('type_zoom');
    const videoSection = document.getElementById('video_section');
    const zoomSection = document.getElementById('zoom_section');
    const videoPlatform = document.getElementById('video_platform');
    const videoUrlField = document.getElementById('video_url_field');
    const videoUploadField = document.getElementById('video_upload_field');

    // Toggle sections based on session type
    function toggleSections() {
        if (typeVideo.checked) {
            videoSection.classList.add('active');
            zoomSection.classList.remove('active');
        } else if (typeZoom.checked) {
            zoomSection.classList.add('active');
            videoSection.classList.remove('active');
        }
    }

    // Toggle video input fields based on platform
    function toggleVideoFields() {
        const platform = videoPlatform.value;

        if (platform === 'local') {
            videoUrlField.style.display = 'none';
            videoUploadField.style.display = 'block';
        } else if (platform && platform !== '') {
            videoUrlField.style.display = 'block';
            videoUploadField.style.display = 'none';
        } else {
            videoUrlField.style.display = 'none';
            videoUploadField.style.display = 'none';
        }
    }

    // Event listeners
    typeVideo.addEventListener('change', toggleSections);
    typeZoom.addEventListener('change', toggleSections);
    videoPlatform.addEventListener('change', toggleVideoFields);

    // Initialize on page load
    toggleSections();
    toggleVideoFields();
});
</script>
@endpush
