@extends('layouts.dashboard')

@section('title', 'مشكلة #' . $issue->id)

@include('admin.system-issues.partials.assets')

@section('content')
@php
    $statuses   = \App\Models\SystemIssue::STATUSES;
    $priorities = \App\Models\SystemIssue::PRIORITIES;
    $thread     = $issue->comments; // top-level only (replies loaded per comment)
    $commentCount = $thread->where('type', 'comment')->count() + $thread->sum(fn ($c) => $c->replies->count());
@endphp
<div class="si-wrap">

    <div style="font-size:.8rem;margin-bottom:6px">
        <a href="{{ route('admin.system-issues.index') }}" style="color:#6b7280;text-decoration:none">مشاكل النظام</a>
        <span class="si-muted" style="margin:0 6px">›</span>
        <span class="si-muted">#{{ $issue->id }}</span>
    </div>

    @if(session('success'))
    <div style="margin-bottom:14px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:12px;color:#16a34a;font-weight:600;font-size:.875rem">{{ session('success') }}</div>
    @endif
    @if($errors->any())
    <div style="margin-bottom:14px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;color:#dc2626;font-size:.85rem">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <div style="display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:16px;align-items:start" class="si-grid">

        {{-- ── Main column ── --}}
        <div style="display:grid;gap:16px;min-width:0">

            {{-- Issue --}}
            <div class="si-card">
                <div class="si-card-b">
                    <div data-issue-view>
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px">
                            <h1 class="si-title" style="font-size:1.3rem;font-weight:900;color:#111827;margin:0;word-break:break-word">
                                <span class="si-muted" style="font-size:1rem">#{{ $issue->id }}</span> {{ $issue->title }}
                            </h1>
                            <button type="button" class="si-btn si-btn-sm si-btn-light" onclick="siToggleIssueEdit(true)">تعديل</button>
                        </div>
                        <div class="si-muted" style="margin-top:6px">
                            سجّلها {{ $issue->creator->name ?? '—' }} · {{ $issue->created_at->format('Y/m/d H:i') }} ({{ $issue->created_at->diffForHumans() }})
                        </div>
                        @if($issue->description)
                        <div style="margin-top:14px;font-size:.9rem;color:#1f2937;white-space:pre-wrap;line-height:1.8;word-break:break-word">{{ $issue->description }}</div>
                        @endif
                        @if($issue->voiceUrl())
                        <div style="margin-top:12px;display:flex;align-items:center;gap:8px">
                            <span style="font-size:.78rem;font-weight:700;color:#dc2626">🎙 رسالة صوتية</span>
                            <audio controls preload="metadata" src="{{ $issue->voiceUrl() }}" class="si-audio"></audio>
                        </div>
                        @endif
                    </div>

                    {{-- Edit form --}}
                    <form action="{{ route('admin.system-issues.update', $issue) }}" method="POST" enctype="multipart/form-data" data-issue-edit hidden>
                        @csrf
                        @method('PUT')
                        <div style="display:grid;gap:12px">
                            <div>
                                <label class="si-label">العنوان</label>
                                <input type="text" name="title" class="si-input" value="{{ $issue->title }}" required maxlength="255">
                            </div>
                            <div>
                                <label class="si-label">الوصف</label>
                                <textarea name="description" class="si-textarea">{{ $issue->description }}</textarea>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                                <div>
                                    <label class="si-label">الأولوية</label>
                                    <select name="priority" class="si-select">
                                        @foreach($priorities as $key => $p)
                                        <option value="{{ $key }}" @selected($issue->priority === $key)>{{ $p['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="si-label">إسناد إلى</label>
                                    <select name="assigned_to" class="si-select">
                                        <option value="">— بدون —</option>
                                        @foreach($admins as $a)
                                        <option value="{{ $a->id }}" @selected((int) $issue->assigned_to === (int) $a->id)>{{ $a->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="si-label">{{ $issue->voice_path ? 'استبدال الرسالة الصوتية' : 'إضافة رسالة صوتية' }}</label>
                                @include('admin.system-issues.partials.voice-input')
                                @if($issue->voice_path)
                                <label style="display:flex;align-items:center;gap:6px;margin-top:8px;font-size:.78rem;color:#dc2626;font-weight:600;cursor:pointer">
                                    <input type="checkbox" name="remove_voice" value="1"> حذف الرسالة الصوتية الحالية
                                </label>
                                @endif
                            </div>
                            <div style="display:flex;gap:8px;justify-content:flex-end">
                                <button type="button" class="si-btn si-btn-light" onclick="siToggleIssueEdit(false)">إلغاء</button>
                                <button type="submit" class="si-btn si-btn-primary">حفظ التعديلات</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Attachments --}}
            <div class="si-card">
                <div class="si-card-h"><h2>📎 المرفقات ({{ $issue->attachments->count() }})</h2></div>
                <div class="si-card-b">
                    @include('admin.system-issues.partials.attachments', ['attachments' => $issue->attachments])
                    <form action="{{ route('admin.system-issues.attachments.store', $issue) }}" method="POST" enctype="multipart/form-data"
                          style="margin-top:{{ $issue->attachments->count() ? '14px' : '0' }}">
                        @csrf
                        @include('admin.system-issues.partials.attachments-input')
                        <div style="display:flex;justify-content:flex-end;margin-top:8px">
                            <button type="submit" class="si-btn si-btn-sm si-btn-primary">رفع الملفات</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Tags --}}
            <div class="si-card">
                <div class="si-card-h"><h2>🏷 التاجات ({{ $issue->tags->count() }})</h2></div>
                <div class="si-card-b">
                    @if($issue->tags->count())
                    <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:12px">
                        @foreach($issue->tags as $tag)
                            @include('admin.system-issues.partials.tag', ['tag' => $tag, 'removable' => true])
                        @endforeach
                    </div>
                    @endif
                    @include('admin.system-issues.partials.tag-picker', ['mode' => 'submit'])
                    <p class="si-muted" style="margin:8px 0 0">اختر النوع ثم ابحث واضغط على النتيجة لإضافتها. اضغط على أي تاج للانتقال إلى صفحته.</p>
                </div>
            </div>

            {{-- Discussion --}}
            <div class="si-card">
                <div class="si-card-h"><h2>💬 التعليقات ({{ $commentCount }})</h2></div>
                <div class="si-card-b" style="padding-top:4px">
                    @forelse($thread as $comment)
                        <div style="{{ !$loop->first ? 'border-top:1px solid #f1f5f9' : '' }}">
                            @include('admin.system-issues.partials.comment', ['comment' => $comment, 'isReply' => false])
                        </div>
                    @empty
                        <p class="si-muted" style="padding:14px 0;text-align:center">لا توجد تعليقات بعد — كن أول من يعلّق.</p>
                    @endforelse
                </div>

                {{-- New comment --}}
                <form action="{{ route('admin.system-issues.comments.store', $issue) }}" method="POST" enctype="multipart/form-data"
                      style="padding:14px 18px;border-top:1px solid #f1f5f9;background:#fafafa;border-radius:0 0 16px 16px">
                    @csrf
                    <textarea name="body" class="si-textarea" style="min-height:80px;background:#fff" placeholder="اكتب تعليقاً...">{{ old('parent_id') ? '' : old('body') }}</textarea>
                    <div style="margin-top:8px">@include('admin.system-issues.partials.attachments-input')</div>
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:8px;flex-wrap:wrap">
                        @include('admin.system-issues.partials.voice-input')
                        <button type="submit" class="si-btn si-btn-primary">إضافة تعليق</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Side column ── --}}
        <div style="display:grid;gap:16px">
            <div class="si-card">
                <div class="si-card-h"><h2>الحالة</h2>
                    <span class="si-chip" style="{{ $issue->statusStyle() }}">{{ $issue->statusLabel() }}</span>
                </div>
                <div class="si-card-b" style="display:grid;gap:6px">
                    @foreach($statuses as $key => $s)
                    <form action="{{ route('admin.system-issues.status', $issue) }}" method="POST" style="margin:0">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $key }}">
                        <button type="submit" class="si-btn" @disabled($issue->status === $key)
                                style="width:100%;justify-content:flex-start;{{ $issue->status === $key ? "background:{$s['color']};color:#fff;cursor:default" : "background:{$s['bg']};color:{$s['color']}" }}">
                            <span style="width:8px;height:8px;border-radius:50%;background:currentColor"></span>
                            {{ $s['label'] }}
                            @if($issue->status === $key)<span style="margin-right:auto">✓</span>@endif
                        </button>
                    </form>
                    @endforeach
                </div>
            </div>

            <div class="si-card">
                <div class="si-card-b" style="display:grid;gap:12px;font-size:.82rem">
                    <div style="display:flex;justify-content:space-between;gap:8px"><span class="si-muted">الأولوية</span>
                        <span class="si-chip" style="{{ $issue->priorityStyle() }}">{{ $issue->priorityLabel() }}</span></div>
                    <div style="display:flex;justify-content:space-between;gap:8px"><span class="si-muted">مسندة إلى</span>
                        <b style="color:#334155">{{ $issue->assignee->name ?? '—' }}</b></div>
                    <div style="display:flex;justify-content:space-between;gap:8px"><span class="si-muted">آخر تحديث</span>
                        <span style="color:#334155">{{ $issue->updated_at->diffForHumans() }}</span></div>
                    @if($issue->resolved_at)
                    <div style="display:flex;justify-content:space-between;gap:8px"><span class="si-muted">تاريخ الحل</span>
                        <span style="color:#16a34a;font-weight:700">{{ $issue->resolved_at->format('Y/m/d H:i') }}</span></div>
                    @endif
                </div>
            </div>

            <form action="{{ route('admin.system-issues.destroy', $issue) }}" method="POST"
                  onsubmit="return confirm('حذف هذه المشكلة نهائياً؟')">
                @csrf
                @method('DELETE')
                <button type="submit" class="si-btn si-btn-danger" style="width:100%">حذف المشكلة</button>
            </form>
        </div>
    </div>
