{{-- One comment (or reply). $comment, $issue, $isReply. --}}
@php
    $me      = auth()->user();
    $canEdit = (int) $comment->user_id === (int) $me->id || $me->role === 'super_admin';
    $name    = $comment->user->name ?? 'مستخدم محذوف';
    $initial = mb_substr($name, 0, 1);
@endphp

@if($comment->isStatusChange())
<div id="comment-{{ $comment->id }}" style="display:flex;align-items:center;gap:8px;padding:6px 0;font-size:.78rem;color:#64748b">
    <span style="width:26px;height:26px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0">⟳</span>
    <span><b style="color:#334155">{{ $name }}</b> غيّر الحالة: {{ $comment->body }}</span>
    <span class="si-muted" title="{{ $comment->created_at->format('Y/m/d H:i') }}">· {{ $comment->created_at->diffForHumans() }}</span>
</div>
@else
<div id="comment-{{ $comment->id }}" style="display:flex;gap:10px;padding:{{ $isReply ? '10px 0 0' : '14px 0' }}">
    <div style="width:{{ $isReply ? 28 : 34 }}px;height:{{ $isReply ? 28 : 34 }}px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-weight:800;font-size:.8rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">{{ $initial }}</div>
    <div style="flex:1;min-width:0">
        <div style="background:{{ $isReply ? '#f8fafc' : '#f1f5f9' }};border-radius:12px;padding:10px 14px" class="si-bubble">
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-bottom:4px">
                <b class="si-title" style="font-size:.82rem;color:#111827">{{ $name }}</b>
                <span class="si-muted" title="{{ $comment->created_at->format('Y/m/d H:i') }}">{{ $comment->created_at->diffForHumans() }}</span>
                @if($comment->edited_at)<span class="si-muted">· معدّل</span>@endif
            </div>

            <div data-view>
                @if($comment->body)
                <div style="font-size:.86rem;color:#1f2937;white-space:pre-wrap;line-height:1.7;word-break:break-word">{{ $comment->body }}</div>
                @endif
                @if($comment->voiceUrl())
                <audio controls preload="none" src="{{ $comment->voiceUrl() }}" class="si-audio" style="margin-top:6px"></audio>
                @endif
            </div>
            @include('admin.system-issues.partials.attachments', ['attachments' => $comment->attachments])

            @if($canEdit)
            <form action="{{ route('admin.system-issues.comments.update', [$issue, $comment]) }}" method="POST" data-edit hidden style="margin:0">
                @csrf
                @method('PUT')
                <textarea name="body" class="si-textarea" style="min-height:70px">{{ $comment->body }}</textarea>
                <div style="display:flex;gap:6px;margin-top:6px">
                    <button type="submit" class="si-btn si-btn-sm si-btn-primary">حفظ</button>
                    <button type="button" class="si-btn si-btn-sm si-btn-light" onclick="siToggleEdit({{ $comment->id }}, false)">إلغاء</button>
                </div>
            </form>
            @endif
        </div>

        <div style="display:flex;gap:12px;margin-top:4px;padding-right:4px;font-size:.75rem;font-weight:700">
            <button type="button" onclick="siToggleReply({{ $isReply ? $comment->parent_id : $comment->id }}, {{ Js::from($isReply ? $name : null) }})"
                    style="background:none;border:none;color:#6366f1;cursor:pointer;padding:0;font:inherit">ردّ</button>
            @if($canEdit)
            <button type="button" onclick="siToggleEdit({{ $comment->id }}, true)"
                    style="background:none;border:none;color:#64748b;cursor:pointer;padding:0;font:inherit">تعديل</button>
            <form action="{{ route('admin.system-issues.comments.destroy', [$issue, $comment]) }}" method="POST" style="margin:0"
                  onsubmit="return confirm('{{ $isReply ? 'حذف هذا الرد؟' : 'حذف هذا التعليق وكل الردود عليه؟' }}')">
                @csrf
                @method('DELETE')
                <button type="submit" style="background:none;border:none;color:#dc2626;cursor:pointer;padding:0;font:inherit">حذف</button>
            </form>
            @endif
        </div>

        @unless($isReply)
            @foreach($comment->replies as $reply)
                @include('admin.system-issues.partials.comment', ['comment' => $reply, 'isReply' => true])
            @endforeach

            {{-- Reply form (hidden until "ردّ" is clicked) --}}
            <form action="{{ route('admin.system-issues.comments.store', $issue) }}" method="POST" enctype="multipart/form-data"
                  id="reply-form-{{ $comment->id }}" hidden style="margin-top:10px">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                <textarea name="body" class="si-textarea" style="min-height:60px" placeholder="اكتب ردّك..."></textarea>
                <div style="margin-top:6px">@include('admin.system-issues.partials.attachments-input')</div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:6px;flex-wrap:wrap">
                    @include('admin.system-issues.partials.voice-input')
                    <div style="display:flex;gap:6px">
                        <button type="button" class="si-btn si-btn-sm si-btn-light" onclick="siToggleReply({{ $comment->id }})">إلغاء</button>
                        <button type="submit" class="si-btn si-btn-sm si-btn-primary">إرسال الرد</button>
                    </div>
                </div>
            </form>
        @endunless
    </div>
</div>
@endif
