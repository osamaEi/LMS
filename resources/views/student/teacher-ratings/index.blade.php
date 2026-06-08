@extends('layouts.dashboard')
@section('title', 'تقييم المدربين')

@push('styles')
<style>
.tr-page { max-width: 1200px; margin: 0 auto; }

.tr-header {
    background: linear-gradient(135deg, #0071AA 0%, #004d77 100%);
    border-radius: 24px; padding: 2rem 2.5rem; color: #fff;
    position: relative; overflow: hidden; margin-bottom: 1.5rem;
}
.tr-header::before {
    content:''; position:absolute; top:-40%; right:-10%;
    width:280px; height:280px;
    background:radial-gradient(circle,rgba(255,255,255,0.08) 0%,transparent 70%);
    border-radius:50%;
}

.tr-section-title {
    font-size: 0.9rem; font-weight: 700; color: #374151;
    margin-bottom: 0.85rem; display: flex; align-items: center; gap: 0.5rem;
    padding-bottom: 0.5rem; border-bottom: 2px solid #f3f4f6;
}
.dark .tr-section-title { color: #e2e8f0; border-color: #374151; }

.tr-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; margin-bottom: 2rem; }

.tr-card {
    background: #fff; border-radius: 18px; padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.06);
    display: flex; align-items: center; gap: 1rem;
    border: 2px solid transparent; transition: border-color 0.18s, transform 0.18s, box-shadow 0.18s;
}
.tr-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(236,72,153,0.12); border-color: #ec4899; }
.dark .tr-card { background: #1f2937; }

.tr-avatar { width: 52px; height: 52px; border-radius: 14px; flex-shrink: 0; object-fit: cover; }
.tr-info { flex: 1; min-width: 0; }
.tr-name { font-weight: 700; font-size: 0.95rem; color: #111827; truncate; }
.dark .tr-name { color: #f9fafb; }
.tr-subject { font-size: 0.78rem; color: #6b7280; margin-top: 0.2rem; }
.tr-program-badge {
    display: inline-block; font-size: 0.68rem; font-weight: 700;
    padding: 0.15rem 0.55rem; border-radius: 6px; margin-top: 0.3rem;
}

.tr-btn {
    padding: 0.5rem 1.1rem; border-radius: 10px; font-size: 0.82rem; font-weight: 700;
    text-decoration: none; color: #fff; background: linear-gradient(135deg, #ec4899, #be185d);
    flex-shrink: 0; transition: opacity 0.2s; white-space: nowrap;
}
.tr-btn:hover { opacity: 0.85; color: #fff; }

.tr-stars { display: flex; gap: 0.1rem; margin-top: 0.3rem; }
.tr-stars span { color: #f59e0b; font-size: 0.95rem; }
.tr-stars span.empty { color: #e5e7eb; }
.tr-comment { font-size: 0.78rem; color: #6b7280; margin-top: 0.45rem; line-height: 1.5; }

.tr-done-card {
    background: #fff; border-radius: 18px; padding: 1.25rem 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    border: 2px solid #f0fdf4;
}
.dark .tr-done-card { background: #1f2937; border-color: #064e3b; }

.tr-empty {
    text-align: center; padding: 3rem 1rem; background: #fff; border-radius: 18px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04); color: #9ca3af; font-size: 0.9rem;
}
.dark .tr-empty { background: #1f2937; }

.alert-box { padding: 0.85rem 1.25rem; border-radius: 12px; font-size: 0.9rem; font-weight: 600; margin-bottom: 1rem; }
.alert-success { background: #ecfdf5; color: #065f46; }
.alert-error { background: #fef2f2; color: #991b1b; }
.alert-info { background: #eff6ff; color: #1e40af; }
</style>
@endpush

@section('content')
<div class="tr-page space-y-5">

    {{-- Header --}}
    <div class="tr-header">
        <div style="position:relative;z-index:1;">
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                <div style="width:52px;height:52px;border-radius:16px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:26px;height:26px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div>
                    <h1 style="font-size:1.6rem;font-weight:800;line-height:1.2;">تقييم المدربين</h1>
                    <p style="opacity:.75;font-size:.9rem;margin-top:.2rem;">قيّم المدربين في برامجك ومقرراتك المسجلة</p>
                </div>
            </div>
            <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                <div style="background:rgba(255,255,255,0.15);border-radius:14px;padding:.75rem 1.25rem;text-align:center;">
                    <div style="font-size:1.4rem;font-weight:800;">{{ $ratableSubjects->count() }}</div>
                    <div style="font-size:.72rem;opacity:.8;">بانتظار تقييمك</div>
                </div>
                <div style="background:rgba(255,255,255,0.15);border-radius:14px;padding:.75rem 1.25rem;text-align:center;">
                    <div style="font-size:1.4rem;font-weight:800;">{{ $submittedRatings->count() }}</div>
                    <div style="font-size:.72rem;opacity:.8;">تقييم مُرسَل</div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-box alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert-box alert-error">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert-box alert-info">{{ session('info') }}</div>
    @endif

    {{-- Ratable teachers --}}
    @if($ratableSubjects->count() > 0)
    <div>
        <div class="tr-section-title">
            <svg style="width:16px;height:16px;color:#ec4899;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            مدربون بانتظار تقييمك
            <span style="font-size:.75rem;font-weight:600;color:#ec4899;background:#fdf2f8;padding:.15rem .5rem;border-radius:6px;">{{ $ratableSubjects->count() }}</span>
        </div>
        <div class="tr-grid">
            @foreach($ratableSubjects as $subject)
            @php
                $prog = $subject->term->program ?? null;
                $progType = $prog?->type ?? 'course';
                $typeLabel = match($progType) { 'diploma'=>'دبلومة', 'english'=>'إنجليزي', 'training'=>'تدريب', default=>'دورة' };
                $typeColor = match($progType) { 'diploma'=>'#7c3aed,#ede9fe', 'english'=>'#0891b2,#cffafe', 'training'=>'#059669,#d1fae5', default=>'#0071AA,#e0f2fe' };
                [$tc, $tbg] = explode(',', $typeColor);
            @endphp
            <div class="tr-card">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($subject->teacher->name ?? 'T') }}&background=ec4899&color=fff&size=112&bold=true"
                     class="tr-avatar" />
                <div class="tr-info">
                    <div class="tr-name">{{ $subject->teacher->name ?? '-' }}</div>
                    <div class="tr-subject">{{ $subject->name_ar ?? $subject->name }}</div>
                    @if($prog)
                    <span class="tr-program-badge" style="color:{{ $tc }};background:{{ $tbg }};">{{ $typeLabel }}: {{ Str::limit($prog->name_ar ?? $prog->name, 22) }}</span>
                    @endif
                </div>
                <a href="{{ route('student.teacher-ratings.create', $subject->id) }}" class="tr-btn">قيّم</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Submitted ratings --}}
    <div>
        <div class="tr-section-title">
            <svg style="width:16px;height:16px;color:#10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            تقييماتي المُرسَلة
        </div>
        @if($submittedRatings->count() > 0)
        <div class="tr-grid">
            @foreach($submittedRatings as $rating)
            @php
                $prog = $rating->subject?->term?->program ?? null;
                $progType = $prog?->type ?? 'course';
                $typeLabel = match($progType) { 'diploma'=>'دبلومة', 'english'=>'إنجليزي', 'training'=>'تدريب', default=>'دورة' };
                $typeColor = match($progType) { 'diploma'=>'#7c3aed,#ede9fe', 'english'=>'#0891b2,#cffafe', 'training'=>'#059669,#d1fae5', default=>'#0071AA,#e0f2fe' };
                [$tc, $tbg] = explode(',', $typeColor);
                $rate = $rating->overall_rating;
                $rateColor = $rate >= 4 ? '#10b981' : ($rate >= 3 ? '#f59e0b' : '#ef4444');
            @endphp
            <div class="tr-done-card">
                <div style="display:flex;align-items:center;gap:.85rem;margin-bottom:.75rem;">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($rating->teacher->name ?? 'T') }}&background=0071AA&color=fff&size=96&bold=true"
                         class="tr-avatar" style="width:44px;height:44px;" />
                    <div style="flex:1;min-width:0;">
                        <div class="tr-name">{{ $rating->teacher->name ?? '-' }}</div>
                        <div class="tr-subject">{{ $rating->subject?->name_ar ?? $rating->subject?->name ?? '-' }}</div>
                        @if($prog)
                        <span class="tr-program-badge" style="color:{{ $tc }};background:{{ $tbg }};">{{ $typeLabel }}: {{ Str::limit($prog->name_ar ?? $prog->name, 20) }}</span>
                        @endif
                    </div>
                    <div style="font-weight:800;font-size:1.15rem;color:{{ $rateColor }};flex-shrink:0;">
                        {{ number_format($rate, 1) }} ★
                    </div>
                </div>
                <div class="tr-stars">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= round($rate) ? '' : 'empty' }}">★</span>
                    @endfor
                    <span style="font-size:.72rem;color:#9ca3af;margin-right:.5rem;">{{ $rating->created_at->diffForHumans() }}</span>
                </div>
                @if($rating->is_approved)
                    <span style="font-size:.7rem;background:#d1fae5;color:#065f46;padding:.15rem .5rem;border-radius:6px;font-weight:700;margin-top:.4rem;display:inline-block;">معتمد</span>
                @else
                    <span style="font-size:.7rem;background:#fef3c7;color:#92400e;padding:.15rem .5rem;border-radius:6px;font-weight:700;margin-top:.4rem;display:inline-block;">قيد المراجعة</span>
                @endif
                @if($rating->comment)
                    <div class="tr-comment">"{{ Str::limit($rating->comment, 120) }}"</div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="tr-empty">
            <svg style="width:48px;height:48px;margin:0 auto 1rem;color:#e5e7eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
            <p>لا توجد تقييمات مُرسَلة بعد</p>
            @if($ratableSubjects->count() > 0)
                <p style="font-size:.82rem;color:#ec4899;margin-top:.5rem;">لديك {{ $ratableSubjects->count() }} مدرب بانتظار تقييمك ↑</p>
            @endif
        </div>
        @endif
    </div>

</div>
@endsection
