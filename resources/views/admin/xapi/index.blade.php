@extends('layouts.dashboard')

@section('title', 'xAPI Dashboard')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Hero Header -->
    <div class="mb-8 overflow-hidden rounded-2xl bg-gradient-to-br from-primary via-purple-600 to-blue-600 p-8 shadow-2xl">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-xl">
                    <svg class="fill-white" width="40" height="40" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5zm0 18c-4.41 0-8-3.59-8-8V8.3l8-4 8 4V12c0 4.41-3.59 8-8 8z"/>
                        <path d="M10.5 13.5l-2.15-2.15L7 12.7l3.5 3.5 7-7-1.35-1.35z"/>
                    </svg>
                </div>
                <div class="text-white">
                    <h1 class="text-3xl font-bold mb-1">xAPI Dashboard</h1>
                    <p class="text-white/80 text-sm">Experience API • Learning Record Store Integration</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.activity-logs.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-white/20 backdrop-blur-sm py-3 px-5 text-white border-2 border-white/30 hover:bg-white/30 transition-all shadow-lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" fill="currentColor"/>
                    </svg>
                    <span class="font-medium">Activity Logs</span>
                </a>
                <a href="{{ route('admin.reports.index') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-white py-3 px-5 text-primary border-2 border-white hover:shadow-xl transition-all font-medium">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" fill="currentColor"/>
                    </svg>
                    <span>Reports</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    @if(!$configured || !$enabled)
        <div class="mb-6 overflow-hidden rounded-xl border-2 border-warning bg-gradient-to-r from-warning/10 via-orange-500/10 to-warning/10 shadow-lg animate-pulse">
            <div class="flex items-start gap-4 p-6">
                <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-xl bg-warning shadow-lg">
                    <svg class="fill-white" width="28" height="28" viewBox="0 0 24 24">
                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="mb-2 text-xl font-bold text-black dark:text-white">⚠️ Configuration Required</h3>
                    <p class="mb-4 text-sm text-gray-700 dark:text-gray-300">
                        xAPI integration is not fully configured. Complete the setup below to enable learning activity tracking and NELC compliance.
                    </p>
                    <div class="space-y-2">
                        @if(!$enabled)
                            <div class="flex items-center gap-3 rounded-lg bg-white/50 dark:bg-black/20 p-3">
                                <svg class="fill-danger flex-shrink-0" width="20" height="20" viewBox="0 0 24 24">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                                <span class="text-sm font-medium">Set <code class="rounded bg-gray-900 px-2 py-1 text-xs text-green-400">XAPI_ENABLED=true</code> in your .env file</span>
                            </div>
                        @endif
                        @if(!$configured)
                            <div class="flex items-center gap-3 rounded-lg bg-white/50 dark:bg-black/20 p-3">
                                <svg class="fill-danger flex-shrink-0" width="20" height="20" viewBox="0 0 24 24">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                                <span class="text-sm font-medium">Configure LRS credentials in .env file</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Grid -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 h-32 w-32 -mr-10 -mt-10 rounded-full bg-white opacity-10"></div>
            <div class="relative">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                        <svg class="fill-white" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-medium text-white/70 uppercase tracking-wide">Total</p>
                    </div>
                </div>
                <h4 class="text-4xl font-bold text-white mb-1">{{ number_format($stats['total'] ?? 0) }}</h4>
                <p class="text-sm text-white/80">Statements Created</p>
            </div>
        </div>

        <!-- Pending -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 h-32 w-32 -mr-10 -mt-10 rounded-full bg-white opacity-10"></div>
            <div class="relative">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                        <svg class="fill-white" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        @if(($stats['total'] ?? 0) > 0)
                            <p class="text-lg font-bold text-white">{{ round((($stats['pending'] ?? 0)/($stats['total'] ?? 1))*100) }}%</p>
                        @endif
                    </div>
                </div>
                <h4 class="text-4xl font-bold text-white mb-1">{{ number_format($stats['pending'] ?? 0) }}</h4>
                <p class="text-sm text-white/80">Pending Sync</p>
            </div>
        </div>

        <!-- Sent -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 h-32 w-32 -mr-10 -mt-10 rounded-full bg-white opacity-10"></div>
            <div class="relative">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                        <svg class="fill-white" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        @if(($stats['total'] ?? 0) > 0)
                            <p class="text-lg font-bold text-white">{{ round((($stats['sent'] ?? 0)/($stats['total'] ?? 1))*100) }}%</p>
                        @endif
                    </div>
                </div>
                <h4 class="text-4xl font-bold text-white mb-1">{{ number_format($stats['sent'] ?? 0) }}</h4>
                <p class="text-sm text-white/80">Successfully Sent</p>
            </div>
        </div>

        <!-- Failed -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-red-500 to-pink-600 p-6 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
            <div class="absolute top-0 right-0 h-32 w-32 -mr-10 -mt-10 rounded-full bg-white opacity-10"></div>
            <div class="relative">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                        <svg class="fill-white" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M12 2C6.47 2 2 6.47 2 12s4.47 10 10 10 10-4.47 10-10S17.53 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"/>
                        </svg>
                    </div>
                    @if(($stats['failed'] ?? 0) > 0)
                        <div class="text-right">
                            <p class="text-xs font-semibold text-white bg-white/20 rounded-full px-2 py-1">Action Needed</p>
                        </div>
                    @endif
                </div>
                <h4 class="text-4xl font-bold text-white mb-1">{{ number_format($stats['failed'] ?? 0) }}</h4>
                <p class="text-sm text-white/80">Failed to Send</p>
            </div>
        </div>
    </div>

    <!-- Sync Progress -->
    @if(($stats['total'] ?? 0) > 0)
        <div class="mb-8 rounded-2xl border border-stroke bg-white shadow-xl dark:border-strokedark dark:bg-boxdark overflow-hidden">
            <div class="border-b border-stroke bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 dark:border-strokedark dark:from-meta-4 dark:to-meta-4">
                <h3 class="font-bold text-lg text-black dark:text-white flex items-center gap-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" fill="currentColor"/>
                    </svg>
                    Sync Progress Overview
                </h3>
            </div>
            <div class="p-8">
                <div class="mb-6">
                    <div class="mb-3 flex items-center justify-between">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Overall Completion</span>
                        <span class="text-2xl font-bold text-primary">
                            {{ round((($stats['sent'] + $stats['failed'])/$stats['total'])*100, 1) }}%
                        </span>
                    </div>
                    <div class="h-4 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="h-full bg-gradient-to-r from-green-500 via-blue-500 to-purple-600 rounded-full transition-all duration-1000 ease-out shadow-lg"
                             style="width: {{ round((($stats['sent'] + $stats['failed'])/$stats['total'])*100, 1) }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Sent Progress -->
                    <div class="rounded-xl bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-5 border border-green-200 dark:border-green-800">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm font-medium text-green-700 dark:text-green-300">Sent</span>
                            <span class="text-lg font-bold text-green-600">{{ round(($stats['sent']/$stats['total'])*100) }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-green-200 dark:bg-green-800">
                            <div class="h-full bg-gradient-to-r from-green-500 to-emerald-600 rounded-full shadow"
                                 style="width: {{ round(($stats['sent']/$stats['total'])*100) }}%"></div>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-green-600">{{ number_format($stats['sent']) }}</p>
                    </div>

                    <!-- Pending Progress -->
                    <div class="rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 p-5 border border-amber-200 dark:border-amber-800">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm font-medium text-amber-700 dark:text-amber-300">Pending</span>
                            <span class="text-lg font-bold text-amber-600">{{ round(($stats['pending']/$stats['total'])*100) }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-amber-200 dark:bg-amber-800">
                            <div class="h-full bg-gradient-to-r from-amber-500 to-orange-600 rounded-full shadow"
                                 style="width: {{ round(($stats['pending']/$stats['total'])*100) }}%"></div>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-amber-600">{{ number_format($stats['pending']) }}</p>
                    </div>

                    <!-- Failed Progress -->
                    <div class="rounded-xl bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 p-5 border border-red-200 dark:border-red-800">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-sm font-medium text-red-700 dark:text-red-300">Failed</span>
                            <span class="text-lg font-bold text-red-600">{{ round(($stats['failed']/$stats['total'])*100) }}%</span>
                        </div>
                        <div class="h-2 w-full overflow-hidden rounded-full bg-red-200 dark:bg-red-800">
                            <div class="h-full bg-gradient-to-r from-red-500 to-pink-600 rounded-full shadow"
                                 style="width: {{ round(($stats['failed']/$stats['total'])*100) }}%"></div>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-red-600">{{ number_format($stats['failed']) }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
        <!-- Configuration Status -->
        <div class="rounded-2xl border border-stroke bg-white shadow-xl dark:border-strokedark dark:bg-boxdark overflow-hidden">
            <div class="border-b border-stroke bg-gradient-to-r from-purple-50 to-blue-50 px-6 py-4 dark:border-strokedark dark:from-meta-4 dark:to-meta-4">
                <h3 class="font-bold text-lg text-black dark:text-white flex items-center gap-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.32-.02-.64-.07-.94l2.03-1.58c.18-.14.23-.41.12-.61l-1.92-3.32c-.12-.22-.37-.29-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94L14.4 2.81c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.57-1.62.94l-2.39-.96c-.22-.08-.47 0-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.03 1.58c-.05.3-.09.63-.09.94s.02.64.07.94l-2.03 1.58c-.18.14-.23.41-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.08.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" fill="currentColor"/>
                    </svg>
                    Configuration Status
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <!-- xAPI Enabled -->
                <div class="flex items-center justify-between p-5 rounded-xl {{ $enabled ? 'bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800' }}">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $enabled ? 'bg-green-500' : 'bg-red-500' }} shadow-lg">
                            @if($enabled)
                                <svg class="fill-white" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            @else
                                <svg class="fill-white" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-bold text-black dark:text-white">xAPI Enabled</h4>
                            <p class="text-xs text-gray-500">XAPI_ENABLED setting</p>
                        </div>
                    </div>
                    <span class="text-2xl">{{ $enabled ? '✅' : '❌' }}</span>
                </div>

                <!-- LRS Configured -->
                <div class="flex items-center justify-between p-5 rounded-xl {{ $configured ? 'bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800' }}">
                    <div class="flex items-center gap-4">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $configured ? 'bg-green-500' : 'bg-red-500' }} shadow-lg">
                            @if($configured)
                                <svg class="fill-white" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            @else
                                <svg class="fill-white" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <h4 class="font-bold text-black dark:text-white">LRS Configured</h4>
                            <p class="text-xs text-gray-500">Endpoint & Credentials</p>
                        </div>
                    </div>
                    <span class="text-2xl">{{ $configured ? '✅' : '❌' }}</span>
                </div>
            </div>
        </div>

        <!-- Actions & Testing -->
        <div class="rounded-2xl border border-stroke bg-white shadow-xl dark:border-strokedark dark:bg-boxdark overflow-hidden">
            <div class="border-b border-stroke bg-gradient-to-r from-blue-50 to-purple-50 px-6 py-4 dark:border-strokedark dark:from-meta-4 dark:to-meta-4">
                <h3 class="font-bold text-lg text-black dark:text-white flex items-center gap-2">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M14.06 9.02l.92.92L5.92 19H5v-.92l9.06-9.06M17.66 3c-.25 0-.51.1-.7.29l-1.83 1.83 3.75 3.75 1.83-1.83c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.2-.2-.45-.29-.71-.29zm-3.6 3.19L3 17.25V21h3.75L17.81 9.94l-3.75-3.75z" fill="currentColor"/>
                    </svg>
                    Actions & Testing
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <button onclick="testConnection()"
                        class="group flex w-full items-center justify-center gap-3 rounded-xl bg-gradient-to-r from-primary to-purple-600 py-5 px-6 text-center font-bold text-white hover:shadow-2xl transition-all hover:-translate-y-1">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                    </svg>
                    <span class="text-lg">Test LRS Connection</span>
                </button>

                <button onclick="processPending()"
                        class="group flex w-full items-center justify-center gap-3 rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 py-5 px-6 text-center font-bold text-white hover:shadow-2xl transition-all hover:-translate-y-1">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z" fill="currentColor"/>
                    </svg>
                    <span class="text-lg">Process Pending Now</span>
                    @if(($stats['pending'] ?? 0) > 0)
                        <span class="rounded-full bg-white/30 px-3 py-1 text-sm font-bold">
                            {{ number_format($stats['pending']) }}
                        </span>
                    @endif
                </button>

                <a href="{{ route('admin.activity-logs.index') }}"
                   class="group flex w-full items-center justify-center gap-3 rounded-xl border-2 border-gray-300 dark:border-gray-600 py-5 px-6 text-center font-bold hover:bg-gray-100 dark:hover:bg-meta-4 transition-all">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" fill="currentColor"/>
                    </svg>
                    <span class="text-lg">View Activity Logs</span>
                </a>

                <!-- Result Message -->
                <div id="result-message" class="mt-6"></div>
            </div>
        </div>
    </div>

    <!-- Setup Guide -->
    @if(!$configured || !$enabled)
        <div class="mt-8 rounded-2xl border-2 border-blue-200 dark:border-blue-800 bg-gradient-to-br from-blue-50 via-purple-50 to-blue-50 dark:from-blue-900/20 dark:via-purple-900/20 dark:to-blue-900/20 shadow-xl overflow-hidden">
            <div class="border-b-2 border-blue-200 dark:border-blue-800 bg-white/50 dark:bg-black/20 px-6 py-4">
                <h3 class="font-bold text-xl text-black dark:text-white flex items-center gap-2">
                    <span class="text-2xl">📚</span>
                    Quick Setup Guide
                </h3>
            </div>
            <div class="p-8">
                <p class="text-sm text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                    To enable xAPI integration with <strong>NELC FutureX platform</strong>, add the following configuration to your <code class="bg-gray-900 text-green-400 px-2 py-1 rounded">.env</code> file:
                </p>
                <pre class="rounded-xl bg-gray-900 p-6 text-sm text-green-400 overflow-x-auto shadow-2xl border-2 border-gray-700"><code># xAPI Configuration - NELC Compliance
