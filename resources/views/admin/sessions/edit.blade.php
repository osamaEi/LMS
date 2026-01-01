@extends('layouts.dashboard')

@section('title', 'تعديل الدرس')

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-3 mb-3">
        <a href="{{ route('admin.sessions.index') }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل الدرس</h1>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400">تعديل بيانات الدرس ومحتوياته</p>
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

<form action="{{ route('admin.sessions.update', $session) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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
                        <option value="{{ $subject->id }}" {{ old('subject_id', $session->subject_id) == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }} ({{ $subject->code }}) - {{ $subject->term->name ?? '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- عنوان الدرس بالعربي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    عنوان الدرس (عربي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="title_ar"
                       value="{{ old('title_ar', $session->title_ar) }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- عنوان الدرس بالإنجليزي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    عنوان الدرس (إنجليزي) <span class="text-error-500">*</span>
                </label>
                <input type="text"
                       name="title_en"
                       value="{{ old('title_en', $session->title_en) }}"
                       required
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- رقم الدرس -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رقم الدرس <span class="text-error-500">*</span>
                </label>
                <input type="number"
                       name="session_number"
                       value="{{ old('session_number', $session->session_number) }}"
                       required
                       min="1"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- نوع الدرس -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    نوع الدرس <span class="text-error-500">*</span>
                </label>
                <select name="type"
                        required
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="recorded_video" {{ old('type', $session->type) === 'recorded_video' ? 'selected' : '' }}>فيديو مسجل</option>
                    <option value="live_zoom" {{ old('type', $session->type) === 'live_zoom' ? 'selected' : '' }}>Zoom مباشر</option>
                </select>
            </div>

            <!-- تاريخ ووقت الدرس -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    تاريخ ووقت الدرس
                </label>
                <input type="datetime-local"
                       name="scheduled_at"
                       value="{{ old('scheduled_at', $session->scheduled_at?->format('Y-m-d\TH:i')) }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- المدة بالدقائق - مخفي حالياً -->
            <input type="hidden" name="duration_minutes" value="{{ old('duration_minutes', $session->duration_minutes) }}">

            <!-- الوصف بالعربي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    وصف الدرس (عربي)
                </label>
                <textarea name="description_ar"
                          rows="3"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description_ar', $session->description_ar) }}</textarea>
            </div>

            <!-- الوصف بالإنجليزي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    وصف الدرس (إنجليزي)
                </label>
                <textarea name="description_en"
                          rows="3"
                          class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ old('description_en', $session->description_en) }}</textarea>
            </div>
        </div>
    </div>

    <!-- Video Section -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">محتوى الفيديو</h2>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- رابط الفيديو -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رابط الفيديو
                </label>
                <input type="url"
                       name="video_url"
                       value="{{ old('video_url', $session->video_url) }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- منصة الفيديو -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    منصة الفيديو
                </label>
                <select name="video_platform"
                        class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                    <option value="">اختر المنصة</option>
                    <option value="youtube" {{ old('video_platform', $session->video_platform) === 'youtube' ? 'selected' : '' }}>YouTube</option>
                    <option value="vimeo" {{ old('video_platform', $session->video_platform) === 'vimeo' ? 'selected' : '' }}>Vimeo</option>
                    <option value="external" {{ old('video_platform', $session->video_platform) === 'external' ? 'selected' : '' }}>رابط خارجي</option>
                    <option value="local" {{ old('video_platform', $session->video_platform) === 'local' ? 'selected' : '' }}>ملف محلي</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Zoom Section -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">معلومات Zoom</h2>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Meeting ID -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رقم الاجتماع (Meeting ID)
                </label>
                <input type="text"
                       name="zoom_meeting_id"
                       value="{{ old('zoom_meeting_id', $session->zoom_meeting_id) }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- كلمة المرور -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    كلمة مرور Zoom
                </label>
                <input type="text"
                       name="zoom_password"
                       value="{{ old('zoom_password', $session->zoom_password) }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>

            <!-- رابط الانضمام -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    رابط الانضمام (Join URL)
                </label>
                <input type="url"
                       name="zoom_join_url"
                       value="{{ old('zoom_join_url', $session->zoom_join_url) }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>
        </div>
    </div>

    <!-- Files Section -->
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">الملفات المرفقة</h2>

        <!-- Existing Files -->
        @if($session->files->count() > 0)
        <div class="mb-6">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">الملفات الحالية</h3>
            <div class="space-y-2">
                @foreach($session->files as $file)
                <div class="flex items-center justify-between rounded-lg border border-gray-200 p-3 dark:border-gray-800">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $file->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($file->file_size / 1024, 2) }} KB</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.sessions.files.delete', $file) }}" method="POST" class="inline"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="rounded-lg bg-error-50 px-3 py-1.5 text-xs font-medium text-error-600 hover:bg-error-100 dark:bg-error-900 dark:text-error-200 dark:hover:bg-error-800">
                            حذف
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Upload New Files -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                رفع ملفات جديدة
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
            حفظ التغييرات
        </button>
    </div>
</form>
@endsection
