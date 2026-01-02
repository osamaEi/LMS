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

    <!-- Quizzes Subject Selection Modal -->
    <div id="quizzes-info-modal" class="fixed inset-0 z-50 hidden" onclick="if(event.target === this) this.classList.add('hidden')">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6" onclick="event.stopPropagation()">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">اختر المادة</h3>
                    <button onclick="document.getElementById('quizzes-info-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-4">اختر المادة للوصول إلى اختباراتها</p>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @auth
                        @if(auth()->user()->role === 'teacher')
                            @php
                                $teacherSubjects = \App\Models\Subject::where('teacher_id', auth()->id())->get();
                            @endphp
                            @forelse($teacherSubjects as $subj)
                            <a href="{{ route('teacher.quizzes.index', $subj->id) }}"
                               class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $subj->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $subj->term->name ?? '' }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            @empty
                            <p class="text-center text-gray-500 py-4">لا توجد مواد مسندة إليك</p>
                            @endforelse
                        @elseif(auth()->user()->role === 'student')
                            @php
                                $studentSubjects = \App\Models\Subject::whereHas('enrollments', function($q) {
                                    $q->where('student_id', auth()->id());
                                })->get();
                            @endphp
                            @forelse($studentSubjects as $subj)
                            <a href="{{ route('student.quizzes.index', $subj->id) }}"
                               class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $subj->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $subj->term->name ?? '' }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                            @empty
                            <p class="text-center text-gray-500 py-4">لم يتم التسجيل في أي مادة بعد</p>
                            @endforelse
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </div>

    @stack('head-scripts')
    <script defer src="{{ asset('js/tailadmin.js') }}"></script>
    @stack('scripts')
</body>
</html>
