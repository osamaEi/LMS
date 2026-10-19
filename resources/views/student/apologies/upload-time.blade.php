<div style="font-size:12px;color:#64748b;margin-bottom:8px;">
    تاريخ ووقت رفع العذر:
    <time datetime="{{ $apology->created_at?->toIso8601String() }}">{{ $apology->created_at?->format('Y/m/d H:i') }}</time>
</div>
