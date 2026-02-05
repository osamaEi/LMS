@extends('layouts.dashboard')

@section('title', 'إدارة الدفعات')

@push('styles')
<style>
    .payments-page { max-width: 1400px; margin: 0 auto; }

    /* Header */
    .payments-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .payments-header::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .payments-header::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -5%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    .header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    .header-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        color: #fff;
        text-decoration: none;
    }
    .header-btn-warning {
        background: rgba(245, 158, 11, 0.9);
    }
    .header-btn-warning:hover {
        background: #f59e0b;
        transform: translateY(-1px);
    }
    .header-btn-primary {
        background: rgba(16, 185, 129, 0.9);
    }
    .header-btn-primary:hover {
        background: #10b981;
        transform: translateY(-1px);
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

    /* Alert Messages */
    .alert-success {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border-radius: 14px;
        margin-bottom: 1.5rem;
    }
    .alert-success-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: #10b981;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .alert-success-text {
        color: #065f46;
        font-weight: 600;
        font-size: 0.9rem;
        flex: 1;
    }
    .alert-danger {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-radius: 14px;
        margin-bottom: 1.5rem;
    }
    .alert-danger-icon {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: #ef4444;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .alert-danger-text {
        color: #991b1b;
        font-weight: 600;
        font-size: 0.9rem;
        flex: 1;
    }

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
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
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
        margin-bottom: 1rem;
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
        border-color: #0071AA;
        box-shadow: 0 0 0 3px rgba(0, 113, 170, 0.1);
    }

    .btn-filter {
        width: 100%;
        padding: 0.7rem 1.25rem;
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        color: #fff;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    .btn-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 113, 170, 0.25);
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
        background: linear-gradient(135deg, #10b981, #059669);
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
    }
    .custom-table tbody tr:hover {
        background: #f9fafb;
    }
    .dark .custom-table tbody tr:hover {
        background: rgba(255,255,255,0.02);
    }

    /* User Cell */
    .user-cell {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(135deg, #8b5cf6, #6d28d9);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .user-info {
        flex: 1;
    }
    .user-name {
        font-weight: 700;
        color: #111827;
        font-size: 0.95rem;
        margin-bottom: 0.15rem;
    }
    .dark .user-name { color: #f9fafb; }
    .user-email {
        font-size: 0.8rem;
        color: #6b7280;
    }
    .dark .user-email { color: #9ca3af; }

    /* Payment Progress */
    .payment-progress {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        min-width: 140px;
    }
    .progress-text {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
    }
    .progress-amount {
        font-weight: 700;
        color: #10b981;
    }
    .progress-percentage {
        font-weight: 700;
        color: #6b7280;
    }
    .progress-bar-container {
        width: 100%;
        height: 8px;
        background: #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
    }
    .dark .progress-bar-container { background: #374151; }
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        transition: width 0.5s ease;
        border-radius: 10px;
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
    .badge-pending { background: rgba(107, 114, 128, 0.15); color: #6b7280; }
    .badge-partial { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .badge-completed { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .badge-cancelled { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .badge-full { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .badge-installment { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }
    .badge-cash { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .badge-bank { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .badge-tamara { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }
    .badge-paytabs { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .badge-waived { background: rgba(107, 114, 128, 0.15); color: #6b7280; }

    /* Action Button */
    .btn-view {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #0071AA, #005a88);
        color: #fff;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-view:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 113, 170, 0.3);
        color: #fff;
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

    /* Responsive */
    @media (max-width: 768px) {
        .payments-header {
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
<div class="payments-page">
    @if(session('success'))
        <div class="alert-success">
            <div class="alert-success-icon">
                <svg style="width: 18px; height: 18px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <span class="alert-success-text">{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" style="color: #059669; background: none; border: none; cursor: pointer; padding: 0;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger">
            <div class="alert-danger-icon">
                <svg style="width: 18px; height: 18px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <span class="alert-danger-text">{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" style="color: #dc2626; background: none; border: none; cursor: pointer; padding: 0;">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Header -->
    <div class="payments-header">
        <div class="flex items-start gap-4 relative z-10">
            <div class="flex-1">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-2xl font-extrabold mb-1">إدارة الدفعات</h1>
                        <p class="opacity-75 text-sm">عرض وإدارة جميع دفعات الطلاب والأقساط</p>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('admin.payments.overdue') }}" class="header-btn header-btn-warning">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            الأقساط المتأخرة
                        </a>
                        <a href="{{ route('admin.payments.create') }}" class="header-btn header-btn-primary">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            إنشاء دفعة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                <svg style="width: 28px; height: 28px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">إجمالي الدفعات</div>
                <div class="stat-value">{{ $payments->total() }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                <svg style="width: 28px; height: 28px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">دفعات مكتملة</div>
                <div class="stat-value" style="color: #10b981;">{{ $payments->where('status', 'completed')->count() }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <svg style="width: 28px; height: 28px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">قيد الانتظار</div>
                <div class="stat-value" style="color: #f59e0b;">{{ $payments->where('status', 'pending')->count() }}</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <svg style="width: 28px; height: 28px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">إجمالي المبلغ</div>
                <div class="stat-value" style="color: #3b82f6; font-size: 1.4rem;">{{ number_format($payments->sum('total_amount'), 0) }} <span style="font-size: 1rem;">ر.س</span></div>
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
            <form action="{{ route('admin.payments.index') }}" method="GET">
                <div class="filter-grid">
                    <div class="form-group">
                        <label class="form-label">الطالب</label>
                        <select name="user_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ request('user_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">البرنامج</label>
                        <select name="program_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>جزئية</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">طريقة الدفع</label>
                        <select name="payment_method" class="form-select">
                            <option value="">الكل</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                            <option value="tamara" {{ request('payment_method') == 'tamara' ? 'selected' : '' }}>تمارا</option>
                            <option value="paytabs" {{ request('payment_method') == 'paytabs' ? 'selected' : '' }}>PayTabs</option>
                            <option value="waived" {{ request('payment_method') == 'waived' ? 'selected' : '' }}>معفي</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">نوع الدفع</label>
                        <select name="payment_type" class="form-select">
                            <option value="">الكل</option>
                            <option value="full" {{ request('payment_type') == 'full' ? 'selected' : '' }}>دفعة كاملة</option>
                            <option value="installment" {{ request('payment_type') == 'installment' ? 'selected' : '' }}>تقسيط</option>
                        </select>
                    </div>

                    <div class="form-group" style="display: flex; align-items: flex-end;">
                        <button type="submit" class="btn-filter">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="table-card">
        <div class="table-header">
            <div class="table-title">
                <div class="table-icon">
                    <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                قائمة الدفعات ({{ $payments->total() }})
            </div>
        </div>
        <div style="overflow-x: auto;">
            @if($payments->count() > 0)
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الطالب</th>
                            <th>البرنامج</th>
                            <th>نوع الدفع</th>
                            <th>التقدم</th>
                            <th>طريقة الدفع</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td style="font-weight: 700; color: #111827;" class="dark:text-white">{{ $payment->id }}</td>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">
                                            {{ substr($payment->user->name, 0, 1) }}
                                        </div>
                                        <div class="user-info">
                                            <div class="user-name">{{ $payment->user->name }}</div>
                                            <div class="user-email">{{ $payment->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-weight: 600;">{{ $payment->program->name_ar }}</td>
                                <td>
                                    @if($payment->payment_type === 'full')
                                        <span class="table-badge badge-full">
                                            <svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                                                <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                                            </svg>
                                            دفعة كاملة
                                        </span>
                                    @else
                                        <span class="table-badge badge-installment">
                                            <svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                            </svg>
                                            تقسيط
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="payment-progress">
                                        <div class="progress-text">
                                            <span class="progress-amount">{{ number_format($payment->paid_amount, 0) }} ر.س</span>
                                            <span class="progress-percentage">{{ $payment->total_amount > 0 ? number_format(($payment->paid_amount / $payment->total_amount) * 100, 0) : 0 }}%</span>
                                        </div>
                                        <div class="progress-bar-container">
                                            <div class="progress-bar-fill" style="width: {{ $payment->total_amount > 0 ? ($payment->paid_amount / $payment->total_amount) * 100 : 0 }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($payment->payment_method === 'cash')
                                        <span class="table-badge badge-cash">💵 نقدي</span>
                                    @elseif($payment->payment_method === 'bank_transfer')
                                        <span class="table-badge badge-bank">🏦 تحويل بنكي</span>
                                    @elseif($payment->payment_method === 'tamara')
                                        <span class="table-badge badge-tamara">💳 تمارا</span>
                                    @elseif($payment->payment_method === 'paytabs')
                                        <span class="table-badge badge-paytabs">💳 PayTabs</span>
                                    @elseif($payment->payment_method === 'waived')
                                        <span class="table-badge badge-waived">🎁 معفي</span>
                                    @else
                                        <span class="table-badge badge-waived">--</span>
                                    @endif
                                </td>
                                <td>
                                    @if($payment->status === 'completed')
                                        <span class="table-badge badge-completed">
                                            <svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            مكتملة
                                        </span>
                                    @elseif($payment->status === 'partial')
                                        <span class="table-badge badge-partial">
                                            <svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-6a1 1 0 011-1h4a1 1 0 110 2h-4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            جزئية
                                        </span>
                                    @elseif($payment->status === 'pending')
                                        <span class="table-badge badge-pending">
                                            <svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            قيد الانتظار
                                        </span>
                                    @elseif($payment->status === 'cancelled')
                                        <span class="table-badge badge-cancelled">
                                            <svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            ملغاة
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.payments.show', $payment) }}" class="btn-view">
                                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        عرض التفاصيل
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon">💰</div>
                    <p class="empty-text">لا توجد دفعات متاحة</p>
                </div>
            @endif
        </div>
        @if($payments->hasPages())
            <div class="pagination-container">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
