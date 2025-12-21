@extends('layouts.dashboard')

@section('title', 'تذكرة #' . $ticket->id)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.tickets.index') }}" class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">تذكرة #{{ $ticket->id }}</h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $ticket->subject }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full {{ $ticket->getStatusColorClass() }}">
                {{ $ticket->getStatusLabel() }}
            </span>
            <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full {{ $ticket->getPriorityColorClass() }}">
                {{ $ticket->getPriorityLabel() }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Ticket Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Original Message -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-brand-100 text-brand-600 rounded-full flex items-center justify-center font-bold">
                        {{ substr($ticket->user?->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $ticket->user?->name }}</div>
                        <div class="text-sm text-gray-500">{{ $ticket->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
                <div class="prose dark:prose-invert max-w-none">
                    {!! nl2br(e($ticket->message)) !!}
                </div>
            </div>

            <!-- Replies -->
            @foreach($ticket->replies as $reply)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 {{ $reply->isFromStaff() ? 'border-r-4 border-r-brand-500' : '' }}">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 {{ $reply->isFromStaff() ? 'bg-brand-500 text-white' : 'bg-gray-100 text-gray-600' }} rounded-full flex items-center justify-center font-bold">
                        {{ substr($reply->user?->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">
                            {{ $reply->user?->name }}
                            @if($reply->isFromStaff())
                            <span class="text-xs bg-brand-100 text-brand-700 px-2 py-0.5 rounded mr-2">فريق الدعم</span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500">{{ $reply->created_at->format('Y/m/d H:i') }}</div>
                    </div>
                </div>
                <div class="prose dark:prose-invert max-w-none">
                    {!! nl2br(e($reply->message)) !!}
                </div>
            </div>
            @endforeach

            <!-- Reply Form -->
            <form action="{{ route('admin.tickets.reply', $ticket) }}" method="POST" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">إضافة رد</h3>
                <textarea name="message" rows="4" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand-500 dark:bg-gray-700 dark:text-white" placeholder="اكتب ردك هنا..."></textarea>
                <div class="flex items-center justify-between mt-4">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300 text-brand-500">
                        <span class="text-sm text-gray-600 dark:text-gray-400">ملاحظة داخلية (لن تظهر للمستخدم)</span>
                    </label>
                    <button type="submit" class="px-6 py-2 text-white bg-brand-500 rounded-lg hover:bg-brand-600">إرسال الرد</button>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Ticket Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">معلومات التذكرة</h3>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm text-gray-500">الفئة</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $ticket->getCategoryLabel() }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-gray-500">تاريخ الإنشاء</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $ticket->created_at->format('Y/m/d H:i') }}</dd>
                    </div>
                    @if($ticket->first_response_at)
                    <div>
                        <dt class="text-sm text-gray-500">أول رد</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $ticket->first_response_at->format('Y/m/d H:i') }}</dd>
                    </div>
                    @endif
                    @if($ticket->resolved_at)
                    <div>
                        <dt class="text-sm text-gray-500">تاريخ الحل</dt>
                        <dd class="font-medium text-gray-900 dark:text-white">{{ $ticket->resolved_at->format('Y/m/d H:i') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">إجراءات</h3>

                <!-- Status -->
                <form action="{{ route('admin.tickets.status', $ticket) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تغيير الحالة</label>
                    <select name="status" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>مفتوحة</option>
                        <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>تم الحل</option>
                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>مغلقة</option>
                    </select>
                </form>

                <!-- Priority -->
                <form action="{{ route('admin.tickets.priority', $ticket) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تغيير الأولوية</label>
                    <select name="priority" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>منخفضة</option>
                        <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>متوسطة</option>
                        <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>عالية</option>
                        <option value="urgent" {{ $ticket->priority === 'urgent' ? 'selected' : '' }}>عاجلة</option>
                    </select>
                </form>

                <!-- Assign -->
                <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تعيين إلى</label>
                    <select name="assigned_to" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="">-- اختر --</option>
                        @foreach($staff as $member)
                        <option value="{{ $member->id }}" {{ $ticket->assigned_to == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
