@extends('layouts.dashboard')

@section('title', $subject->name)

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

    <div class="mb-6">
        <a href="{{ route('student.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">← العودة</a>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $subject->name }}</h1>
                <p class="text-gray-600 mt-1">{{ $subject->term->program->name ?? '' }} - {{ $subject->term->name ?? '' }}</p>
            </div>
            <a href="{{ route('student.quizzes.index', $subject->id) }}"
               class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-medium text-white transition-colors"
               style="background: linear-gradient(135deg, #0071AA, #005a88);">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                الاختبارات
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sessions List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-bold">الجلسات ({{ $sessions->count() }})</h2>
                </div>
                <div class="p-6">
                    @if($sessions->count() > 0)
                        <div class="space-y-3">
                            @foreach($sessions as $session)
                            <div class="border rounded-lg p-4 hover:border-blue-500 transition">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="font-bold text-gray-900">#{{ $session->session_number }} {{ $session->title }}</h3>
                                            <span class="text-xs px-2 py-1 bg-{{ $session->status === 'live' ? 'red' : ($session->status === 'completed' ? 'green' : 'blue') }}-100 text-{{ $session->status === 'live' ? 'red' : ($session->status === 'completed' ? 'green' : 'blue') }}-700 rounded">
                                                {{ $session->status === 'live' ? 'مباشر' : ($session->status === 'completed' ? 'مكتمل' : 'مجدول') }}
                                            </span>
                                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-700 rounded">
                                                {{ $session->type === 'live_zoom' ? 'Zoom' : 'فيديو' }}
                                            </span>
                                        </div>

                                        @if($session->description)
                                        <p class="text-sm text-gray-600 mb-2">{{ $session->description }}</p>
                                        @endif

                                        @if($session->scheduled_at)
                                        <div class="flex items-center gap-1 text-sm text-gray-500">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y-m-d H:i') }}
                                            <span class="text-xs text-gray-400 mr-2">
                                                ({{ \Carbon\Carbon::parse($session->scheduled_at)->diffForHumans() }})
                                            </span>
                                        </div>
                                        @endif

                                        @if($session->duration)
                                        <div class="text-sm text-gray-500 mt-1">
                                            المدة: {{ $session->duration }} دقيقة
                                        </div>
                                        @endif

                                        <!-- Recording Status -->
                                        @if($session->type === 'live_zoom' && $session->recording_status === 'completed' && $session->recording_file_path)
                                        <div class="mt-3 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z" />
                                            </svg>
                                            <span class="text-sm font-medium text-green-600">التسجيل متاح</span>
                                        </div>
                                        @elseif($session->type === 'live_zoom' && $session->recording_status === 'processing')
                                        <div class="mt-3 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-blue-600">جاري معالجة التسجيل...</span>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-2 mr-4">
                                        <a href="{{ route('admin.sessions.show', $session) }}"
                                           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm text-center whitespace-nowrap">
                                            عرض التفاصيل
                                        </a>
                                        @if($session->type === 'live_zoom' && $session->status === 'live')
                                        <a href="{{ route('admin.sessions.zoom', $session) }}"
                                           class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm text-center whitespace-nowrap animate-pulse">
                                            انضم الآن
                                        </a>
                                        @endif
                                    </div>
                                </div>

                                <!-- Recording Player (Collapsible) -->
                                @if($session->type === 'live_zoom' && $session->recording_status === 'completed' && $session->recording_file_path)
                                <div class="mt-3 border-t pt-3">
                                    <button type="button"
                                            onclick="toggleRecording({{ $session->id }})"
                                            class="flex items-center gap-2 text-sm font-medium text-blue-600 hover:text-blue-700">
                                        <svg id="icon-{{ $session->id }}" class="w-5 h-5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                        <span id="label-{{ $session->id }}">مشاهدة التسجيل</span>
                                    </button>

                                    <div id="recording-{{ $session->id }}" class="hidden mt-3">
                                        <div class="bg-black rounded-lg overflow-hidden">
                                            <video
                                                id="video-{{ $session->id }}"
                                                class="w-full"
                                                controls
                                                controlsList="nodownload"
                                                preload="metadata">
                                                <source src="{{ Storage::url($session->recording_file_path) }}" type="video/mp4">
                                                متصفحك لا يدعم تشغيل الفيديو
                                            </video>
                                        </div>
                                        <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                                            <div class="flex items-center gap-3">
                                                @if($session->recording_duration)
                                                <span>المدة: {{ gmdate('H:i:s', $session->recording_duration) }}</span>
                                                @endif
                                                @if($session->recording_size)
                                                <span>الحجم: {{ number_format($session->recording_size / 1024 / 1024, 2) }} MB</span>
                                                @endif
                                                @if($session->recording_synced_at)
                                                <span>تاريخ المزامنة: {{ $session->recording_synced_at->format('Y-m-d') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-4 text-gray-600">لا توجد جلسات لهذه المادة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Subject Info Sidebar -->
        <div>
            <!-- Teacher Info -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-bold">معلومات المادة</h2>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <div class="text-sm text-gray-600 mb-1">المعلم</div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-bold">{{ substr($subject->teacher->name ?? 'غ', 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $subject->teacher->name ?? 'غير محدد' }}</div>
                                @if($subject->teacher)
                                <div class="text-xs text-gray-600">{{ $subject->teacher->email }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t">
                        <div class="text-sm text-gray-600 mb-1">البرنامج</div>
                        <div class="font-medium text-gray-900">{{ $subject->term->program->name ?? 'غير محدد' }}</div>
                    </div>

                    <div class="pt-4 border-t mt-4">
                        <div class="text-sm text-gray-600 mb-1">الفصل الدراسي</div>
                        <div class="font-medium text-gray-900">{{ $subject->term->name ?? 'غير محدد' }}</div>
                    </div>

                    @if($subject->code)
                    <div class="pt-4 border-t mt-4">
                        <div class="text-sm text-gray-600 mb-1">رمز المادة</div>
                        <div class="font-medium text-gray-900">{{ $subject->code }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-bold">الإحصائيات</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-600">إجمالي الجلسات</span>
                                <span class="font-bold text-gray-900">{{ $sessions->count() }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-600">جلسات مكتملة</span>
                                <span class="font-bold text-green-600">{{ $sessions->where('status', 'completed')->count() }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-600">جلسات مجدولة</span>
                                <span class="font-bold text-blue-600">{{ $sessions->where('status', 'scheduled')->count() }}</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm text-gray-600">جلسات مباشرة</span>
                                <span class="font-bold text-red-600">{{ $sessions->where('status', 'live')->count() }}</span>
                            </div>
                        </div>

                        @if($sessions->count() > 0)
                        <div class="pt-4 border-t">
                            <div class="text-sm text-gray-600 mb-2">نسبة الإنجاز</div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($sessions->where('status', 'completed')->count() / $sessions->count()) * 100 }}%"></div>
                            </div>
                            <div class="text-xs text-gray-600 mt-1 text-center">
                                {{ round(($sessions->where('status', 'completed')->count() / $sessions->count()) * 100) }}%
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function toggleRecording(sessionId) {
    const recordingDiv = document.getElementById('recording-' + sessionId);
    const label = document.getElementById('label-' + sessionId);
    const icon = document.getElementById('icon-' + sessionId);
    const video = document.getElementById('video-' + sessionId);

    if (recordingDiv.classList.contains('hidden')) {
        recordingDiv.classList.remove('hidden');
        label.textContent = 'إخفاء التسجيل';
        icon.style.transform = 'rotate(180deg)';
    } else {
        recordingDiv.classList.add('hidden');
        label.textContent = 'مشاهدة التسجيل';
        icon.style.transform = 'rotate(0deg)';
        // Pause video when hiding
        if (video) {
            video.pause();
        }
    }
}
</script>
@endpush
