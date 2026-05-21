@extends('layouts.dashboard')

@section('title', 'تعديل المحاضرة')

@push('styles')
<style>
    .conditional-section { display: none; animation: fadeIn .3s ease; }
    .conditional-section.active { display: block; }
    @keyframes fadeIn { from { opacity:0;transform:translateY(-10px); } to { opacity:1;transform:translateY(0); } }
</style>
@endpush

@section('content')
<div class="mb-6">
    <nav class="mb-4 text-sm">
        <ol class="flex items-center gap-2 text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('teacher.my-courses.index') }}" class="hover:text-brand-500">دوراتي</a></li>
            <li>/</li>
            <li><a href="{{ route('teacher.my-courses.show', $program->id) }}" class="hover:text-brand-500">{{ $program->name_ar }}</a></li>
            <li>/</li>
            <li class="text-gray-900 dark:text-white">تعديل المحاضرة</li>
        </ol>
    </nav>
    <div class="flex items-center gap-3">
        <a href="{{ route('teacher.my-courses.show', $program->id) }}"
           class="flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-800 transition-colors">
            <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تعديل المحاضرة</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $session->title }}</p>
        </div>
    </div>
</div>

@if($errors->any())
<div class="mb-4 rounded-lg bg-red-50 p-4 dark:bg-red-900">
    <ul class="list-disc list-inside text-sm text-red-600 dark:text-red-200">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
</div>
@endif

<form action="{{ route('teacher.my-courses.sessions.update', [$program->id, $session->id]) }}"
      method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    {{-- Basic Info --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">المعلومات الأساسية</h2>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">عنوان المحاضرة <span class="text-red-500">*</span></label>
                <input type="text" name="title_ar" required value="{{ old('title_ar', $session->title_ar) }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="عنوان المحاضرة">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">رقم المحاضرة <span class="text-red-500">*</span></label>
                <input type="number" name="session_number" required value="{{ old('session_number', $session->session_number) }}" min="1"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">موعد المحاضرة</label>
                <input type="datetime-local" name="scheduled_at"
                       value="{{ old('scheduled_at', $session->scheduled_at?->format('Y-m-d\TH:i')) }}"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المدة (بالدقائق)</label>
                <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $session->duration_minutes) }}" min="1"
                       class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                       placeholder="60">
            </div>
        </div>
    </div>

    {{-- Session Type --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">نوع المحاضرة</h2>
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <input type="radio" id="type_zoom" name="type" value="live_zoom" class="hidden peer"
                       {{ old('type', $session->type) === 'live_zoom' ? 'checked' : '' }}
                       onchange="document.querySelectorAll('.conditional-section').forEach(s=>s.classList.remove('active'));document.getElementById('zoom_section').classList.add('active')">
                <label for="type_zoom" class="peer-checked:border-brand-500 peer-checked:bg-brand-50 dark:peer-checked:bg-brand-900/20 cursor-pointer flex flex-col items-center gap-2 rounded-xl border-2 border-gray-200 p-4 hover:border-brand-300 dark:border-gray-700 transition-colors">
                    <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">مباشرة (Zoom)</span>
                </label>
            </div>
            <div>
                <input type="radio" id="type_recorded" name="type" value="recorded_video" class="hidden peer"
                       {{ old('type', $session->type) === 'recorded_video' ? 'checked' : '' }}
                       onchange="document.querySelectorAll('.conditional-section').forEach(s=>s.classList.remove('active'));document.getElementById('video_section').classList.add('active')">
                <label for="type_recorded" class="peer-checked:border-brand-500 peer-checked:bg-brand-50 dark:peer-checked:bg-brand-900/20 cursor-pointer flex flex-col items-center gap-2 rounded-xl border-2 border-gray-200 p-4 hover:border-brand-300 dark:border-gray-700 transition-colors">
                    <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">مسجلة</span>
                </label>
            </div>
        </div>

        {{-- Zoom Fields --}}
        <div id="zoom_section" class="conditional-section {{ old('type', $session->type) === 'live_zoom' ? 'active' : '' }}">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 rounded-xl bg-blue-50 dark:bg-blue-900/10 p-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Zoom Meeting ID</label>
                    <input type="text" name="zoom_meeting_id" value="{{ old('zoom_meeting_id', $session->zoom_meeting_id) }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" placeholder="تلقائي عند الحفظ">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">رابط الانضمام</label>
                    <input type="url" name="zoom_join_url" value="{{ old('zoom_join_url', $session->zoom_join_url) }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" dir="ltr">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">كلمة المرور</label>
                    <input type="text" name="zoom_password" value="{{ old('zoom_password', $session->zoom_password) }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                </div>
            </div>
        </div>

        {{-- Video Fields --}}
        <div id="video_section" class="conditional-section {{ old('type', $session->type) === 'recorded_video' ? 'active' : '' }}">
            <div class="rounded-xl bg-purple-50 dark:bg-purple-900/10 p-4 space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">رابط الفيديو (YouTube/Vimeo)</label>
                    <input type="url" name="video_url" value="{{ old('video_url', $session->video_url) }}"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white" dir="ltr"
                           placeholder="https://...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">أو رفع ملف فيديو</label>
                    <input type="file" name="video_file"
                           class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                           accept=".mp4,.avi,.mov,.mkv,.wmv,.flv">
                    @if($session->video_path)
                        <p class="mt-1 text-xs text-purple-600 dark:text-purple-400">يوجد فيديو مرفوع — ارفع ملفاً جديداً للاستبدال</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Attachments --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900 mb-6">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">المرفقات</h2>
        @if($session->files->isNotEmpty())
        <div class="mb-4 space-y-2">
            @foreach($session->files as $file)
            <div class="flex items-center justify-between rounded-lg bg-gray-50 dark:bg-gray-800 px-4 py-2">
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $file->title }}</span>
                <form action="{{ route('teacher.my-courses.sessions.files.destroy', [$program->id, $session->id, $file->id]) }}"
                      method="POST" class="inline" onsubmit="return confirm('حذف الملف؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-xs font-medium">حذف</button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">إضافة ملفات جديدة</label>
            <input type="file" name="files[]" multiple
                   class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.png,.jpg,.jpeg">
            <p class="mt-1 text-xs text-gray-400">يمكنك رفع عدة ملفات — حد أقصى 10MB لكل ملف</p>
        </div>
    </div>

    <div class="flex gap-3 justify-end">
        <a href="{{ route('teacher.my-courses.show', $program->id) }}"
           class="rounded-lg border border-gray-300 dark:border-gray-700 px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition">إلغاء</a>
        <button type="submit"
                class="rounded-lg px-6 py-2.5 text-sm font-semibold text-white transition"
                style="background:linear-gradient(135deg,#059669,#047857)">حفظ التعديلات</button>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('input[name="type"]');
        radios.forEach(r => {
            if (r.checked) {
                document.querySelectorAll('.conditional-section').forEach(s => s.classList.remove('active'));
                const sec = r.value === 'live_zoom' ? 'zoom_section' : 'video_section';
                document.getElementById(sec)?.classList.add('active');
            }
        });
    });
</script>
@endpush
@endsection
