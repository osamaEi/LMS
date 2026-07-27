@props(['title', 'icon' => ''])
<div style="background:white;border:1px solid #e5e7eb;border-radius:16px;overflow:hidden;margin-bottom:16px;box-shadow:0 2px 12px rgba(0,0,0,.05);">
    <div style="padding:14px 18px;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
        <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0;">{{ $icon }} {{ $title }}</h3>
    </div>
    <div style="padding:6px 4px;">
        {{ $slot }}
    </div>
</div>
