@extends('layouts.dashboard')

@section('title', __('Activity Logs'))

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Header with Actions -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-2">
                <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary bg-opacity-10">
                    <svg class="fill-primary" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </span>
                <div>
                    <span class="block">{{ __('Activity Logs') }}</span>
                    <span class="block text-sm font-normal text-gray-500">سجل النشاطات المتقدم</span>
                </div>
            </h2>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.activity-logs.stats') }}"
               class="inline-flex items-center justify-center gap-2 rounded-md bg-meta-3 py-2 px-4 text-center font-medium text-white hover:bg-opacity-90 transition">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" fill="currentColor"/>
                </svg>
                {{ __('Analytics') }}
            </a>
            <a href="{{ route('admin.activity-logs.export', ['format' => 'csv'] + request()->all()) }}"
               class="inline-flex items-center justify-center gap-2 rounded-md border border-primary py-2 px-4 text-center font-medium text-primary hover:bg-primary hover:text-white transition">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19 12v7H5v-7H3v7c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-7h-2zm-6 .67l2.59-2.58L17 11.5l-5 5-5-5 1.41-1.41L11 12.67V3h2z" fill="currentColor"/>
                </svg>
                {{ __('Export') }}
            </a>
        </div>
    </div>

    <!-- Enhanced Stats Cards with Icons & Gradients -->
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <!-- Total Activities -->
        <div class="group relative overflow-hidden rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-lg transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-primary opacity-5 group-hover:opacity-10 transition"></div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-3xl font-bold text-black dark:text-white mb-1">
                            {{ number_format($stats['total']) }}
                        </h4>
                        <span class="text-sm font-medium text-gray-500">{{ __('Total Activities') }}</span>
                        <div class="mt-2 text-xs text-success">
                            ↗ All time tracking
                        </div>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-primary bg-opacity-10">
                        <svg class="fill-primary" width="28" height="28" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last 24 Hours -->
        <div class="group relative overflow-hidden rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-lg transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-warning opacity-5 group-hover:opacity-10 transition"></div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-3xl font-bold text-black dark:text-white mb-1">
                            {{ number_format($stats['last_24h']) }}
                        </h4>
                        <span class="text-sm font-medium text-gray-500">{{ __('Last 24 Hours') }}</span>
                        <div class="mt-2 text-xs text-warning">
                            🔥 Recent activity
                        </div>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-warning bg-opacity-10">
                        <svg class="fill-warning" width="28" height="28" viewBox="0 0 24 24">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Last 7 Days -->
        <div class="group relative overflow-hidden rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-lg transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-meta-3 opacity-5 group-hover:opacity-10 transition"></div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-3xl font-bold text-black dark:text-white mb-1">
                            {{ number_format($stats['last_7d']) }}
                        </h4>
                        <span class="text-sm font-medium text-gray-500">{{ __('Last 7 Days') }}</span>
                        <div class="mt-2 text-xs text-meta-3">
                            📊 Weekly trend
                        </div>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-meta-3 bg-opacity-10">
                        <svg class="fill-meta-3" width="28" height="28" viewBox="0 0 24 24">
                            <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- xAPI Synced -->
        <div class="group relative overflow-hidden rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark hover:shadow-lg transition-all duration-300">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 h-24 w-24 rounded-full bg-success opacity-5 group-hover:opacity-10 transition"></div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-3xl font-bold text-black dark:text-white mb-1">
                            {{ number_format($stats['xapi_synced']) }}
                        </h4>
                        <span class="text-sm font-medium text-gray-500">{{ __('xAPI Synced') }}</span>
                        <div class="mt-2 text-xs text-success">
                            ✓ {{ $stats['total'] > 0 ? round(($stats['xapi_synced']/$stats['total'])*100, 1) : 0 }}% synced
                        </div>
                    </div>
                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-success bg-opacity-10">
                        <svg class="fill-success" width="28" height="28" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <!-- Activity Timeline Chart -->
        <div class="rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
                <h3 class="font-semibold text-black dark:text-white flex items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z" fill="currentColor"/>
                    </svg>
                    {{ __('Activity Timeline') }} - Last 7 Days
                </h3>
            </div>
            <div class="p-6">
                <canvas id="activityChart" height="200"></canvas>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
                <h3 class="font-semibold text-black dark:text-white flex items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M11 2v20c-5.07-.5-9-4.79-9-10s3.93-9.5 9-10zm2.03 0v8.99H22c-.47-4.74-4.24-8.52-8.97-8.99zm0 11.01V22c4.74-.47 8.5-4.25 8.97-8.99h-8.97z" fill="currentColor"/>
                    </svg>
                    {{ __('Category Distribution') }}
                </h3>
            </div>
            <div class="p-6">
                <canvas id="categoryChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Actions & Quick Stats -->
    <div class="mb-6 rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6 py-4 dark:border-strokedark">
            <h3 class="font-semibold text-black dark:text-white">🔥 {{ __('Top Actions') }}</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                @foreach($topActions as $index => $action)
                    <div class="flex items-center gap-3 rounded-lg border border-stroke p-4 hover:bg-gray-50 dark:border-strokedark dark:hover:bg-meta-4 transition">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full {{ ['bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-meta-3'][$index % 5] }} bg-opacity-10">
                            <span class="text-lg font-bold {{ ['text-primary', 'text-success', 'text-warning', 'text-danger', 'text-meta-3'][$index % 5] }}">{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500">{{ $action->action }}</p>
                            <p class="text-lg font-bold text-black dark:text-white">{{ number_format($action->count) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="mb-6 rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6 py-4 dark:border-strokedark flex items-center justify-between">
            <h3 class="font-semibold text-black dark:text-white flex items-center gap-2">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" fill="currentColor"/>
                </svg>
                {{ __('Filters') }} / الفلاتر
            </h3>
            <button onclick="toggleFilters()" class="text-sm text-primary hover:underline">
                <span id="filterToggleText">{{ __('Show Filters') }}</span>
            </button>
        </div>
        <div id="filtersContainer" class="hidden p-6 border-t border-stroke dark:border-strokedark bg-gray-50 dark:bg-meta-4">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <!-- Search -->
                <div class="md:col-span-4">
                    <label class="mb-2 block text-sm font-medium text-black dark:text-white flex items-center gap-2">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/>
                        </svg>
                        {{ __('Search') }}
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by user name, email, or action..."
                           class="w-full rounded-lg border border-stroke bg-white py-3 px-4 text-black focus:border-primary focus:ring-2 focus:ring-primary focus:ring-opacity-20 dark:border-strokedark dark:bg-boxdark dark:text-white transition">
                </div>

                <!-- Action Filter -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-black dark:text-white">{{ __('Action') }}</label>
                    <select name="action" class="w-full rounded-lg border border-stroke bg-white py-3 px-4 text-black focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white transition">
                        <option value="">{{ __('All Actions') }}</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-black dark:text-white">{{ __('Category') }}</label>
                    <select name="category" class="w-full rounded-lg border border-stroke bg-white py-3 px-4 text-black focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white transition">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-black dark:text-white">{{ __('Date From') }}</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full rounded-lg border border-stroke bg-white py-3 px-4 text-black focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white transition">
                </div>

                <!-- Date To -->
                <div>
                    <label class="mb-2 block text-sm font-medium text-black dark:text-white">{{ __('Date To') }}</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full rounded-lg border border-stroke bg-white py-3 px-4 text-black focus:border-primary dark:border-strokedark dark:bg-boxdark dark:text-white transition">
                </div>

                <!-- Action Buttons -->
                <div class="md:col-span-4 flex gap-3">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary py-3 px-6 text-center font-medium text-white hover:bg-opacity-90 transition">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill="currentColor"/>
                        </svg>
                        {{ __('Apply Filters') }}
                    </button>
                    <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-stroke py-3 px-6 text-center font-medium text-black hover:bg-gray-100 dark:border-strokedark dark:text-white dark:hover:bg-meta-4 transition">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" fill="currentColor"/>
                        </svg>
                        {{ __('Reset') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Logs Table with Enhanced Design -->
    <div class="rounded-lg border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6 py-4 dark:border-strokedark flex items-center justify-between">
            <h4 class="text-lg font-semibold text-black dark:text-white">
                📋 {{ __('Recent Activity') }}
                <span class="ml-2 rounded-full bg-primary bg-opacity-10 px-3 py-1 text-sm font-medium text-primary">
                    {{ $logs->total() }} {{ __('records') }}
                </span>
            </h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-4 px-4 font-medium text-black dark:text-white">
                            <div class="flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
                                </svg>
                                {{ __('User') }}
                            </div>
                        </th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('Action') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('Category') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('Related') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('IP') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('Time') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('xAPI') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white text-center">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="border-b border-stroke dark:border-strokedark hover:bg-gray-50 dark:hover:bg-meta-4 transition-colors">
                            <td class="py-4 px-4">
                                @if($log->user)
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary bg-opacity-10 text-primary font-bold">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-black dark:text-white">{{ $log->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $log->user->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-gray-400">System</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center gap-1 rounded-full bg-primary bg-opacity-10 py-1 px-3 text-sm font-medium text-primary">
                                    @php
                                        $actionIcons = [
                                            'login' => '🔐',
                                            'logout' => '🚪',
                                            'view' => '👁️',
                                            'create' => '➕',
                                            'update' => '✏️',
                                            'delete' => '🗑️',
                                            'submit' => '📝',
                                            'grade' => '📊',
                                        ];
                                        $icon = '📌';
                                        foreach($actionIcons as $key => $value) {
                                            if(str_contains(strtolower($log->action), $key)) {
                                                $icon = $value;
                                                break;
                                            }
                                        }
                                    @endphp
                                    {{ $icon }} {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $categoryColors = [
                                        'auth' => 'bg-primary text-primary',
                                        'content' => 'bg-meta-3 text-meta-3',
                                        'assessment' => 'bg-warning text-warning',
                                        'attendance' => 'bg-success text-success',
                                        'admin' => 'bg-danger text-danger',
                                    ];
                                    $colorClass = $categoryColors[$log->action_category] ?? 'bg-gray-500 text-gray-500';
                                @endphp
                                <span class="inline-flex rounded-full bg-opacity-10 py-1 px-2 text-xs font-medium {{ $colorClass }}">
                                    {{ ucfirst($log->action_category) }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                @if($log->loggable_type)
                                    <div class="text-sm">
                                        <span class="font-medium">{{ class_basename($log->loggable_type) }}</span>
                                        <span class="text-gray-400">#{{ $log->loggable_id }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-xs font-mono text-gray-600 dark:text-gray-400">{{ $log->ip_address ?? 'N/A' }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="text-sm text-black dark:text-white">{{ $log->created_at->diffForHumans() }}</div>
                                <div class="text-xs text-gray-500">{{ $log->created_at->format('M d, H:i') }}</div>
                            </td>
                            <td class="py-4 px-4">
                                @if($log->xapi_sent)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-success bg-opacity-10 py-1 px-2 text-xs font-medium text-success">
                                        ✓ Sent
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-warning bg-opacity-10 py-1 px-2 text-xs font-medium text-warning">
                                        ⏳ Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center">
                                <a href="{{ route('admin.activity-logs.show', $log) }}"
                                   class="inline-flex items-center justify-center gap-1 rounded-lg bg-primary bg-opacity-10 py-2 px-3 text-sm font-medium text-primary hover:bg-opacity-100 hover:text-white transition">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>
                                    </svg>
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 px-4 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" class="text-gray-300">
                                        <path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z" fill="currentColor"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-500">{{ __('No activity logs found') }}</p>
                                    <p class="text-sm text-gray-400">{{ __('Try adjusting your filters or check back later') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="border-t border-stroke px-6 py-4 dark:border-strokedark">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Activity Timeline Chart
const activityCtx = document.getElementById('activityChart').getContext('2d');
new Chart(activityCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($activityTimeline->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))) !!},
        datasets: [{
            label: 'Activities',
            data: {!! json_encode($activityTimeline->pluck('count')) !!},
            borderColor: '#3C50E0',
            backgroundColor: 'rgba(60, 80, 224, 0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#3C50E0',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12,
                titleColor: '#fff',
                bodyColor: '#fff',
                borderColor: '#3C50E0',
                borderWidth: 1
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0, 0, 0, 0.05)' }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});

// Category Distribution Chart
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($categoryStats->keys()) !!},
        datasets: [{
            data: {!! json_encode($categoryStats->values()) !!},
            backgroundColor: [
                'rgba(60, 80, 224, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(251, 191, 36, 0.8)',
                'rgba(239, 68, 68, 0.8)',
                'rgba(147, 51, 234, 0.8)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: { size: 12 }
                }
            },
            tooltip: {
                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                padding: 12
            }
        }
    }
});

// Toggle Filters
function toggleFilters() {
    const container = document.getElementById('filtersContainer');
    const text = document.getElementById('filterToggleText');

    if (container.classList.contains('hidden')) {
        container.classList.remove('hidden');
        text.textContent = '{{ __("Hide Filters") }}';
    } else {
        container.classList.add('hidden');
        text.textContent = '{{ __("Show Filters") }}';
    }
}

// Auto-show filters if any filter is active
@if(request()->hasAny(['action', 'category', 'date_from', 'date_to', 'search']))
    document.addEventListener('DOMContentLoaded', function() {
        toggleFilters();
    });
@endif
</script>
@endsection
