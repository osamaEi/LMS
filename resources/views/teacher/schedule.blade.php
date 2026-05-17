@extends('layouts.dashboard')

@section('title', __('Academic Schedule'))

@section('content')
<div style="direction:rtl;font-family:'Segoe UI',sans-serif;">

{{-- ── Alerts ── --}}
@if(session('success'))
<div style="background:#f0fdf4;border:1px solid #bbf7d0;border-right:4px solid #22c55e;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
    <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <span style="color:#15803d;font-size:14px;font-weight:500;">{{ session('success') }}</span>
</div>
@endif
@if($errors->any())
<div style="background:#fff1f2;border:1px solid #fecaca;border-right:4px solid #ef4444;border-radius:12px;padding:14px 18px;margin-bottom:20px;">
    <p style="color:#dc2626;font-size:14px;font-weight:600;margin:0 0 6px;">{{ __('Please fix the following errors:') }}</p>
    <ul style="margin:0;padding-right:18px;color:#dc2626;font-size:13px;">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif

{{-- ── Hero ── --}}
<div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a5f 55%,#0071AA 100%);border-radius:20px;padding:32px 28px;margin-bottom:26px;position:relative;overflow:hidden;">
    <div style="position:absolute;top:-60px;left:-60px;width:220px;height:220px;background:rgba(255,255,255,.05);border-radius:50%;pointer-events:none;"></div>
    <div style="position:absolute;bottom:-50px;right:20%;width:180px;height:180px;background:rgba(0,113,170,.15);border-radius:50%;pointer-events:none;"></div>

    <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;">
        <div style="display:flex;align-items:center;gap:16px;">
            <div style="width:54px;height:54px;background:linear-gradient(135deg,#0071AA,#005a88);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(0,113,170,0.4);flex-shrink:0;">
                <svg width="26" height="26" fill="white" viewBox="0 0 24 24"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zM7 12h5v5H7z"/></svg>
            </div>
            <div>
                <p style="color:rgba(255,255,255,0.55);font-size:13px;margin:0 0 2px;">{{ now()->translatedFormat('l، d F Y') }}</p>
                <h1 style="color:white;font-size:22px;font-weight:700;margin:0;">{{ __('Academic Schedule') }}</h1>
                <p style="color:rgba(255,255,255,0.55);font-size:13px;margin:3px 0 0;">{{ __('Overview of all your sessions and lectures') }}</p>
            </div>
        </div>

        <div style="display:flex;gap:10px;flex-wrap:wrap;">
            @php
            $chips = [
                ['val' => $stats['total'],     'label' => __('Total'),     'color' => 'rgba(255,255,255,0.75)', 'bg' => 'rgba(255,255,255,0.1)'],
                ['val' => $stats['upcoming'],  'label' => __('Upcoming'),  'color' => '#fde68a',               'bg' => 'rgba(255,255,255,0.1)'],
                ['val' => $stats['completed'], 'label' => __('Completed'), 'color' => '#86efac',               'bg' => 'rgba(255,255,255,0.1)'],
                ['val' => $past->count(),      'label' => __('Past'),      'color' => 'rgba(255,255,255,0.6)', 'bg' => 'rgba(255,255,255,0.08)'],
            ];
            @endphp
            @foreach($chips as $chip)
            <div style="background:{{ $chip['bg'] }};border:1px solid rgba(255,255,255,0.12);border-radius:14px;padding:10px 18px;text-align:center;min-width:70px;">
                <div style="font-size:22px;font-weight:700;color:{{ $chip['color'] }};line-height:1;">{{ $chip['val'] }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,0.6);margin-top:3px;">{{ $chip['label'] }}</div>
            </div>
            @endforeach
            @if($stats['live'] > 0)
            <div style="background:rgba(239,68,68,0.7);border:1px solid rgba(239,68,68,0.4);border-radius:14px;padding:10px 18px;text-align:center;min-width:70px;">
                <div style="font-size:22px;font-weight:700;color:white;line-height:1;animation:livePulse 2s infinite;">{{ $stats['live'] }}</div>
                <div style="font-size:11px;color:rgba(255,255,255,0.85);margin-top:3px;">{{ __('Live Now') }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Main Grid ── --}}
