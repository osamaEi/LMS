{{-- Upcoming sessions as a timeline. Expects $items (Session collection). Styles in show.blade.php (.tp-*). --}}
@if($items->count())
<div class="tp-timeline">
    @foreach($items as $s)
        @php $at = \Carbon\Carbon::parse($s->scheduled_at); @endphp
        <div class="tp-tl-item">
            <div class="tp-date"><b>{{ $at->format('d') }}</b><small>{{ $at->translatedFormat('M') }}</small></div>
            <div class="tp-tl-body">
                <div class="tp-row-title">{{ $s->title_ar ?: ($s->subject->name_ar ?? 'جلسة') }}</div>
                <div class="tp-row-sub">
                    <span dir="ltr">{{ $at->format('H:i') }}</span>
                    @if($s->duration_minutes) · {{ $s->duration_minutes }} دقيقة@endif
                    @if($s->programClass) · {{ $s->programClass->name }}@endif
                    @if($s->subject && $s->title_ar) · {{ $s->subject->name_ar }}@endif
                </div>
                <div class="tp-tl-when">{{ $at->diffForHumans() }}</div>
            </div>
        </div>
    @endforeach
</div>
@else
<div class="tp-empty"><p style="margin:0;">لا توجد جلسات مجدولة</p></div>
@endif
