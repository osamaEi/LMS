@extends('layouts.dashboard')

@section('title', 'إعدادات النظام')

@section('content')
<div style="direction:rtl;font-family:'Segoe UI',sans-serif;" x-data="{ activeTab: 'general' }">

    {{-- Hero --}}
    <div style="background:linear-gradient(135deg,#0f172a 0%,#1e293b 60%,#0f172a 100%);border-radius:20px;padding:32px 28px;margin-bottom:26px;position:relative;overflow:hidden;">
        <div style="position:absolute;top:-50px;left:-50px;width:200px;height:200px;background:rgba(0,113,170,0.15);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-60px;right:15%;width:240px;height:240px;background:rgba(99,102,241,0.08);border-radius:50%;"></div>

        <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px;">
            <div style="display:flex;align-items:center;gap:14px;">
                <div style="width:52px;height:52px;background:linear-gradient(135deg,#0071AA,#005a88);border-radius:14px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 20px rgba(0,113,170,0.4);flex-shrink:0;">
                    <svg width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 style="color:white;font-size:22px;font-weight:700;margin:0;">إعدادات النظام</h1>
                    <p style="color:rgba(255,255,255,0.55);font-size:13px;margin:3px 0 0;">إدارة وتخصيص إعدادات التطبيق والنظام</p>
                </div>
            </div>
            {{-- Quick Info Chips --}}
            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                @php
                    $totalSettings = collect($settings)->flatten(1)->count();
                @endphp
                <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);padding:7px 14px;border-radius:20px;">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.7)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span style="color:rgba(255,255,255,0.75);font-size:12px;">{{ $totalSettings }} إعداد</span>
                </div>
                <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,0.08);border:1px solid rgba(255,255,255,0.12);padding:7px 14px;border-radius:20px;">
                    <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="rgba(255,255,255,0.7)" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span style="color:rgba(255,255,255,0.75);font-size:12px;">{{ count($settings) }} أقسام</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-right:4px solid #22c55e;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span style="color:#15803d;font-size:14px;font-weight:500;">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fff1f2;border:1px solid #fecaca;border-right:4px solid #ef4444;border-radius:12px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#dc2626" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span style="color:#dc2626;font-size:14px;font-weight:500;">{{ session('error') }}</span>
    </div>
    @endif

    <div style="display:flex;gap:20px;align-items:flex-start;">

        {{-- ── Sidebar ── --}}
        <div style="width:250px;flex-shrink:0;position:sticky;top:20px;">
            <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.06);">

                {{-- Sidebar Header --}}
                <div style="padding:16px 18px;background:linear-gradient(135deg,#f8f9fa,#f0f4f8);border-bottom:1px solid #e5e7eb;">
                    <p style="font-size:11px;font-weight:700;color:#9ca3af;letter-spacing:1px;text-transform:uppercase;margin:0;">أقسام الإعدادات</p>
                </div>

                {{-- Tab Buttons --}}
                @php
                $tabs = [
                    ['id'=>'general',       'label'=>'الإعدادات العامة',    'grad'=>'#0071AA,#005a88', 'light'=>'#e0f2fe','text'=>'#0071AA','icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['id'=>'contact',       'label'=>'بيانات التواصل',      'grad'=>'#059669,#047857', 'light'=>'#dcfce7','text'=>'#059669','icon'=>'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
                    ['id'=>'social',        'label'=>'وسائل التواصل',       'grad'=>'#7c3aed,#6d28d9', 'light'=>'#ede9fe','text'=>'#7c3aed','icon'=>'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z'],
                    ['id'=>'email',         'label'=>'إعدادات البريد',      'grad'=>'#ea580c,#c2410c', 'light'=>'#ffedd5','text'=>'#ea580c','icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['id'=>'notifications', 'label'=>'الإشعارات',           'grad'=>'#d97706,#b45309', 'light'=>'#fef3c7','text'=>'#d97706','icon'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                    ['id'=>'security',      'label'=>'الأمان',              'grad'=>'#dc2626,#b91c1c', 'light'=>'#fee2e2','text'=>'#dc2626','icon'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                    ['id'=>'zoom',          'label'=>'Zoom',                'grad'=>'#2563eb,#1d4ed8', 'light'=>'#dbeafe','text'=>'#2563eb','icon'=>'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                ];
                @endphp

                <nav style="padding:8px;">
                    @foreach($tabs as $tab)
                    <button @click="activeTab = '{{ $tab['id'] }}'"
                            x-bind:style="activeTab === '{{ $tab['id'] }}' ? 'background:linear-gradient(135deg,{{ $tab['grad'] }});color:white;box-shadow:0 2px 8px rgba(0,0,0,0.15);' : 'background:transparent;color:#374151;'"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;border:none;cursor:pointer;font-size:13px;font-weight:600;text-align:right;transition:all .15s;margin-bottom:3px;">
                        <div x-bind:style="activeTab === '{{ $tab['id'] }}' ? 'background:rgba(255,255,255,0.2)' : 'background:{{ $tab['light'] }}'"
                             style="width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .15s;">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24"
                                 x-bind:stroke="activeTab === '{{ $tab['id'] }}' ? 'white' : '{{ $tab['text'] }}'"
                                 stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $tab['icon'] }}"/>
                            </svg>
                        </div>
                        <span>{{ $tab['label'] }}</span>
                    </button>
                    @endforeach
                </nav>

                {{-- Cache Clear --}}
                <div style="padding:12px;border-top:1px solid #f3f4f6;">
                    <form action="{{ route('admin.settings.clear-cache') }}" method="POST">
                        @csrf
                        <button type="submit"
                                style="width:100%;display:flex;align-items:center;justify-content:center;gap:7px;padding:10px;background:#f3f4f6;border:none;border-radius:10px;font-size:12px;font-weight:600;color:#6b7280;cursor:pointer;transition:all .15s;"
                                onmouseover="this.style.background='#fee2e2';this.style.color='#dc2626'" onmouseout="this.style.background='#f3f4f6';this.style.color='#6b7280'">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            مسح الذاكرة المؤقتة
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── Content Area ── --}}
        <div style="flex:1;min-width:0;">

            {{-- GENERAL --}}
            <div x-show="activeTab === 'general'" x-cloak>
                @include('admin.settings-partials.tab', [
                    'tabTitle' => 'الإعدادات العامة', 'tabDesc' => 'إعدادات التطبيق الأساسية والمعلومات العامة',
                    'tabIcon'  => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                    'tabColor' => '#0071AA', 'tabGrad' => '#0071AA,#005a88', 'tabLight' => '#e0f2fe',
                    'group' => 'general', 'settingsGroup' => $settings['general'] ?? [],
                ])
            </div>

            {{-- CONTACT --}}
            <div x-show="activeTab === 'contact'" x-cloak>
                @include('admin.settings-partials.tab', [
                    'tabTitle' => 'بيانات التواصل', 'tabDesc' => 'معلومات الاتصال والموقع الجغرافي',
                    'tabIcon'  => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
                    'tabColor' => '#059669', 'tabGrad' => '#059669,#047857', 'tabLight' => '#dcfce7',
                    'group' => 'contact', 'settingsGroup' => $settings['contact'] ?? [],
                ])
            </div>

            {{-- SOCIAL --}}
            <div x-show="activeTab === 'social'" x-cloak>
                @include('admin.settings-partials.tab', [
                    'tabTitle' => 'وسائل التواصل الاجتماعي', 'tabDesc' => 'روابط حسابات المنصة على وسائل التواصل الاجتماعي',
                    'tabIcon'  => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z',
                    'tabColor' => '#7c3aed', 'tabGrad' => '#7c3aed,#6d28d9', 'tabLight' => '#ede9fe',
                    'group' => 'social', 'settingsGroup' => $settings['social'] ?? [],
                ])
            </div>

            {{-- EMAIL --}}
            <div x-show="activeTab === 'email'" x-cloak>
                <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.06);">
                    {{-- Card Header --}}
                    <div style="padding:20px 24px;border-bottom:1px solid #f3f4f6;background:linear-gradient(135deg,#fff7ed,#fffbeb);">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:40px;height:40px;background:linear-gradient(135deg,#ea580c,#c2410c);border-radius:10px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 10px rgba(234,88,12,0.3);">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h2 style="font-size:16px;font-weight:700;color:#111827;margin:0;">إعدادات البريد الإلكتروني</h2>
                                <p style="font-size:13px;color:#9ca3af;margin:2px 0 0;">إعدادات SMTP لإرسال رسائل البريد الإلكتروني</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('admin.settings.update-group', 'email') }}" method="POST" style="padding:24px;">
                        @csrf
                        @method('PUT')
                        <div style="display:flex;flex-direction:column;gap:18px;">
                            @foreach($settings['email'] ?? [] as $setting)
                                @include('admin.settings-partials.field', ['setting' => $setting, 'accentColor' => '#ea580c'])
                            @endforeach
                        </div>
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding-top:20px;margin-top:20px;border-top:1px solid #f3f4f6;">
                            <button type="button" id="test-email-btn" onclick="testEmail()"
                                    style="display:inline-flex;align-items:center;gap:7px;padding:10px 18px;background:linear-gradient(135deg,#059669,#047857);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;box-shadow:0 2px 8px rgba(5,150,105,0.3);">
                                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                إرسال بريد تجريبي
                            </button>
                            <button type="submit" style="padding:10px 24px;background:linear-gradient(135deg,#ea580c,#c2410c);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 3px 10px rgba(234,88,12,0.3);">
                                حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
                <div id="email-test-result" style="margin-top:12px;display:none;"></div>
            </div>

            {{-- NOTIFICATIONS --}}
            <div x-show="activeTab === 'notifications'" x-cloak>
                @include('admin.settings-partials.tab', [
                    'tabTitle' => 'إعدادات الإشعارات', 'tabDesc' => 'تحكم في أنواع الإشعارات المرسلة للمستخدمين',
                    'tabIcon'  => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                    'tabColor' => '#d97706', 'tabGrad' => '#d97706,#b45309', 'tabLight' => '#fef3c7',
                    'group' => 'notifications', 'settingsGroup' => $settings['notifications'] ?? [],
                ])
            </div>

            {{-- SECURITY --}}
            <div x-show="activeTab === 'security'" x-cloak>
                @include('admin.settings-partials.tab', [
                    'tabTitle' => 'إعدادات الأمان', 'tabDesc' => 'إعدادات كلمات المرور والجلسات والتحقق',
                    'tabIcon'  => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
                    'tabColor' => '#dc2626', 'tabGrad' => '#dc2626,#b91c1c', 'tabLight' => '#fee2e2',
                    'group' => 'security', 'settingsGroup' => $settings['security'] ?? [],
                ])
            </div>

            {{-- ZOOM --}}
            <div x-show="activeTab === 'zoom'" x-cloak>
                <div style="background:white;border-radius:16px;border:1px solid #e5e7eb;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.06);">
                    <div style="padding:20px 24px;border-bottom:1px solid #f3f4f6;background:linear-gradient(135deg,#eff6ff,#eef2ff);">
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div style="width:40px;height:40px;background:linear-gradient(135deg,#2563eb,#1d4ed8);border-radius:10px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 10px rgba(37,99,235,0.3);">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h2 style="font-size:16px;font-weight:700;color:#111827;margin:0;">إعدادات Zoom</h2>
                                <p style="font-size:13px;color:#9ca3af;margin:2px 0 0;">مفاتيح API والتكامل مع منصة Zoom للاجتماعات</p>
                            </div>
                        </div>
                    </div>

                    {{-- Info Banner --}}
                    <div style="margin:20px 24px 0;padding:14px 16px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:12px;display:flex;align-items:flex-start;gap:10px;">
                        <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2" style="flex-shrink:0;margin-top:1px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p style="font-size:13px;color:#1e40af;margin:0;line-height:1.6;">
                            احصل على هذه المفاتيح من لوحة تحكم <strong>Zoom Marketplace</strong> بعد إنشاء تطبيق من نوع Server-to-Server OAuth.
                        </p>
                    </div>

                    <form action="{{ route('admin.settings.update-group', 'zoom') }}" method="POST" style="padding:24px;">
                        @csrf
                        @method('PUT')
                        <div style="display:flex;flex-direction:column;gap:18px;">
                            @foreach($settings['zoom'] ?? [] as $setting)
                                @include('admin.settings-partials.field', ['setting' => $setting, 'isPassword' => str_contains($setting['key'], 'secret'), 'accentColor' => '#2563eb'])
                            @endforeach
                        </div>
                        <div style="display:flex;justify-content:flex-end;padding-top:20px;margin-top:20px;border-top:1px solid #f3f4f6;">
                            <button type="submit" style="padding:10px 24px;background:linear-gradient(135deg,#2563eb,#1d4ed8);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;box-shadow:0 3px 10px rgba(37,99,235,0.3);">
                                حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>{{-- end content --}}
    </div>
