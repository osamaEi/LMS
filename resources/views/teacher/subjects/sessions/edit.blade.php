@extends('layouts.dashboard')

@section('title', 'تعديل الحصة')

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
            <li class="text-gray-900 dark:text-white">تعديل الحصة</li>
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
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل الحصة</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $session->title }}</p>
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

<form action="{{ route('teacher.my-subjects.sessions.update', [$subject->id, $session->id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">المعلومات الأساسية</h2>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- الوحدة -->
            @if($subject->units->count() > 0)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الوحدة (اختياري)
                </label>
                <select name="unit_id"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">بدون وحدة</option>
                    @foreach($subject->units as $unit)
                        <option value="{{ $unit->id }}" {{ old('unit_id', $session->unit_id) == $unit->id ? 'selected' : '' }}>
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
                       value="{{ old('title_ar', $session->title_ar) }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- عنوان الحصة بالإنجليزي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    عنوان الحصة (إنجليزي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="title_en"
                       value="{{ old('title_en', $session->title_en) }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- رقم الحصة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رقم الحصة <span class="text-error-500">*</span>
                </label>
                <input type="number"
                       name="session_number"
                       value="{{ old('session_number', $session->session_number) }}"
                       required
                       min="1"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- المدة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    المدة (بالدقائق)
                </label>
                <input type="number"
                       name="duration_minutes"
                       value="{{ old('duration_minutes', $session->duration_minutes) }}"
                       min="1"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
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
                               {{ old('type', $session->type) === 'recorded_video' ? 'checked' : '' }}
                               required class="sr-only peer">
                        <label for="type_video" class="flex flex-col items-center justify-center p-5 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                            <svg class="w-12 h-12 mb-2 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                            </svg>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">فيديو مسجل</span>
                        </label>
                    </div>

                    <!-- Live Zoom -->
                    <div class="session-type-card">
                        <input type="radio" id="type_zoom" name="type" value="live_zoom"
                               {{ old('type', $session->type) === 'live_zoom' ? 'checked' : '' }}
                               required class="sr-only peer">
                        <label for="type_zoom" class="flex flex-col items-center justify-center p-5 bg-white border-2 border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                            <svg class="w-12 h-12 mb-2 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span class="text-base font-semibold text-gray-900 dark:text-white">بث مباشر Zoom</span>
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
                       value="{{ old('scheduled_at', $session->scheduled_at ? \Carbon\Carbon::parse($session->scheduled_at)->format('Y-m-d\TH:i') : '') }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- الوصف بالعربي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    وصف الحصة (عربي)
                </label>
                <textarea name="description_ar"
                          rows="3"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description_ar', $session->description_ar) }}</textarea>
            </div>

            <!-- الوصف بالإنجليزي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    وصف الحصة (إنجليزي)
                </label>
                <textarea name="description_en"
                          rows="3"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description_en', $session->description_en) }}</textarea>
            </div>
        </div>
    </div>

    <!-- Video Section -->
    <div id="video_section" class="conditional-section rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
            </svg>
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">محتوى الفيديو</h2>
        </div>

        @if($session->hasVideo())
        <div class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-950 border border-green-200 dark:border-green-800">
            <p class="text-sm text-green-800 dark:text-green-200">
                <strong>الفيديو الحالي:</strong>
                @if($session->video_platform === 'local')
                    ملف محلي مرفوع
                @else
                    {{ $session->video_url }}
                @endif
            </p>
        </div>
        @endif

        <div class="grid grid-cols-1 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    مصدر الفيديو
                </label>
                <select id="video_platform" name="video_platform"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">اختر المصدر</option>
                    <option value="youtube" {{ old('video_platform', $session->video_platform) === 'youtube' ? 'selected' : '' }}>YouTube</option>
                    <option value="vimeo" {{ old('video_platform', $session->video_platform) === 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                    <option value="external" {{ old('video_platform', $session->video_platform) === 'external' ? 'selected' : '' }}>رابط خارجي</option>
                    <option value="local" {{ old('video_platform', $session->video_platform) === 'local' ? 'selected' : '' }}>رفع ملف محلي</option>
                </select>
            </div>

            <div id="video_url_field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رابط الفيديو
                </label>
                <input type="url"
                       name="video_url"
                       id="video_url"
                       value="{{ old('video_url', $session->video_url) }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="https://youtube.com/watch?v=...">
            </div>

            <div id="video_upload_field" style="display: none;">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رفع ملف فيديو جديد (سيستبدل الملف الحالي)
                </label>
                <div class="flex items-center justify-center w-full">
                    <label for="video_file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">انقر للرفع</span></p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">MP4, AVI, MOV, MKV (MAX. 500MB)</p>
                        </div>
                        <input id="video_file" name="video_file" type="file" accept="video/*" class="hidden" />
                    </label>
                </div>
                <p id="video_file_name" class="mt-2 text-sm text-brand-600 dark:text-brand-400"></p>
            </div>
        </div>
    </div>

    <!-- Zoom Section -->
    <div id="zoom_section" class="conditional-section rounded-xl border-2 border-blue-200 bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-6 dark:border-blue-900 dark:from-blue-950 dark:via-gray-900 dark:to-indigo-950 mb-6 shadow-lg">
        <div class="flex items-center gap-3 mb-4">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white">اجتماع Zoom</h2>
                @if($session->zoom_meeting_id)
                    <p class="text-sm text-green-600 dark:text-green-400 mt-1">الاجتماع موجود: {{ $session->zoom_meeting_id }}</p>
                @else
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">سيتم إنشاء اجتماع جديد عند الحفظ</p>
                @endif
            </div>
        </div>

        @if($session->zoom_meeting_id)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="p-4 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">رابط الانضمام</p>
                <a href="{{ $session->zoom_join_url }}" target="_blank" class="text-sm text-blue-600 hover:underline break-all">
                    {{ $session->zoom_join_url }}
                </a>
            </div>
            @if($session->zoom_password)
            <div class="p-4 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">كلمة المرور</p>
                <p class="text-sm font-mono text-gray-900 dark:text-white">{{ $session->zoom_password }}</p>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Files Section -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">الملفات المرفقة</h2>

        @if($session->files->count() > 0)
        <div class="mb-4">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">الملفات الحالية:</p>
            <div class="space-y-2">
                @foreach($session->files as $file)
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $file->title }}</span>
                    </div>
                    <form action="{{ route('teacher.my-subjects.sessions.files.destroy', [$subject->id, $session->id, $file->id]) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs text-error-600 hover:text-error-800" onclick="return confirm('هل أنت متأكد من حذف هذا الملف؟')">
                            حذف
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                إضافة ملفات جديدة
            </label>
            <input type="file"
                   name="files[]"
                   multiple
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">الحد الأقصى لكل ملف: 10 ميجابايت</p>
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
            حفظ التغييرات
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

    function toggleSections() {
        if (typeVideo.checked) {
            videoSection.classList.add('active');
            zoomSection.classList.remove('active');
        } else if (typeZoom.checked) {
            zoomSection.classList.add('active');
            videoSection.classList.remove('active');
        }
    }

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

    if (videoFileInput) {
        videoFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                videoFileName.textContent = 'الملف المحدد: ' + this.files[0].name;
            }
        });
    }

    typeVideo.addEventListener('change', toggleSections);
    typeZoom.addEventListener('change', toggleSections);
    videoPlatform.addEventListener('change', toggleVideoFields);

    toggleSections();
    toggleVideoFields();
});
</script>
@endpush
