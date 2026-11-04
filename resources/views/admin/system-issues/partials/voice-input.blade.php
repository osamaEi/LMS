{{-- Voice note input: record in the browser or upload an audio file. Uses field name "voice". --}}
<div class="si-voice">
    <input type="file" name="voice" accept="audio/*" hidden>
    <button type="button" class="si-btn si-btn-sm si-rec-btn" data-rec>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 14a3 3 0 003-3V5a3 3 0 00-6 0v6a3 3 0 003 3zm5-3a5 5 0 01-10 0H5a7 7 0 006 6.92V21h2v-3.08A7 7 0 0019 11h-2z"/></svg>
        <span>تسجيل صوتي</span>
    </button>
    <span class="si-rec-time"></span>
    <button type="button" class="si-btn si-btn-sm si-btn-light" data-upload>رفع ملف صوتي</button>
    <audio controls hidden></audio>
    <button type="button" class="si-btn si-btn-sm si-btn-danger" data-clear hidden>حذف الصوت</button>
</div>
