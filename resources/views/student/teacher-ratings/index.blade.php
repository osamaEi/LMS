@extends('layouts.dashboard')
@section('title', 'تقييم المدربين')

@push('styles')
<style>
.tr-page { padding: 1.5rem; max-width: 1200px; margin: 0 auto; }
.tr-title { font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-bottom: 0.25rem; }
.tr-sub { color: #64748b; font-size: 0.9rem; margin-bottom: 1.5rem; }
.tr-section-title {
    font-size: 1rem; font-weight: 700; color: #1e293b;
    margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;
}
.tr-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
.tr-card {
    background: #fff; border-radius: 16px; padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    display: flex; align-items: center; gap: 1rem;
}
.tr-avatar { width: 56px; height: 56px; border-radius: 50%; flex-shrink: 0; }
.tr-info { flex: 1; min-width: 0; }
.tr-name { font-weight: 700; font-size: 0.95rem; color: #1e293b; }
.tr-subject { font-size: 0.8rem; color: #64748b; margin-top: 0.15rem; }
.tr-btn {
    padding: 0.5rem 1.25rem; border-radius: 10px; font-size: 0.85rem; font-weight: 700;
    text-decoration: none; color: #fff; background: linear-gradient(135deg, #ec4899, #be185d);
    flex-shrink: 0; transition: opacity 0.2s;
}
.tr-btn:hover { opacity: 0.85; }
.tr-stars { display: flex; gap: 0.15rem; margin-top: 0.35rem; }
.tr-stars span { color: #f59e0b; font-size: 1rem; }
.tr-stars span.empty { color: #d1d5db; }
.tr-comment { font-size: 0.8rem; color: #64748b; margin-top: 0.5rem; line-height: 1.5; }
.tr-empty {
    text-align: center; padding: 3rem 1rem; color: #94a3b8; font-size: 0.95rem;
}
.alert-box {
    padding: 0.85rem 1.25rem; border-radius: 12px; font-size: 0.9rem; font-weight: 600;
    margin-bottom: 1rem;
}
.alert-success { background: #ecfdf5; color: #065f46; }
.alert-error { background: #fef2f2; color: #991b1b; }
.alert-info { background: #eff6ff; color: #1e40af; }

/* Dark */
.dark .tr-title, .dark .tr-name { color: #f1f5f9; }
.dark .tr-card { background: #1e293b; }
.dark .tr-section-title { color: #e2e8f0; }
</style>
@endpush

@section('content')
<div class="tr-page">
    <h1 class="tr-title">تقييم المدربين</h1>
    <p class="tr-sub">قيّم المدربين في المواد المسجلة لديك</p>

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
        <div class="tr-section-title">⭐ مدربون بانتظار تقييمك</div>
        <div class="tr-grid">
            @foreach($ratableSubjects as $subject)
                <div class="tr-card">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($subject->teacher->name ?? 'T') }}&background=ec4899&color=fff&size=112&bold=true"
                         class="tr-avatar" />
                    <div class="tr-info">
                        <div class="tr-name">{{ $subject->teacher->name ?? '-' }}</div>
                        <div class="tr-subject">{{ $subject->name }}</div>
                    </div>
                    <a href="{{ route('student.teacher-ratings.create', $subject->id) }}" class="tr-btn">قيّم</a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Submitted ratings --}}
    <div class="tr-section-title">📋 تقييماتي السابقة</div>
    @if($submittedRatings->count() > 0)
        <div class="tr-grid">
            @foreach($submittedRatings as $rating)
                <div class="tr-card" style="flex-direction: column; align-items: flex-start;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; width: 100%;">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($rating->teacher->name ?? 'T') }}&background=0071AA&color=fff&size=112&bold=true"
                             class="tr-avatar" style="width: 44px; height: 44px;" />
                        <div class="tr-info">
                            <div class="tr-name">{{ $rating->teacher->name ?? '-' }}</div>
                            <div class="tr-subject">{{ $rating->subject->name ?? '-' }}</div>
                        </div>
                        <div style="font-weight: 800; color: #f59e0b; font-size: 1.1rem; flex-shrink: 0;">
                            {{ number_format($rating->overall_rating, 1) }} ★
                        </div>
                    </div>
                    <div class="tr-stars">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= round($rating->overall_rating) ? '' : 'empty' }}">★</span>
                        @endfor
                    </div>
                    @if($rating->comment)
                        <div class="tr-comment">{{ Str::limit($rating->comment, 120) }}</div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="tr-empty">لا توجد تقييمات سابقة</div>
    @endif
</div>
@endsection
