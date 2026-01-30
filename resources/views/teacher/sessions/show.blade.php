@extends('layouts.dashboard')

@section('title', $session->title_ar ?? $session->title)

@section('content')
<div class="space-y-6">
    <!-- Header with Back Button -->
    <div>
        <a href="{{ route('teacher.subjects.show', $session->subject_id) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm inline-flex items-center gap-1 mb-3">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة إلى المادة
        </a>

        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $session->title_ar ?? $session->title }}</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    {{ $session->subject->name ?? '' }}
                </p>
            </div>

            @if($session->status === 'live')
                <span class="px-4 py-2 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200 rounded-lg font-bold text-sm flex items-center gap-2 w-fit">
                    <span class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></span>
                    مباشر الآن
                </span>
            @elseif($session->status === 'completed')
                <span class="px-4 py-2 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200 rounded-lg font-bold text-sm w-fit">مكتملة</span>
            @else
                <span class="px-4 py-2 bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 rounded-lg font-bold text-sm w-fit">مجدولة</span>
            @endif
        </div>
    </div>

    <!-- Main Join Button Card -->
    @if($session->type === 'live_zoom' && $session->zoom_meeting_id)
    <div class="bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800 dark:from-blue-700 dark:via-blue-800 dark:to-blue-900 rounded-2xl p-6 sm:p-8 text-white shadow-xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7" fill="white" viewBox="0 0 24 24">
                            <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold">جلسة Zoom مباشرة</h2>
                        <p class="text-blue-100 text-sm">انضم كمضيف وابدأ التدريس</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 border border-white/20">
                        <p class="text-blue-200 text-xs mb-1">معرف الاجتماع</p>
                        <p class="font-mono font-bold text-lg">{{ $session->zoom_meeting_id }}</p>
                    </div>
                    @if($session->zoom_password)
                    <div class="bg-white/10 backdrop-blur-sm rounded-lg p-3 border border-white/20">
                        <p class="text-blue-200 text-xs mb-1">كلمة المرور</p>
                        <p class="font-mono font-bold text-lg">{{ $session->zoom_password }}</p>
                    </div>
                    @endif
                </div>

                <a href="{{ route('teacher.my-subjects.sessions.zoom', ['subjectId' => $session->subject_id, 'sessionId' => $session->id]) }}"
                   class="inline-flex items-center justify-center gap-3 px-8 py-4 bg-white text-blue-600 rounded-xl font-bold text-lg hover:bg-blue-50 transition-all shadow-lg hover:shadow-xl hover:scale-105 w-full sm:w-auto">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    بدء / الانضمام للحصة الآن
                </a>
            </div>

            <div class="hidden lg:flex items-center justify-center">
                <div class="w-40 h-40 bg-white/10 backdrop-blur-sm rounded-3xl flex items-center justify-center border-2 border-white/20">
                    <svg class="w-20 h-20 opacity-80" fill="white" viewBox="0 0 24 24">
                        <path d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Grid Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content (2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Session Details Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    تفاصيل الجلسة
                </h3>

                <div class="space-y-4">
                    @if($session->scheduled_at)
                    <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>التاريخ</span>
                        </div>
                        <span class="font-bold text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($session->scheduled_at)->locale('ar')->isoFormat('dddd، D MMMM YYYY') }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>الوقت</span>
                        </div>
                        <span class="font-bold text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::parse($session->scheduled_at)->format('h:i A') }}
                        </span>
                    </div>
                    @endif

                    @if($session->duration_minutes)
                    <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>المدة</span>
                        </div>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $session->duration_minutes }} دقيقة</span>
                    </div>
                    @endif

                    <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                            </svg>
                            <span>النوع</span>
                        </div>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-200 rounded-lg text-sm font-bold">
                            {{ $session->type === 'live_zoom' ? 'Zoom مباشر' : 'فيديو مسجل' }}
                        </span>
                    </div>

                    @if($session->description_ar || $session->description_en)
                    <div class="pt-4">
                        <span class="text-gray-600 dark:text-gray-400 block mb-2 font-medium">الوصف</span>
                        <p class="text-gray-900 dark:text-white leading-relaxed">{{ $session->description_ar ?? $session->description_en }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Files Card -->
            @if($session->files && $session->files->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    الملفات المرفقة ({{ $session->files->count() }})
                </h3>

                <div class="space-y-3">
                    @foreach($session->files as $file)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 dark:text-white truncate">{{ $file->title }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ round($file->file_size / 1024 / 1024, 2) }} MB</p>
                            </div>
                        </div>
                        <a href="{{ Storage::url($file->file_path) }}"
                           download
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition flex-shrink-0 mr-3">
                            تحميل
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar (1/3) -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">إجراءات سريعة</h3>

                <div class="space-y-3">
                    @if($session->zoom_start_url)
                    <a href="{{ $session->zoom_start_url }}"
                       target="_blank"
                       class="flex items-center gap-3 px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        <span>فتح في تطبيق Zoom</span>
                    </a>
                    @endif

                    <a href="{{ route('teacher.my-subjects.sessions.attendance', ['subjectId' => $session->subject_id, 'sessionId' => $session->id]) }}"
                       class="flex items-center gap-3 px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all font-medium">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span>الحضور والغياب</span>
                    </a>

                    <a href="{{ route('admin.sessions.edit', $session) }}"
                       class="flex items-center gap-3 px-4 py-3 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all font-medium">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>تعديل الجلسة</span>
                    </a>
                </div>
            </div>

            <!-- Share Link -->
            @if($session->zoom_join_url)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    رابط الطلاب
                </h3>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">شارك هذا الرابط مع طلابك للانضمام للحصة</p>

                <div class="flex gap-2">
                    <input type="text"
                           value="{{ $session->zoom_join_url }}"
                           readonly
                           id="join-url"
                           class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-mono">
                    <button onclick="copyLink()"
                            id="copy-btn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                        </svg>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function copyLink() {
    const input = document.getElementById('join-url');
    const btn = document.getElementById('copy-btn');

    input.select();
    input.setSelectionRange(0, 99999); // For mobile devices

    try {
        document.execCommand('copy');

        // Show success feedback
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        btn.classList.add('bg-green-600');

        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('bg-green-600');
            btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }, 2000);
    } catch (err) {
        console.error('Failed to copy:', err);
    }
}
</script>
@endsection
