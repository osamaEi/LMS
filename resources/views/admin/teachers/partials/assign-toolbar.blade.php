{{-- Search box + live change summary for an assign form. --}}
<div class="mb-3 flex flex-wrap items-center justify-between gap-3">
    <div class="relative w-full max-w-sm">
        <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
        <input type="search" x-model="query" placeholder="{{ $placeholder }}" aria-label="{{ $placeholder }}"
               @keydown.escape.prevent="query = ''"
               class="w-full rounded-lg border border-gray-300 py-2 pl-3 pr-9 text-sm focus:border-brand-500 focus:ring-brand-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white">
    </div>
    <p class="flex flex-wrap items-center gap-2 text-sm" aria-live="polite">
        <span class="text-gray-500 dark:text-gray-400"><span x-text="selected.length"></span> محدد</span>
        <span x-show="added" x-cloak class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">+<span x-text="added"></span> إضافة</span>
        <span x-show="removed" x-cloak class="rounded-full bg-red-50 px-2 py-0.5 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-300">−<span x-text="removed"></span> إلغاء</span>
    </p>
</div>
