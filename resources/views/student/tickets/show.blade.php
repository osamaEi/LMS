@extends('layouts.dashboard')

@section('title', 'تذكرة #' . $ticket->ticket_number)

@push('styles')
<style>
    .ticket-page { max-width: 900px; margin: 0 auto; }

    /* Header */
    .ticket-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
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
    .ticket-header::after {
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
    }
    .back-btn:hover {
        background: rgba(255,255,255,0.25);
    }

    /* Status Badges */
    .status-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1rem;
    }
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.4rem 0.85rem;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    .badge-status-open { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .badge-status-in_progress { background: rgba(59, 130, 246, 0.15); color: #3b82f6; }
    .badge-status-resolved { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .badge-status-closed { background: rgba(107, 114, 128, 0.15); color: #6b7280; }
    .badge-priority-high { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .badge-priority-medium { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }
    .badge-priority-low { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .badge-category { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }

    /* Card */
    .ticket-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .dark .ticket-card { background: #1f2937; }

    .ticket-card-head {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .dark .ticket-card-head { border-color: #374151; }

    .ticket-card-title {
        font-size: 1rem;
        font-weight: 800;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .dark .ticket-card-title { color: #f9fafb; }

    .ticket-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Messages */
    .message-item {
        padding: 1.5rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .dark .message-item { border-color: #374151; }
    .message-item:last-child { border-bottom: none; }

    .message-staff {
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
    }
    .dark .message-staff {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(16, 185, 129, 0.05) 100%);
    }

    .message-avatar {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.1rem;
        color: #fff;
        flex-shrink: 0;
    }

    .message-content {
        flex: 1;
        min-width: 0;
    }

    .message-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.75rem;
    }

    .message-author {
        font-weight: 700;
        font-size: 0.95rem;
        color: #111827;
    }
    .dark .message-author { color: #f9fafb; }

    .message-role {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        margin-right: 0.5rem;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .message-role-user { background: #e0f2fe; color: #0369a1; }
    .message-role-staff { background: #d1fae5; color: #065f46; }

    .message-time {
        font-size: 0.8rem;
        color: #6b7280;
    }
    .dark .message-time { color: #9ca3af; }

    .message-body {
        font-size: 0.95rem;
        color: #374151;
        line-height: 1.7;
        white-space: pre-wrap;
    }
    .dark .message-body { color: #d1d5db; }

    /* Reply Form */
    .reply-form {
        padding: 1.5rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
    }
    .dark .reply-form {
        background: #111827;
        border-color: #374151;
    }

    .reply-textarea {
        width: 100%;
        padding: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 14px;
        font-size: 0.95rem;
        resize: vertical;
        min-height: 120px;
        transition: all 0.2s;
        background: #fff;
        color: #111827;
    }
    .dark .reply-textarea {
        background: #1f2937;
        border-color: #374151;
        color: #f9fafb;
    }
    .reply-textarea:focus {
        outline: none;
        border-color: #0071AA;
        box-shadow: 0 0 0 3px rgba(0, 113, 170, 0.1);
    }

    .reply-actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 1rem;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        background: linear-gradient(135deg, #0071AA 0%, #005a88 100%);
        color: #fff;
        font-weight: 700;
        font-size: 0.9rem;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(0, 113, 170, 0.25);
    }
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(0, 113, 170, 0.35);
    }

    /* Closed Notice */
    .closed-notice {
        padding: 1.5rem;
        text-align: center;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        color: #6b7280;
        font-size: 0.9rem;
    }
    .dark .closed-notice {
        background: #111827;
        border-color: #374151;
        color: #9ca3af;
    }

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

    /* Ticket Info */
    .ticket-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-top: 0.5rem;
        font-size: 0.85rem;
        opacity: 0.85;
    }
    .ticket-info-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }
</style>
@endpush

@section('content')
<div class="ticket-page space-y-6">
    @if(session('success'))
        <div class="alert-success">
            <div class="alert-success-icon">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <span class="alert-success-text">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="ticket-header">
        <div class="flex items-start gap-4 relative z-10">
            <a href="{{ route('student.tickets.index') }}" class="back-btn">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <div class="flex-1">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-3 py-1 rounded-lg text-sm font-bold" style="background: rgba(255,255,255,0.15);">
                        {{ $ticket->ticket_number }}
                    </span>
                    <h1 class="text-xl font-extrabold">{{ $ticket->subject }}</h1>
                </div>

                <div class="status-badges">
                    <!-- Status -->
                    <span class="badge badge-status-{{ $ticket->status }}">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            @if($ticket->status == 'open')
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            @elseif($ticket->status == 'in_progress')
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"/>
                            @elseif($ticket->status == 'resolved')
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            @else
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            @endif
                        </svg>
                        {{ $ticket->getStatusLabel() }}
                    </span>

                    <!-- Category -->
                    <span class="badge badge-category">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                        {{ $ticket->getCategoryLabel() }}
                    </span>

                    <!-- Priority -->
                    <span class="badge badge-priority-{{ $ticket->priority }}">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z" clip-rule="evenodd"/>
                        </svg>
                        {{ $ticket->getPriorityLabel() }}
                    </span>
                </div>

                <div class="ticket-info">
                    <div class="ticket-info-item">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $ticket->created_at->translatedFormat('j F Y - H:i') }}</span>
                    </div>
                    @if($ticket->replies->count() > 0)
                    <div class="ticket-info-item">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span>{{ $ticket->replies->count() }} رد</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Card -->
    <div class="ticket-card">
        <div class="ticket-card-head">
            <div class="ticket-card-title">
                <div class="ticket-card-icon" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                المحادثة
            </div>
        </div>

        <!-- Original Message -->
        <div class="message-item">
            <div class="flex items-start gap-4">
                <div class="message-avatar" style="background: linear-gradient(135deg, #0071AA, #005a88);">
                    {{ mb_substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="message-content">
                    <div class="message-header">
                        <div class="flex items-center">
                            <span class="message-author">{{ auth()->user()->name }}</span>
                            <span class="message-role message-role-user">أنت</span>
                        </div>
                        <span class="message-time">{{ $ticket->created_at->translatedFormat('j F Y - H:i') }}</span>
                    </div>
                    <div class="message-body">{{ $ticket->description }}</div>
                </div>
            </div>
        </div>

        <!-- Replies -->
        @foreach($ticket->replies as $reply)
            <div class="message-item @if($reply->user_id != auth()->id()) message-staff @endif">
                <div class="flex items-start gap-4">
                    <div class="message-avatar" style="background: linear-gradient(135deg, @if($reply->user_id == auth()->id()) #0071AA, #005a88 @else #10b981, #059669 @endif);">
                        {{ mb_substr($reply->user->name ?? 'د', 0, 1) }}
                    </div>
                    <div class="message-content">
                        <div class="message-header">
                            <div class="flex items-center">
                                <span class="message-author">{{ $reply->user->name ?? 'فريق الدعم' }}</span>
                                @if($reply->user_id == auth()->id())
                                    <span class="message-role message-role-user">أنت</span>
                                @else
                                    <span class="message-role message-role-staff">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        فريق الدعم
                                    </span>
                                @endif
                            </div>
                            <span class="message-time">{{ $reply->created_at->translatedFormat('j F Y - H:i') }}</span>
                        </div>
                        <div class="message-body">{{ $reply->message }}</div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Reply Form -->
        @if(!in_array($ticket->status, ['closed']))
            <div class="reply-form">
                <form action="{{ route('student.tickets.reply', $ticket) }}" method="POST">
                    @csrf
                    <label for="message" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        إضافة رد
                    </label>
                    <textarea name="message" id="message" required
                              class="reply-textarea"
                              placeholder="اكتب ردك هنا..."></textarea>
                    @error('message')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                    <div class="reply-actions">
                        <button type="submit" class="btn-primary">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            إرسال الرد
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="closed-notice">
                <svg class="w-5 h-5 inline-block me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                هذه التذكرة مغلقة ولا يمكن إضافة ردود جديدة
            </div>
        @endif
    </div>
</div>
@endsection
