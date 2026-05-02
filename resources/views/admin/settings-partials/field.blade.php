{{--
    Reusable setting field partial.
    Variables: $setting (array), $accentColor (optional), $isPassword (optional bool)
--}}
@php
    $accentColor = $accentColor ?? '#0071AA';
    $isPassword  = $isPassword ?? (str_contains($setting['key'] ?? '', 'secret') || str_contains($setting['key'] ?? '', 'password'));
    $uid = 'field_' . str_replace(['.', '-'], '_', $setting['key'] ?? uniqid());
@endphp

@if(($setting['type'] ?? '') === 'boolean')
{{-- ── Toggle ── --}}
<div style="display:flex;align-items:center;justify-content:space-between;padding:16px 18px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;gap:16px;">
    <div style="flex:1;min-width:0;">
        <p style="font-size:14px;font-weight:600;color:#111827;margin:0;">{{ $setting['label'] ?? '' }}</p>
        @if(!empty($setting['description']))
        <p style="font-size:12px;color:#9ca3af;margin:4px 0 0;line-height:1.5;">{{ $setting['description'] }}</p>
        @endif
    </div>
    <label style="position:relative;display:inline-flex;align-items:center;cursor:pointer;flex-shrink:0;"
           title="{{ $setting['label'] ?? '' }}">
        <input type="hidden" name="settings[{{ $setting['key'] ?? '' }}]" value="0">
        <input type="checkbox" name="settings[{{ $setting['key'] ?? '' }}]" value="1"
               id="{{ $uid }}"
               {{ !empty($setting['value']) && $setting['value'] != '0' ? 'checked' : '' }}
               style="position:absolute;opacity:0;width:0;height:0;"
               onchange="
                   var track = this.nextElementSibling;
                   var dot   = track.querySelector('.tog-dot');
                   if(this.checked){
                       track.style.background='{{ $accentColor }}';
                       dot.style.transform='translateX(-22px)';
                   } else {
                       track.style.background='#d1d5db';
                       dot.style.transform='translateX(0)';
                   }">
        <div style="width:46px;height:26px;background:{{ (!empty($setting['value']) && $setting['value'] != '0') ? $accentColor : '#d1d5db' }};border-radius:13px;transition:background .2s;position:relative;">
            <div class="tog-dot" style="width:20px;height:20px;background:white;border-radius:50%;position:absolute;top:3px;right:3px;box-shadow:0 1px 3px rgba(0,0,0,0.2);transition:transform .2s;transform:{{ (!empty($setting['value']) && $setting['value'] != '0') ? 'translateX(-22px)' : 'translateX(0)' }};"></div>
        </div>
    </label>
</div>

@elseif(($setting['type'] ?? '') === 'textarea')
{{-- ── Textarea ── --}}
<div>
    <label for="{{ $uid }}" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">{{ $setting['label'] ?? '' }}</label>
    @if(!empty($setting['description']))
    <p style="font-size:12px;color:#9ca3af;margin:-2px 0 8px;line-height:1.5;">{{ $setting['description'] }}</p>
    @endif
    <textarea id="{{ $uid }}" name="settings[{{ $setting['key'] ?? '' }}]" rows="3"
              style="width:100%;border-radius:10px;border:1.5px solid #e5e7eb;background:white;padding:12px 14px;font-size:14px;color:#111827;font-family:inherit;resize:vertical;outline:none;transition:border-color .15s;box-sizing:border-box;"
              onfocus="this.style.borderColor='{{ $accentColor }}'" onblur="this.style.borderColor='#e5e7eb'">{{ $setting['value'] ?? '' }}</textarea>
</div>

@elseif(($setting['type'] ?? '') === 'file')
{{-- ── File / Image Upload ── --}}
<div>
    <label for="{{ $uid }}" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">{{ $setting['label'] ?? '' }}</label>
    @if(!empty($setting['description']))
    <p style="font-size:12px;color:#9ca3af;margin:-2px 0 8px;line-height:1.5;">{{ $setting['description'] }}</p>
    @endif
    <div style="display:flex;align-items:center;gap:14px;">
        @if(!empty($setting['value']))
        <div style="width:72px;height:72px;border-radius:12px;border:2px solid #e5e7eb;background:#f9fafb;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
            <img src="{{ Storage::url($setting['value']) }}" alt="{{ $setting['label'] ?? '' }}"
                 style="max-width:100%;max-height:100%;object-fit:contain;">
        </div>
        @else
        <div style="width:72px;height:72px;border-radius:12px;border:2px dashed #d1d5db;background:#f9fafb;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        @endif
        <div style="flex:1;min-width:0;">
            <label for="{{ $uid }}"
                   style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;background:{{ $accentColor }};color:white;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;transition:opacity .15s;"
                   onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                اختر صورة
            </label>
            <input id="{{ $uid }}" type="file" name="settings[{{ $setting['key'] ?? '' }}]" accept="image/*"
                   style="position:absolute;opacity:0;pointer-events:none;width:1px;height:1px;">
            @if(!empty($setting['value']))
            <p style="font-size:11px;color:#9ca3af;margin:6px 0 0;">الملف الحالي: {{ basename($setting['value']) }}</p>
            @endif
        </div>
    </div>
