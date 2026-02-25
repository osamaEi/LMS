@extends('layouts.dashboard')

@section('title', 'الملفات والموارد')

@push('styles')
<style>
    .files-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(0,0,0,0.25);
    }
    .files-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -5%;
        width: 40%;
        height: 200%;
        background: radial-gradient(ellipse, rgba(0,113,170,0.2) 0%, transparent 70%);
        pointer-events: none;
    }
    .subject-tab {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 14px;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        background: none;
        width: 100%;
        text-align: right;
        text-decoration: none;
        color: inherit;
    }
    .subject-tab:hover {
        background: rgba(0,113,170,0.08);
        text-decoration: none;
        color: inherit;
    }
    .subject-tab.active {
        background: linear-gradient(135deg, #0071AA, #005a88);
        color: #fff;
    }
    .subject-tab.active .tab-count {
        background: rgba(255,255,255,0.25);
        color: #fff;
    }
    .tab-count {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 22px;
        padding: 0 6px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        background: #dbeafe;
        color: #0071AA;
        margin-right: auto;
        flex-shrink: 0;
    }
    .file-card {
        background: #fff;
        border-radius: 16px;
        border: 1.5px solid #f1f5f9;
        overflow: hidden;
        transition: all 0.25s;
    }
    .dark .file-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.08);
    }
    .file-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.1);
        border-color: #0071AA;
    }
    .preview-modal {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .preview-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.75);
        backdrop-filter: blur(4px);
    }
    .preview-container {
        position: relative;
        z-index: 1;
        background: #0f172a;
        border-radius: 20px;
        overflow: hidden;
        width: 100%;
        max-width: 900px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 40px 100px rgba(0,0,0,0.5);
    }
    .preview-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        flex-shrink: 0;
    }
    .preview-body {
        flex: 1;
        overflow: hidden;
        min-height: 0;
    }
    .preview-body iframe,
    .preview-body video {
        width: 100%;
        min-height: 480px;
        border: none;
        display: block;
    }
    .type-badge-pdf {
        background: #fee2e2;
        color: #dc2626;
    }
    .type-badge-video {
        background: #dbeafe;
        color: #2563eb;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl" x-data="filesApp()" x-init="init()">

    {{-- Hero --}}
    <div class="files-hero mb-6">
        <div class="relative z-10" style="display:flex;align-items:center;gap:20px;flex-wrap:wrap">
            <div style="display:flex;align-items:center;gap:16px">
                <div style="width:56px;height:56px;border-radius:16px;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 8px 24px rgba(0,113,170,0.4)">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="white">
                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8 17h8v-1.5H8V17zm0-3h8v-1.5H8V14zm0-3h5v-1.5H8V11z"/>
                    </svg>
                </div>
                <div>
                    <h1 style="font-size:1.6rem;font-weight:900;color:#fff;margin:0">الملفات والموارد</h1>
                    <p style="font-size:0.875rem;color:rgba(255,255,255,0.6);margin:4px 0 0">
                        جميع ملفات وفيديوهات موادك الدراسية
                    </p>
                </div>
            </div>
            <div style="margin-right:auto;display:flex;gap:10px;flex-wrap:wrap">
                @php
                    $totalFiles = $subjects->sum(fn($s) => $s->sessions->sum(fn($sess) => $sess->files->count()));
                    $totalPdfs  = $subjects->sum(fn($s) => $s->sessions->sum(fn($sess) => $sess->files->where('type','pdf')->count()));
                    $totalVids  = $subjects->sum(fn($s) => $s->sessions->sum(fn($sess) => $sess->files->where('type','video')->count()));
                @endphp
                @foreach([[$totalFiles,'إجمالي الملفات','#38bdf8'],[$totalPdfs,'PDF','#f87171'],[$totalVids,'فيديو','#60a5fa']] as [$count,$label,$color])
                <div style="background:rgba(255,255,255,0.08);border-radius:12px;padding:8px 16px;text-align:center">
                    <div style="font-size:1.3rem;font-weight:900;color:{{ $color }}">{{ $count }}</div>
                    <div style="font-size:0.72rem;color:rgba(255,255,255,0.6)">{{ $label }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    @if($subjects->isEmpty())
    {{-- No program enrolled --}}
    <div style="background:#fff;border-radius:18px;border:1.5px solid #f1f5f9;padding:80px 40px;text-align:center" class="dark:bg-slate-800 dark:border-white/10">
        <div style="width:80px;height:80px;border-radius:22px;background:#f0f9ff;display:flex;align-items:center;justify-content:center;margin:0 auto 20px" class="dark:bg-white/10">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#0071AA" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p style="font-size:1.1rem;font-weight:700;color:#374151;margin:0 0 8px" class="dark:text-white">لا توجد ملفات</p>
        <p style="font-size:0.875rem;color:#9ca3af;margin:0 0 20px">سجّل في برنامج دراسي للوصول إلى الملفات والموارد</p>
        <a href="{{ route('student.my-program') }}"
           style="display:inline-flex;align-items:center;gap:8px;padding:10px 24px;background:#0071AA;color:#fff;border-radius:12px;font-weight:700;text-decoration:none;font-size:0.875rem">
            اختر برنامجك الدراسي
        </a>
    </div>
    @else
    <div style="display:grid;grid-template-columns:260px 1fr;gap:20px;align-items:start">

        {{-- Subject Sidebar --}}
        <div style="background:#fff;border-radius:18px;border:1.5px solid #f1f5f9;padding:12px;position:sticky;top:80px" class="dark:bg-slate-800 dark:border-white/10">
            <div style="font-size:0.72rem;font-weight:700;color:#9ca3af;letter-spacing:0.08em;padding:4px 8px 10px;text-transform:uppercase">
                المواد الدراسية
            </div>

            <a href="{{ route('student.files.index') }}"
               class="subject-tab {{ $activeSubjectId === 0 ? 'active' : '' }}">
                <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white">
                        <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z"/>
                    </svg>
                </div>
                <span style="font-size:0.875rem;font-weight:600;color:{{ $activeSubjectId === 0 ? '#fff' : '#374151' }}" class="{{ $activeSubjectId === 0 ? '' : 'dark:text-white' }}">
                    جميع المواد
                </span>
                <span class="tab-count">{{ $totalFiles }}</span>
            </a>

            <div style="margin:8px 0;height:1px;background:#f1f5f9" class="dark:bg-white/10"></div>

            @foreach($subjects as $subject)
            @php
                $subjectFileCount = $subject->sessions->sum(fn($s) => $s->files->count());
                $isActive = $activeSubjectId === $subject->id;
                $subjectColor = $subject->color ?? '#0071AA';
            @endphp
            <a href="{{ route('student.files.index', ['subject' => $subject->id]) }}"
               class="subject-tab {{ $isActive ? 'active' : '' }}">
                <div style="width:32px;height:32px;border-radius:8px;background:{{ $subjectColor }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="white">
                        <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3zM5 13.18v4L12 21l7-3.82v-4L12 17l-7-3.82z"/>
                    </svg>
                </div>
                <span style="font-size:0.83rem;font-weight:600;color:{{ $isActive ? '#fff' : '#374151' }};flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" class="{{ $isActive ? '' : 'dark:text-white' }}">
                    {{ $subject->name }}
                </span>
                <span class="tab-count" style="background:{{ $subjectFileCount > 0 ? '#dbeafe' : '#f1f5f9' }};color:{{ $subjectFileCount > 0 ? '#0071AA' : '#9ca3af' }}">
                    {{ $subjectFileCount }}
                </span>
            </a>
            @endforeach
        </div>

        {{-- Files Grid --}}
        <div>
            @php
                $displaySubjects = $activeSubjectId
                    ? $subjects->where('id', $activeSubjectId)
                    : $subjects;
                $hasAnyFiles = $displaySubjects->sum(fn($s) => $s->sessions->sum(fn($sess) => $sess->files->count())) > 0;
            @endphp

            @if(!$hasAnyFiles)
            <div style="background:#fff;border-radius:18px;border:1.5px solid #f1f5f9;padding:60px 40px;text-align:center" class="dark:bg-slate-800 dark:border-white/10">
                <div style="width:72px;height:72px;border-radius:20px;background:#f0f9ff;display:flex;align-items:center;justify-content:center;margin:0 auto 16px" class="dark:bg-white/10">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#0071AA" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p style="font-size:1rem;font-weight:700;color:#374151;margin:0 0 6px" class="dark:text-white">لا توجد ملفات بعد</p>
                <p style="font-size:0.83rem;color:#9ca3af;margin:0">سيتم إضافة الملفات والفيديوهات من قِبل أستاذ المادة</p>
            </div>
            @else
            @foreach($displaySubjects as $subject)
            @php
                $allFiles = $subject->sessions->flatMap(fn($s) => $s->files->map(fn($f) => ['file' => $f, 'session' => $s]));
            @endphp
            @if($allFiles->count())
            <div style="margin-bottom:28px">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px">
                    <div style="width:36px;height:36px;border-radius:10px;background:{{ $subject->color ?? '#0071AA' }};display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="white">
                            <path d="M12 3L1 9l11 6 9-4.91V17h2V9L12 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 style="font-size:1rem;font-weight:800;color:#111827;margin:0" class="dark:text-white">{{ $subject->name }}</h2>
                        <span style="font-size:0.75rem;color:#6b7280">{{ $allFiles->count() }} ملف</span>
                    </div>
                    <div style="flex:1;height:1px;background:#e5e7eb;margin-right:8px" class="dark:bg-white/10"></div>
                </div>

                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px">
                    @foreach($allFiles as $item)
                    @php
                        $file    = $item['file'];
                        $session = $item['session'];
                        $isPdf   = $file->type === 'pdf';
                        $isVideo = $file->type === 'video';
                        $fileUrl = $file->getFileUrl();
                    @endphp
                    <div class="file-card"
                         style="cursor:{{ $fileUrl ? 'pointer' : 'default' }}"
                         @if($fileUrl)
                         @click="openPreview({
                             id: {{ $file->id }},
                             title: @js($file->title ?? $session->title),
                             type: '{{ $file->type }}',
                             url: @js($fileUrl),
                             session: @js($session->title),
                             size: @js($file->getFormattedSize()),
                             duration: @js($file->getDurationFormatted())
                         })"
                         @endif>
                        <div style="height:4px;background:{{ $isPdf ? 'linear-gradient(90deg,#dc2626,#ef4444)' : 'linear-gradient(90deg,#0071AA,#38bdf8)' }}"></div>

                        <div style="padding:16px">
                            <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px">
                                <div style="width:48px;height:48px;border-radius:12px;background:{{ $isPdf ? '#fee2e2' : '#e0f2fe' }};display:flex;align-items:center;justify-content:center">
                                    @if($isPdf)
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="#dc2626">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM8 17h8v-1.5H8V17zm0-3h8v-1.5H8V14zm0-3h5v-1.5H8V11z"/>
                                    </svg>
                                    @else
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="#0071AA">
                                        <path d="M8 5v14l11-7L8 5z"/>
                                    </svg>
                                    @endif
                                </div>
                                <div style="display:flex;flex-direction:column;align-items:flex-end;gap:4px">
                                    <span style="font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:999px" class="type-badge-{{ $file->type }}">
                                        {{ $isPdf ? 'PDF' : 'فيديو' }}
                                    </span>
                                    @if($fileUrl)
                                    <span style="font-size:0.68rem;color:#22c55e;display:flex;align-items:center;gap:3px">
                                        <span style="width:6px;height:6px;background:#22c55e;border-radius:50%;display:inline-block"></span>
                                        متاح
                                    </span>
                                    @else
                                    <span style="font-size:0.68rem;color:#9ca3af">لا يوجد</span>
                                    @endif
                                </div>
                            </div>

                            <h4 style="font-size:0.9rem;font-weight:700;color:#111827;margin:0 0 4px;line-height:1.3;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical" class="dark:text-white">
                                {{ $file->title ?? $session->title }}
                            </h4>

                            <p style="font-size:0.75rem;color:#6b7280;margin:0 0 12px">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:middle">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                {{ $session->title }}
                            </p>

                            <div style="display:flex;align-items:center;justify-content:space-between;padding-top:10px;border-top:1px solid #f1f5f9" class="dark:border-white/10">
                                <span style="font-size:0.72rem;color:#9ca3af">
                                    @if($isVideo && $file->getDurationFormatted())
                                        {{ $file->getDurationFormatted() }}
                                    @elseif($file->getFormattedSize() !== 'Unknown')
                                        {{ $file->getFormattedSize() }}
                                    @else
                                        &mdash;
                                    @endif
                                </span>
                                @if($fileUrl)
                                <span style="font-size:0.75rem;font-weight:600;color:#0071AA;display:flex;align-items:center;gap:4px">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    معاينة
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
            @endif
        </div>
    </div>
    @endif

    {{-- Preview Modal --}}
    <div x-show="preview.open" class="preview-modal" x-cloak>
        <div class="preview-backdrop" @click="closePreview()"></div>
        <div class="preview-container">
            <div class="preview-header">
                <div style="width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0"
                     :style="preview.type === 'pdf' ? 'background:#fee2e2' : 'background:#e0f2fe'">
                    <template x-if="preview.type === 'pdf'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#dc2626">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5z"/>
                        </svg>
                    </template>
                    <template x-if="preview.type === 'video'">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#0071AA">
                            <path d="M8 5v14l11-7L8 5z"/>
                        </svg>
                    </template>
                </div>
                <div style="flex:1;min-width:0">
                    <h3 x-text="preview.title" style="font-size:0.95rem;font-weight:700;color:#fff;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"></h3>
                    <p x-text="preview.session" style="font-size:0.75rem;color:rgba(255,255,255,0.5);margin:2px 0 0"></p>
                </div>
                <div style="display:flex;align-items:center;gap:8px">
                    <a :href="preview.url" target="_blank" rel="noopener"
                       style="display:inline-flex;align-items:center;gap:6px;padding:6px 12px;border-radius:8px;background:rgba(255,255,255,0.1);color:#fff;font-size:0.78rem;font-weight:600;text-decoration:none;transition:background 0.15s"
                       onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        فتح
                    </a>
                    <button @click="closePreview()"
                            style="width:32px;height:32px;border-radius:8px;background:rgba(255,255,255,0.1);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff"
                            onmouseover="this.style.background='rgba(255,255,255,0.2)'" onmouseout="this.style.background='rgba(255,255,255,0.1)'">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="preview-body">
                <template x-if="preview.open && preview.type === 'pdf'">
                    <iframe :src="preview.url + '#toolbar=1&navpanes=0'" style="width:100%;min-height:520px;border:none"></iframe>
                </template>
                <template x-if="preview.open && preview.type === 'video' && isLocalVideo()">
                    <video :src="preview.url" controls autoplay style="width:100%;min-height:420px;background:#000;display:block"></video>
                </template>
                <template x-if="preview.open && preview.type === 'video' && isYouTube()">
                    <div style="position:relative;padding-bottom:56.25%;height:0;min-height:420px">
                        <iframe :src="'https://www.youtube.com/embed/' + getYouTubeId(preview.url) + '?autoplay=1'"
                                allow="autoplay;fullscreen" allowfullscreen
                                style="position:absolute;top:0;left:0;width:100%;height:100%;border:none"></iframe>
                    </div>
                </template>
                <template x-if="preview.open && preview.type === 'video' && !isLocalVideo() && !isYouTube()">
                    <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;min-height:300px;gap:16px;padding:40px">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.3)" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                        <p style="color:rgba(255,255,255,0.5);font-size:0.875rem;text-align:center;margin:0">
                            لا يمكن عرض هذا الفيديو مباشرة.<br>
                            <a :href="preview.url" target="_blank" style="color:#38bdf8">افتح في تبويب جديد</a>
                        </p>
                    </div>
                </template>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function filesApp() {
    return {
        preview: { open: false, title: '', type: '', url: '', session: '' },

        init() {
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') this.closePreview();
            });
        },

        openPreview(data) {
            this.preview = { open: true, ...data };
            document.body.style.overflow = 'hidden';
        },

        closePreview() {
            this.preview.open = false;
            document.body.style.overflow = '';
        },

        isLocalVideo() {
            return this.preview.url && !this.isYouTube() && !this.isVimeo();
        },

        isYouTube() {
            return this.preview.url && (this.preview.url.includes('youtube.com') || this.preview.url.includes('youtu.be'));
        },

        isVimeo() {
            return this.preview.url && this.preview.url.includes('vimeo.com');
        },

        getYouTubeId(url) {
            if (!url) return '';
            const match = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([^&\s?]+)/);
            return match ? match[1] : '';
        }
    };
}
</script>
@endpush
