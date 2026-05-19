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

    /* Receipt review card */
    .receipt-pending-card {
        background: linear-gradient(135deg,#fffbeb,#fef3c7);
        border: 2px solid #fde68a;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .receipt-pending-head {
        padding: 1.1rem 1.5rem;
        border-bottom: 1px solid #fde68a;
        display: flex; align-items: center; gap: .75rem;
        background: rgba(253,230,138,.3);
    }
    .receipt-item {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #fde68a;
        display: flex; align-items: flex-start; gap: 1rem; flex-wrap: wrap;
    }
    .receipt-item:last-child { border-bottom: none; }
    .receipt-img-wrap {
        width: 80px; height: 80px; border-radius: 12px; overflow: hidden;
        background: #f1f5f9; flex-shrink: 0; border: 2px solid #e5e7eb;
        display: flex; align-items: center; justify-content: center;
    }
    .receipt-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .badge-receipt-pending  { background: rgba(245,158,11,.15); color: #d97706; }
    .badge-receipt-approved { background: rgba(16,185,129,.15);  color: #059669; }
    .badge-receipt-rejected { background: rgba(239,68,68,.15);   color: #dc2626; }

    /* Stat boxes */
    .stat-box {
        background: #fff;
        border-radius: 20px;
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
    }
    .dark .stat-box { background: #1f2937; }
    .s-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .s-val {
        font-size: 1.5rem;
        font-weight: 900;
        color: #111827;
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    .dark .s-val { color: #f9fafb; }
    .s-lbl {
        font-size: 0.78rem;
        font-weight: 600;
        color: #6b7280;
        white-space: nowrap;
    }
    .dark .s-lbl { color: #9ca3af; }

    @keyframes blink {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }

    /* Timeline transaction dot + card */
    .tx-dot { width:20px; height:20px; border-radius:50%; flex-shrink:0; margin-top:2px; border:3px solid #fff; }
    .tx-payment-dot { background:#10b981; box-shadow:0 0 0 2px #10b981; }
    .tx-refund-dot  { background:#ef4444; box-shadow:0 0 0 2px #ef4444; }
    .tx-adjust-dot  { background:#f59e0b; box-shadow:0 0 0 2px #f59e0b; }
    .tx-card { flex:1; border-radius:12px; padding:1rem 1.25rem; }
    .tx-payment-card { background:#d1fae5; border:1px solid rgba(16,185,129,.2); }
    .tx-refund-card  { background:#fee2e2; border:1px solid rgba(239,68,68,.2); }
    .tx-adjust-card  { background:#fef3c7; border:1px solid rgba(245,158,11,.2); }
    .tx-payment-lbl { font-weight:800; font-size:0.95rem; color:#10b981; }
    .tx-refund-lbl  { font-weight:800; font-size:0.95rem; color:#ef4444; }
    .tx-adjust-lbl  { font-weight:800; font-size:0.95rem; color:#f59e0b; }

    /* Print receipt status */
    .rs-completed { background:#d1fae5; }
    .rs-cancelled { background:#fee2e2; }
    .rs-other     { background:#fef3c7; }
    .rs-txt-completed { color:#065f46; }
    .rs-txt-cancelled { color:#991b1b; }
    .rs-txt-other     { color:#92400e; }

    /* Flash messages */
    .flash-msg     { display:flex; align-items:center; gap:.75rem; padding:.9rem 1.25rem; border-radius:14px; font-size:.88rem; font-weight:600; margin-bottom:.75rem; }
    .flash-success { background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; }
    .flash-error   { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }
    .flash-warning { background:#fffbeb; border:1px solid #fde68a; color:#92400e; }
    .flash-info    { background:#eff6ff; border:1px solid #bfdbfe; color:#1d4ed8; }

    /* Custom modal overlay */
    .m-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.55);
        z-index: 1050;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        overflow-y: auto;
    }
    .m-overlay.open { display: flex; }
    .m-overlay .modal-dialog {
        width: 100%;
        max-width: 520px;
        margin: auto;
        position: relative;
        animation: mSlideIn .22s ease;
    }
    .m-overlay .modal-dialog.modal-lg { max-width: 720px; }
    @keyframes mSlideIn {
        from { transform: translateY(-18px) scale(.97); opacity: 0; }
        to   { transform: translateY(0) scale(1); opacity: 1; }
    }
    .m-overlay .modal-content {
        border-radius: 20px;
        border: none;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 20px 60px rgba(0,0,0,.25);
    }
    .dark .m-overlay .modal-content { background: #1f2937; }
</style>
@endpush

@section('content')
@php
    $pct      = $payment->total_amount > 0 ? ($payment->paid_amount / $payment->total_amount) * 100 : 0;
    $pctRound = round($pct, 1);
    $remPct   = min(100, round(100 - $pct, 1));
    $r        = 44;
    $circ     = 2 * pi() * $r;
    $dash     = $circ * (1 - $pct / 100);
    $barColor = $pct >= 100 ? '#10b981' : ($pct >= 50 ? '#0071AA' : '#f59e0b');
    $pendingReceipts = $payment->transactions->where('payment_method','bank_transfer')->where('receipt_status','pending');
@endphp
<script>
document.addEventListener('DOMContentLoaded',function(){
    document.documentElement.style.setProperty('--dyn-pct','{{ $pctRound }}%');
    document.documentElement.style.setProperty('--dyn-rem','{{ $remPct }}%');
});
</script>

<div class="payment-page space-y-6" dir="rtl">

{{-- ══ FLASH MESSAGES ══ --}}
@foreach(['success'=>'flash-success','error'=>'flash-error','warning'=>'flash-warning','info'=>'flash-info'] as $k=>$cls)
@if(session($k))
<div class="flash-msg {{ $cls }}">
    <svg style="width:18px;height:18px;flex-shrink:0;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
    {{ session($k) }}
</div>
@endif
@endforeach

{{-- ══ HEADER ══ --}}
<div class="payment-header">
    <div style="position:relative;z-index:10;">
        <div style="display:flex;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
            {{-- Back + icon --}}
            <div style="display:flex;align-items:center;gap:1rem;">
                <a href="{{ route('admin.payments.index') }}" class="back-btn" title="العودة">
                    <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <div style="width:56px;height:56px;border-radius:16px;background:rgba(255,255,255,.15);backdrop-filter:blur(8px);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:28px;height:28px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
            </div>

            {{-- Title + meta --}}
            <div style="flex:1;min-width:0;">
                <p style="font-size:.78rem;font-weight:600;opacity:.6;margin:0 0 .2rem;">دفعة #{{ $payment->id }} — {{ $payment->program->name_ar }}</p>
                <h1 style="font-size:1.75rem;font-weight:900;letter-spacing:-.5px;margin:0 0 .5rem;">{{ $payment->user->name }}</h1>
                <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap;">
                    @if($payment->status=='pending')
                        <span style="background:rgba(245,158,11,.2);color:#fef3c7;padding:.25rem .75rem;border-radius:20px;font-size:.75rem;font-weight:700;">⏳ قيد الانتظار</span>
                    @elseif($payment->status=='partial')
                        <span style="background:rgba(59,130,246,.2);color:#bfdbfe;padding:.25rem .75rem;border-radius:20px;font-size:.75rem;font-weight:700;">◑ مدفوع جزئياً</span>
                    @elseif($payment->status=='completed')
                        <span style="background:rgba(16,185,129,.2);color:#a7f3d0;padding:.25rem .75rem;border-radius:20px;font-size:.75rem;font-weight:700;">✓ مكتملة</span>
                    @elseif($payment->status=='cancelled')
                        <span style="background:rgba(239,68,68,.2);color:#fecaca;padding:.25rem .75rem;border-radius:20px;font-size:.75rem;font-weight:700;">✕ ملغاة</span>
                    @endif
                    <span style="font-size:.75rem;opacity:.55;">{{ $payment->payment_type=='full'?'دفعة كاملة':'تقسيط' }}</span>
                    <span style="font-size:.75rem;opacity:.55;">{{ $payment->created_at->format('Y/m/d') }}</span>
                    @if($pendingReceipts->count() > 0)
                    <span style="background:rgba(239,68,68,.25);color:#fecaca;padding:.25rem .75rem;border-radius:20px;font-size:.73rem;font-weight:800;animation:blink 1.5s infinite;">
                        🔔 {{ $pendingReceipts->count() }} إيصال بانتظار المراجعة
                    </span>
                    @endif
                </div>
            </div>

            {{-- Action buttons --}}
            <div class="header-actions" style="align-self:center;">
                <button type="button" class="header-btn" style="background:rgba(255,255,255,.18);font-size:.8rem;padding:.55rem 1rem;" data-bs-toggle="modal" data-bs-target="#receiptModal">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    طباعة
                </button>
                @if(!$payment->isCancelled() && !$payment->isFullyPaid())
                <button type="button" class="header-btn header-btn-success" style="font-size:.8rem;padding:.55rem 1rem;" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    تسجيل دفعة
                </button>
                <button type="button" class="header-btn header-btn-warning" style="font-size:.8rem;padding:.55rem 1rem;" data-bs-toggle="modal" data-bs-target="#waiveModal">
                    إعفاء
                </button>
                <button type="button" class="header-btn header-btn-danger" style="font-size:.8rem;padding:.55rem 1rem;" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    إلغاء
                </button>
                <button type="button" class="header-btn" style="background:rgba(99,102,241,.85);font-size:.8rem;padding:.55rem 1rem;" data-bs-toggle="modal" data-bs-target="#editAmountModal">
                    تعديل
                </button>
                @endif
                @if($payment->isFullyPaid())
                <button type="button" class="header-btn" style="background:rgba(239,68,68,.85);font-size:.8rem;padding:.55rem 1rem;" data-bs-toggle="modal" data-bs-target="#refundModal">
                    استرداد
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ══ STAT BOXES ══ --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;">
    <div class="stat-box">
        <div class="s-icon" style="background:linear-gradient(135deg,#0071AA,#004d77);">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="s-val">{{ number_format($payment->total_amount,0) }}<small style="font-size:.75rem;"> <x-riyal /></small></div>
            <div class="s-lbl">إجمالي المبلغ</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="s-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="s-val">{{ number_format($payment->paid_amount,0) }}<small style="font-size:.75rem;"> <x-riyal /></small></div>
            <div class="s-lbl">المدفوع</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="s-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <div class="s-val">{{ number_format($payment->remaining_amount,0) }}<small style="font-size:.75rem;"> <x-riyal /></small></div>
            <div class="s-lbl">المتبقي</div>
        </div>
    </div>
    <div class="stat-box">
        <div class="s-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed);">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        </div>
        <div>
            <div class="s-val">{{ number_format($pct,0) }}<small style="font-size:.8rem;">%</small></div>
            <div class="s-lbl">نسبة الدفع</div>
        </div>
    </div>
</div>

{{-- ══ ROW: Progress + Info ══ --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

    {{-- Progress card --}}
    <div class="pay-card" style="margin-bottom:0;">
        <div class="pay-card-head">
            <div class="pay-card-title">
                <div class="pay-card-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
                    <svg style="width:18px;height:18px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                تقدم السداد
            </div>
            <button type="button" class="action-btn action-btn-primary" style="font-size:.75rem;" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                ملاحظة
            </button>
        </div>
        <div class="pay-card-body" style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
            {{-- Donut --}}
            <div style="position:relative;width:100px;height:100px;flex-shrink:0;">
                <svg width="100" height="100" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="{{ $r }}" fill="none" stroke="#f3f4f6" stroke-width="9"/>
                    <circle cx="50" cy="50" r="{{ $r }}" fill="none" stroke="{{ $barColor }}" stroke-width="9"
                            stroke-dasharray="{{ $circ }}" stroke-dashoffset="{{ $dash }}"
                            stroke-linecap="round" transform="rotate(-90 50 50)"
                            style="transition:stroke-dashoffset 1s ease;"/>
                </svg>
                <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                    <span style="font-size:1.2rem;font-weight:900;color:#111827;">{{ number_format($pct,0) }}%</span>
                    <span style="font-size:.6rem;color:#9ca3af;">مكتمل</span>
                </div>
            </div>

            {{-- Bars --}}
            <div style="flex:1;min-width:150px;">
                <div style="margin-bottom:.75rem;">
                    <div style="display:flex;justify-content:space-between;margin-bottom:.25rem;">
                        <span style="font-size:.75rem;color:#6b7280;">المدفوع</span>
                        <span style="font-size:.75rem;font-weight:700;color:#10b981;">{{ number_format($payment->paid_amount,0) }} <x-riyal /></span>
                    </div>
                    <div style="height:7px;background:#f3f4f6;border-radius:4px;overflow:hidden;">
                        <div style="height:100%;border-radius:4px;background:#10b981;width:var(--dyn-pct);transition:width .8s ease;"></div>
                    </div>
                </div>
                @if($payment->discount_amount > 0)
                <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:.5rem .8rem;display:flex;justify-content:space-between;font-size:.75rem;margin-bottom:.75rem;">
                    <span style="color:#991b1b;">خصم مطبّق</span>
                    <span style="font-weight:700;color:#dc2626;">{{ number_format($payment->discount_amount,0) }} <x-riyal /></span>
                </div>
                @endif
                <div style="background:#f8fafc;border-radius:10px;padding:.6rem .9rem;">
                    <div style="display:flex;justify-content:space-between;font-size:.75rem;margin-bottom:.3rem;">
                        <span style="color:#6b7280;">المتبقي</span>
                        <span style="font-weight:700;color:#f59e0b;">{{ number_format($payment->remaining_amount,0) }} <x-riyal /></span>
                    </div>
                    <div style="height:7px;background:#f3f4f6;border-radius:4px;overflow:hidden;">
                        <div style="height:100%;border-radius:4px;background:#f59e0b;width:var(--dyn-rem);"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick actions strip --}}
        @if(!$payment->isCancelled() && !$payment->isFullyPaid())
        <div style="padding:.85rem 1.5rem;border-top:1px solid #f1f5f9;background:#fafafa;display:flex;gap:.5rem;flex-wrap:wrap;">
            <button type="button" class="action-btn action-btn-success" style="font-size:.75rem;" data-bs-toggle="modal" data-bs-target="#recordPaymentModal">
                + تسجيل دفعة يدوية
            </button>
            <button type="button" class="action-btn action-btn-primary" style="font-size:.75rem;" data-bs-toggle="modal" data-bs-target="#editAmountModal">
                تعديل المبلغ
            </button>
            <button type="button" class="action-btn" style="font-size:.75rem;background:rgba(245,158,11,.1);color:#d97706;" data-bs-toggle="modal" data-bs-target="#waiveModal">
                إعفاء جزئي
            </button>
        </div>
        @endif
    </div>

    {{-- Student + Program Info --}}
    <div class="pay-card" style="margin-bottom:0;">
        <div class="pay-card-head">
            <div class="pay-card-title">
                <div class="pay-card-icon" style="background:linear-gradient(135deg,#8b5cf6,#6d28d9);">
                    <svg style="width:18px;height:18px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                بيانات ال متدرب والدفعة
            </div>
            <a href="{{ route('admin.students.show', $payment->user) }}" class="action-btn action-btn-primary" style="font-size:.73rem;text-decoration:none;">
                ملف ال متدرب ←
            </a>
        </div>
        <div>
            @php
                $rows = [
                    ['الطالب',      $payment->user->name],
                    ['البريد',      $payment->user->email],
                    ['رقم الطالب',  $payment->user->student_code ?? '--'],
                    ['البرنامج',    $payment->program->name_ar],
                    ['نوع الدفع',   $payment->payment_type=='full'?'دفعة كاملة':'تقسيط'],
                    ['تاريخ الإنشاء', $payment->created_at->format('Y/m/d H:i')],
                ];
                if($payment->payment_method){
                    $methodMap = ['cash'=>'نقدي','bank_transfer'=>'تحويل بنكي','tamara'=>'تمارا','paytabs'=>'PayTabs','waived'=>'معفي'];
                    $rows[] = ['طريقة الدفع', $methodMap[$payment->payment_method] ?? $payment->payment_method];
                }
                if($payment->creator) $rows[] = ['أنشأ بواسطة', $payment->creator->name];
            @endphp
            @foreach($rows as $r)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:.65rem 1.5rem;border-bottom:1px solid #f8fafc;font-size:.83rem;">
                <span style="color:#6b7280;font-weight:500;">{{ $r[0] }}</span>
                <span style="font-weight:700;color:#111827;text-align:left;max-width:55%;word-break:break-word;">{{ $r[1] }}</span>
            </div>
            @endforeach
            @if($payment->notes)
            <div style="padding:.85rem 1.5rem;background:#fffbeb;border-top:1px dashed #fde68a;">
                <div style="font-size:.72rem;color:#d97706;font-weight:700;margin-bottom:.3rem;">ملاحظات</div>
                <div style="font-size:.82rem;color:#92400e;line-height:1.6;">{{ $payment->notes }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ══ INSTALLMENTS ══ --}}
@if($payment->payment_type == 'installment')
@php
    $paidInst    = $payment->installments->where('status','paid')->count();
    $overdueInst = $payment->installments->filter(fn($i)=>$i->isOverdue())->count();
    $pendingInst = $payment->installments->filter(fn($i)=>$i->status=='pending'&&!$i->isOverdue())->count();
@endphp
<div class="pay-card" style="margin-bottom:0;">
    <div class="pay-card-head">
        <div class="pay-card-title">
            <div class="pay-card-icon" style="background:linear-gradient(135deg,#10b981,#059669);">
                <svg style="width:18px;height:18px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            </div>
            الأقساط
        </div>
        <div style="display:flex;gap:.5rem;align-items:center;">
            @if($paidInst > 0)<span class="table-badge badge-success">{{ $paidInst }} مدفوع</span>@endif
            @if($pendingInst > 0)<span class="table-badge badge-warning">{{ $pendingInst }} قادم</span>@endif
            @if($overdueInst > 0)<span class="table-badge badge-danger">{{ $overdueInst }} متأخر</span>@endif
            @if($payment->installments->count() == 0 && !$payment->isCancelled())
            <button type="button" class="action-btn action-btn-primary" style="font-size:.75rem;" data-bs-toggle="modal" data-bs-target="#createInstallmentModal">
                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                إنشاء خطة
            </button>
            @endif
        </div>
    </div>
    <div style="overflow-x:auto;">
        @if($payment->installments->count() > 0)
        <table class="custom-table">
            <thead><tr>
                <th>القسط</th><th>المبلغ</th><th>الاستحقاق</th><th>الحالة</th><th>تاريخ الدفع</th><th>إجراء</th>
            </tr></thead>
            <tbody>
            @foreach($payment->installments as $inst)
            <tr>
                <td><strong>#{{ $inst->installment_number }}</strong></td>
                <td><strong>{{ number_format($inst->amount,2) }} <x-riyal /></strong></td>
                <td>{{ $inst->due_date->format('Y/m/d') }}</td>
                <td>
                    @if($inst->status=='paid') <span class="table-badge badge-success">مدفوع</span>
                    @elseif($inst->isOverdue()) <span class="table-badge badge-danger">متأخر</span>
                    @elseif($inst->status=='cancelled') <span class="table-badge badge-secondary">ملغي</span>
                    @else <span class="table-badge badge-warning">قيد الانتظار</span>
                    @endif
                </td>
                <td>{{ $inst->paid_at ? $inst->paid_at->format('Y/m/d') : '--' }}</td>
                <td>
                    @if($inst->status=='pending')
                    <button type="button" class="action-btn action-btn-success" style="font-size:.75rem;" data-inst-id="{{ $inst->id }}" onclick="recordInstallmentPayment(this.dataset.instId)">
                        <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        تسجيل
                    </button>
                    @else --
                    @endif
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state"><div class="empty-state-icon">📋</div><p class="empty-state-text">لم يتم إنشاء خطة تقسيط بعد</p></div>
        @endif
    </div>
</div>
@endif

{{-- ══ PENDING RECEIPTS ══ --}}
@if($pendingReceipts->count() > 0)
<div class="receipt-pending-card">
    <div class="receipt-pending-head">
        <div class="pay-card-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
            <svg style="width:20px;height:20px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v8"/>
            </svg>
        </div>
        <div style="flex:1;">
            <div style="font-size:.95rem;font-weight:800;color:#92400e;">إيصالات بانتظار المراجعة</div>
            <div style="font-size:.78rem;color:#b45309;margin-top:.1rem;">{{ $pendingReceipts->count() }} إيصال بحاجة للموافقة أو الرفض</div>
        </div>
        <span class="table-badge badge-warning">{{ $pendingReceipts->count() }} معلق</span>
    </div>
    @foreach($pendingReceipts as $receipt)
    <div class="receipt-item">
        <div class="receipt-img-wrap">
            @if($receipt->receipt_path)
                @php $ext = strtolower(pathinfo($receipt->receipt_path,PATHINFO_EXTENSION)); @endphp
                @if(in_array($ext,['jpg','jpeg','png']))
                    <img src="{{ asset('storage/'.$receipt->receipt_path) }}" alt="إيصال">
                @else
                    <svg style="width:28px;height:28px;color:#6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                @endif
            @else
                <svg style="width:28px;height:28px;color:#6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            @endif
        </div>
        <div style="flex:1;min-width:140px;">
            <div style="font-size:.95rem;font-weight:800;color:#111827;">{{ number_format($receipt->amount,2) }} <x-riyal /></div>
            <div style="font-size:.75rem;color:#6b7280;margin-top:.15rem;">{{ $receipt->created_at->format('Y/m/d H:i') }}</div>
            @if($receipt->notes)<div style="font-size:.75rem;color:#6b7280;margin-top:.1rem;">{{ $receipt->notes }}</div>@endif
            @if($receipt->receipt_path)
            <a href="{{ asset('storage/'.$receipt->receipt_path) }}" target="_blank"
               style="display:inline-flex;align-items:center;gap:.25rem;margin-top:.4rem;font-size:.73rem;font-weight:700;color:#0071AA;">
                <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                عرض كامل
            </a>
            @endif
        </div>
        <div style="display:flex;flex-direction:column;gap:.5rem;flex-shrink:0;">
            <form action="{{ route('admin.payments.transactions.approve-receipt', $receipt) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success" style="padding:.45rem .9rem;font-size:.78rem;width:100%;" onclick="return confirm('تأكيد قبول الإيصال وتسجيل الدفعة؟')">
                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    قبول
                </button>
            </form>
            <button type="button" class="btn btn-danger" style="padding:.45rem .9rem;font-size:.78rem;" data-receipt-id="{{ $receipt->id }}" onclick="openRejectModal(this.dataset.receiptId)">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                رفض
            </button>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- ══ TRANSACTIONS ══ --}}
<div class="pay-card" style="margin-bottom:0;">
    <div class="pay-card-head">
        <div class="pay-card-title">
            <div class="pay-card-icon" style="background:linear-gradient(135deg,#3b82f6,#2563eb);">
                <svg style="width:18px;height:18px;color:#fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
            سجل المعاملات
        </div>
        <button class="action-btn action-btn-primary" style="font-size:.73rem;" data-bs-toggle="modal" data-bs-target="#timelineModal">
            <svg style="width:13px;height:13px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            جدول الأحداث
        </button>
    </div>
    <div style="overflow-x:auto;">
        @if($payment->transactions->count() > 0)
        <table class="custom-table">
            <thead><tr>
                <th>#</th><th>المبلغ</th><th>النوع</th><th>طريقة الدفع</th><th>المرجع</th><th>الحالة</th><th>الإيصال</th><th>بواسطة</th><th>التاريخ</th>
            </tr></thead>
            <tbody>
            @foreach($payment->transactions as $trx)
            <tr>
                <td style="color:#9ca3af;font-size:.78rem;">{{ $trx->id }}</td>
                <td><strong>{{ number_format($trx->amount,2) }} <x-riyal /></strong></td>
                <td>
                    @if($trx->type=='payment') <span class="table-badge badge-success">دفعة</span>
                    @elseif($trx->type=='refund') <span class="table-badge badge-danger">استرداد</span>
                    @else <span class="table-badge badge-info">تعديل</span>
                    @endif
                </td>
                <td>
                    @php $mmap=['cash'=>'نقدي','bank_transfer'=>'تحويل بنكي','tamara'=>'تمارا','paytabs'=>'PayTabs','waived'=>'معفي']; @endphp
                    {{ $mmap[$trx->payment_method] ?? $trx->payment_method }}
                </td>
                <td style="font-size:.78rem;color:#6b7280;">{{ $trx->transaction_reference ?? '--' }}</td>
                <td>
                    @if($trx->status=='success') <span class="table-badge badge-success">ناجح</span>
                    @elseif($trx->status=='pending') <span class="table-badge badge-warning">معالجة</span>
                    @else <span class="table-badge badge-danger">فشل</span>
                    @endif
                </td>
                <td>
                    @if($trx->receipt_status=='pending') <span class="table-badge badge-receipt-pending">⏳ مراجعة</span>
                    @elseif($trx->receipt_status=='approved') <span class="table-badge badge-receipt-approved">✓ مقبول</span>
                    @elseif($trx->receipt_status=='rejected') <span class="table-badge badge-receipt-rejected">✕ مرفوض</span>
                    @else <span style="color:#d1d5db;">--</span>
                    @endif
                </td>
                <td style="font-size:.78rem;">{{ $trx->creator->name ?? '--' }}</td>
                <td style="font-size:.75rem;color:#6b7280;">{{ $trx->created_at->format('Y/m/d H:i') }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state"><div class="empty-state-icon">🧾</div><p class="empty-state-text">لا توجد معاملات بعد</p></div>
        @endif
    </div>
</div>

</div>{{-- end .payment-page --}}

<!-- Record Payment Modal -->
<div class="m-overlay" id="recordPaymentModal" tabindex="-1">
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
                        <small class="text-muted">💰 المتبقي: {{ number_format($payment->remaining_amount, 2) }} <x-riyal /></small>
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
<div class="m-overlay" id="waiveModal" tabindex="-1">
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
                        <small class="text-muted">💰 المتبقي: {{ number_format($payment->remaining_amount, 2) }} <x-riyal /></small>
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
<div class="m-overlay" id="cancelModal" tabindex="-1">
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
<div class="m-overlay" id="createInstallmentModal" tabindex="-1">
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
                        <small class="text-muted">📊 من 2 إلى 12 قسط - المبلغ: {{ number_format($payment->total_amount, 2) }} <x-riyal /></small>
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

{{-- ════════════════════════════ NEW MODALS ════════════════════════════ --}}

<!-- ① Print Receipt Modal -->
<div class="m-overlay" id="receiptModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg style="width:22px;height:22px;color:#0071AA" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    معاينة الإيصال
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:0">
                <!-- Receipt Preview -->
                <div id="receiptPrintArea" style="background:#fff;padding:2.5rem;font-family:Arial,sans-serif;direction:rtl">
                    <!-- Header -->
                    <div style="text-align:center;border-bottom:3px solid #0071AA;padding-bottom:1.5rem;margin-bottom:1.5rem">
                        <div style="width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                            <svg width="28" height="28" fill="white" viewBox="0 0 24 24"><path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/></svg>
                        </div>
                        <h2 style="font-size:1.4rem;font-weight:900;color:#0071AA;margin:0">إيصال دفعة</h2>
                        <p style="color:#6b7280;font-size:0.85rem;margin:4px 0 0">المنصة التعليمية</p>
                    </div>
                    <!-- Receipt Number -->
                    <div style="display:flex;justify-content:space-between;background:#f8fafc;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.5rem">
                        <div>
                            <div style="font-size:0.75rem;color:#9ca3af;font-weight:600">رقم الإيصال</div>
                            <div style="font-size:1.1rem;font-weight:800;color:#111827">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
                        </div>
                        <div style="text-align:left">
                            <div style="font-size:0.75rem;color:#9ca3af;font-weight:600">التاريخ</div>
                            <div style="font-size:1rem;font-weight:700;color:#111827">{{ $payment->created_at->format('Y/m/d') }}</div>
                        </div>
                    </div>
                    <!-- Student & Program -->
                    <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;margin-bottom:1.5rem">
                        <div style="background:#f1f5f9;padding:10px 16px;font-size:0.8rem;font-weight:700;color:#374151;border-bottom:1px solid #e5e7eb">بيانات ال متدرب والبرنامج</div>
                        <div style="padding:1rem 1.25rem">
                            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed #f3f4f6">
                                <span style="color:#6b7280;font-size:0.875rem">الاسم</span>
                                <span style="font-weight:700;color:#111827;font-size:0.875rem">{{ $payment->user->name }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed #f3f4f6">
                                <span style="color:#6b7280;font-size:0.875rem">البريد الإلكتروني</span>
                                <span style="font-weight:600;color:#374151;font-size:0.875rem">{{ $payment->user->email }}</span>
                            </div>
                            <div style="display:flex;justify-content:space-between;padding:6px 0">
                                <span style="color:#6b7280;font-size:0.875rem">البرنامج</span>
                                <span style="font-weight:700;color:#111827;font-size:0.875rem">{{ $payment->program->name_ar }}</span>
                            </div>
                        </div>
                    </div>
                    <!-- Amount Summary -->
                    <div style="border:1px solid #e5e7eb;border-radius:12px;overflow:hidden;margin-bottom:1.5rem">
                        <div style="background:#f1f5f9;padding:10px 16px;font-size:0.8rem;font-weight:700;color:#374151;border-bottom:1px solid #e5e7eb">تفاصيل المبالغ</div>
                        <div style="padding:1rem 1.25rem">
                            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed #f3f4f6">
                                <span style="color:#6b7280;font-size:0.875rem">إجمالي البرنامج</span>
                                <span style="font-weight:700;color:#111827">{{ number_format($payment->total_amount, 2) }} <x-riyal /></span>
                            </div>
                            @if($payment->discount_amount > 0)
                            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed #f3f4f6">
                                <span style="color:#6b7280;font-size:0.875rem">الخصم</span>
                                <span style="font-weight:700;color:#ef4444">- {{ number_format($payment->discount_amount, 2) }} <x-riyal /></span>
                            </div>
                            @endif
                            <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px dashed #f3f4f6">
                                <span style="color:#6b7280;font-size:0.875rem">المدفوع</span>
                                <span style="font-weight:700;color:#10b981">{{ number_format($payment->paid_amount, 2) }} <x-riyal /></span>
                            </div>
                            <div style="display:flex;justify-content:space-between;padding:10px 0;margin-top:4px;border-top:2px solid #0071AA">
                                <span style="font-weight:800;color:#111827">المتبقي</span>
                                <span style="font-weight:900;font-size:1.1rem;color:#0071AA">{{ number_format($payment->remaining_amount, 2) }} <x-riyal /></span>
                            </div>
                        </div>
                    </div>
                    <!-- Status -->
                    @php
                        $rsBg  = $payment->status=='completed' ? 'rs-completed' : ($payment->status=='cancelled' ? 'rs-cancelled' : 'rs-other');
                        $rsTxt = $payment->status=='completed' ? 'rs-txt-completed' : ($payment->status=='cancelled' ? 'rs-txt-cancelled' : 'rs-txt-other');
                    @endphp
                    <div class="{{ $rsBg }}" style="text-align:center;padding:1rem;border-radius:12px;margin-bottom:1.5rem">
                        <span class="{{ $rsTxt }}" style="font-weight:800;font-size:1rem">
                            {{ $payment->status=='completed' ? '✅ مكتملة' : ($payment->status=='cancelled' ? '❌ ملغاة' : ($payment->status=='partial' ? '🔄 جزئية' : '⏳ قيد الانتظار')) }}
                        </span>
                    </div>
                    <!-- Footer -->
                    <div style="text-align:center;color:#9ca3af;font-size:0.75rem;border-top:1px dashed #e5e7eb;padding-top:1rem">
                        <p style="margin:0">هذا الإيصال صادر إلكترونياً ولا يحتاج إلى توقيع</p>
                        <p style="margin:4px 0 0">{{ now()->format('Y-m-d H:i:s') }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    طباعة
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ② Send Email Receipt Modal -->
<div class="m-overlay" id="emailReceiptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg style="width:22px;height:22px;color:#6366f1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    إرسال الإيصال بالبريد
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Student info preview -->
                <div style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:14px;padding:1.25rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:1rem">
                    <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#4f46e5);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <span style="color:#fff;font-weight:800;font-size:1.1rem">{{ mb_substr($payment->user->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <div style="font-weight:700;color:#1e3a8a">{{ $payment->user->name }}</div>
                        <div style="font-size:0.85rem;color:#3b82f6">{{ $payment->user->email }}</div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">البريد الإلكتروني للإرسال</label>
                    <input type="email" class="form-control" value="{{ $payment->user->email }}" id="receiptEmail" placeholder="example@email.com">
                </div>
                <div class="mb-3">
                    <label class="form-label">رسالة إضافية (اختياري)</label>
                    <textarea class="form-control" rows="3" placeholder="أضف ملاحظة لل متدرب مع الإيصال..."></textarea>
                </div>
                <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:0.75rem 1rem;font-size:0.85rem;color:#15803d">
                    ✅ سيتم إرسال إيصال PDF كامل مع تفاصيل الدفعة إلى البريد المحدد
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn" style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;box-shadow:0 4px 12px rgba(99,102,241,0.3)"
                        onclick="alert('تم إرسال الإيصال بنجاح إلى: ' + document.getElementById('receiptEmail').value); closeModal(document.getElementById('emailReceiptModal'));">
                    <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    إرسال الإيصال
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ③ Edit Amount Modal -->
@if(!$payment->isCancelled() && !$payment->isFullyPaid())
<div class="m-overlay" id="editAmountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.update', $payment) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg style="width:22px;height:22px;color:#10b981" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        تعديل تفاصيل الدفعة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div style="background:#fef3c7;border:1.5px solid #fbbf24;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;display:flex;gap:10px;align-items:flex-start;font-size:0.875rem;color:#92400e">
                        <svg style="width:20px;height:20px;flex-shrink:0;color:#d97706;margin-top:1px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        تعديل المبلغ سيؤثر على حسابات الأقساط والمتبقي. تأكد من الأرقام قبل الحفظ.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">المبلغ الإجمالي <span style="color:#ef4444">*</span></label>
                        <input type="number" name="total_amount" class="form-control" step="0.01" min="0"
                               value="{{ $payment->total_amount }}" required>
                        <small class="text-muted">القيمة الحالية: {{ number_format($payment->total_amount, 2) }} <x-riyal /></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مبلغ الخصم</label>
                        <input type="number" name="discount_amount" class="form-control" step="0.01" min="0"
                               value="{{ $payment->discount_amount }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظة سبب التعديل <span style="color:#ef4444">*</span></label>
                        <textarea name="edit_reason" class="form-control" rows="3" required
                                  placeholder="وضّح سبب تعديل المبلغ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">
                        <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ④ Refund Modal -->
@if($payment->isFullyPaid())
<div class="m-overlay" id="refundModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.refund', $payment) }}" method="POST">
                @csrf
                <div class="modal-header" style="background:linear-gradient(135deg,#fef2f2,#fee2e2)">
                    <h5 class="modal-title">
                        <svg style="width:22px;height:22px;color:#ef4444" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        استرداد المبلغ
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert-warning" style="margin-bottom:1.25rem">
                        <svg style="width:20px;height:20px;color:#d97706;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div><strong style="display:block;margin-bottom:4px">تنبيه هام</strong>
                        عملية الاسترداد لا يمكن التراجع عنها. سيتم تسجيل معاملة استرداد وتغيير حالة الدفعة.</div>
                    </div>
                    <div style="background:#f8fafc;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem">
                        <div style="display:flex;justify-content:space-between;margin-bottom:8px">
                            <span style="color:#6b7280;font-size:0.875rem">المبلغ المدفوع</span>
                            <span style="font-weight:700;color:#10b981">{{ number_format($payment->paid_amount, 2) }} <x-riyal /></span>
                        </div>
                        <div style="display:flex;justify-content:space-between">
                            <span style="color:#6b7280;font-size:0.875rem">الحد الأقصى للاسترداد</span>
                            <span style="font-weight:700;color:#ef4444">{{ number_format($payment->paid_amount, 2) }} <x-riyal /></span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مبلغ الاسترداد <span style="color:#ef4444">*</span></label>
                        <input type="number" name="refund_amount" class="form-control" step="0.01"
                               min="0.01" max="{{ $payment->paid_amount }}" required
                               placeholder="أدخل المبلغ المراد استرداده">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">طريقة الاسترداد <span style="color:#ef4444">*</span></label>
                        <select name="refund_method" class="form-select" required>
                            <option value="">اختر طريقة الاسترداد</option>
                            <option value="cash">💵 نقدي</option>
                            <option value="bank_transfer">🏦 تحويل بنكي</option>
                        </select>
                    </div>
                    <div class="mb-3" style="margin-bottom:0">
                        <label class="form-label">سبب الاسترداد <span style="color:#ef4444">*</span></label>
                        <textarea name="refund_reason" class="form-control" rows="3" required
                                  placeholder="وضّح سبب الاسترداد بالتفصيل..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">
                        <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                        تأكيد الاسترداد
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- ⑤ Payment Timeline Modal -->
<div class="m-overlay" id="timelineModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <svg style="width:22px;height:22px;color:#8b5cf6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    سجل الأحداث الكامل
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if($payment->transactions->count() > 0)
                <div style="position:relative;padding-right:2rem">
                    <div style="position:absolute;right:9px;top:0;bottom:0;width:2px;background:linear-gradient(to bottom,#8b5cf6,#e5e7eb)"></div>
                    @foreach($payment->transactions as $i => $tx)
                    @php
                        $txDot = $tx->type=='payment' ? 'tx-payment-dot' : ($tx->type=='refund' ? 'tx-refund-dot' : 'tx-adjust-dot');
                        $txCard= $tx->type=='payment' ? 'tx-payment-card' : ($tx->type=='refund' ? 'tx-refund-card' : 'tx-adjust-card');
                        $txLbl = $tx->type=='payment' ? 'tx-payment-lbl' : ($tx->type=='refund' ? 'tx-refund-lbl' : 'tx-adjust-lbl');
                        $txLabel = $tx->type=='payment' ? 'دفعة' : ($tx->type=='refund' ? 'استرداد' : 'تعديل');
                    @endphp
                    <div style="display:flex;gap:1rem;margin-bottom:1.5rem;align-items:flex-start">
                        <div class="tx-dot {{ $txDot }}"></div>
                        <div class="tx-card {{ $txCard }}">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                                <span class="{{ $txLbl }}">{{ $txLabel }}</span>
                                <span style="font-size:0.75rem;color:#6b7280">{{ $tx->created_at->format('Y/m/d H:i') }}</span>
                            </div>
                            <div style="font-size:1.1rem;font-weight:900;color:#111827;margin-bottom:4px">
                                {{ number_format($tx->amount, 2) }} <x-riyal />
                            </div>
                            <div style="display:flex;gap:12px;font-size:0.8rem;color:#6b7280">
                                @if($tx->payment_method)
                                <span>💳 {{ $tx->payment_method=='cash'?'نقدي':($tx->payment_method=='bank_transfer'?'تحويل بنكي':$tx->payment_method) }}</span>
                                @endif
                                @if($tx->transaction_reference)
                                <span>🔖 {{ $tx->transaction_reference }}</span>
                                @endif
                                @if($tx->creator)
                                <span>👤 {{ $tx->creator->name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="text-align:center;padding:3rem 2rem;color:#9ca3af">
                    <svg style="width:48px;height:48px;margin:0 auto 1rem;opacity:0.3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p style="font-weight:600;margin:0">لا توجد معاملات مسجلة بعد</p>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

<!-- ⑥ Add Note Modal -->
<div class="m-overlay" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.payments.update', $payment) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg style="width:22px;height:22px;color:#f59e0b" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        إضافة / تعديل ملاحظة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3" style="margin-bottom:0">
                        <label class="form-label">الملاحظة</label>
                        <textarea name="notes" class="form-control" rows="5"
                                  placeholder="أضف ملاحظة داخلية على هذه الدفعة...">{{ $payment->notes }}</textarea>
                        <small class="text-muted">📝 هذه الملاحظة مرئية للمشرفين فقط</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning">
                        <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        حفظ الملاحظة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Quick action buttons below table --}}
<div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:1.5rem;padding:1rem 1.5rem;background:#f8fafc;border-radius:16px;border:1px solid #e2e8f0">
    <span style="font-size:0.8rem;font-weight:700;color:#6b7280;align-self:center;margin-left:auto">إجراءات سريعة:</span>
    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#timelineModal" style="font-size:0.82rem;padding:8px 16px">
        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        سجل الأحداث
    </button>
    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#addNoteModal" style="font-size:0.82rem;padding:8px 16px">
        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
        {{ $payment->notes ? 'تعديل الملاحظة' : 'إضافة ملاحظة' }}
    </button>
    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#receiptModal" style="font-size:0.82rem;padding:8px 16px">
        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        طباعة
    </button>
    <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#emailReceiptModal" style="font-size:0.82rem;padding:8px 16px">
        <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        إرسال بريد
    </button>
</div>

<!-- ⑦ Reject Receipt Modal -->
<div class="m-overlay" id="rejectReceiptModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectReceiptForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <svg style="width:22px;height:22px;color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        رفض الإيصال
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert-warning" style="margin-bottom:1.25rem;">
                        <svg style="width:20px;height:20px;color:#d97706;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div>سيتم إشعار ال متدرب برفض الإيصال وسبب الرفض حتى يتمكن من إعادة الرفع.</div>
                    </div>
                    <div class="mb-3" style="margin-bottom:0;">
                        <label class="form-label">سبب الرفض <span style="color:#ef4444;">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" required
                                  placeholder="وضّح سبب رفض الإيصال بالتفصيل..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">تراجع</button>
                    <button type="submit" class="btn btn-danger">
                        <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        تأكيد الرفض
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openModal(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeModal(trigger) {
    var overlay = trigger.classList && trigger.classList.contains('m-overlay')
        ? trigger
        : trigger.closest ? trigger.closest('.m-overlay') : null;
    if (overlay) {
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Open modals via data-bs-target buttons
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var id = (this.getAttribute('data-bs-target') || '').replace('#', '');
            if (id) openModal(id);
        });
    });

    // Close via data-bs-dismiss or backdrop click
    document.addEventListener('click', function(e) {
        if (e.target.hasAttribute && e.target.hasAttribute('data-bs-dismiss') && e.target.getAttribute('data-bs-dismiss') === 'modal') {
            closeModal(e.target);
        }
        if (e.target.classList && e.target.classList.contains('m-overlay')) {
            closeModal(e.target);
        }
    });

    // ESC key to close
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('.m-overlay.open').forEach(function(el) {
                el.classList.remove('open');
                document.body.style.overflow = '';
            });
        }
    });
});

function recordInstallmentPayment(installmentId) {
    if (confirm('هل تريد تسجيل دفع هذا القسط؟')) {
        var form = document.getElementById('recordInstallmentForm');
        form.action = '/admin/payments/installments/' + installmentId + '/record-payment';
        form.submit();
    }
}

function openRejectModal(transactionId) {
    document.getElementById('rejectReceiptForm').action = '/admin/payments/transactions/' + transactionId + '/reject-receipt';
    openModal('rejectReceiptModal');
}
</script>
@endpush
@endsection
