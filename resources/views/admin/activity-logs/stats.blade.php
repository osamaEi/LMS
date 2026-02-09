@extends('layouts.dashboard')

@section('title', __('Activity Statistics'))

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Breadcrumb -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">
            {{ __('Activity Statistics') }} / إحصائيات النشاطات
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <a class="font-medium" href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} /</a>
                </li>
                <li>
                    <a class="font-medium" href="{{ route('admin.activity-logs.index') }}">{{ __('Activity Logs') }} /</a>
                </li>
                <li class="font-medium text-primary">{{ __('Statistics') }}</li>
            </ol>
        </nav>
    </div>

    <!-- Date Range Filter -->
    <div class="mb-6 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="p-7">
            <form method="GET" action="{{ route('admin.activity-logs.stats') }}" class="flex gap-4 items-end">
                <div>
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">{{ __('Date From') }}</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded border border-stroke bg-gray py-3 px-4.5 text-black focus:border-primary dark:border-strokedark dark:bg-meta-4 dark:text-white">
                </div>
                <div>
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">{{ __('Date To') }}</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded border border-stroke bg-gray py-3 px-4.5 text-black focus:border-primary dark:border-strokedark dark:bg-meta-4 dark:text-white">
                </div>
                <button type="submit" class="rounded-md bg-primary py-3 px-6 text-white hover:bg-opacity-90">
                    {{ __('Update') }}
                </button>
            </form>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ number_format($totalLogs) }}
                    </h4>
                    <span class="text-sm font-medium">{{ __('Total Activities') }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ number_format($stats['total_activities']) }}
                    </h4>
                    <span class="text-sm font-medium">{{ __('In Selected Range') }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ number_format($stats['unique_users']) }}
                    </h4>
                    <span class="text-sm font-medium">{{ __('Unique Users') }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-success">
                        {{ number_format($xapiSyncedCount) }}
                    </h4>
                    <h4 class="text-sm font-bold text-warning mt-1">
                        {{ number_format($xapiPendingCount) }}
                    </h4>
                    <span class="text-sm font-medium">{{ __('xAPI Synced / Pending') }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Activities by Category -->
        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
                <h3 class="font-medium text-black dark:text-white">
                    {{ __('Activities by Category') }}
                </h3>
            </div>
            <div class="p-7">
                <div class="space-y-4">
                    @foreach($stats['by_category'] as $category => $count)
                        <div>
                            <div class="mb-2 flex justify-between">
                                <span class="text-sm font-medium">{{ ucfirst($category) }}</span>
                                <span class="text-sm font-bold">{{ number_format($count) }}</span>
                            </div>
                            <div class="h-2 w-full rounded-full bg-gray-200 dark:bg-meta-4">
                                <div class="h-2 rounded-full bg-primary" style="width: {{ ($count / $stats['total_activities']) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Actions -->
        <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
                <h3 class="font-medium text-black dark:text-white">
                    {{ __('Top Actions') }}
                </h3>
            </div>
            <div class="p-7">
                <div class="space-y-3">
                    @php
                        $topActions = collect($stats['by_action'])->sortDesc()->take(10);
                    @endphp
                    @foreach($topActions as $action => $count)
                        <div class="flex justify-between items-center">
                            <span class="text-sm">{{ $action }}</span>
                            <span class="inline-flex rounded-full bg-opacity-10 py-1 px-3 text-sm font-medium bg-primary text-primary">
                                {{ number_format($count) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Daily Breakdown -->
        <div class="lg:col-span-2 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
                <h3 class="font-medium text-black dark:text-white">
                    {{ __('Daily Activity Breakdown') }}
                </h3>
            </div>
            <div class="p-7">
                <div class="space-y-2">
                    @foreach($stats['daily_breakdown'] as $date => $count)
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium">{{ $date }}</span>
                            <div class="flex items-center gap-3">
                                <div class="h-2 w-48 rounded-full bg-gray-200 dark:bg-meta-4">
                                    <div class="h-2 rounded-full bg-success" style="width: {{ ($count / max($stats['daily_breakdown']->values()->all())) * 100 }}%"></div>
                                </div>
                                <span class="text-sm font-bold w-16 text-right">{{ number_format($count) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Active Users -->
        <div class="lg:col-span-2 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
                <h3 class="font-medium text-black dark:text-white">
                    {{ __('Most Active Users') }}
                </h3>
            </div>
            <div class="p-7">
                <div class="overflow-x-auto">
                    <table class="w-full table-auto">
                        <thead>
                            <tr class="bg-gray-2 text-left dark:bg-meta-4">
                                <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('User') }}</th>
                                <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('Email') }}</th>
                                <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('Role') }}</th>
                                <th class="py-4 px-4 font-medium text-black dark:text-white text-right">{{ __('Activities') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topUsers as $userActivity)
                                <tr class="border-b border-stroke dark:border-strokedark">
                                    <td class="py-4 px-4 font-medium">{{ $userActivity->user?->name ?? 'N/A' }}</td>
                                    <td class="py-4 px-4">{{ $userActivity->user?->email ?? 'N/A' }}</td>
                                    <td class="py-4 px-4">
                                        <span class="inline-flex rounded-full bg-opacity-10 py-1 px-2 text-xs font-medium bg-success text-success">
                                            {{ ucfirst($userActivity->user?->role ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4 text-right font-bold">{{ number_format($userActivity->activity_count) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
