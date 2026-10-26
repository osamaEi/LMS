{{-- One checkbox row inside an assign form (expects the assignPanel() scope). --}}
<label data-search="{{ $search }}" x-show="matches($el.dataset.search)"
       class="flex cursor-pointer items-center gap-3 border-b border-gray-100 px-4 py-2.5 last:border-0 hover:bg-gray-50 dark:border-gray-700 dark:hover:bg-gray-700/50"
       :class="isChecked({{ $id }}) && 'bg-brand-50/40 dark:bg-brand-900/10'">
    <input type="checkbox" name="{{ $name }}" value="{{ $id }}"
           :checked="isChecked({{ $id }})" @change="toggle({{ $id }}, $event.target.checked)"
           class="h-4 w-4 shrink-0 rounded border-gray-300 {{ $accent }} focus:ring-brand-500">
    <span class="min-w-0 flex-1">
        <span class="block truncate text-sm text-gray-800 dark:text-gray-100">{{ $label }}</span>
        @if(!empty($warning))
        <span class="mt-0.5 block text-xs font-medium text-red-600 dark:text-red-400">{{ $warning }}</span>
        @endif
    </span>
    @if(!empty($meta))
    <span class="shrink-0 text-xs text-gray-400" dir="ltr">{{ $meta }}</span>
    @endif
</label>
