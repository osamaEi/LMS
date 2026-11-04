@extends('layouts.dashboard')

@section('title', 'تسجيل مشكلة')

@include('admin.system-issues.partials.assets')

@section('content')
<div class="si-wrap" style="max-width:820px">

    <div style="font-size:.8rem;margin-bottom:6px">
        <a href="{{ route('admin.system-issues.index') }}" style="color:#6b7280;text-decoration:none">مشاكل النظام</a>
        <span class="si-muted" style="margin:0 6px">›</span>
        <span class="si-muted">تسجيل مشكلة</span>
    </div>
    <h1 class="si-title" style="font-size:1.4rem;font-weight:900;color:#111827;margin:0 0 16px">تسجيل مشكلة جديدة</h1>

    @if($errors->any())
    <div style="margin-bottom:14px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;color:#dc2626;font-size:.85rem">
        @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
    </div>
    @endif

    <form action="{{ route('admin.system-issues.store') }}" method="POST" enctype="multipart/form-data" class="si-card">
        @csrf
        <div class="si-card-b" style="display:grid;gap:16px">
            <div>
                <label class="si-label">عنوان المشكلة *</label>
                <input type="text" name="title" class="si-input" value="{{ old('title') }}" required maxlength="255"
                       placeholder="مثال: الطالب لا يظهر في كشف حضور المحاضرة">
            </div>

            <div>
                <label class="si-label">الوصف</label>
                <textarea name="description" class="si-textarea" placeholder="اشرح المشكلة: ماذا حدث، متى، وكيف يمكن تكرارها...">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="si-label">رسالة صوتية (اختياري)</label>
                @include('admin.system-issues.partials.voice-input')
            </div>

            <div>
                <label class="si-label">مرفقات — ملفات، صور، فيديو (اختياري)</label>
                @include('admin.system-issues.partials.attachments-input')
                <p class="si-muted" style="margin:6px 0 0">حتى 10 ملفات، وكل ملف حتى 100 ميجابايت.</p>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div>
                    <label class="si-label">الأولوية</label>
                    <select name="priority" class="si-select">
                        @foreach(\App\Models\SystemIssue::PRIORITIES as $key => $p)
                        <option value="{{ $key }}" @selected(old('priority', 'medium') === $key)>{{ $p['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="si-label">إسناد إلى</label>
                    <select name="assigned_to" class="si-select">
                        <option value="">— بدون —</option>
                        @foreach($admins as $a)
                        <option value="{{ $a->id }}" @selected((string) old('assigned_to') === (string) $a->id)>{{ $a->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="si-label">التاجات — اربط المشكلة بطالب أو مدرب أو أي عنصر في النظام</label>
                @include('admin.system-issues.partials.tag-picker', ['mode' => 'inputs', 'list' => 'new-tags'])
                <div id="new-tags" style="display:flex;gap:6px;flex-wrap:wrap;margin-top:10px"></div>
            </div>
        </div>

        <div style="padding:14px 18px;border-top:1px solid #f1f5f9;display:flex;gap:8px;justify-content:flex-end">
            <a href="{{ route('admin.system-issues.index') }}" class="si-btn si-btn-light">إلغاء</a>
            <button type="submit" class="si-btn si-btn-primary">تسجيل المشكلة</button>
        </div>
    </form>
</div>
@endsection
