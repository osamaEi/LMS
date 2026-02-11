@extends('layouts.dashboard')

@section('title', 'طلبات التسجيل في البرامج المعلقة')

@push('styles')
<style>
    .enrollments-page { max-width: 1400px; margin: 0 auto; }

    /* Header */
    .enrollments-header {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .enrollments-header::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .enrollments-header::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -5%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    .header-title {
        font-size: 1.75rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }
    .header-subtitle {
        font-size: 0.95rem;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
    .stat-icon svg { width: 28px; height: 28px; }

    .stat-content {
        flex: 1;
    }
    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    .dark .stat-label { color: #9ca3af; }
    .stat-value {
        font-size: 1.875rem;
        font-weight: 800;
        color: #111827;
        line-height: 1;
    }
    .dark .stat-value { color: #f9fafb; }

    /* Search & Filter Card */
    .filter-card {
        background: #fff;
        border-radius: 18px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        margin-bottom: 1.5rem;
    }
    .dark .filter-card { background: #1f2937; }

    /* Table Card */
    .table-card {
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
    }
    .dark .table-card { background: #1f2937; }

    .table-wrapper {
        overflow-x: auto;
    }
    .enrollments-table {
        width: 100%;
        border-collapse: collapse;
    }
    .enrollments-table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }
    .dark .enrollments-table thead {
        background: #111827;
        border-color: #374151;
    }
    .enrollments-table th {
        padding: 1rem 1.5rem;
        text-align: right;
        font-size: 0.75rem;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .dark .enrollments-table th { color: #d1d5db; }

    .enrollments-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }
    .dark .enrollments-table tbody tr { border-color: #374151; }
    .enrollments-table tbody tr:hover {
        background: #f9fafb;
    }
    .dark .enrollments-table tbody tr:hover { background: #111827; }

    .enrollments-table td {
        padding: 1.25rem 1.5rem;
        font-size: 0.9rem;
        color: #1f2937;
    }
    .dark .enrollments-table td { color: #f3f4f6; }

    /* User Avatar */
    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-weight: 700;
        color: #fff;
        font-size: 0.95rem;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.15s;
        cursor: pointer;
        border: none;
        text-decoration: none;
    }
    .btn-view {
        background: #dbeafe;
        color: #1e40af;
    }
    .btn-view:hover {
        background: #bfdbfe;
        transform: translateY(-1px);
    }
    .btn-approve {
        background: #d1fae5;
        color: #065f46;
    }
    .btn-approve:hover {
        background: #a7f3d0;
        transform: translateY(-1px);
    }
    .btn-reject {
        background: #fee2e2;
        color: #991b1b;
    }
    .btn-reject:hover {
        background: #fecaca;
        transform: translateY(-1px);
    }
    .btn-search {
        background: #3b82f6;
        color: #fff;
        padding: 0.75rem 1.5rem;
    }
    .btn-search:hover {
        background: #2563eb;
        transform: translateY(-1px);
    }
    .btn-cancel {
        background: #6b7280;
        color: #fff;
        padding: 0.75rem 1.5rem;
    }
    .btn-cancel:hover {
        background: #4b5563;
        transform: translateY(-1px);
    }

    /* Badge */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.875rem;
        border-radius: 999px;
        font-size: 0.8125rem;
        font-weight: 700;
    }
    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }
    .dark .badge-warning {
        background: #78350f;
        color: #fef3c7;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }
    .empty-state svg {
        width: 80px;
        height: 80px;
        margin: 0 auto 1.5rem;
        color: #d1d5db;
    }
    .dark .empty-state svg { color: #4b5563; }
    .empty-state h3 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
    }
    .dark .empty-state h3 { color: #f9fafb; }
    .empty-state p {
        color: #6b7280;
    }
    .dark .empty-state p { color: #9ca3af; }

    /* Bulk Actions */
    .bulk-actions {
        background: #eff6ff;
        border: 2px solid #bfdbfe;
        border-radius: 16px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    .dark .bulk-actions {
        background: rgba(59, 130, 246, 0.1);
        border-color: rgba(59, 130, 246, 0.3);
    }

    .input-search {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.9375rem;
        transition: all 0.2s;
        color: #111827;
        background: #fff;
    }
    .dark .input-search {
        background: #111827;
        border-color: #374151;
        color: #f3f4f6;
    }
    .input-search:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
</style>
@endpush

@section('content')
<div class="enrollments-page">
    <!-- Header -->
    <div class="enrollments-header">
        <div style="position: relative; z-index: 1;">
            <h1 class="header-title">طلبات التسجيل في البرامج</h1>
            <p class="header-subtitle">مراجعة وقبول طلبات التسجيل في البرامج الدراسية</p>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #fff;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">طلبات معلقة</div>
                <div class="stat-value">{{ $users->total() }}</div>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.program-enrollments.index') }}" style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 250px;">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="بحث بالاسم، البريد، الجوال، رقم الهوية..."
                       class="input-search">
            </div>
            <button type="submit" class="btn btn-search">
                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                بحث
            </button>
            @if(request('search'))
                <a href="{{ route('admin.program-enrollments.index') }}" class="btn btn-cancel">إلغاء</a>
            @endif
        </form>
    </div>

    <!-- Bulk Actions Form -->
    <form id="bulk-form" method="POST">
        @csrf

        <!-- Bulk Actions Bar -->
        <div id="bulk-actions-bar" class="bulk-actions" style="display: none;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <span style="font-weight: 600; color: #1e40af;">
                    تم تحديد <span id="selected-count">0</span> طالب
                </span>
                <div class="dark:text-blue-200" style="display: flex; gap: 0.5rem;">
                    <button type="button" onclick="bulkApprove()" class="btn btn-approve">
                        قبول المحددين
                    </button>
                    <button type="button" onclick="bulkReject()" class="btn btn-reject">
                        رفض المحددين
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        @if($users->isEmpty())
            <div class="table-card">
                <div class="empty-state">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3>لا توجد طلبات معلقة</h3>
                    <p>جميع طلبات التسجيل في البرامج تمت مراجعتها</p>
                </div>
            </div>
        @else
            <div class="table-card">
                <div class="table-wrapper">
                    <table class="enrollments-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="select-all" onchange="toggleSelectAll(this)"
                                           style="width: 18px; height: 18px; cursor: pointer;">
                                </th>
                                <th>الطالب</th>
                                <th>البرنامج المطلوب</th>
                                <th>البريد الإلكتروني</th>
                                <th>الجوال</th>
                                <th>تاريخ الطلب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                               class="user-checkbox" onchange="updateBulkActions()"
                                               style="width: 18px; height: 18px; cursor: pointer;">
                                    </td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div class="user-avatar">
                                                {{ substr($user->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; margin-bottom: 0.125rem;" class="dark:text-gray-100">
                                                    {{ $user->name }}
                                                </div>
                                                <div style="font-size: 0.8125rem; color: #6b7280;" class="dark:text-gray-400">
                                                    {{ $user->national_id ?? '-' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; margin-bottom: 0.125rem;" class="dark:text-gray-100">
                                            {{ $user->program->name ?? '-' }}
                                        </div>
                                        @if($user->program && $user->program->code)
                                            <div style="font-size: 0.8125rem; color: #6b7280;" class="dark:text-gray-400">
                                                {{ $user->program->code }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="color: #4b5563;" class="dark:text-gray-300">{{ $user->email }}</td>
                                    <td style="color: #4b5563;" class="dark:text-gray-300">{{ $user->phone ?? '-' }}</td>
                                    <td style="color: #6b7280;" class="dark:text-gray-400">{{ $user->updated_at->diffForHumans() }}</td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                            <a href="{{ route('admin.program-enrollments.show', $user) }}" class="btn btn-view">
                                                عرض
                                            </a>
                                            <form action="{{ route('admin.program-enrollments.approve', $user) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" onclick="return confirm('هل أنت متأكد من قبول طلب التسجيل لهذا الطالب؟')"
                                                        class="btn btn-approve">
                                                    قبول
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.program-enrollments.reject', $user) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('هل أنت متأكد من رفض طلب التسجيل؟ سيتم إزالة البرنامج من حساب الطالب.')"
                                                        class="btn btn-reject">
                                                    رفض
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div style="margin-top: 1.5rem;">
                {{ $users->links() }}
            </div>
        @endif
    </form>
</div>

<script>
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.user-checkbox');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
        updateBulkActions();
    }

    function updateBulkActions() {
        const checkboxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkboxes.length;
        const bulkBar = document.getElementById('bulk-actions-bar');
        const countSpan = document.getElementById('selected-count');

        if (count > 0) {
            bulkBar.style.display = 'block';
            countSpan.textContent = count;
        } else {
            bulkBar.style.display = 'none';
        }
    }

    function bulkApprove() {
        if (!confirm('هل أنت متأكد من قبول طلبات التسجيل المحددة؟')) {
            return;
        }

        const form = document.getElementById('bulk-form');
        form.action = '{{ route("admin.program-enrollments.bulk-approve") }}';
        form.submit();
    }

    function bulkReject() {
        if (!confirm('هل أنت متأكد من رفض طلبات التسجيل المحددة؟ سيتم إزالة البرامج من حسابات الطلاب.')) {
            return;
        }

        const form = document.getElementById('bulk-form');
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        form.action = '{{ route("admin.program-enrollments.bulk-reject") }}';
        form.submit();
    }
</script>
@endsection
