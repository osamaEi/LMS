{{-- Rich-text editor (Quill). Params: $name, $value (stored HTML or plain text), $placeholder, $dir ('rtl'|'ltr') --}}
@php($rxDir = ($dir ?? 'rtl') === 'ltr' ? 'ltr' : 'rtl')
<div class="rx-editor rx-{{ $rxDir }}" data-rx-editor data-dir="{{ $rxDir }}" data-placeholder="{{ $placeholder ?? '' }}">
    <div data-rx-editor-body>{!! \App\Support\RichText::render($value ?? null) !!}</div>
    <input type="hidden" name="{{ $name }}" data-rx-editor-input>
</div>

@once
@push('styles')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
    .rx-editor .ql-toolbar.ql-snow { border: 1px solid #d1d5db; border-radius: 8px 8px 0 0; background: #f8fafc; direction: ltr; }
    .rx-editor .ql-container.ql-snow { border: 1px solid #d1d5db; border-top: none; border-radius: 0 0 8px 8px; font-family: inherit; font-size: .9rem; background: #fff; }
    .rx-editor .ql-editor { min-height: 170px; line-height: 1.8; color: #111827; }
    .rx-editor.rx-rtl .ql-editor { direction: rtl; text-align: right; }
    .rx-editor.rx-rtl .ql-editor.ql-blank::before { right: 15px; left: 15px; }
    .rx-editor .ql-editor.ql-blank::before { color: #94a3b8; font-style: normal; }
    .rx-editor.rx-rtl .ql-editor ol, .rx-editor.rx-rtl .ql-editor ul { padding-right: 1.5em; padding-left: 0; }
    .rx-editor.rx-rtl .ql-editor li:not(.ql-direction-rtl) { padding-right: 1.5em; padding-left: 0; }
    .rx-editor.rx-rtl .ql-editor li::before { margin-left: .3em; margin-right: -1.5em; text-align: left; }
    .rx-editor .ql-snow .ql-tooltip { direction: ltr; z-index: 50; }
    .dark .rx-editor .ql-toolbar.ql-snow { background: #111827; border-color: #374151; }
    .dark .rx-editor .ql-container.ql-snow { background: #1f2937; border-color: #374151; }
    .dark .rx-editor .ql-editor { color: #fff; }
    .dark .rx-editor .ql-snow .ql-stroke { stroke: #cbd5e1; }
    .dark .rx-editor .ql-snow .ql-fill { fill: #cbd5e1; }
    .dark .rx-editor .ql-snow .ql-picker { color: #cbd5e1; }
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
        ['blockquote', 'link'],
        ['clean'],
    ];

    document.querySelectorAll('[data-rx-editor]').forEach(function (box) {
        const body  = box.querySelector('[data-rx-editor-body]');
        const input = box.querySelector('[data-rx-editor-input]');
        const quill = new Quill(body, {
            theme: 'snow',
            placeholder: box.dataset.placeholder || '',
            modules: { toolbar: toolbar },
        });
        quill.root.setAttribute('dir', box.dataset.dir || 'rtl');

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
