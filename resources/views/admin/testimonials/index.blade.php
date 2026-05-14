@extends('layouts.dashboard')

@section('title', 'إدارة آراء المتدربين')

@push('styles')
<style>
.testi-hero {
    background: linear-gradient(135deg,#0071AA 0%,#005a88 50%,#003d5c 100%);
    border-radius: 24px; padding: 2rem 2.5rem;
    position: relative; overflow: hidden; margin-bottom: 1.75rem;
}
.testi-hero::before {
    content:''; position:absolute; inset:0;
    background:repeating-linear-gradient(45deg,rgba(255,255,255,.03) 0,rgba(255,255,255,.03) 1px,transparent 0,transparent 50%) 0 0/20px 20px;
}
.testi-card {
    background:#fff; border-radius:16px;
    box-shadow:0 1px 4px rgba(0,0,0,.07);
    border: 1px solid #f0f0f0;
    transition: box-shadow .2s, transform .2s;
}
.dark .testi-card { background:#1f2937; border-color:#374151; }
.testi-card:hover { box-shadow:0 6px 20px rgba(0,0,0,.1); transform:translateY(-2px); }
.testi-avatar {
    width:52px; height:52px; border-radius:50%;
    background:linear-gradient(135deg,#0071AA,#005a88);
    display:flex; align-items:center; justify-content:center;
    color:#fff; font-weight:900; font-size:1.3rem; flex-shrink:0;
}
.stars { color:#f59e0b; font-size:.85rem; letter-spacing:1px; }
.badge-active   { background:#d1fae5; color:#065f46; padding:.2rem .65rem; border-radius:20px; font-size:.72rem; font-weight:700; }
.badge-inactive { background:#fee2e2; color:#991b1b; padding:.2rem .65rem; border-radius:20px; font-size:.72rem; font-weight:700; }
</style>
@endpush

@section('content')
<div class="p-6">

    {{-- Hero --}}
    <div class="testi-hero">
        <div style="position:relative;z-index:2;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
            <div>
                <h1 style="color:#fff;font-size:1.5rem;font-weight:900;margin:0 0 .25rem;">آراء المتدربين</h1>
                <p style="color:rgba(255,255,255,.75);margin:0;font-size:.9rem;">{{ $testimonials->count() }} تقييم مضاف</p>
            </div>
            <a href="{{ route('admin.testimonials.create') }}"
               style="display:inline-flex;align-items:center;gap:.5rem;background:#fff;color:#0071AA;font-weight:800;font-size:.9rem;padding:.65rem 1.4rem;border-radius:12px;text-decoration:none;box-shadow:0 4px 12px rgba(0,0,0,.2);">
                <i class="bi bi-plus-lg"></i> إضافة تقييم
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-3 rounded-xl text-green-800 bg-green-50 border border-green-200 flex items-center gap-2">
        <i class="bi bi-check-circle-fill text-green-500"></i> {{ session('success') }}
    </div>
    @endif

    @if($testimonials->isEmpty())
    <div class="text-center py-16 text-gray-400">
        <i class="bi bi-chat-quote" style="font-size:3rem;"></i>
        <p class="mt-3 font-semibold">لا توجد آراء بعد — أضف أول تقييم</p>
    </div>
    @else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        @foreach($testimonials as $t)
        <div class="testi-card p-5 flex flex-col gap-3">
            {{-- header --}}
            <div class="flex items-center gap-3">
                <div class="testi-avatar">{{ mb_substr($t->author_ar, 0, 1) }}</div>
                <div class="flex-1">
                    <div class="font-bold text-gray-800 dark:text-gray-100">{{ $t->author_ar }}</div>
                    @if($t->author_en)<div class="text-xs text-gray-400">{{ $t->author_en }}</div>@endif
                </div>
                <span class="{{ $t->is_active ? 'badge-active' : 'badge-inactive' }}">
                    {{ $t->is_active ? 'نشط' : 'مخفي' }}
                </span>
            </div>

            {{-- stars --}}
            <div class="stars">
                @for($i=1;$i<=5;$i++)
                    <i class="bi bi-star{{ $i <= $t->rating ? '-fill' : '' }}"></i>
                @endfor
            </div>

            {{-- text --}}
            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed flex-1" style="margin:0;">
                "{{ Str::limit($t->text_ar, 120) }}"
            </p>

            @if($t->role_ar)
            <div class="text-xs text-gray-400 font-semibold">{{ $t->role_ar }}</div>
            @endif

            {{-- order --}}
            <div class="text-xs text-gray-300 dark:text-gray-600">ترتيب: {{ $t->sort_order }}</div>

            {{-- actions --}}
            <div class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                <a href="{{ route('admin.testimonials.edit', $t) }}"
                   class="flex-1 text-center py-2 rounded-lg text-sm font-bold text-white"
                   style="background:#0071AA; text-decoration:none;">
                    <i class="bi bi-pencil-fill"></i> تعديل
                </a>
                <form action="{{ route('admin.testimonials.destroy', $t) }}" method="POST"
                      onsubmit="return confirm('حذف هذا التقييم؟')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 rounded-lg text-sm font-bold text-white"
                            style="background:#ef4444;border:none;cursor:pointer;">
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
