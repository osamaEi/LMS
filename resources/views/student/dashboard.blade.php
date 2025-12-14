@extends('layouts.dashboard')

@section('title', 'لوحة الطالب')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">مرحباً {{ auth()->user()->name }}</h1>
        <p class="text-gray-600 mt-1">لوحة تحكم الطالب</p>
    </div>

    <!-- Live Sessions Alert -->
    @if($liveSessions->count() > 0)
    <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-6 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-red-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="mr-3 flex-1">
                <h3 class="text-lg font-bold text-red-800">جلسة مباشرة الآن!</h3>
                <div class="mt-2 space-y-2">
                    @foreach($liveSessions as $session)
                    <div class="flex items-center justify-between bg-white rounded p-3">
                        <div>
                            <div class="font-medium text-gray-900">{{ $session->title }}</div>
                            <div class="text-sm text-gray-600">{{ $session->subject->name }}</div>
                        </div>
                        <a href="{{ route('admin.sessions.show', $session->id) }}"
                           class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                            انضم الآن
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">موادي</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['subjects_count'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">إجمالي الجلسات</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_sessions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">جلسات مكتملة</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['completed_sessions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">مباشر الآن</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['live_sessions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- My Subjects -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">موادي الدراسية</h2>
                    <a href="{{ route('student.schedule') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        عرض الجدول →
                    </a>
                </div>
                <div class="p-6">
                    @if($subjects->count() > 0)
                        <div class="space-y-4">
                            @foreach($subjects as $subject)
                            <div class="border rounded-lg p-4 hover:border-blue-500 transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900">{{ $subject->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $subject->term->program->name ?? '' }} - {{ $subject->term->name ?? '' }}
                                        </p>
                                        <div class="flex items-center gap-4 mt-2">
                                            <span class="text-sm text-gray-600">
                                                <svg class="w-4 h-4 inline" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                                                </svg>
                                                المعلم: {{ $subject->teacher->name ?? 'غير محدد' }}
                                            </span>
                                            <span class="text-sm text-gray-600">
                                                📹 {{ $subject->sessions_count }} جلسة
                                            </span>
                                        </div>
                                    </div>
                                    <a href="{{ route('student.subjects.show', $subject->id) }}"
                                       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm whitespace-nowrap mr-4">
                                        عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p class="mt-4 text-gray-600">لم يتم تسجيلك في أي مواد حتى الآن</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Sessions -->
            <div class="bg-white rounded-lg shadow mt-6">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-bold text-gray-900">الجلسات الأخيرة</h2>
                </div>
                <div class="p-6">
                    @if($recentSessions->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentSessions as $session)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded hover:bg-gray-100 transition">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $session->title }}</div>
                                    <div class="text-sm text-gray-600">{{ $session->subject->name }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 bg-{{ $session->status === 'live' ? 'red' : ($session->status === 'completed' ? 'green' : 'blue') }}-100 text-{{ $session->status === 'live' ? 'red' : ($session->status === 'completed' ? 'green' : 'blue') }}-700 rounded text-xs">
                                        {{ $session->status === 'live' ? 'مباشر' : ($session->status === 'completed' ? 'مكتمل' : 'مجدول') }}
                                    </span>
                                    <a href="{{ route('admin.sessions.show', $session->id) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                        عرض
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 text-center py-4">لا توجد جلسات</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Upcoming Sessions -->
        <div>
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-bold text-gray-900">الجلسات القادمة</h2>
                </div>
                <div class="p-6">
                    @if($upcomingSessions->count() > 0)
                        <div class="space-y-4">
                            @foreach($upcomingSessions as $session)
                            <div class="border-r-4 border-blue-500 pr-4 py-2">
                                <div class="font-medium text-gray-900 text-sm">{{ $session->title }}</div>
                                <div class="text-xs text-gray-600 mt-1">{{ $session->subject->name }}</div>
                                <div class="flex items-center gap-1 text-xs text-gray-500 mt-1">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y-m-d H:i') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->diffForHumans() }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2 text-gray-600 text-sm">لا توجد جلسات قادمة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
