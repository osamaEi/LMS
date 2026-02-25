@extends('layouts.dashboard')

@section('title', 'إدارة الأخبار')

@push('styles')
<style>
    .news-hero {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #003d5c 100%);
        border-radius: 1.25rem;
        position: relative;
        overflow: hidden;
    }
    .news-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        background: rgba(255,255,255,0.15);
        color: #fff;
        backdrop-filter: blur(4px);
    }
    .news-card {
        background: #fff;
        border-radius: 1rem;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
    }
    .news-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(0,113,170,0.12);
    }
    .news-card .card-img {
        width: 100%;
        height: 160px;
        object-fit: cover;
    }
    .news-card .img-placeholder {
        width: 100%;
        height: 160px;
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
    }
    .news-card .card-body {
        padding: 1rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
    }
    .status-pill::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }
    .pill-active   { background: #d1fae5; color: #065f46; }
    .pill-inactive { background: #fee2e2; color: #991b1b; }
    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid;
        transition: all 0.15s;
        cursor: pointer;
    }
    .action-btn svg { width: 14px; height: 14px; }
    .action-btn.toggle-on  { border-color: #fed7aa; color: #c2410c; background: #fff7ed; }
    .action-btn.toggle-on:hover  { background: #c2410c; color: #fff; border-color: #c2410c; }
    .action-btn.toggle-off { border-color: #bbf7d0; color: #15803d; background: #f0fdf4; }
    .action-btn.toggle-off:hover { background: #15803d; color: #fff; border-color: #15803d; }
    .action-btn.edit-btn   { border-color: #bfdbfe; color: #1d4ed8; background: #eff6ff; }
    .action-btn.edit-btn:hover   { background: #1d4ed8; color: #fff; border-color: #1d4ed8; }
    .action-btn.del-btn    { border-color: #fecaca; color: #dc2626; background: #fef2f2; }
    .action-btn.del-btn:hover    { background: #dc2626; color: #fff; border-color: #dc2626; }
</style>
@endpush

@section('content')
<div class="p-6">

    {{-- Hero --}}
    <div class="news-hero p-6 mb-6 text-white">
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-white bg-opacity-20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold">إدارة الأخبار</h1>
                </div>
                <p class="text-blue-200 text-sm mb-3">نشر وتعديل وإدارة الأخبار المعروضة في الموقع</p>
                <div class="flex flex-wrap gap-2">
                    <span class="stat-pill">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        {{ $news->total() }} خبر
                    </span>
                    <span class="stat-pill" style="background:rgba(5,150,105,0.25);">
                        <span style="width:6px;height:6px;border-radius:50%;background:#6ee7b7;display:inline-block;"></span>
                        {{ $news->where('status','active')->count() }} نشط
                    </span>
                    <span class="stat-pill" style="background:rgba(239,68,68,0.2);">
                        <span style="width:6px;height:6px;border-radius:50%;background:#fca5a5;display:inline-block;"></span>
                        {{ $news->where('status','inactive')->count() }} معطل
                    </span>
                </div>
            </div>
            <a href="{{ route('admin.news.create') }}"
               style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:#fff;color:#1d4ed8;border-radius:12px;font-weight:700;font-size:0.875rem;text-decoration:none;box-shadow:0 2px 8px rgba(0,0,0,0.15);white-space:nowrap;transition:background 0.15s;"
               onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background='#fff'">
                <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                إضافة خبر جديد
            </a>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="mb-5 flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
        </svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Cards Grid --}}
    @if($news->isEmpty())
    <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
        <div style="width:80px;height:80px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
            <svg style="width:36px;height:36px;color:#93c5fd" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
        </div>
        <p class="text-gray-500 font-medium mb-1">لا توجد أخبار بعد</p>
        <p class="text-gray-400 text-sm mb-5">ابدأ بإضافة أول خبر للموقع</p>
        <a href="{{ route('admin.news.create') }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#0071AA;color:#fff;border-radius:12px;font-size:0.875rem;font-weight:700;text-decoration:none;">
            <svg style="width:16px;height:16px" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            إضافة خبر جديد
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($news as $item)
        <div class="news-card">
            {{-- Image --}}
            @if($item->image)
                <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title_ar }}" class="card-img">
            @else
                <div class="img-placeholder">
                    <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif

            <div class="card-body">
                {{-- Status + Date --}}
                <div class="flex items-center justify-between mb-2">
                    <span class="status-pill {{ $item->status === 'active' ? 'pill-active' : 'pill-inactive' }}">
                        {{ $item->status === 'active' ? 'نشط' : 'غير نشط' }}
                    </span>
                    <span class="text-gray-400 text-xs">
                        {{ $item->published_at ? $item->published_at->format('Y/m/d') : $item->created_at->format('Y/m/d') }}
                    </span>
                </div>

                {{-- Title --}}
                <h3 class="font-bold text-gray-800 text-sm mb-1 leading-snug">
                    {{ Str::limit($item->title_ar, 70) }}
                </h3>
                @if($item->title_en)
                <p class="text-gray-400 text-xs mb-2 leading-snug" dir="ltr">{{ Str::limit($item->title_en, 60) }}</p>
                @endif

                {{-- Body preview --}}
                <p class="text-gray-500 text-xs leading-relaxed flex-1 mb-3">
                    {{ Str::limit($item->body_ar, 90) }}
                </p>

                {{-- Actions --}}
                <div class="flex items-center gap-2 pt-3 border-t border-gray-100">
                    {{-- Toggle --}}
                    <form action="{{ route('admin.news.toggle-status', $item) }}" method="POST">
                        @csrf
                        <button type="submit"
                                title="{{ $item->status === 'active' ? 'تعطيل' : 'تفعيل' }}"
                                class="action-btn {{ $item->status === 'active' ? 'toggle-on' : 'toggle-off' }}">
                            @if($item->status === 'active')
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            @else
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            @endif
                        </button>
                    </form>

                    {{-- Edit --}}
                    <a href="{{ route('admin.news.edit', $item) }}"
                       title="تعديل"
                       class="action-btn edit-btn">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>

                    {{-- Delete --}}
                    <form action="{{ route('admin.news.destroy', $item) }}" method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الخبر نهائياً؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" title="حذف" class="action-btn del-btn">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>

                    <span class="flex-1"></span>
                    <span class="text-gray-300 text-xs">#{{ $loop->iteration + ($news->currentPage() - 1) * $news->perPage() }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if($news->hasPages())
        <div class="mt-6">{{ $news->links() }}</div>
    @endif
    @endif

</div>
@endsection
