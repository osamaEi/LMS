@extends('layouts.dashboard')

@section('title', 'تذاكر الدعم')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تذاكر الدعم</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">إدارة تذاكر الدعم الفني الخاصة بك</p>
        </div>
        <a href="{{ route('student.tickets.create') }}"
           class="inline-flex items-center px-5 py-2.5 text-white font-medium rounded-xl transition-all"
           style="background-color: #0071AA;">
            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            تذكرة جديدة
        </a>
    </div>

    <!-- Tickets List -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        @if($tickets->count() > 0)
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($tickets as $ticket)
                    <a href="{{ route('student.tickets.show', $ticket) }}" class="block p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <!-- Status Icon -->
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0"
                                    style="background-color: @if($ticket->status == 'open') #fef3c7 @elseif($ticket->status == 'in_progress') #dbeafe @elseif($ticket->status == 'resolved') #d1fae5 @else #f3f4f6 @endif;">
                                    @if($ticket->status == 'open')
                                        <svg class="w-6 h-6" style="color: #f59e0b;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @elseif($ticket->status == 'in_progress')
                                        <svg class="w-6 h-6" style="color: #3b82f6;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                    @elseif($ticket->status == 'resolved')
                                        <svg class="w-6 h-6" style="color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @else
                                        <svg class="w-6 h-6" style="color: #6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @endif
                                </div>

                                <!-- Ticket Info -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">#{{ $ticket->id }}</span>
                                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $ticket->subject }}</h3>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">{{ Str::limit($ticket->message, 100) }}</p>
                                    <div class="flex flex-wrap items-center gap-3 mt-2">
                                        <!-- Category -->
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium"
                                            style="background-color: #e6f4fa; color: #0071AA;">
                                            @switch($ticket->category)
                                                @case('technical') دعم فني @break
                                                @case('academic') أكاديمي @break
                                                @case('financial') مالي @break
                                                @default أخرى
                                            @endswitch
                                        </span>
                                        <!-- Priority -->
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium"
                                            style="background-color: @if($ticket->priority == 'high') #fee2e2 @elseif($ticket->priority == 'medium') #fef3c7 @else #d1fae5 @endif;
                                                   color: @if($ticket->priority == 'high') #dc2626 @elseif($ticket->priority == 'medium') #d97706 @else #059669 @endif;">
                                            @switch($ticket->priority)
                                                @case('high') عالية @break
                                                @case('medium') متوسطة @break
                                                @default منخفضة
                                            @endswitch
                                        </span>
                                        <!-- Date -->
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $ticket->created_at->diffForHumans() }}
                                        </span>
                                        @if($ticket->replies_count > 0)
                                            <span class="inline-flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                                </svg>
                                                {{ $ticket->replies_count }} رد
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold text-white"
                                    style="background-color: @if($ticket->status == 'open') #f59e0b @elseif($ticket->status == 'in_progress') #3b82f6 @elseif($ticket->status == 'resolved') #10b981 @else #6b7280 @endif;">
                                    @switch($ticket->status)
                                        @case('open') مفتوحة @break
                                        @case('in_progress') قيد المعالجة @break
                                        @case('resolved') تم الحل @break
                                        @default مغلقة
                                    @endswitch
                                </span>
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($tickets->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $tickets->links() }}
                </div>
            @endif
        @else
            <div class="p-16 text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">لا توجد تذاكر</h3>
                <p class="mt-2 text-gray-500 dark:text-gray-400 max-w-sm mx-auto">لم تقم بإنشاء أي تذاكر دعم بعد</p>
                <a href="{{ route('student.tickets.create') }}"
                   class="inline-flex items-center mt-6 px-5 py-2.5 text-white font-medium rounded-xl"
                   style="background-color: #0071AA;">
                    <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إنشاء تذكرة جديدة
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
