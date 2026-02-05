@extends('layouts.dashboard')

@section('title', 'تذكرة #' . $ticket->id)

@push('styles')
<style>
    .ticket-page { max-width: 1400px; margin: 0 auto; }

    /* Header */
    .ticket-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .ticket-header::before {
        content: '';
        position: absolute;
        top: -40%;
        left: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
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

    /* Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.25rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.9rem;
    }

    /* Cards */
    .ticket-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .dark .ticket-card { background: #1f2937; }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .dark .card-header { border-color: #374151; }

    .card-title {
        font-size: 1rem;
        font-weight: 800;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .card-title { color: #f9fafb; }

    .card-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Message Thread */
    .message-item {
        background: #fff;
        border-radius: 18px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        border-left: 4px solid #e5e7eb;
    }
    .dark .message-item {
        background: #1f2937;
        border-color: #374151;
    }

    .message-item.staff-message {
        border-left-color: #8b5cf6;
        background: linear-gradient(135deg, #faf5ff 0%, #f5f3ff 100%);
    }
    .dark .message-item.staff-message {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(139, 92, 246, 0.05) 100%);
        border-left-color: #8b5cf6;
    }

    .message-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .message-avatar {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        flex-shrink: 0;
    }

    .message-avatar.user-avatar {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #fff;
    }

    .message-avatar.staff-avatar {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: #fff;
    }

    .message-info {
        flex: 1;
    }

    .message-author {
        font-weight: 700;
        color: #111827;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    .dark .message-author { color: #f9fafb; }

    .message-date {
        font-size: 0.8rem;
        color: #6b7280;
    }
    .dark .message-date { color: #9ca3af; }

    .staff-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: #fff;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .message-content {
        color: #374151;
        font-size: 0.95rem;
        line-height: 1.7;
        white-space: pre-wrap;
    }
    .dark .message-content { color: #d1d5db; }

    /* Reply Form */
    .reply-form {
        background: #fff;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
    }
    .dark .reply-form { background: #1f2937; }

    .form-label {
        font-size: 0.875rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.5rem;
        display: block;
    }
    .dark .form-label { color: #d1d5db; }

    .form-textarea {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.2s;
        resize: vertical;
        min-height: 120px;
    }
    .dark .form-textarea {
        background: #111827;
        border-color: #374151;
        color: #f9fafb;
    }
    .form-textarea:focus {
        outline: none;
        border-color: #8b5cf6;
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
    }

    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
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

    .btn-submit {
        padding: 0.875rem 2rem;
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: #fff;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(139, 92, 246, 0.35);
    }

    /* Info Items */
    .info-item {
        margin-bottom: 1.25rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .dark .info-item { border-color: #374151; }
    .info-item:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    .dark .info-label { color: #9ca3af; }

    .info-value {
        font-size: 0.95rem;
        font-weight: 700;
        color: #111827;
    }
    .dark .info-value { color: #f9fafb; }

    /* Checkbox */
    .checkbox-label {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        padding: 0.75rem 1rem;
        background: #f9fafb;
        border-radius: 10px;
        transition: all 0.2s;
    }
    .dark .checkbox-label { background: #111827; }
    .checkbox-label:hover {
        background: #f3f4f6;
    }
    .dark .checkbox-label:hover { background: rgba(255,255,255,0.05); }

    .checkbox-input {
        width: 18px;
        height: 18px;
        border-radius: 6px;
        border: 2px solid #d1d5db;
        cursor: pointer;
    }
    .checkbox-input:checked {
        background: #8b5cf6;
        border-color: #8b5cf6;
    }

    .checkbox-text {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 600;
    }
    .dark .checkbox-text { color: #9ca3af; }

    /* Responsive */
    @media (max-width: 1024px) {
        .ticket-header {
            padding: 1.5rem 1.25rem;
        }
    }
</style>
@endpush

@section('content')
<div class="ticket-page">
    <!-- Header -->
    <div class="ticket-header">
        <div class="flex items-start gap-4 relative z-10">
            <a href="{{ route('admin.tickets.index') }}" class="back-btn">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-2xl font-extrabold mb-1">تذكرة #{{ $ticket->id }}</h1>
                        <p class="opacity-75 text-sm">{{ $ticket->subject }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="status-badge {{ $ticket->getStatusColorClass() }}">
                            {{ $ticket->getStatusLabel() }}
                        </span>
                        <span class="status-badge {{ $ticket->getPriorityColorClass() }}">
                            {{ $ticket->getPriorityLabel() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <!-- Original Message -->
            <div class="message-item">
                <div class="message-header">
                    <div class="message-avatar user-avatar">
                        {{ substr($ticket->user?->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="message-info">
                        <div class="message-author">{{ $ticket->user?->name ?? 'مستخدم غير معروف' }}</div>
                        <div class="message-date">
                            <svg style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            {{ $ticket->created_at->format('Y/m/d H:i') }} - {{ $ticket->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <span class="status-badge {{ $ticket->getCategoryColorClass() }}">
                        {{ $ticket->getCategoryLabel() }}
                    </span>
                </div>
                <div class="message-content">{{ $ticket->description }}</div>
            </div>

            <!-- Replies -->
            @foreach($ticket->replies as $reply)
                <div class="message-item {{ $reply->user && in_array($reply->user->role, ['admin', 'super_admin']) ? 'staff-message' : '' }}">
                    <div class="message-header">
                        <div class="message-avatar {{ $reply->user && in_array($reply->user->role, ['admin', 'super_admin']) ? 'staff-avatar' : 'user-avatar' }}">
                            {{ substr($reply->user?->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="message-info">
                            <div class="message-author">
                                {{ $reply->user?->name ?? 'مستخدم غير معروف' }}
                                @if($reply->user && in_array($reply->user->role, ['admin', 'super_admin']))
                                    <span class="staff-badge">
                                        <svg style="width: 12px; height: 12px;" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        فريق الدعم
                                    </span>
                                @endif
                            </div>
                            <div class="message-date">
                                <svg style="width: 14px; height: 14px; display: inline-block; vertical-align: middle;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                {{ $reply->created_at->format('Y/m/d H:i') }} - {{ $reply->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    <div class="message-content">{{ $reply->message }}</div>
                </div>
            @endforeach

            <!-- Reply Form -->
            <div class="reply-form">
                <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST">
                    @csrf
                    <h3 class="card-title" style="margin-bottom: 1.25rem;">
                        <div class="card-icon" style="background: linear-gradient(135deg, #10b981, #059669);">
                            <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                            </svg>
                        </div>
                        إضافة رد
                    </h3>

                    <div style="margin-bottom: 1.25rem;">
                        <label class="form-label">الرسالة <span style="color: #ef4444;">*</span></label>
                        <textarea name="message" class="form-textarea" required placeholder="اكتب ردك هنا..."></textarea>
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_internal_note_note" value="1" class="checkbox-input">
                            <span class="checkbox-text">
                                <svg style="width: 14px; height: 14px; display: inline-block; vertical-align: middle; margin-left: 0.25rem;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                </svg>
                                ملاحظة داخلية (لن تظهر للمستخدم)
                            </span>
                        </label>

                        <button type="submit" class="btn-submit">
                            <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            إرسال الرد
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Ticket Info -->
            <div class="ticket-card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="card-icon" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                            <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        معلومات التذكرة
                    </div>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="info-label">رقم التذكرة</div>
                        <div class="info-value">#{{ $ticket->id }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">الفئة</div>
                        <div class="info-value">{{ $ticket->getCategoryLabel() }}</div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">تاريخ الإنشاء</div>
                        <div class="info-value">{{ $ticket->created_at->format('Y/m/d H:i') }}</div>
                    </div>

                    @if($ticket->first_response_at)
                        <div class="info-item">
                            <div class="info-label">أول رد</div>
                            <div class="info-value" style="color: #10b981;">{{ $ticket->first_response_at->format('Y/m/d H:i') }}</div>
                        </div>
                    @endif

                    @if($ticket->resolved_at)
                        <div class="info-item">
                            <div class="info-label">تاريخ الحل</div>
                            <div class="info-value" style="color: #10b981;">{{ $ticket->resolved_at->format('Y/m/d H:i') }}</div>
                        </div>
                    @endif

                    <div class="info-item">
                        <div class="info-label">عدد الردود</div>
                        <div class="info-value">{{ $ticket->replies->count() }} رد</div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="ticket-card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="card-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <svg style="width: 20px; height: 20px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                        </div>
                        إجراءات
                    </div>
                </div>
                <div class="card-body">
                    <!-- Status -->
                    <div style="margin-bottom: 1.25rem;">
                        <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label class="form-label">تغيير الحالة</label>
                            <select name="status" onchange="this.form.submit()" class="form-select">
                                <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>مفتوحة</option>
                                <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                                <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>تم الحل</option>
                                <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>مغلقة</option>
                            </select>
                        </form>
                    </div>

                    <!-- Priority -->
                    <div style="margin-bottom: 1.25rem;">
                        <form action="{{ route('admin.tickets.priority', $ticket) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label class="form-label">تغيير الأولوية</label>
                            <select name="priority" onchange="this.form.submit()" class="form-select">
                                <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>منخفضة</option>
                                <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>متوسطة</option>
                                <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>عالية</option>
                                <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>عاجلة</option>
                            </select>
                        </form>
                    </div>

                    <!-- Assign -->
                    <div style="margin-bottom: 0;">
                        <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <label class="form-label">تعيين إلى</label>
                            <select name="assigned_to" onchange="this.form.submit()" class="form-select">
                                <option value="">-- غير معين --</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}" {{ $ticket->assigned_to == $member->id ? 'selected' : '' }}>
                                        {{ $member->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
