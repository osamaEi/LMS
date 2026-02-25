@extends('layouts.dashboard')

@section('title', 'إدارة الطلاب')

@push('styles')
<style>
    .students-page { max-width: 1400px; margin: 0 auto; }

    /* Header */
    .students-header {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .students-header::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .students-header::after {
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
    .students-table {
        width: 100%;
        border-collapse: collapse;
    }
    .students-table thead {
        background: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }
    .dark .students-table thead {
        background: #111827;
        border-color: #374151;
    }
    .students-table th {
        padding: 1rem 1.5rem;
        text-align: right;
        font-size: 0.75rem;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .dark .students-table th { color: #d1d5db; }

    .students-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.15s;
    }
    .dark .students-table tbody tr { border-color: #374151; }
    .students-table tbody tr:hover {
        background: #f9fafb;
    }
    .dark .students-table tbody tr:hover { background: #111827; }

    .students-table td {
        padding: 1.25rem 1.5rem;
        font-size: 0.9rem;
        color: #1f2937;
    }
    .dark .students-table td { color: #f3f4f6; }

    /* User Avatar */
    .user-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
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
    .btn-edit {
        background: #fef3c7;
        color: #92400e;
    }
    .btn-edit:hover {
        background: #fde68a;
        transform: translateY(-1px);
    }
    .btn-delete {
        background: #fee2e2;
        color: #991b1b;
    }
    .btn-delete:hover {
        background: #fecaca;
        transform: translateY(-1px);
    }
    .btn-activate {
        background: #d1fae5;
        color: #065f46;
    }
    .btn-activate:hover {
        background: #a7f3d0;
        transform: translateY(-1px);
    }
    .btn-deactivate {
        background: #fee2e2;
        color: #991b1b;
    }
    .btn-deactivate:hover {
        background: #fecaca;
        transform: translateY(-1px);
    }

    /* Status Badge */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.875rem;
        border-radius: 999px;
        font-size: 0.8125rem;
        font-weight: 700;
    }
    .badge-active {
        background: #d1fae5;
        color: #065f46;
    }
    .dark .badge-active {
        background: #064e3b;
        color: #d1fae5;
    }
    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
    }
    .dark .badge-inactive {
        background: #7f1d1d;
        color: #fee2e2;
    }
    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }
    .dark .badge-pending {
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

    /* Success Message */
    .alert-success {
        background: #d1fae5;
        border: 2px solid #a7f3d0;
        color: #065f46;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-weight: 600;
    }
    .dark .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border-color: rgba(16, 185, 129, 0.3);
        color: #d1fae5;
    }
</style>
@endpush

@section('content')
<div class="students-page">
    <!-- Header -->
    <div class="students-header">
        <div style="position: relative; z-index: 1; display: flex; align-items: center; justify-content: space-between;">
            <div>
                <h1 class="header-title">إدارة الطلاب</h1>
                <p class="header-subtitle">عرض وإدارة جميع الطلاب المسجلين في النظام</p>
            </div>
            <a href="{{ route('admin.students.export') }}"
               style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; background: rgba(255,255,255,0.2); border: 2px solid rgba(255,255,255,0.5); border-radius: 12px; color: #fff; font-weight: 600; font-size: 0.9rem; text-decoration: none; transition: all 0.2s; backdrop-filter: blur(4px);"
               onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <svg style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                تصدير Excel
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #fff;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">إجمالي الطلاب</div>
                <div class="stat-value">{{ $students->total() }}</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    @if($students->isEmpty())
        <div class="table-card">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <h3>لا يوجد طلاب مسجلين</h3>
                <p>لم يتم العثور على أي طلاب في النظام</p>
            </div>
        </div>
    @else
        <div class="table-card">
            <div class="table-wrapper">
                <table class="students-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الطالب</th>
                            <th>البريد الإلكتروني</th>
                            <th>رقم الهوية</th>
                            <th>رقم الهاتف</th>
                            <th>الحالة</th>
                            <th>تاريخ التسجيل</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td style="color: #6b7280;" class="dark:text-gray-400">{{ $loop->iteration }}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="user-avatar">
                                            {{ substr($student->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div style="font-weight: 700;" class="dark:text-gray-100">
                                                {{ $student->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td style="color: #4b5563;" class="dark:text-gray-300">{{ $student->email }}</td>
                                <td style="color: #4b5563;" class="dark:text-gray-300">{{ $student->national_id }}</td>
                                <td style="color: #4b5563;" class="dark:text-gray-300">{{ $student->phone ?? '-' }}</td>
                                <td>
                                    @if($student->status === 'active')
                                        <span class="badge badge-active">نشط</span>
                                    @elseif($student->status === 'pending')
                                        <span class="badge badge-pending">معلق</span>
                                    @else
                                        <span class="badge badge-inactive">غير نشط</span>
                                    @endif
                                </td>
                                <td style="color: #6b7280;" class="dark:text-gray-400">{{ $student->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <a href="{{ route('admin.students.show', $student) }}" class="btn btn-view">
                                            عرض
                                        </a>

                                        <!-- Toggle Status Button -->
                                        <form action="{{ route('admin.students.toggle-status', $student) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @if($student->status === 'active')
                                                <button type="submit" onclick="return confirm('هل أنت متأكد من إلغاء تفعيل هذا الطالب؟')"
                                                        class="btn btn-deactivate">
                                                    إلغاء التفعيل
                                                </button>
                                            @else
                                                <button type="submit" onclick="return confirm('هل أنت متأكد من تفعيل هذا الطالب؟')"
                                                        class="btn btn-activate">
                                                    تفعيل
                                                </button>
                                            @endif
                                        </form>

                                        <form action="{{ route('admin.students.destroy', $student) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('هل أنت متأكد من حذف هذا الطالب؟')"
                                                    class="btn btn-delete">
                                                حذف
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
        @if($students->hasPages())
            <div style="margin-top: 1.5rem;">
                {{ $students->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