</div>

<style>
    @media (max-width: 900px) { .si-grid { grid-template-columns: 1fr !important; } }
    .dark .si-bubble { background: rgba(255,255,255,.05) !important; }
    .dark .si-bubble div { color: #e5e7eb !important; }
</style>
@endsection

@push('scripts')
<script>
function siToggleIssueEdit(on) {
    document.querySelector('[data-issue-view]').hidden = on;
    document.querySelector('[data-issue-edit]').hidden = !on;
}
function siToggleEdit(id, on) {
    const box = document.getElementById('comment-' + id);
    box.querySelector('[data-view]').hidden = on;
    box.querySelector('[data-edit]').hidden = !on;
    if (on) box.querySelector('[data-edit] textarea').focus();
}
function siToggleReply(id, mention) {
    const f = document.getElementById('reply-form-' + id);
    if (!f) return;
    const show = f.hidden || !!mention;
    f.hidden = !show;
    if (show) {
        const ta = f.querySelector('textarea');
        if (mention && !ta.value.startsWith('@' + mention)) ta.value = '@' + mention + ' ' + ta.value;
        ta.focus();
    }
}
// Open the thread a link points at (#comment-123) with a short highlight.
(function () {
    const el = location.hash && document.querySelector(location.hash);
    if (el) { el.scrollIntoView({ block: 'center' }); el.style.transition = 'background 1.5s'; el.style.background = '#eef2ff'; setTimeout(() => el.style.background = '', 1600); }
})();
</script>
@endpush
