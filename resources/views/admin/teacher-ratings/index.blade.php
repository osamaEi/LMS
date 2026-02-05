@extends('layouts.dashboard')

@section('title', 'تقييمات المعلمين')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">تقييمات المعلمين</h2>
            <p class="text-gray-600 dark:text-gray-400">إدارة وعرض تقييمات المعلمين من قبل الطلاب (معيار NELC 2.4.9)</p>
        </div>
        <a href="{{ route('admin.teacher-ratings.report') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-brand-500 rounded-lg hover:bg-brand-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            تقرير التقييمات
        </a>
    </div>

    @if(session('success'))
    <div class="rounded-lg bg-success-50 p-4 text-success-600 dark:bg-success-900 dark:text-success-200">
        {{ session('success') }}
    </div>
    @endif

    <!-- Pending Approvals -->
    @if($pendingRatings->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                <span class="flex items-center justify-center w-6 h-6 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold">{{ $pendingRatings->total() }}</span>
                تقييمات بانتظار الموافقة
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">الطالب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">المعلم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">المادة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">التقييم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($pendingRatings as $rating)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($rating->student->name ?? 'Unknown') }}&background=6366f1&color=fff"
                                     class="w-8 h-8 rounded-full" alt="">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $rating->student->name ?? 'غير معروف' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-900 dark:text-white">{{ $rating->teacher->name ?? 'غير معروف' }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                            {{ $rating->subject->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $rating->overall_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                                <span class="mr-1 text-sm text-gray-600 dark:text-gray-400">({{ number_format($rating->overall_rating, 1) }})</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                            {{ $rating->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <form action="{{ route('admin.teacher-ratings.approve', $rating) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-green-500 rounded-lg hover:bg-green-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        موافقة
                                    </button>
                                </form>
                                <form action="{{ route('admin.teacher-ratings.reject', $rating) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من رفض هذا التقييم؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-white bg-red-500 rounded-lg hover:bg-red-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        رفض
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($pendingRatings->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
            {{ $pendingRatings->links() }}
        </div>
        @endif
    </div>
    @endif

    <!-- Teachers List with Ratings -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">تقييمات المعلمين</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">المعلم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">متوسط التقييم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">عدد التقييمات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($teachers as $index => $teacher)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->name) }}&background=6366f1&color=fff"
                                     class="w-10 h-10 rounded-full border-2 border-brand-500" alt="">
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $teacher->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $teacher->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($teacher->avg_rating > 0)
                            <div class="flex items-center gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($teacher->avg_rating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                                <span class="mr-2 text-sm font-medium text-gray-900 dark:text-white">{{ number_format($teacher->avg_rating, 2) }}</span>
                            </div>
                            @else
                            <span class="text-gray-400">لا يوجد تقييمات</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $teacher->ratings_received_count }} تقييم
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.teacher-ratings.show', $teacher) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100 dark:bg-brand-900 dark:text-brand-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                عرض التفاصيل
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <p class="mt-2">لا يوجد معلمين مسجلين</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
