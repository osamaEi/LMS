{{-- Save / cancel bar for an assign form. --}}
<div class="mt-4 flex flex-wrap items-center gap-2">
    <button type="submit" :disabled="!dirty || submitting"
            class="rounded-lg px-5 py-2 text-sm font-semibold text-white shadow-sm disabled:cursor-not-allowed disabled:opacity-50 {{ $tone }}">
        <span x-show="!submitting">{{ $save }}</span>
        <span x-show="submitting" x-cloak>جارٍ الحفظ…</span>
    </button>
    <button type="button" @click="if (cancel()) editing = null" :disabled="submitting"
            class="rounded-lg border border-gray-300 px-5 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
        إلغاء
    </button>
    <p class="text-xs text-gray-400">✓ محدد = معيّن · إزالة العلامة = إلغاء التعيين عند الحفظ</p>
</div>