</div>

@elseif(($setting['type'] ?? '') === 'select')
{{-- ── Select ── --}}
<div>
    <label for="{{ $uid }}" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">{{ $setting['label'] ?? '' }}</label>
    @if(!empty($setting['description']))
    <p style="font-size:12px;color:#9ca3af;margin:-2px 0 8px;line-height:1.5;">{{ $setting['description'] }}</p>
    @endif
    <div style="position:relative;">
        <select id="{{ $uid }}" name="settings[{{ $setting['key'] ?? '' }}]"
                style="width:100%;border-radius:10px;border:1.5px solid #e5e7eb;background:white;padding:11px 14px;font-size:14px;color:#111827;font-family:inherit;outline:none;appearance:none;-webkit-appearance:none;cursor:pointer;transition:border-color .15s;"
                onfocus="this.style.borderColor='{{ $accentColor }}'" onblur="this.style.borderColor='#e5e7eb'">
            @foreach($setting['options'] ?? [] as $optKey => $optLabel)
            <option value="{{ $optKey }}" {{ ($setting['value'] ?? '') == $optKey ? 'selected' : '' }}>{{ $optLabel }}</option>
            @endforeach
        </select>
        <div style="position:absolute;left:14px;top:50%;transform:translateY(-50%);pointer-events:none;">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </div>
    </div>
</div>

@elseif(($setting['type'] ?? '') === 'number')
{{-- ── Number ── --}}
<div>
    <label for="{{ $uid }}" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">{{ $setting['label'] ?? '' }}</label>
    @if(!empty($setting['description']))
    <p style="font-size:12px;color:#9ca3af;margin:-2px 0 8px;line-height:1.5;">{{ $setting['description'] }}</p>
    @endif
    <input id="{{ $uid }}" type="number" name="settings[{{ $setting['key'] ?? '' }}]" value="{{ $setting['value'] ?? '' }}"
           style="width:100%;border-radius:10px;border:1.5px solid #e5e7eb;background:white;padding:11px 14px;font-size:14px;color:#111827;font-family:inherit;outline:none;transition:border-color .15s;box-sizing:border-box;"
           onfocus="this.style.borderColor='{{ $accentColor }}'" onblur="this.style.borderColor='#e5e7eb'">
</div>

@else
{{-- ── Text / Email / Password ── --}}
<div>
    <label for="{{ $uid }}" style="display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;">{{ $setting['label'] ?? '' }}</label>
    @if(!empty($setting['description']))
    <p style="font-size:12px;color:#9ca3af;margin:-2px 0 8px;line-height:1.5;">{{ $setting['description'] }}</p>
    @endif
    <div style="position:relative;">
        <input id="{{ $uid }}"
               type="{{ $isPassword ? 'password' : ($setting['type'] ?? 'text') }}"
               name="settings[{{ $setting['key'] ?? '' }}]"
               value="{{ $setting['value'] ?? '' }}"
               autocomplete="{{ $isPassword ? 'new-password' : 'off' }}"
               style="width:100%;border-radius:10px;border:1.5px solid #e5e7eb;background:white;padding:11px {{ $isPassword ? '44px' : '14px' }} 11px 14px;font-size:14px;color:#111827;font-family:inherit;outline:none;transition:border-color .15s;box-sizing:border-box;"
               onfocus="this.style.borderColor='{{ $accentColor }}'" onblur="this.style.borderColor='#e5e7eb'">
        @if($isPassword)
        <button type="button"
                onclick="var i=document.getElementById('{{ $uid }}');i.type=i.type==='password'?'text':'password';this.querySelector('.eye-off').style.display=i.type==='text'?'none':'block';this.querySelector('.eye-on').style.display=i.type==='text'?'block':'none';"
                style="position:absolute;left:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:4px;color:#9ca3af;"
                title="إظهار/إخفاء">
            <svg class="eye-off" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            <svg class="eye-on" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
        </button>
        @endif
    </div>
</div>
@endif
