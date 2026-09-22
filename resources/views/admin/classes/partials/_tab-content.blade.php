<div id="ctab-files" style="display:none;" class="space-y-5">
    <section class="bg-white dark:bg-gray-800 border rounded-xl p-5">
        <h2 class="font-bold mb-4">ملفات المواد والبرنامج</h2>
        @forelse($subjectFiles as $file)
            <div class="border-b py-4 flex flex-wrap items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold break-words">{{ $file->title }}</p>
                    <p class="text-sm text-gray-500">{{ $file->subject?->name ?? 'ملف مشترك للبرنامج' }} · {{ $file->getFormattedSize() }} · {{ $file->created_at?->format('Y/m/d') }}</p>
                    @if($file->description)<p class="text-sm mt-2 whitespace-pre-wrap">{{ $file->description }}</p>@endif
                </div>
                <a href="{{ $file->getUrl() }}" target="_blank" rel="noopener noreferrer" class="text-blue-700 underline">فتح الملف</a>
            </div>
        @empty
            <p class="text-gray-500">لا توجد ملفات مواد أو برنامج مرتبطة بهذا الفصل.</p>
        @endforelse
        <div class="mt-4">{{ $subjectFiles->links() }}</div>
    </section>
    <section class="bg-white dark:bg-gray-800 border rounded-xl p-5">
        <h2 class="font-bold mb-4">ملفات الجلسات والتسجيلات</h2>
        @forelse($sessionFiles as $file)
            <div class="border-b py-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-semibold break-words">{{ $file->title }}</p>
                    <p class="text-sm text-gray-500">{{ $file->session?->title }} · {{ $file->session?->subject?->name ?? 'جلسة البرنامج' }}</p>
                </div>
                @php $fileUrl = $file->getFileUrl(); @endphp
                @if($fileUrl && (str_starts_with($fileUrl, '/') || in_array(strtolower(parse_url($fileUrl, PHP_URL_SCHEME) ?? ''), ['http', 'https'])))
                    <a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="text-blue-700 underline">{{ $file->isVideo() ? 'عرض التسجيل' : 'فتح الملف' }}</a>
                @else
                    <span class="text-gray-500 text-sm">الملف غير متاح</span>
                @endif
            </div>
        @empty
            <p class="text-gray-500">لا توجد ملفات جلسات مرتبطة بهذا الفصل.</p>
        @endforelse
        <div class="mt-4">{{ $sessionFiles->links() }}</div>
    </section>
</div>
<div id="ctab-homeworks" style="display:none;" class="bg-white dark:bg-gray-800 border rounded-xl p-5">
    <h2 class="font-bold mb-4">الواجبات المرتبطة بالفصل</h2>
    @forelse($homeworks as $homework)
        <article class="border-b py-4">
            <h3 class="font-semibold">{{ $homework->title }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $homework->subject?->name ?? $homework->session?->title ?? 'واجب البرنامج' }} · {{ $homework->class_id ? 'خاص بالفصل' : 'مشترك ضمن المحتوى المرتبط' }}</p>
            <p class="text-sm mt-2">موعد التسليم: {{ $homework->due_date?->format('Y/m/d') ?? 'غير محدد' }}</p>
            @if($homework->description)<p class="mt-2 whitespace-pre-wrap break-words">{{ $homework->description }}</p>@endif
            @if($homework->file_url)
                <a href="{{ $homework->file_url }}" target="_blank" rel="noopener noreferrer" class="inline-block mt-3 text-blue-700 underline">{{ $homework->file_name ?: 'فتح مرفق الواجب' }}</a>
            @endif
        </article>
    @empty
        <p class="text-gray-500">لا توجد واجبات مرتبطة بهذا الفصل.</p>
    @endforelse
    <div class="mt-4">{{ $homeworks->links() }}</div>
</div>
