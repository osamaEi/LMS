@extends('layouts.dashboard')

@section('title', $subject->name)

@push('styles')
<style>
    .subject-header {
        background: linear-gradient(135deg, #0071AA 0%, #005a88 50%, #004266 100%);
        border-radius: 20px;
        padding: 2rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(0, 113, 170, 0.3);
    }
    .subject-header::before {
        content: '';
        position: absolute;
        top: -100%;
        right: -20%;
        width: 80%;
        height: 300%;
        background: radial-gradient(ellipse, rgba(255,255,255,0.1) 0%, transparent 60%);
    }
    .info-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .dark .info-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.1);
    }
    .file-card {
        background: white;
        border-radius: 14px;
        border: 2px solid #f1f5f9;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .dark .file-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.08);
    }
    .file-card:hover {
        border-color: #0071AA;
        box-shadow: 0 6px 24px rgba(0,113,170,0.12);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

    {{-- Subject Header --}}
    <div class="subject-header mb-6">
        <div class="relative z-10">
            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center gap-1 text-white/70 hover:text-white text-sm mb-4 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                العودة للوحة التحكم
            </a>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">{{ $subject->name }}</h1>
                    <p class="text-white/70">{{ $subject->term->program->name ?? '' }} - {{ $subject->term->name ?? '' }}</p>
                    @if($subject->code)
                        <span class="inline-block mt-2 px-3 py-1 bg-white/15 rounded-lg text-white/90 text-sm font-medium">{{ $subject->code }}</span>
                    @endif
                </div>
                <a href="{{ route('student.quizzes.index', $subject->id) }}"
                   class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-bold text-white transition-all hover:scale-105"
                   style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 6px 20px rgba(16,185,129,0.4);">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    الاختبارات
                </a>
            </div>
        </div>
    </div>

    @php
        $allFiles = $sessions->flatMap(fn($s) => $s->files ?? collect());

        // Extension → color + icon helper
        $extMeta = [
            'pdf'  => ['bg' => '#fee2e2', 'icon_bg' => '#ef4444', 'label' => 'PDF'],
            'doc'  => ['bg' => '#dbeafe', 'icon_bg' => '#2563eb', 'label' => 'Word'],
            'docx' => ['bg' => '#dbeafe', 'icon_bg' => '#2563eb', 'label' => 'Word'],
            'xls'  => ['bg' => '#d1fae5', 'icon_bg' => '#059669', 'label' => 'Excel'],
            'xlsx' => ['bg' => '#d1fae5', 'icon_bg' => '#059669', 'label' => 'Excel'],
            'ppt'  => ['bg' => '#ffedd5', 'icon_bg' => '#ea580c', 'label' => 'PowerPoint'],
            'pptx' => ['bg' => '#ffedd5', 'icon_bg' => '#ea580c', 'label' => 'PowerPoint'],
            'mp4'  => ['bg' => '#ede9fe', 'icon_bg' => '#7c3aed', 'label' => 'فيديو'],
            'mp3'  => ['bg' => '#fae8ff', 'icon_bg' => '#a21caf', 'label' => 'صوت'],
            'zip'  => ['bg' => '#fef9c3', 'icon_bg' => '#ca8a04', 'label' => 'ZIP'],
            'rar'  => ['bg' => '#fef9c3', 'icon_bg' => '#ca8a04', 'label' => 'RAR'],
            'jpg'  => ['bg' => '#e0f2fe', 'icon_bg' => '#0284c7', 'label' => 'صورة'],
            'jpeg' => ['bg' => '#e0f2fe', 'icon_bg' => '#0284c7', 'label' => 'صورة'],
            'png'  => ['bg' => '#e0f2fe', 'icon_bg' => '#0284c7', 'label' => 'صورة'],
        ];
        $defaultMeta = ['bg' => '#f1f5f9', 'icon_bg' => '#64748b', 'label' => 'ملف'];

        // Group files by session
        $filesBySession = [];
        foreach ($sessions as $session) {
            if ($session->files && $session->files->count() > 0) {
                $filesBySession[] = ['session' => $session, 'files' => $session->files];
            }
        }
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Files Main Panel --}}
        <div class="lg:col-span-2">

            {{-- Header row --}}
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#0071AA,#005a88);display:flex;align-items:center;justify-content:center">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">ملفات المادة</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $allFiles->count() }} ملف في {{ count($filesBySession) }} جلسة</p>
                    </div>
                </div>

                {{-- Search --}}
                <div style="position:relative">
                    <input type="text" id="fileSearch" oninput="filterFiles()" placeholder="ابحث في الملفات..."
                           style="border:1.5px solid #e2e8f0;border-radius:10px;padding:8px 36px 8px 12px;font-size:0.875rem;outline:none;width:220px;color:#374151"
                           onfocus="this.style.borderColor='#0071AA'" onblur="this.style.borderColor='#e2e8f0'">
                    <svg style="position:absolute;right:10px;top:50%;transform:translateY(-50%);color:#9ca3af" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="m21 21-4.35-4.35"/>
                    </svg>
                </div>
            </div>

            @if($allFiles->count() === 0)
                {{-- Empty state --}}
                <div class="info-card" style="padding:4rem 2rem;text-align:center">
                    <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#eff6ff,#dbeafe);display:flex;align-items:center;justify-content:center;margin:0 auto 1.25rem">
                        <svg width="38" height="38" viewBox="0 0 24 24" fill="none" stroke="#93c5fd" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </div>
                    <p style="font-size:1rem;font-weight:700;color:#374151;margin:0">لا توجد ملفات بعد</p>
                    <p style="font-size:0.85rem;color:#9ca3af;margin:6px 0 0">سيتم رفع الملفات هنا من قِبَل المعلم</p>
                </div>

            @else
                {{-- Files grouped by session --}}
                <div id="filesList" class="space-y-5">
                    @foreach($filesBySession as $group)
                    @php $session = $group['session']; @endphp
                    <div class="file-group" data-session="{{ $session->title }}">
                        {{-- Session label --}}
                        <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                            <div style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:0.8rem;flex-shrink:0;
                                background:{{ $session->ended_at ? 'linear-gradient(135deg,#10b981,#059669)' : ($session->started_at ? 'linear-gradient(135deg,#ef4444,#dc2626)' : 'linear-gradient(135deg,#3b82f6,#2563eb)') }}">
                                {{ $session->session_number }}
                            </div>
                            <span style="font-size:0.95rem;font-weight:700;color:#374151" class="dark:text-white">
                                {{ $session->title }}
                            </span>
                            <span style="font-size:0.78rem;color:#6b7280;background:#f1f5f9;padding:2px 10px;border-radius:999px">
                                {{ $group['files']->count() }} {{ $group['files']->count() == 1 ? 'ملف' : 'ملفات' }}
                            </span>
                        </div>

                        {{-- File cards --}}
                        <div class="space-y-2 file-items">
                            @foreach($group['files'] as $file)
                            @php
                                $ext = strtolower(pathinfo($file->file_path ?? $file->title ?? '', PATHINFO_EXTENSION));
                                $meta = $extMeta[$ext] ?? $defaultMeta;
                                $sizeKb = $file->file_size ? number_format($file->file_size / 1024, 0) : null;
                                $fileName = $file->title ?: basename($file->file_path ?? 'ملف');
                            @endphp
                            <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                               class="file-card file-item-card" data-name="{{ strtolower($fileName) }}">

                                {{-- Icon --}}
                                <div style="width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:{{ $meta['bg'] }}">
                                    @if(in_array($ext, ['mp4', 'avi', 'mov', 'mkv']))
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ $meta['icon_bg'] }}">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                    @elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ $meta['icon_bg'] }}">
                                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                    </svg>
                                    @elseif(in_array($ext, ['mp3', 'wav', 'ogg']))
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ $meta['icon_bg'] }}">
                                        <path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/>
                                    </svg>
                                    @else
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="{{ $meta['icon_bg'] }}">
                                        <path d="M14 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                                    </svg>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div style="flex:1;min-width:0">
                                    <div style="font-size:0.95rem;font-weight:700;color:#111827;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" class="dark:text-white">
                                        {{ $fileName }}
                                    </div>
                                    <div style="display:flex;align-items:center;gap:8px;margin-top:3px">
                                        <span style="font-size:0.75rem;font-weight:600;padding:1px 8px;border-radius:999px;background:{{ $meta['bg'] }};color:{{ $meta['icon_bg'] }}">
                                            {{ strtoupper($ext) ?: $meta['label'] }}
                                        </span>
                                        @if($sizeKb)
                                        <span style="font-size:0.75rem;color:#9ca3af">{{ $sizeKb }} KB</span>
                                        @endif
                                        @if($file->created_at)
                                        <span style="font-size:0.75rem;color:#9ca3af">{{ \Carbon\Carbon::parse($file->created_at)->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Download icon --}}
                                <div style="flex-shrink:0;width:36px;height:36px;border-radius:10px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;transition:all .2s"
                                     onmouseover="this.style.background='#0071AA';this.style.color='#fff'"
                                     onmouseout="this.style.background='#f1f5f9';this.style.color='#6b7280'">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                    </svg>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- No results --}}
                <div id="noResults" style="display:none;text-align:center;padding:3rem 1rem">
                    <p style="color:#9ca3af;font-size:0.95rem">لا توجد ملفات تطابق بحثك</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">

            {{-- Stats --}}
            <div class="info-card">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-bold text-gray-900 dark:text-white">إحصائيات الملفات</h2>
                </div>
                <div class="p-5 space-y-3">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#eff6ff;border-radius:12px">
                        <span style="font-size:0.85rem;color:#2563eb;font-weight:600">إجمالي الملفات</span>
                        <span style="font-size:1.2rem;font-weight:800;color:#1d4ed8">{{ $allFiles->count() }}</span>
                    </div>
                    @php
                        $pdfCount    = $allFiles->filter(fn($f) => strtolower(pathinfo($f->file_path ?? '', PATHINFO_EXTENSION)) === 'pdf')->count();
                        $videoCount  = $allFiles->filter(fn($f) => in_array(strtolower(pathinfo($f->file_path ?? '', PATHINFO_EXTENSION)), ['mp4','avi','mov','mkv']))->count();
                        $docCount    = $allFiles->filter(fn($f) => in_array(strtolower(pathinfo($f->file_path ?? '', PATHINFO_EXTENSION)), ['doc','docx','ppt','pptx','xls','xlsx']))->count();
                        $otherCount  = $allFiles->count() - $pdfCount - $videoCount - $docCount;
                    @endphp
                    @if($pdfCount > 0)
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:10px;height:10px;border-radius:50%;background:#ef4444"></div>
                            <span style="font-size:0.85rem;color:#6b7280">PDF</span>
                        </div>
                        <span style="font-weight:700;color:#374151">{{ $pdfCount }}</span>
                    </div>
                    @endif
                    @if($videoCount > 0)
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:10px;height:10px;border-radius:50%;background:#7c3aed"></div>
                            <span style="font-size:0.85rem;color:#6b7280">فيديو</span>
                        </div>
                        <span style="font-weight:700;color:#374151">{{ $videoCount }}</span>
                    </div>
                    @endif
                    @if($docCount > 0)
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:10px;height:10px;border-radius:50%;background:#2563eb"></div>
                            <span style="font-size:0.85rem;color:#6b7280">مستندات</span>
                        </div>
                        <span style="font-weight:700;color:#374151">{{ $docCount }}</span>
                    </div>
                    @endif
                    @if($otherCount > 0)
                    <div style="display:flex;align-items:center;justify-content:space-between">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:10px;height:10px;border-radius:50%;background:#9ca3af"></div>
                            <span style="font-size:0.85rem;color:#6b7280">أخرى</span>
                        </div>
                        <span style="font-weight:700;color:#374151">{{ $otherCount }}</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Teacher Info --}}
            <div class="info-card">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-bold text-gray-900 dark:text-white">المعلم</h2>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg"
                             style="background: linear-gradient(135deg, #0071AA, #005a88);">
                            {{ mb_substr($subject->teacher->name ?? 'غ', 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 dark:text-white">{{ $subject->teacher->name ?? 'غير محدد' }}</div>
                            @if($subject->teacher)
                            <div class="text-xs text-gray-500">{{ $subject->teacher->email }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Subject Info --}}
            <div class="info-card">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="font-bold text-gray-900 dark:text-white">معلومات المادة</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">البرنامج</div>
                        <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $subject->term->program->name ?? 'غير محدد' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">الفصل الدراسي</div>
                        <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $subject->term->name ?? 'غير محدد' }}</div>
                    </div>
                    @if($subject->credits)
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">الساعات المعتمدة</div>
                        <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $subject->credits }}</div>
                    </div>
                    @endif
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">عدد الجلسات</div>
                        <div class="font-medium text-gray-900 dark:text-white text-sm">{{ $sessions->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterFiles() {
    const q = document.getElementById('fileSearch').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.file-item-card');
    const groups = document.querySelectorAll('.file-group');
    let totalVisible = 0;

    groups.forEach(group => {
        let groupVisible = 0;
        group.querySelectorAll('.file-item-card').forEach(card => {
            const name = card.dataset.name || '';
            const match = !q || name.includes(q);
            card.style.display = match ? '' : 'none';
            if (match) groupVisible++;
        });
        group.style.display = groupVisible > 0 ? '' : 'none';
        totalVisible += groupVisible;
    });

    document.getElementById('noResults').style.display = totalVisible === 0 ? '' : 'none';
    const list = document.getElementById('filesList');
    if (list) list.style.display = totalVisible === 0 ? 'none' : '';
}
</script>
@endpush
