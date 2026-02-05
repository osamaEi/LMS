@extends('layouts.dashboard')

@section('title', 'الملف الشخصي للطالب')

@push('styles')
<style>
    /* Modern Profile Styles */
    .profile-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004466 100%);
        border-radius: 24px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .profile-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 60%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .profile-header::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 40%;
        height: 150%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.05) 0%, transparent 70%);
        pointer-events: none;
    }

    /* Avatar Container */
    .avatar-container {
        position: relative;
        width: 140px;
        height: 140px;
    }

    .avatar-ring {
        position: absolute;
        inset: -4px;
        border-radius: 24px;
        background: linear-gradient(135deg, #fff, rgba(255,255,255,0.3));
        animation: pulse-ring 2s ease-in-out infinite;
    }

    @keyframes pulse-ring {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .avatar-image {
        position: relative;
        width: 100%;
        height: 100%;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    }

    .avatar-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Status Badge */
    .status-badge {
        position: absolute;
        bottom: -8px;
        right: -8px;
        padding: 0.5rem 1rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        border: 3px solid white;
    }

    .status-badge.active {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .status-badge.pending {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .status-badge.suspended {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    /* Info Chips */
    .info-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        border-radius: 14px;
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        color: white;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .info-chip:hover {
        background: rgba(255,255,255,0.25);
        transform: translateY(-2px);
    }

    .info-chip svg {
        width: 18px;
        height: 18px;
        opacity: 0.9;
    }

    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-top: -3rem;
        position: relative;
        z-index: 10;
        padding: 0 1rem;
    }

    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 113, 170, 0.1);
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(0, 113, 170, 0.2);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--card-color), var(--card-color-light));
    }

    .stat-card .icon-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, var(--card-color), var(--card-color-light));
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        margin-bottom: 1rem;
    }

    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--card-color), var(--card-color-dark));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        line-height: 1.2;
    }

    .stat-card .stat-label {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 500;
        margin-top: 0.25rem;
    }

    .stat-card .decorative-circle {
        position: absolute;
        bottom: -15px;
        left: -15px;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--card-color), transparent);
        opacity: 0.1;
    }

    .stat-card.blue { --card-color: #0071AA; --card-color-light: #0099dd; --card-color-dark: #005580; }
    .stat-card.green { --card-color: #10b981; --card-color-light: #34d399; --card-color-dark: #059669; }
    .stat-card.amber { --card-color: #f59e0b; --card-color-light: #fbbf24; --card-color-dark: #d97706; }
    .stat-card.purple { --card-color: #8b5cf6; --card-color-light: #a78bfa; --card-color-dark: #7c3aed; }

    /* Info Cards */
    .info-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 113, 170, 0.1);
        transition: all 0.3s ease;
    }

    .info-card:hover {
        box-shadow: 0 10px 30px rgba(0, 113, 170, 0.12);
    }

    .info-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .info-card-header .icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0071AA, #005a88);
    }

    .info-card-header h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
    }

    .info-card-body {
        padding: 1.25rem;
    }

    /* Personal Info Item */
    .info-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 16px;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.08), rgba(0, 113, 170, 0.04));
        transform: translateX(-4px);
    }

    .info-item .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .info-item .content {
        flex: 1;
    }

    .info-item .label {
        font-size: 0.75rem;
        color: #94a3b8;
        margin-bottom: 0.25rem;
    }

    .info-item .value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1e293b;
    }

    /* Subject Card */
    .subject-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 16px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .subject-item:hover {
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.05), rgba(0, 113, 170, 0.02));
        border-color: rgba(0, 113, 170, 0.1);
        transform: translateX(-4px);
    }

    .subject-item .icon-box {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0071AA, #005a88);
        flex-shrink: 0;
    }

    .subject-item .info {
        flex: 1;
        min-width: 0;
    }

    .subject-item .name {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .subject-item .code {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .subject-item .status-pill {
        padding: 0.375rem 0.875rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .subject-item .status-pill.active {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(59, 130, 246, 0.1));
        color: #2563eb;
    }

    .subject-item .status-pill.completed {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.1));
        color: #059669;
    }

    /* Progress Bar */
    .progress-wrapper {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .progress-bar {
        flex: 1;
        height: 8px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #0071AA, #0099dd);
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    .progress-text {
        font-size: 0.75rem;
        font-weight: 600;
        color: #0071AA;
        min-width: 40px;
        text-align: left;
    }

    /* Verification Item */
    .verification-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-radius: 14px;
        transition: all 0.3s ease;
    }

    .verification-item:hover {
        transform: translateX(-4px);
    }

    .verification-item.verified {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .verification-item.unverified {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .verification-item .icon-box {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .verification-item.verified .icon-box {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .verification-item.unverified .icon-box {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .verification-item .status-text {
        font-size: 0.8rem;
        font-weight: 600;
    }

    .verification-item.verified .status-text {
        color: #059669;
    }

    .verification-item.unverified .status-text {
        color: #d97706;
    }

    /* Action Button */
    .action-btn {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        padding: 1rem;
        border-radius: 14px;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        text-decoration: none;
    }

    .action-btn:hover {
        transform: translateX(-4px);
    }

    .action-btn .icon-box {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .action-btn.primary {
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.1), rgba(0, 113, 170, 0.05));
        color: #0071AA;
    }

    .action-btn.primary .icon-box {
        background: linear-gradient(135deg, #0071AA, #005a88);
    }

    .action-btn.primary:hover {
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.15), rgba(0, 113, 170, 0.1));
    }

    .action-btn.blue {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(59, 130, 246, 0.05));
        color: #2563eb;
    }

    .action-btn.blue .icon-box {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
    }

    .action-btn.blue:hover {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(59, 130, 246, 0.1));
    }

    .action-btn.purple {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(139, 92, 246, 0.05));
        color: #7c3aed;
    }

    .action-btn.purple .icon-box {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    }

    .action-btn.purple:hover {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(139, 92, 246, 0.1));
    }

    /* Account Info Row */
    .account-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.875rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .account-row:last-child {
        border-bottom: none;
    }

    .account-row .label {
        font-size: 0.875rem;
        color: #64748b;
    }

    .account-row .value {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1e293b;
    }

    .completion-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .completion-badge.complete {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.1));
        color: #059669;
    }

    .completion-badge.incomplete {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.1));
        color: #d97706;
    }

    /* Back Button */
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #64748b;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        text-decoration: none;
        border: 1px solid #e2e8f0;
    }

    .back-btn:hover {
        color: #0071AA;
        border-color: #0071AA;
        transform: translateX(4px);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem;
    }

    .empty-state .icon-wrapper {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .empty-state .icon-wrapper svg {
        width: 40px;
        height: 40px;
        color: #94a3b8;
    }

    .empty-state p {
        color: #94a3b8;
        font-size: 0.95rem;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease forwards;
    }

    .delay-100 { animation-delay: 0.1s; opacity: 0; }
    .delay-200 { animation-delay: 0.2s; opacity: 0; }
    .delay-300 { animation-delay: 0.3s; opacity: 0; }
    .delay-400 { animation-delay: 0.4s; opacity: 0; }

    /* Dark Mode */
    .dark .stat-card,
    .dark .info-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.1);
    }

    .dark .info-card-header {
        border-color: #334155;
    }

    .dark .info-card-header h3 {
        color: #f1f5f9;
    }

    .dark .info-item {
        background: linear-gradient(135deg, #334155, #1e293b);
    }

    .dark .info-item .value {
        color: #f1f5f9;
    }

    .dark .subject-item .name {
        color: #f1f5f9;
    }

    .dark .account-row {
        border-color: #334155;
    }

    .dark .account-row .value {
        color: #f1f5f9;
    }

    /* Tabs Styles */
    .tabs-container {
        margin-top: 2rem;
    }

    .tabs-header {
        display: flex;
        gap: 0.5rem;
        padding: 0.5rem;
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        margin-bottom: 1.5rem;
        overflow-x: auto;
    }

    .tab-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border-radius: 12px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .tab-btn:hover {
        background: #f1f5f9;
        color: #0071AA;
    }

    .tab-btn.active {
        background: linear-gradient(135deg, #0071AA, #005a88);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 113, 170, 0.3);
    }

    .tab-btn .icon {
        width: 20px;
        height: 20px;
    }

    .tab-btn .badge {
        padding: 0.125rem 0.5rem;
        border-radius: 10px;
        font-size: 0.7rem;
        font-weight: 700;
        background: rgba(255,255,255,0.2);
    }

    .tab-btn:not(.active) .badge {
        background: #e2e8f0;
        color: #64748b;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Grade Table */
    .grade-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .grade-table th {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        padding: 1rem;
        text-align: right;
        font-size: 0.8rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .grade-table th:first-child {
        border-radius: 0 12px 0 0;
    }

    .grade-table th:last-child {
        border-radius: 12px 0 0 0;
    }

    .grade-table td {
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
        color: #1e293b;
    }

    .grade-table tr:hover td {
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.03), rgba(0, 113, 170, 0.01));
    }

    .grade-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 48px;
        padding: 0.375rem 0.75rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .grade-badge.excellent {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.1));
        color: #059669;
    }

    .grade-badge.good {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(59, 130, 246, 0.1));
        color: #2563eb;
    }

    .grade-badge.average {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.1));
        color: #d97706;
    }

    .grade-badge.poor {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.1));
        color: #dc2626;
    }

    /* Attendance Item */
    .attendance-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 14px;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }

    .attendance-item:hover {
        transform: translateX(-4px);
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.05), rgba(0, 113, 170, 0.02));
    }

    .attendance-item .date-box {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .attendance-item .date-box.present {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .attendance-item .date-box.absent {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .attendance-item .date-box.late {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .attendance-item .date-box .day {
        font-size: 1.25rem;
        font-weight: 800;
        line-height: 1;
    }

    .attendance-item .date-box .month {
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .attendance-item .info {
        flex: 1;
    }

    .attendance-item .subject-name {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .attendance-item .session-info {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .attendance-item .status-badge {
        padding: 0.375rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .attendance-item .status-badge.present {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.1));
        color: #059669;
    }

    .attendance-item .status-badge.absent {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.1));
        color: #dc2626;
    }

    .attendance-item .status-badge.late {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.1));
        color: #d97706;
    }

    /* Payment Item */
    .payment-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem;
        border-radius: 16px;
        background: white;
        border: 1px solid #e2e8f0;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .payment-item:hover {
        transform: translateX(-4px);
        box-shadow: 0 10px 30px rgba(0, 113, 170, 0.1);
        border-color: rgba(0, 113, 170, 0.2);
    }

    .payment-item .icon-box {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .payment-item .icon-box.paid {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .payment-item .icon-box.pending {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .payment-item .icon-box.overdue {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .payment-item .info {
        flex: 1;
    }

    .payment-item .title {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
    }

    .payment-item .details {
        font-size: 0.8rem;
        color: #94a3b8;
    }

    .payment-item .amount {
        text-align: left;
    }

    .payment-item .amount .value {
        font-size: 1.25rem;
        font-weight: 800;
        color: #1e293b;
    }

    .payment-item .amount .currency {
        font-size: 0.8rem;
        color: #64748b;
    }

    .payment-item .status-badge {
        padding: 0.375rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .payment-item .status-badge.paid {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.1));
        color: #059669;
    }

    .payment-item .status-badge.pending {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.1));
        color: #d97706;
    }

    .payment-item .status-badge.overdue {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.1));
        color: #dc2626;
    }

    /* Ticket Item */
    .ticket-item {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.25rem;
        border-radius: 16px;
        background: white;
        border: 1px solid #e2e8f0;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .ticket-item:hover {
        transform: translateX(-4px);
        box-shadow: 0 10px 30px rgba(0, 113, 170, 0.1);
        border-color: rgba(0, 113, 170, 0.2);
    }

    .ticket-item .ticket-id {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #0071AA, #005a88);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    .ticket-item .info {
        flex: 1;
        min-width: 0;
    }

    .ticket-item .subject {
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.25rem;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .ticket-item .preview {
        font-size: 0.85rem;
        color: #64748b;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .ticket-item .meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .ticket-item .priority-badge {
        padding: 0.25rem 0.625rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .ticket-item .priority-badge.urgent,
    .ticket-item .priority-badge.high {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.1));
        color: #dc2626;
    }

    .ticket-item .priority-badge.medium {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.1));
        color: #d97706;
    }

    .ticket-item .priority-badge.low {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.1));
        color: #059669;
    }

    .ticket-item .status-badge {
        padding: 0.375rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .ticket-item .status-badge.open {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.15), rgba(59, 130, 246, 0.1));
        color: #2563eb;
    }

    .ticket-item .status-badge.in_progress {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.1));
        color: #d97706;
    }

    .ticket-item .status-badge.closed {
        background: linear-gradient(135deg, rgba(107, 114, 128, 0.15), rgba(107, 114, 128, 0.1));
        color: #4b5563;
    }

    .ticket-item .status-badge.resolved {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.1));
        color: #059669;
    }

    /* Attendance Stats */
    .attendance-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .attendance-stat {
        padding: 1.25rem;
        border-radius: 16px;
        text-align: center;
    }

    .attendance-stat.present {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.05));
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .attendance-stat.absent {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
        border: 1px solid rgba(239, 68, 68, 0.2);
    }

    .attendance-stat.late {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.05));
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .attendance-stat .value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .attendance-stat.present .value { color: #059669; }
    .attendance-stat.absent .value { color: #dc2626; }
    .attendance-stat.late .value { color: #d97706; }

    .attendance-stat .label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Payment Summary */
    .payment-summary {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .payment-summary-card {
        padding: 1.5rem;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
    }

    .payment-summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 4px;
        background: linear-gradient(90deg, var(--card-color), var(--card-color-light));
    }

    .payment-summary-card.total {
        --card-color: #0071AA;
        --card-color-light: #0099dd;
        background: linear-gradient(135deg, rgba(0, 113, 170, 0.08), rgba(0, 113, 170, 0.03));
        border: 1px solid rgba(0, 113, 170, 0.15);
    }

    .payment-summary-card.paid {
        --card-color: #10b981;
        --card-color-light: #34d399;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(16, 185, 129, 0.03));
        border: 1px solid rgba(16, 185, 129, 0.15);
    }

    .payment-summary-card .label {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .payment-summary-card .amount {
        font-size: 1.75rem;
        font-weight: 800;
    }

    .payment-summary-card.total .amount { color: #0071AA; }
    .payment-summary-card.paid .amount { color: #059669; }

    .payment-summary-card .currency {
        font-size: 0.9rem;
        font-weight: 500;
        color: #64748b;
        margin-right: 0.25rem;
    }

    /* Dark Mode for Tabs */
    .dark .tabs-header {
        background: #1e293b;
    }

    .dark .tab-btn {
        color: #94a3b8;
    }

    .dark .tab-btn:hover {
        background: #334155;
        color: #0099dd;
    }

    .dark .grade-table th {
        background: linear-gradient(135deg, #334155, #1e293b);
        color: #94a3b8;
    }

    .dark .grade-table td {
        color: #e2e8f0;
        border-color: #334155;
    }

    .dark .attendance-item {
        background: linear-gradient(135deg, #334155, #1e293b);
    }

    .dark .attendance-item .subject-name {
        color: #f1f5f9;
    }

    .dark .payment-item,
    .dark .ticket-item {
        background: #1e293b;
        border-color: #334155;
    }

    .dark .payment-item .title,
    .dark .ticket-item .subject {
        color: #f1f5f9;
    }

    .dark .payment-item .amount .value {
        color: #f1f5f9;
    }
</style>
@endpush

@section('content')
@php
    $completedEnrollments = $student->enrollments->where('status', 'completed')->whereNotNull('final_grade');
    $averageGrade = $completedEnrollments->count() > 0 ? $completedEnrollments->avg('final_grade') : 0;

    // Calculate attendance stats
    $totalAttendances = $student->attendances->count();
    $presentCount = $student->attendances->where('attended', true)->count();
    $absentCount = $student->attendances->where('attended', false)->count();
    $attendancePercentage = $totalAttendances > 0 ? round(($presentCount / $totalAttendances) * 100) : 0;

    // Get open tickets count
    $openTicketsCount = $student->tickets->whereIn('status', ['open', 'in_progress'])->count();
@endphp

<!-- Back Button -->
<div class="mb-6">
    <a href="{{ route('admin.students.index') }}" class="back-btn">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        العودة لقائمة الطلاب
    </a>
</div>

<!-- Profile Header -->
<div class="profile-header animate-fadeInUp">
    <div class="relative z-10">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
            <!-- Avatar -->
            <div class="avatar-container">
                <div class="avatar-ring"></div>
                <div class="avatar-image">
                    @if($student->profile_photo)
                        <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->name }}">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&size=200&background=0071AA&color=fff&bold=true" alt="{{ $student->name }}">
                    @endif
                </div>
                <div class="status-badge {{ $student->status }}">
                    {{ $student->getStatusDisplayName() }}
                </div>
            </div>

            <!-- Info -->
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white mb-2">{{ $student->name }}</h1>
                <p class="text-white/80 text-lg mb-4">{{ $student->email }}</p>

                <!-- Info Chips -->
                <div class="flex flex-wrap gap-3">
                    @if($student->national_id)
                    <div class="info-chip">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                        </svg>
                        <span>{{ $student->national_id }}</span>
                    </div>
                    @endif
                    @if($student->phone)
                    <div class="info-chip">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <span>{{ $student->phone }}</span>
                    </div>
                    @endif
                    @if($student->program)
                    <div class="info-chip" style="background: rgba(16, 185, 129, 0.3);">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>{{ $student->program->name }}</span>
                    </div>
                    @endif
                    <div class="info-chip">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>انضم {{ $student->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Edit Button -->
            <a href="{{ route('admin.students.edit', $student) }}"
               class="hidden md:flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/20 backdrop-blur-sm text-white font-medium hover:bg-white/30 transition-all hover:scale-105">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                تعديل
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="stats-grid mb-8">
    <div class="stat-card blue animate-fadeInUp delay-100">
        <div class="icon-wrapper">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
        </div>
        <div class="stat-value">{{ $student->enrollments->count() }}</div>
        <div class="stat-label">إجمالي المقررات</div>
        <div class="decorative-circle"></div>
    </div>

    <div class="stat-card green animate-fadeInUp delay-200">
        <div class="icon-wrapper">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div class="stat-value">{{ $student->enrollments->where('status', 'completed')->count() }}</div>
        <div class="stat-label">مقررات مكتملة</div>
        <div class="decorative-circle"></div>
    </div>

    <div class="stat-card amber animate-fadeInUp delay-300">
        <div class="icon-wrapper">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <div class="stat-value">{{ $student->enrollments->where('status', 'active')->count() }}</div>
        <div class="stat-label">مقررات نشطة</div>
        <div class="decorative-circle"></div>
    </div>

    <div class="stat-card purple animate-fadeInUp delay-400">
        <div class="icon-wrapper">
            <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <div class="stat-value">{{ $averageGrade > 0 ? number_format($averageGrade, 1) : '-' }}</div>
        <div class="stat-label">المعدل التراكمي</div>
        <div class="decorative-circle"></div>
    </div>
</div>

<!-- Tabs Section -->
<div class="tabs-container">
    <!-- Tabs Header -->
    <div class="tabs-header">
        <button class="tab-btn active" onclick="switchTab('personal')">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            المعلومات الشخصية
        </button>
        <button class="tab-btn" onclick="switchTab('grades')">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            الدرجات
            <span class="badge">{{ $student->enrollments->count() }}</span>
        </button>
        <button class="tab-btn" onclick="switchTab('attendance')">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            الحضور والغياب
            @if($totalAttendances > 0)
            <span class="badge">{{ $attendancePercentage }}%</span>
            @endif
        </button>
        <button class="tab-btn" onclick="switchTab('payments')">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            المدفوعات
        </button>
        <button class="tab-btn" onclick="switchTab('tickets')">
            <svg class="icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
            </svg>
            تذاكر الدعم
            @if($openTicketsCount > 0)
            <span class="badge">{{ $openTicketsCount }}</span>
            @endif
        </button>
    </div>

    <!-- Personal Info Tab -->
    <div id="tab-personal" class="tab-content active">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Info Card -->
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="icon">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3>المعلومات الشخصية</h3>
                    </div>
                    <div class="info-card-body">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="info-item">
                                <div class="icon-box" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div class="content">
                                    <div class="label">الاسم الكامل</div>
                                    <div class="value">{{ $student->name }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="icon-box" style="background: linear-gradient(135deg, #10b981, #059669);">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="content">
                                    <div class="label">البريد الإلكتروني</div>
                                    <div class="value">{{ $student->email }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="icon-box" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                    </svg>
                                </div>
                                <div class="content">
                                    <div class="label">رقم الهوية</div>
                                    <div class="value">{{ $student->national_id ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="icon-box" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div class="content">
                                    <div class="label">رقم الهاتف</div>
                                    <div class="value">{{ $student->phone ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="icon-box" style="background: linear-gradient(135deg, #ec4899, #db2777);">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="content">
                                    <div class="label">تاريخ الميلاد</div>
                                    <div class="value">{{ $student->date_of_birth ? $student->date_of_birth->format('Y/m/d') : 'غير محدد' }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="icon-box" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <div class="content">
                                    <div class="label">الجنس</div>
                                    <div class="value">{{ $student->gender === 'male' ? 'ذكر' : ($student->gender === 'female' ? 'أنثى' : 'غير محدد') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enrolled Subjects Card -->
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="icon">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <h3>المقررات المسجلة</h3>
                        <span style="margin-right: auto; background: linear-gradient(135deg, rgba(0, 113, 170, 0.15), rgba(0, 113, 170, 0.1)); color: #0071AA; padding: 0.375rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 600;">
                            {{ $student->enrollments->count() }} مقرر
                        </span>
                    </div>
                    @if($student->enrollments->count() > 0)
                    <div class="info-card-body space-y-3">
                        @foreach($student->enrollments as $enrollment)
                        <div class="subject-item">
                            <div class="icon-box">
                                <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div class="info">
                                <div class="name">{{ $enrollment->subject->name ?? 'غير محدد' }}</div>
                                <div class="code">{{ $enrollment->subject->code ?? '' }}</div>
                            </div>
                            <div class="flex items-center gap-4">
                                @if($enrollment->progress)
                                <div class="progress-wrapper hidden sm:flex" style="width: 100px;">
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ $enrollment->progress }}%"></div>
                                    </div>
                                    <span class="progress-text">{{ $enrollment->progress }}%</span>
                                </div>
                                @endif
                                <span class="status-pill {{ $enrollment->status }}">
                                    @if($enrollment->status === 'active') نشط
                                    @elseif($enrollment->status === 'completed') مكتمل
                                    @else {{ $enrollment->status }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="empty-state">
                        <div class="icon-wrapper">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <p>لا توجد مقررات مسجلة حالياً</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">
                <!-- Verification Status -->
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <h3>حالة التحقق</h3>
                    </div>
                    <div class="info-card-body space-y-3">
                        <div class="verification-item {{ $student->email_verified_at ? 'verified' : 'unverified' }}">
                            <div class="flex items-center gap-3">
                                <div class="icon-box">
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">البريد الإلكتروني</span>
                            </div>
                            <span class="status-text">{{ $student->email_verified_at ? 'موثق' : 'غير موثق' }}</span>
                        </div>
                        <div class="verification-item {{ $student->phone_verified_at ? 'verified' : 'unverified' }}">
                            <div class="flex items-center gap-3">
                                <div class="icon-box">
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">رقم الهاتف</span>
                            </div>
                            <span class="status-text">{{ $student->phone_verified_at ? 'موثق' : 'غير موثق' }}</span>
                        </div>
                        <div class="verification-item {{ $student->nafath_verified_at ? 'verified' : 'unverified' }}">
                            <div class="flex items-center gap-3">
                                <div class="icon-box">
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">نفاذ</span>
                            </div>
                            <span class="status-text">{{ $student->nafath_verified_at ? 'موثق' : 'غير موثق' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Program Management -->
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3>البرنامج الدراسي</h3>
                    </div>
                    <div class="info-card-body">
                        @if($student->program)
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-4 mb-4">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ $student->program->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $student->program->code }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500">المدة: {{ $student->program->duration_months }} شهر</span>
                                    <span class="text-green-600 font-medium">{{ number_format($student->program->price) }} ر.س</span>
                                </div>
                            </div>
                            <form action="{{ route('admin.students.remove-program', $student) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من إزالة البرنامج من هذا الطالب؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/30 transition-colors font-medium text-sm">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    إزالة البرنامج
                                </button>
                            </form>
                        @else
                            <div class="text-center py-4">
                                <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">لم يتم تعيين برنامج لهذا الطالب</p>
                            </div>
                            <form action="{{ route('admin.students.assign-program', $student) }}" method="POST">
                                @csrf
                                <div class="space-y-3">
                                    <select name="program_id" required class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2.5 text-gray-900 dark:text-white focus:border-brand-500 focus:ring-2 focus:ring-brand-500">
                                        <option value="">اختر البرنامج...</option>
                                        @foreach($programs as $program)
                                            <option value="{{ $program->id }}">{{ $program->name }} ({{ $program->code }})</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-brand-500 text-white hover:bg-brand-600 transition-colors font-medium text-sm">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        تعيين البرنامج
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <h3>إجراءات سريعة</h3>
                    </div>
                    <div class="info-card-body space-y-3">
                        <a href="{{ route('admin.students.edit', $student) }}" class="action-btn primary">
                            <div class="icon-box">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            تعديل البيانات
                        </a>
                        <button class="action-btn blue">
                            <div class="icon-box">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            إرسال رسالة
                        </button>
                        <button class="action-btn purple">
                            <div class="icon-box">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            السجل الأكاديمي
                        </button>
                    </div>
                </div>

                <!-- Account Info -->
                <div class="info-card">
                    <div class="info-card-header">
                        <div class="icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3>معلومات الحساب</h3>
                    </div>
                    <div class="info-card-body">
                        <div class="account-row">
                            <span class="label">تاريخ الإنشاء</span>
                            <span class="value">{{ $student->created_at->format('Y/m/d') }}</span>
                        </div>
                        <div class="account-row">
                            <span class="label">آخر تحديث</span>
                            <span class="value">{{ $student->updated_at->diffForHumans() }}</span>
                        </div>
                        <div class="account-row">
                            <span class="label">اكتمال الملف</span>
                            @if($student->profile_completed_at)
                                <span class="completion-badge complete">مكتمل</span>
                            @else
                                <span class="completion-badge incomplete">غير مكتمل</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades Tab -->
    <div id="tab-grades" class="tab-content">
        <div class="info-card">
            <div class="info-card-header">
                <div class="icon">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3>سجل الدرجات</h3>
            </div>
            @if($student->enrollments->count() > 0)
            <div class="overflow-x-auto">
                <table class="grade-table">
                    <thead>
                        <tr>
                            <th>المادة</th>
                            <th>الكود</th>
                            <th>الفصل</th>
                            <th>الدرجة النهائية</th>
                            <th>التقدير</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($student->enrollments as $enrollment)
                        @php
                            $finalGrade = $enrollment->final_grade;
                            $gradeLetter = $enrollment->grade_letter ?? ($finalGrade ? $enrollment->calculateGradeLetter() : null);
                            $gradeClass = $finalGrade >= 85 ? 'excellent' : ($finalGrade >= 70 ? 'good' : ($finalGrade >= 60 ? 'average' : 'poor'));
                        @endphp
                        <tr>
                            <td class="font-medium">{{ $enrollment->subject->name ?? 'غير محدد' }}</td>
                            <td>{{ $enrollment->subject->code ?? '-' }}</td>
                            <td>{{ $enrollment->subject->term->name ?? '-' }}</td>
                            <td>
                                @if($finalGrade)
                                <span class="grade-badge {{ $gradeClass }}">{{ number_format($finalGrade, 1) }}</span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td>{{ $gradeLetter ?? '-' }}</td>
                            <td>
                                <span class="status-pill {{ $enrollment->status }}">
                                    @if($enrollment->status === 'active') نشط
                                    @elseif($enrollment->status === 'completed') مكتمل
                                    @elseif($enrollment->status === 'withdrawn') منسحب
                                    @elseif($enrollment->status === 'failed') راسب
                                    @else {{ $enrollment->status }}
                                    @endif
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="empty-state">
                <div class="icon-wrapper">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <p>لا توجد درجات مسجلة</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Attendance Tab -->
    <div id="tab-attendance" class="tab-content">
        <div class="info-card">
            <div class="info-card-header">
                <div class="icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3>سجل الحضور والغياب</h3>
            </div>
            <div class="info-card-body">
                <!-- Attendance Stats -->
                <div class="attendance-stats">
                    <div class="attendance-stat present">
                        <div class="value">{{ $attendancePercentage }}%</div>
                        <div class="label">نسبة الحضور</div>
                    </div>
                    <div class="attendance-stat absent">
                        <div class="value">{{ $absentCount }}</div>
                        <div class="label">أيام الغياب</div>
                    </div>
                    <div class="attendance-stat late">
                        <div class="value">{{ $presentCount }}</div>
                        <div class="label">أيام الحضور</div>
                    </div>
                </div>

                <!-- Attendance Records -->
                @if($student->attendances->count() > 0)
                    @foreach($student->attendances->sortByDesc('created_at')->take(10) as $attendance)
                    <div class="attendance-item">
                        <div class="date-box {{ $attendance->attended ? 'present' : 'absent' }}">
                            <span class="day">{{ $attendance->created_at->format('d') }}</span>
                            <span class="month">{{ $attendance->created_at->translatedFormat('M') }}</span>
                        </div>
                        <div class="info">
                            <div class="subject-name">{{ $attendance->session->subject->name ?? 'جلسة' }}</div>
                            <div class="session-info">{{ $attendance->session->title ?? 'جلسة دراسية' }} - {{ $attendance->created_at->format('H:i') }}</div>
                        </div>
                        <span class="status-badge {{ $attendance->attended ? 'present' : 'absent' }}">
                            {{ $attendance->attended ? 'حاضر' : 'غائب' }}
                        </span>
                    </div>
                    @endforeach
                @else
                <div class="empty-state">
                    <div class="icon-wrapper">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <p>لا توجد سجلات حضور</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Payments Tab -->
    <div id="tab-payments" class="tab-content">
        <div class="info-card">
            <div class="info-card-header">
                <div class="icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h3>سجل المدفوعات</h3>
            </div>
            <div class="info-card-body">
                <!-- Payment Summary -->
                <div class="payment-summary" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="payment-summary-card" style="background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 16px; padding: 1.25rem; text-align: center;">
                        <div style="font-size: 0.75rem; font-weight: 600; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">إجمالي الرسوم</div>
                        <div style="font-size: 1.75rem; font-weight: 800; color: #1e3a8a;">{{ number_format($totalPayments, 2) }} <span style="font-size: 0.9rem;">ر.س</span></div>
                    </div>
                    <div class="payment-summary-card" style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 16px; padding: 1.25rem; text-align: center;">
                        <div style="font-size: 0.75rem; font-weight: 600; color: #065f46; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">المدفوع</div>
                        <div style="font-size: 1.75rem; font-weight: 800; color: #064e3b;">{{ number_format($totalPaid, 2) }} <span style="font-size: 0.9rem;">ر.س</span></div>
                    </div>
                    <div class="payment-summary-card" style="background: linear-gradient(135deg, #fee2e2, #fecaca); border-radius: 16px; padding: 1.25rem; text-align: center;">
                        <div style="font-size: 0.75rem; font-weight: 600; color: #991b1b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem;">المتبقي</div>
                        <div style="font-size: 1.75rem; font-weight: 800; color: #7f1d1d;">{{ number_format($totalRemaining, 2) }} <span style="font-size: 0.9rem;">ر.س</span></div>
                    </div>
                </div>

                @if($student->payments->count() > 0)
                    <!-- Payments Table -->
                    <div style="overflow-x: auto; border-radius: 12px; border: 1px solid #e5e7eb;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead style="background: #f9fafb;">
                                <tr>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb;">#</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb;">البرنامج</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb;">نوع الدفع</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb;">المبلغ الإجمالي</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb;">المدفوع</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb;">المتبقي</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb;">الحالة</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 700; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 2px solid #e5e7eb;">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->payments as $payment)
                                    <tr style="transition: all 0.15s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #111827;">{{ $payment->id }}</td>
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb; font-weight: 600; color: #374151;">{{ $payment->program->name_ar }}</td>
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                                            @if($payment->payment_type === 'full')
                                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; border-radius: 10px; font-size: 0.75rem; font-weight: 700; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">دفعة كاملة</span>
                                            @else
                                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; border-radius: 10px; font-size: 0.75rem; font-weight: 700; background: rgba(139, 92, 246, 0.15); color: #8b5cf6;">تقسيط</span>
                                            @endif
                                        </td>
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #111827;">{{ number_format($payment->total_amount, 2) }} ر.س</td>
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #10b981;">{{ number_format($payment->paid_amount, 2) }} ر.س</td>
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb; font-weight: 700; color: #ef4444;">{{ number_format($payment->remaining_amount, 2) }} ر.س</td>
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                                            @if($payment->status === 'completed')
                                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; border-radius: 10px; font-size: 0.75rem; font-weight: 700; background: rgba(16, 185, 129, 0.15); color: #10b981;">مكتملة</span>
                                            @elseif($payment->status === 'partial')
                                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; border-radius: 10px; font-size: 0.75rem; font-weight: 700; background: rgba(59, 130, 246, 0.15); color: #3b82f6;">جزئية</span>
                                            @elseif($payment->status === 'pending')
                                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; border-radius: 10px; font-size: 0.75rem; font-weight: 700; background: rgba(107, 114, 128, 0.15); color: #6b7280;">قيد الانتظار</span>
                                            @elseif($payment->status === 'cancelled')
                                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.4rem 0.85rem; border-radius: 10px; font-size: 0.75rem; font-weight: 700; background: rgba(239, 68, 68, 0.15); color: #ef4444;">ملغاة</span>
                                            @endif
                                        </td>
                                        <td style="padding: 1rem; border-bottom: 1px solid #e5e7eb;">
                                            <a href="{{ route('admin.payments.show', $payment) }}" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.5rem 1rem; background: linear-gradient(135deg, #0071AA, #005a88); color: #fff; border-radius: 10px; font-size: 0.8rem; font-weight: 700; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(0, 113, 170, 0.3)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'">
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
                    </div>
                @else
                    <div class="empty-state" style="text-align: center; padding: 3rem 1.5rem;">
                        <div style="font-size: 3.5rem; margin-bottom: 1rem;">💰</div>
                        <p style="color: #6b7280; font-size: 1rem; font-weight: 600;">لا توجد دفعات مسجلة لهذا الطالب</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tickets Tab -->
    <div id="tab-tickets" class="tab-content">
        <div class="info-card">
            <div class="info-card-header">
                <div class="icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                </div>
                <h3>تذاكر الدعم الفني</h3>
                @if($student->tickets->count() > 0)
                <span style="margin-right: auto; background: linear-gradient(135deg, rgba(139, 92, 246, 0.15), rgba(139, 92, 246, 0.1)); color: #7c3aed; padding: 0.375rem 1rem; border-radius: 20px; font-size: 0.875rem; font-weight: 600;">
                    {{ $student->tickets->count() }} تذكرة
                </span>
                @endif
            </div>
            <div class="info-card-body">
                @if($student->tickets->count() > 0)
                    @foreach($student->tickets->sortByDesc('created_at') as $ticket)
                    <div class="ticket-item">
                        <div class="ticket-id">{{ substr($ticket->ticket_number, -4) }}</div>
                        <div class="info">
                            <div class="subject">{{ $ticket->subject }}</div>
                            <div class="preview">{{ Str::limit($ticket->description, 100) }}</div>
                            <div class="meta">
                                <span class="priority-badge {{ $ticket->priority }}">
                                    {{ $ticket->getPriorityLabel() }}
                                </span>
                                <span>{{ $ticket->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <span class="status-badge {{ $ticket->status }}">
                            {{ $ticket->getStatusLabel() }}
                        </span>
                    </div>
                    @endforeach
                @else
                <div class="empty-state">
                    <div class="icon-wrapper">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                    </div>
                    <p>لا توجد تذاكر دعم</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });

    // Remove active from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });

    // Show selected tab content
    document.getElementById('tab-' + tabName).classList.add('active');

    // Add active to clicked button
    event.currentTarget.classList.add('active');
}
</script>
@endpush
@endsection