XAPI_ENABLED=true
XAPI_LRS_ENDPOINT=https://lrs.futurex.sa/xapi
XAPI_LRS_USERNAME=your_username_here
XAPI_LRS_PASSWORD=your_password_here

# Optional Settings
XAPI_BATCH_SIZE=50
XAPI_RETRY_MAX=3
XAPI_PLATFORM_IRI=https://yourdomain.sa</code></pre>
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-xl bg-gradient-to-br from-blue-100 to-purple-100 dark:from-blue-900/30 dark:to-purple-900/30 p-5 border-2 border-blue-300 dark:border-blue-700">
                        <h4 class="text-sm font-bold text-blue-900 dark:text-blue-300 mb-3 flex items-center gap-2">
                            <span class="text-xl">💡</span> Pro Tips
                        </h4>
                        <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-2">
                            <li class="flex items-start gap-2">
                                <span class="text-blue-500">•</span>
                                <span>Contact NELC to obtain your LRS credentials</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-500">•</span>
                                <span>Test the connection before enabling in production</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-500">•</span>
                                <span>Commands run automatically via Laravel scheduler</span>
                            </li>
                        </ul>
                    </div>
                    <div class="rounded-xl bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 p-5 border-2 border-purple-300 dark:border-purple-700">
                        <h4 class="text-sm font-bold text-purple-900 dark:text-purple-300 mb-3 flex items-center gap-2">
                            <span class="text-xl">⚙️</span> Next Steps
                        </h4>
                        <ul class="text-sm text-purple-800 dark:text-purple-200 space-y-2">
                            <li class="flex items-start gap-2">
                                <span class="text-purple-500">1.</span>
                                <span>Configure .env with LRS credentials</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-500">2.</span>
                                <span>Test connection using button above</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-purple-500">3.</span>
                                <span>Monitor activity logs and sync status</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function testConnection() {
    const btn = event.target.closest('button');
    const resultDiv = document.getElementById('result-message');
    const originalHTML = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-6 w-6 mx-auto" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    resultDiv.innerHTML = `
        <div class="rounded-xl border-2 border-blue-300 bg-blue-50 dark:bg-blue-900/20 p-5 animate-pulse">
            <p class="text-sm font-medium text-blue-800 dark:text-blue-200 flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Testing connection to LRS...
            </p>
        </div>
    `;

    fetch('{{ route('admin.xapi.test-connection') }}', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    })
    .then(r => r.json())
    .then(data => {
        const borderColor = data.success ? 'border-green-300 dark:border-green-700' : 'border-red-300 dark:border-red-700';
        const bgColor = data.success ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20';
        const textColor = data.success ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200';
        const icon = data.success ? '✅' : '❌';

        resultDiv.innerHTML = `
            <div class="rounded-xl border-2 ${borderColor} ${bgColor} p-5 shadow-lg">
                <p class="text-base font-bold ${textColor} mb-2 flex items-center gap-2">
                    <span class="text-2xl">${icon}</span>
                    ${data.message}
                </p>
                ${data.lrs_info ? `<pre class="mt-3 text-xs ${textColor} overflow-auto bg-black/10 p-3 rounded">${JSON.stringify(data.lrs_info, null, 2)}</pre>` : ''}
            </div>
        `;

        btn.disabled = false;
        btn.innerHTML = originalHTML;
    })
    .catch(err => {
        resultDiv.innerHTML = `
            <div class="rounded-xl border-2 border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20 p-5 shadow-lg">
                <p class="text-base font-bold text-red-800 dark:text-red-200 flex items-center gap-2">
                    <span class="text-2xl">❌</span>
                    Error: ${err.message}
                </p>
            </div>
        `;
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    });
}

function processPending() {
    const btn = event.target.closest('button');
    const resultDiv = document.getElementById('result-message');
    const originalHTML = btn.innerHTML;

    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-6 w-6 mx-auto" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

    resultDiv.innerHTML = `
        <div class="rounded-xl border-2 border-orange-300 bg-orange-50 dark:bg-orange-900/20 p-5 animate-pulse">
            <p class="text-sm font-medium text-orange-800 dark:text-orange-200 flex items-center gap-2">
                <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Processing pending statements...
            </p>
        </div>
    `;

    fetch('{{ route('admin.xapi.process-pending') }}', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
    })
    .then(r => r.json())
    .then(data => {
        resultDiv.innerHTML = `
            <div class="rounded-xl border-2 border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/20 p-5 shadow-lg">
                <p class="text-base font-bold text-green-800 dark:text-green-200 mb-2 flex items-center gap-2">
                    <span class="text-2xl">✅</span>
                    ${data.message}
                </p>
                <p class="text-sm text-green-700 dark:text-green-300">Refreshing page in 2 seconds...</p>
            </div>
        `;
        setTimeout(() => location.reload(), 2000);
    })
    .catch(err => {
        resultDiv.innerHTML = `
            <div class="rounded-xl border-2 border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20 p-5 shadow-lg">
                <p class="text-base font-bold text-red-800 dark:text-red-200 flex items-center gap-2">
                    <span class="text-2xl">❌</span>
                    Error: ${err.message}
                </p>
            </div>
        `;
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    });
}
</script>
@endsection
