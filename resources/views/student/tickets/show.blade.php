@extends('layouts.dashboard')

@section('title', 'تذكرة #' . $ticket->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.tickets.index') }}"
               class="w-10 h-10 rounded-xl flex items-center justify-center bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 dark:text-gray-400">#{{ $ticket->id }}</span>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $ticket->subject }}</h1>
                </div>
                <div class="flex items-center gap-3 mt-1">
                    <!-- Status -->
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold text-white"
                        style="background-color: @if($ticket->status == 'open') #f59e0b @elseif($ticket->status == 'in_progress') #3b82f6 @elseif($ticket->status == 'resolved') #10b981 @else #6b7280 @endif;">
                        @switch($ticket->status)
                            @case('open') مفتوحة @break
                            @case('in_progress') قيد المعالجة @break
                            @case('resolved') تم الحل @break
                            @default مغلقة
                        @endswitch
                    </span>
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
                            @case('high') أولوية عالية @break
                            @case('medium') أولوية متوسطة @break
                            @default أولوية منخفضة
                        @endswitch
                    </span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $ticket->created_at->format('Y/m/d H:i') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl p-4" style="background-color: #d1fae5;">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" style="color: #10b981;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span style="color: #065f46;">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Messages -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            <!-- Original Message -->
            <div class="p-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #0071AA;">
                        <span class="text-white font-bold">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-bold text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400 ms-2">أنت</span>
                            </div>
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $ticket->created_at->format('Y/m/d H:i') }}</span>
                        </div>
                        <div class="mt-3 text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $ticket->message }}</div>
                    </div>
                </div>
            </div>

            <!-- Replies -->
            @foreach($ticket->replies as $reply)
                <div class="p-6 @if($reply->user_id != auth()->id()) bg-gray-50 dark:bg-gray-700/50 @endif">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0"
                            style="background-color: @if($reply->user_id == auth()->id()) #0071AA @else #10b981 @endif;">
                            <span class="text-white font-bold">{{ mb_substr($reply->user->name ?? 'D', 0, 1) }}</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-bold text-gray-900 dark:text-white">{{ $reply->user->name ?? 'فريق الدعم' }}</span>
                                    @if($reply->user_id == auth()->id())
                                        <span class="text-sm text-gray-500 dark:text-gray-400 ms-2">أنت</span>
                                    @else
                                        <span class="inline-flex items-center ms-2 px-2 py-0.5 rounded text-xs font-medium" style="background-color: #d1fae5; color: #065f46;">
                                            فريق الدعم
                                        </span>
                                    @endif
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $reply->created_at->format('Y/m/d H:i') }}</span>
                            </div>
                            <div class="mt-3 text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $reply->message }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Reply Form -->
        @if(!in_array($ticket->status, ['closed']))
            <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30">
                <form action="{{ route('student.tickets.reply', $ticket) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            إضافة رد
                        </label>
                        <textarea name="message" id="message" rows="4" required
                                  class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                  placeholder="اكتب ردك هنا..."></textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center px-5 py-2.5 text-white font-medium rounded-xl"
                                style="background-color: #0071AA;">
                            <svg class="w-5 h-5 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            إرسال الرد
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/30 text-center">
                <p class="text-gray-500 dark:text-gray-400">هذه التذكرة مغلقة ولا يمكن إضافة ردود جديدة</p>
            </div>
        @endif
    </div>
</div>
@endsection
