@extends('layouts.dashboard')

@section('title', 'xAPI Dashboard')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-title-md2 font-bold text-black dark:text-white">
            xAPI Dashboard / لوحة تحكم xAPI
        </h2>
    </div>

    <!-- Status Cards -->
    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <h4 class="text-title-md font-bold text-black dark:text-white">{{ number_format($stats['total']) }}</h4>
            <span class="text-sm font-medium">Total Statements</span>
        </div>
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <h4 class="text-title-md font-bold text-warning">{{ number_format($stats['pending']) }}</h4>
            <span class="text-sm font-medium">Pending</span>
        </div>
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <h4 class="text-title-md font-bold text-success">{{ number_format($stats['sent']) }}</h4>
            <span class="text-sm font-medium">Sent</span>
        </div>
        <div class="rounded-sm border border-stroke bg-white py-6 px-7.5 shadow-default dark:border-strokedark dark:bg-boxdark">
            <h4 class="text-title-md font-bold text-danger">{{ number_format($stats['failed']) }}</h4>
            <span class="text-sm font-medium">Failed</span>
        </div>
    </div>

    <!-- Status & Actions -->
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
            <h3 class="font-medium text-black dark:text-white">Configuration Status</h3>
        </div>
        <div class="p-7">
            <div class="mb-4">
                <span class="text-sm font-medium">xAPI Enabled: </span>
                @if($enabled)
                    <span class="inline-flex rounded-full bg-opacity-10 py-1 px-3 text-sm font-medium bg-success text-success">✓ Yes</span>
                @else
                    <span class="inline-flex rounded-full bg-opacity-10 py-1 px-3 text-sm font-medium bg-danger text-danger">✗ No</span>
                    <p class="text-sm text-gray-500 mt-2">Set XAPI_ENABLED=true in .env to enable</p>
                @endif
            </div>
            <div class="mb-4">
                <span class="text-sm font-medium">LRS Configured: </span>
                @if($configured)
                    <span class="inline-flex rounded-full bg-opacity-10 py-1 px-3 text-sm font-medium bg-success text-success">✓ Yes</span>
                @else
                    <span class="inline-flex rounded-full bg-opacity-10 py-1 px-3 text-sm font-medium bg-danger text-danger">✗ No</span>
                    <p class="text-sm text-gray-500 mt-2">Set XAPI_LRS_ENDPOINT, XAPI_LRS_USERNAME, XAPI_LRS_PASSWORD in .env</p>
                @endif
            </div>

            <div class="flex gap-3 mt-6">
                <button onclick="testConnection()" class="inline-flex items-center justify-center rounded-md bg-primary py-3 px-6 text-center font-medium text-white hover:bg-opacity-90">
                    Test LRS Connection
                </button>
                <button onclick="processPending()" class="inline-flex items-center justify-center rounded-md bg-meta-3 py-3 px-6 text-center font-medium text-white hover:bg-opacity-90">
                    Process Pending Now
                </button>
            </div>
            <div id="result-message" class="mt-4"></div>
        </div>
    </div>
</div>

<script>
function testConnection() {
    document.getElementById('result-message').innerHTML = '<p class="text-sm text-gray-500">Testing connection...</p>';
    fetch('{{ route('admin.xapi.test-connection') }}', { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
        .then(r => r.json())
        .then(data => {
            const cls = data.success ? 'text-success' : 'text-danger';
            document.getElementById('result-message').innerHTML = `<p class="text-sm ${cls}">${data.message}</p>`;
        });
}

function processPending() {
    document.getElementById('result-message').innerHTML = '<p class="text-sm text-gray-500">Processing...</p>';
    fetch('{{ route('admin.xapi.process-pending') }}', { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}})
        .then(r => r.json())
        .then(data => {
            document.getElementById('result-message').innerHTML = `<p class="text-sm text-success">${data.message}</p>`;
            setTimeout(() => location.reload(), 2000);
        });
}
</script>
@endsection
