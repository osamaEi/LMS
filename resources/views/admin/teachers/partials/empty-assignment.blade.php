{{-- Empty state for an assignment tab, with a shortcut into edit mode. --}}
<div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 py-10 text-center dark:border-gray-600">
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $text }}</p>
    <button type="button" @click="editing = '{{ $action }}'"
            class="mt-3 rounded-lg bg-brand-500 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-600">{{ $cta }}</button>
</div>
