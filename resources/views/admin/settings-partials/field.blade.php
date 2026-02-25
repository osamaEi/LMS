{{--
    Reusable setting field partial.
    Variables: $setting (array), $isPassword (optional bool)
--}}
@php $isPassword = $isPassword ?? false; @endphp

@if($setting['type'] === 'boolean')
<div class="flex items-center justify-between p-5 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-600">
    <div class="flex-1">
        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $setting['label'] }}</p>
        @if(!empty($setting['description']))
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $setting['description'] }}</p>
        @endif
    </div>
    <label class="relative inline-flex items-center cursor-pointer mr-4">
        <input type="hidden" name="settings[{{ $setting['key'] }}]" value="0">
        <input type="checkbox" name="settings[{{ $setting['key'] }}]" value="1"
               {{ $setting['value'] ? 'checked' : '' }} class="sr-only peer">
        <div class="w-11 h-6 bg-gray-300 peer-focus:outline-none rounded-full peer dark:bg-gray-600
                    peer-checked:after:translate-x-full peer-checked:after:border-white
                    after:content-[''] after:absolute after:top-[2px] after:right-[2px]
                    after:bg-white after:border-gray-300 after:border after:rounded-full
                    after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
    </label>
</div>

@elseif($setting['type'] === 'textarea')
<div>
    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ $setting['label'] }}</label>
    @if(!empty($setting['description']))
    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $setting['description'] }}</p>
    @endif
    <textarea name="settings[{{ $setting['key'] }}]" rows="3"
              class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:border-blue-500"
              style="focus:ring-color:rgba(0,113,170,.2)">{{ $setting['value'] }}</textarea>
</div>

@elseif($setting['type'] === 'file')
<div>
    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ $setting['label'] }}</label>
    @if(!empty($setting['description']))
    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $setting['description'] }}</p>
    @endif
    <div class="flex items-center gap-4">
        @if(!empty($setting['value']))
        <img src="{{ Storage::url($setting['value']) }}" alt="{{ $setting['label'] }}"
             class="h-20 w-20 object-contain rounded-xl border-2 border-gray-200 dark:border-gray-600 p-2 bg-white dark:bg-gray-700">
        @else
        <div class="h-20 w-20 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center bg-gray-50 dark:bg-gray-700/50">
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        @endif
        <input type="file" name="settings[{{ $setting['key'] }}]" accept="image/*"
               class="flex-1 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-3 text-gray-900 dark:text-white
                      file:ml-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium
                      file:bg-blue-50 dark:file:bg-blue-900/30 file:text-blue-700 dark:file:text-blue-400
                      hover:file:bg-blue-100 dark:hover:file:bg-blue-900/50">
    </div>
</div>

@elseif($setting['type'] === 'select')
<div>
    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ $setting['label'] }}</label>
    @if(!empty($setting['description']))
    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $setting['description'] }}</p>
    @endif
    <select name="settings[{{ $setting['key'] }}]"
            class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:border-blue-500">
        @foreach($setting['options'] ?? [] as $optKey => $optLabel)
        <option value="{{ $optKey }}" {{ $setting['value'] == $optKey ? 'selected' : '' }}>{{ $optLabel }}</option>
        @endforeach
    </select>
</div>

@elseif($setting['type'] === 'number')
<div>
    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ $setting['label'] }}</label>
    @if(!empty($setting['description']))
    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $setting['description'] }}</p>
    @endif
    <input type="number" name="settings[{{ $setting['key'] }}]" value="{{ $setting['value'] }}"
           class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:border-blue-500">
</div>

@else
{{-- text / email / password --}}
<div>
    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ $setting['label'] }}</label>
    @if(!empty($setting['description']))
    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $setting['description'] }}</p>
    @endif
    <input type="{{ $isPassword ? 'password' : $setting['type'] }}"
           name="settings[{{ $setting['key'] }}]"
           value="{{ $setting['value'] }}"
           autocomplete="{{ $isPassword ? 'new-password' : 'off' }}"
           class="w-full rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:border-blue-500">
</div>
@endif
