@extends('layouts.dashboard')
@section('title', 'تقييم المدرب')

@push('styles')
<style>
.rate-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    max-width: 680px;
    margin: 0 auto;
}
.teacher-header {
    background: linear-gradient(135deg, #0071AA, #005a88);
    border-radius: 20px 20px 0 0;
    padding: 2.5rem 2rem;
    text-align: center;
    color: #fff;
}
.teacher-avatar {
    width: 80px; height: 80px;
    border-radius: 50%;
    border: 4px solid rgba(255,255,255,0.3);
    margin: 0 auto 1rem;
}
.rate-body { padding: 2rem; }
.rate-group {
    margin-bottom: 1.75rem;
}
.rate-label {
    font-weight: 700;
    font-size: 0.95rem;
    color: #1e293b;
    margin-bottom: 0.75rem;
    display: block;
}
.stars {
    display: flex;
    gap: 0.35rem;
    direction: ltr;
}
.stars input { display: none; }
.stars label {
    cursor: pointer;
    font-size: 2rem;
    color: #d1d5db;
    transition: color 0.15s, transform 0.15s;
}
.stars label:hover,
.stars label:hover ~ label,
.stars input:checked ~ label {
    color: #f59e0b;
    transform: scale(1.1);
}
/* Reverse order for RTL star selection */
.stars { flex-direction: row-reverse; justify-content: flex-end; }
.stars label:hover ~ label,
.stars input:checked ~ label { color: #d1d5db; }
.stars label:hover,
.stars label:hover ~ label { color: #f59e0b; }
.stars input:checked + label,
.stars input:checked + label ~ label { color: #f59e0b; }

.comment-box {
    width: 100%;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    padding: 1rem;
    font-size: 0.95rem;
    resize: vertical;
    min-height: 100px;
    transition: border-color 0.2s;
    font-family: 'Cairo', sans-serif;
}
.comment-box:focus {
    outline: none;
    border-color: #0071AA;
}
.submit-btn {
    width: 100%;
    padding: 0.9rem;
    background: linear-gradient(135deg, #0071AA, #005a88);
    color: #fff;
    border: none;
    border-radius: 14px;
    font-size: 1rem;
    font-weight: 800;
    cursor: pointer;
    transition: opacity 0.2s;
}
.submit-btn:hover { opacity: 0.9; }
.back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    color: #64748b;
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    margin-bottom: 1.5rem;
}
.back-link:hover { color: #0071AA; }

@media (max-width: 640px) {
    .rate-body { padding: 1.25rem; }
    .stars label { font-size: 1.6rem; }
}

/* Dark mode */
.dark .rate-card { background: #1e293b; }
.dark .rate-label { color: #e2e8f0; }
.dark .comment-box { background: #0f172a; border-color: #334155; color: #e2e8f0; }
.dark .comment-box:focus { border-color: #0071AA; }
</style>
@endpush

@section('content')
<div style="padding: 1.5rem; max-width: 1200px; margin: 0 auto;">
    <a href="{{ route('student.teacher-ratings.index') }}" class="back-link">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        العودة لقائمة التقييمات
    </a>

    <div class="rate-card">
        <div class="teacher-header">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($subject->teacher->name ?? 'T') }}&background=ffffff&color=0071AA&size=160&bold=true"
                 class="teacher-avatar" />
            <h2 style="font-size: 1.35rem; font-weight: 800; margin-bottom: 0.25rem;">{{ $subject->teacher->name ?? '-' }}</h2>
            <p style="opacity: 0.8; font-size: 0.9rem;">{{ $subject->name }}</p>
        </div>

        <div class="rate-body">
            <form action="{{ route('student.teacher-ratings.store', $subject->id) }}" method="POST">
                @csrf

                @php
                    $criteria = [
                        ['name' => 'knowledge_rating', 'label' => 'الإلمام بالمادة العلمية', 'icon' => '📚'],
                        ['name' => 'communication_rating', 'label' => 'مهارات التواصل والشرح', 'icon' => '💬'],
                        ['name' => 'punctuality_rating', 'label' => 'الالتزام بالمواعيد', 'icon' => '⏰'],
                        ['name' => 'support_rating', 'label' => 'الدعم والمساعدة', 'icon' => '🤝'],
                    ];
                @endphp

                @foreach($criteria as $c)
                <div class="rate-group">
                    <label class="rate-label">{{ $c['icon'] }} {{ $c['label'] }}</label>
                    <div class="stars" id="stars-{{ $c['name'] }}">
                        @for($i = 5; $i >= 1; $i--)
                            <input type="radio" name="{{ $c['name'] }}" id="{{ $c['name'] }}_{{ $i }}" value="{{ $i }}" {{ old($c['name']) == $i ? 'checked' : '' }} required>
                            <label for="{{ $c['name'] }}_{{ $i }}">&#9733;</label>
                        @endfor
                    </div>
                    @error($c['name'])
                        <p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>
                @endforeach

                <div class="rate-group">
                    <label class="rate-label">💭 تعليق (اختياري)</label>
                    <textarea name="comment" class="comment-box" placeholder="شاركنا رأيك عن المدرب...">{{ old('comment') }}</textarea>
                    @error('comment')
                        <p style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="submit-btn">إرسال التقييم</button>
            </form>
        </div>
    </div>
</div>
@endsection
