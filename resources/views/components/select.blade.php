@props(['name', 'options' => [], 'selected' => ''])

<div class="relative" x-data="{
    open: false,
    selected: @js($selected),
    options: @js($options),
    get label() {
        return this.options[this.selected] ?? Object.values(this.options)[0] ?? '';
    }
}">
    <input type="hidden" name="{{ $name }}" x-model="selected">

    <button type="button"
            @click="open = !open"
            @click.outside="open = false"
            class="input-field flex w-full items-center justify-between text-start"
            :class="selected === '' || selected === null ? 'text-gray-400 dark:text-gray-500' : 'text-gray-900 dark:text-white'">
        <span x-text="label"></span>
        <svg class="h-4 w-4 shrink-0 text-gray-400 transition-transform" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 mt-1 w-full rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-700 dark:bg-gray-800"
         style="display:none;">
        @foreach($options as $value => $label)
        <button type="button"
                @click="selected = @js($value); open = false"
                class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700"
                :class="selected === @js($value) && 'font-medium text-[#0071AA] dark:text-[#38bdf8]'">
            {{ $label }}
        </button>
        @endforeach
    </div>
</div>
