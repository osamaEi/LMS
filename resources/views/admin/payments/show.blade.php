@extends('layouts.dashboard')

@section('title', 'تفاصيل الدفعة #' . $payment->id)

@push('styles')
<style>
    .payment-page { max-width: 1200px; margin: 0 auto; }

    /* Header */
    .payment-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .payment-header::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }
    .payment-header::after {
        content: '';
        position: absolute;
        bottom: -50%;
        right: -5%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    .back-btn {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: rgba(255,255,255,0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        color: #fff;
        text-decoration: none;
    }
    .back-btn:hover {
        background: rgba(255,255,255,0.25);
        color: #fff;
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
    .header-btn-success {
        background: rgba(16, 185, 129, 0.9);
    }
    .header-btn-success:hover {
        background: #10b981;
        transform: translateY(-1px);
    }
    .header-btn-warning {
        background: rgba(245, 158, 11, 0.9);
    }
    .header-btn-warning:hover {
        background: #f59e0b;
        transform: translateY(-1px);
    }
    .header-btn-danger {
        background: rgba(239, 68, 68, 0.9);
    }
    .header-btn-danger:hover {
        background: #ef4444;
        transform: translateY(-1px);
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 700;
    }
    .status-pending { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .status-partial { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .status-completed { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .status-cancelled { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .status-refunded { background: rgba(107, 114, 128, 0.15); color: #6b7280; }

    /* Card */
    .pay-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .dark .pay-card { background: #1f2937; }

    .pay-card-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .dark .pay-card-head { border-color: #374151; }

    .pay-card-title {
        font-size: 1rem;
        font-weight: 800;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .pay-card-title { color: #f9fafb; }

    .pay-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .pay-card-body {
        padding: 1.5rem;
    }

    /* Summary Items */
    .summary-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
    }
    .summary-item:not(:last-child) {
        border-bottom: 1px solid #f1f5f9;
    }
    .dark .summary-item:not(:last-child) { border-color: #374151; }

    .summary-label {
        font-size: 0.9rem;
        color: #6b7280;
    }
    .dark .summary-label { color: #9ca3af; }

    .summary-value {
        font-size: 1.05rem;
        font-weight: 700;
        color: #111827;
    }
    .dark .summary-value { color: #f9fafb; }

    .summary-value-success { color: #10b981; }
    .summary-value-danger { color: #ef4444; }
    .summary-value-primary { color: #0071AA; }

    /* Progress */
    .progress-bar-container {
        width: 100%;
        height: 24px;
        background: #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }
    .dark .progress-bar-container { background: #374151; }

    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: #fff;
        transition: width 0.5s ease;
    }

    /* Info Items */
    .info-item {
        margin-bottom: 1.25rem;
    }
    .info-item:last-child { margin-bottom: 0; }

    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.35rem;
    }
    .dark .info-label { color: #9ca3af; }

    .info-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #111827;
    }
    .dark .info-value { color: #f9fafb; }

    .info-subvalue {
        font-size: 0.85rem;
        color: #6b7280;
    }
    .dark .info-subvalue { color: #9ca3af; }

    /* Table */
    .custom-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .custom-table th {
        font-size: 0.8rem;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.75rem 1rem;
        text-align: right;
        border-bottom: 2px solid #f1f5f9;
    }
    .dark .custom-table th {
        color: #9ca3af;
        border-color: #374151;
    }
    .custom-table td {
        padding: 1rem;
        font-size: 0.9rem;
        color: #374151;
        border-bottom: 1px solid #f1f5f9;
    }
    .dark .custom-table td {
        color: #d1d5db;
        border-color: #374151;
    }
    .custom-table tbody tr:hover {
        background: #f9fafb;
    }
    .dark .custom-table tbody tr:hover {
        background: rgba(255,255,255,0.02);
    }

    .table-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .badge-success { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .badge-warning { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .badge-danger { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .badge-info { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .badge-secondary { background: rgba(107, 114, 128, 0.15); color: #6b7280; }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
    }
    .empty-state-icon {
        font-size: 3.5rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    .empty-state-text {
        color: #6b7280;
        font-size: 0.95rem;
    }
    .dark .empty-state-text { color: #9ca3af; }

    /* Alert */
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
    }

    /* Action Button */
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 0.85rem;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }
    .action-btn-success {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
    }
    .action-btn-success:hover {
        background: rgba(16, 185, 129, 0.25);
        color: #059669;
    }
    .action-btn-primary {
        background: rgba(0, 113, 170, 0.15);
        color: #0071AA;
    }
    .action-btn-primary:hover {
        background: rgba(0, 113, 170, 0.25);
        color: #005a88;
    }

    /* Payment Type Badge */
    .payment-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .payment-type-full { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .payment-type-installment { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }

    /* Modal Styling */
    .modal-content {
        border-radius: 20px;
        border: none;
        overflow: hidden;
    }
    .modal-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 1px solid #e5e7eb;
        padding: 1.5rem 1.75rem;
    }
    .dark .modal-header {
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        border-color: #374151;
    }
    .modal-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .dark .modal-title { color: #f9fafb; }
    .modal-body {
        padding: 1.75rem;
    }
    .modal-footer {
        background: #f8fafc;
        border-top: 1px solid #e5e7eb;
        padding: 1.25rem 1.75rem;
    }
    .dark .modal-footer {
        background: #111827;
        border-color: #374151;
    }

    /* Form Elements in Modal */
    .form-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }
    .dark .form-label { color: #d1d5db; }

    .form-control, .form-select {
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.2s;
        width: 100%;
    }
    .dark .form-control, .dark .form-select {
        background: #1f2937;
        border-color: #374151;
        color: #f9fafb;
    }
    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #0071AA;
        box-shadow: 0 0 0 3px rgba(0, 113, 170, 0.1);
    }

    .text-muted {
        color: #6b7280;
        font-size: 0.8rem;
        margin-top: 0.35rem;
        display: block;
    }
    .dark .text-muted { color: #9ca3af; }

    /* Modal Buttons */
    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
    }
    .btn-secondary:hover {
        background: #d1d5db;
    }
    .dark .btn-secondary {
        background: #374151;
        color: #e5e7eb;
    }
    .dark .btn-secondary:hover {
        background: #4b5563;
    }
    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }
    .btn-success:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
    }
    .btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
    }
    .btn-warning:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.35);
    }
    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
    }
    .btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.35);
    }
    .btn-primary {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(0, 113, 170, 0.25);
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0, 113, 170, 0.35);
    }

    /* Warning Alert in Modal */
    .alert-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border: 2px solid #fbbf24;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        color: #92400e;
        font-size: 0.9rem;
        line-height: 1.7;
    }
    .alert-warning svg {
        flex-shrink: 0;
        margin-top: 0.1rem;
    }
    .dark .alert-warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.1) 100%);
        border-color: rgba(251, 191, 36, 0.3);
        color: #fbbf24;
    }

    .mb-3 {
        margin-bottom: 1.25rem;
    }
</style>
@endpush

@section('content')
<div class="payment-page">
    @if(session('success'))
        <div class="alert-success">
            <div class="alert-success-icon">
                <svg style="width: 18px; height: 18px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <span class="alert-success-text">{{ session('success') }}</span>
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
        </div>
    @endif

    <!-- Header -->
    <div class="payment-header">
        <div class="flex items-start gap-4 relative z-10">
            <a href="{{ route('admin.payments.index') }}" class="back-btn">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-2xl font-extrabold mb-1">تفاصيل الدفعة #{{ $payment->id }}</h1>
                        <p class="opacity-75 text-sm">عرض التفاصيل الكاملة للدفعة والمعاملات</p>
                    </div>
                    @if(!$payment->isCancelled() && !$payment->isFullyPaid())
                        <div class="header-actions">
                            <button type="button" class="header-btn header-btn-success" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                تسجيل دفعة
                            </button>
                            <button type="button" class="header-btn header-btn-warning" data-bs-toggle="modal" data-bs-target="#waiveModal">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                                </svg>
                                إعفاء
                            </button>
                            <button type="button" class="header-btn header-btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                إلغاء
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Payment Summary -->
            <div class="pay-card">
                <div class="pay-card-head">
                    <div class="pay-card-title">
                        <div class="pay-card-icon" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                            <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        ملخص الدفعة
                    </div>
                </div>
                <div class="pay-card-body">
                    <div class="mb-4">
                        @if($payment->status == 'pending')
                            <span class="status-badge status-pending">
                                <svg style="width: 14px; height: 14px;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                قيد الانتظار
                            </span>
                        @elseif($payment->status == 'partial')
                            <span class="status-badge status-partial">
                                <svg style="width: 14px; height: 14px;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-6a1 1 0 011-1h4a1 1 0 110 2h-4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                </svg>
                                دفع جزئي
                            </span>
                        @elseif($payment->status == 'completed')
                            <span class="status-badge status-completed">
                                <svg style="width: 14px; height: 14px;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                مكتملة
                            </span>
                        @elseif($payment->status == 'cancelled')
                            <span class="status-badge status-cancelled">
                                <svg style="width: 14px; height: 14px;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                ملغاة
                            </span>
                        @elseif($payment->status == 'refunded')
                            <span class="status-badge status-refunded">
                                <svg style="width: 14px; height: 14px;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                </svg>
                                مستردة
                            </span>
                        @endif
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">إجمالي المبلغ</span>
                        <span class="summary-value">{{ number_format($payment->total_amount, 2) }} ر.س</span>
                    </div>

                    @if($payment->discount_amount > 0)
                        <div class="summary-item">
                            <span class="summary-label">الخصم</span>
                            <span class="summary-value summary-value-danger">- {{ number_format($payment->discount_amount, 2) }} ر.س</span>
                        </div>
                    @endif

                    <div class="summary-item">
                        <span class="summary-label">المدفوع</span>
                        <span class="summary-value summary-value-success">{{ number_format($payment->paid_amount, 2) }} ر.س</span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">المتبقي</span>
                        <span class="summary-value summary-value-primary">{{ number_format($payment->remaining_amount, 2) }} ر.س</span>
                    </div>

                    <div class="mt-4">
                        @php
                            $percentage = $payment->total_amount > 0 ? ($payment->paid_amount / $payment->total_amount) * 100 : 0;
                        @endphp
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: {{ $percentage }}%">
                                {{ number_format($percentage, 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student & Program Info -->
            <div class="pay-card">
                <div class="pay-card-head">
                    <div class="pay-card-title">
                        <div class="pay-card-icon" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                            <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        معلومات الطالب
                    </div>
                </div>
                <div class="pay-card-body">
                    <div class="info-item">
                        <div class="info-label">الطالب</div>
                        <div class="info-value">{{ $payment->user->name }}</div>
                        <div class="info-subvalue">{{ $payment->user->email }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">البرنامج</div>
                        <div class="info-value">{{ $payment->program->name_ar }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">نوع الدفع</div>
                        <div>
                            @if($payment->payment_type == 'full')
                                <span class="payment-type-badge payment-type-full">دفعة كاملة</span>
                            @else
                                <span class="payment-type-badge payment-type-installment">تقسيط</span>
                            @endif
                        </div>
                    </div>

                    @if($payment->payment_method)
                        <div class="info-item">
                            <div class="info-label">طريقة الدفع</div>
                            <div class="info-value">
                                @if($payment->payment_method == 'cash')
                                    نقدي
                                @elseif($payment->payment_method == 'bank_transfer')
                                    تحويل بنكي
                                @elseif($payment->payment_method == 'tamara')
                                    تمارا
                                @elseif($payment->payment_method == 'paytabs')
                                    PayTabs
                                @elseif($payment->payment_method == 'waived')
                                    معفي
                                @else
                                    {{ $payment->payment_method }}
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">تاريخ الإنشاء</div>
                        <div class="info-value">{{ $payment->created_at->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>

            @if($payment->notes)
                <div class="pay-card">
                    <div class="pay-card-head">
                        <div class="pay-card-title">
                            <div class="pay-card-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                            </div>
                            ملاحظات
                        </div>
                    </div>
                    <div class="pay-card-body">
                        <p style="margin: 0; color: #374151; line-height: 1.6;">{{ $payment->notes }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Installments -->
            @if($payment->payment_type == 'installment')
                <div class="pay-card">
                    <div class="pay-card-head">
                        <div class="pay-card-title">
                            <div class="pay-card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                                <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            الأقساط
                        </div>
                        @if($payment->installments->count() == 0 && !$payment->isCancelled())
                            <button type="button" class="action-btn action-btn-primary" data-bs-toggle="modal" data-bs-target="#createInstallmentModal">
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                إنشاء خطة تقسيط
                            </button>
                        @endif
                    </div>
                    <div class="pay-card-body" style="padding: 0; overflow-x: auto;">
                        @if($payment->installments->count() > 0)
                            <table class="custom-table">
                                <thead>
                                    <tr>
                                        <th>القسط</th>
                                        <th>المبلغ</th>
                                        <th>تاريخ الاستحقاق</th>
                                        <th>الحالة</th>
                                        <th>تاريخ الدفع</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payment->installments as $installment)
                                        <tr>
                                            <td>القسط #{{ $installment->installment_number }}</td>
                                            <td>{{ number_format($installment->amount, 2) }} ر.س</td>
                                            <td>{{ $installment->due_date->format('Y-m-d') }}</td>
                                            <td>
                                                @if($installment->status == 'pending')
                                                    @if($installment->isOverdue())
                                                        <span class="table-badge badge-danger">متأخر</span>
                                                    @else
                                                        <span class="table-badge badge-warning">قيد الانتظار</span>
                                                    @endif
                                                @elseif($installment->status == 'paid')
                                                    <span class="table-badge badge-success">مدفوع</span>
                                                @elseif($installment->status == 'cancelled')
                                                    <span class="table-badge badge-secondary">ملغي</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($installment->paid_at)
                                                    {{ $installment->paid_at->format('Y-m-d') }}
                                                @else
                                                    --
                                                @endif
                                            </td>
                                            <td>
                                                @if($installment->status == 'pending')
                                                    <button type="button" class="action-btn action-btn-success" onclick="recordInstallmentPayment({{ $installment->id }})">
                                                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        تسجيل
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="empty-state">
                                <div class="empty-state-icon">📋</div>
                                <p class="empty-state-text">لم يتم إنشاء خطة تقسيط بعد</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Transactions -->
            <div class="pay-card">
                <div class="pay-card-head">
                    <div class="pay-card-title">
                        <div class="pay-card-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                            <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        سجل المعاملات
                    </div>
                </div>
                <div class="pay-card-body" style="padding: 0; overflow-x: auto;">
                    @if($payment->transactions->count() > 0)
                        <table class="custom-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المبلغ</th>
                                    <th>النوع</th>
                                    <th>طريقة الدفع</th>
                                    <th>المرجع</th>
                                    <th>الحالة</th>
                                    <th>تم بواسطة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payment->transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td>{{ number_format($transaction->amount, 2) }} ر.س</td>
                                        <td>
                                            @if($transaction->type == 'payment')
                                                <span class="table-badge badge-success">دفعة</span>
                                            @elseif($transaction->type == 'refund')
                                                <span class="table-badge badge-danger">استرداد</span>
                                            @elseif($transaction->type == 'adjustment')
                                                <span class="table-badge badge-info">تعديل</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($transaction->payment_method == 'cash')
                                                نقدي
                                            @elseif($transaction->payment_method == 'bank_transfer')
                                                تحويل بنكي
                                            @elseif($transaction->payment_method == 'tamara')
                                                تمارا
                                            @elseif($transaction->payment_method == 'paytabs')
                                                PayTabs
                                            @elseif($transaction->payment_method == 'waived')
                                                معفي
                                            @else
                                                {{ $transaction->payment_method }}
                                            @endif
                                        </td>
                                        <td>{{ $transaction->transaction_reference ?? '--' }}</td>
                                        <td>
                                            @if($transaction->status == 'success')
                                                <span class="table-badge badge-success">ناجح</span>
                                            @elseif($transaction->status == 'pending')
                                                <span class="table-badge badge-warning">قيد المعالجة</span>
                                            @elseif($transaction->status == 'failed')
                                                <span class="table-badge badge-danger">فشل</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaction->creator->name ?? '--' }}</td>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">🧾</div>
                            <p class="empty-state-text">لا توجد معاملات بعد</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.record-payment', $payment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg style="width: 22px; height: 22px; color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        تسجيل دفعة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المبلغ <span style="color: #ef4444;">*</span></label>
                        <input type="number" name="amount" class="form-control" step="0.01" min="0.01" max="{{ $payment->remaining_amount }}" required placeholder="أدخل المبلغ المدفوع">
                        <small class="text-muted">💰 المتبقي: {{ number_format($payment->remaining_amount, 2) }} ر.س</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">طريقة الدفع <span style="color: #ef4444;">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="">اختر طريقة الدفع</option>
                            <option value="cash">💵 نقدي</option>
                            <option value="bank_transfer">🏦 تحويل بنكي</option>
                            <option value="waived">🎁 معفي</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">رقم المرجع/الإيصال</label>
                        <input type="text" name="transaction_reference" class="form-control" placeholder="رقم الإيصال أو المرجع (اختياري)">
                    </div>

                    <div class="mb-3" style="margin-bottom: 0;">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="أضف أي ملاحظات إضافية..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-success">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        تسجيل الدفعة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Waive Modal -->
<div class="modal fade" id="waiveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.waive', $payment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg style="width: 22px; height: 22px; color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                        إعفاء من الدفع
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">مبلغ الإعفاء <span style="color: #ef4444;">*</span></label>
                        <input type="number" name="waive_amount" class="form-control" step="0.01" min="0.01" max="{{ $payment->remaining_amount }}" required placeholder="أدخل مبلغ الإعفاء">
                        <small class="text-muted">💰 المتبقي: {{ number_format($payment->remaining_amount, 2) }} ر.س</small>
                    </div>

                    <div class="mb-3" style="margin-bottom: 0;">
                        <label class="form-label">السبب <span style="color: #ef4444;">*</span></label>
                        <textarea name="reason" class="form-control" rows="4" required placeholder="اشرح سبب الإعفاء بالتفصيل..."></textarea>
                        <small class="text-muted">⚠️ سيتم تسجيل هذا السبب في سجل المعاملات</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        تأكيد الإعفاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.cancel', $payment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg style="width: 22px; height: 22px; color: #ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        إلغاء الدفعة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert-warning">
                        <svg style="width: 20px; height: 20px; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>
                            <strong style="display: block; margin-bottom: 0.5rem;">تحذير هام!</strong>
                            هل أنت متأكد من إلغاء هذه الدفعة؟ سيتم إلغاء جميع الأقساط المعلقة ولن يمكن التراجع عن هذا الإجراء.
                        </div>
                    </div>

                    <div class="mb-3" style="margin-bottom: 0;">
                        <label class="form-label">سبب الإلغاء <span style="color: #ef4444;">*</span></label>
                        <textarea name="reason" class="form-control" rows="4" required placeholder="يرجى توضيح سبب إلغاء الدفعة بالتفصيل..."></textarea>
                        <small class="text-muted">📝 سيتم حفظ هذا السبب في السجلات</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        تراجع
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        تأكيد الإلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Installment Plan Modal -->
@if($payment->payment_type == 'installment' && $payment->installments->count() == 0)
<div class="modal fade" id="createInstallmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.installment-plan', $payment) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg style="width: 22px; height: 22px; color: #0071AA;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        إنشاء خطة تقسيط
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">عدد الأقساط <span style="color: #ef4444;">*</span></label>
                        <input type="number" name="number_of_installments" class="form-control" min="2" max="12" value="3" required placeholder="أدخل عدد الأقساط">
                        <small class="text-muted">📊 من 2 إلى 12 قسط - المبلغ: {{ number_format($payment->total_amount, 2) }} ر.س</small>
                    </div>

                    <div class="mb-3" style="margin-bottom: 0;">
                        <label class="form-label">تاريخ بداية التقسيط <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        <small class="text-muted">📅 سيتم احتساب مواعيد الأقساط بناءً على هذا التاريخ</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        إنشاء خطة التقسيط
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Record Installment Payment Form (Hidden) -->
<form id="recordInstallmentForm" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="payment_method" value="cash">
</form>

@push('head-scripts')
<!-- Bootstrap JS for Modals -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush

@push('scripts')
<script>
    // Initialize Bootstrap modals when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Get all modal trigger buttons
        const modalButtons = document.querySelectorAll('[data-bs-toggle="modal"]');

        // Add click event listeners to each button
        modalButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const targetModalId = this.getAttribute('data-bs-target');
                const modalElement = document.querySelector(targetModalId);

                if (modalElement) {
                    // Create and show Bootstrap modal
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }
            });
        });
    });

    function recordInstallmentPayment(installmentId) {
        if (confirm('هل تريد تسجيل دفع هذا القسط؟')) {
            const form = document.getElementById('recordInstallmentForm');
            form.action = `/admin/payments/installments/${installmentId}/record-payment`;
            form.submit();
        }
    }
</script>
@endpush
@endsection
