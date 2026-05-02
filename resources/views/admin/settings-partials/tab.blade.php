{{--
    Generic settings tab card.
    Variables: $tabTitle, $tabDesc, $tabIcon, $tabColor, $tabGrad, $tabLight, $group, $settingsGroup
--}}
@php
    $tabColor ??= '#0071AA';
    $tabGrad  ??= '#0071AA,#005a88';
    $tabLight ??= '#e0f2fe';
@endphp

<div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.06);">
    <div style="padding:20px 24px;border-bottom:1px solid #f3f4f6;background:linear-gradient(135deg,{{ $tabLight }},white);">
        <div style="display:flex;align-items:center;gap:14px;">
            <div style="width:44px;height:44px;background:linear-gradient(135deg,{{ $tabGrad }});border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 12px rgba(0,0,0,0.18);">
                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tabIcon }}"/>
                </svg>
            </div>
            <div>
                <h2 style="font-size:16px;font-weight:700;color:#111827;margin:0;">{{ $tabTitle }}</h2>
                <p style="font-size:13px;color:#9ca3af;margin:3px 0 0;">{{ $tabDesc }}</p>
            </div>
            @if(!empty($settingsGroup))
            <div style="margin-right:auto;display:inline-flex;align-items:center;gap:5px;background:white;border:1px solid #e5e7eb;padding:4px 10px;border-radius:20px;box-shadow:0 1px 4px rgba(0,0,0,0.06);">
                <span style="color:{{ $tabColor }};font-size:13px;font-weight:700;">{{ count($settingsGroup) }}</span>
                <span style="color:#9ca3af;font-size:12px;">حقل</span>
            </div>
            @endif
        </div>
    </div>

    <form action="{{ route('admin.settings.update-group', $group) }}" method="POST" enctype="multipart/form-data" style="padding:24px;">
        @csrf
        @method('PUT')

        @if(empty($settingsGroup))
        <div style="display:flex;flex-direction:column;align-items:center;padding:48px 0;text-align:center;">
            <div style="width:64px;height:64px;background:#f9fafb;border:2px dashed #e5e7eb;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:14px;">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#d1d5db" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p style="color:#9ca3af;font-size:14px;font-weight:500;margin:0;">لا توجد إعدادات في هذا القسم</p>
        </div>
        @else
        <div style="display:flex;flex-direction:column;gap:16px;">
            @foreach($settingsGroup as $setting)
                @include('admin.settings-partials.field', ['setting' => $setting, 'accentColor' => $tabColor])
            @endforeach
        </div>

        <div style="display:flex;justify-content:flex-end;padding-top:20px;margin-top:20px;border-top:1px solid #f3f4f6;">
            <button type="submit"
                    style="padding:11px 28px;background:linear-gradient(135deg,{{ $tabGrad }});color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 3px 10px rgba(0,0,0,0.15);display:inline-flex;align-items:center;gap:8px;transition:opacity .15s;"
                    onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                حفظ التغييرات
            </button>
        </div>
        @endif
    </form>
</div>
