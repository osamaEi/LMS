@extends('layouts.dashboard')

@section('title', __('Activity Log Details'))

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <!-- Header -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-title-md2 font-bold text-black dark:text-white flex items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary bg-opacity-10">
                    <svg class="fill-primary" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                </span>
                <div>
                    <span class="block">{{ __('Activity Details') }}</span>
                    <span class="block text-sm font-normal text-gray-500">تفاصيل النشاط #{{ $log->id }}</span>
                </div>
            </h2>
        </div>
        <a href="{{ route('admin.activity-logs.index') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg border border-stroke py-2 px-4 text-center font-medium hover:bg-gray-100 dark:border-strokedark dark:hover:bg-meta-4 transition">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z" fill="currentColor"/>
            </svg>
            {{ __('Back to List') }}
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Activity Overview Card -->
            <div class="rounded-xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
                <div class="border-b border-stroke bg-gradient-to-r from-primary to-purple-600 px-6 py-4">
                    <h3 class="font-semibold text-white flex items-center gap-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" fill="currentColor"/>
                        </svg>
                        {{ __('Activity Overview') }}
                    </h3>
                </div>
                <div class="p-6">
                    <!-- Action -->
                    <div class="mb-6 flex items-start gap-4 p-4 rounded-lg bg-gradient-to-r from-primary/5 to-purple-600/5 border border-primary/20">
                        <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary text-white font-bold text-xl flex-shrink-0">
                            @php
                                $actionIcons = [
                                    'login' => '🔐', 'logout' => '🚪', 'view' => '👁️',
                                    'create' => '➕', 'update' => '✏️', 'delete' => '🗑️',
                                    'submit' => '📝', 'grade' => '📊',
                                ];
                                $icon = '📌';
                                foreach($actionIcons as $key => $value) {
                                    if(str_contains(strtolower($log->action), $key)) {
                                        $icon = $value;
                                        break;
                                    }
                                }
                            @endphp
                            {{ $icon }}
                        </div>
                        <div class="flex-1">
                            <label class="text-xs font-medium text-gray-500 uppercase">Action</label>
                            <h4 class="text-xl font-bold text-black dark:text-white">{{ $log->action }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $log->getDescription() }}</p>
                        </div>
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
                        <span class="inline-flex items-center gap-1 rounded-full bg-opacity-10 py-2 px-3 text-sm font-medium {{ $colorClass }}">
                            {{ ucfirst($log->action_category) }}
                        </span>
                    </div>

                    <!-- Related Entity -->
                    @if($log->loggable_type)
                        <div class="mb-6">
                            <label class="mb-3 block text-sm font-medium text-black dark:text-white flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M10 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z" fill="currentColor"/>
                                </svg>
                                {{ __('Related Entity') }}
                            </label>
                            <div class="rounded-lg border border-stroke bg-gray-50 p-4 dark:border-strokedark dark:bg-meta-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-meta-3 bg-opacity-10 text-meta-3 font-bold">
                                        {{ strtoupper(substr(class_basename($log->loggable_type), 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-black dark:text-white">{{ class_basename($log->loggable_type) }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $log->loggable_id }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Properties -->
                    @if($log->properties && count($log->properties) > 0)
                        <div class="mb-6">
                            <label class="mb-3 block text-sm font-medium text-black dark:text-white flex items-center gap-2">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M20 3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H4v-4h11v4zm0-5H4V7h11v5zm5 5h-4V7h4v10z" fill="currentColor"/>
                                </svg>
                                {{ __('Additional Properties') }} / البيانات الإضافية
                            </label>
                            <div class="rounded-lg border border-stroke bg-gray-900 p-4 dark:border-strokedark overflow-hidden">
                                <pre class="text-sm text-green-400 overflow-auto max-h-96">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif

                    <!-- Technical Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Session ID -->
                        <div>
                            <label class="mb-2 block text-xs font-medium text-gray-500 uppercase">Session ID</label>
                            <div class="rounded-lg border border-stroke bg-gray-50 p-3 dark:border-strokedark dark:bg-meta-4">
                                <code class="text-xs font-mono text-gray-700 dark:text-gray-300 break-all">{{ $log->session_id }}</code>
                            </div>
                        </div>

                        <!-- IP Address -->
                        <div>
                            <label class="mb-2 block text-xs font-medium text-gray-500 uppercase">IP Address</label>
                            <div class="rounded-lg border border-stroke bg-gray-50 p-3 dark:border-strokedark dark:bg-meta-4 flex items-center gap-2">
                                <svg class="fill-gray-500" width="16" height="16" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/>
                                </svg>
                                <code class="text-sm font-mono text-gray-700 dark:text-gray-300">{{ $log->ip_address ?? 'N/A' }}</code>
                            </div>
                        </div>
                    </div>

                    <!-- User Agent -->
                    @if($log->user_agent)
                        <div class="mt-4">
                            <label class="mb-2 block text-xs font-medium text-gray-500 uppercase">User Agent / Browser</label>
                            <div class="rounded-lg border border-stroke bg-gray-50 p-3 dark:border-strokedark dark:bg-meta-4">
                                <div class="text-xs text-gray-600 dark:text-gray-400 break-all">{{ $log->user_agent }}</div>
                                @php
                                    $ua = $log->user_agent;
                                    $browser = 'Unknown';
                                    $os = 'Unknown';

                                    if(str_contains($ua, 'Firefox')) $browser = '🦊 Firefox';
                                    elseif(str_contains($ua, 'Chrome')) $browser = '🌐 Chrome';
                                    elseif(str_contains($ua, 'Safari')) $browser = '🧭 Safari';
                                    elseif(str_contains($ua, 'Edge')) $browser = '🔷 Edge';

                                    if(str_contains($ua, 'Windows')) $os = '💻 Windows';
                                    elseif(str_contains($ua, 'Mac')) $os = '🍎 macOS';
                                    elseif(str_contains($ua, 'Linux')) $os = '🐧 Linux';
                                    elseif(str_contains($ua, 'Android')) $os = '📱 Android';
                                    elseif(str_contains($ua, 'iPhone')) $os = '📱 iOS';
                                @endphp
                                <div class="mt-2 flex flex-wrap gap-2">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-primary bg-opacity-10 px-3 py-1 text-xs font-medium text-primary">
                                        {{ $browser }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-success bg-opacity-10 px-3 py-1 text-xs font-medium text-success">
                                        {{ $os }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- User Info Card -->
            @if($log->user)
                <div class="rounded-xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
                    <div class="border-b border-stroke bg-gradient-to-r from-success to-emerald-600 px-6 py-4">
                        <h3 class="font-semibold text-white flex items-center gap-2">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
                            </svg>
                            {{ __('User Information') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="flex flex-col items-center text-center mb-4">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-success to-emerald-600 text-white text-3xl font-bold mb-3 shadow-lg">
                                {{ strtoupper(substr($log->user->name, 0, 1)) }}
                            </div>
                            <h4 class="font-bold text-lg text-black dark:text-white">{{ $log->user->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $log->user->email }}</p>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-meta-4">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Role</span>
                                <span class="inline-flex rounded-full bg-success bg-opacity-10 px-3 py-1 text-xs font-medium text-success">
                                    {{ ucfirst($log->user->role) }}
                                </span>
                            </div>

                            @if($log->user->national_id)
                                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-meta-4">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">National ID</span>
                                    <span class="text-sm font-medium text-black dark:text-white">{{ $log->user->national_id }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Timestamp Card -->
            <div class="rounded-xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
                <div class="border-b border-stroke bg-gradient-to-r from-warning to-orange-600 px-6 py-4">
                    <h3 class="font-semibold text-white flex items-center gap-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" fill="currentColor"/>
                        </svg>
                        {{ __('Timeline') }}
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-xs font-medium text-gray-500 uppercase block mb-2">Created At</label>
                        <div class="flex items-center gap-2 p-3 rounded-lg bg-warning bg-opacity-10">
                            <svg class="fill-warning" width="18" height="18" viewBox="0 0 24 24">
                                <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10z"/>
                            </svg>
                            <div>
                                <div class="text-sm font-medium text-black dark:text-white">{{ $log->created_at->format('M d, Y - H:i:s') }}</div>
                                <div class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    </div>

                    @if($log->xapi_sent_at)
                        <div>
                            <label class="text-xs font-medium text-gray-500 uppercase block mb-2">xAPI Sent At</label>
                            <div class="flex items-center gap-2 p-3 rounded-lg bg-success bg-opacity-10">
                                <svg class="fill-success" width="18" height="18" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                                <div>
                                    <div class="text-sm font-medium text-black dark:text-white">{{ $log->xapi_sent_at->format('M d, Y - H:i:s') }}</div>
                                    <div class="text-xs text-gray-500">{{ $log->xapi_sent_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- xAPI Status Card -->
            <div class="rounded-xl border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark overflow-hidden">
                <div class="border-b border-stroke px-6 py-4 {{ $log->xapi_sent ? 'bg-gradient-to-r from-success to-emerald-600' : 'bg-gradient-to-r from-warning to-orange-600' }}">
                    <h3 class="font-semibold text-white flex items-center gap-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                            <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z" fill="currentColor"/>
                        </svg>
                        xAPI Status
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex flex-col items-center text-center">
                        @if($log->xapi_sent)
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-success bg-opacity-10 mb-3">
                                <svg class="fill-success" width="32" height="32" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-bold text-success mb-1">✓ Successfully Sent</h4>
                            <p class="text-sm text-gray-500">This activity has been synced to the LRS</p>
                        @else
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-warning bg-opacity-10 mb-3">
                                <svg class="fill-warning" width="32" height="32" viewBox="0 0 24 24">
                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                </svg>
                            </div>
                            <h4 class="text-lg font-bold text-warning mb-1">⏳ Pending Sync</h4>
                            <p class="text-sm text-gray-500">Will be synced in the next batch</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
