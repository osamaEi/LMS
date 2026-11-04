{{-- Show attachments: images as thumbnails, videos with a player, other files as download rows. $attachments, $issue. --}}
@php
    $me     = auth()->user();
    $media  = $attachments->filter->isInline();
    $others = $attachments->reject->isInline();
@endphp
@if($attachments->count())
<div style="margin-top:10px;display:grid;gap:8px">
    @if($media->count())
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        @foreach($media as $a)
        <div class="si-media">
            @if($a->isImage())
            <a href="{{ $a->url() }}" target="_blank" rel="noopener" title="{{ $a->original_name }}">
                <img src="{{ $a->url() }}" alt="{{ $a->original_name }}" loading="lazy">
            </a>
            @else
            <video src="{{ $a->url() }}" controls preload="metadata" title="{{ $a->original_name }}"></video>
            @endif
            <div class="si-media-bar">
                <span class="si-media-name" title="{{ $a->original_name }}">{{ $a->original_name }}</span>
                <a href="{{ $a->url(true) }}" title="تحميل" class="si-media-act">⬇</a>
                @if((int) $a->user_id === (int) $me->id || $me->role === 'super_admin')
                <form action="{{ route('admin.system-issues.attachments.destroy', [$issue, $a]) }}" method="POST" style="margin:0"
                      onsubmit="return confirm('حذف هذا الملف؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="si-media-act" title="حذف" style="color:#dc2626">✕</button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    @endif

    @foreach($others as $a)
    <div class="si-file-row">
        <span style="font-size:1.2rem">{{ $a->icon() }}</span>
        <a href="{{ $a->url(true) }}" class="si-file-name" title="تحميل {{ $a->original_name }}">{{ $a->original_name }}</a>
        <span class="si-muted">{{ $a->humanSize() }}</span>
        @if((int) $a->user_id === (int) $me->id || $me->role === 'super_admin')
        <form action="{{ route('admin.system-issues.attachments.destroy', [$issue, $a]) }}" method="POST" style="margin:0"
              onsubmit="return confirm('حذف هذا الملف؟')">
            @csrf
            @method('DELETE')
            <button type="submit" class="si-tag-x" title="حذف" style="color:#dc2626">×</button>
        </form>
        @endif
    </div>
    @endforeach
</div>
@endif
