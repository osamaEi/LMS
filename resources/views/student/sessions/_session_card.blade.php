@php
    $isLive      = $session->started_at && !$session->ended_at;
    $isCompleted = !is_null($session->ended_at);
    $att         = $attendances[$session->id] ?? null;
    $cardColor   = $color ?? '#0071AA';
@endphp
<div class="session-card">
    <div class="session-card-top">
        <div class="session-num" style="background:linear-gradient(135deg,{{ $isLive ? '#ef4444,#dc2626' : ($isCompleted ? '#10b981,#059669' : $cardColor.','.$cardColor.'cc') }});">
            {{ $session->session_number ?? '—' }}
        </div>
        <div style="flex:1;min-width:0;">
            <div class="session-title">{{ $session->title_ar ?: $session->title_en ?: $session->title ?: 'جلسة بدون عنوان' }}</div>
            <div class="session-info">
                @if($isLive)
                    <span class="badge badge-live"><svg style="width:10px;height:10px;" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg> مباشر</span>
                @elseif($isCompleted)
                    <span class="badge badge-completed">مكتمل</span>
                @else
                    <span class="badge badge-scheduled">مجدول</span>
                @endif
                @if($att)
                    @if($att->attended)
                        <span class="badge badge-attended"><svg style="width:10px;height:10px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg> حضرت</span>
                    @else
                        <span class="badge badge-absent"><svg style="width:10px;height:10px;" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg> غائب</span>
                    @endif
                @endif
                @if($session->files && $session->files->count() > 0)
                    <span class="badge" style="background:#f3e8ff;color:#7c3aed;">{{ $session->files->count() }} ملف</span>
                @endif
                @if($session->homework)
                    <span class="badge" style="background:#fef3c7;color:#d97706;">واجب</span>
                @endif
            </div>
        </div>
        @php $joinUrl = $session->zoom_join_url ?? null; @endphp
        @if($joinUrl && !$isCompleted)
        <a href="{{ $joinUrl }}" target="_blank" class="btn btn-join" style="white-space:nowrap;flex-shrink:0;{{ !$isLive ? 'background:linear-gradient(135deg,#2563eb,#1d4ed8);' : '' }}">
            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            انضم للحصة
        </a>
        @endif
    </div>

    @if($session->scheduled_at || $session->duration_minutes)
    <div class="session-card-meta">
        @if($session->scheduled_at)
        <div class="meta-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y/m/d · H:i') }}
        </div>
        @endif
        @if($session->duration_minutes)
        <div class="meta-item">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ $session->duration_minutes }} دقيقة
        </div>
        @endif
    </div>
    @endif

    @if($session->files && $session->files->count() > 0)
    <div style="padding:0.5rem 1.25rem;display:flex;gap:0.5rem;flex-wrap:wrap;border-top:1px solid #f3f4f6;">
        @foreach($session->files as $file)
        <a href="{{ asset('storage/'.$file->file_path) }}" target="_blank" class="file-tag">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            {{ Str::limit($file->title ?? $file->file_name ?? 'ملف', 20) }}
        </a>
        @endforeach
    </div>
    @endif

</div>
