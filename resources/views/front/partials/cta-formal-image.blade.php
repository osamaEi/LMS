<div style="position:relative;padding:18px 0 18px 18px;max-width:520px;width:100%;">
    {{-- background geometric frames --}}
    <div style="position:absolute;top:0;right:0;width:74%;height:76%;background:rgba(255,255,255,.08);border:1.5px solid rgba(255,255,255,.15);border-radius:20px;pointer-events:none;"></div>
    <div style="position:absolute;bottom:0;left:0;width:48%;height:50%;border:2px solid rgba(255,255,255,.15);border-radius:16px;pointer-events:none;"></div>

    <div style="position:relative;border-radius:18px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,.3);">
        <img loading="lazy" src="{{ $src }}" alt="{{ $badgeText }}"
             style="width:100%;height:420px;object-fit:cover;display:block;filter:brightness(.93) contrast(1.05) saturate(1.07);"
             onerror="this.parentElement.parentElement.style.display='none'">

        {{-- dark gradient overlay --}}
        <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,40,80,.58) 0%,transparent 52%);border-radius:18px;pointer-events:none;"></div>

        {{-- bottom badge --}}
        <div style="position:absolute;bottom:14px;right:14px;display:flex;align-items:center;gap:9px;background:rgba(255,255,255,.96);backdrop-filter:blur(8px);border-radius:12px;padding:8px 13px;box-shadow:0 4px 18px rgba(0,0,0,.18);">
            <div style="width:32px;height:32px;background:linear-gradient(135deg,#0071AA,#004d77);border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
            </div>
            <div>
                <strong style="display:block;font-size:12px;font-weight:800;color:#111827;line-height:1.2;">{{ $badgeText }}</strong>
                <span style="font-size:10px;color:#6b7280;">{{ $badgeSub }}</span>
            </div>
        </div>

        {{-- top corner tag --}}
        <div style="position:absolute;top:14px;left:14px;background:linear-gradient(135deg,#ef4444,#dc2626);border-radius:11px;padding:7px 11px;text-align:center;box-shadow:0 4px 14px rgba(239,68,68,.4);">
            <span style="display:block;font-size:12px;font-weight:800;color:#fff;white-space:nowrap;">{{ $tagText }}</span>
        </div>

        {{-- glass accent top-right --}}
        <div style="position:absolute;top:-9px;right:-9px;width:42px;height:42px;background:rgba(255,255,255,.15);backdrop-filter:blur(6px);border:1.5px solid rgba(255,255,255,.25);border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 14px rgba(0,0,0,.2);">
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
            </svg>
        </div>
    </div>
</div>