<div style="display:flex;gap:22px;align-items:flex-start;">

    {{-- ── Upcoming Sessions (2/3) ── --}}
    <div style="flex:2;min-width:0;">

        {{-- Section Header --}}
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
            <div style="width:38px;height:38px;background:linear-gradient(135deg,#0071AA,#005a88);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="18" height="18" fill="white" viewBox="0 0 24 24"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
            </div>
            <h2 style="font-size:17px;font-weight:700;color:#111827;margin:0;">{{ __('Upcoming Sessions') }}</h2>
            @if(!$groupedUpcoming->isEmpty())
            <span style="background:#dbeafe;color:#1d4ed8;font-size:12px;font-weight:700;padding:3px 10px;border-radius:20px;">{{ $stats['upcoming'] }}</span>
            @endif
        </div>

        @if($groupedUpcoming->isEmpty())
        <div style="background:white;border-radius:16px;border:2px dashed #e5e7eb;padding:60px 24px;text-align:center;">
            <div style="width:72px;height:72px;background:#eff6ff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;">
                <svg width="32" height="32" fill="#93c5fd" viewBox="0 0 24 24"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
            </div>
            <p style="font-weight:600;color:#374151;margin:0 0 6px;font-size:15px;">{{ __('No upcoming sessions') }}</p>
            <p style="color:#9ca3af;font-size:13px;margin:0;">{{ __('Create new session') }}</p>
        </div>

        @else
            @foreach($groupedUpcoming as $dayLabel => $daySessions)

            {{-- Day Separator --}}
            <div style="display:flex;align-items:center;gap:12px;margin:{{ $loop->first ? '0' : '24px' }} 0 14px;">
                <span style="
                    display:inline-flex;align-items:center;gap:7px;
                    padding:5px 14px;border-radius:20px;font-size:13px;font-weight:700;
                    border:1.5px solid {{ $dayLabel === 'اليوم' ? '#bfdbfe' : ($dayLabel === 'غداً' ? '#ddd6fe' : '#e5e7eb') }};
                    background:{{ $dayLabel === 'اليوم' ? '#eff6ff' : ($dayLabel === 'غداً' ? '#f5f3ff' : 'white') }};
                    color:{{ $dayLabel === 'اليوم' ? '#1d4ed8' : ($dayLabel === 'غداً' ? '#7c3aed' : '#6b7280') }};
                ">
                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $dayLabel === 'اليوم' ? '#3b82f6' : ($dayLabel === 'غداً' ? '#8b5cf6' : '#d1d5db') }};{{ $dayLabel === 'اليوم' ? 'animation:livePulse 2s infinite;' : '' }}"></span>
                    {{ $dayLabel }}
                </span>
                <div style="flex:1;height:1px;background:linear-gradient(90deg,#e5e7eb,transparent);"></div>
            </div>

            <div style="display:flex;flex-direction:column;gap:10px;margin-bottom:4px;">
            @foreach($daySessions as $session)
            @php
                $dt        = \Carbon\Carbon::parse($session->scheduled_at);
                $isLive    = $session->status === 'live';
                $isToday   = $dt->isToday();
                $typeLabel = match($session->type ?? '') {
                    'live_zoom'      => 'Zoom مباشر',
                    'recorded_video' => 'مسجّل',
                    'in_person'      => 'حضوري',
                    default          => ucfirst($session->type ?? ''),
                };
                $typeColor = match($session->type ?? '') {
                    'live_zoom'      => ['bg' => '#dbeafe', 'color' => '#1d4ed8'],
                    'recorded_video' => ['bg' => '#fce7f3', 'color' => '#be185d'],
                    'in_person'      => ['bg' => '#dcfce7', 'color' => '#15803d'],
                    default          => ['bg' => '#f3f4f6', 'color' => '#4b5563'],
                };
                $accentGrad = $isLive ? '#ef4444,#dc2626' : ($isToday ? '#0071AA,#005a88' : '#94a3b8,#64748b');
                $borderColor = $isLive ? '#fca5a5' : ($isToday ? '#bfdbfe' : '#e5e7eb');
            @endphp

            <div style="background:white;border-radius:14px;border:1.5px solid {{ $borderColor }};overflow:hidden;display:flex;align-items:stretch;box-shadow:0 2px 8px rgba(0,0,0,0.05);transition:transform .15s,box-shadow .15s;"
                 onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 8px 24px rgba(0,0,0,0.09)'"
                 onmouseout="this.style.transform='';this.style.boxShadow='0 2px 8px rgba(0,0,0,0.05)'">

                {{-- Accent stripe --}}
                <div style="width:4px;flex-shrink:0;background:linear-gradient(180deg,{{ $accentGrad }});"></div>

                {{-- Time block --}}
                <div style="width:68px;flex-shrink:0;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:16px 8px;border-left:1px solid #f3f4f6;">
                    <span style="font-size:24px;font-weight:800;color:#111827;line-height:1;">{{ $dt->format('H') }}</span>
                    <span style="font-size:14px;font-weight:600;color:#6b7280;margin-top:1px;">{{ $dt->format('i') }}</span>
                    <span style="font-size:10px;color:#9ca3af;margin-top:2px;letter-spacing:.5px;">{{ $dt->format('A') === 'AM' ? 'ص' : 'م' }}</span>
                </div>

                {{-- Content --}}
                <div style="flex:1;min-width:0;padding:14px 16px;display:flex;flex-direction:column;justify-content:center;gap:8px;">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap;">
                        <div style="min-width:0;">
                            <h3 style="font-size:15px;font-weight:700;color:#111827;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $session->title ?: '(' . $typeLabel . ')' }}
                            </h3>
                            <p style="font-size:12px;color:#6b7280;margin:4px 0 0;display:flex;align-items:center;gap:5px;">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3L1 9l4 2.18V17h2v-4.68L9 13.4V17c0 2.21 1.34 4 3 4s3-1.79 3-4v-3.6l2-.92V17h2v-5.82L23 9 12 3z"/></svg>
                                {{ $session->subject->name ?? '—' }}
                            </p>
                        </div>
                        <div style="display:flex;gap:6px;flex-wrap:wrap;flex-shrink:0;">
                            @if($isLive)
                            <span style="background:#ef4444;color:white;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;display:inline-flex;align-items:center;gap:5px;">
                                <span style="width:6px;height:6px;background:white;border-radius:50%;animation:livePulse 2s infinite;"></span>مباشر
                            </span>
                            @elseif($isToday)
                            <span style="background:#dbeafe;color:#1d4ed8;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;">{{ __('Today') }}</span>
                            @endif
                            <span style="background:{{ $typeColor['bg'] }};color:{{ $typeColor['color'] }};font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">{{ $typeLabel }}</span>
                            @if($session->session_number)
                            <span style="background:#eef2ff;color:#4f46e5;font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">#{{ $session->session_number }}</span>
                            @endif
                        </div>
                    </div>
                    <p style="font-size:12px;color:#9ca3af;margin:0;">
                        {{ $dt->diffForHumans() }}
                        @if($session->duration_minutes)· {{ $session->duration_minutes }} {{ __('minutes') }}@endif
                    </p>
                </div>

                {{-- Actions --}}
                <div style="flex-shrink:0;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;padding:14px 16px;border-right:1px solid #f3f4f6;">
                    @if($session->zoom_start_url)
                    <a href="{{ $session->zoom_start_url }}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;transition:opacity .15s;"
                       onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                        {{ __('Start Session') }}
                    </a>
                    @endif
                    @if($session->zoom_join_url)
                    <a href="{{ $session->zoom_join_url }}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;transition:opacity .15s;"
                       onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                        {{ __('Join via Zoom') }}
                    </a>
                    @endif
                    @if(!$session->zoom_start_url && !$session->zoom_join_url)
                    <span style="font-size:11px;color:#9ca3af;text-align:center;padding:4px 8px;">{{ __('No Zoom link') }}</span>
                    @endif
                </div>
            </div>
            @endforeach
            </div>

            @endforeach
        @endif
    </div>

    {{-- ── Create Form Sidebar (1/3) ── --}}
    <div style="width:320px;flex-shrink:0;position:sticky;top:20px;">
        <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.06);">

            {{-- Panel Header --}}
            <div style="padding:18px 20px;background:linear-gradient(135deg,#2563eb,#1d4ed8);border-bottom:1px solid rgba(255,255,255,.1);">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:38px;height:38px;background:rgba(255,255,255,.18);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <svg width="18" height="18" fill="white" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                    </div>
                    <div>
                        <h3 style="font-size:14px;font-weight:700;color:white;margin:0;">{{ __('Create Zoom Sessions') }}</h3>
                        <p style="font-size:12px;color:rgba(255,255,255,.65);margin:2px 0 0;">أضف عدة جلسات مباشرة دفعة واحدة</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form action="{{ route('teacher.schedule.sessions.store') }}" method="POST" style="padding:18px;">
                @csrf

                <div id="sessionRows" style="display:flex;flex-direction:column;gap:12px;margin-bottom:12px;">

                    {{-- First row (static) --}}
                    <div class="session-row" data-index="0"
                         style="border-radius:12px;border:1.5px solid #bfdbfe;background:#eff6ff;padding:14px;position:relative;">
                        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                            <span class="row-label" style="font-size:12px;font-weight:700;color:#2563eb;background:#dbeafe;padding:3px 10px;border-radius:20px;display:inline-flex;align-items:center;gap:5px;">
                                <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                                Zoom 1
                            </span>
                            <button type="button" onclick="removeRow(this)"
                                    class="remove-btn" style="display:none;width:24px;height:24px;background:#fee2e2;border:none;border-radius:6px;cursor:pointer;color:#ef4444;align-items:center;justify-content:center;">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                            </button>
                        </div>

                        <div style="margin-bottom:10px;">
                            <label style="display:block;font-size:11px;font-weight:600;color:#374151;margin-bottom:4px;">{{ __('Subject') }}</label>
                            <select name="sessions[0][subject_id]" style="width:100%;border-radius:8px;border:1.5px solid #e5e7eb;background:white;padding:8px 10px;font-size:13px;color:#111827;outline:none;transition:border-color .15s;font-family:inherit;"
                                    onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e5e7eb'">
                                <option value="">— اختر المقرر  —</option>
                                @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name_ar }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="sessions[0][type]" value="live_zoom">

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
                            <div>
                                <label style="display:block;font-size:11px;font-weight:600;color:#374151;margin-bottom:4px;">{{ __('Date') }} & {{ __('Time') }}</label>
                                <input type="datetime-local" name="sessions[0][scheduled_at]"
                                       style="width:100%;border-radius:8px;border:1.5px solid #e5e7eb;background:white;padding:7px 8px;font-size:12px;color:#111827;outline:none;transition:border-color .15s;box-sizing:border-box;"
                                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>
                            <div>
                                <label style="display:block;font-size:11px;font-weight:600;color:#374151;margin-bottom:4px;">{{ __('Duration (minutes)') }}</label>
                                <input type="number" name="sessions[0][duration_minutes]" value="60" min="15" max="480" step="15"
                                       style="width:100%;border-radius:8px;border:1.5px solid #e5e7eb;background:white;padding:7px 8px;font-size:13px;color:#111827;outline:none;transition:border-color .15s;box-sizing:border-box;"
                                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e5e7eb'">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Add row --}}
                <button type="button" onclick="addRow()"
                        style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;border:1.5px dashed #bfdbfe;border-radius:10px;background:transparent;font-size:13px;font-weight:600;color:#64748b;cursor:pointer;margin-bottom:12px;transition:all .15s;"
                        onmouseover="this.style.borderColor='#2563eb';this.style.color='#2563eb';this.style.background='#eff6ff'"
                        onmouseout="this.style.borderColor='#bfdbfe';this.style.color='#64748b';this.style.background='transparent'">
                    <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/></svg>
                    {{ __('Add Row') }}
                </button>

                {{-- Submit --}}
                <button type="submit"
                        style="width:100%;display:flex;align-items:center;justify-content:center;gap:8px;padding:12px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 3px 10px rgba(37,99,235,0.35);transition:opacity .15s;"
                        onmouseover="this.style.opacity='.9'" onmouseout="this.style.opacity='1'">
                    <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                    {{ __('Save Zoom Sessions') }}
                </button>

                <p style="text-align:center;font-size:11px;color:#9ca3af;margin:10px 0 0;">رابط Zoom يُضاف لاحقاً من صفحة المقرر </p>
            </form>
        </div>
    </div>

