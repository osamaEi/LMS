<div style="margin:14px 0;padding:14px;background:#f0f7fb;border:1px solid #cbdfea;border-radius:10px;color:#334155;font-size:14px;line-height:1.9;">
    <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
        <input type="checkbox" name="certificate_consent" value="1" required style="flex-shrink:0;width:18px;height:18px;margin-top:5px;accent-color:#0A5A86;">
        <span>{{ \App\Services\ConsentService::CERTIFICATE }}</span>
    </label>
    @error('certificate_consent')<p role="alert" style="color:#b91c1c;">{{ $message }}</p>@enderror
</div>
