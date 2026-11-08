{{-- Empty state for an assignment tab, with a shortcut into edit mode. Styles in show.blade.php (.tp-*). --}}
<div class="tp-empty">
    @if(!empty($icon))<div class="tp-empty-icon">{!! $icon !!}</div>@endif
    <p>{{ $text }}</p>
    <button type="button" class="tp-btn tp-btn-primary" @click="editing = '{{ $action }}'">{{ $cta }}</button>
</div>
