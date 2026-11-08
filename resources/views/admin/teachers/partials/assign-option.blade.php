{{-- One checkbox row inside an assign form (expects the assignPanel() scope). Styles in show.blade.php (.tp-*). --}}
<label data-search="{{ $search }}" x-show="matches($el.dataset.search)" class="tp-option"
       :class="isChecked({{ $id }}) && 'on'">
    <input type="checkbox" name="{{ $name }}" value="{{ $id }}"
           :checked="isChecked({{ $id }})" @change="toggle({{ $id }}, $event.target.checked)">
    <span class="tp-option-label">
        {{ $label }}
        @if(!empty($warning))
        <span class="tp-option-warn">{{ $warning }}</span>
        @endif
    </span>
    @if(!empty($meta))
    <span class="tp-option-meta" dir="ltr">{{ $meta }}</span>
    @endif
</label>
