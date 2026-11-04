{{--
    Tag picker. $mode: 'inputs' (adds hidden inputs to the form, needs $list = id of the chip container)
                       'submit' (posts the tag immediately, needs $issue).
--}}
@php
    $tagTypes = collect(\App\Models\SystemIssueTag::types())->map(fn ($t) => ['label' => $t['label'], 'color' => $t['color']]);
@endphp
<div class="si-picker"
     data-mode="{{ $mode }}"
     data-search-url="{{ route('admin.system-issues.tags.search') }}"
     data-types='@json($tagTypes)'
     @if($mode === 'inputs') data-list="{{ $list }}" @endif
     @if($mode === 'submit') data-store-url="{{ route('admin.system-issues.tags.store', $issue) }}" data-csrf="{{ csrf_token() }}" @endif>
    <div class="si-picker-row">
        <select class="si-select" data-tag-type>
            @foreach($tagTypes as $key => $t)
            <option value="{{ $key }}">{{ $t['label'] }}</option>
            @endforeach
        </select>
        <input type="text" class="si-input" data-tag-q autocomplete="off">
        <div data-link-row style="display:none;gap:8px;flex:1;flex-wrap:wrap">
            <input type="text" class="si-input" data-link-url placeholder="الرابط: https://... أو /admin/..." style="flex:2;min-width:200px" dir="ltr">
            <input type="text" class="si-input" data-link-label placeholder="اسم التاج (اختياري)" style="flex:1;min-width:140px">
            <button type="button" class="si-btn si-btn-light" data-link-add>إضافة</button>
        </div>
    </div>
    <div class="si-results"></div>
</div>
