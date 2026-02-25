@extends('layouts.dashboard')

@section('title', 'إعدادات النظام')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2.5 rounded-xl shadow-lg" style="background:linear-gradient(135deg,#0071AA,#005a88)">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">إعدادات النظام</h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400 mr-14">إدارة وتخصيص إعدادات التطبيق والنظام</p>
        </div>

        <!-- Alerts -->
        @if(session('success'))
        <div class="mb-6 border-r-4 border-green-500 text-green-800 dark:text-green-400 px-6 py-4 rounded-lg shadow-sm flex items-start gap-3"
             style="background:rgba(34,197,94,.08)">
            <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
        @endif

        @if(session('error'))
        <div class="mb-6 border-r-4 border-red-500 text-red-800 dark:text-red-400 px-6 py-4 rounded-lg shadow-sm flex items-start gap-3"
             style="background:rgba(239,68,68,.08)">
            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="font-medium">{{ session('error') }}</p>
        </div>
        @endif

        <div x-data="{ activeTab: 'general' }">
            <div class="flex flex-col lg:flex-row gap-6">

                <!-- ─── Sidebar ─────────────────────────────── -->
                <div class="lg:w-72 shrink-0">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden sticky top-6">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700"
                             style="background:linear-gradient(135deg,rgba(0,113,170,.06),rgba(0,90,136,.04))">
                            <h3 class="text-sm font-bold text-gray-900 dark:text-white">أقسام الإعدادات</h3>
                        </div>

                        <nav class="p-3 space-y-1">
                            @php
                            $tabs = [
                                ['id'=>'general',       'label'=>'الإعدادات العامة',    'icon'=>'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                                ['id'=>'contact',       'label'=>'بيانات التواصل',      'icon'=>'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z'],
                                ['id'=>'social',        'label'=>'وسائل التواصل',       'icon'=>'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z'],
                                ['id'=>'email',         'label'=>'إعدادات البريد',      'icon'=>'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                                ['id'=>'notifications', 'label'=>'الإشعارات',           'icon'=>'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9'],
                                ['id'=>'security',      'label'=>'الأمان',              'icon'=>'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                                ['id'=>'zoom',          'label'=>'Zoom',                'icon'=>'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                            ];
                            @endphp

                            @foreach($tabs as $tab)
                            <button @click="activeTab = '{{ $tab['id'] }}'"
                                    :class="activeTab === '{{ $tab['id'] }}' ? 'text-white border-r-4 border-white/30' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50'"
                                    :style="activeTab === '{{ $tab['id'] }}' ? 'background:linear-gradient(135deg,#0071AA,#005a88)' : ''"
                                    class="w-full flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all">
                                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                                </svg>
                                {{ $tab['label'] }}
                            </button>
                            @endforeach
                        </nav>

                        <!-- Cache Clear -->
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                            <form action="{{ route('admin.settings.clear-cache') }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    مسح الذاكرة المؤقتة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- ─── Content Area ───────────────────────── -->
                <div class="flex-1 min-w-0">

                    {{-- ══════════════ GENERAL ══════════════ --}}
                    <div x-show="activeTab === 'general'" x-cloak>
                        @include('admin.settings-partials.tab', [
                            'tabTitle'    => 'الإعدادات العامة',
                            'tabDesc'     => 'إعدادات التطبيق الأساسية والمعلومات العامة',
                            'tabIcon'     => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                            'group'       => 'general',
                            'settingsGroup' => $settings['general'] ?? [],
                        ])
                    </div>

                    {{-- ══════════════ CONTACT ══════════════ --}}
                    <div x-show="activeTab === 'contact'" x-cloak>
                        @include('admin.settings-partials.tab', [
                            'tabTitle'    => 'بيانات التواصل',
                            'tabDesc'     => 'معلومات الاتصال والموقع الجغرافي',
                            'tabIcon'     => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z',
                            'group'       => 'contact',
                            'settingsGroup' => $settings['contact'] ?? [],
                        ])
                    </div>

                    {{-- ══════════════ SOCIAL ══════════════ --}}
                    <div x-show="activeTab === 'social'" x-cloak>
                        @include('admin.settings-partials.tab', [
                            'tabTitle'    => 'وسائل التواصل الاجتماعي',
                            'tabDesc'     => 'روابط حسابات المنصة على وسائل التواصل الاجتماعي',
                            'tabIcon'     => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z',
                            'group'       => 'social',
                            'settingsGroup' => $settings['social'] ?? [],
                        ])
                    </div>

                    {{-- ══════════════ EMAIL ══════════════ --}}
                    <div x-show="activeTab === 'email'" x-cloak>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700"
                                 style="background:linear-gradient(135deg,rgba(0,113,170,.05),rgba(0,90,136,.03))">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                    <div class="p-2 rounded-lg" style="background:rgba(0,113,170,.12)">
                                        <svg class="w-5 h-5" style="color:#0071AA" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    إعدادات البريد الإلكتروني
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">إعدادات SMTP لإرسال رسائل البريد الإلكتروني</p>
                            </div>

                            <form action="{{ route('admin.settings.update-group', 'email') }}" method="POST" class="p-8">
                                @csrf
                                @method('PUT')

                                <div class="space-y-6">
                                    @foreach($settings['email'] ?? [] as $setting)
                                        @include('admin.settings-partials.field', ['setting' => $setting])
                                    @endforeach
                                </div>

                                <div class="flex items-center justify-between gap-3 pt-6 mt-8 border-t border-gray-200 dark:border-gray-700">
                                    <!-- Test Email Button -->
                                    <button type="button" id="test-email-btn"
                                            onclick="testEmail()"
                                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-medium text-white transition-all"
                                            style="background:linear-gradient(135deg,#059669,#047857)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                        </svg>
                                        إرسال بريد تجريبي
                                    </button>
                                    <button type="submit"
                                            class="px-8 py-3 text-white font-semibold rounded-xl transition-all shadow-lg"
                                            style="background:linear-gradient(135deg,#0071AA,#005a88)">
                                        حفظ التغييرات
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Test Email Result -->
                        <div id="email-test-result" class="mt-4 hidden"></div>
                    </div>

                    {{-- ══════════════ NOTIFICATIONS ══════════════ --}}
                    <div x-show="activeTab === 'notifications'" x-cloak>
                        @include('admin.settings-partials.tab', [
                            'tabTitle'    => 'إعدادات الإشعارات',
                            'tabDesc'     => 'تحكم في أنواع الإشعارات المرسلة للمستخدمين',
                            'tabIcon'     => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                            'group'       => 'notifications',
                            'settingsGroup' => $settings['notifications'] ?? [],
                        ])
                    </div>

                    {{-- ══════════════ SECURITY ══════════════ --}}
                    <div x-show="activeTab === 'security'" x-cloak>
                        @include('admin.settings-partials.tab', [
                            'tabTitle'    => 'إعدادات الأمان',
                            'tabDesc'     => 'إعدادات كلمات المرور والجلسات والتحقق',
                            'tabIcon'     => 'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
                            'group'       => 'security',
                            'settingsGroup' => $settings['security'] ?? [],
                        ])
                    </div>

                    {{-- ══════════════ ZOOM ══════════════ --}}
                    <div x-show="activeTab === 'zoom'" x-cloak>
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700"
                                 style="background:linear-gradient(135deg,rgba(37,99,235,.06),rgba(29,78,216,.04))">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                                    <div class="p-2 rounded-lg" style="background:rgba(37,99,235,.12)">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    إعدادات Zoom
                                </h2>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">مفاتيح API والتكامل مع منصة Zoom للاجتماعات</p>
                            </div>

                            <!-- Zoom Info Banner -->
                            <div class="mx-8 mt-6 p-4 rounded-xl flex items-start gap-3" style="background:rgba(37,99,235,.06);border:1px solid rgba(37,99,235,.15)">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm text-blue-800 dark:text-blue-300">
                                    احصل على هذه المفاتيح من لوحة تحكم <strong>Zoom Marketplace</strong> بعد إنشاء تطبيق من نوع Server-to-Server OAuth.
                                </p>
                            </div>

                            <form action="{{ route('admin.settings.update-group', 'zoom') }}" method="POST" class="p-8">
                                @csrf
                                @method('PUT')

                                <div class="space-y-6">
                                    @foreach($settings['zoom'] ?? [] as $setting)
                                        @include('admin.settings-partials.field', ['setting' => $setting, 'isPassword' => str_contains($setting['key'], 'secret')])
                                    @endforeach
                                </div>

                                <div class="flex items-center justify-end gap-3 pt-6 mt-8 border-t border-gray-200 dark:border-gray-700">
                                    <button type="submit"
                                            class="px-8 py-3 text-white font-semibold rounded-xl transition-all shadow-lg"
                                            style="background:linear-gradient(135deg,#0071AA,#005a88)">
                                        حفظ التغييرات
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>{{-- end content area --}}
            </div>
        </div>

    </div>
</div>

<script>
function testEmail() {
    const btn = document.getElementById('test-email-btn');
    const result = document.getElementById('email-test-result');

    btn.disabled = true;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> جاري الإرسال...';

    fetch('{{ route('admin.settings.test-email') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        result.classList.remove('hidden');
        if (data.success) {
            result.innerHTML = '<div class="p-4 rounded-xl flex items-center gap-3" style="background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2)"><svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg><span class="text-green-700 font-medium">' + data.message + '</span></div>';
        } else {
            result.innerHTML = '<div class="p-4 rounded-xl flex items-center gap-3" style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2)"><svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg><span class="text-red-700 font-medium">' + data.message + '</span></div>';
        }
    })
    .catch(() => {
        result.classList.remove('hidden');
        result.innerHTML = '<div class="p-4 rounded-xl" style="background:rgba(239,68,68,.08)"><span class="text-red-700">حدث خطأ أثناء الاتصال بالخادم</span></div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> إرسال بريد تجريبي';
    });
}
</script>
@endsection
