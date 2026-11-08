{{-- Save / cancel bar for an assign form. Styles in show.blade.php (.tp-*). --}}
<div class="tp-footer">
    <button type="submit" class="tp-btn tp-btn-primary" :disabled="!dirty || submitting">
        <span x-show="!submitting">{{ $save }}</span>
        <span x-show="submitting" x-cloak>جارٍ الحفظ…</span>
    </button>
    <button type="button" class="tp-btn tp-btn-outline" @click="if (cancel()) editing = null" :disabled="submitting">إلغاء</button>
    <p>✓ محدد = معيّن · إزالة العلامة = إلغاء التعيين عند الحفظ</p>
</div>
