@extends('layouts.dashboard')

@section('title', 'عارض السجلات — Laravel Log')

@section('content')
<div style="direction:rtl;font-family:'Segoe UI',Tahoma,sans-serif;">

    {{-- HERO --}}
    <div style="background:linear-gradient(135deg,#0071AA 0%,#004d77 100%);border-radius:24px;padding:2rem 2.5rem;color:#fff;position:relative;overflow:hidden;margin-bottom:1.5rem;">
        <div style="position:absolute;top:-40%;right:-10%;width:280px;height:280px;background:radial-gradient(circle,rgba(255,255,255,0.08) 0%,transparent 70%);border-radius:50%;pointer-events:none;"></div>
        <div style="position:absolute;bottom:-50%;left:5%;width:220px;height:220px;background:radial-gradient(circle,rgba(255,255,255,0.05) 0%,transparent 70%);border-radius:50%;pointer-events:none;"></div>
        <div style="position:relative;z-index:1;">
            <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;margin-bottom:1.25rem;">
                <div style="display:flex;align-items:center;gap:1rem;">
                    <div style="width:52px;height:52px;border-radius:16px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="26" height="26" fill="none" viewBox="0 0 24 24">
                            <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                  stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h1 style="font-size:1.6rem;font-weight:800;margin:0;">عارض سجلات النظام</h1>
                        <p style="opacity:.75;font-size:.875rem;margin:.2rem 0 0;">
                            laravel.log
                            @if($lastModified)
                                — آخر تعديل: {{ $lastModified->diffForHumans() }}
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div style="display:flex;gap:.75rem;flex-wrap:wrap;">
                    <a href="{{ route('admin.logs.download', ['file' => $currentFile]) }}"
                       style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.25rem;
                              background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25);
                              color:#fff;border-radius:10px;text-decoration:none;font-size:.875rem;font-weight:600;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
                            <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"
                                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        تحميل السجل
                    </a>

                    <form method="POST" action="{{ route('admin.logs.clear') }}"
                          onsubmit="return confirm('هل أنت متأكد من مسح ملف السجل بالكامل؟ لا يمكن التراجع عن هذه العملية.');">
                        @csrf
                        <input type="hidden" name="file" value="{{ $currentFile }}">
                        <button type="submit"
                                style="display:flex;align-items:center;gap:.5rem;padding:.6rem 1.25rem;
                                       background:rgba(239,68,68,.25);border:1px solid rgba(239,68,68,.5);
                                       color:#fff;border-radius:10px;font-size:.875rem;font-weight:600;cursor:pointer;">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24">
                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                      stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            مسح السجل
                        </button>
                    </form>
                </div>
            </div>

            {{-- Stats chips --}}
            <div style="display:flex;flex-wrap:wrap;gap:.75rem;">
                <div style="background:rgba(255,255,255,0.15);border-radius:14px;padding:.6rem 1rem;display:flex;align-items:center;gap:.5rem;">
                    <span style="color:rgba(255,255,255,.8);font-size:.8rem;">حجم الملف:</span>
                    <span style="color:#fff;font-size:.8rem;font-weight:700;">
                        @if($fileSize > 1048576)
                            {{ number_format($fileSize / 1048576, 2) }} MB
                        @elseif($fileSize > 1024)
                            {{ number_format($fileSize / 1024, 1) }} KB
                        @else
                            {{ $fileSize }} B
                        @endif
                    </span>
                </div>
                <div style="background:rgba(255,255,255,0.15);border-radius:14px;padding:.6rem 1rem;display:flex;align-items:center;gap:.5rem;">
                    <span style="color:rgba(255,255,255,.8);font-size:.8rem;">إجمالي الأسطر:</span>
                    <span style="color:#fff;font-size:.8rem;font-weight:700;">{{ number_format($totalLines) }}</span>
                </div>
                <div style="background:rgba(255,255,255,0.15);border-radius:14px;padding:.6rem 1rem;display:flex;align-items:center;gap:.5rem;">
                    <span style="color:rgba(255,255,255,.8);font-size:.8rem;">المدخلات المعروضة:</span>
                    <span style="color:#fff;font-size:.8rem;font-weight:700;">{{ count($entries) }}</span>
                </div>
                @if($search)
                <div style="background:rgba(251,191,36,.25);border-radius:14px;padding:.6rem 1rem;">
                    <span style="color:#fef08a;font-size:.8rem;">بحث: "{{ $search }}"</span>
                </div>
                @endif
            </div>

            @if(session('success'))
            <div style="margin-top:1rem;padding:.75rem 1rem;background:rgba(255,255,255,0.15);border-radius:10px;color:#fff;font-size:.875rem;">
                ✓ {{ session('success') }}
            </div>
            @endif
        </div>
    </div>

    <div style="padding:0;max-width:1600px;margin:0 auto;">

        {{-- Level counter cards --}}
        @php
        $levelConfig = [
            'ERROR'     => ['color'=>'#ef4444','bg'=>'#fef2f2','border'=>'#fecaca','icon'=>'🔴','label'=>'خطأ'],
            'WARNING'   => ['color'=>'#d97706','bg'=>'#fffbeb','border'=>'#fde68a','icon'=>'⚠️','label'=>'تحذير'],
            'INFO'      => ['color'=>'#2563eb','bg'=>'#eff6ff','border'=>'#bfdbfe','icon'=>'ℹ️','label'=>'معلومة'],
            'DEBUG'     => ['color'=>'#6b7280','bg'=>'#f9fafb','border'=>'#e5e7eb','icon'=>'🔧','label'=>'تصحيح'],
            'CRITICAL'  => ['color'=>'#dc2626','bg'=>'#fef2f2','border'=>'#fca5a5','icon'=>'🚨','label'=>'حرج'],
            'EMERGENCY' => ['color'=>'#be123c','bg'=>'#fff1f2','border'=>'#fda4af','icon'=>'🆘','label'=>'طارئ'],
        ];
        @endphp

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:1rem;margin-bottom:1.5rem;">
            @foreach($levelConfig as $lvl => $cfg)
            @if(($levelCounts[$lvl] ?? 0) > 0 || in_array($lvl, ['ERROR','WARNING','INFO']))
            <a href="{{ request()->fullUrlWithQuery(['level' => strtolower($lvl)]) }}"
               style="background:{{ $cfg['bg'] }};border:2px solid {{ strtoupper($level)===$lvl ? $cfg['color'] : $cfg['border'] }};
                      border-radius:16px;padding:1.25rem;text-decoration:none;display:block;
                      box-shadow:{{ strtoupper($level)===$lvl ? '0 4px 14px rgba(0,0,0,.1)' : '0 1px 4px rgba(0,0,0,.05)' }};">
                <div style="font-size:1.4rem;margin-bottom:.4rem;">{{ $cfg['icon'] }}</div>
                <div style="font-size:1.5rem;font-weight:800;color:{{ $cfg['color'] }};">{{ number_format($levelCounts[$lvl] ?? 0) }}</div>
                <div style="color:#6b7280;font-size:.78rem;margin-top:.1rem;">{{ $cfg['label'] }}</div>
            </a>
            @endif
            @endforeach
            <a href="{{ route('admin.logs.index') }}"
               style="background:#f0f9ff;border:2px solid {{ $level==='all' ? '#0071AA' : '#bae6fd' }};
                      border-radius:16px;padding:1.25rem;text-decoration:none;display:block;
                      box-shadow:{{ $level==='all' ? '0 4px 14px rgba(0,113,170,.15)' : '0 1px 4px rgba(0,0,0,.05)' }};">
                <div style="font-size:1.4rem;margin-bottom:.4rem;">📋</div>
                <div style="font-size:1.5rem;font-weight:800;color:#0071AA;">{{ count($entries) }}</div>
                <div style="color:#6b7280;font-size:.78rem;margin-top:.1rem;">الكل</div>
            </a>
        </div>

        {{-- Filter bar --}}
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:1.25rem;margin-bottom:1.25rem;box-shadow:0 1px 6px rgba(0,0,0,.06);">
            <form method="GET" action="{{ route('admin.logs.index') }}"
                  style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">
                <div style="flex:2;min-width:200px;">
                    <label style="color:#6b7280;font-size:.8rem;display:block;margin-bottom:.4rem;">بحث في الرسائل</label>
                    <div style="position:relative;">
                        <input type="text" name="search" value="{{ $search }}" placeholder="ابحث عن نص في الرسالة..."
                               style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:.6rem 1rem .6rem 2.5rem;color:#111827;font-size:.875rem;box-sizing:border-box;background:#f9fafb;">
                        <svg style="position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:#9ca3af;"
                             width="16" height="16" fill="none" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
                <div style="min-width:200px;">
                    <label style="color:#6b7280;font-size:.8rem;display:block;margin-bottom:.4rem;">ملف السجل</label>
                    <select name="file" onchange="this.form.submit()" style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:.6rem 1rem;color:#111827;font-size:.875rem;background:#f9fafb;">
                        @forelse($logFiles as $f)
                            <option value="{{ $f['name'] }}" {{ $currentFile === $f['name'] ? 'selected' : '' }}>
                                {{ $f['name'] }} ({{ $f['size'] > 1048576 ? number_format($f['size']/1048576,1).' MB' : ($f['size'] > 1024 ? number_format($f['size']/1024,0).' KB' : $f['size'].' B') }})
                            </option>
                        @empty
                            <option value="">لا توجد ملفات سجل</option>
                        @endforelse
                    </select>
                </div>
                <div style="min-width:150px;">
                    <label style="color:#6b7280;font-size:.8rem;display:block;margin-bottom:.4rem;">مستوى السجل</label>
                    <select name="level" style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:.6rem 1rem;color:#111827;font-size:.875rem;background:#f9fafb;">
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
                    <label style="color:#6b7280;font-size:.8rem;display:block;margin-bottom:.4rem;">عدد المدخلات</label>
                    <select name="lines" style="width:100%;border:1px solid #d1d5db;border-radius:8px;padding:.6rem 1rem;color:#111827;font-size:.875rem;background:#f9fafb;">
                        <option value="100"  {{ $lines==100  ? 'selected' : '' }}>آخر 100</option>
                        <option value="250"  {{ $lines==250  ? 'selected' : '' }}>آخر 250</option>
                        <option value="500"  {{ $lines==500  ? 'selected' : '' }}>آخر 500</option>
                        <option value="1000" {{ $lines==1000 ? 'selected' : '' }}>آخر 1000</option>
                        <option value="2000" {{ $lines==2000 ? 'selected' : '' }}>آخر 2000</option>
                    </select>
                </div>
                <div style="display:flex;gap:.5rem;align-items:flex-end;">
                    <button type="submit"
                            style="padding:.65rem 1.5rem;background:linear-gradient(135deg,#0071AA,#004d77);border:none;border-radius:8px;color:white;font-size:.875rem;font-weight:600;cursor:pointer;">
                        تصفية
                    </button>
                    <a href="{{ route('admin.logs.index') }}"
                       style="padding:.65rem 1.25rem;background:#f3f4f6;border:1px solid #e5e7eb;border-radius:8px;color:#6b7280;font-size:.875rem;text-decoration:none;display:inline-block;">
                        مسح
                    </a>
                </div>
            </form>
        </div>

        {{-- Log entries table --}}
        <div id="logTable" style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 1px 6px rgba(0,0,0,.06);">
            <div style="padding:1rem 1.5rem;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
                <h3 style="color:#111827;font-size:1rem;font-weight:700;margin:0;">
                    سجلات النظام
                    <span style="background:#e0f2fe;color:#0071AA;padding:.2rem .6rem;border-radius:6px;font-size:.75rem;font-weight:600;margin-right:.5rem;">
                        {{ count($entries) }} مدخل
                    </span>
                </h3>
                <div style="display:flex;gap:.5rem;">
                    <button onclick="toggleAll(true)"
                            style="padding:.35rem .85rem;background:#e0f2fe;border:1px solid #bae6fd;color:#0071AA;border-radius:6px;font-size:.75rem;cursor:pointer;font-weight:600;">
                        توسيع الكل
                    </button>
                    <button onclick="toggleAll(false)"
                            style="padding:.35rem .85rem;background:#f3f4f6;border:1px solid #e5e7eb;color:#6b7280;border-radius:6px;font-size:.75rem;cursor:pointer;">
                        طي الكل
                    </button>
                </div>
            </div>

            @if(count($entries) === 0)
            <div style="padding:4rem;text-align:center;">
                <div style="font-size:3rem;margin-bottom:1rem;">📭</div>
                <p style="color:#9ca3af;font-size:1rem;">لا توجد مدخلات تطابق معايير البحث</p>
                @if(!File::exists(storage_path('logs/laravel.log')))
                <p style="color:#d1d5db;font-size:.875rem;margin-top:.5rem;">ملف laravel.log غير موجود</p>
                @endif
            </div>
            @else

            <div style="max-height:75vh;overflow-y:auto;" id="logScroll">
                @foreach($entries as $i => $entry)
                @php
                $lvl = strtoupper($entry['level']);
                $cfg = match($lvl) {
                    'ERROR'     => ['c'=>'#ef4444','bg'=>'#fff5f5','icon'=>'🔴','badge'=>'#fee2e2','badgeText'=>'#dc2626'],
                    'CRITICAL'  => ['c'=>'#dc2626','bg'=>'#fff5f5','icon'=>'🚨','badge'=>'#fee2e2','badgeText'=>'#b91c1c'],
                    'EMERGENCY' => ['c'=>'#be123c','bg'=>'#fff1f2','icon'=>'🆘','badge'=>'#ffe4e6','badgeText'=>'#9f1239'],
                    'ALERT'     => ['c'=>'#e11d48','bg'=>'#fff1f2','icon'=>'📢','badge'=>'#ffe4e6','badgeText'=>'#be123c'],
                    'WARNING'   => ['c'=>'#d97706','bg'=>'#fffdf0','icon'=>'⚠️','badge'=>'#fef3c7','badgeText'=>'#b45309'],
                    'NOTICE'    => ['c'=>'#0891b2','bg'=>'#f0fdfa','icon'=>'📝','badge'=>'#cffafe','badgeText'=>'#0e7490'],
                    'INFO'      => ['c'=>'#2563eb','bg'=>'#f0f9ff','icon'=>'ℹ️','badge'=>'#dbeafe','badgeText'=>'#1d4ed8'],
                    'DEBUG'     => ['c'=>'#9ca3af','bg'=>'#f9fafb','icon'=>'🔧','badge'=>'#f3f4f6','badgeText'=>'#6b7280'],
                    default     => ['c'=>'#d1d5db','bg'=>'#f9fafb','icon'=>'📄','badge'=>'#f3f4f6','badgeText'=>'#6b7280'],
                };
                $hasDetails = !empty($entry['context']) || !empty($entry['extra']);
                @endphp

                <div class="log-entry" style="border-bottom:1px solid #f1f5f9;"
                     data-full="{{ json_encode([
                        'level'       => $lvl,
                        'icon'        => $cfg['icon'],
                        'datetime'    => $entry['datetime'],
                        'environment' => $entry['environment'],
                        'message'     => $entry['message'],
                        'context'     => $entry['context'] ?? '',
                        'extra'       => trim($entry['extra'] ?? ''),
                     ], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_APOS) }}">
                    <div onclick="{{ $hasDetails ? 'toggleEntry(this)' : '' }}"
                         style="display:flex;align-items:flex-start;gap:1rem;padding:.875rem 1.5rem;
                                background:{{ $cfg['bg'] }};border-right:3px solid {{ $cfg['c'] }};
                                {{ $hasDetails ? 'cursor:pointer;' : '' }}transition:background .15s;">
                        <span style="flex-shrink:0;padding:.2rem .6rem;border-radius:6px;background:{{ $cfg['badge'] }};color:{{ $cfg['badgeText'] }};font-size:.68rem;font-weight:700;font-family:monospace;min-width:80px;text-align:center;margin-top:.1rem;">
                            {{ $cfg['icon'] }} {{ $lvl }}
                        </span>
                        <span style="flex-shrink:0;color:#9ca3af;font-size:.73rem;font-family:monospace;min-width:155px;margin-top:.15rem;">
                            {{ $entry['datetime'] }}
                        </span>
                        <span style="color:#374151;font-size:.86rem;flex:1;word-break:break-all;line-height:1.5;">
                            {{ Str::limit($entry['message'], 200) }}
                        </span>
                        <span style="flex-shrink:0;color:#d1d5db;font-size:.68rem;font-family:monospace;margin-top:.2rem;">
                            {{ $entry['environment'] }}
                        </span>
                        <button type="button" onclick="event.stopPropagation();showFullLog(this.closest('.log-entry'))"
                                style="flex-shrink:0;padding:.25rem .7rem;background:#0071AA;color:#fff;border:none;border-radius:6px;font-size:.7rem;font-weight:700;cursor:pointer;margin-top:.05rem;white-space:nowrap;">
                            عرض كامل
                        </button>
                        @if($hasDetails)
                        <span class="toggle-icon" style="flex-shrink:0;color:#0071AA;font-size:.75rem;margin-top:.15rem;transition:transform .2s;">▼</span>
                        @endif
                    </div>

                    @if($hasDetails)
                    <div class="entry-details" style="display:none;background:#f8fafc;padding:1rem 1.5rem 1.25rem;border-right:3px solid {{ $cfg['c'] }};">
                        @if(!empty($entry['message']) && strlen($entry['message']) > 200)
                        <div style="margin-bottom:1rem;">
                            <div style="color:#9ca3af;font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem;">الرسالة الكاملة</div>
                            <pre style="color:#374151;font-size:.8rem;font-family:'Courier New',monospace;white-space:pre-wrap;word-break:break-all;margin:0;background:#fff;padding:.75rem;border-radius:8px;border:1px solid #e5e7eb;">{{ $entry['message'] }}</pre>
                        </div>
                        @endif
                        @if(!empty($entry['context']))
                        <div style="margin-bottom:1rem;">
                            <div style="color:#9ca3af;font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem;">السياق (Context)</div>
                            <pre style="color:#1d4ed8;font-size:.78rem;font-family:'Courier New',monospace;white-space:pre-wrap;word-break:break-all;margin:0;background:#eff6ff;padding:.75rem;border-radius:8px;border:1px solid #bfdbfe;max-height:300px;overflow-y:auto;">{{ $entry['context'] }}</pre>
                        </div>
                        @endif
                        @if(!empty($entry['extra']))
                        <div>
                            <div style="color:#9ca3af;font-size:.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem;">Stack Trace</div>
                            <pre style="color:#6b7280;font-size:.75rem;font-family:'Courier New',monospace;white-space:pre-wrap;word-break:break-all;margin:0;background:#f9fafb;padding:.75rem;border-radius:8px;border:1px solid #e5e7eb;max-height:400px;overflow-y:auto;">{{ trim($entry['extra']) }}</pre>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div style="text-align:center;margin-top:1.25rem;color:#9ca3af;font-size:.8rem;">
            يعرض آخر {{ count($entries) }} مدخل من ملف
            <code style="background:#f3f4f6;padding:.15rem .4rem;border-radius:4px;color:#6b7280;">storage/logs/{{ $currentFile }}</code>
        </div>
    </div>
