@extends('layouts.dashboard')

@section('title', 'إدارة تذاكر الدعم')

@push('styles')
<style>
    .tickets-page { max-width: 1400px; margin: 0 auto; }

    /* Header */
    .tickets-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .tickets-header::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .tickets-header::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -5%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .stat-card {
        background: #fff;
        border-radius: 18px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: all 0.2s;
    }
    .dark .stat-card { background: #1f2937; }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08), 0 2px 6px rgba(0,0,0,0.04);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-content {
        flex: 1;
    }
    .stat-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.35rem;
    }
    .dark .stat-label { color: #9ca3af; }
    .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }
    .dark .stat-value { color: #f9fafb; }

    /* Filter Card */
    .filter-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .dark .filter-card { background: #1f2937; }

    .filter-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.2s;
    }
    .dark .filter-header { border-color: #374151; }
    .filter-header:hover {
        background: #f9fafb;
    }
    .dark .filter-header:hover {
        background: rgba(255,255,255,0.02);
    }

    .filter-title {
        font-size: 1rem;
        font-weight: 800;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .filter-title { color: #f9fafb; }

    .filter-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .filter-body {
        padding: 1.5rem;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .form-group {
        margin-bottom: 0;
    }
    .form-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }
    .dark .form-label { color: #d1d5db; }

    .form-select {
        width: 100%;
        padding: 0.7rem 0.9rem;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.9rem;
        transition: all 0.2s;
        background: #fff;
    }
    .dark .form-select {
        background: #111827;
        border-color: #374151;
        color: #f9fafb;
    }
    .form-select:focus {
        outline: none;
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }

    /* Table Card */
    .table-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .table-card { background: #1f2937; }

    .table-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .dark .table-header { border-color: #374151; }

    .table-title {
        font-size: 1rem;
        font-weight: 800;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .table-title { color: #f9fafb; }

    .table-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Custom Table */
    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .custom-table th {
        font-size: 0.75rem;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.85rem 1.25rem;
        text-align: right;
        border-bottom: 2px solid #f1f5f9;
        background: #f9fafb;
    }
    .dark .custom-table th {
        color: #9ca3af;
        border-color: #374151;
        background: #111827;
    }
    .custom-table td {
        padding: 1.25rem;
        font-size: 0.9rem;
        color: #374151;
        border-bottom: 1px solid #f1f5f9;
    }
    .dark .custom-table td {
        color: #d1d5db;
        border-color: #374151;
    }
    .custom-table tbody tr {
        transition: all 0.15s;
        cursor: pointer;
    }
    .custom-table tbody tr:hover {
        background: #f9fafb;
    }
    .dark .custom-table tbody tr:hover {
        background: rgba(255,255,255,0.02);
    }

    /* Badges */
    .table-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 700;
        white-space: nowrap;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 1.5rem;
    }
    .empty-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
    }
    .empty-text {
        color: #6b7280;
        font-size: 1rem;
        font-weight: 600;
    }
    .dark .empty-text { color: #9ca3af; }

    /* Pagination */
    .pagination-container {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #f1f5f9;
    }
    .dark .pagination-container { border-color: #374151; }

    /* User Cell */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .user-info {
        flex: 1;
    }
    .user-name {
        font-weight: 700;
        color: #111827;
        font-size: 0.9rem;
        margin-bottom: 0.15rem;
    }
    .dark .user-name { color: #f9fafb; }
    .user-role {
        font-size: 0.75rem;
        color: #6b7280;
    }
    .dark .user-role { color: #9ca3af; }

    /* Subject Cell */
    .subject-text {
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.25rem;
    }
    .dark .subject-text { color: #f9fafb; }
    .subject-meta {
        font-size: 0.75rem;
        color: #6b7280;
    }
    .dark .subject-meta { color: #9ca3af; }

    /* Responsive */
    @media (max-width: 768px) {
        .tickets-header {
            padding: 1.5rem 1.25rem;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .filter-grid {
            grid-template-columns: 1fr;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
        }
        .stat-value {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="tickets-page">
    <!-- Header -->
    <div class="tickets-header">
        <div class="flex items-start gap-4 relative z-10">
            <div class="flex-1">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-2xl font-extrabold mb-1">إدارة تذاكر الدعم</h1>
                        <p class="opacity-75 text-sm">إدارة طلبات الدعم الفني (معيار NELC 1.3.3)</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <svg style="width: 28px; height: 28px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">إجمالي التذاكر</div>
                <div class="stat-value">{{ $stats['total'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                <svg style="width: 28px; height: 28px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">مفتوحة</div>
                <div class="stat-value" style="color: #ef4444;">{{ $stats['open'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <svg style="width: 28px; height: 28px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">قيد المعالجة</div>
                <div class="stat-value" style="color: #f59e0b;">{{ $stats['in_progress'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <svg style="width: 28px; height: 28px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">تم الحل</div>
                <div class="stat-value" style="color: #10b981;">{{ $stats['resolved'] }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                <svg style="width: 28px; height: 28px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">متوسط وقت الرد</div>
                <div class="stat-value" style="color: #0071AA; font-size: 1.4rem;">{{ $stats['avg_response_time'] }}<span style="font-size: 0.9rem;">د</span></div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <div class="filter-header" onclick="document.getElementById('filterBody').classList.toggle('hidden')">
            <div class="filter-title">
                <div class="filter-icon">
                    <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </div>
                تصفية النتائج
            </div>
            <svg style="width: 20px; height: 20px; color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
        <div id="filterBody" class="filter-body hidden">
            <form method="GET">
                <div class="filter-grid">
                    <div class="form-group">
                        <label class="form-label">الحالة</label>
                        <select name="status" onchange="this.form.submit()" class="form-select">
                            <option value="all">كل الحالات</option>
                            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>مفتوحة</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>تم الحل</option>
                            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>مغلقة</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">الأولوية</label>
                        <select name="priority" onchange="this.form.submit()" class="form-select">
                            <option value="all">كل الأولويات</option>
                            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>عاجلة</option>
                            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>عالية</option>
                            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>متوسطة</option>
                            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>منخفضة</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">الفئة</label>
                        <select name="category" onchange="this.form.submit()" class="form-select">
                            <option value="all">كل الفئات</option>
                            <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>تقني</option>
                            <option value="academic" {{ request('category') === 'academic' ? 'selected' : '' }}>أكاديمي</option>
                            <option value="financial" {{ request('category') === 'financial' ? 'selected' : '' }}>مالي</option>
                            <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="table-card">
        <div class="table-header">
            <div class="table-title">
                <div class="table-icon">
                    <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                </div>
                قائمة التذاكر ({{ $tickets->total() }})
            </div>
        </div>
        <div style="overflow-x: auto;">
            @if($tickets->count() > 0)
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الموضوع</th>
                            <th>المستخدم</th>
                            <th>الفئة</th>
                            <th>الأولوية</th>
                            <th>الحالة</th>
                            <th>المعين</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr onclick="window.location='{{ route('admin.tickets.show', $ticket) }}'">
                                <td style="font-weight: 700; color: #111827;" class="dark:text-white">#{{ $ticket->id }}</td>
                                <td>
                                    <div class="subject-text">{{ Str::limit($ticket->subject, 50) }}</div>
                                    <div class="subject-meta">
                                        <svg style="width: 12px; height: 12px; display: inline-block; vertical-align: middle;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $ticket->replies_count }} رد
                                    </div>
                                </td>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">
                                            {{ substr($ticket->user?->name ?? 'N/A', 0, 1) }}
                                        </div>
                                        <div class="user-info">
                                            <div class="user-name">{{ $ticket->user?->name ?? 'غير معروف' }}</div>
                                            <div class="user-role">{{ $ticket->user?->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="table-badge {{ $ticket->getCategoryColorClass() }}">
                                        {{ $ticket->getCategoryLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="table-badge {{ $ticket->getPriorityColorClass() }}">
                                        {{ $ticket->getPriorityLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="table-badge {{ $ticket->getStatusColorClass() }}">
                                        {{ $ticket->getStatusLabel() }}
                                    </span>
                                </td>
                                <td style="font-weight: 600; color: #6b7280;" class="dark:text-gray-400">
                                    {{ $ticket->assignedTo?->name ?? '-' }}
                                </td>
                                <td style="font-size: 0.85rem; color: #6b7280;" class="dark:text-gray-400">
                                    {{ $ticket->created_at->diffForHumans() }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon">🎫</div>
                    <p class="empty-text">لا توجد تذاكر متاحة</p>
                </div>
            @endif
        </div>
        @if($tickets->hasPages())
            <div class="pagination-container">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
