@extends('layouts.dashboard')

@section('title', 'عرض الدرس')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-5xl">

    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('admin.sessions.index') }}" class="text-gray-600 hover:text-gray-900 inline-flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            العودة للدروس
        </a>
    </div>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-3">
            <h1 class="text-2xl font-bold text-gray-900">{{ $session->title }}</h1>
            <div class="flex gap-2">
                <a href="{{ route('admin.sessions.edit', $session) }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                    تعديل
                </a>
                <form action="{{ route('admin.sessions.destroy', $session) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('هل أنت متأكد؟')"
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
                        حذف
                    </button>
                </form>
            </div>
        </div>

        <div class="text-gray-600 mb-3">{{ $session->subject->name ?? 'غير محدد' }}</div>

        <!-- Status Badges -->
        <div class="flex flex-wrap gap-2">
            @if($session->status === 'live')
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded text-xs">🔴 مباشر</span>
            @elseif($session->status === 'scheduled')
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-xs">📅 مجدول</span>
            @elseif($session->status === 'completed')
                <span class="px-3 py-1 bg-green-100 text-green-700 rounded text-xs">✓ مكتمل</span>
            @endif

            @if($session->is_mandatory)
                <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded text-xs">⚠️ إلزامي</span>
            @endif

            @if($session->type === 'live_zoom')
                <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded text-xs">Zoom</span>
            @endif
        </div>
    </div>

    <!-- JOIN ZOOM BUTTON - PROMINENT -->
    @if($session->type === 'live_zoom' && $session->zoom_meeting_id)
    <div class="bg-blue-600 rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="text-white">
                <div class="text-lg font-bold mb-1">🎥 اجتماع Zoom</div>
                <div class="text-sm text-blue-100">Meeting ID: {{ $session->zoom_meeting_id }}</div>
            </div>
            <a href="{{ route('admin.sessions.zoom', $session) }}"
               class="px-8 py-3 bg-white text-blue-600 rounded-lg font-bold hover:bg-blue-50 transition text-lg">
                الانضمام للاجتماع ←
            </a>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Countdown Timer -->
            @if($session->status === 'scheduled' && $session->scheduled_at)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-900 mb-4">⏰ الوقت المتبقي</h3>
                <div id="countdown-timer" class="grid grid-cols-4 gap-3 text-center">
                    <div>
                        <div class="bg-purple-100 rounded p-3">
                            <div class="text-2xl font-bold text-purple-600" id="days">00</div>
                            <div class="text-xs text-purple-600">يوم</div>
                        </div>
                    </div>
                    <div>
                        <div class="bg-pink-100 rounded p-3">
                            <div class="text-2xl font-bold text-pink-600" id="hours">00</div>
                            <div class="text-xs text-pink-600">ساعة</div>
                        </div>
                    </div>
                    <div>
                        <div class="bg-blue-100 rounded p-3">
                            <div class="text-2xl font-bold text-blue-600" id="minutes">00</div>
                            <div class="text-xs text-blue-600">دقيقة</div>
                        </div>
                    </div>
                    <div>
                        <div class="bg-indigo-100 rounded p-3">
                            <div class="text-2xl font-bold text-indigo-600" id="seconds">00</div>
                            <div class="text-xs text-indigo-600">ثانية</div>
                        </div>
                    </div>
                </div>
                <div id="countdown-message" class="hidden bg-green-100 text-green-700 rounded p-4 text-center mt-3">
                    <div class="font-bold">🎉 بدأت الجلسة!</div>
                </div>
                <script>
                    const scheduledAt = new Date('{{ $session->scheduled_at }}').getTime();
                    function updateCountdown() {
                        const now = new Date().getTime();
                        const distance = scheduledAt - now;
                        if (distance < 0) {
                            document.getElementById('countdown-timer').classList.add('hidden');
                            document.getElementById('countdown-message').classList.remove('hidden');
                            return;
                        }
                        document.getElementById('days').textContent = Math.floor(distance / (1000 * 60 * 60 * 24)).toString().padStart(2, '0');
                        document.getElementById('hours').textContent = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)).toString().padStart(2, '0');
                        document.getElementById('minutes').textContent = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)).toString().padStart(2, '0');
                        document.getElementById('seconds').textContent = Math.floor((distance % (1000 * 60)) / 1000).toString().padStart(2, '0');
                    }
                    updateCountdown();
                    setInterval(updateCountdown, 1000);
                </script>
            </div>
            @endif

            <!-- Description -->
            @if($session->description)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-900 mb-3">📝 الوصف</h3>
                <div class="text-gray-700 whitespace-pre-line">{{ $session->description }}</div>
            </div>
            @endif

            <!-- Video -->
            @if($session->type === 'video' && $session->video_path)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-900 mb-3">🎬 الفيديو</h3>
                <div class="aspect-video bg-gray-900 rounded overflow-hidden">
                    <video controls class="w-full h-full">
                        <source src="{{ Storage::url($session->video_path) }}" type="video/mp4">
                    </video>
                </div>
            </div>
            @endif

            <!-- Files -->
            @if($session->files && count($session->files) > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-900 mb-3">📎 الملفات</h3>
                <div class="space-y-2">
                    @foreach($session->files as $file)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded hover:bg-gray-100">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-900">{{ $file['name'] }}</span>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ $file['url'] }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">
                                تحميل
                            </a>
                            <form action="{{ route('admin.sessions.files.delete', $file['id']) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('حذف الملف؟')" class="text-red-600 hover:text-red-800 text-sm">
                                    حذف
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <!-- Sidebar -->
        <div class="space-y-6">

            <!-- Session Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-900 mb-3">ℹ️ المعلومات</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">الرقم:</span>
                        <span class="font-medium">{{ $session->session_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">النوع:</span>
                        <span class="font-medium">
                            @if($session->type === 'live_zoom') Zoom
                            @elseif($session->type === 'video') فيديو
                            @else {{ $session->type }}
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">المدة:</span>
                        <span class="font-medium">{{ $session->duration }} دقيقة</span>
                    </div>
                    @if($session->scheduled_at)
                    <div class="flex justify-between">
                        <span class="text-gray-600">الموعد:</span>
                        <span class="font-medium text-xs">{{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y-m-d H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Subject -->
            @if($session->subject)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-900 mb-3">📚 المادة</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <div class="text-gray-600 text-xs">المادة</div>
                        <div class="font-medium">{{ $session->subject->name }}</div>
                    </div>
                    @if($session->subject->term)
                    <div>
                        <div class="text-gray-600 text-xs">الفصل</div>
                        <div class="font-medium">{{ $session->subject->term->name }}</div>
                    </div>
                    @endif
                    @if($session->subject->term && $session->subject->term->program)
                    <div>
                        <div class="text-gray-600 text-xs">البرنامج</div>
                        <div class="font-medium">{{ $session->subject->term->program->name }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Zoom Details -->
            @if($session->type === 'live_zoom' && $session->zoom_meeting_id)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-900 mb-3">🔗 Zoom</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <div class="text-gray-600 text-xs">Meeting ID</div>
                        <div class="font-mono text-xs">{{ $session->zoom_meeting_id }}</div>
                    </div>
                    @if($session->zoom_password)
                    <div>
                        <div class="text-gray-600 text-xs">Password</div>
                        <div class="font-mono text-xs">{{ $session->zoom_password }}</div>
                    </div>
                    @endif
                    <div class="pt-2">
                        <a href="{{ $session->zoom_join_url }}" target="_blank"
                           class="text-blue-600 hover:text-blue-800 text-xs">
                            فتح في تطبيق Zoom ↗
                        </a>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection
