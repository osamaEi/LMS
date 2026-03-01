@extends('layouts.dashboard')

@section('title', 'عارض السجلات — Laravel Log')

@section('content')
<div style="direction:rtl; font-family:'Segoe UI',Tahoma,sans-serif; background:#0f172a; min-height:100vh;">

    {{-- ══════════════════════════════════════════════════════ --}}
    {{-- HERO                                                  --}}
    {{-- ══════════════════════════════════════════════════════ --}}
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1a0a2e 40%,#16213e 100%);
                padding:2.5rem 2rem 2rem; border-bottom:1px solid rgba(139,92,246,.25);">

        {{-- Title row --}}
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.5rem;">
            <div style="display:flex;align-items:center;gap:1rem;">
                <div style="width:56px;height:56px;border-radius:16px;
                            background:linear-gradient(135deg,#7c3aed,#4f46e5);
                            display:flex;align-items:center;justify-content:center;
                            box-shadow:0 8px 25px rgba(124,58,237,.4);">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                              stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <h1 style="color:#f8fafc;font-size:1.75rem;font-weight:700;margin:0;">عارض سجلات النظام</h1>
                    <p style="color:#94a3b8;font-size:.875rem;margin:0;">
                        laravel.log
                        @if($lastModified)
                            — آخر تعديل: {{ $lastModified->diffForHumans() }}
                        @endif
                    </p>
                </div>
            </div>

            {{-- Action buttons --}}
            <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                <a href="{{ route('admin.logs.download') }}"
                   style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.25rem;
                          background:rgba(16,185,129,.15);border:1px solid rgba(16,185,129,.4);
                          color:#6ee7b7;border-radius:10px;text-decoration:none;font-size:.875rem;font-weight:600;
                          transition:all .2s;">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
                        <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    تحميل السجل
                </a>

                <form method="POST" action="{{ route('admin.logs.clear') }}"
                      onsubmit="return confirm('هل أنت متأكد من مسح ملف السجل بالكامل؟ لا يمكن التراجع عن هذه العملية.');">
                    @csrf
                    <button type="submit"
                            style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.25rem;
                                   background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.4);
                                   color:#fca5a5;border-radius:10px;font-size:.875rem;font-weight:600;cursor:pointer;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        مسح السجل
                    </button>
                </form>

                <button onclick="document.getElementById('logTable').scrollIntoView({behavior:'smooth'})"
                        style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.25rem;
                               background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.4);
                               color:#a5b4fc;border-radius:10px;font-size:.875rem;font-weight:600;cursor:pointer;">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                    </svg>
                    الأعلى
                </button>
            </div>
        </div>

        {{-- File stats chips --}}
        <div style="display:flex;flex-wrap:wrap;gap:.75rem;">
            <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
                        border-radius:8px;padding:.4rem 1rem;display:flex;align-items:center;gap:.5rem;">
                <span style="width:8px;height:8px;border-radius:50%;background:#6ee7b7;display:inline-block;"></span>
                <span style="color:#94a3b8;font-size:.8rem;">حجم الملف:</span>
                <span style="color:#f1f5f9;font-size:.8rem;font-weight:600;">
                    @if($fileSize > 1048576)
                        {{ number_format($fileSize / 1048576, 2) }} MB
                    @elseif($fileSize > 1024)
                        {{ number_format($fileSize / 1024, 1) }} KB
                    @else
                        {{ $fileSize }} B
                    @endif
                </span>
            </div>
            <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
                        border-radius:8px;padding:.4rem 1rem;display:flex;align-items:center;gap:.5rem;">
                <span style="color:#94a3b8;font-size:.8rem;">إجمالي الأسطر:</span>
                <span style="color:#f1f5f9;font-size:.8rem;font-weight:600;">{{ number_format($totalLines) }}</span>
            </div>
            <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
                        border-radius:8px;padding:.4rem 1rem;display:flex;align-items:center;gap:.5rem;">
                <span style="color:#94a3b8;font-size:.8rem;">المدخلات المعروضة:</span>
                <span style="color:#f1f5f9;font-size:.8rem;font-weight:600;">{{ count($entries) }}</span>
            </div>
            @if($search)
            <div style="background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.3);
                        border-radius:8px;padding:.4rem 1rem;display:flex;align-items:center;gap:.5rem;">
                <span style="color:#fbbf24;font-size:.8rem;">بحث: "{{ $search }}"</span>
            </div>
            @endif
        </div>

        @if(session('success'))
        <div style="margin-top:1rem;padding:.75rem 1rem;background:rgba(16,185,129,.15);
                    border:1px solid rgba(16,185,129,.4);border-radius:10px;color:#6ee7b7;font-size:.875rem;">
            ✓ {{ session('success') }}
        </div>
        @endif
    </div>

    <div style="padding:2rem;max-width:1600px;margin:0 auto;">

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- LEVEL COUNTER CARDS                                  --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        @php
        $levelConfig = [
            'ERROR'     => ['color'=>'#ef4444','bg'=>'rgba(239,68,68,.12)','border'=>'rgba(239,68,68,.3)','icon'=>'🔴','label'=>'خطأ'],
            'WARNING'   => ['color'=>'#f59e0b','bg'=>'rgba(245,158,11,.12)','border'=>'rgba(245,158,11,.3)','icon'=>'⚠️','label'=>'تحذير'],
            'INFO'      => ['color'=>'#3b82f6','bg'=>'rgba(59,130,246,.12)','border'=>'rgba(59,130,246,.3)','icon'=>'ℹ️','label'=>'معلومة'],
            'DEBUG'     => ['color'=>'#6b7280','bg'=>'rgba(107,114,128,.12)','border'=>'rgba(107,114,128,.3)','icon'=>'🔧','label'=>'تصحيح'],
            'CRITICAL'  => ['color'=>'#dc2626','bg'=>'rgba(220,38,38,.15)','border'=>'rgba(220,38,38,.4)','icon'=>'🚨','label'=>'حرج'],
            'EMERGENCY' => ['color'=>'#be123c','bg'=>'rgba(190,18,60,.15)','border'=>'rgba(190,18,60,.4)','icon'=>'🆘','label'=>'طارئ'],
        ];
        @endphp

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:1rem;margin-bottom:2rem;">
            @foreach($levelConfig as $lvl => $cfg)
            @if(($levelCounts[$lvl] ?? 0) > 0 || in_array($lvl, ['ERROR','WARNING','INFO']))
            <a href="{{ request()->fullUrlWithQuery(['level' => strtolower($lvl)]) }}"
               style="background:{{ $cfg['bg'] }};border:1px solid {{ $cfg['border'] }};
                      border-radius:12px;padding:1.25rem;text-decoration:none;
                      transition:transform .2s;display:block;
                      {{ strtoupper($level) === $lvl ? 'box-shadow:0 0 0 2px '.$cfg['color'].';' : '' }}">
                <div style="font-size:1.5rem;margin-bottom:.5rem;">{{ $cfg['icon'] }}</div>
                <div style="font-size:1.5rem;font-weight:700;color:{{ $cfg['color'] }};">
                    {{ number_format($levelCounts[$lvl] ?? 0) }}
                </div>
                <div style="color:#94a3b8;font-size:.8rem;">{{ $cfg['label'] }}</div>
            </a>
            @endif
            @endforeach

            {{-- All levels card --}}
            <a href="{{ route('admin.logs.index') }}"
               style="background:rgba(139,92,246,.12);border:1px solid rgba(139,92,246,.3);
                      border-radius:12px;padding:1.25rem;text-decoration:none;
                      {{ $level === 'all' ? 'box-shadow:0 0 0 2px #7c3aed;' : '' }}">
                <div style="font-size:1.5rem;margin-bottom:.5rem;">📋</div>
                <div style="font-size:1.5rem;font-weight:700;color:#a78bfa;">{{ count($entries) }}</div>
                <div style="color:#94a3b8;font-size:.8rem;">الكل</div>
            </a>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- FILTER BAR                                           --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div style="background:#1e293b;border:1px solid rgba(255,255,255,.08);
                    border-radius:14px;padding:1.25rem;margin-bottom:1.5rem;">
            <form method="GET" action="{{ route('admin.logs.index') }}"
                  style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">

                <div style="flex:2;min-width:200px;">
                    <label style="color:#94a3b8;font-size:.8rem;display:block;margin-bottom:.4rem;">بحث في الرسائل</label>
                    <div style="position:relative;">
                        <input type="text" name="search" value="{{ $search }}" placeholder="ابحث عن نص في الرسالة..."
                               style="width:100%;background:#0f172a;border:1px solid rgba(255,255,255,.1);
                                      border-radius:8px;padding:.6rem 1rem .6rem 2.5rem;color:#f1f5f9;font-size:.875rem;
                                      box-sizing:border-box;">
                        <svg style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:#475569;"
                             width="16" height="16" fill="none" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                  stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>

                <div style="min-width:150px;">
                    <label style="color:#94a3b8;font-size:.8rem;display:block;margin-bottom:.4rem;">مستوى السجل</label>
                    <select name="level"
                            style="width:100%;background:#0f172a;border:1px solid rgba(255,255,255,.1);
                                   border-radius:8px;padding:.6rem 1rem;color:#f1f5f9;font-size:.875rem;">
                        <option value="all" {{ $level==='all' ? 'selected' : '' }}>الكل</option>
                        <option value="error"     {{ $level==='error'     ? 'selected' : '' }}>🔴 خطأ (ERROR)</option>
                        <option value="warning"   {{ $level==='warning'   ? 'selected' : '' }}>⚠️ تحذير (WARNING)</option>
                        <option value="info"      {{ $level==='info'      ? 'selected' : '' }}>ℹ️ معلومة (INFO)</option>
                        <option value="debug"     {{ $level==='debug'     ? 'selected' : '' }}>🔧 تصحيح (DEBUG)</option>
                        <option value="critical"  {{ $level==='critical'  ? 'selected' : '' }}>🚨 حرج (CRITICAL)</option>
                        <option value="emergency" {{ $level==='emergency' ? 'selected' : '' }}>🆘 طارئ (EMERGENCY)</option>
                    </select>
                </div>

                <div style="min-width:130px;">
                    <label style="color:#94a3b8;font-size:.8rem;display:block;margin-bottom:.4rem;">عدد المدخلات</label>
                    <select name="lines"
                            style="width:100%;background:#0f172a;border:1px solid rgba(255,255,255,.1);
                                   border-radius:8px;padding:.6rem 1rem;color:#f1f5f9;font-size:.875rem;">
                        <option value="100"  {{ $lines==100  ? 'selected' : '' }}>آخر 100</option>
                        <option value="250"  {{ $lines==250  ? 'selected' : '' }}>آخر 250</option>
                        <option value="500"  {{ $lines==500  ? 'selected' : '' }}>آخر 500</option>
                        <option value="1000" {{ $lines==1000 ? 'selected' : '' }}>آخر 1000</option>
                        <option value="2000" {{ $lines==2000 ? 'selected' : '' }}>آخر 2000</option>
                    </select>
                </div>

                <div style="display:flex;gap:.5rem;align-items:flex-end;">
                    <button type="submit"
                            style="padding:.65rem 1.5rem;background:linear-gradient(135deg,#7c3aed,#4f46e5);
                                   border:none;border-radius:8px;color:white;font-size:.875rem;font-weight:600;cursor:pointer;">
                        تصفية
                    </button>
                    <a href="{{ route('admin.logs.index') }}"
                       style="padding:.65rem 1.25rem;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);
                              border-radius:8px;color:#94a3b8;font-size:.875rem;text-decoration:none;display:inline-block;">
                        مسح
                    </a>
                </div>
            </form>
        </div>

        {{-- ══════════════════════════════════════════════════════ --}}
        {{-- LOG ENTRIES TABLE                                    --}}
        {{-- ══════════════════════════════════════════════════════ --}}
        <div id="logTable" style="background:#1e293b;border:1px solid rgba(255,255,255,.08);border-radius:14px;overflow:hidden;">

            {{-- Table Header --}}
            <div style="padding:1rem 1.5rem;border-bottom:1px solid rgba(255,255,255,.06);
                        display:flex;align-items:center;justify-content:space-between;">
                <h3 style="color:#f1f5f9;font-size:1rem;font-weight:600;margin:0;">
                    سجلات النظام
                    <span style="background:rgba(139,92,246,.2);color:#a78bfa;padding:.2rem .6rem;
                                 border-radius:6px;font-size:.75rem;font-weight:600;margin-right:.5rem;">
                        {{ count($entries) }} مدخل
                    </span>
                </h3>
                <div style="display:flex;gap:.5rem;">
                    <button onclick="toggleAll(true)"
                            style="padding:.35rem .85rem;background:rgba(99,102,241,.15);border:1px solid rgba(99,102,241,.3);
                                   color:#a5b4fc;border-radius:6px;font-size:.75rem;cursor:pointer;">
                        توسيع الكل
                    </button>
                    <button onclick="toggleAll(false)"
                            style="padding:.35rem .85rem;background:rgba(107,114,128,.1);border:1px solid rgba(107,114,128,.2);
                                   color:#6b7280;border-radius:6px;font-size:.75rem;cursor:pointer;">
                        طي الكل
                    </button>
                </div>
            </div>

            @if(count($entries) === 0)
            <div style="padding:4rem;text-align:center;">
                <div style="font-size:3rem;margin-bottom:1rem;">📭</div>
                <p style="color:#475569;font-size:1rem;">لا توجد مدخلات تطابق معايير البحث</p>
                @if(!File::exists(storage_path('logs/laravel.log')))
                <p style="color:#334155;font-size:.875rem;margin-top:.5rem;">ملف laravel.log غير موجود</p>
                @endif
            </div>
            @else

            {{-- Log entries list --}}
            <div style="max-height:75vh;overflow-y:auto;" id="logScroll">
                @foreach($entries as $i => $entry)
                @php
                $lvl = strtoupper($entry['level']);
                $cfg = match($lvl) {
                    'ERROR'     => ['c'=>'#ef4444','bg'=>'rgba(239,68,68,.08)','border'=>'rgba(239,68,68,.2)','icon'=>'🔴','badge'=>'rgba(239,68,68,.2)','badgeText'=>'#fca5a5'],
                    'CRITICAL'  => ['c'=>'#dc2626','bg'=>'rgba(220,38,38,.1)','border'=>'rgba(220,38,38,.25)','icon'=>'🚨','badge'=>'rgba(220,38,38,.2)','badgeText'=>'#fca5a5'],
                    'EMERGENCY' => ['c'=>'#be123c','bg'=>'rgba(190,18,60,.1)','border'=>'rgba(190,18,60,.25)','icon'=>'🆘','badge'=>'rgba(190,18,60,.2)','badgeText'=>'#fda4af'],
                    'ALERT'     => ['c'=>'#e11d48','bg'=>'rgba(225,29,72,.08)','border'=>'rgba(225,29,72,.2)','icon'=>'📢','badge'=>'rgba(225,29,72,.2)','badgeText'=>'#fda4af'],
                    'WARNING'   => ['c'=>'#f59e0b','bg'=>'rgba(245,158,11,.08)','border'=>'rgba(245,158,11,.2)','icon'=>'⚠️','badge'=>'rgba(245,158,11,.2)','badgeText'=>'#fcd34d'],
                    'NOTICE'    => ['c'=>'#06b6d4','bg'=>'rgba(6,182,212,.08)','border'=>'rgba(6,182,212,.2)','icon'=>'📝','badge'=>'rgba(6,182,212,.2)','badgeText'=>'#67e8f9'],
                    'INFO'      => ['c'=>'#3b82f6','bg'=>'rgba(59,130,246,.08)','border'=>'rgba(59,130,246,.2)','icon'=>'ℹ️','badge'=>'rgba(59,130,246,.2)','badgeText'=>'#93c5fd'],
                    'DEBUG'     => ['c'=>'#6b7280','bg'=>'rgba(107,114,128,.06)','border'=>'rgba(107,114,128,.15)','icon'=>'🔧','badge'=>'rgba(107,114,128,.15)','badgeText'=>'#9ca3af'],
                    default     => ['c'=>'#64748b','bg'=>'rgba(100,116,139,.06)','border'=>'rgba(100,116,139,.15)','icon'=>'📄','badge'=>'rgba(100,116,139,.15)','badgeText'=>'#94a3b8'],
                };
                $hasDetails = !empty($entry['context']) || !empty($entry['extra']);
                @endphp

                <div class="log-entry" style="border-bottom:1px solid rgba(255,255,255,.04);">

                    {{-- Entry header (always visible) --}}
                    <div onclick="{{ $hasDetails ? 'toggleEntry(this)' : '' }}"
                         style="display:flex;align-items:flex-start;gap:1rem;padding:1rem 1.5rem;
                                background:{{ $cfg['bg'] }};
                                border-right:3px solid {{ $cfg['c'] }};
                                {{ $hasDetails ? 'cursor:pointer;' : '' }}
                                transition:background .15s;">

                        {{-- Level badge --}}
                        <span style="flex-shrink:0;padding:.25rem .7rem;border-radius:6px;
                                     background:{{ $cfg['badge'] }};color:{{ $cfg['badgeText'] }};
                                     font-size:.7rem;font-weight:700;font-family:monospace;
                                     min-width:80px;text-align:center;margin-top:.1rem;">
                            {{ $cfg['icon'] }} {{ $lvl }}
                        </span>

                        {{-- Datetime --}}
                        <span style="flex-shrink:0;color:#475569;font-size:.75rem;font-family:monospace;
                                     min-width:155px;margin-top:.15rem;padding-top:.05rem;">
                            {{ $entry['datetime'] }}
                        </span>

                        {{-- Message --}}
                        <span style="color:#e2e8f0;font-size:.875rem;flex:1;word-break:break-all;line-height:1.5;">
                            {{ Str::limit($entry['message'], 200) }}
                        </span>

                        {{-- Environment --}}
                        <span style="flex-shrink:0;color:#334155;font-size:.7rem;font-family:monospace;margin-top:.2rem;">
                            {{ $entry['environment'] }}
                        </span>

                        @if($hasDetails)
                        <span class="toggle-icon" style="flex-shrink:0;color:#4f46e5;font-size:.75rem;margin-top:.15rem;
                                     transition:transform .2s;">▼</span>
                        @endif
                    </div>

                    {{-- Expandable details --}}
                    @if($hasDetails)
                    <div class="entry-details" style="display:none;background:#0f172a;padding:1rem 1.5rem 1.25rem;
                                border-right:3px solid {{ $cfg['c'] }};">

                        @if(!empty($entry['message']) && strlen($entry['message']) > 200)
                        <div style="margin-bottom:1rem;">
                            <div style="color:#64748b;font-size:.7rem;font-weight:600;text-transform:uppercase;
                                        letter-spacing:.05em;margin-bottom:.4rem;">الرسالة الكاملة</div>
                            <pre style="color:#e2e8f0;font-size:.8rem;font-family:'Courier New',monospace;
                                        white-space:pre-wrap;word-break:break-all;margin:0;
                                        background:#1e293b;padding:.75rem;border-radius:8px;
                                        border:1px solid rgba(255,255,255,.06);">{{ $entry['message'] }}</pre>
                        </div>
                        @endif

                        @if(!empty($entry['context']))
                        <div style="margin-bottom:1rem;">
                            <div style="color:#64748b;font-size:.7rem;font-weight:600;text-transform:uppercase;
                                        letter-spacing:.05em;margin-bottom:.4rem;">السياق (Context)</div>
                            <pre style="color:#a78bfa;font-size:.78rem;font-family:'Courier New',monospace;
                                        white-space:pre-wrap;word-break:break-all;margin:0;
                                        background:#1e293b;padding:.75rem;border-radius:8px;
                                        border:1px solid rgba(139,92,246,.15);max-height:300px;overflow-y:auto;">{{ $entry['context'] }}</pre>
                        </div>
                        @endif

                        @if(!empty($entry['extra']))
                        <div>
                            <div style="color:#64748b;font-size:.7rem;font-weight:600;text-transform:uppercase;
                                        letter-spacing:.05em;margin-bottom:.4rem;">Stack Trace</div>
                            <pre style="color:#6b7280;font-size:.75rem;font-family:'Courier New',monospace;
                                        white-space:pre-wrap;word-break:break-all;margin:0;
                                        background:#1e293b;padding:.75rem;border-radius:8px;
                                        border:1px solid rgba(107,114,128,.15);max-height:400px;overflow-y:auto;">{{ trim($entry['extra']) }}</pre>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Footer note --}}
        <div style="text-align:center;margin-top:1.5rem;color:#334155;font-size:.8rem;">
            يعرض آخر {{ count($entries) }} مدخل من ملف
            <code style="background:#1e293b;padding:.15rem .4rem;border-radius:4px;color:#6b7280;">storage/logs/laravel.log</code>
        </div>
    </div>
</div>

<script>
function toggleEntry(el) {
    const details = el.nextElementSibling;
    const icon    = el.querySelector('.toggle-icon');
    if (!details) return;
    const isHidden = details.style.display === 'none';
    details.style.display = isHidden ? 'block' : 'none';
    if (icon) icon.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0deg)';
}

function toggleAll(expand) {
    document.querySelectorAll('.entry-details').forEach(d => {
        d.style.display = expand ? 'block' : 'none';
    });
    document.querySelectorAll('.toggle-icon').forEach(i => {
        i.style.transform = expand ? 'rotate(180deg)' : 'rotate(0deg)';
    });
}

// Auto-scroll to bottom of log list on load
document.addEventListener('DOMContentLoaded', function () {
    // Highlight search terms
    const search = @json($search);
    if (search) {
        document.querySelectorAll('.log-entry').forEach(entry => {
            const msgEl = entry.querySelector('span:nth-child(3)');
            if (msgEl && msgEl.textContent.toLowerCase().includes(search.toLowerCase())) {
                entry.style.background = 'rgba(251,191,36,.03)';
            }
        });
    }
});
</script>
@endsection
