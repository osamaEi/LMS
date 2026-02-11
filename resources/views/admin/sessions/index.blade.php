@extends('layouts.dashboard')

@section('title', 'إدارة الجلسات')

@push('styles')
<style>
    .sessions-page { max-width: 1400px; margin: 0 auto; }

    /* Header */
    .sessions-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .sessions-header::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .sessions-header::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -5%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }
    .header-title { font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem; position: relative; z-index: 1; }
    .header-subtitle { font-size: 0.95rem; opacity: 0.95; position: relative; z-index: 1; }
    .header-actions { display: flex; gap: 0.75rem; flex-wrap: wrap; position: relative; z-index: 1; }
    .header-btn {
        display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.25rem;
        border-radius: 12px; font-weight: 700; font-size: 0.9rem; border: none;
        cursor: pointer; transition: all 0.2s; color: #fff; text-decoration: none;
        background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.25);
    }
    .header-btn:hover { background: rgba(255,255,255,0.3); transform: translateY(-1px); }
    .header-btn svg { width: 20px; height: 20px; }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: #fff; border-radius: 18px; padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        display: flex; align-items: center; gap: 1.25rem; transition: all 0.2s;
    }
    .dark .stat-card { background: #1f2937; }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .stat-icon svg { width: 26px; height: 26px; color: #fff; }
    .stat-label { font-size: 0.875rem; color: #6b7280; font-weight: 500; margin-bottom: 0.25rem; }
    .dark .stat-label { color: #9ca3af; }
    .stat-value { font-size: 1.75rem; font-weight: 800; color: #111827; line-height: 1; }
    .dark .stat-value { color: #f9fafb; }

    /* Table Card */
    .table-card {
        background: #fff; border-radius: 18px; overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
    }
    .dark .table-card { background: #1f2937; }

    .sessions-table { width: 100%; border-collapse: collapse; }
    .sessions-table thead {
        background: #f9fafb; border-bottom: 2px solid #e5e7eb;
    }
    .dark .sessions-table thead { background: #111827; border-color: #374151; }
    .sessions-table th {
        padding: 1rem 1.5rem; text-align: right; font-size: 0.75rem;
        font-weight: 700; color: #374151; text-transform: uppercase; letter-spacing: 0.05em;
    }
    .dark .sessions-table th { color: #d1d5db; }
    .sessions-table tbody tr {
        border-bottom: 1px solid #f3f4f6; transition: background 0.15s;
    }
    .dark .sessions-table tbody tr { border-color: #374151; }
    .sessions-table tbody tr:hover { background: #f9fafb; }
    .dark .sessions-table tbody tr:hover { background: #111827; }
    .sessions-table td { padding: 1.25rem 1.5rem; font-size: 0.9rem; color: #1f2937; }
    .dark .sessions-table td { color: #f3f4f6; }

    /* Badges */
    .badge { display: inline-flex; align-items: center; gap: 6px; padding: 0.375rem 0.875rem; border-radius: 999px; font-size: 0.8125rem; font-weight: 700; }
    .badge-live { background: #fef2f2; color: #dc2626; }
    .dark .badge-live { background: rgba(239, 68, 68, 0.15); color: #fca5a5; }
    .badge-scheduled { background: #eff6ff; color: #2563eb; }
    .dark .badge-scheduled { background: rgba(59, 130, 246, 0.15); color: #93c5fd; }
    .badge-completed { background: #f0fdf4; color: #16a34a; }
    .dark .badge-completed { background: rgba(16, 185, 129, 0.15); color: #86efac; }
    .badge-dot { width: 8px; height: 8px; border-radius: 50%; }

    /* Action Buttons */
    .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.625rem 1rem; border-radius: 10px; font-weight: 600; font-size: 0.875rem; transition: all 0.15s; cursor: pointer; border: none; text-decoration: none; }
    .btn-view { background: #dbeafe; color: #1e40af; }
    .btn-view:hover { background: #bfdbfe; transform: translateY(-1px); }
    .btn-edit { background: #fef3c7; color: #92400e; }
    .btn-edit:hover { background: #fde68a; transform: translateY(-1px); }
    .btn-delete { background: #fee2e2; color: #991b1b; }
    .btn-delete:hover { background: #fecaca; transform: translateY(-1px); }

    /* Empty State */
    .empty-state { text-align: center; padding: 4rem 2rem; }
    .empty-state svg { width: 80px; height: 80px; margin: 0 auto 1.5rem; color: #d1d5db; }
    .dark .empty-state svg { color: #4b5563; }
    .empty-state h3 { font-size: 1.25rem; font-weight: 700; color: #111827; margin-bottom: 0.5rem; }
    .dark .empty-state h3 { color: #f9fafb; }
    .empty-state p { color: #6b7280; }
    .dark .empty-state p { color: #9ca3af; }

    /* Alert */
    .alert-success {
        background: #d1fae5; border: 2px solid #a7f3d0; color: #065f46;
        padding: 1rem 1.5rem; border-radius: 14px; margin-bottom: 1.5rem; font-weight: 600;
    }
    .dark .alert-success { background: rgba(16, 185, 129, 0.1); border-color: rgba(16, 185, 129, 0.3); color: #d1fae5; }
    .alert-error {
        background: #fef2f2; border: 2px solid #fecaca; color: #991b1b;
        padding: 1rem 1.5rem; border-radius: 14px; margin-bottom: 1.5rem; font-weight: 600;
    }
    .dark .alert-error { background: rgba(239, 68, 68, 0.1); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5; }

    /* Subject Color Dot */
    .subject-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-left: 0.5rem; flex-shrink: 0; }

    @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
    .badge-live .badge-dot { animation: pulse-dot 1.5s ease-in-out infinite; }

    /* Modal Styles - Keep for Add Session */
    .modal-backdrop {
        position: fixed; inset: 0; background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px); z-index: 1000; display: none;
        align-items: center; justify-content: center; padding: 1rem;
    }
    .modal-backdrop.active { display: flex; }
    .modal-container {
        background: white; border-radius: 24px; width: 100%; max-width: 560px;
        max-height: 90vh; overflow: hidden; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        animation: modalSlide 0.3s ease;
    }
    .dark .modal-container { background: #1f2937; }
    @keyframes modalSlide { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
    .modal-header { background: linear-gradient(135deg, #0071AA 0%, #005a88 100%); padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; }
    .modal-close { width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; transition: all 0.2s; cursor: pointer; border: none; }
    .modal-close:hover { background: rgba(255,255,255,0.3); }
    .modal-body { padding: 1.5rem; max-height: 60vh; overflow-y: auto; }
    .modal-footer { padding: 1.25rem 1.5rem; background: #f9fafb; border-top: 1px solid #e5e7eb; display: flex; gap: 0.75rem; justify-content: flex-end; }
    .dark .modal-footer { background: #111827; border-color: #374151; }
    .form-group { margin-bottom: 1.25rem; }
    .form-label { display: block; font-weight: 600; color: #374151; margin-bottom: 0.5rem; font-size: 0.875rem; }
    .dark .form-label { color: #d1d5db; }
    .form-input, .form-select { width: 100%; padding: 0.75rem 1rem; border: 2px solid #e5e7eb; border-radius: 12px; font-size: 0.9rem; transition: all 0.2s; background: white; color: #1f2937; }
    .dark .form-input, .dark .form-select { background: #111827; border-color: #374151; color: #f9fafb; }
    .form-input:focus, .form-select:focus { outline: none; border-color: #0071AA; box-shadow: 0 0 0 4px rgba(0, 113, 170, 0.1); }
    .btn-secondary { padding: 0.75rem 1.5rem; background: white; border: 2px solid #e5e7eb; border-radius: 12px; font-weight: 600; color: #374151; cursor: pointer; transition: all 0.2s; }
    .dark .btn-secondary { background: #374151; border-color: #4b5563; color: #d1d5db; }
    .btn-secondary:hover { background: #f3f4f6; }
    .btn-primary { padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #0071AA 0%, #005a88 100%); border: none; border-radius: 12px; font-weight: 600; color: white; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 0.5rem; }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 113, 170, 0.3); }

    .recurrence-toggle { display: flex; gap: 0.5rem; padding: 4px; background: #f3f4f6; border-radius: 12px; }
    .dark .recurrence-toggle { background: #111827; }
    .recurrence-option { flex: 1; padding: 0.625rem 1rem; border-radius: 10px; font-weight: 600; font-size: 0.875rem; text-align: center; cursor: pointer; transition: all 0.2s; color: #6b7280; background: transparent; border: none; }
    .recurrence-option.active { background: white; color: #0071AA; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .dark .recurrence-option.active { background: #374151; color: #60a5fa; }
    .day-selector { display: flex; flex-wrap: wrap; gap: 0.5rem; }
    .day-btn { width: 44px; height: 44px; border-radius: 12px; border: 2px solid #e5e7eb; background: white; color: #6b7280; font-weight: 600; font-size: 0.75rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
    .dark .day-btn { background: #111827; border-color: #374151; color: #9ca3af; }
    .day-btn:hover { border-color: #0071AA; color: #0071AA; }
    .day-btn.active { background: linear-gradient(135deg, #0071AA 0%, #005a88 100%); border-color: transparent; color: white; }
    .options-panel { background: #f9fafb; border-radius: 12px; padding: 1rem; margin-top: 1rem; }
    .dark .options-panel { background: #111827; }
</style>
@endpush

@section('content')
<div class="sessions-page">
    <!-- Header -->
    <div class="sessions-header">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; position: relative; z-index: 1;">
            <div>
                <h1 class="header-title">إدارة الجلسات</h1>
                <p class="header-subtitle">عرض وإدارة جميع جلسات Zoom</p>
            </div>
            <div class="header-actions">
                <button onclick="showCreateModal()" class="header-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إضافة جلسة
                </button>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
    @endif

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">إجمالي الجلسات</div>
                <div class="stat-value">{{ $sessions->total() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">مباشر الآن</div>
                <div class="stat-value">{{ $sessions->where('status', 'live')->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">مجدولة</div>
                <div class="stat-value">{{ $sessions->where('status', 'scheduled')->count() }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">مكتملة</div>
                <div class="stat-value">{{ $sessions->where('status', 'completed')->count() }}</div>
            </div>
        </div>
    </div>

    <!-- Sessions Table -->
    @if($sessions->isEmpty())
        <div class="table-card">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3>لا توجد جلسات</h3>
                <p>ابدأ بإضافة جلسة جديدة</p>
            </div>
        </div>
    @else
        <div class="table-card">
            <div style="overflow-x: auto;">
                <table class="sessions-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان الجلسة</th>
                            <th>المادة</th>
                            <th>التاريخ والوقت</th>
                            <th>المدة</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                            <tr>
                                <td style="color: #6b7280;" class="dark:text-gray-400">{{ $loop->iteration }}</td>
                                <td>
                                    <div style="font-weight: 700;" class="dark:text-gray-100">
                                        {{ $session->title_ar ?? $session->title }}
                                    </div>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        @if($session->subject && $session->subject->color)
                                            <span class="subject-dot" style="background: {{ $session->subject->color }};"></span>
                                        @endif
                                        <span style="color: #4b5563;" class="dark:text-gray-300">{{ $session->subject->name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($session->scheduled_at)
                                        <div style="font-weight: 600;" class="dark:text-gray-200">{{ $session->scheduled_at->format('Y/m/d') }}</div>
                                        <div style="font-size: 0.8125rem; color: #6b7280;" class="dark:text-gray-400">{{ $session->scheduled_at->format('h:i A') }}</div>
                                    @else
                                        <span style="color: #9ca3af;">غير محدد</span>
                                    @endif
                                </td>
                                <td style="color: #4b5563;" class="dark:text-gray-300">{{ $session->duration_minutes ?? 60 }} دقيقة</td>
                                <td>
                                    @if($session->status === 'live')
                                        <span class="badge badge-live">
                                            <span class="badge-dot" style="background: #dc2626;"></span>
                                            مباشر
                                        </span>
                                    @elseif($session->status === 'scheduled')
                                        <span class="badge badge-scheduled">مجدول</span>
                                    @elseif($session->status === 'completed')
                                        <span class="badge badge-completed">مكتمل</span>
                                    @else
                                        <span class="badge" style="background: #f3f4f6; color: #6b7280;">{{ $session->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <a href="{{ route('admin.sessions.show', $session) }}" class="btn btn-view">عرض</a>
                                        <a href="{{ route('admin.sessions.edit', $session) }}" class="btn btn-edit">تعديل</a>
                                        <form action="{{ route('admin.sessions.destroy', $session) }}" method="POST" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذه الجلسة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-delete">حذف</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($sessions->hasPages())
            <div style="margin-top: 1.5rem;">
                {{ $sessions->links() }}
            </div>
        @endif
    @endif
</div>

<!-- Add Session Modal -->
<div class="modal-backdrop" id="sessionModal">
    <div class="modal-container">
        <div class="modal-header">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 24px; height: 24px; color: #fff;" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h10a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm14 2.5l4-2v11l-4-2v-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: #fff;">إضافة جلسة Zoom</h3>
                    <p style="color: rgba(255,255,255,0.7); font-size: 0.875rem;" id="selectedDateText">اختر التاريخ</p>
                </div>
            </div>
            <button type="button" onclick="closeModal()" class="modal-close">
                <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="modal-body">
            <input type="hidden" id="modal_scheduled_date">

            <div class="form-group">
                <label class="form-label">المادة الدراسية <span style="color: #ef4444;">*</span></label>
                <select id="modal_subject_id" class="form-select" required>
                    <option value="">اختر المادة</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">تاريخ البدء <span style="color: #ef4444;">*</span></label>
                    <input type="date" id="modal_start_date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">تاريخ الانتهاء <span style="color: #ef4444;">*</span></label>
                    <input type="date" id="modal_end_date" class="form-input" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">وقت البدء <span style="color: #ef4444;">*</span></label>
                    <input type="time" id="modal_start_time" class="form-input" value="10:00" required onchange="calculateModalDuration()" style="text-align: center;">
                </div>
                <div class="form-group">
                    <label class="form-label">وقت الانتهاء <span style="color: #ef4444;">*</span></label>
                    <input type="time" id="modal_end_time" class="form-input" value="11:00" required onchange="calculateModalDuration()" style="text-align: center;">
                </div>
                <div class="form-group">
                    <label class="form-label">المدة</label>
                    <div id="modal_calculated_duration" class="form-input" style="background: #f3f4f6; text-align: center; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #059669;">60 دقيقة</div>
                    <input type="hidden" id="modal_duration" value="60">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">التكرار</label>
                <div class="recurrence-toggle">
                    <button type="button" class="recurrence-option active" data-value="none">مرة واحدة</button>
                    <button type="button" class="recurrence-option" data-value="weekly">أسبوعي</button>
                    <button type="button" class="recurrence-option" data-value="monthly">شهري</button>
                </div>
            </div>

            <div id="weeklyOptions" class="options-panel" style="display: none;">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">أيام التكرار</label>
                    <div class="day-selector">
                        <button type="button" class="day-btn" data-day="0">أحد</button>
                        <button type="button" class="day-btn" data-day="1">إثن</button>
                        <button type="button" class="day-btn" data-day="2">ثلا</button>
                        <button type="button" class="day-btn" data-day="3">أرب</button>
                        <button type="button" class="day-btn" data-day="4">خمي</button>
                        <button type="button" class="day-btn" data-day="5">جمع</button>
                        <button type="button" class="day-btn" data-day="6">سبت</button>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">عدد الأسابيع</label>
                    <select id="modal_weeks" class="form-select" style="max-width: 200px;">
                        @for($i = 2; $i <= 16; $i++)
                            <option value="{{ $i }}" {{ $i == 4 ? 'selected' : '' }}>{{ $i }} أسابيع</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div id="monthlyOptions" class="options-panel" style="display: none;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">عدد الأشهر</label>
                    <select id="modal_months" class="form-select" style="max-width: 200px;">
                        @for($i = 2; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == 3 ? 'selected' : '' }}>{{ $i }} أشهر</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" onclick="closeModal()" class="btn-secondary">إلغاء</button>
            <button type="button" onclick="createSession()" class="btn-primary">
                <svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إنشاء الجلسة
            </button>
        </div>
    </div>
</div>

<form id="batchForm" action="{{ route('admin.sessions.store-batch') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="sessions" id="batchSessionsInput">
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedDate = null;
    let currentRecurrence = 'none';
    let selectedDays = [];

    window.showCreateModal = function() {
        const today = new Date().toISOString().split('T')[0];
        openModal(today);
    };

    window.calculateModalDuration = function() {
        const startTime = document.getElementById('modal_start_time').value;
        const endTime = document.getElementById('modal_end_time').value;
        const durationDisplay = document.getElementById('modal_calculated_duration');
        const durationInput = document.getElementById('modal_duration');

        if (startTime && endTime) {
            const [startHour, startMin] = startTime.split(':').map(Number);
            const [endHour, endMin] = endTime.split(':').map(Number);
            let totalMinutes = (endHour * 60 + endMin) - (startHour * 60 + startMin);

            if (totalMinutes < 0) {
                durationDisplay.textContent = 'وقت غير صحيح';
                durationDisplay.style.color = '#ef4444';
                durationInput.value = '';
                return;
            }

            durationDisplay.style.color = '#059669';
            if (totalMinutes >= 60) {
                const hours = Math.floor(totalMinutes / 60);
                const mins = totalMinutes % 60;
                durationDisplay.textContent = mins > 0 ? hours + ' ساعة و ' + mins + ' دقيقة' : hours + ' ساعة';
            } else {
                durationDisplay.textContent = totalMinutes + ' دقيقة';
            }
            durationInput.value = totalMinutes;
        }
    };

    window.openModal = function(dateStr) {
        selectedDate = dateStr;
        document.getElementById('modal_scheduled_date').value = dateStr;
        document.getElementById('modal_start_date').value = dateStr;
        document.getElementById('modal_end_date').value = dateStr;

        const dateObj = new Date(dateStr);
        document.getElementById('selectedDateText').textContent = dateObj.toLocaleDateString('ar-SA', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

        selectedDays = [dateObj.getDay()];
        updateDayButtons();
        calculateModalDuration();
        document.getElementById('sessionModal').classList.add('active');
    };

    window.closeModal = function() {
        document.getElementById('sessionModal').classList.remove('active');
    };

    // Recurrence handlers
    document.querySelectorAll('.recurrence-option').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.recurrence-option').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentRecurrence = this.dataset.value;
            document.getElementById('weeklyOptions').style.display = currentRecurrence === 'weekly' ? 'block' : 'none';
            document.getElementById('monthlyOptions').style.display = currentRecurrence === 'monthly' ? 'block' : 'none';
        });
    });

    // Day selector
    document.querySelectorAll('.day-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const day = parseInt(this.dataset.day);
            if (selectedDays.includes(day)) {
                selectedDays = selectedDays.filter(d => d !== day);
                this.classList.remove('active');
            } else {
                selectedDays.push(day);
                this.classList.add('active');
            }
        });
    });

    function updateDayButtons() {
        document.querySelectorAll('.day-btn').forEach(btn => {
            const day = parseInt(btn.dataset.day);
            btn.classList.toggle('active', selectedDays.includes(day));
        });
    }

    window.createSession = function() {
        const subjectId = document.getElementById('modal_subject_id').value;
        const startDate = document.getElementById('modal_start_date').value;
        const endDate = document.getElementById('modal_end_date').value;
        const startTime = document.getElementById('modal_start_time').value;
        const endTime = document.getElementById('modal_end_time').value;
        const duration = parseInt(document.getElementById('modal_duration').value);

        if (!subjectId) { alert('الرجاء اختيار المادة'); return; }
        if (!startDate || !endDate) { alert('الرجاء تحديد التواريخ'); return; }
        if (new Date(endDate) < new Date(startDate)) { alert('تاريخ الانتهاء يجب أن يكون بعد البدء'); return; }
        if (!duration || duration <= 0) { alert('وقت الانتهاء يجب أن يكون بعد البدء'); return; }

        const dates = generateSessions(startDate, currentRecurrence);
        const sessions = dates.map((date, index) => ({
            subject_id: parseInt(subjectId),
            title_ar: `جلسة ${index + 1}`,
            title_en: `Session ${index + 1}`,
            scheduled_at: date + ' ' + startTime,
            start_date: startDate,
            end_date: endDate,
            start_time: startTime,
            end_time: endTime,
            duration_minutes: duration,
            type: 'live_zoom'
        }));

        fetch('{{ route('admin.sessions.store-batch') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ sessions: sessions })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeModal();
                window.location.reload();
            } else {
                alert(data.message || 'فشل إنشاء الجلسات');
            }
        })
        .catch(() => alert('حدث خطأ أثناء إنشاء الجلسات'));
    };

    function generateSessions(baseDate, recurrenceType) {
        const sessions = [];
        const date = new Date(baseDate);

        if (recurrenceType === 'none') {
            sessions.push(baseDate);
        } else if (recurrenceType === 'weekly') {
            const weeks = parseInt(document.getElementById('modal_weeks').value) || 4;
            const days = selectedDays.length > 0 ? selectedDays : [date.getDay()];
            for (let w = 0; w < weeks; w++) {
                days.forEach(day => {
                    const d = new Date(date);
                    let diff = day - d.getDay();
                    if (diff < 0) diff += 7;
                    d.setDate(d.getDate() + diff + (w * 7));
                    const today = new Date(); today.setHours(0,0,0,0);
                    if (d >= today) sessions.push(d.toISOString().split('T')[0]);
                });
            }
        } else if (recurrenceType === 'monthly') {
            const months = parseInt(document.getElementById('modal_months').value) || 3;
            for (let m = 0; m < months; m++) {
                const d = new Date(date);
                d.setMonth(d.getMonth() + m);
                sessions.push(d.toISOString().split('T')[0]);
            }
        }
        return [...new Set(sessions)].sort();
    }

    // Keyboard & backdrop close
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
    document.getElementById('sessionModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });

    // Auto-open if subject_id in URL
    const subjectId = new URLSearchParams(window.location.search).get('subject_id');
    if (subjectId) {
        setTimeout(() => {
            document.getElementById('modal_subject_id').value = subjectId;
            showCreateModal();
        }, 500);
    }
});
</script>
@endpush
