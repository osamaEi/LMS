@extends('layouts.dashboard')

@section('title', __('Activity Logs'))

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Breadcrumb -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">
            {{ __('Activity Logs') }} / سجل النشاطات
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <a class="font-medium" href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} /</a>
                </li>
                <li class="font-medium text-primary">{{ __('Activity Logs') }}</li>
            </ol>
        </nav>
    </div>

    <!-- Stats Cards -->
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ number_format(\App\Models\ActivityLog::count()) }}
                    </h4>
                    <span class="text-sm font-medium">{{ __('Total Activities') }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ number_format(\App\Models\ActivityLog::where('created_at', '>=', now()->subDay())->count()) }}
                    </h4>
                    <span class="text-sm font-medium">{{ __('Last 24 Hours') }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <h4 class="text-title-md font-bold text-black dark:text-white">
                        {{ number_format(\App\Models\ActivityLog::where('xapi_sent', true)->count()) }}
                    </h4>
                    <span class="text-sm font-medium">{{ __('xAPI Synced') }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <div class="mt-4 flex items-end justify-between">
                <div>
                    <a href="{{ route('admin.activity-logs.stats') }}" class="text-primary hover:underline">
                        <h4 class="text-title-md font-bold text-black dark:text-white">
                            {{ __('View Stats') }}
                        </h4>
                        <span class="text-sm font-medium">{{ __('Analytics Dashboard') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
            <h3 class="font-medium text-black dark:text-white">
                {{ __('Filters') }} / الفلاتر
            </h3>
        </div>
        <div class="p-7">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                <div>
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">{{ __('Action') }}</label>
                    <select name="action" class="w-full rounded border border-stroke bg-gray py-3 px-4.5 text-black focus:border-primary dark:border-strokedark dark:bg-meta-4 dark:text-white">
                        <option value="">{{ __('All Actions') }}</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">{{ __('Category') }}</label>
                    <select name="category" class="w-full rounded border border-stroke bg-gray py-3 px-4.5 text-black focus:border-primary dark:border-strokedark dark:bg-meta-4 dark:text-white">
                        <option value="">{{ __('All Categories') }}</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">{{ __('Date From') }}</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded border border-stroke bg-gray py-3 px-4.5 text-black focus:border-primary dark:border-strokedark dark:bg-meta-4 dark:text-white">
                </div>

                <div>
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">{{ __('Date To') }}</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded border border-stroke bg-gray py-3 px-4.5 text-black focus:border-primary dark:border-strokedark dark:bg-meta-4 dark:text-white">
                </div>

                <div class="md:col-span-4 flex gap-3">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md bg-primary py-3 px-6 text-center font-medium text-white hover:bg-opacity-90">
                        {{ __('Apply Filters') }}
                    </button>
                    <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex items-center justify-center rounded-md border border-primary py-3 px-6 text-center font-medium text-primary hover:bg-opacity-90">
                        {{ __('Reset') }}
                    </a>
                    <a href="{{ route('admin.activity-logs.export', ['format' => 'csv'] + request()->all()) }}" class="inline-flex items-center justify-center rounded-md bg-meta-3 py-3 px-6 text-center font-medium text-white hover:bg-opacity-90">
                        {{ __('Export CSV') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="px-4 py-6 md:px-6 xl:px-7.5">
            <h4 class="text-xl font-bold text-black dark:text-white">
                {{ __('Activity Logs') }} ({{ $logs->total() }})
            </h4>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-2 text-left dark:bg-meta-4">
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('User') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('Action') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('Category') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('Related') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('IP Address') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('Time') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('xAPI') }}</th>
                        <th class="py-4 px-4 font-medium text-black dark:text-white">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="border-b border-stroke dark:border-strokedark">
                            <td class="py-4 px-4">
                                @if($log->user)
                                    <div class="font-medium">{{ $log->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $log->user->email }}</div>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex rounded-full bg-opacity-10 py-1 px-3 text-sm font-medium bg-primary text-primary">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-sm">{{ ucfirst($log->action_category) }}</span>
                            </td>
                            <td class="py-4 px-4">
                                @if($log->loggable_type)
                                    <div class="text-sm">{{ class_basename($log->loggable_type) }}</div>
                                    <div class="text-xs text-gray-500">#{{ $log->loggable_id }}</div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-sm">{{ $log->ip_address ?? 'N/A' }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="text-sm">{{ $log->created_at->diffForHumans() }}</span>
                                <div class="text-xs text-gray-500">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                            </td>
                            <td class="py-4 px-4">
                                @if($log->xapi_sent)
                                    <span class="inline-flex rounded-full bg-opacity-10 py-1 px-2 text-xs font-medium bg-success text-success">
                                        ✓ Sent
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-opacity-10 py-1 px-2 text-xs font-medium bg-warning text-warning">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-4">
                                <a href="{{ route('admin.activity-logs.show', $log) }}" class="text-primary hover:underline">
                                    {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 px-4 text-center text-gray-500">
                                {{ __('No activity logs found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
