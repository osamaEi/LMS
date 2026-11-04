{{-- Rich-text description editor (Quill). Params: $name, $value (stored description), $placeholder --}}
<div class="si-editor" data-si-editor data-placeholder="{{ $placeholder ?? '' }}">
    <div data-si-editor-body>{!! \App\Models\SystemIssue::renderDescription($value ?? null) !!}</div>
    <input type="hidden" name="{{ $name ?? 'description' }}" data-si-editor-input>
</div>

@once
@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .si-editor .ql-toolbar.ql-snow { border: 1.5px solid #e5e7eb; border-radius: 10px 10px 0 0; background: #f8fafc; direction: ltr; }
    .si-editor .ql-container.ql-snow { border: 1.5px solid #e5e7eb; border-top: none; border-radius: 0 0 10px 10px; font-family: inherit; font-size: .9rem; background: #fff; }
    .si-editor .ql-editor { min-height: 160px; direction: rtl; text-align: right; line-height: 1.8; color: #111827; }
    .si-editor .ql-editor.ql-blank::before { right: 15px; left: 15px; color: #94a3b8; font-style: normal; }
    .si-editor .ql-editor ol, .si-editor .ql-editor ul { padding-right: 1.5em; padding-left: 0; }
    .si-editor .ql-editor li:not(.ql-direction-rtl) { padding-right: 1.5em; padding-left: 0; }
    .si-editor .ql-editor li::before { margin-left: .3em; margin-right: -1.5em; text-align: left; }
    .si-editor .ql-snow .ql-tooltip { direction: ltr; z-index: 50; }
    .dark .si-editor .ql-toolbar.ql-snow { background: #0f172a; border-color: rgba(255,255,255,.12); }
    .dark .si-editor .ql-container.ql-snow { background: #0f172a; border-color: rgba(255,255,255,.12); }
    .dark .si-editor .ql-editor { color: #fff; }
    .dark .si-editor .ql-snow .ql-stroke { stroke: #cbd5e1; }
    .dark .si-editor .ql-snow .ql-fill { fill: #cbd5e1; }
    .dark .si-editor .ql-snow .ql-picker { color: #cbd5e1; }

    /* Rendered description */
    .si-rich { font-size: .9rem; color: #1f2937; line-height: 1.8; word-break: break-word; }
    .dark .si-rich { color: #e2e8f0; }
    .si-rich p { margin: 0 0 .4em; }
    .si-rich h2 { font-size: 1.15rem; font-weight: 800; margin: .6em 0 .3em; }
    .si-rich h3 { font-size: 1rem; font-weight: 800; margin: .6em 0 .3em; }
    .si-rich ul { list-style: disc; padding-right: 1.5em; margin: 0 0 .5em; }
    .si-rich ol { list-style: decimal; padding-right: 1.5em; margin: 0 0 .5em; }
    .si-rich a { color: #4f46e5; text-decoration: underline; }
    .si-rich blockquote { border-right: 4px solid #c7d2fe; padding: 2px 12px; margin: 0 0 .5em; color: #475569; }
    .si-rich pre, .si-rich .ql-syntax { direction: ltr; text-align: left; background: #0f172a; color: #e2e8f0; padding: 10px 12px; border-radius: 8px; overflow-x: auto; font-size: .8rem; white-space: pre-wrap; }
    .si-rich code { background: #f1f5f9; padding: 1px 5px; border-radius: 4px; font-size: .85em; }
    .si-rich .ql-align-center { text-align: center; } .si-rich .ql-align-left { text-align: left; } .si-rich .ql-align-justify { text-align: justify; }
    .si-rich .ql-direction-rtl { direction: rtl; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
(function () {
    const toolbar = [
        [{ header: [2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        [{ align: [] }],
        ['blockquote', 'code-block', 'link'],
        ['clean'],
    ];

    document.querySelectorAll('[data-si-editor]').forEach(function (box) {
        const body  = box.querySelector('[data-si-editor-body]');
        const input = box.querySelector('[data-si-editor-input]');
        const quill = new Quill(body, {
            theme: 'snow',
            placeholder: box.dataset.placeholder || '',
            modules: { toolbar: toolbar },
        });
        quill.root.setAttribute('dir', 'rtl');

        function sync() {
            input.value = quill.getText().trim() === '' ? '' : quill.root.innerHTML;
        }
        sync();
        quill.on('text-change', sync);
        box.closest('form')?.addEventListener('submit', sync);
    });
})();
</script>
@endpush
@endonce
