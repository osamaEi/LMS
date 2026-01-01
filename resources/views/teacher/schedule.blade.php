@extends('layouts.dashboard')

@section('title', 'الجدول الدراسي')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-sky-100 via-sky-50 to-blue-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 py-8">
    <div class="container mx-auto px-4 max-w-7xl">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">الجدول الدراسي</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">عرض جميع الجلسات على التقويم</p>
        </div>

        <!-- Main Grid: Calendar (Large) + Sidebar (Small) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Calendar (9 columns) -->
        <div class="lg:col-span-9">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="p-6">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar (3 columns) -->
        <div class="lg:col-span-3">
            <div class="space-y-6">
                
                <!-- Legend Card -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-5 pb-3 border-b border-gray-200 dark:border-gray-700">المفتاح اللوني</h3>
                    
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-5 h-5 rounded-lg bg-blue-500 shadow-sm"></div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">مجدولة</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">قادمة</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-5 h-5 rounded-lg bg-red-500 shadow-sm"></div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">مباشر</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">جارية</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-5 h-5 rounded-lg bg-green-500 shadow-sm"></div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">مكتملة</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">منتهية</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-5 h-5 rounded-lg bg-gray-400 shadow-sm"></div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900 dark:text-white">أخرى</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">حالات أخرى</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-lg p-6 border border-blue-200 dark:border-blue-700/50">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">الإحصائيات</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-gray-300">إجمالي الجلسات</span>
                            <span class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ count($sessions) }}</span>
                        </div>

                        <div class="h-px bg-blue-200 dark:bg-blue-700/30"></div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-gray-300">هذا الشهر</span>
                            <span class="text-xl font-semibold text-gray-900 dark:text-white">{{ count($sessions->where('start', '>=', now()->startOfMonth())) }}</span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-700 dark:text-gray-300">القادمة</span>
                            <span class="text-xl font-semibold text-gray-900 dark:text-white">{{ count($sessions->where('start', '>=', now())) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/20 rounded-lg p-6 border border-green-200 dark:border-green-700/50">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">💡 نصيحة</h3>
                    <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                        انقر على أي جلسة لعرض التفاصيل الكاملة والانضمام إلى الجلسة المباشرة
                    </p>
                </div>

            </div>
        </div>

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
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
        border-radius: 0.4rem;
        font-weight: 600;
        text-transform: none;
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
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #1F2937;
        margin: 0 !important;
    }

    .fc-toolbar {
        gap: 0.4rem;
        padding: 0.6rem 0;
        flex-wrap: wrap;
    }

    .fc-event {
        border-radius: 0.3rem;
        padding: 2px 3px;
        cursor: pointer;
        transition: all 0.2s;
        border: none !important;
        font-size: 0.7rem;
    }

    .fc-event:hover {
        transform: translateY(-1px);
        opacity: 0.95;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .fc-daygrid-day-number {
        color: #374151;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 0.25rem;
    }

    .fc-col-header-cell {
        background-color: #F3F4F6;
        font-weight: 700;
        color: #374151;
        font-size: 0.8rem;
        padding: 0.4rem 0;
        border-color: #E5E7EB;
    }

    .fc-day-today {
        background-color: #DBEAFE !important;
    }

    .fc-daygrid-day-top {
        justify-content: center;
    }

    .fc-daygrid-day {
        min-height: 70px;
    }

    .fc-daygrid-day-cell {
        border-color: #E5E7EB;
    }

    .fc-daygrid-day-frame {
        height: auto;
    }

    .fc-event-title {
        font-size: 0.7rem;
        white-space: normal;
        font-weight: 600;
    }

    .fc-event-time {
        font-size: 0.65rem;
        white-space: normal;
    }

    /* Dark mode support */
    .dark .fc-toolbar-title {
        color: #F3F4F6;
    }

    .dark .fc-col-header-cell {
        background-color: #374151;
        color: #F3F4F6;
        border-color: #4B5563;
    }

    .dark .fc-daygrid-day-number {
        color: #E5E7EB;
    }

    .dark .fc-day-today {
        background-color: #1E40AF !important;
    }

    .dark .fc-daygrid-day-cell {
        border-color: #4B5563;
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

                // Show rich popup with details
                let props = info.event.extendedProps;
                let status = props.status;
                let statusColor = {
                    'مباشر': '#EF4444',
                    'مكتملة': '#10B981',
                    'مجدولة': '#3B82F6',
                    'أخرى': '#6B7280'
                }[status] || '#6B7280';

                // Create a popup/modal with event details
                showEventModal({
                    title: info.event.title,
                    subject: props.subject,
                    teacher: props.teacher,
                    teacher_id: props.teacher_id,
                    subject_id: props.subject_id,
                    status: status,
                    statusColor: statusColor,
                    type: props.type,
                    session_number: props.session_number,
                    start: info.event.start,
                    url: info.event.url
                });
            },
            eventContent: function(arg) {
                let session = arg.event.extendedProps;
                let statusBadge = {
                    'مباشر': '<span class="inline-block w-2 h-2 rounded-full bg-red-500 mr-1"></span>',
                    'مكتملة': '<span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-1"></span>',
                    'مجدولة': '<span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-1"></span>',
                    'أخرى': '<span class="inline-block w-2 h-2 rounded-full bg-gray-500 mr-1"></span>'
                }[session.status] || '';

                return {
                    html: `
                        <div class="p-1.5">
                            <div class="font-bold text-xs text-white">${statusBadge}#${session.session_number} ${arg.event.title}</div>
                            <div class="text-xs text-white/90 mt-0.5">${session.subject}</div>
                            <div class="text-xs text-white/75">${session.type === 'live_zoom' ? '🎥 Zoom' : '📹 فيديو'}</div>
                        </div>
                    `
                };
            },
            eventDidMount: function(info) {
                let session = info.event.extendedProps;
                // Add rich tooltip
                info.el.title = `${info.event.title}\n${session.subject}\nالمعلم: ${session.teacher}\nالحالة: ${session.status}`;
                info.el.style.cursor = 'pointer';
                info.el.style.opacity = '0.95';
            },
            dayMaxEvents: 3,
            moreLinkText: function(num) {
                return '+' + num + ' المزيد';
            }
        });

        calendar.render();
    });

    // Function to show event details modal
    function showEventModal(event) {
        const statusColor = {
            'مباشر': 'bg-red-100 text-red-800 border-red-300',
            'مكتملة': 'bg-green-100 text-green-800 border-green-300',
            'مجدولة': 'bg-blue-100 text-blue-800 border-blue-300',
            'أخرى': 'bg-gray-100 text-gray-800 border-gray-300'
        }[event.status] || 'bg-gray-100 text-gray-800 border-gray-300';

        const statusBg = {
            'مباشر': 'bg-red-500',
            'مكتملة': 'bg-green-500',
            'مجدولة': 'bg-blue-500',
            'أخرى': 'bg-gray-500'
        }[event.status] || 'bg-gray-500';

        const startTime = event.start ? new Date(event.start).toLocaleString('ar-SA', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }) : 'غير محدد';

        const modal = document.createElement('div');
        modal.innerHTML = `
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" onclick="this.remove()">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-md w-full mx-4" onclick="event.stopPropagation()">
                    <!-- Header with status color -->
                    <div class="h-2 ${statusBg}"></div>
                    
                    <div class="p-6">
                        <!-- Title -->
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                            ${event.title}
                        </h2>

                        <!-- Status Badge -->
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold ${statusColor} border mb-4">
                            ${event.status}
                        </span>

                        <!-- Subject -->
                        <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">المادة الدراسية</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                ${event.subject}
                            </p>
                        </div>

                        <!-- Teacher -->
                        <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">المعلم</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">
                                👨‍🏫 ${event.teacher}
                            </p>
                        </div>

                        <!-- Session Details -->
                        <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">رقم الجلسة</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">#${event.session_number}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">النوع</p>
                                    <p class="font-semibold text-gray-900 dark:text-white">
                                        ${event.type === 'live_zoom' ? '🎥 Zoom' : '📹 فيديو'}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Date and Time -->
                        <div class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">التاريخ والوقت</p>
                            <p class="font-semibold text-gray-900 dark:text-white">📅 ${startTime}</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <a href="${event.url}" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center font-semibold">
                                عرض التفاصيل
                            </a>
                            <button onclick="this.closest('div').parentElement.parentElement.remove()" class="flex-1 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition font-semibold">
                                إغلاق
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
    }
</script>
@endsection
