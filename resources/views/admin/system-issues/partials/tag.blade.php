{{-- One tag chip. Clickable when it points at a page. Optional $removable + $issue for the × button. --}}
@php $href = $tag->href(); $c = $tag->color(); @endphp
<span style="display:inline-flex;align-items:center;gap:2px">
    @if($href)
    <a href="{{ $href }}" class="si-tag" style="color:{{ $c }}" title="{{ $tag->typeLabel() }}: {{ $tag->label }}"
       @if($tag->type === 'link' && !str_starts_with($href, '/')) target="_blank" rel="noopener" @endif>
        <span class="si-tag-type" style="background:{{ $c }}">{{ $tag->typeLabel() }}</span>
        <span class="si-tag-label">{{ $tag->label }}</span>
    </a>
    @else
    <span class="si-tag" style="color:{{ $c }}" title="{{ $tag->typeLabel() }}: {{ $tag->label }}">
        <span class="si-tag-type" style="background:{{ $c }}">{{ $tag->typeLabel() }}</span>
        <span class="si-tag-label">{{ $tag->label }}</span>
    </span>
    @endif
    @if(!empty($removable))
    <form action="{{ route('admin.system-issues.tags.destroy', [$issue, $tag]) }}" method="POST" style="margin:0"
          onsubmit="return confirm('إزالة هذا التاج؟')">
        @csrf
        @method('DELETE')
        <button type="submit" class="si-tag-x" title="إزالة التاج" style="color:{{ $c }}">×</button>
    </form>
    @endif
</span>
