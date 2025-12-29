@extends('layouts.dashboard')

@section('title', 'إعدادات النظام')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إعدادات النظام</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة إعدادات النظام العامة</p>
</div>

@if(session('success'))
<div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 dark:bg-green-900/20 dark:border-green-800">
    <div class="flex items-center gap-3">
        <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 dark:bg-red-900/20 dark:border-red-800">
    <div class="flex items-center gap-3">
        <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
    </div>
</div>
@endif

<div x-data="{ activeTab: 'general' }">
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        <!-- Settings Menu -->
        <div class="lg:col-span-1">
            <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <nav class="flex flex-col p-4 space-y-2">
                    <button @click="activeTab = 'general'"
                            :class="activeTab === 'general' ? 'bg-brand-50 text-brand-600 dark:bg-brand-900 dark:text-brand-300' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800'"
                            class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        الإعدادات العامة
                    </button>
                    <button @click="activeTab = 'contact'"
                            :class="activeTab === 'contact' ? 'bg-brand-50 text-brand-600 dark:bg-brand-900 dark:text-brand-300' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800'"
                            class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        بيانات التواصل
                    </button>
                    <button @click="activeTab = 'social'"
                            :class="activeTab === 'social' ? 'bg-brand-50 text-brand-600 dark:bg-brand-900 dark:text-brand-300' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800'"
                            class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                        </svg>
                        وسائل التواصل
                    </button>
                    <button @click="activeTab = 'email'"
                            :class="activeTab === 'email' ? 'bg-brand-50 text-brand-600 dark:bg-brand-900 dark:text-brand-300' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800'"
                            class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        إعدادات البريد
                    </button>
                    <button @click="activeTab = 'notifications'"
                            :class="activeTab === 'notifications' ? 'bg-brand-50 text-brand-600 dark:bg-brand-900 dark:text-brand-300' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800'"
                            class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        الإشعارات
                    </button>
                    <button @click="activeTab = 'security'"
                            :class="activeTab === 'security' ? 'bg-brand-50 text-brand-600 dark:bg-brand-900 dark:text-brand-300' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800'"
                            class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        الأمان
                    </button>
                    <button @click="activeTab = 'zoom'"
                            :class="activeTab === 'zoom' ? 'bg-brand-50 text-brand-600 dark:bg-brand-900 dark:text-brand-300' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800'"
                            class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Zoom
                    </button>
                </nav>

                <!-- Cache Clear Button -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-800">
                    <form action="{{ route('admin.settings.clear-cache') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            مسح الذاكرة المؤقتة
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="lg:col-span-3">
            <!-- General Settings -->
            <div x-show="activeTab === 'general'" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">الإعدادات العامة</h2>
                <form action="{{ route('admin.settings.update-group', 'general') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @foreach($settings['general'] ?? [] as $setting)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $setting['label'] }}
                            @if($setting['description'])
                            <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1">{{ $setting['description'] }}</span>
                            @endif
                        </label>

                        @if($setting['type'] === 'text' || $setting['type'] === 'email')
                        <input type="{{ $setting['type'] }}"
                               name="settings[{{ $setting['key'] }}]"
                               value="{{ $setting['value'] }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">

                        @elseif($setting['type'] === 'textarea')
                        <textarea name="settings[{{ $setting['key'] }}]"
                                  rows="3"
                                  class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ $setting['value'] }}</textarea>

                        @elseif($setting['type'] === 'select')
                        <select name="settings[{{ $setting['key'] }}]"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @foreach($setting['options'] ?? [] as $optKey => $optLabel)
                            <option value="{{ $optKey }}" {{ $setting['value'] == $optKey ? 'selected' : '' }}>{{ $optLabel }}</option>
                            @endforeach
                        </select>

                        @elseif($setting['type'] === 'boolean')
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $setting['label'] }}</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="settings[{{ $setting['key'] }}]" value="0">
                                <input type="checkbox" name="settings[{{ $setting['key'] }}]" value="1" class="sr-only peer" {{ $setting['value'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 dark:peer-focus:ring-brand-800 rounded-full peer dark:bg-gray-700 peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-600"></div>
                            </label>
                        </div>

                        @elseif($setting['type'] === 'file')
                        <div class="flex items-center gap-4">
                            @if($setting['value'])
                            <img src="{{ Storage::url($setting['value']) }}" alt="{{ $setting['label'] }}" class="h-16 w-16 object-contain rounded-lg border border-gray-200 dark:border-gray-700">
                            @endif
                            <input type="file"
                                   name="settings[{{ $setting['key'] }}]"
                                   accept="image/*"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                        @endif
                    </div>
                    @endforeach

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <button type="submit" class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>

            <!-- Contact Settings -->
            <div x-show="activeTab === 'contact'" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">بيانات التواصل</h2>
                <form action="{{ route('admin.settings.update-group', 'contact') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @foreach($settings['contact'] ?? [] as $setting)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $setting['label'] }}
                            @if($setting['description'])
                            <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1">{{ $setting['description'] }}</span>
                            @endif
                        </label>

                        @if($setting['type'] === 'textarea')
                        <textarea name="settings[{{ $setting['key'] }}]"
                                  rows="3"
                                  class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">{{ $setting['value'] }}</textarea>
                        @else
                        <input type="{{ $setting['type'] === 'email' ? 'email' : 'text' }}"
                               name="settings[{{ $setting['key'] }}]"
                               value="{{ $setting['value'] }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @endif
                    </div>
                    @endforeach

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <button type="submit" class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>

            <!-- Social Media Settings -->
            <div x-show="activeTab === 'social'" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">وسائل التواصل الاجتماعي</h2>
                <form action="{{ route('admin.settings.update-group', 'social') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @foreach($settings['social'] ?? [] as $setting)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $setting['label'] }}
                            @if($setting['description'])
                            <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1">{{ $setting['description'] }}</span>
                            @endif
                        </label>
                        <div class="relative">
                            <input type="url"
                                   name="settings[{{ $setting['key'] }}]"
                                   value="{{ $setting['value'] }}"
                                   placeholder="https://"
                                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        </div>
                    </div>
                    @endforeach

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <button type="submit" class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>

            <!-- Email Settings -->
            <div x-show="activeTab === 'email'" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">إعدادات البريد الإلكتروني</h2>
                <form action="{{ route('admin.settings.update-group', 'email') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @foreach($settings['email'] ?? [] as $setting)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $setting['label'] }}
                            @if($setting['description'])
                            <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1">{{ $setting['description'] }}</span>
                            @endif
                        </label>

                        @if($setting['type'] === 'select')
                        <select name="settings[{{ $setting['key'] }}]"
                                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                            @foreach($setting['options'] ?? [] as $optKey => $optLabel)
                            <option value="{{ $optKey }}" {{ $setting['value'] == $optKey ? 'selected' : '' }}>{{ $optLabel }}</option>
                            @endforeach
                        </select>
                        @elseif($setting['type'] === 'number')
                        <input type="number"
                               name="settings[{{ $setting['key'] }}]"
                               value="{{ $setting['value'] }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @elseif($setting['key'] === 'smtp_password')
                        <input type="password"
                               name="settings[{{ $setting['key'] }}]"
                               value="{{ $setting['value'] }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @else
                        <input type="{{ $setting['type'] === 'email' ? 'email' : 'text' }}"
                               name="settings[{{ $setting['key'] }}]"
                               value="{{ $setting['value'] }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @endif
                    </div>
                    @endforeach

                    <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <button type="button"
                                onclick="testEmail()"
                                class="rounded-lg border border-gray-300 px-6 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                            اختبار الإرسال
                        </button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notifications Settings -->
            <div x-show="activeTab === 'notifications'" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">إعدادات الإشعارات</h2>
                <form action="{{ route('admin.settings.update-group', 'notifications') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @foreach($settings['notifications'] ?? [] as $setting)
                    <div>
                        @if($setting['type'] === 'boolean')
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $setting['label'] }}</p>
                                @if($setting['description'])
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $setting['description'] }}</p>
                                @endif
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="settings[{{ $setting['key'] }}]" value="0">
                                <input type="checkbox" name="settings[{{ $setting['key'] }}]" value="1" class="sr-only peer" {{ $setting['value'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 dark:peer-focus:ring-brand-800 rounded-full peer dark:bg-gray-700 peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-600"></div>
                            </label>
                        </div>
                        @else
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $setting['label'] }}
                            @if($setting['description'])
                            <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1">{{ $setting['description'] }}</span>
                            @endif
                        </label>
                        <input type="number"
                               name="settings[{{ $setting['key'] }}]"
                               value="{{ $setting['value'] }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @endif
                    </div>
                    @endforeach

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <button type="submit" class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>

            <!-- Security Settings -->
            <div x-show="activeTab === 'security'" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">إعدادات الأمان</h2>
                <form action="{{ route('admin.settings.update-group', 'security') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @foreach($settings['security'] ?? [] as $setting)
                    <div>
                        @if($setting['type'] === 'boolean')
                        <div class="flex items-center justify-between rounded-lg border border-gray-200 p-4 dark:border-gray-800">
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $setting['label'] }}</p>
                                @if($setting['description'])
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $setting['description'] }}</p>
                                @endif
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="settings[{{ $setting['key'] }}]" value="0">
                                <input type="checkbox" name="settings[{{ $setting['key'] }}]" value="1" class="sr-only peer" {{ $setting['value'] ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 dark:peer-focus:ring-brand-800 rounded-full peer dark:bg-gray-700 peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-brand-600"></div>
                            </label>
                        </div>
                        @else
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $setting['label'] }}
                            @if($setting['description'])
                            <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1">{{ $setting['description'] }}</span>
                            @endif
                        </label>
                        <input type="number"
                               name="settings[{{ $setting['key'] }}]"
                               value="{{ $setting['value'] }}"
                               min="1"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @endif
                    </div>
                    @endforeach

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <button type="submit" class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>

            <!-- Zoom Settings -->
            <div x-show="activeTab === 'zoom'" class="rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">إعدادات Zoom</h2>
                <div class="mb-6 rounded-lg bg-blue-50 border border-blue-200 p-4 dark:bg-blue-900/20 dark:border-blue-800">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">للحصول على مفاتيح Zoom</p>
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">قم بزيارة <a href="https://marketplace.zoom.us/" target="_blank" class="underline">Zoom App Marketplace</a> وإنشاء تطبيق Server-to-Server OAuth</p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('admin.settings.update-group', 'zoom') }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @foreach($settings['zoom'] ?? [] as $setting)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $setting['label'] }}
                            @if($setting['description'])
                            <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1">{{ $setting['description'] }}</span>
                            @endif
                        </label>
                        @if(str_contains($setting['key'], 'secret'))
                        <input type="password"
                               name="settings[{{ $setting['key'] }}]"
                               value="{{ $setting['value'] }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @else
                        <input type="text"
                               name="settings[{{ $setting['key'] }}]"
                               value="{{ $setting['value'] }}"
                               class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-brand-500 focus:ring-2 focus:ring-brand-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                        @endif
                    </div>
                    @endforeach

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-800">
                        <button type="submit" class="rounded-lg bg-brand-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function testEmail() {
    if (confirm('سيتم إرسال بريد اختباري للتأكد من الإعدادات. هل تريد المتابعة؟')) {
        fetch('{{ route("admin.settings.test-email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('تم إرسال البريد الاختباري بنجاح!');
            } else {
                alert('فشل إرسال البريد: ' + data.message);
            }
        })
        .catch(error => {
            alert('حدث خطأ: ' + error.message);
        });
    }
}
</script>
@endpush
@endsection
