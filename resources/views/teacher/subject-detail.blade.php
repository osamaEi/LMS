@extends('layouts.dashboard')

@section('title', $subject->name)

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    .view-toggle {
        display: flex;
        gap: 4px;
        padding: 4px;
        background: #f3f4f6;
        border-radius: 10px;
    }

    .view-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
        color: #6b7280;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        background: transparent;
    }

    .view-btn.active {
        background: white;
        color: #0071AA;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .fc .fc-event {
        border-radius: 6px !important;
        border: none !important;
        padding: 2px 6px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        margin-bottom: 2px !important;
    }

    .fc .fc-event:hover {
        transform: scale(1.02) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

    <div class="mb-6">
        <a href="{{ route('teacher.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">← العودة</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $subject->name }}</h1>
        <p class="text-gray-600 mt-1">{{ $subject->term->program->name ?? '' }} - {{ $subject->term->name ?? '' }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-bold">إضافة جلسة جديدة</h2>
                </div>
                <div class="p-6">
                    <a href="{{ route('admin.sessions.index', ['subject_id' => $subject->id]) }}" class="block w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center font-medium">
                        + إضافة جلسة (Zoom / فيديو / ملفات)
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b flex justify-between items-center">
                    <h2 class="text-lg font-bold">الجلسات ({{ $sessions->count() }})</h2>
                    <div class="view-toggle">
                        <button class="view-btn active" data-view="calendar">
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            التقويم
                        </button>
                        <button class="view-btn" data-view="list">
                            <svg class="w-4 h-4 inline-block ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            القائمة
                        </button>
                    </div>
                </div>

                <!-- Calendar View -->
                <div id="calendarView" class="p-6">
                    <div id="calendar"></div>
                </div>

                <!-- List View -->
                <div id="listView" class="p-6" style="display: none;">
                    @if($sessions->count() > 0)
                        <div class="space-y-3">
                            @foreach($sessions as $session)
                            <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <h3 class="font-bold">{{ $session->title_ar ?? $session->title }}</h3>
                                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">
                                                {{ $session->type === 'live_zoom' ? 'Zoom' : 'فيديو' }}
                                            </span>
                                            @if($session->status === 'live')
                                                <span class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded flex items-center gap-1">
                                                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                                    مباشر
                                                </span>
                                            @endif
                                        </div>
                                        @if($session->scheduled_at)
                                            <div class="text-sm text-gray-600">
                                                <svg class="w-4 h-4 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                {{ \Carbon\Carbon::parse($session->scheduled_at)->locale('ar')->isoFormat('dddd، D MMMM YYYY - h:mm A') }}
                                            </div>
                                        @endif
                                    </div>
                                    <a href="{{ route('teacher.sessions.show', $session) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">
                                        عرض
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center py-8 text-gray-600">لا توجد جلسات</p>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-bold">الطلاب ({{ $students->count() }})</h2>
                </div>
                <div class="p-6">
                    @if($students->count() > 0)
                        @foreach($students as $student)
                        <div class="flex gap-3 p-3 bg-gray-50 rounded mb-2">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-bold">{{ substr($student->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="font-medium text-sm">{{ $student->name }}</div>
                                <div class="text-xs text-gray-600">{{ $student->email }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-center py-8 text-sm text-gray-600">لا يوجد طلاب</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/ar.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sessionsData = @json($sessions);
    const subjectColor = '{{ $subject->color ?? "#0071AA" }}';

    // Convert to calendar events
    const events = sessionsData.map(session => {
        let backgroundColor = subjectColor;
        if (session.status === 'live') backgroundColor = '#ef4444';
        else if (session.status === 'completed') backgroundColor = '#10b981';

        return {
            id: session.id,
            title: session.title_ar || session.title,
            start: session.scheduled_at,
            backgroundColor: backgroundColor,
            borderColor: backgroundColor,
            url: '/teacher/sessions/' + session.id,
            extendedProps: {
                type: session.type,
                status: session.status,
                duration: session.duration_minutes
            }
        };
    }).filter(e => e.start);

    // Initialize Calendar
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'ar',
        direction: 'rtl',
        headerToolbar: {
            right: 'prev,next today',
            center: 'title',
            left: 'dayGridMonth,dayGridWeek,listWeek'
        },
        height: 'auto',
        events: events,
        eventDidMount: function(info) {
            info.el.title = info.event.title + ' - ' + (info.event.extendedProps.duration || 60) + ' دقيقة';
        }
    });
    calendar.render();

    // View Toggle
    const viewBtns = document.querySelectorAll('.view-btn');
    const calendarView = document.getElementById('calendarView');
    const listView = document.getElementById('listView');

    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            if (this.dataset.view === 'calendar') {
                calendarView.style.display = 'block';
                listView.style.display = 'none';
                calendar.render();
            } else {
                calendarView.style.display = 'none';
                listView.style.display = 'block';
            }
        });
    });
});
</script>
@endpush
