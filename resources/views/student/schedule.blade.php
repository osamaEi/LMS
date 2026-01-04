@extends('layouts.dashboard')

@section('title', 'الجدول الدراسي')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="rounded-2xl p-6 text-white shadow-lg" style="background: linear-gradient(to left, #0071AA, #005a88);">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">الجدول الدراسي</h1>
                <p class="text-cyan-100 mt-1">تابع جلساتك ومواعيدك الدراسية</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 text-sm">
                    <span class="opacity-80">إجمالي الجلسات:</span>
                    <span class="font-bold mr-1">{{ count($sessions) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="rounded-2xl p-6 shadow-lg" style="background: linear-gradient(to left, #0071AA, #005a88);">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                    <svg class="w-7 h-7" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold" style="color: white;">{{ count($sessions) }}</p>
                    <p class="text-sm" style="color: rgba(255,255,255,0.8);">إجمالي الجلسات</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                    <svg class="w-7 h-7" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold" style="color: white;" id="completed-count">0</p>
                    <p class="text-sm" style="color: rgba(255,255,255,0.8);">جلسات مكتملة</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                    <svg class="w-7 h-7" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold" style="color: white;" id="upcoming-count">0</p>
                    <p class="text-sm" style="color: rgba(255,255,255,0.8);">جلسات قادمة</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.2);">
                    <svg class="w-7 h-7" style="color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-3xl font-bold" style="color: white;" id="live-count">0</p>
                    <p class="text-sm" style="color: rgba(255,255,255,0.8);">جلسات مباشرة</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Calendar First on Mobile -->
        <div class="xl:col-span-3 order-2 xl:order-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <!-- Calendar Header -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <!-- Navigation -->
                        <div class="flex items-center gap-2">
                            <button onclick="calendar.prev()" class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center justify-center transition shadow-sm">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                            <button onclick="calendar.next()" class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center justify-center transition shadow-sm">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <button onclick="calendar.today()" class="px-4 h-10 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 text-white font-medium hover:from-cyan-600 hover:to-blue-700 transition shadow-sm">
                                اليوم
                            </button>
                        </div>

                        <!-- Title -->
                        <h2 id="calendar-title" class="text-xl font-bold text-gray-900 dark:text-white order-first sm:order-none"></h2>

                        <!-- View Toggle -->
                        <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-xl p-1">
                            <button onclick="changeView('dayGridMonth')" id="btn-month" class="view-btn active px-3 py-2 rounded-lg text-sm font-medium transition-all">
                                شهر
                            </button>
                            <button onclick="changeView('timeGridWeek')" id="btn-week" class="view-btn px-3 py-2 rounded-lg text-sm font-medium transition-all">
                                أسبوع
                            </button>
                            <button onclick="changeView('timeGridDay')" id="btn-day" class="view-btn px-3 py-2 rounded-lg text-sm font-medium transition-all">
                                يوم
                            </button>
                            <button onclick="changeView('listWeek')" id="btn-list" class="view-btn px-3 py-2 rounded-lg text-sm font-medium transition-all">
                                قائمة
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Calendar Container -->
                <div class="p-4">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="xl:col-span-1 order-1 xl:order-2 space-y-6">
            <!-- Legend Card -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                    دليل الألوان
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="w-4 h-4 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 shadow-sm"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-300">جلسة مجدولة</span>
                    </div>
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="w-4 h-4 rounded-full bg-gradient-to-r from-red-500 to-red-600 shadow-sm animate-pulse"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-300">جلسة مباشرة الآن</span>
                    </div>
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="w-4 h-4 rounded-full bg-gradient-to-r from-green-500 to-green-600 shadow-sm"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-300">جلسة مكتملة</span>
                    </div>
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <div class="w-4 h-4 rounded-full bg-gradient-to-r from-gray-400 to-gray-500 shadow-sm"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-300">أخرى</span>
                    </div>
                </div>
            </div>

            <!-- Today Card -->
            <div class="bg-gradient-to-br from-cyan-500 via-cyan-600 to-blue-600 rounded-2xl p-5 shadow-lg text-white relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full opacity-10">
                    <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                        <circle cx="80" cy="20" r="40" fill="white"/>
                        <circle cx="20" cy="80" r="30" fill="white"/>
                    </svg>
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-lg">اليوم</h3>
                        <span class="text-sm bg-white/20 px-3 py-1 rounded-full">{{ now()->locale('ar')->translatedFormat('l') }}</span>
                    </div>
                    <div class="text-5xl font-bold mb-1">{{ now()->format('d') }}</div>
                    <div class="text-cyan-100 mb-4">{{ now()->locale('ar')->translatedFormat('F Y') }}</div>

                    <div class="pt-4 border-t border-white/20 space-y-2">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-white"></div>
                            <span class="text-sm" id="today-sessions">جاري التحميل...</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-gray-100 dark:border-gray-700">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">إجراءات سريعة</h3>
                <div class="space-y-2">
                    <button onclick="calendar.today()" class="w-full flex items-center gap-3 p-3 rounded-xl bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300 hover:bg-cyan-100 dark:hover:bg-cyan-900/30 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="font-medium">الذهاب لليوم</span>
                    </button>
                    <button onclick="changeView('listWeek')" class="w-full flex items-center gap-3 p-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                        </svg>
                        <span class="font-medium">عرض القائمة</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Event Modal -->
<div id="event-modal" class="fixed inset-0 z-50 hidden">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeEventModal()"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4 pointer-events-none">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full p-6 pointer-events-auto transform transition-all" id="modal-content">
            <!-- Modal content will be injected here -->
        </div>
    </div>
</div>

<!-- FullCalendar CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<style>
    /* View Toggle Buttons */
    .view-btn {
        color: #6B7280;
        background: transparent;
    }
    .view-btn:hover {
        color: #0891B2;
    }
    .view-btn.active {
        color: white;
        background: linear-gradient(135deg, #06B6D4, #2563EB);
        box-shadow: 0 2px 8px rgba(6, 182, 212, 0.4);
    }

    /* Calendar Base */
    .fc {
        direction: rtl;
        font-family: inherit;
    }

    #calendar {
        min-height: 500px;
    }

    .fc .fc-toolbar {
        display: none;
    }

    .fc .fc-view-harness {
        min-height: 450px;
    }

    /* Header Cells */
    .fc-col-header-cell {
        background: linear-gradient(180deg, #F8FAFC 0%, #F1F5F9 100%);
        padding: 14px 0 !important;
        font-weight: 700;
        color: #475569;
        font-size: 0.875rem;
        border: none !important;
    }

    .dark .fc-col-header-cell {
        background: linear-gradient(180deg, #1E293B 0%, #0F172A 100%);
        color: #94A3B8;
    }

    /* Table Borders */
    .fc-theme-standard td,
    .fc-theme-standard th {
        border-color: #E2E8F0;
    }

    .dark .fc-theme-standard td,
    .dark .fc-theme-standard th {
        border-color: #334155;
    }

    .fc-theme-standard .fc-scrollgrid {
        border: none;
    }

    /* Day Cells */
    .fc-daygrid-day {
        transition: all 0.2s ease;
    }

    .fc-daygrid-day:hover {
        background: linear-gradient(180deg, rgba(6, 182, 212, 0.05) 0%, rgba(6, 182, 212, 0.1) 100%) !important;
    }

    .fc-daygrid-day-number {
        color: #475569;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 10px !important;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 4px auto;
        border-radius: 10px;
        transition: all 0.2s;
    }

    .dark .fc-daygrid-day-number {
        color: #CBD5E1;
    }

    .fc-daygrid-day-top {
        justify-content: center;
    }

    /* Today Highlight */
    .fc-day-today {
        background: linear-gradient(180deg, rgba(6, 182, 212, 0.08) 0%, rgba(37, 99, 235, 0.08) 100%) !important;
    }

    .fc-day-today .fc-daygrid-day-number {
        background: linear-gradient(135deg, #06B6D4, #2563EB);
        color: white !important;
        font-weight: 700;
        box-shadow: 0 4px 12px rgba(6, 182, 212, 0.4);
    }

    /* Events */
    .fc-event {
        border: none !important;
        border-radius: 8px !important;
        padding: 4px 8px !important;
        margin: 2px 4px !important;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 0.75rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .fc-event:hover {
        transform: translateY(-2px) scale(1.02);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .fc-event-main {
        padding: 2px 0;
    }

    /* Event Types */
    .event-scheduled {
        background: linear-gradient(135deg, #3B82F6, #2563EB) !important;
    }

    .event-live {
        background: linear-gradient(135deg, #EF4444, #DC2626) !important;
        animation: pulse-live 2s ease-in-out infinite;
    }

    .event-completed {
        background: linear-gradient(135deg, #10B981, #059669) !important;
    }

    .event-default {
        background: linear-gradient(135deg, #6B7280, #4B5563) !important;
    }

    @keyframes pulse-live {
        0%, 100% {
            opacity: 1;
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        }
        50% {
            opacity: 0.85;
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.5);
        }
    }

    /* Time Grid */
    .fc-timegrid-slot {
        height: 52px !important;
    }

    .fc-timegrid-slot-label {
        font-size: 0.75rem;
        color: #64748B;
        font-weight: 500;
    }

    .fc-timegrid-now-indicator-line {
        border-color: #EF4444;
        border-width: 2px;
    }

    .fc-timegrid-now-indicator-arrow {
        border-color: #EF4444;
    }

    /* List View */
    .fc-list {
        border: none !important;
    }

    .fc-list-event {
        background: transparent !important;
    }

    .fc-list-event:hover td {
        background: linear-gradient(90deg, rgba(6, 182, 212, 0.05) 0%, rgba(6, 182, 212, 0.1) 100%) !important;
    }

    .fc-list-day-cushion {
        background: linear-gradient(90deg, #F8FAFC 0%, #F1F5F9 100%) !important;
        font-weight: 700;
        padding: 12px 16px !important;
    }

    .dark .fc-list-day-cushion {
        background: linear-gradient(90deg, #1E293B 0%, #0F172A 100%) !important;
    }

    .fc-list-event-title {
        font-weight: 600;
    }

    /* More Link */
    .fc-daygrid-more-link {
        color: #0891B2;
        font-weight: 600;
        font-size: 0.75rem;
        padding: 2px 6px;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .fc-daygrid-more-link:hover {
        background: rgba(6, 182, 212, 0.1);
        color: #0E7490;
    }

    /* Popover */
    .fc-popover {
        border-radius: 16px !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15) !important;
        border: 1px solid #E2E8F0 !important;
        overflow: hidden;
    }

    .fc-popover-header {
        background: linear-gradient(135deg, #06B6D4, #2563EB) !important;
        color: white !important;
        padding: 12px 16px !important;
        font-weight: 600;
    }

    .fc-popover-body {
        padding: 8px !important;
    }

    /* Scrollbar */
    .fc-scroller::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .fc-scroller::-webkit-scrollbar-track {
        background: #F1F5F9;
        border-radius: 3px;
    }

    .fc-scroller::-webkit-scrollbar-thumb {
        background: #CBD5E1;
        border-radius: 3px;
    }

    .fc-scroller::-webkit-scrollbar-thumb:hover {
        background: #94A3B8;
    }

    /* Modal Animation */
    #event-modal.show #modal-content {
        animation: modalIn 0.3s ease-out;
    }

    @keyframes modalIn {
        from {
            opacity: 0;
            transform: scale(0.95) translateY(10px);
        }
        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }
</style>

<script>
    var calendar;
    var sessionsData = @json($sessions);

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        // Process sessions for calendar
        var events = sessionsData.map(function(session) {
            var statusClass = 'event-default';
            if (session.extendedProps) {
                switch(session.extendedProps.status) {
                    case 'live': statusClass = 'event-live'; break;
                    case 'completed': statusClass = 'event-completed'; break;
                    case 'scheduled': statusClass = 'event-scheduled'; break;
                }
            }
            return {
                ...session,
                className: statusClass
            };
        });

        // Update stats
        updateStats(events);

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'ar',
            firstDay: 6,
            direction: 'rtl',
            height: 'auto',
            events: events,
            dayMaxEvents: 3,
            nowIndicator: true,
            moreLinkText: function(num) {
                return '+' + num + ' جلسات';
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                showEventModal(info.event);
            },
            eventContent: function(arg) {
                var props = arg.event.extendedProps || {};
                var icon = props.type === 'live_zoom'
                    ? '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>'
                    : '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>';

                return {
                    html: '<div class="flex items-center gap-1 text-white font-medium truncate">' + icon + '<span>' + arg.event.title + '</span></div>'
                };
            },
            eventDidMount: function(info) {
                var props = info.event.extendedProps || {};
                info.el.title = info.event.title + '\n' + (props.subject || '') + '\nالحالة: ' + (props.status || 'غير محدد');
            },
            datesSet: function(info) {
                updateCalendarTitle();
            }
        });

        calendar.render();
        updateCalendarTitle();
    });

    function updateStats(events) {
        var completed = 0, upcoming = 0, live = 0;
        var today = new Date().toDateString();
        var todaySessions = 0;

        events.forEach(function(event) {
            var props = event.extendedProps || {};
            switch(props.status) {
                case 'completed': completed++; break;
                case 'scheduled': upcoming++; break;
                case 'live': live++; break;
            }

            if (event.start && new Date(event.start).toDateString() === today) {
                todaySessions++;
            }
        });

        document.getElementById('completed-count').textContent = completed;
        document.getElementById('upcoming-count').textContent = upcoming;
        document.getElementById('live-count').textContent = live;
        document.getElementById('today-sessions').textContent = todaySessions + ' جلسات اليوم';
    }

    function updateCalendarTitle() {
        var view = calendar.view;
        document.getElementById('calendar-title').textContent = view.title;
    }

    function changeView(viewName) {
        calendar.changeView(viewName);

        document.querySelectorAll('.view-btn').forEach(function(btn) {
            btn.classList.remove('active');
        });

        var btnMap = {
            'dayGridMonth': 'btn-month',
            'timeGridWeek': 'btn-week',
            'timeGridDay': 'btn-day',
            'listWeek': 'btn-list'
        };

        var btn = document.getElementById(btnMap[viewName]);
        if (btn) btn.classList.add('active');

        updateCalendarTitle();
    }

    function showEventModal(event) {
        var props = event.extendedProps || {};
        var statusColors = {
            'scheduled': 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'live': 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'completed': 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
        };
        var statusLabels = {
            'scheduled': 'مجدولة',
            'live': 'مباشرة',
            'completed': 'مكتملة'
        };

        var statusClass = statusColors[props.status] || 'bg-gray-100 text-gray-700';
        var statusLabel = statusLabels[props.status] || 'غير محدد';

        var startTime = event.start ? new Date(event.start).toLocaleString('ar-SA', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }) : 'غير محدد';

        var modalContent = document.getElementById('modal-content');
        modalContent.innerHTML =
            '<button onclick="closeEventModal()" class="absolute top-4 left-4 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center justify-center transition">' +
                '<svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>' +
            '</button>' +
            '<div class="text-center mb-6">' +
                '<div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center mb-4 shadow-lg">' +
                    '<svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>' +
                '</div>' +
                '<h3 class="text-xl font-bold text-gray-900 dark:text-white">' + event.title + '</h3>' +
                '<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mt-2 ' + statusClass + '">' + statusLabel + '</span>' +
            '</div>' +
            '<div class="space-y-3 mb-6">' +
                '<div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl">' +
                    '<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>' +
                    '<div><p class="text-xs text-gray-500 dark:text-gray-400">المادة</p><p class="font-medium text-gray-900 dark:text-white">' + (props.subject || 'غير محدد') + '</p></div>' +
                '</div>' +
                '<div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl">' +
                    '<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' +
                    '<div><p class="text-xs text-gray-500 dark:text-gray-400">الموعد</p><p class="font-medium text-gray-900 dark:text-white">' + startTime + '</p></div>' +
                '</div>' +
                '<div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl">' +
                    '<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>' +
                    '<div><p class="text-xs text-gray-500 dark:text-gray-400">رقم الجلسة</p><p class="font-medium text-gray-900 dark:text-white">#' + (props.session_number || '1') + '</p></div>' +
                '</div>' +
            '</div>' +
            '<div class="flex gap-3">' +
                (props.status === 'live' || props.status === 'scheduled' ?
                    '<button class="flex-1 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-600 hover:to-blue-700 text-white font-medium py-3 px-4 rounded-xl transition flex items-center justify-center gap-2 shadow-lg">' +
                        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>' +
                        'انضم الآن' +
                    '</button>'
                :
                    '<button class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium py-3 px-4 rounded-xl transition flex items-center justify-center gap-2 shadow-lg">' +
                        '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' +
                        'شاهد التسجيل' +
                    '</button>'
                ) +
                '<button onclick="closeEventModal()" class="px-4 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-xl transition">' +
                    'إغلاق' +
                '</button>' +
            '</div>';

        document.getElementById('event-modal').classList.remove('hidden');
        document.getElementById('event-modal').classList.add('show');
    }

    function closeEventModal() {
        document.getElementById('event-modal').classList.add('hidden');
        document.getElementById('event-modal').classList.remove('show');
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeEventModal();
    });
</script>
@endsection
