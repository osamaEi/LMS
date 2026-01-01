@extends('layouts.dashboard')

@section('title', 'تذكرة #' . $ticket->id)

@section('content')
<div class="mb-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('teacher.tickets.index') }}" class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تذكرة #{{ $ticket->id }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $ticket->subject }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @php
                $statusStyles = [
                    'open' => 'background: linear-gradient(135deg, #ef4444, #dc2626); color: white;',
                    'in_progress' => 'background: linear-gradient(135deg, #f59e0b, #d97706); color: white;',
                    'resolved' => 'background: linear-gradient(135deg, #10b981, #059669); color: white;',
                    'closed' => 'background: linear-gradient(135deg, #6b7280, #4b5563); color: white;',
                ];
                $statusLabels = [
                    'open' => 'مفتوحة',
                    'in_progress' => 'قيد المعالجة',
                    'resolved' => 'تم الحل',
                    'closed' => 'مغلقة',
                ];
                $priorityStyles = [
                    'low' => 'background: linear-gradient(135deg, #6b7280, #4b5563); color: white;',
                    'medium' => 'background: linear-gradient(135deg, #3b82f6, #2563eb); color: white;',
                    'high' => 'background: linear-gradient(135deg, #f59e0b, #d97706); color: white;',
                    'urgent' => 'background: linear-gradient(135deg, #ef4444, #dc2626); color: white;',
                ];
                $priorityLabels = [
                    'low' => 'منخفضة',
                    'medium' => 'متوسطة',
                    'high' => 'عالية',
                    'urgent' => 'عاجلة',
                ];
            @endphp
            <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full" style="{{ $statusStyles[$ticket->status] ?? '' }}">
                {{ $statusLabels[$ticket->status] ?? $ticket->status }}
            </span>
            <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full" style="{{ $priorityStyles[$ticket->priority] ?? '' }}">
                {{ $priorityLabels[$ticket->priority] ?? $ticket->priority }}
            </span>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900 dark:text-green-200">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Ticket Content -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Original Message -->
        <div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden shadow-lg">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-blue-600" style="background: rgba(255,255,255,0.9);">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-medium text-white">{{ auth()->user()->name }}</div>
                        <div class="text-sm" style="color: rgba(255,255,255,0.8);">{{ $ticket->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                    {!! nl2br(e($ticket->description)) !!}
                </div>
            </div>
        </div>

        <!-- Replies -->
        @foreach($ticket->replies as $reply)
        <div class="rounded-xl border {{ $reply->user_id !== auth()->id() ? 'border-r-4' : '' }} border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden shadow-lg" style="{{ $reply->user_id !== auth()->id() ? 'border-right-color: #10b981;' : '' }}">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700" style="{{ $reply->user_id !== auth()->id() ? 'background: linear-gradient(135deg, #10b981 0%, #059669 100%);' : 'background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold" style="{{ $reply->user_id !== auth()->id() ? 'background: rgba(255,255,255,0.9); color: #059669;' : 'background: rgba(255,255,255,0.9); color: #6d28d9;' }}">
                        {{ mb_substr($reply->user?->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <div class="font-medium text-white">
                            {{ $reply->user?->name }}
                            @if($reply->user_id !== auth()->id())
                            <span class="text-xs px-2 py-0.5 rounded mr-2" style="background: rgba(255,255,255,0.2);">فريق الدعم</span>
                            @endif
                        </div>
                        <div class="text-sm" style="color: rgba(255,255,255,0.8);">{{ $reply->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                    {!! nl2br(e($reply->message)) !!}
                </div>
            </div>
        </div>
        @endforeach

        <!-- Reply Form -->
        @if(!in_array($ticket->status, ['closed']))
        <form action="{{ route('teacher.tickets.reply', $ticket) }}" method="POST" class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden shadow-lg">
            @csrf

            <div class="p-4 border-b border-gray-200 dark:border-gray-700" style="background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);">
                <h3 class="text-lg font-semibold text-white">إضافة رد</h3>
            </div>

            <div class="p-6">
                <textarea name="message" rows="4" required
                          class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white transition-colors"
                          placeholder="اكتب ردك هنا..."></textarea>
            </div>

            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end" style="background: #f9fafb;">
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-medium text-white rounded-lg transition-all shadow-lg hover:shadow-xl"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    إرسال الرد
                </button>
            </div>
        </form>
        @else
        <div class="rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900 p-6 text-center">
            <svg class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400 font-medium">هذه التذكرة مغلقة</p>
            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">لا يمكن إضافة ردود جديدة</p>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Ticket Info -->
        <div class="rounded-xl shadow-lg overflow-hidden" style="background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);">
            <div class="p-5 border-b" style="border-color: rgba(255,255,255,0.1);">
                <h3 class="text-base font-bold text-white">معلومات التذكرة</h3>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <dt class="text-sm" style="color: rgba(255,255,255,0.6);">رقم التذكرة</dt>
                    <dd class="font-medium text-white mt-1">#{{ $ticket->id }}</dd>
                </div>

                <div>
                    <dt class="text-sm" style="color: rgba(255,255,255,0.6);">الفئة</dt>
                    <dd class="font-medium text-white mt-1">
                        @php
                            $categoryLabels = [
                                'technical' => 'تقني',
                                'academic' => 'أكاديمي',
                                'financial' => 'مالي',
                                'other' => 'أخرى',
                            ];
                        @endphp
                        {{ $categoryLabels[$ticket->category] ?? $ticket->category }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm" style="color: rgba(255,255,255,0.6);">تاريخ الإنشاء</dt>
                    <dd class="font-medium text-white mt-1">{{ $ticket->created_at->format('Y/m/d H:i') }}</dd>
                </div>

                <div>
                    <dt class="text-sm" style="color: rgba(255,255,255,0.6);">آخر تحديث</dt>
                    <dd class="font-medium text-white mt-1">{{ $ticket->updated_at->diffForHumans() }}</dd>
                </div>

                <div>
                    <dt class="text-sm" style="color: rgba(255,255,255,0.6);">عدد الردود</dt>
                    <dd class="font-medium text-white mt-1">{{ $ticket->replies->count() }}</dd>
                </div>
            </div>
        </div>

        <!-- Status Guide -->
        <div class="rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
            <h3 class="text-base font-bold text-white mb-4 pb-2" style="border-bottom: 1px solid rgba(255,255,255,0.2);">حالات التذكرة</h3>

            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-3 h-3 rounded-full" style="background: #ef4444;"></div>
                    <div>
                        <span class="text-sm font-medium text-white">مفتوحة</span>
                        <p class="text-xs" style="color: rgba(255,255,255,0.7);">بانتظار الرد</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-3 h-3 rounded-full" style="background: #f59e0b;"></div>
                    <div>
                        <span class="text-sm font-medium text-white">قيد المعالجة</span>
                        <p class="text-xs" style="color: rgba(255,255,255,0.7);">يتم العمل عليها</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-3 h-3 rounded-full" style="background: #10b981;"></div>
                    <div>
                        <span class="text-sm font-medium text-white">تم الحل</span>
                        <p class="text-xs" style="color: rgba(255,255,255,0.7);">تم حل المشكلة</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-3 h-3 rounded-full" style="background: #6b7280;"></div>
                    <div>
                        <span class="text-sm font-medium text-white">مغلقة</span>
                        <p class="text-xs" style="color: rgba(255,255,255,0.7);">تم إغلاق التذكرة</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help Card -->
        <div class="rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #ec4899 0%, #be185d 100%);">
            <h3 class="text-sm font-bold text-white mb-2">تحتاج مساعدة؟</h3>
            <p class="text-xs leading-relaxed" style="color: rgba(255,255,255,0.9);">
                إذا لم يتم الرد على تذكرتك خلال 24 ساعة، يرجى التواصل مع الإدارة مباشرة.
            </p>
        </div>
    </div>
</div>
@endsection
