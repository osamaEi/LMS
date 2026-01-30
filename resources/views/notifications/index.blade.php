@extends('layouts.dashboard')
@section('title', 'الإشعارات')

@push('styles')
<style>
.notif-page { padding: 1.5rem; max-width: 900px; margin: 0 auto; }
.notif-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;
}
.notif-title { font-size: 1.5rem; font-weight: 800; color: #1e293b; }
.notif-badge {
    background: #ef4444; color: #fff; font-size: 0.75rem; font-weight: 700;
    padding: 0.2rem 0.65rem; border-radius: 999px; margin-right: 0.5rem;
}
.mark-all-btn {
    padding: 0.5rem 1.25rem; border-radius: 10px; font-size: 0.85rem; font-weight: 700;
    background: #eff6ff; color: #2563eb; border: none; cursor: pointer;
    transition: background 0.2s;
}
.mark-all-btn:hover { background: #dbeafe; }

.notif-list { display: flex; flex-direction: column; gap: 0.5rem; }
.notif-item {
    background: #fff; border-radius: 14px; padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    display: flex; align-items: flex-start; gap: 1rem;
    text-decoration: none; color: inherit;
    transition: box-shadow 0.2s, transform 0.15s;
    cursor: pointer; border: 2px solid transparent;
}
.notif-item:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-1px); }
.notif-item.unread { background: #eff6ff; border-color: #bfdbfe; }
.notif-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.notif-icon.created { background: linear-gradient(135deg, #3b82f6, #2563eb); }
.notif-icon.updated { background: linear-gradient(135deg, #f59e0b, #d97706); }
.notif-icon svg { width: 22px; height: 22px; color: #fff; }
.notif-content { flex: 1; min-width: 0; }
.notif-msg { font-size: 0.95rem; font-weight: 600; color: #1e293b; line-height: 1.6; margin-bottom: 0.35rem; }
.notif-meta {
    display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;
    font-size: 0.8rem; color: #64748b;
}
.notif-meta-item { display: flex; align-items: center; gap: 0.3rem; }
.notif-dot {
    width: 10px; height: 10px; border-radius: 50%; background: #3b82f6;
    flex-shrink: 0; margin-top: 0.35rem;
}
.notif-time { font-size: 0.8rem; color: #94a3b8; flex-shrink: 0; text-align: left; min-width: 80px; }

.notif-empty {
    text-align: center; padding: 4rem 1rem; color: #94a3b8;
}
.notif-empty svg { width: 64px; height: 64px; margin: 0 auto 1rem; opacity: 0.4; }
.notif-empty p { font-size: 1rem; }

.notif-pagination { margin-top: 1.5rem; display: flex; justify-content: center; }
.notif-pagination nav { display: flex; gap: 0.25rem; }

/* Dark */
.dark .notif-title { color: #f1f5f9; }
.dark .notif-item { background: #1e293b; }
.dark .notif-item.unread { background: #1e3a5f; border-color: #2563eb44; }
.dark .notif-msg { color: #e2e8f0; }
.dark .notif-meta { color: #94a3b8; }
.dark .mark-all-btn { background: #1e3a5f; color: #60a5fa; }

@media (max-width: 640px) {
    .notif-page { padding: 1rem; }
    .notif-item { padding: 1rem; flex-direction: column; gap: 0.75rem; }
    .notif-time { text-align: right; min-width: auto; }
}
</style>
@endpush

@section('content')
<div class="notif-page">
    <div class="notif-header">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <h1 class="notif-title">الإشعارات</h1>
            @if($unreadCount > 0)
                <span class="notif-badge">{{ $unreadCount }} جديد</span>
            @endif
        </div>
        @if($unreadCount > 0)
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="mark-all-btn">
                    <svg style="width:14px;height:14px;display:inline;vertical-align:middle;margin-left:0.35rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    تعليم الكل كمقروء
                </button>
            </form>
        @endif
    </div>

    @if($notifications->count() > 0)
        <div class="notif-list">
            @foreach($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);
                    $type = $data['notification_type'] ?? 'session_created';
                    $actionUrl = $data['action_url'] ?? '#';
                @endphp
                <a href="{{ $actionUrl }}"
                   class="notif-item {{ $isUnread ? 'unread' : '' }}"
                   @if($isUnread)
                   onclick="fetch('/notifications/{{ $notification->id }}/read', {method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})"
                   @endif
                >
                    <div class="notif-icon {{ $type === 'session_updated' ? 'updated' : 'created' }}">
                        @if($type === 'session_updated')
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        @else
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        @endif
                    </div>

                    <div class="notif-content">
                        <div class="notif-msg">{{ $data['message_ar'] ?? $data['session_title'] ?? 'إشعار جديد' }}</div>
                        <div class="notif-meta">
                            @if(!empty($data['subject_name']))
                                <span class="notif-meta-item">
                                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    {{ $data['subject_name'] }}
                                </span>
                            @endif
                            @if(!empty($data['scheduled_at_formatted']))
                                <span class="notif-meta-item">
                                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $data['scheduled_at_formatted'] }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($isUnread)
                        <div class="notif-dot"></div>
                    @endif

                    <div class="notif-time">{{ $notification->created_at->diffForHumans() }}</div>
                </a>
            @endforeach
        </div>

        <div class="notif-pagination">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="notif-empty">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p>لا توجد إشعارات حتى الآن</p>
        </div>
    @endif
</div>
@endsection