</div>

<script>
function testEmail() {
    const btn = document.getElementById('test-email-btn');
    const result = document.getElementById('email-test-result');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin" width="15" height="15" fill="none" viewBox="0 0 24 24"><circle style="opacity:.25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path style="opacity:.75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> جاري الإرسال...';
    fetch('{{ route('admin.settings.test-email') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        result.style.display = 'block';
        const color = data.success ? '#059669' : '#dc2626';
        const bg    = data.success ? '#f0fdf4' : '#fff1f2';
        const bdr   = data.success ? '#bbf7d0' : '#fecaca';
        const icon  = data.success
            ? '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>'
            : '<path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>';
        result.innerHTML = `<div style="padding:14px 16px;background:${bg};border:1px solid ${bdr};border-radius:12px;display:flex;align-items:center;gap:10px;"><svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="${color}" stroke-width="2">${icon}</svg><span style="color:${color};font-size:14px;font-weight:500;">${data.message}</span></div>`;
    })
    .catch(() => {
        result.style.display = 'block';
        result.innerHTML = '<div style="padding:14px 16px;background:#fff1f2;border:1px solid #fecaca;border-radius:12px;color:#dc2626;font-size:14px;">حدث خطأ أثناء الاتصال بالخادم</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> إرسال بريد تجريبي';
    });
}
</script>
@endsection