</div>{{-- end main grid --}}

{{-- ── Past Sessions ── --}}
@if($past->isNotEmpty())
<div style="margin-top:32px;">
    <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;">
        <div style="width:38px;height:38px;background:linear-gradient(135deg,#6b7280,#4b5563);border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg width="18" height="18" fill="white" viewBox="0 0 24 24"><path d="M13 3a9 9 0 0 0-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42A8.954 8.954 0 0 0 13 21a9 9 0 0 0 0-18zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/></svg>
        </div>
        <h2 style="font-size:17px;font-weight:700;color:#111827;margin:0;">{{ __('Past') }}</h2>
        <span style="background:#f3f4f6;color:#6b7280;font-size:12px;font-weight:600;padding:3px 10px;border-radius:20px;">{{ $past->count() }}</span>
    </div>

    <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.06);">
        <table style="width:100%;border-collapse:collapse;font-size:13px;">
            <thead>
                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                    <th style="padding:12px 18px;text-align:right;font-size:12px;font-weight:600;color:#6b7280;">{{ __('Session') }}</th>
                    <th style="padding:12px 18px;text-align:right;font-size:12px;font-weight:600;color:#6b7280;">{{ __('Subject') }}</th>
                    <th style="padding:12px 18px;text-align:right;font-size:12px;font-weight:600;color:#6b7280;">{{ __('Date') }}</th>
                    <th style="padding:12px 18px;text-align:right;font-size:12px;font-weight:600;color:#6b7280;">{{ __('Subject') }}</th>
                    <th style="padding:12px 18px;text-align:right;font-size:12px;font-weight:600;color:#6b7280;">{{ __('Status') }}</th>
                    <th style="padding:12px 18px;text-align:right;font-size:12px;font-weight:600;color:#6b7280;">{{ __('Join via Zoom') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($past as $session)
                @php
                    $dt = $session->scheduled_at ? \Carbon\Carbon::parse($session->scheduled_at) : null;
                    $typeLabel = match($session->type ?? '') {
                        'live_zoom'      => 'Zoom',
                        'recorded_video' => 'مسجّل',
                        'in_person'      => 'حضوري',
                        default          => ucfirst($session->type ?? '—'),
                    };
                    $statusBadge = match($session->status ?? '') {
                        'completed' => ['label' => 'مكتملة', 'bg' => '#dcfce7', 'color' => '#16a34a'],
                        'live'      => ['label' => 'مباشر',  'bg' => '#fee2e2', 'color' => '#dc2626'],
                        'cancelled' => ['label' => 'ملغاة',  'bg' => '#f3f4f6', 'color' => '#6b7280'],
                        default     => ['label' => 'مجدولة', 'bg' => '#dbeafe', 'color' => '#1d4ed8'],
                    };
                @endphp
                <tr style="border-bottom:1px solid #f3f4f6;transition:background .1s;"
                    onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                    <td style="padding:12px 18px;">
                        <p style="font-weight:600;color:#111827;margin:0;">{{ $session->title_ar ?: ($session->title_en ?: '—') }}</p>
                        @if($session->session_number)
                        <p style="font-size:11px;color:#9ca3af;margin:2px 0 0;">#{{ $session->session_number }}</p>
                        @endif
                    </td>
                    <td style="padding:12px 18px;color:#374151;">{{ $session->subject->name_ar ?? '—' }}</td>
                    <td style="padding:12px 18px;color:#6b7280;font-size:12px;">{{ $dt ? $dt->format('Y/m/d H:i') : '—' }}</td>
                    <td style="padding:12px 18px;">
                        <span style="background:#f3f4f6;color:#4b5563;font-size:11px;padding:3px 10px;border-radius:20px;">{{ $typeLabel }}</span>
                    </td>
                    <td style="padding:12px 18px;">
                        <span style="background:{{ $statusBadge['bg'] }};color:{{ $statusBadge['color'] }};font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;">{{ $statusBadge['label'] }}</span>
                    </td>
                    <td style="padding:12px 18px;">
                        @if($session->zoom_join_url)
                        <a href="{{ $session->zoom_join_url }}" target="_blank"
                           style="display:inline-flex;align-items:center;gap:5px;padding:5px 12px;background:#2563eb;color:white;border-radius:8px;font-size:11px;font-weight:600;text-decoration:none;transition:opacity .15s;"
                           onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                            <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                            الرابط
                        </a>
                        @else
                        <span style="color:#d1d5db;font-size:13px;">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

</div>

<style>
@keyframes livePulse { 0%,100%{opacity:1} 50%{opacity:.35} }
</style>

<script>
let rowCount = 1;
const subjectOptions = `{!! $subjects->map(fn($s) => '<option value="'.$s->id.'">'.e($s->name_ar).'</option>')->join('') !!}`;

function buildRowHTML(idx, num) {
    return `
    <div class="session-row" data-index="${idx}"
         style="border-radius:12px;border:1.5px solid #bfdbfe;background:#eff6ff;padding:14px;position:relative;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
            <span class="row-label" style="font-size:12px;font-weight:700;color:#2563eb;background:#dbeafe;padding:3px 10px;border-radius:20px;display:inline-flex;align-items:center;gap:5px;">
                <svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg>
                Zoom ${num}
            </span>
            <button type="button" onclick="removeRow(this)"
                    class="remove-btn" style="display:flex;width:24px;height:24px;background:#fee2e2;border:none;border-radius:6px;cursor:pointer;color:#ef4444;align-items:center;justify-content:center;">
                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
        </div>
        <input type="hidden" name="sessions[${idx}][type]" value="live_zoom">
        <div style="margin-bottom:10px;">
            <label style="display:block;font-size:11px;font-weight:600;color:#374151;margin-bottom:4px;">المقرر </label>
            <select name="sessions[${idx}][subject_id]" style="width:100%;border-radius:8px;border:1.5px solid #bfdbfe;background:white;padding:8px 10px;font-size:13px;color:#111827;outline:none;font-family:inherit;"
                    onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#bfdbfe'">
                <option value="">— اختر المقرر  —</option>${subjectOptions}
            </select>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;">
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;margin-bottom:4px;">التاريخ والوقت</label>
                <input type="datetime-local" name="sessions[${idx}][scheduled_at]"
                       style="width:100%;border-radius:8px;border:1.5px solid #bfdbfe;background:white;padding:7px 8px;font-size:12px;color:#111827;outline:none;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#bfdbfe'">
            </div>
            <div>
                <label style="display:block;font-size:11px;font-weight:600;color:#374151;margin-bottom:4px;">المدة (دقيقة)</label>
                <input type="number" name="sessions[${idx}][duration_minutes]" value="60" min="15" max="480" step="15"
                       style="width:100%;border-radius:8px;border:1.5px solid #bfdbfe;background:white;padding:7px 8px;font-size:13px;color:#111827;outline:none;box-sizing:border-box;"
                       onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#bfdbfe'">
            </div>
        </div>
    </div>`;
}

function addRow() {
    const idx = rowCount++;
    document.getElementById('sessionRows').insertAdjacentHTML('beforeend', buildRowHTML(idx, idx + 1));
    updateRemoveButtons();
}

function removeRow(btn) {
    btn.closest('.session-row').remove();
    document.querySelectorAll('#sessionRows .row-label').forEach((el, i) => {
        el.innerHTML = `<svg width="11" height="11" fill="currentColor" viewBox="0 0 24 24"><path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/></svg> Zoom ${i + 1}`;
    });
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('#sessionRows .session-row');
    rows.forEach(row => {
        const btn = row.querySelector('.remove-btn');
        if (btn) btn.style.display = rows.length === 1 ? 'none' : 'flex';
    });
}

function openZoomApp(joinUrl) {
    try {
        const url    = new URL(joinUrl);
        const parts  = url.pathname.split('/');
        const confno = parts[parts.length - 1];
        const pwd    = url.searchParams.get('pwd') || '';
        window.location.href = 'zoommtg://zoom.us/join?confno=' + confno + (pwd ? '&pwd=' + pwd : '') + '&zc=0';
        setTimeout(() => window.open(joinUrl, '_blank'), 1500);
    } catch(e) {
        window.open(joinUrl, '_blank');
    }
}
</script>
@endsection
