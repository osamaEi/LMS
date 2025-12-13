@extends('layouts.dashboard')

@section('title', 'الجدول الدراسي')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">الجدول الدراسي</h1>
        <p class="text-gray-600 mt-1">عرض جميع الجلسات على التقويم</p>
    </div>

    <!-- Legend -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex items-center gap-6 justify-center flex-wrap">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #3B82F6;"></div>
                <span class="text-sm text-gray-700">مجدولة</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #EF4444;"></div>
                <span class="text-sm text-gray-700">مباشر</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #10B981;"></div>
                <span class="text-sm text-gray-700">مكتملة</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded" style="background-color: #6B7280;"></div>
                <span class="text-sm text-gray-700">أخرى</span>
            </div>
        </div>
    </div>

    <!-- Calendar -->
    <div class="bg-white rounded-lg shadow p-6">
        <div id="calendar"></div>
    </div>

</div>

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<style>
    /* Calendar customization */
    .fc {
        direction: rtl;
        font-family: inherit;
    }

    .fc .fc-button {
        background-color: #3B82F6;
        border-color: #3B82F6;
        color: white;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        border-radius: 0.5rem;
        font-weight: 600;
    }

    .fc .fc-button:hover {
        background-color: #2563EB;
        border-color: #2563EB;
    }

    .fc .fc-button:disabled {
        background-color: #9CA3AF;
        border-color: #9CA3AF;
        opacity: 0.6;
    }

    .fc .fc-button-active {
        background-color: #1D4ED8 !important;
        border-color: #1D4ED8 !important;
    }

    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        color: #1F2937;
    }

    .fc-event {
        border-radius: 0.375rem;
        padding: 2px 4px;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .fc-event:hover {
        transform: scale(1.02);
        opacity: 0.9;
    }

    .fc-daygrid-day-number {
        color: #374151;
        font-weight: 600;
    }

    .fc-col-header-cell {
        background-color: #F9FAFB;
        font-weight: 700;
        color: #374151;
    }

    .fc-day-today {
        background-color: #EFF6FF !important;
    }

    .fc-daygrid-day-top {
        justify-content: center;
    }

    /* Tooltip styling */
    .fc-event-title,
    .fc-event-time {
        font-size: 0.75rem;
        white-space: normal;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var sessions = @json($sessions);

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            buttonText: {
                today: 'اليوم',
                month: 'شهر',
                week: 'أسبوع',
                day: 'يوم',
                list: 'قائمة'
            },
            locale: 'ar',
            firstDay: 6, // Saturday
            direction: 'rtl',
            height: 'auto',
            events: sessions,
            eventClick: function(info) {
                info.jsEvent.preventDefault();

                if (info.event.url) {
                    window.location.href = info.event.url;
                }
            },
            eventContent: function(arg) {
                let session = arg.event.extendedProps;

                return {
                    html: `
                        <div class="p-1">
                            <div class="font-bold text-xs">#${session.session_number} ${arg.event.title}</div>
                            <div class="text-xs mt-0.5">${session.subject}</div>
                            <div class="text-xs opacity-75">${session.type === 'live_zoom' ? 'Zoom' : 'فيديو'}</div>
                        </div>
                    `
                };
            },
            eventDidMount: function(info) {
                // Add tooltip
                info.el.title = `${info.event.title}\n${info.event.extendedProps.subject}\nالحالة: ${info.event.extendedProps.status}`;
            },
            dayMaxEvents: 3,
            moreLinkText: function(num) {
                return '+' + num + ' المزيد';
            }
        });

        calendar.render();
    });
</script>
@endsection
