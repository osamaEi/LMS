{{-- File/image/video picker. Field name "attachments[]". Selections accumulate; each can be removed before sending. --}}
<div class="si-files" data-max="10" data-max-mb="100">
    <input type="file" name="attachments[]" multiple hidden
           accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.csv,.ppt,.pptx,.txt,.rtf,.odt,.ods,.odp,.zip,.rar,.7z,.heic,.heif,.mkv">
    <div class="si-drop" data-drop>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
        <span><b>إرفاق ملفات</b> — صور، فيديو، PDF، Word، Excel... (اسحب الملفات هنا أو اضغط)</span>
    </div>
    <div class="si-file-list" data-list></div>
</div>
