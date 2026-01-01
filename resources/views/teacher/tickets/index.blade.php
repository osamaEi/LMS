@extends('layouts.dashboard')

@section('title', 'تذاكر الدعم')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">تذاكر الدعم الفني</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">إدارة ومتابعة طلبات الدعم الفني</p>
</div>

@if(session('success'))
<div class="mb-4 rounded-lg bg-green-50 p-4 text-sm text-green-600 dark:bg-green-900 dark:text-green-200">
    {{ session('success') }}
</div>
@endif

<!-- Statistics Cards -->
<div class="flex flex-wrap gap-4 mb-6">
    <div class="flex-1 min-w-[200px] rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm" style="color: rgba(255,255,255,0.8);">إجمالي التذاكر</p>
                <p class="text-2xl font-bold text-white">{{ $tickets->total() }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>
    </div>

    <div class="flex-1 min-w-[200px] rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm" style="color: rgba(255,255,255,0.8);">مفتوحة</p>
                <p class="text-2xl font-bold text-white">{{ $tickets->where('status', 'open')->count() }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="flex-1 min-w-[200px] rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm" style="color: rgba(255,255,255,0.8);">قيد المعالجة</p>
                <p class="text-2xl font-bold text-white">{{ $tickets->where('status', 'in_progress')->count() }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    <div class="flex-1 min-w-[200px] rounded-xl shadow-lg p-5" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm" style="color: rgba(255,255,255,0.8);">تم الحل</p>
                <p class="text-2xl font-bold text-white">{{ $tickets->where('status', 'resolved')->count() }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl" style="background: rgba(255,255,255,0.2);">
                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Action Button -->
<div class="mb-6">
    <a href="{{ route('teacher.tickets.create') }}"
       class="inline-flex items-center gap-2 rounded-lg px-5 py-2.5 text-sm font-medium text-white transition-all shadow-lg hover:shadow-xl"
       style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        إنشاء تذكرة جديدة
    </a>
</div>

<!-- Tickets Table -->
<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 overflow-hidden shadow-lg">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr style="background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);">
                    <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase">#</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase">الموضوع</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase">الفئة</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase">الأولوية</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase">الحالة</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase">الردود</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase">التاريخ</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-white uppercase">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">#{{ $ticket->id }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900 dark:text-white">{{ Str::limit($ticket->subject, 40) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $categoryLabels = [
                                'technical' => 'تقني',
                                'academic' => 'أكاديمي',
                                'financial' => 'مالي',
                                'other' => 'أخرى',
                            ];
                        @endphp
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $categoryLabels[$ticket->category] ?? $ticket->category }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php
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
                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full" style="{{ $priorityStyles[$ticket->priority] ?? '' }}">
                            {{ $priorityLabels[$ticket->priority] ?? $ticket->priority }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
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
                        @endphp
                        <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full" style="{{ $statusStyles[$ticket->status] ?? '' }}">
                            {{ $statusLabels[$ticket->status] ?? $ticket->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 text-gray-600 dark:text-gray-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                            </svg>
                            {{ $ticket->replies_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-sm">{{ $ticket->created_at->diffForHumans() }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('teacher.tickets.show', $ticket) }}"
                           class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-medium text-white transition-all"
                           style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            عرض
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">لا توجد تذاكر</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">أنشئ تذكرة جديدة للتواصل مع الدعم الفني</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tickets->hasPages())
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $tickets->links() }}
    </div>
    @endif
</div>
@endsection
