{{-- Search box + live change summary for an assign form. Styles in show.blade.php (.tp-*). --}}
<div class="tp-toolbar">
    <div class="tp-search">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
        <input type="search" x-model="query" placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}"
               @keydown.escape.prevent="query = ''">
    </div>
    <p style="display:flex; flex-wrap:wrap; align-items:center; gap:.4rem; margin:0; font-size:.85rem;" aria-live="polite">
        <span class="tp-pill gray"><span x-text="selected.length"></span> محدد</span>
        <span x-show="added" x-cloak class="tp-pill green">+<span x-text="added"></span> إضافة</span>
        <span x-show="removed" x-cloak class="tp-pill red">−<span x-text="removed"></span> إلغاء</span>
    </p>
</div>