</div>

{{-- Full log modal --}}
<div id="fullLogModal" onclick="if(event.target===this)closeFullLog()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.55);z-index:2000;align-items:center;justify-content:center;padding:1.5rem;">
    <div style="background:#fff;border-radius:16px;width:100%;max-width:900px;max-height:88vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,.3);overflow:hidden;">
        <div style="padding:1rem 1.5rem;border-bottom:1px solid #e5e7eb;display:flex;align-items:center;justify-content:space-between;gap:1rem;">
            <h3 id="flmTitle" style="margin:0;font-size:1rem;font-weight:800;color:#111827;">السجل الكامل</h3>
            <div style="display:flex;gap:.5rem;">
                <button type="button" onclick="copyFullLog()" style="padding:.4rem .9rem;background:#e0f2fe;border:1px solid #bae6fd;color:#0071AA;border-radius:8px;font-size:.78rem;font-weight:700;cursor:pointer;">نسخ</button>
                <button type="button" onclick="closeFullLog()" style="width:32px;height:32px;background:#f3f4f6;border:none;border-radius:8px;color:#6b7280;font-size:18px;cursor:pointer;">×</button>
            </div>
        </div>
        <div style="padding:1.25rem 1.5rem;overflow-y:auto;">
            <div id="flmMeta" style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem;font-family:monospace;font-size:.75rem;"></div>
            <pre id="flmBody" style="white-space:pre-wrap;word-break:break-word;margin:0;font-family:'Courier New',monospace;font-size:.8rem;line-height:1.55;color:#111827;background:#f8fafc;border:1px solid #e5e7eb;border-radius:10px;padding:1rem;"></pre>
        </div>
    </div>
