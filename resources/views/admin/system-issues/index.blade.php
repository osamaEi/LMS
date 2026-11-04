@extends('layouts.dashboard')

@section('title', 'مشاكل النظام')

@include('admin.system-issues.partials.assets')

@section('content')
@php
    $statuses   = \App\Models\SystemIssue::STATUSES;
    $priorities = \App\Models\SystemIssue::PRIORITIES;
    $tagTypes   = \App\Models\SystemIssueTag::types();
    $openCount  = ($counts['open'] ?? 0) + ($counts['in_progress'] ?? 0) + ($counts['waiting'] ?? 0);
@endphp
<div class="si-wrap">

    {{-- Header --}}
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:18px">
        <div>
            <h1 class="si-title" style="font-size:1.5rem;font-weight:900;color:#111827;margin:0">مشاكل النظام</h1>
            <p class="si-muted" style="margin:4px 0 0;font-size:.85rem">سجّل أي مشكلة في النظام، اربطها بطالب أو مدرب أو أي عنصر، وتابع حالتها مع الفريق.</p>
        </div>
        <a href="{{ route('admin.system-issues.create') }}" class="si-btn si-btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
            تسجيل مشكلة
        </a>
    </div>

    @if(session('success'))
    <div style="margin-bottom:14px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:12px;color:#16a34a;font-weight:600;font-size:.875rem">{{ session('success') }}</div>
    @endif

    {{-- Status tabs --}}
    <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px">
        @php $base = request()->except(['status', 'page']); @endphp
        <a href="{{ route('admin.system-issues.index', $base) }}" class="si-btn si-btn-sm"
           style="{{ !$status ? 'background:#111827;color:#fff' : 'background:#fff;color:#334155;border:1.5px solid #e5e7eb' }}">
            الكل <span style="opacity:.7">({{ $counts->sum() }})</span>
        </a>
        @foreach($statuses as $key => $s)
        <a href="{{ route('admin.system-issues.index', $base + ['status' => $key]) }}" class="si-btn si-btn-sm"
           style="{{ $status === $key ? "background:{$s['color']};color:#fff" : "background:{$s['bg']};color:{$s['color']}" }}">
            {{ $s['label'] }} <span style="opacity:.75">({{ $counts[$key] ?? 0 }})</span>
        </a>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.system-issues.index') }}" class="si-card" style="padding:12px;margin-bottom:16px;display:flex;gap:8px;flex-wrap:wrap;align-items:center">
        @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
        <input type="text" name="search" value="{{ $search }}" class="si-input" style="flex:2;min-width:200px" placeholder="ابحث بالعنوان أو الوصف أو اسم التاج أو رقم المشكلة...">
        <select name="priority" class="si-select" style="width:auto;min-width:130px">
            <option value="">كل الأولويات</option>
            @foreach($priorities as $key => $p)
            <option value="{{ $key }}" @selected($priority === $key)>{{ $p['label'] }}</option>
            @endforeach
        </select>
        <select name="tag_type" class="si-select" style="width:auto;min-width:130px">
            <option value="">كل التاجات</option>
            @foreach($tagTypes as $key => $t)
            <option value="{{ $key }}" @selected($tagType === $key)>مرتبطة بـ{{ $t['label'] }}</option>
            @endforeach
        </select>
        <label style="display:flex;align-items:center;gap:6px;font-size:.8rem;font-weight:600;color:#374151;cursor:pointer">
            <input type="checkbox" name="mine" value="1" @checked($mine)> المسندة لي
        </label>
        <button type="submit" class="si-btn si-btn-light">بحث</button>
        @if($search || $priority || $tagType || $mine)
        <a href="{{ route('admin.system-issues.index', $status ? ['status' => $status] : []) }}" class="si-btn si-btn-sm si-btn-danger">مسح الفلاتر</a>
        @endif
    </form>

    {{-- List --}}
    @forelse($issues as $issue)
    <a href="{{ route('admin.system-issues.show', $issue) }}" class="si-card" style="display:block;padding:14px 18px;margin-bottom:10px;text-decoration:none;transition:border-color .15s"
       onmouseover="this.style.borderColor='#c7d2fe'" onmouseout="this.style.borderColor=''">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
            <div style="min-width:0;flex:1">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                    <span class="si-muted" style="font-weight:700">#{{ $issue->id }}</span>
                    <span class="si-title" style="font-weight:800;color:#111827;font-size:.95rem">{{ $issue->title }}</span>
                    @if($issue->voice_path)
                    <span title="يحتوي على رسالة صوتية" style="color:#dc2626">🎙</span>
                    @endif
                </div>
                @if($issue->tags->count())
                {{-- Tags here are plain chips: the whole card is a link, and nested links aren't valid HTML. --}}
                <div style="display:flex;gap:6px;flex-wrap:wrap;margin-top:8px">
                    @foreach($issue->tags->take(6) as $tag)
                    <span class="si-tag" style="color:{{ $tag->color() }};padding:2px 8px 2px 6px">
                        <span class="si-tag-type" style="background:{{ $tag->color() }}">{{ $tag->typeLabel() }}</span>
                        <span class="si-tag-label" style="max-width:160px">{{ $tag->label }}</span>
                    </span>
                    @endforeach
                    @if($issue->tags->count() > 6)
                    <span class="si-chip" style="background:#f1f5f9;color:#64748b">+{{ $issue->tags->count() - 6 }}</span>
                    @endif
                </div>
                @endif
                <div class="si-muted" style="margin-top:8px">
                    بواسطة {{ $issue->creator->name ?? '—' }} · {{ $issue->created_at->diffForHumans() }}
                    @if($issue->assignee) · مسندة إلى <b style="color:#475569">{{ $issue->assignee->name }}</b>@endif
                    · 💬 {{ $issue->comments_count }}
                    @if($issue->all_attachments_count) · 📎 {{ $issue->all_attachments_count }}@endif
                </div>
            </div>
            <div style="display:flex;gap:6px;flex-shrink:0">
                <span class="si-chip" style="{{ $issue->priorityStyle() }}">{{ $issue->priorityLabel() }}</span>
                <span class="si-chip" style="{{ $issue->statusStyle() }}">{{ $issue->statusLabel() }}</span>
            </div>
        </div>
    </a>
    @empty
    <div class="si-card" style="padding:60px 20px;text-align:center">
        <div style="font-size:2.5rem">✅</div>
        <p class="si-title" style="font-weight:800;color:#111827;margin:8px 0 4px">لا توجد مشاكل</p>
        <p class="si-muted">{{ $search || $status || $priority || $tagType || $mine ? 'لا توجد نتائج مطابقة للفلاتر.' : 'لم يتم تسجيل أي مشكلة بعد.' }}</p>
    </div>
    @endforelse

    @if($issues->hasPages())
    <div style="display:flex;justify-content:center;padding:12px 0">{{ $issues->links() }}</div>
    @endif
</div>
@endsection
