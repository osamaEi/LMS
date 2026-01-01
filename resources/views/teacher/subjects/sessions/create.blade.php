@extends('layouts.dashboard')

@section('title', 'إضافة حصة جديدة')

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
    <!-- Breadcrumb -->
    <nav class="mb-4 text-sm">
        <ol class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('teacher.my-subjects.index') }}" class="hover:text-brand-500">موادي</a></li>
            <li>/</li>
            <li><a href="{{ route('teacher.my-subjects.show', $subject->id) }}" class="hover:text-brand-500">{{ $subject->name }}</a></li>
            <li>/</li>
            <li class="text-gray-900 dark:text-white">إضافة حصة جديدة</li>
        </ol>
    </nav>

    <div class="flex items-center gap-3">
        <a href="{{ route('teacher.my-subjects.show', $subject->id) }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إضافة حصة جديدة</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $subject->name }}</p>
        </div>
    </div>
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

<form action="{{ route('teacher.my-subjects.sessions.store', $subject->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">المعلومات الأساسية</h2>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- الوحدة (اختياري) -->
            @if($subject->units->count() > 0)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الوحدة (اختياري)
                </label>
                <select name="unit_id"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">بدون وحدة</option>
                    @foreach($subject->units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- عنوان الحصة بالعربي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    عنوان الحصة (عربي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="title_ar"
                       value="{{ old('title_ar') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="مثال: مقدمة في البرمجة - الحصة الأولى">
            </div>

            <!-- عنوان الحصة بالإنجليزي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    عنوان الحصة (إنجليزي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="title_en"
                       value="{{ old('title_en') }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="Example: Introduction to Programming - Session 1">
            </div>

            <!-- رقم الحصة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رقم الحصة <span class="text-error-500">*</span>
                </label>
                <input type="number"
                       name="session_number"
                       value="{{ old('session_number', $nextSessionNumber) }}"
                       required
                       min="1"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- المدة بالدقائق -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    المدة (بالدقائق)
                </label>
                <input type="number"
                       name="duration_minutes"
                       value="{{ old('duration_minutes', 60) }}"
                       min="1"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="60">
            </div>

            <!-- نوع الحصة -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    نوع الحصة <span class="text-error-500">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Recorded Video -->
                    <div class="session-type-card">
                        <input type="radio" id="type_video" name="type" value="recorded_video"
                               {{ old('type', 'recorded_video') === 'recorded_video' ? 'checked' : '' }}
                               required class="sr-only peer">
                        <label for="type_video" class="flex flex-col items-center justify-center p-5 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                            <svg class="w-12 h-12 mb-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                            </svg>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">فيديو مسجل</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">رفع فيديو أو رابط YouTube/Vimeo</span>
                        </label>
                    </div>

                    <!-- Live Zoom -->
                    <div class="session-type-card">
                        <input type="radio" id="type_zoom" name="type" value="live_zoom"
                               {{ old('type') === 'live_zoom' ? 'checked' : '' }}
                               required class="sr-only peer">
                        <label for="type_zoom" class="flex flex-col items-center justify-center p-5 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                            <svg class="w-12 h-12 mb-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">بث مباشر Zoom</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 mt-1">إنشاء اجتماع Zoom تلقائياً</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- تاريخ ووقت الحصة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    تاريخ ووقت الحصة
                </label>
                <input type="datetime-local"
                       name="scheduled_at"
                       value="{{ old('scheduled_at') }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- الوصف بالعربي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    وصف الحصة (عربي)
                </label>
                <textarea name="description_ar"
                          rows="3"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                          placeholder="وصف تفصيلي عن محتوى الحصة...">{{ old('description_ar') }}</textarea>
            </div>

            <!-- الوصف بالإنجليزي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    وصف الحصة (إنجليزي)
                </label>
                <textarea name="description_en"
                          rows="3"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                          placeholder="Detailed description about the session content...">{{ old('description_en') }}</textarea>
            </div>
        </div>
    </div>

    <!-- Video Section (Conditional) -->
    <div id="video_section" class="conditional-section rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
            </svg>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">محتوى الفيديو</h2>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <!-- منصة الفيديو -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    مصدر الفيديو
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

            <!-- رابط الفيديو -->
            <div id="video_url_field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رابط الفيديو
                </label>
                <input type="url"
                       name="video_url"
                       id="video_url"
                       value="{{ old('video_url') }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="https://youtube.com/watch?v=... or https://vimeo.com/...">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">الصق رابط الفيديو هنا</p>
            </div>

            <!-- رفع ملف فيديو -->
            <div id="video_upload_field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رفع ملف الفيديو
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
                <p id="video_file_name" class="mt-2 text-sm text-brand-600 dark:text-brand-400"></p>
                <p class="mt-2 text-sm text-warning-600 dark:text-warning-400">تنبيه: رفع الفيديو قد يستغرق وقتاً حسب حجم الملف</p>
            </div>
        </div>
    </div>

    <!-- Zoom Section (Conditional) -->
    <div id="zoom_section" class="conditional-section rounded-xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-6 dark:border-blue-900 dark:from-blue-950 dark:via-gray-900 dark:to-indigo-950 mb-6 shadow-lg">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white flex items-center gap-2">
                    إنشاء اجتماع Zoom
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        تلقائي
                    </span>
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">سيتم إنشاء اجتماع Zoom تلقائياً عند الحفظ</p>
            </div>
        </div>

        <!-- Features -->
        <div class="grid grid-cols-2 gap-3">
            <div class="rounded-lg bg-blue-50 dark:bg-blue-950 p-3 border border-blue-200 dark:border-blue-800">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span class="text-sm font-medium text-blue-900 dark:text-blue-100">إنشاء فوري</span>
                </div>
            </div>
            <div class="rounded-lg bg-red-50 dark:bg-red-950 p-3 border border-red-200 dark:border-red-800">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-medium text-red-900 dark:text-red-100">تسجيل تلقائي</span>
                </div>
            </div>
            <div class="rounded-lg bg-green-50 dark:bg-green-950 p-3 border border-green-200 dark:border-green-800">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                    <span class="text-sm font-medium text-green-900 dark:text-green-100">Chat & Q&A</span>
                </div>
            </div>
            <div class="rounded-lg bg-purple-50 dark:bg-purple-950 p-3 border border-purple-200 dark:border-purple-800">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-sm font-medium text-purple-900 dark:text-purple-100">غرفة انتظار</span>
                </div>
            </div>
        </div>

        <p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
            ملاحظة: تأكد من تحديد موعد الحصة أعلاه ليتم جدولة الاجتماع بشكل صحيح
        </p>
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
        <a href="{{ route('teacher.my-subjects.show', $subject->id) }}"
           class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 transition-colors">
            إلغاء
        </a>
        <button type="submit"
                class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600 transition-colors">
            حفظ الحصة
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
    const videoFileInput = document.getElementById('video_file');
    const videoFileName = document.getElementById('video_file_name');

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

    // Show selected file name
    if (videoFileInput) {
        videoFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                videoFileName.textContent = 'الملف المحدد: ' + this.files[0].name;
            }
        });
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
