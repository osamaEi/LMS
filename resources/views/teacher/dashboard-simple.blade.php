@extends('layouts.dashboard')

@section('title', 'لوحة المعلم - عرض مبسط')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">مرحباً {{ auth()->user()->name }}</h1>
        <p class="text-gray-600 dark:text-gray-400 mt-1">لوحة تحكم المعلم - عرض مبسط</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 w-full">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="text-4xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['subjects_count'] }}</div>
            <p class="text-gray-600 dark:text-gray-400 mt-2">المواد الدراسية</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="text-4xl font-bold text-green-600 dark:text-green-400">{{ $stats['total_students'] }}</div>
            <p class="text-gray-600 dark:text-gray-400 mt-2">إجمالي الطلاب</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="text-4xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['total_sessions'] }}</div>
            <p class="text-gray-600 dark:text-gray-400 mt-2">الجلسات</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 hover:shadow-lg transition">
            <div class="text-4xl font-bold text-orange-600 dark:text-orange-400">{{ $stats['live_sessions'] }}</div>
            <p class="text-gray-600 dark:text-gray-400 mt-2">جلسات مباشرة</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">

            <!-- My Subjects Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-lg font-bold text-white">المواد الدراسية</h2>
                </div>
                <div class="p-6">
                    @if($subjects->count() > 0)
                        <div class="space-y-3">
                            @foreach($subjects as $subject)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $subject->name }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $subject->term->name ?? 'N/A' }} • {{ $subject->enrollments_count }} طالب
                                    </p>
                                </div>
                                <a href="{{ route('teacher.subjects.show', $subject->id) }}" 
                                   class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700 transition">
                                    عرض
                                </a>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-600 dark:text-gray-400 py-8">لا توجد مواد دراسية مسندة إليك</p>
                    @endif
                </div>
            </div>

            <!-- Upcoming Sessions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h2 class="text-lg font-bold text-white">الجلسات القادمة</h2>
                </div>
                <div class="p-6">
                    @if($upcomingSessions->count() > 0)
                        <div class="space-y-3">
                            @foreach($upcomingSessions as $session)
                            <div class="flex items-start justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border-l-4 border-green-500">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $session->title }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $session->subject->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">
                                        📅 {{ \Carbon\Carbon::parse($session->scheduled_at)->format('Y-m-d H:i') }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-600 dark:text-gray-400 py-8">لا توجد جلسات قادمة</p>
                    @endif
                </div>
            </div>

        </div>

        <!-- Sidebar -->
        <div class="space-y-6">

            <!-- Support Tickets -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-4">
                    <h2 class="text-lg font-bold text-white">تذاكر الدعم</h2>
                </div>
                <div class="p-6">
                    <a href="{{ route('teacher.tickets.create') }}" 
                       class="w-full mb-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm transition">
                        + تذكرة جديدة
                    </a>
                    @if(isset($myTickets) && $myTickets->count() > 0)
                        <div class="space-y-2">
                            @foreach($myTickets as $ticket)
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                <div class="font-medium text-sm text-gray-900 dark:text-white">{{ Str::limit($ticket->subject, 20) }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $ticket->created_at->diffForHumans() }}</div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center text-gray-600 dark:text-gray-400 text-sm py-4">لا توجد تذاكر</p>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-purple-700 px-6 py-4">
                    <h2 class="text-lg font-bold text-white">روابط سريعة</h2>
                </div>
                <div class="p-6 space-y-2">
                    <a href="{{ route('teacher.my-subjects.index') }}" 
                       class="block w-full px-4 py-2 text-center bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-200 rounded hover:bg-blue-200 dark:hover:bg-blue-800 transition text-sm">
                        مواداتي الدراسية
                    </a>
                    <a href="{{ route('teacher.schedule') }}" 
                       class="block w-full px-4 py-2 text-center bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-200 rounded hover:bg-green-200 dark:hover:bg-green-800 transition text-sm">
                        الجدول الزمني
                    </a>
                    <a href="{{ route('teacher.students.index') }}" 
                       class="block w-full px-4 py-2 text-center bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-200 rounded hover:bg-purple-200 dark:hover:bg-purple-800 transition text-sm">
                        إدارة الطلاب
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
