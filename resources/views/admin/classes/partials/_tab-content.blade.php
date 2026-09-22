<style>
.class-resources { --cr-text:#1e293b; --cr-muted:#64748b; --cr-line:#e2e8f0; --cr-bg:#fff; --cr-soft:#f8fafc; color:var(--cr-text); }
.dark .class-resources { --cr-text:#f1f5f9; --cr-muted:#cbd5e1; --cr-line:#334155; --cr-bg:#1e293b; --cr-soft:#0f172a; }
.class-resources section, .class-resources.cr-homework { background:var(--cr-bg); border:1px solid var(--cr-line); border-radius:18px; padding:24px; }
.class-resources h2 { font-size:15px; font-weight:800; display:flex; align-items:center; gap:10px; margin-bottom:22px; }
.class-resources h2::before { content:''; width:5px; height:22px; border-radius:4px; background:#0071aa; }
.cr-file { border:1px solid var(--cr-line); border-radius:12px; background:var(--cr-soft); padding:18px; margin-top:12px; display:flex; align-items:center; gap:16px; transition:border-color .2s; }
.cr-file:hover, .cr-assignment:hover { border-color:#8cbdd5; }
.cr-icon { display:flex; align-items:center; justify-content:center; width:48px; height:52px; border-radius:12px; background:#e0f2fe; color:#0369a1; font-size:11px; font-weight:800; flex-shrink:0; }
.cr-icon.video { background:#ede9fe; color:#7c3aed; }
.cr-body { flex:1; min-width:0; }
.cr-body p, .cr-assignment h3 { overflow-wrap:anywhere; }
.cr-meta { display:flex; flex-wrap:wrap; align-items:center; gap:8px; color:var(--cr-muted); font-size:12px; margin-top:8px; }
.cr-meta span { border:1px solid var(--cr-line); background:var(--cr-bg); border-radius:6px; padding:3px 8px; }
.cr-action { display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:10px 18px; border-radius:9px; background:#0071aa; color:white; font-weight:700; font-size:12px; text-decoration:none; flex-shrink:0; }
.cr-action:hover { background:#005781; color:white; }
.cr-action:focus-visible { outline:3px solid #38bdf8; outline-offset:3px; }
.cr-action::after { content:'↗'; font-size:16px; }
.cr-empty { border:1px dashed var(--cr-line); background:var(--cr-soft); padding:25px 18px; border-radius:12px; text-align:center; font-size:13px; color:var(--cr-muted); line-height:1.9; }
.cr-assignment { border:1px solid var(--cr-line); border-radius:14px; padding:20px; margin-top:14px; }
.cr-assignment h3 { font-size:15px; font-weight:700; }
.cr-assignment .cr-description { color:var(--cr-muted); font-size:13px; line-height:1.9; padding:12px 0; }
.cr-count { margin-inline-start:auto; border-radius:20px; padding:4px 10px; background:var(--cr-soft); color:var(--cr-muted); font-size:12px; }
@media(max-width:640px) { .class-resources section, .class-resources.cr-homework { padding:16px; } .cr-file { flex-wrap:wrap; padding:14px; gap:12px; } .cr-file .cr-action { width:100%; } .cr-icon { width:40px; height:44px; } }
</style>
<div id="ctab-files" style="display:none;" class="class-resources space-y-5">
    <section class="bg-white dark:bg-gray-800 border rounded-xl p-5">
        <h2>ملفات المواد والبرنامج <span class="cr-count">{{ $subjectFiles->total() }} ملف</span></h2>
        @forelse($subjectFiles as $file)
            <div class="cr-file">
                <span class="cr-icon" aria-hidden="true">{{ strtoupper($file->file_type ?: 'FILE') }}</span>
                <div class="cr-body">
                    <p class="font-semibold break-words">{{ $file->title }}</p>
                    <div class="cr-meta"><span>{{ $file->subject?->name ?? 'ملف مشترك للبرنامج' }}</span>@if($file->getFormattedSize())<span><bdi dir="ltr">{{ $file->getFormattedSize() }}</bdi></span>@endif<span>أُضيف في <bdi>{{ $file->created_at?->format('Y/m/d') }}</bdi></span></div>
                    @if($file->description)<p class="text-sm mt-2 whitespace-pre-wrap">{{ $file->description }}</p>@endif
                </div>
                <a href="{{ $file->getUrl() }}" target="_blank" rel="noopener noreferrer" class="cr-action" aria-label="فتح الملف: {{ $file->title }}">فتح الملف</a>
            </div>
        @empty
            <p class="cr-empty">لا توجد ملفات بعد.<br>ستظهر هنا الملفات المرفوعة للمواد المرتبطة بهذا الفصل.</p>
        @endforelse
        <div class="mt-4">{{ $subjectFiles->links() }}</div>
    </section>
    <section class="bg-white dark:bg-gray-800 border rounded-xl p-5">
        <h2>ملفات الجلسات والتسجيلات <span class="cr-count">{{ $sessionFiles->total() }} مرفق</span></h2>
        @forelse($sessionFiles as $file)
            <div class="cr-file">
                <span class="cr-icon {{ $file->isVideo() ? 'video' : '' }}" aria-hidden="true">{{ $file->isVideo() ? '▶' : 'PDF' }}</span>
                <div class="cr-body">
                    <p class="font-semibold break-words">{{ $file->title }}</p>
                    <p class="text-sm text-gray-500">{{ $file->session?->title }} · {{ $file->session?->subject?->name ?? 'جلسة البرنامج' }}</p>
                </div>
                @php $fileUrl = $file->getFileUrl(); @endphp
                @if($fileUrl && (str_starts_with($fileUrl, '/') || in_array(strtolower(parse_url($fileUrl, PHP_URL_SCHEME) ?? ''), ['http', 'https'])))
                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="cr-action" aria-label="فتح المرفق: {{ $file->title }}">{{ $file->isVideo() ? 'عرض التسجيل' : 'فتح الملف' }}</a>
                @else
                    <span class="text-gray-500 text-sm">الملف غير متاح</span>
                @endif
            </div>
        @empty
            <p class="cr-empty">لا توجد مرفقات للجلسات بعد.<br>ستظهر هنا ملفات الجلسات والتسجيلات عند إضافتها.</p>
        @endforelse
        <div class="mt-4">{{ $sessionFiles->links() }}</div>
    </section>
</div>
<div id="ctab-homeworks" style="display:none;" class="class-resources cr-homework">
    <h2>الواجبات المرتبطة بالفصل <span class="cr-count">{{ $homeworks->total() }} واجب</span></h2>
    @forelse($homeworks as $homework)
        <article class="cr-assignment">
            <h3 class="font-semibold">{{ $homework->title }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $homework->subject?->name ?? $homework->session?->title ?? 'واجب البرنامج' }} · {{ $homework->class_id ? 'خاص بالفصل' : 'مشترك ضمن المحتوى المرتبط' }}</p>
            <div class="cr-meta"><span>موعد التسليم: <bdi>{{ $homework->due_date?->format('Y/m/d') ?? 'غير محدد' }}</bdi></span></div>
            @if($homework->description)<p class="cr-description whitespace-pre-wrap break-words">{{ $homework->description }}</p>@endif
            @if($homework->file_url)
                <a href="{{ $homework->file_url }}" target="_blank" rel="noopener noreferrer" class="cr-action mt-3" title="{{ $homework->file_name }}">فتح مرفق الواجب</a>
            @endif
        </article>
    @empty
        <p class="cr-empty">لم تُضف واجبات بعد.<br>ستظهر هنا الواجبات المرتبطة بالفصل ومواده.</p>
    @endforelse
    <div class="mt-4">{{ $homeworks->links() }}</div>
</div>
