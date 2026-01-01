@extends('layouts.dashboard')

@section('title', 'الجدول الدراسي')

@section('content')
<div class="py-6">
    <div class="mx-auto px-4 max-w-7xl">

        <div class="mb-6 p-6 rounded-xl shadow-lg" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
            <h1 class="text-2xl font-bold text-white">الجدول الدراسي</h1>
            <p class="mt-1" style="color: rgba(255,255,255,0.8);">عرض جميع الجلسات على التقويم</p>
        </div>

        <!-- Statistics Row - Above Calendar -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <!-- Statistics Card -->
            <div class="rounded-lg p-5 shadow-lg" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-white">إجمالي الجلسات</span>
                    <span class="text-3xl font-bold text-white">{{ count($sessions) }}</span>
                </div>
            </div>

            <!-- This Month Card -->
            <div class="rounded-lg p-5 shadow-lg" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-white">جلسات هذا الشهر</span>
                    <span class="text-3xl font-bold text-white">{{ count($sessions->where('start', '>=', now()->startOfMonth())) }}</span>
                </div>
            </div>

            <!-- Upcoming Card -->
            <div class="rounded-lg p-5 shadow-lg" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-white">الجلسات القادمة</span>
                    <span class="text-3xl font-bold text-white">{{ count($sessions->where('start', '>=', now())) }}</span>
                </div>
            </div>
        </div>

        <!-- Main Grid: Calendar + Legend -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Calendar (9 columns) -->
        <div class="lg:col-span-9 order-2 lg:order-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="p-6">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Legend Sidebar (3 columns) -->
        <div class="lg:col-span-3 order-1 lg:order-2">
            <div class="space-y-4">

                <!-- Legend Card -->
                <div class="rounded-lg shadow-lg p-5" style="background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);">
                    <h3 class="text-base font-bold text-white mb-4 pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.2);">المفتاح اللوني</h3>

                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-4 h-4 rounded" style="background: #3b82f6;"></div>
                            <span class="text-sm text-white">مجدولة (قادمة)</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-4 h-4 rounded" style="background: #ef4444;"></div>
                            <span class="text-sm text-white">مباشر (جارية)</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-4 h-4 rounded" style="background: #10b981;"></div>
                            <span class="text-sm text-white">مكتملة (منتهية)</span>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-4 h-4 rounded" style="background: #9ca3af;"></div>
                            <span class="text-sm text-white">أخرى</span>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="rounded-lg p-5 shadow-lg" style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);">
                    <h3 class="text-sm font-bold text-white mb-2">💡 نصيحة</h3>
                    <p class="text-xs leading-relaxed" style="color: rgba(255,255,255,0.9);">
                        انقر على أي جلسة لعرض التفاصيل والانضمام للبث المباشر
                    </p>
                </div>

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
    /* Calendar Container */
    .fc {
        direction: rtl;
        font-family: inherit;
        background: white;
        border-radius: 12px;
    }

    /* Header Toolbar */
    .fc-toolbar {
        padding: 1rem;
        margin-bottom: 0 !important;
        background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
        border-radius: 12px 12px 0 0;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        margin: 0 !important;
    }

    /* Toolbar Buttons - Navigation Arrows */
    .fc .fc-button {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        border: none !important;
        color: white !important;
        padding: 0.6rem 1rem;
        font-size: 0.875rem;
        border-radius: 8px;
        font-weight: 600;
        text-transform: none;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }

    .fc .fc-button:hover {
        background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
    }

    .fc .fc-button-primary:disabled {
        background: rgba(255,255,255,0.2) !important;
        color: rgba(255,255,255,0.5) !important;
        box-shadow: none;
        cursor: not-allowed;
    }

    .fc .fc-button-active,
    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.4);
    }

    /* Today Button */
    .fc .fc-today-button {
        background: linear-gradient(135deg, #10b981, #059669) !important;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .fc .fc-today-button:hover {
        background: linear-gradient(135deg, #059669, #047857) !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .fc .fc-today-button:disabled {
        background: rgba(255,255,255,0.2) !important;
        color: rgba(255,255,255,0.5) !important;
        box-shadow: none;
    }

    /* Navigation Prev/Next Buttons */
    .fc .fc-prev-button,
    .fc .fc-next-button {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
        min-width: 40px;
        display: flex !important;
        align-items: center;
        justify-content: center;
    }

    .fc .fc-prev-button:hover,
    .fc .fc-next-button:hover {
        background: linear-gradient(135deg, #d97706, #b45309) !important;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
    }

    /* Ensure arrow icons are visible */
    .fc .fc-prev-button .fc-icon,
    .fc .fc-next-button .fc-icon,
    .fc .fc-button .fc-icon {
        color: white !important;
        font-size: 1.2rem !important;
        display: inline-block !important;
    }

    .fc .fc-icon-chevron-left:before,
    .fc .fc-icon-chevron-right:before {
        color: white !important;
    }

    /* View Buttons Group */
    .fc .fc-button-group .fc-button {
        background: rgba(255,255,255,0.15) !important;
        border: 1px solid rgba(255,255,255,0.3) !important;
        box-shadow: none;
    }

    .fc .fc-button-group .fc-button:hover {
        background: rgba(255,255,255,0.25) !important;
    }

    .fc .fc-button-group .fc-button-active {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed) !important;
        border-color: #8b5cf6 !important;
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.4);
    }

    /* Column Headers (Days) */
    .fc-col-header-cell {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        font-weight: 700;
        color: #1e3a5f;
        font-size: 0.9rem;
        padding: 0.75rem 0;
        border-color: #e2e8f0;
    }

    .fc-col-header-cell-cushion {
        color: #1e3a5f;
        font-weight: 700;
    }

    /* Day Cells */
    .fc-daygrid-day {
        min-height: 90px;
        transition: all 0.2s;
    }

    .fc-daygrid-day:hover {
        background-color: #f8fafc;
    }

    .fc-daygrid-day-frame {
        height: auto;
        padding: 4px;
    }

    .fc-daygrid-day-top {
        justify-content: center;
        padding: 4px;
    }

    .fc-daygrid-day-number {
        color: #475569;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 4px 8px;
        border-radius: 50%;
        transition: all 0.2s;
    }

    .fc-daygrid-day:hover .fc-daygrid-day-number {
        background: #e2e8f0;
    }

    /* Today */
    .fc-day-today {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
    }

    .fc-day-today .fc-daygrid-day-number {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white !important;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Grid Borders */
    .fc-scrollgrid {
        border: none !important;
        border-radius: 0 0 12px 12px;
        overflow: hidden;
    }

    .fc-scrollgrid td,
    .fc-scrollgrid th {
        border-color: #e2e8f0 !important;
    }

    /* Events */
    .fc-event {
        border-radius: 6px;
        padding: 4px 8px;
        margin: 2px 4px;
        cursor: pointer;
        transition: all 0.2s;
        border: none !important;
        font-size: 0.75rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .fc-event:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .fc-event-title {
        font-size: 0.75rem;
        white-space: normal;
        font-weight: 600;
    }

    .fc-event-time {
        font-size: 0.7rem;
        white-space: normal;
        opacity: 0.9;
    }

    /* More Link */
    .fc-daygrid-more-link {
        color: #3b82f6;
        font-weight: 600;
        font-size: 0.75rem;
    }

    /* Previous Month Days */
    .fc-day-other .fc-daygrid-day-number {
        color: #cbd5e1;
    }

    /* Scrollbar */
    .fc-scroller::-webkit-scrollbar {
        width: 6px;
    }

    .fc-scroller::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .fc-scroller::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    /* Dark mode support */
    .dark .fc {
        background: #1f2937;
    }

    .dark .fc-toolbar-title {
        color: #f9fafb !important;
    }

    .dark .fc-col-header-cell {
        background: linear-gradient(135deg, #374151, #1f2937);
        color: #f9fafb;
        border-color: #4b5563;
    }

    .dark .fc-col-header-cell-cushion {
        color: #f9fafb;
    }

    .dark .fc-daygrid-day-number {
        color: #e5e7eb;
    }

    .dark .fc-day-today {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%) !important;
    }

    .dark .fc-scrollgrid td,
    .dark .fc-scrollgrid th {
        border-color: #4b5563 !important;
    }

    .dark .fc-daygrid-day:hover {
        background-color: #374151;
    }

    .dark .fc-day-other .fc-daygrid-day-number {
        color: #6b7280;
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