</div>

<script>
let _fullLogText = '';
function showFullLog(entryEl) {
    const d = JSON.parse(entryEl.getAttribute('data-full'));
    document.getElementById('flmTitle').textContent = `${d.icon} ${d.level} — ${d.datetime}`;
    document.getElementById('flmMeta').innerHTML =
        `<span style="background:#dbeafe;color:#1d4ed8;padding:.2rem .6rem;border-radius:6px;font-weight:700;">${d.level}</span>` +
        `<span style="background:#f3f4f6;color:#6b7280;padding:.2rem .6rem;border-radius:6px;">${d.datetime}</span>` +
        `<span style="background:#f3f4f6;color:#6b7280;padding:.2rem .6rem;border-radius:6px;">${d.environment}</span>`;

    let parts = [d.message];
    if (d.context) parts.push('\n── Context ──\n' + d.context);
    if (d.extra)   parts.push('\n── Stack Trace ──\n' + d.extra);
    _fullLogText = parts.join('\n');
    document.getElementById('flmBody').textContent = _fullLogText;
    document.getElementById('fullLogModal').style.display = 'flex';
}
function closeFullLog() {
    document.getElementById('fullLogModal').style.display = 'none';
}
function copyFullLog() {
    navigator.clipboard?.writeText(_fullLogText);
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeFullLog(); });

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
