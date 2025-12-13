@extends('layouts.dashboard')

@section('title', $subject->name)

@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">

    <div class="mb-6">
        <a href="{{ route('teacher.dashboard') }}" class="text-blue-600 hover:text-blue-800 text-sm mb-2 inline-block">← العودة</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $subject->name }}</h1>
        <p class="text-gray-600 mt-1">{{ $subject->term->program->name ?? '' }} - {{ $subject->term->name ?? '' }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-bold">إضافة جلسة جديدة</h2>
                </div>
                <div class="p-6">
                    <a href="{{ route('admin.sessions.create', ['subject_id' => $subject->id]) }}" class="block w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center font-medium">
                        + إضافة جلسة (Zoom / فيديو / ملفات)
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-bold">الجلسات ({{ $sessions->count() }})</h2>
                </div>
                <div class="p-6">
                    @if($sessions->count() > 0)
                        <div class="space-y-3">
                            @foreach($sessions as $session)
                            <div class="border rounded-lg p-4">
                                <div class="flex justify-between">
                                    <div>
                                        <h3 class="font-bold">#{{ $session->session_number }} {{ $session->title }}</h3>
                                        <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded">{{ $session->type === 'live_zoom' ? 'Zoom' : 'فيديو' }}</span>
                                    </div>
                                    <a href="{{ route('admin.sessions.show', $session) }}" class="px-3 py-2 bg-blue-600 text-white rounded text-sm">عرض</a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-center py-8 text-gray-600">لا توجد جلسات</p>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-bold">الطلاب ({{ $students->count() }})</h2>
                </div>
                <div class="p-6">
                    @if($students->count() > 0)
                        @foreach($students as $student)
                        <div class="flex gap-3 p-3 bg-gray-50 rounded mb-2">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <span class="text-blue-600 font-bold">{{ substr($student->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="font-medium text-sm">{{ $student->name }}</div>
                                <div class="text-xs text-gray-600">{{ $student->email }}</div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-center py-8 text-sm text-gray-600">لا يوجد طلاب</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
