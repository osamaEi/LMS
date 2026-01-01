@extends('layouts.dashboard')

@section('title', 'إدارة تذاكر الدعم')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">تذاكر الدعم الفني</h2>
        <p class="text-gray-600 dark:text-gray-400">إدارة طلبات الدعم الفني (معيار NELC 1.3.3)</p>
    </div>

    <!-- Stats -->
    <div class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[180px] bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">إجمالي التذاكر</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="flex-1 min-w-[180px] bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-red-500">{{ $stats['open'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">مفتوحة</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="flex-1 min-w-[180px] bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-yellow-500">{{ $stats['in_progress'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">قيد المعالجة</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="flex-1 min-w-[180px] bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-green-500">{{ $stats['resolved'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">تم الحل</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="flex-1 min-w-[180px] bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-3xl font-bold text-brand-500">{{ $stats['avg_response_time'] }}د</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">متوسط وقت الرد</div>
                </div>
                <div class="w-12 h-12 rounded-full bg-brand-100 dark:bg-brand-900/30 flex items-center justify-center">
                    <svg class="w-6 h-6 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="flex flex-wrap gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
        <select name="status" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            <option value="all">كل الحالات</option>
            <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>مفتوحة</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
            <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>تم الحل</option>
            <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>مغلقة</option>
        </select>

        <select name="priority" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            <option value="all">كل الأولويات</option>
            <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>عاجلة</option>
            <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>عالية</option>
            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>متوسطة</option>
            <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>منخفضة</option>
        </select>

        <select name="category" onchange="this.form.submit()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
            <option value="all">كل الفئات</option>
            <option value="technical" {{ request('category') === 'technical' ? 'selected' : '' }}>تقني</option>
            <option value="academic" {{ request('category') === 'academic' ? 'selected' : '' }}>أكاديمي</option>
            <option value="financial" {{ request('category') === 'financial' ? 'selected' : '' }}>مالي</option>
            <option value="other" {{ request('category') === 'other' ? 'selected' : '' }}>أخرى</option>
        </select>
    </form>

    <!-- Tickets Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الموضوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المستخدم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الفئة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الأولوية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المعين</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($tickets as $ticket)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer" onclick="window.location='{{ route('admin.tickets.show', $ticket) }}'">
                        <td class="px-6 py-4 text-gray-900 dark:text-white font-medium">#{{ $ticket->id }}</td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900 dark:text-white">{{ Str::limit($ticket->subject, 40) }}</div>
                            <div class="text-sm text-gray-500">{{ $ticket->replies_count }} رد</div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $ticket->user?->name }}</td>
                        <td class="px-6 py-4">
                            <span class="text-sm">{{ $ticket->getCategoryLabel() }}</span>
                        </td>
                        <td class="px-6 py-4">
                       
                        </td>
                        <td class="px-6 py-4">
                         
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $ticket->assignedTo?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-sm">{{ $ticket->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                            لا توجد تذاكر
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
</div>
@endsection
