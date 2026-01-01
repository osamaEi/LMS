<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة التحكم') - نظام إدارة التعلم</title>

    <!-- Cairo Font -->
   

    <link href="{{ asset('css/tailadmin.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>

<style>
    body, * {
     font-family: 'Cairo';
            font-style: normal;
            font-weight: 400;
            src: url('/font/static/Cairo-Bold.ttf') format('truetype');
    }
    [x-cloak] { display: none !important; }
</style>

    <!-- Preloader -->
    <div
        x-show="loaded"
        x-init="window.addEventListener('DOMContentLoaded', () => {setTimeout(() => loaded = false, 500)})"
        class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black"
    >
        <div class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent"></div>
    </div>

    <!-- Page Wrapper -->
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Content Area -->
        <div class="relative flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
            <!-- Header -->
            @include('layouts.partials.header')

            <!-- Main Content -->
            <main class="bg-white dark:bg-gray-900">
                <div class="p-4 mx-auto max-w-screen-2xl md:p-6 space-y-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('head-scripts')
    <script defer src="{{ asset('js/tailadmin.js') }}"></script>
    @stack('scripts')
</body>
</html>
