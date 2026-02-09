@extends('layouts.dashboard')

@section('title', __('Activity Log Details'))

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Breadcrumb -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">
            {{ __('Activity Log Details') }} / تفاصيل سجل النشاط
        </h2>
        <nav>
            <ol class="flex items-center gap-2">
                <li>
                    <a class="font-medium" href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }} /</a>
                </li>
                <li>
                    <a class="font-medium" href="{{ route('admin.activity-logs.index') }}">{{ __('Activity Logs') }} /</a>
                </li>
                <li class="font-medium text-primary">{{ __('Details') }}</li>
            </ol>
        </nav>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Details -->
        <div class="lg:col-span-2">
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        {{ __('Activity Information') }}
                    </h3>
                </div>
                <div class="p-7">
                    <div class="mb-5.5">
                        <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                            {{ __('Action') }}
                        </label>
                        <div class="rounded border border-stroke bg-gray py-3 px-4.5 dark:border-strokedark dark:bg-meta-4">
                            <span class="inline-flex rounded-full bg-opacity-10 py-1 px-3 text-sm font-medium bg-primary text-primary">
                                {{ $log->action }}
                            </span>
                            <span class="ml-3 text-sm text-gray-600">{{ $log->getDescription() }}</span>
                        </div>
                    </div>

                    <div class="mb-5.5">
                        <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                            {{ __('Category') }}
                        </label>
                        <div class="rounded border border-stroke bg-gray py-3 px-4.5 dark:border-strokedark dark:bg-meta-4">
                            {{ ucfirst($log->action_category) }}
                        </div>
                    </div>

                    @if($log->loggable_type)
                        <div class="mb-5.5">
                            <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                {{ __('Related Entity') }}
                            </label>
                            <div class="rounded border border-stroke bg-gray py-3 px-4.5 dark:border-strokedark dark:bg-meta-4">
                                <div class="font-medium">{{ class_basename($log->loggable_type) }}</div>
                                <div class="text-sm text-gray-500">ID: {{ $log->loggable_id }}</div>
                                @if($log->loggable)
                                    <div class="mt-2 text-sm">
                                        @if(method_exists($log->loggable, 'name'))
                                            <strong>{{ __('Name') }}:</strong> {{ $log->loggable->name ?? 'N/A' }}
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($log->properties && count($log->properties) > 0)
                        <div class="mb-5.5">
                            <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                                {{ __('Properties') }} / البيانات الإضافية
                            </label>
                            <div class="rounded border border-stroke bg-gray py-3 px-4.5 dark:border-strokedark dark:bg-meta-4">
                                <pre class="text-sm overflow-auto">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif

                    <div class="mb-5.5">
                        <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                            {{ __('Session ID') }}
                        </label>
                        <div class="rounded border border-stroke bg-gray py-3 px-4.5 dark:border-strokedark dark:bg-meta-4">
                            <code class="text-sm">{{ $log->session_id }}</code>
                        </div>
                    </div>

                    <div class="mb-5.5">
                        <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                            {{ __('User Agent') }}
                        </label>
                        <div class="rounded border border-stroke bg-gray py-3 px-4.5 dark:border-strokedark dark:bg-meta-4">
                            <div class="text-sm break-all">{{ $log->user_agent ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- User Info -->
            @if($log->user)
                <div class="mb-6 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                    <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
                        <h3 class="font-medium text-black dark:text-white">
                            {{ __('User Information') }}
                        </h3>
                    </div>
                    <div class="p-7">
                        <div class="mb-4">
                            <div class="font-medium text-black dark:text-white">{{ $log->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $log->user->email }}</div>
                        </div>
                        <div class="mb-4">
                            <div class="text-sm">
                                <strong>{{ __('Role') }}:</strong>
                                <span class="inline-flex rounded-full bg-opacity-10 py-1 px-2 text-xs font-medium bg-success text-success">
                                    {{ ucfirst($log->user->role) }}
                                </span>
                            </div>
                        </div>
                        @if($log->user->national_id)
                            <div class="text-sm">
                                <strong>{{ __('National ID') }}:</strong> {{ $log->user->national_id }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Technical Details -->
            <div class="mb-6 rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
                    <h3 class="font-medium text-black dark:text-white">
                        {{ __('Technical Details') }}
                    </h3>
                </div>
                <div class="p-7">
                    <div class="mb-4">
                        <div class="text-sm font-medium text-black dark:text-white">{{ __('IP Address') }}</div>
                        <div class="text-sm text-gray-500">{{ $log->ip_address ?? 'N/A' }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-sm font-medium text-black dark:text-white">{{ __('Created At') }}</div>
                        <div class="text-sm text-gray-500">{{ $log->created_at->format('Y-m-d H:i:s') }}</div>
                        <div class="text-xs text-gray-400">{{ $log->created_at->diffForHumans() }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="text-sm font-medium text-black dark:text-white">{{ __('xAPI Status') }}</div>
                        @if($log->xapi_sent)
                            <span class="inline-flex rounded-full bg-opacity-10 py-1 px-2 text-xs font-medium bg-success text-success">
                                ✓ Sent
                            </span>
                            @if($log->xapi_sent_at)
                                <div class="text-xs text-gray-400 mt-1">{{ $log->xapi_sent_at->format('Y-m-d H:i:s') }}</div>
                            @endif
                        @else
                            <span class="inline-flex rounded-full bg-opacity-10 py-1 px-2 text-xs font-medium bg-warning text-warning">
                                Pending
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
                <div class="p-7">
                    <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex w-full items-center justify-center rounded-md bg-primary py-3 px-6 text-center font-medium text-white hover:bg-opacity-90">
                        {{ __('Back to List') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
