@extends('layouts.dashboard')

@section('title', 'تذاكر الدعم')

@push('styles')
<style>
    .tickets-hero {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #003f66 100%);
        border-radius: 22px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0,113,170,0.35);
    }
    .tickets-hero::before {
        content: '';
        position: absolute;
        top: -60%;
        right: -8%;
        width: 45%;
        height: 250%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 65%);
        pointer-events: none;
    }
    .tickets-hero::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: 5%;
        width: 30%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.06) 0%, transparent 65%);
        pointer-events: none;
    }
    .stat-card {
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 16px;
        padding: 16px 20px;
        backdrop-filter: blur(10px);
        transition: all 0.2s;
        text-decoration: none;
        display: block;
        flex: 1;
        min-width: 120px;
    }
    .stat-card:hover {
        background: rgba(255,255,255,0.2);
        text-decoration: none;
        transform: translateY(-2px);
    }
    .filter-tab {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 18px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1.5px solid transparent;
        white-space: nowrap;
        color: #6b7280;
        background: #fff;
        border-color: #e5e7eb;
    }
    .dark .filter-tab {
        background: #1e293b;
        border-color: rgba(255,255,255,0.1);
        color: rgba(255,255,255,0.6);
    }
    .filter-tab:hover {
        border-color: #0071AA;
        color: #0071AA;
        text-decoration: none;
    }
    .filter-tab.active {
        background: #0071AA;
        border-color: #0071AA;
        color: #fff;
    }
    .filter-tab .tab-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 5px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        background: rgba(0,0,0,0.12);
    }
    .filter-tab.active .tab-badge {
        background: rgba(255,255,255,0.25);
        color: #fff;
    }
    .ticket-row {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        text-decoration: none;
        transition: background 0.15s;
        position: relative;
    }
    .dark .ticket-row {
        border-color: rgba(255,255,255,0.07);
    }
    .ticket-row:last-child {
        border-bottom: none;
    }
    .ticket-row:hover {
        background: #f8faff;
        text-decoration: none;
    }
    .dark .ticket-row:hover {
        background: rgba(255,255,255,0.03);
    }
    .ticket-row::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        border-radius: 0 4px 4px 0;
        opacity: 0;
        transition: opacity 0.15s;
    }
    .ticket-row:hover::before {
        opacity: 1;
    }
    .ticket-row[data-status="open"]::before        { background: #f59e0b; }
    .ticket-row[data-status="in_progress"]::before { background: #3b82f6; }
    .ticket-row[data-status="waiting_response"]::before { background: #f97316; }
    .ticket-row[data-status="resolved"]::before    { background: #10b981; }
    .ticket-row[data-status="closed"]::before      { background: #6b7280; }

    .status-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .priority-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .new-ticket-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: rgba(255,255,255,0.18);
        border: 1.5px solid rgba(255,255,255,0.4);
        color: #fff;
        font-weight: 700;
        border-radius: 14px;
        text-decoration: none;
        transition: all 0.2s;
        font-size: 0.9rem;
        backdrop-filter: blur(8px);
    }
    .new-ticket-btn:hover {
        background: #fff;
        color: #0071AA;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl">

    {{-- Hero --}}
    <div class="tickets-hero mb-6">
        <div class="relative z-10">
            {{-- Top row --}}
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap;margin-bottom:1.5rem">
                <div style="display:flex;align-items:center;gap:16px">
                    <div style="width:56px;height:56px;border-radius:16px;background:rgba(255,255,255,0.18);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
                            <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12zM7 9h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 style="font-size:1.7rem;font-weight:900;color:#fff;margin:0;line-height:1.2">تذاكر الدعم</h1>
                        <p style="font-size:0.875rem;color:rgba(255,255,255,0.7);margin:5px 0 0">أرسل طلباتك واستفساراتك لفريق الدعم</p>
                    </div>
                </div>
                <a href="{{ route('student.tickets.create') }}" class="new-ticket-btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    تذكرة جديدة
                </a>
            </div>

            {{-- Stats --}}
            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <a href="{{ route('student.tickets.index') }}" class="stat-card">
                    <div style="font-size:1.6rem;font-weight:900;color:#fff;line-height:1">{{ $stats['total'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.7);margin-top:4px">الإجمالي</div>
                </a>
                <a href="{{ route('student.tickets.index', ['status' => 'open']) }}" class="stat-card">
                    <div style="font-size:1.6rem;font-weight:900;color:#fbbf24;line-height:1">{{ $stats['open'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.7);margin-top:4px">مفتوحة</div>
                </a>
                <a href="{{ route('student.tickets.index', ['status' => 'in_progress']) }}" class="stat-card">
                    <div style="font-size:1.6rem;font-weight:900;color:#60a5fa;line-height:1">{{ $stats['in_progress'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.7);margin-top:4px">قيد المعالجة</div>
                </a>
                <a href="{{ route('student.tickets.index', ['status' => 'resolved']) }}" class="stat-card">
                    <div style="font-size:1.6rem;font-weight:900;color:#34d399;line-height:1">{{ $stats['resolved'] }}</div>
                    <div style="font-size:0.75rem;color:rgba(255,255,255,0.7);margin-top:4px">تم الحل</div>
                </a>
            </div>
        </div>
    </div>

    {{-- Filter Tabs + List --}}
    <div style="background:#fff;border-radius:20px;border:1.5px solid #f1f5f9;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.05)" class="dark:bg-slate-800 dark:border-white/10">

        {{-- Tabs --}}
        <div style="padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;gap:8px;flex-wrap:wrap;align-items:center" class="dark:border-white/10">
            @php
                $filterDefs = [
                    ['value' => null,          'label' => 'الكل',           'count' => $stats['total']],
                    ['value' => 'open',        'label' => 'مفتوحة',          'count' => $stats['open']],
                    ['value' => 'in_progress', 'label' => 'قيد المعالجة',    'count' => $stats['in_progress']],
                    ['value' => 'resolved',    'label' => 'تم الحل',         'count' => $stats['resolved']],
                ];
            @endphp
            @foreach($filterDefs as $f)
            @php
                $isActive = ($statusFilter === $f['value']) || ($f['value'] === null && !$statusFilter);
                $href = $f['value'] ? route('student.tickets.index', ['status' => $f['value']]) : route('student.tickets.index');
            @endphp
            <a href="{{ $href }}" class="filter-tab {{ $isActive ? 'active' : '' }}">
                {{ $f['label'] }}
                <span class="tab-badge">{{ $f['count'] }}</span>
            </a>
            @endforeach

            <div style="margin-right:auto;font-size:0.78rem;color:#9ca3af">
                {{ $tickets->total() }} نتيجة
            </div>
        </div>

        {{-- Tickets --}}
        @forelse($tickets as $ticket)
        @php
            $statusConfig = match($ticket->status) {
                'open'             => ['label' => 'مفتوحة',       'bg' => '#fef3c7', 'color' => '#d97706', 'icon_bg' => '#fef3c7', 'icon_color' => '#f59e0b'],
                'in_progress'      => ['label' => 'قيد المعالجة', 'bg' => '#dbeafe', 'color' => '#2563eb', 'icon_bg' => '#dbeafe', 'icon_color' => '#3b82f6'],
                'waiting_response' => ['label' => 'بانتظار ردك',  'bg' => '#ffedd5', 'color' => '#ea580c', 'icon_bg' => '#ffedd5', 'icon_color' => '#f97316'],
                'resolved'         => ['label' => 'تم الحل',       'bg' => '#d1fae5', 'color' => '#059669', 'icon_bg' => '#d1fae5', 'icon_color' => '#10b981'],
                'closed'           => ['label' => 'مغلقة',         'bg' => '#f3f4f6', 'color' => '#6b7280', 'icon_bg' => '#f3f4f6', 'icon_color' => '#9ca3af'],
                default            => ['label' => $ticket->status, 'bg' => '#f3f4f6', 'color' => '#6b7280', 'icon_bg' => '#f3f4f6', 'icon_color' => '#9ca3af'],
            };
            $priorityConfig = match($ticket->priority) {
                'urgent' => ['label' => 'عاجل',    'color' => '#dc2626', 'dot' => '#dc2626'],
                'high'   => ['label' => 'مرتفع',   'color' => '#f97316', 'dot' => '#f97316'],
                'medium' => ['label' => 'متوسط',   'color' => '#d97706', 'dot' => '#f59e0b'],
                default  => ['label' => 'منخفض',   'color' => '#6b7280', 'dot' => '#9ca3af'],
            };
        @endphp
        <a href="{{ route('student.tickets.show', $ticket) }}"
           class="ticket-row"
           data-status="{{ $ticket->status }}">

            {{-- Status Icon --}}
            <div class="status-icon" style="background:{{ $statusConfig['icon_bg'] }}">
                @if($ticket->status === 'open')
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $statusConfig['icon_color'] }}" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                @elseif($ticket->status === 'in_progress')
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $statusConfig['icon_color'] }}" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                @elseif($ticket->status === 'waiting_response')
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $statusConfig['icon_color'] }}" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                @elseif($ticket->status === 'resolved')
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $statusConfig['icon_color'] }}" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                @else
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="{{ $statusConfig['icon_color'] }}" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                @endif
            </div>

            {{-- Main Info --}}
            <div style="flex:1;min-width:0">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap">
                    <span style="font-size:0.72rem;font-weight:600;color:#9ca3af;font-family:monospace">
                        {{ $ticket->ticket_number ?? '#' . $ticket->id }}
                    </span>
                    <h3 style="font-size:0.95rem;font-weight:800;color:#111827;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:400px" class="dark:text-white">
                        {{ $ticket->subject }}
                    </h3>
                </div>

                <p style="font-size:0.8rem;color:#6b7280;margin:0 0 10px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical">
                    {{ Str::limit($ticket->description ?? $ticket->message, 110) }}
                </p>

                <div style="display:flex;flex-wrap:wrap;align-items:center;gap:8px">
                    {{-- Category --}}
                    <span style="font-size:0.72rem;font-weight:700;padding:3px 10px;border-radius:8px;background:#e6f4fa;color:#0071AA">
                        {{ $ticket->getCategoryLabel() }}
                    </span>

                    {{-- Priority --}}
                    <span style="font-size:0.72rem;font-weight:600;display:inline-flex;align-items:center;gap:4px;color:{{ $priorityConfig['color'] }}">
                        <span class="priority-dot" style="background:{{ $priorityConfig['dot'] }}"></span>
                        {{ $priorityConfig['label'] }}
                    </span>

                    {{-- Date --}}
                    <span style="font-size:0.72rem;color:#9ca3af;display:inline-flex;align-items:center;gap:4px">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                        </svg>
                        {{ $ticket->created_at->diffForHumans() }}
                    </span>

                    {{-- Replies --}}
                    @if($ticket->replies_count > 0)
                    <span style="font-size:0.72rem;color:#6b7280;display:inline-flex;align-items:center;gap:4px">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        {{ $ticket->replies_count }} رد
                    </span>
                    @endif
                </div>
            </div>

            {{-- Right: Status Badge + Arrow --}}
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px;flex-shrink:0">
                <span style="font-size:0.75rem;font-weight:700;padding:5px 12px;border-radius:10px;background:{{ $statusConfig['bg'] }};color:{{ $statusConfig['color'] }};white-space:nowrap">
                    {{ $statusConfig['label'] }}
                </span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </div>
        </a>
        @empty
        <div style="padding:80px 40px;text-align:center">
            <div style="width:80px;height:80px;border-radius:22px;background:linear-gradient(135deg,#e6f4fa,#dbeafe);display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#0071AA" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
            <h3 style="font-size:1.1rem;font-weight:800;color:#111827;margin:0 0 8px" class="dark:text-white">
                @if($statusFilter)
                    لا توجد تذاكر في هذا التصنيف
                @else
                    لم تُنشئ أي تذكرة بعد
                @endif
            </h3>
            <p style="font-size:0.875rem;color:#9ca3af;margin:0 0 24px;max-width:340px;margin-left:auto;margin-right:auto">
                @if($statusFilter)
                    جرّب تصفية مختلفة أو أنشئ تذكرة جديدة للتواصل مع الدعم
                @else
                    تواصل مع فريق الدعم الفني لحل أي مشكلة أو استفسار
                @endif
            </p>
            <a href="{{ route('student.tickets.create') }}"
               style="display:inline-flex;align-items:center;gap:8px;padding:11px 26px;background:#0071AA;color:#fff;font-weight:700;border-radius:14px;text-decoration:none;font-size:0.9rem;box-shadow:0 8px 24px rgba(0,113,170,0.3);transition:all 0.2s"
               onmouseover="this.style.background='#005a88'" onmouseout="this.style.background='#0071AA'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                إنشاء تذكرة جديدة
            </a>
        </div>
        @endforelse

        {{-- Pagination --}}
        @if($tickets->hasPages())
        <div style="padding:16px 24px;border-top:1px solid #f1f5f9" class="dark:border-white/10">
            {{ $tickets->links() }}
        </div>
        @endif
    </div>

    {{-- Help tip --}}
    @if($tickets->count() > 0)
    <div style="margin-top:16px;padding:14px 20px;background:linear-gradient(135deg,rgba(0,113,170,0.06),rgba(0,113,170,0.02));border-radius:14px;border:1px solid rgba(0,113,170,0.12);display:flex;align-items:center;gap:12px">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="#0071AA" style="flex-shrink:0">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 17h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 12.9 13 13.5 13 15h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/>
        </svg>
        <p style="font-size:0.8rem;color:#374151;margin:0" class="dark:text-white/70">
            يمكنك متابعة تذاكرك والرد على ردود فريق الدعم مباشرة من هنا. عادةً ما يتم الرد خلال 24 ساعة.
        </p>
    </div>
    @endif

</div>
@endsection
