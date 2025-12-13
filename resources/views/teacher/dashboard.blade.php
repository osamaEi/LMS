@extends('layouts.dashboard')

@section('title', 'لوحة المعلم')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">مرحباً {{ auth()->user()->name }}</h1>
        <p class="text-gray-600 mt-1">لوحة تحكم المعلم</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">المواد</p>
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
                    <p class="text-gray-600 text-sm">الطلاب</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_students'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">الجلسات</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total_sessions'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm12.553 1.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
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
                <div class="p-6 border-b">
                    <h2 class="text-lg font-bold text-gray-900">المواد الدراسية</h2>
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
                                        <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
                                            <span>👥 {{ $subject->enrollments_count }} طالب</span>
                                        </div>
                                    </div>
                                    <a href="{{ route('teacher.subjects.show', $subject->id) }}"
                                       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                                        عرض التفاصيل
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 text-center py-8">لا توجد مواد دراسية</p>
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
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                                <div>
                                    <div class="font-medium text-gray-900">{{ $session->title }}</div>
                                    <div class="text-sm text-gray-600">{{ $session->subject->name }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 bg-{{ $session->status === 'live' ? 'red' : ($session->status === 'completed' ? 'green' : 'blue') }}-100 text-{{ $session->status === 'live' ? 'red' : ($session->status === 'completed' ? 'green' : 'blue') }}-700 rounded text-xs">
                                        {{ $session->status }}
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
                            <div class="border-l-4 border-blue-500 pl-4 py-2">
                                <div class="font-medium text-gray-900 text-sm">{{ $session->title }}</div>
                                <div class="text-xs text-gray-600 mt-1">{{ $session->subject->name }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y-m-d H:i') }}
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 text-center py-8 text-sm">لا توجد جلسات قادمة</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
