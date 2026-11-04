{{-- Shared styles + scripts for the system-issues pages: voice recorder and tag picker. --}}
@push('styles')
<style>
    .si-wrap { direction: rtl; max-width: 1100px; margin: 0 auto; padding: 1.5rem 1rem; }
    .si-card { background: #fff; border: 1.5px solid #f1f5f9; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.04); }
    .dark .si-card { background: #1e293b; border-color: rgba(255,255,255,.08); }
    .si-card-h { padding: 14px 18px; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; }
    .dark .si-card-h { border-color: rgba(255,255,255,.08); }
    .si-card-h h2 { font-size: .95rem; font-weight: 800; color: #111827; margin: 0; }
    .dark .si-card-h h2, .dark .si-title { color: #fff; }
    .si-card-b { padding: 16px 18px; }
    .si-label { display: block; font-size: .8rem; font-weight: 700; color: #374151; margin-bottom: 6px; }
    .dark .si-label { color: #cbd5e1; }
    .si-input, .si-select, .si-textarea {
        width: 100%; padding: 9px 12px; border: 1.5px solid #e5e7eb; border-radius: 10px;
        font-size: .875rem; background: #fff; color: #111827; outline: none; font-family: inherit;
    }
    .si-textarea { min-height: 110px; resize: vertical; line-height: 1.7; }
    .si-input:focus, .si-select:focus, .si-textarea:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
    .dark .si-input, .dark .si-select, .dark .si-textarea { background: #0f172a; border-color: rgba(255,255,255,.12); color: #fff; }
    .si-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 6px;
        padding: 9px 18px; border-radius: 10px; border: none; cursor: pointer;
        font-size: .82rem; font-weight: 700; text-decoration: none; white-space: nowrap; font-family: inherit;
    }
    .si-btn-primary { background: linear-gradient(135deg,#6366f1,#4f46e5); color: #fff; }
    .si-btn-light { background: #f1f5f9; color: #334155; }
    .si-btn-danger { background: #fee2e2; color: #dc2626; }
    .si-btn-sm { padding: 6px 11px; font-size: .75rem; border-radius: 8px; }
    .si-chip { display: inline-flex; align-items: center; gap: 4px; padding: 3px 10px; border-radius: 999px; font-size: .72rem; font-weight: 700; white-space: nowrap; }
    .si-muted { color: #94a3b8; font-size: .75rem; }

    /* Tags */
    .si-tag {
        display: inline-flex; align-items: center; gap: 6px; padding: 4px 6px 4px 10px;
        border-radius: 999px; font-size: .78rem; font-weight: 700; text-decoration: none;
        border: 1.5px solid currentColor; background: #fff; max-width: 100%;
    }
    .dark .si-tag { background: transparent; }
    .si-tag .si-tag-type { font-size: .66rem; font-weight: 800; padding: 1px 7px; border-radius: 999px; color: #fff; }
    .si-tag .si-tag-label { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 220px; }
    a.si-tag:hover { filter: brightness(.9); text-decoration: underline; }
    .si-tag-x { border: none; background: none; cursor: pointer; color: inherit; opacity: .6; font-size: 1rem; line-height: 1; padding: 0 2px; }
    .si-tag-x:hover { opacity: 1; }
    .si-picker { position: relative; }
    .si-picker-row { display: flex; gap: 8px; flex-wrap: wrap; }
    .si-picker-row .si-select { width: auto; min-width: 120px; }
    .si-picker-row .si-input { flex: 1; min-width: 180px; }
    .si-results {
        position: absolute; z-index: 40; top: 100%; margin-top: 4px; right: 0; left: 0;
        background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 12px 32px rgba(15,23,42,.15);
        max-height: 280px; overflow-y: auto; display: none; padding: 4px;
    }
    .dark .si-results { background: #1e293b; border-color: rgba(255,255,255,.1); }
    .si-results.open { display: block; }
    .si-result { display: block; width: 100%; text-align: right; padding: 8px 10px; border-radius: 8px; border: none; background: none; cursor: pointer; font-family: inherit; }
    .si-result:hover, .si-result.active { background: #f1f5f9; }
    .dark .si-result:hover, .dark .si-result.active { background: rgba(255,255,255,.06); }
    .si-result b { display: block; font-size: .84rem; color: #111827; }
    .dark .si-result b { color: #fff; }
    .si-result small { font-size: .72rem; color: #94a3b8; }

    /* Voice */
    .si-voice { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
    .si-rec-btn { background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; }
    .si-rec-btn.recording { background: #dc2626; color: #fff; border-color: #dc2626; animation: si-pulse 1.2s infinite; }
    @keyframes si-pulse { 50% { box-shadow: 0 0 0 6px rgba(220,38,38,.18); } }
    .si-rec-time { font-variant-numeric: tabular-nums; font-size: .8rem; font-weight: 700; color: #dc2626; min-width: 42px; }
    .si-voice audio, .si-audio { height: 36px; max-width: 100%; }

    /* Attachments */
    .si-drop {
        display: flex; align-items: center; gap: 10px; padding: 12px 14px; cursor: pointer;
        border: 1.5px dashed #c7d2fe; border-radius: 12px; background: #f8faff; color: #4f46e5; font-size: .8rem;
        transition: background .15s, border-color .15s;
    }
    .si-drop:hover, .si-drop.over { background: #eef2ff; border-color: #6366f1; }
    .dark .si-drop { background: rgba(99,102,241,.08); border-color: rgba(99,102,241,.4); }
    .si-drop span { color: #475569; } .si-drop b { color: #4f46e5; }
    .dark .si-drop span { color: #cbd5e1; }
    .si-file-list { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 8px; }
    .si-file-list:empty { display: none; }
    .si-pick {
        display: inline-flex; align-items: center; gap: 6px; padding: 4px 6px 4px 10px; max-width: 260px;
        background: #f1f5f9; border-radius: 10px; font-size: .75rem; color: #334155;
    }
    .si-pick img { width: 28px; height: 28px; object-fit: cover; border-radius: 6px; }
    .si-pick .n { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 600; }
    .si-media { width: 180px; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; background: #0f172a; }
    .si-media img, .si-media video { display: block; width: 100%; height: 130px; object-fit: cover; background: #0f172a; }
    .si-media video { object-fit: contain; }
    .si-media-bar { display: flex; align-items: center; gap: 4px; padding: 4px 8px; background: #fff; }
    .dark .si-media-bar { background: #1e293b; }
    .si-media-name { flex: 1; min-width: 0; font-size: .7rem; color: #475569; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .si-media-act { border: none; background: none; cursor: pointer; font-size: .8rem; color: #4f46e5; text-decoration: none; padding: 0 3px; }
    .si-file-row { display: flex; align-items: center; gap: 8px; padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 10px; background: #fff; max-width: 520px; }
    .dark .si-file-row { background: rgba(255,255,255,.03); border-color: rgba(255,255,255,.1); }
    .si-file-name { flex: 1; min-width: 0; font-size: .8rem; font-weight: 700; color: #4f46e5; text-decoration: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .si-file-name:hover { text-decoration: underline; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    // ── Voice recorder ────────────────────────────────────────────────
    // Each .si-voice block wraps a real <input type=file>. A recording is put
    // into that input (DataTransfer), so the form submits it like an upload.
    function pickMime() {
        const types = ['audio/webm;codecs=opus', 'audio/webm', 'audio/ogg;codecs=opus', 'audio/mp4'];
        return window.MediaRecorder ? (types.find(t => MediaRecorder.isTypeSupported(t)) || '') : '';
    }
    function extFor(mime) {
        return mime.includes('ogg') ? 'ogg' : mime.includes('mp4') ? 'm4a' : 'webm';
    }
    function fmt(s) { return String(Math.floor(s / 60)).padStart(2, '0') + ':' + String(s % 60).padStart(2, '0'); }

    document.querySelectorAll('.si-voice').forEach(function (box) {
        const input   = box.querySelector('input[type=file]');
        const recBtn  = box.querySelector('[data-rec]');
        const upBtn   = box.querySelector('[data-upload]');
        const clrBtn  = box.querySelector('[data-clear]');
        const timeEl  = box.querySelector('.si-rec-time');
        const preview = box.querySelector('audio');
        let recorder = null, chunks = [], timer = null, secs = 0, stream = null;

        function showPreview(file) {
            if (!file) { preview.hidden = true; preview.removeAttribute('src'); clrBtn.hidden = true; return; }
            preview.src = URL.createObjectURL(file);
            preview.hidden = false;
            clrBtn.hidden = false;
        }

        upBtn?.addEventListener('click', () => input.click());
        input.addEventListener('change', () => showPreview(input.files[0]));
        clrBtn?.addEventListener('click', () => { input.value = ''; showPreview(null); });

        if (!recBtn) return;
        if (!navigator.mediaDevices || !window.MediaRecorder) { recBtn.hidden = true; return; }

        recBtn.addEventListener('click', async function () {
            if (recorder && recorder.state === 'recording') { recorder.stop(); return; }
            try {
                stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            } catch (e) {
                alert('لا يمكن الوصول إلى الميكروفون. تأكد من السماح للمتصفح باستخدامه.');
                return;
            }
            const mime = pickMime();
            recorder = new MediaRecorder(stream, mime ? { mimeType: mime } : undefined);
            chunks = [];
            recorder.ondataavailable = e => e.data.size && chunks.push(e.data);
            recorder.onstop = function () {
                clearInterval(timer);
                stream.getTracks().forEach(t => t.stop());
                recBtn.classList.remove('recording');
                recBtn.querySelector('span').textContent = 'تسجيل صوتي';
                timeEl.textContent = '';
                const type = recorder.mimeType || mime || 'audio/webm';
                const file = new File(chunks, 'voice-' + Date.now() + '.' + extFor(type), { type: type.split(';')[0] });
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                showPreview(file);
            };
            recorder.start();
            secs = 0;
            timeEl.textContent = fmt(0);
            timer = setInterval(() => { timeEl.textContent = fmt(++secs); if (secs >= 600) recorder.stop(); }, 1000);
            recBtn.classList.add('recording');
            recBtn.querySelector('span').textContent = 'إيقاف التسجيل';
        });

        // Don't submit while still recording.
        box.closest('form')?.addEventListener('submit', function (e) {
            if (recorder && recorder.state === 'recording') {
                e.preventDefault();
                alert('أوقف التسجيل أولاً.');
            }
        });
    });

    // ── Attachments picker ────────────────────────────────────────────
    // Keeps its own file list so several picks/drops add up; the real input
    // is rebuilt from that list (DataTransfer) before the form submits.
    document.querySelectorAll('.si-files').forEach(function (box) {
        const input = box.querySelector('input[type=file]');
        const drop  = box.querySelector('[data-drop]');
        const list  = box.querySelector('[data-list]');
        const max   = +box.dataset.max, maxBytes = +box.dataset.maxMb * 1024 * 1024;
        let files = [];

        function sync() {
            const dt = new DataTransfer();
            files.forEach(f => dt.items.add(f));
            input.files = dt.files;
            list.innerHTML = '';
            files.forEach(function (f, i) {
                const chip = document.createElement('span');
                chip.className = 'si-pick';
                if (f.type.startsWith('image/')) {
                    const img = document.createElement('img'); img.src = URL.createObjectURL(f); chip.appendChild(img);
                } else {
                    const ic = document.createElement('span'); ic.textContent = f.type.startsWith('video/') ? '🎬' : '📎'; chip.appendChild(ic);
                }
                const n = document.createElement('span'); n.className = 'n'; n.textContent = f.name; n.title = f.name;
                const s = document.createElement('span'); s.style.color = '#94a3b8'; s.textContent = (f.size / 1048576).toFixed(1) + 'MB';
                const x = document.createElement('button'); x.type = 'button'; x.className = 'si-tag-x'; x.textContent = '×'; x.title = 'إزالة';
                x.addEventListener('click', () => { files.splice(i, 1); sync(); });
                chip.append(n, s, x);
                list.appendChild(chip);
            });
        }
        function add(newFiles) {
            const rejected = [];
            Array.from(newFiles).forEach(function (f) {
                if (f.size > maxBytes) { rejected.push(f.name + ' (أكبر من ' + box.dataset.maxMb + 'MB)'); return; }
                if (files.length >= max) { rejected.push(f.name + ' (الحد ' + max + ' ملفات)'); return; }
                if (!files.some(e => e.name === f.name && e.size === f.size)) files.push(f);
            });
            if (rejected.length) alert('لم تتم إضافة:\n' + rejected.join('\n'));
            sync();
        }

        drop.addEventListener('click', () => input.click());
        // The input's own selection replaces its FileList, so merge it into ours.
        input.addEventListener('change', () => add(Array.from(input.files)));
        ['dragenter', 'dragover'].forEach(ev => drop.addEventListener(ev, e => { e.preventDefault(); drop.classList.add('over'); }));
        ['dragleave', 'drop'].forEach(ev => drop.addEventListener(ev, e => { e.preventDefault(); drop.classList.remove('over'); }));
        drop.addEventListener('drop', e => add(e.dataTransfer.files));
    });

    // Show progress text on submit for forms with uploads (big videos take a while).
    document.querySelectorAll('form[enctype="multipart/form-data"]').forEach(function (f) {
        f.addEventListener('submit', function (e) {
            if (e.defaultPrevented) return;
            const btn = f.querySelector('button[type=submit]:not([name])');
            const hasFiles = Array.from(f.querySelectorAll('input[type=file]')).some(i => i.files.length);
            if (btn && hasFiles) { setTimeout(() => { btn.disabled = true; btn.textContent = 'جارٍ الرفع...'; }, 0); }
        });
    });

    // ── Tag picker ────────────────────────────────────────────────────
    // data-mode="inputs": add hidden inputs to the surrounding form (create page).
    // data-mode="submit": post the tag straight away (issue page).
    document.querySelectorAll('.si-picker').forEach(function (picker) {
        const mode     = picker.dataset.mode;
        const searchUrl = picker.dataset.searchUrl;
        const typeSel  = picker.querySelector('[data-tag-type]');
        const qInput   = picker.querySelector('[data-tag-q]');
        const results  = picker.querySelector('.si-results');
        const linkRow  = picker.querySelector('[data-link-row]');
        const list     = document.getElementById(picker.dataset.list || '');
        const types    = JSON.parse(picker.dataset.types);
        let seq = 0, timer = null, items = [], active = -1;

        function syncType() {
            const isLink = typeSel.value === 'link';
            qInput.hidden = isLink;
            linkRow.style.display = isLink ? 'flex' : 'none';
            results.classList.remove('open');
            qInput.placeholder = 'ابحث عن ' + (types[typeSel.value]?.label || '') + ' بالاسم أو الرقم...';
        }
        typeSel.addEventListener('change', () => { syncType(); qInput.focus(); });
        syncType();

        function render() {
            results.innerHTML = '';
            if (!items.length) {
                results.innerHTML = '<div style="padding:10px;font-size:.8rem;color:#94a3b8">لا توجد نتائج</div>';
            }
            items.forEach(function (it, i) {
                const b = document.createElement('button');
                b.type = 'button';
                b.className = 'si-result' + (i === active ? ' active' : '');
                const t = document.createElement('b'); t.textContent = it.label;
                const s = document.createElement('small'); s.textContent = it.sub || ('#' + it.id);
                b.append(t, s);
                b.addEventListener('mousedown', e => { e.preventDefault(); choose(it); });
                results.appendChild(b);
            });
            results.classList.add('open');
        }

        qInput.addEventListener('input', function () {
            clearTimeout(timer);
            const q = qInput.value.trim();
            if (!q) { results.classList.remove('open'); return; }
            timer = setTimeout(async function () {
                const my = ++seq;
                const res = await fetch(searchUrl + '?type=' + encodeURIComponent(typeSel.value) + '&q=' + encodeURIComponent(q), { headers: { 'Accept': 'application/json' } });
                if (my !== seq) return;
                items = res.ok ? await res.json() : [];
                active = items.length ? 0 : -1;
                render();
            }, 250);
        });
        // Enter in the link fields must not submit the surrounding form either.
        linkRow.querySelectorAll('input').forEach(i => i.addEventListener('keydown', e => {
            if (e.key === 'Enter') { e.preventDefault(); picker.querySelector('[data-link-add]').click(); }
        }));
        qInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') e.preventDefault(); // never submit the surrounding form from here
            if (!results.classList.contains('open')) return;
            if (e.key === 'ArrowDown') { e.preventDefault(); active = Math.min(active + 1, items.length - 1); render(); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); active = Math.max(active - 1, 0); render(); }
            else if (e.key === 'Enter') { e.preventDefault(); if (items[active]) choose(items[active]); }
            else if (e.key === 'Escape') { results.classList.remove('open'); }
        });
        qInput.addEventListener('blur', () => setTimeout(() => results.classList.remove('open'), 150));

        picker.querySelector('[data-link-add]').addEventListener('click', function () {
            const url = picker.querySelector('[data-link-url]').value.trim();
            const label = picker.querySelector('[data-link-label]').value.trim();
            if (!url) { alert('أدخل الرابط'); return; }
            if (!/^(https?:\/\/|\/)/i.test(url)) { alert('الرابط يجب أن يبدأ بـ https:// أو /'); return; }
            choose({ id: '', label: label || url, url: url });
            picker.querySelector('[data-link-url]').value = '';
            picker.querySelector('[data-link-label]').value = '';
        });

        function choose(it) {
            const type = typeSel.value;
            results.classList.remove('open');
            qInput.value = '';

            if (mode === 'submit') {
                const f = document.createElement('form');
                f.method = 'POST';
                f.action = picker.dataset.storeUrl;
                const fields = { _token: picker.dataset.csrf, type: type, id: it.id || '', label: it.label || '', url: it.url || '' };
                Object.entries(fields).forEach(([k, v]) => {
                    const i = document.createElement('input'); i.type = 'hidden'; i.name = k; i.value = v; f.appendChild(i);
                });
                document.body.appendChild(f);
                f.submit();
                return;
            }

            // inputs mode: skip duplicates
            const key = type + ':' + (it.id || it.url);
            if (list.querySelector('[data-key="' + CSS.escape(key) + '"]')) return;
            const idx = Date.now() + Math.floor(Math.random() * 1000);
            const chip = document.createElement('span');
            chip.className = 'si-tag';
            chip.dataset.key = key;
            chip.style.color = types[type].color;
            const tt = document.createElement('span'); tt.className = 'si-tag-type'; tt.style.background = types[type].color; tt.textContent = types[type].label;
            const tl = document.createElement('span'); tl.className = 'si-tag-label'; tl.textContent = it.label;
            const x = document.createElement('button'); x.type = 'button'; x.className = 'si-tag-x'; x.textContent = '×'; x.title = 'إزالة';
            x.addEventListener('click', () => chip.remove());
            chip.append(tt, tl, x);
            [['type', type], ['id', it.id || ''], ['label', it.label || ''], ['url', it.url || '']].forEach(([k, v]) => {
                const i = document.createElement('input'); i.type = 'hidden'; i.name = 'tags[' + idx + '][' + k + ']'; i.value = v; chip.appendChild(i);
            });
            list.appendChild(chip);
        }
    });
})();
</script>
@endpush
