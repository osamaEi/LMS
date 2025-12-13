@extends('layouts.dashboard')

@section('title', 'الطلاب')

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">الطلاب</h1>
        <p class="text-gray-600 mt-1">عرض جميع الطلاب المسجلين في موادك</p>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">إجمالي الطلاب</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $students->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">المواد</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $subjects->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm">إجمالي التسجيلات</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $students->sum(function($s) { return $s->enrollments->count(); }) }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6 border-b">
            <h2 class="text-lg font-bold text-gray-900">قائمة الطلاب</h2>
        </div>
        <div class="p-6">
            @if($students->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-right py-3 px-4 text-sm font-bold text-gray-700">الطالب</th>
                                <th class="text-right py-3 px-4 text-sm font-bold text-gray-700">البريد الإلكتروني</th>
                                <th class="text-right py-3 px-4 text-sm font-bold text-gray-700">المواد المسجلة</th>
                                <th class="text-right py-3 px-4 text-sm font-bold text-gray-700">عدد المواد</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-blue-600 font-bold text-sm">{{ substr($student->name, 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $student->name }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $student->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-sm text-gray-700">{{ $student->email }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($student->enrollments as $enrollment)
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">
                                            {{ $enrollment->subject->name }}
                                        </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-700 rounded-full text-sm font-bold">
                                        {{ $student->enrollments->count() }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="mt-4 text-gray-600">لا يوجد طلاب مسجلين في موادك حالياً</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Subjects Filter (Optional Future Enhancement) -->
    <div class="bg-white rounded-lg shadow mt-6">
        <div class="p-6 border-b">
            <h2 class="text-lg font-bold text-gray-900">الطلاب حسب المادة</h2>
        </div>
        <div class="p-6">
            @if($subjects->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($subjects as $subject)
                    <div class="border rounded-lg p-4 hover:border-blue-500 transition">
                        <h3 class="font-bold text-gray-900 mb-2">{{ $subject->name }}</h3>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">عدد الطلاب:</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-bold">
                                {{ $subject->enrollments()->count() }}
                            </span>
                        </div>
                        <a href="{{ route('teacher.subjects.show', $subject->id) }}"
                           class="block mt-3 text-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                            عرض التفاصيل
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-600 text-center py-4">لا توجد مواد</p>
            @endif
        </div>
    </div>

</div>
@endsection
