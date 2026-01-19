@extends('layouts.dashboard')

@section('title', 'إدارة التسجيلات')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">إدارة التسجيلات</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">عرض وإدارة جميع تسجيلات الجلسات المباشرة</p>
    </div>
</div>

<!-- Filters -->
<div class="mb-6 rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
    <form method="GET" action="{{ route('admin.recordings.index') }}" class="flex flex-wrap gap-4">
        <!-- Status Filter -->
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">الحالة</label>
            <select name="status" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>جميع الحالات</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>جاري المعالجة</option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل</option>
            </select>
        </div>

        <!-- Subject Filter -->
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المادة</label>
            <select name="subject_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
                <option value="">جميع المواد</option>
                @foreach($subjects as $subject)
                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                    {{ $subject->name_ar }}
                </option>
                @endforeach
            </select>
        </div>

        <!-- Search -->
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">بحث</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="ابحث في عنوان الجلسة..."
                   class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm dark:border-gray-700 dark:bg-gray-800 dark:text-white">
        </div>

        <!-- Buttons -->
        <div class="flex items-end gap-2">
            <button type="submit" class="rounded-lg bg-brand-500 px-6 py-2 text-sm font-medium text-white hover:bg-brand-600">
                تصفية
            </button>
            <a href="{{ route('admin.recordings.index') }}" class="rounded-lg bg-gray-200 px-6 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-300">
                إعادة تعيين
            </a>
        </div>
    </form>
</div>

@if(session('success'))
<div class="mb-6 rounded-lg bg-success-50 p-4 text-success-600 dark:bg-success-900 dark:text-success-200">
    {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="mb-6 rounded-lg bg-error-50 p-4 text-error-600 dark:bg-error-900 dark:text-error-200">
    {{ session('error') }}
</div>
@endif

<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800">
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">#</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">عنوان الجلسة</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">المادة</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">تاريخ الجلسة</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">الحالة</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">الحجم</th>
                    <th class="px-6 py-4 text-right text-sm font-medium text-gray-900 dark:text-white">المدة</th>
                    <th class="px-6 py-4 text-center text-sm font-medium text-gray-900 dark:text-white">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($sessions as $session)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $loop->iteration + ($sessions->currentPage() - 1) * $sessions->perPage() }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $session->title }}</span>
                            @if($session->zoom_meeting_id)
                            <span class="text-xs text-gray-500 dark:text-gray-400">Zoom ID: {{ $session->zoom_meeting_id }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ $session->subject->name_ar ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ $session->scheduled_at ? $session->scheduled_at->format('Y-m-d H:i') : '-' }}
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $statusConfig = [
                                'pending' => ['text' => 'قيد الانتظار', 'class' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-200'],
                                'processing' => ['text' => 'جاري المعالجة', 'class' => 'bg-blue-50 text-blue-600 dark:bg-blue-900 dark:text-blue-200'],
                                'completed' => ['text' => 'مكتمل', 'class' => 'bg-success-50 text-success-600 dark:bg-success-900 dark:text-success-200'],
                                'failed' => ['text' => 'فشل', 'class' => 'bg-error-50 text-error-600 dark:bg-error-900 dark:text-error-200'],
                            ];
                            $status = $statusConfig[$session->recording_status] ?? ['text' => $session->recording_status, 'class' => 'bg-gray-50 text-gray-600'];
                        @endphp
                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-medium {{ $status['class'] }}">
                            {{ $status['text'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                        @if($session->recording_size)
                            {{ number_format($session->recording_size / 1024 / 1024, 2) }} MB
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                        @if($session->recording_duration)
                            {{ gmdate('H:i:s', $session->recording_duration) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            @if($session->recording_status === 'completed' && $session->recording_file_path)
                                <!-- Download -->
                                <a href="{{ route('admin.recordings.download', $session) }}"
                                   class="rounded-lg bg-green-50 px-3 py-2 text-xs font-medium text-green-600 hover:bg-green-100 dark:bg-green-900 dark:text-green-200"
                                   title="تحميل">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>

                                <!-- Delete Local -->
                                <form action="{{ route('admin.recordings.destroy', $session) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف التسجيل المحلي؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg bg-error-50 px-3 py-2 text-xs font-medium text-error-600 hover:bg-error-100 dark:bg-error-900 dark:text-error-200"
                                            title="حذف محلي">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @elseif($session->recording_status === 'pending' || $session->recording_status === 'failed')
                                <!-- Sync from Zoom -->
                                <form action="{{ route('admin.recordings.sync', $session) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="rounded-lg bg-blue-50 px-3 py-2 text-xs font-medium text-blue-600 hover:bg-blue-100 dark:bg-blue-900 dark:text-blue-200"
                                            title="مزامنة من Zoom">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    </button>
                                </form>
                            @elseif($session->recording_status === 'processing')
                                <span class="text-xs text-gray-500 dark:text-gray-400">جاري المعالجة...</span>
                            @endif

                            @if($session->zoom_recording_id)
                                <!-- Delete from Zoom -->
                                <form action="{{ route('admin.recordings.delete-zoom', $session) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من حذف التسجيل من Zoom؟ هذا الإجراء لا يمكن التراجع عنه!');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-lg bg-orange-50 px-3 py-2 text-xs font-medium text-orange-600 hover:bg-orange-100 dark:bg-orange-900 dark:text-orange-200"
                                            title="حذف من Zoom">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <svg class="h-16 w-16 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">لا توجد تسجيلات</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($sessions->hasPages())
    <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-800">
        {{ $sessions->links() }}
    </div>
    @endif
</div>

<!-- Info Box -->
<div class="mt-6 rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900">
    <div class="flex items-start gap-3">
        <svg class="h-5 w-5 text-blue-600 dark:text-blue-300 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div class="text-sm text-blue-800 dark:text-blue-200">
            <p class="font-medium mb-1">ملاحظات هامة:</p>
            <ul class="list-disc list-inside space-y-1">
                <li>يتم مزامنة التسجيلات تلقائياً كل ساعة من Zoom</li>
                <li>يمكنك مزامنة التسجيل يدوياً عن طريق الضغط على زر المزامنة</li>
                <li>حذف التسجيل المحلي لا يؤثر على نسخة Zoom</li>
                <li>حذف التسجيل من Zoom لا يمكن التراجع عنه</li>
            </ul>
        </div>
    </div>
</div>
@endsection
